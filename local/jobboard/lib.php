<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of functions for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Check if user has any of the jobboard custom roles.
 *
 * @param int|null $userid User ID or null for current user.
 * @return bool True if user has any jobboard role.
 */
function local_jobboard_user_has_custom_role(?int $userid = null): bool {
    global $DB, $USER;

    $userid = $userid ?? $USER->id;

    // Get the custom role IDs.
    $customroles = ['jobboard_reviewer', 'jobboard_coordinator', 'jobboard_committee'];
    $roleids = $DB->get_fieldset_select('role', 'id', 'shortname IN (' .
        implode(',', array_fill(0, count($customroles), '?')) . ')', $customroles);

    if (empty($roleids)) {
        return false;
    }

    // Check if user has any of these roles assigned at system level.
    list($insql, $params) = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
    $params['userid'] = $userid;
    $params['contextid'] = context_system::instance()->id;

    return $DB->record_exists_select('role_assignments',
        "roleid $insql AND userid = :userid AND contextid = :contextid", $params);
}

/**
 * Check if there are any open convocatorias.
 *
 * @return bool True if there are open convocatorias.
 */
function local_jobboard_has_open_convocatorias(): bool {
    global $DB;

    // Check if the table exists.
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('local_jobboard_convocatoria')) {
        return false;
    }

    return $DB->record_exists('local_jobboard_convocatoria', ['status' => 'open']);
}

/**
 * Check if the jobboard menu should be visible for the current user.
 *
 * The menu is visible if:
 * - There are open convocatorias, OR
 * - The user has one of the custom jobboard roles, OR
 * - The user is a site admin
 *
 * @return bool True if menu should be visible.
 */
function local_jobboard_should_show_menu(): bool {
    // Site admins always see the menu.
    if (is_siteadmin()) {
        return true;
    }

    // Check if user has custom jobboard roles.
    if (local_jobboard_user_has_custom_role()) {
        return true;
    }

    // Check if there are open convocatorias.
    if (local_jobboard_has_open_convocatorias()) {
        return true;
    }

    return false;
}

/**
 * Adds navigation nodes to the main navigation.
 *
 * This function adds Job Board to both:
 * 1. The Moodle navigation drawer (side navigation)
 * 2. The main navigation menu (custom menu items in top bar)
 *
 * VISIBILITY: Menu is only shown if:
 * - There are open convocatorias, OR
 * - User has a custom jobboard role (reviewer, coordinator, committee), OR
 * - User is a site admin
 *
 * Navigation Structure:
 * - Dashboard (main page)
 * - Public Vacancies (if enabled)
 * - Available Vacancies
 * - My Applications
 * - Management Section (for managers):
 *   - Manage Convocatorias
 *   - Manage Vacancies
 * - Review Section (for reviewers):
 *   - Review Applications
 *   - My Reviews
 * - Reports
 * - Settings
 *
 * @param global_navigation $navigation The global navigation object.
 */
function local_jobboard_extend_navigation(global_navigation $navigation) {
    global $USER, $PAGE, $CFG;

    // Check if menu should be visible at all.
    if (!local_jobboard_should_show_menu()) {
        return;
    }

    $context = context_system::instance();
    $isloggedin = isloggedin() && !isguestuser();

    // Check if public page is enabled and navigation link should be shown.
    $enablepublic = get_config('local_jobboard', 'enable_public_page');
    $showpublicnav = get_config('local_jobboard', 'show_public_nav_link');
    $showinmainmenu = get_config('local_jobboard', 'show_in_main_menu');

    // Get custom menu title or use default.
    $menutitle = get_config('local_jobboard', 'main_menu_title');
    if (empty($menutitle)) {
        $menutitle = get_string('jobboard', 'local_jobboard');
    }

    // Add to custom menu items (main navigation bar) if enabled.
    if ($showinmainmenu) {
        // Store original custom menu items if not already done.
        if (!isset($CFG->dbunmodifiedcustommenuitems_jobboard)) {
            $CFG->dbunmodifiedcustommenuitems_jobboard = $CFG->custommenuitems ?? '';
        }

        // Build the Job Board menu entry with submenus.
        $menuentry = "\n$menutitle|/local/jobboard/index.php";

        // Add submenu items based on user capabilities and settings.
        if ($enablepublic) {
            $publiclabel = get_string('publicvacancies', 'local_jobboard');
            $menuentry .= "\n-$publiclabel|/local/jobboard/index.php?view=public";
        }

        if ($isloggedin) {
            // Browse Convocatorias - PRIMARY ENTRY POINT for all users.
            if (has_capability('local/jobboard:apply', $context) || has_capability('local/jobboard:viewallvacancies', $context)) {
                $convocatorialabel = get_string('convocatorias', 'local_jobboard');
                $menuentry .= "\n-$convocatorialabel|/local/jobboard/index.php?view=browse_convocatorias";
            }

            // Available vacancies submenu.
            if (has_capability('local/jobboard:apply', $context) || has_capability('local/jobboard:viewallvacancies', $context)) {
                $vacancylabel = get_string('vacancies', 'local_jobboard');
                $menuentry .= "\n-$vacancylabel|/local/jobboard/index.php?view=vacancies";
            }

            // My Applications submenu.
            if (has_capability('local/jobboard:apply', $context)) {
                $applabel = get_string('myapplications', 'local_jobboard');
                $menuentry .= "\n-$applabel|/local/jobboard/index.php?view=applications";
            }

            // Management submenus for authorized users.
            if (has_capability('local/jobboard:createvacancy', $context)) {
                // Convocatorias management.
                $convocatoriamanagelabel = get_string('manageconvocatorias', 'local_jobboard');
                $menuentry .= "\n-$convocatoriamanagelabel|/local/jobboard/index.php?view=convocatorias";

                // Vacancies management.
                $managelabel = get_string('managevacancies', 'local_jobboard');
                $menuentry .= "\n-$managelabel|/local/jobboard/index.php?view=manage";
            }

            // Review submenus for reviewers.
            if (has_capability('local/jobboard:reviewdocuments', $context)) {
                $reviewlabel = get_string('reviewapplications', 'local_jobboard');
                $menuentry .= "\n-$reviewlabel|/local/jobboard/index.php?view=review";

                $myreviewslabel = get_string('myreviews', 'local_jobboard');
                $menuentry .= "\n-$myreviewslabel|/local/jobboard/index.php?view=myreviews";
            }

            // Reports.
            if (has_capability('local/jobboard:viewreports', $context)) {
                $reportslabel = get_string('reports', 'local_jobboard');
                $menuentry .= "\n-$reportslabel|/local/jobboard/index.php?view=reports";
            }
        }

        // Append to custom menu items.
        $CFG->custommenuitems .= $menuentry;
    }

    // Also add to the navigation drawer (side navigation).
    // For non-logged in users, show public vacancies link if enabled.
    if (!$isloggedin) {
        if ($enablepublic && $showpublicnav) {
            $navigation->add(
                get_string('publicvacancies', 'local_jobboard'),
                new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
                navigation_node::TYPE_CUSTOM,
                null,
                'local_jobboard_public',
                new pix_icon('i/briefcase', '')
            );
        }
        return;
    }

    // Add main Job Board node for logged-in users.
    $jobboardnode = $navigation->add(
        get_string('jobboard', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_jobboard',
        new pix_icon('i/briefcase', '')
    );

    // Add public vacancies link if enabled (also for logged-in users).
    if ($enablepublic) {
        $jobboardnode->add(
            get_string('publicvacancies', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add Convocatorias link - PRIMARY ENTRY POINT for browsing.
    if (has_capability('local/jobboard:apply', $context) || has_capability('local/jobboard:viewallvacancies', $context)) {
        $jobboardnode->add(
            get_string('convocatorias', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add vacancies link for users who can view or apply.
    if (has_capability('local/jobboard:apply', $context) || has_capability('local/jobboard:viewallvacancies', $context)) {
        $jobboardnode->add(
            get_string('vacancies', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add "My Applications" for users who can apply.
    if (has_capability('local/jobboard:apply', $context)) {
        $jobboardnode->add(
            get_string('myapplications', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Management section for managers.
    if (has_capability('local/jobboard:createvacancy', $context)) {
        // Add convocatorias management.
        $jobboardnode->add(
            get_string('manageconvocatorias', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
            navigation_node::TYPE_CUSTOM
        );

        // Add vacancies management.
        $jobboardnode->add(
            get_string('managevacancies', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Review section for reviewers.
    if (has_capability('local/jobboard:reviewdocuments', $context)) {
        // All applications to review.
        $jobboardnode->add(
            get_string('reviewapplications', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
            navigation_node::TYPE_CUSTOM
        );

        // My assigned reviews.
        $jobboardnode->add(
            get_string('myreviews', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add reports for those with view reports capability.
    if (has_capability('local/jobboard:viewreports', $context)) {
        $jobboardnode->add(
            get_string('reports', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'reports']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add settings for admins - link to admin category.
    if (has_capability('local/jobboard:configure', $context)) {
        $jobboardnode->add(
            get_string('settings', 'local_jobboard'),
            new moodle_url('/admin/category.php', ['category' => 'local_jobboard_category']),
            navigation_node::TYPE_CUSTOM
        );
    }
}

/**
 * Adds settings to the site admin menu.
 *
 * Note: The admin pages are registered in settings.php using admin_externalpage,
 * which automatically adds them to the Moodle admin tree. This function provides
 * additional contextual navigation support.
 *
 * @param settings_navigation $settingsnav The settings navigation object.
 * @param context $context The context.
 */
function local_jobboard_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    // Admin pages are registered in settings.php and appear automatically in the admin tree.
    // No additional navigation configuration needed here.
}

/**
 * Serves files for the local_jobboard plugin.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param context $context The context.
 * @param string $filearea The file area.
 * @param array $args The arguments.
 * @param bool $forcedownload Whether to force download.
 * @param array $options Additional options.
 * @return bool False if file not found, does not return if file found.
 */
function local_jobboard_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $DB, $USER;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    require_login();

    if ($filearea !== 'application_documents') {
        return false;
    }

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    // Get the document record.
    $document = $DB->get_record('local_jobboard_document', ['id' => $itemid], '*', MUST_EXIST);
    $application = $DB->get_record('local_jobboard_application', ['id' => $document->applicationid], '*', MUST_EXIST);

    // Check access permissions.
    $candownload = false;

    // Owner can always download their own documents.
    if ($application->userid == $USER->id) {
        $candownload = true;
    }

    // Reviewers can download any document.
    if (has_capability('local/jobboard:downloadanydocument', $context)) {
        $candownload = true;
    }

    if (!$candownload) {
        return false;
    }

    // Log the access.
    \local_jobboard\audit::log('document_download', 'document', $document->id);

    $fs = get_file_storage();
    // Files are stored with applicationid as itemid, not document id.
    $file = $fs->get_file($context->id, 'local_jobboard', $filearea, $document->applicationid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 86400, 0, $forcedownload, $options);
}

/**
 * Get the list of vacancy statuses.
 *
 * @return array List of status code => name.
 */
function local_jobboard_get_vacancy_statuses(): array {
    return [
        'draft' => get_string('status:draft', 'local_jobboard'),
        'published' => get_string('status:published', 'local_jobboard'),
        'closed' => get_string('status:closed', 'local_jobboard'),
        'assigned' => get_string('status:assigned', 'local_jobboard'),
    ];
}

/**
 * Get the list of application statuses.
 *
 * @return array List of status code => name.
 */
function local_jobboard_get_application_statuses(): array {
    return [
        'submitted' => get_string('appstatus:submitted', 'local_jobboard'),
        'under_review' => get_string('appstatus:under_review', 'local_jobboard'),
        'docs_validated' => get_string('appstatus:docs_validated', 'local_jobboard'),
        'docs_rejected' => get_string('appstatus:docs_rejected', 'local_jobboard'),
        'interview' => get_string('appstatus:interview', 'local_jobboard'),
        'selected' => get_string('appstatus:selected', 'local_jobboard'),
        'rejected' => get_string('appstatus:rejected', 'local_jobboard'),
        'withdrawn' => get_string('appstatus:withdrawn', 'local_jobboard'),
    ];
}

/**
 * Get the list of document validation statuses.
 *
 * @return array List of status code => name.
 */
function local_jobboard_get_document_statuses(): array {
    return [
        'pending' => get_string('docstatus:pending', 'local_jobboard'),
        'approved' => get_string('docstatus:approved', 'local_jobboard'),
        'rejected' => get_string('docstatus:rejected', 'local_jobboard'),
    ];
}

/**
 * Get the list of contract types.
 *
 * @return array List of contract type code => name.
 */
function local_jobboard_get_contract_types(): array {
    return [
        'catedra' => get_string('contract:catedra', 'local_jobboard'),
        'temporal' => get_string('contract:temporal', 'local_jobboard'),
        'termino_fijo' => get_string('contract:termino_fijo', 'local_jobboard'),
        'prestacion_servicios' => get_string('contract:prestacion_servicios', 'local_jobboard'),
        'planta' => get_string('contract:planta', 'local_jobboard'),
    ];
}

/**
 * Get available educational modalities.
 *
 * @return array List of modality code => name.
 */
function local_jobboard_get_modalities(): array {
    return [
        'presencial' => get_string('modality:presencial', 'local_jobboard'),
        'distancia' => get_string('modality:distancia', 'local_jobboard'),
        'virtual' => get_string('modality:virtual', 'local_jobboard'),
        'hibrida' => get_string('modality:hibrida', 'local_jobboard'),
    ];
}

/**
 * Get allowed state transitions for applications.
 *
 * @return array From status => array of allowed to statuses.
 */
function local_jobboard_get_allowed_transitions(): array {
    return [
        'submitted' => ['under_review', 'rejected'],
        'under_review' => ['docs_validated', 'docs_rejected'],
        'docs_rejected' => ['under_review'],
        'docs_validated' => ['interview', 'rejected'],
        'interview' => ['selected', 'rejected'],
    ];
}

/**
 * Check if a status transition is allowed.
 *
 * @param string $from Current status.
 * @param string $to Target status.
 * @return bool True if transition is allowed.
 */
function local_jobboard_is_transition_allowed(string $from, string $to): bool {
    $transitions = local_jobboard_get_allowed_transitions();

    if (!isset($transitions[$from])) {
        return false;
    }

    return in_array($to, $transitions[$from]);
}

/**
 * Get the user's company ID (for Iomad multi-tenant).
 *
 * @param int|null $userid User ID or null for current user.
 * @return int|null Company ID or null if not in a company.
 */
function local_jobboard_get_user_companyid(?int $userid = null): ?int {
    global $DB, $USER;

    $userid = $userid ?? $USER->id;

    // Check if Iomad is installed.
    if (!file_exists(__DIR__ . '/../../local/iomad/lib/company.php')) {
        return null;
    }

    require_once(__DIR__ . '/../../local/iomad/lib/company.php');

    $companies = $DB->get_records_sql(
        "SELECT companyid FROM {company_users} WHERE userid = :userid ORDER BY lastused DESC, companyid DESC",
        ['userid' => $userid]
    );

    if (!empty($companies)) {
        $first = reset($companies);
        return (int) $first->companyid;
    }

    return null;
}

/**
 * Check if user can view a vacancy (respects multi-tenant).
 *
 * @param stdClass $vacancy The vacancy record.
 * @param int|null $userid User ID or null for current user.
 * @return bool True if user can view the vacancy.
 */
function local_jobboard_can_view_vacancy(stdClass $vacancy, ?int $userid = null): bool {
    global $USER;

    $userid = $userid ?? $USER->id;
    $context = context_system::instance();

    // Users with viewallvacancies can see everything.
    if (has_capability('local/jobboard:viewallvacancies', $context, $userid)) {
        return true;
    }

    // For published vacancies, check company filtering.
    if ($vacancy->status !== 'published') {
        return false;
    }

    // If vacancy has no company restriction, anyone can view.
    if (empty($vacancy->companyid)) {
        return true;
    }

    // Check if user belongs to the same company.
    $usercompanyid = local_jobboard_get_user_companyid($userid);
    return $usercompanyid === (int) $vacancy->companyid;
}

/**
 * Get company name by ID.
 *
 * @param int $companyid The company ID.
 * @return string Company name or empty string.
 */
function local_jobboard_get_company_name(int $companyid): string {
    global $DB;

    if (!$companyid) {
        return '';
    }

    $company = $DB->get_record('company', ['id' => $companyid], 'name');
    return $company ? $company->name : '';
}

/**
 * Get list of available companies for dropdown.
 *
 * @return array Company ID => name.
 */
function local_jobboard_get_companies(): array {
    global $DB;

    // Check if company table exists (Iomad).
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('company')) {
        return [];
    }

    $companies = $DB->get_records('company', ['suspended' => 0], 'name ASC', 'id, name');

    $result = [];
    foreach ($companies as $company) {
        $result[$company->id] = $company->name;
    }

    return $result;
}

/**
 * Check if Iomad is installed.
 *
 * @return bool True if Iomad is installed.
 */
function local_jobboard_is_iomad_installed(): bool {
    global $DB;

    $dbman = $DB->get_manager();
    return $dbman->table_exists('company') && $dbman->table_exists('company_users');
}

/**
 * Get list of departments for a company (IOMAD).
 *
 * Only returns child departments (parent > 0), excluding the root department.
 * In IOMAD, each company has a root department (parent = 0) that serves as
 * a container, and the actual departments are children of this root.
 *
 * @param int $companyid The company ID.
 * @param bool $includeroot Whether to include the root department (default: false).
 * @return array Department ID => name.
 */
function local_jobboard_get_departments(int $companyid, bool $includeroot = false): array {
    global $DB;

    if (!$companyid) {
        return [];
    }

    // Check if department table exists (IOMAD).
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('department')) {
        return [];
    }

    // Build query conditions - exclude root department by default.
    $conditions = ['company' => $companyid];
    $sql = "SELECT id, name, parent FROM {department} WHERE company = :company";
    $params = ['company' => $companyid];

    if (!$includeroot) {
        // Only get child departments (parent > 0).
        $sql .= " AND parent > 0";
    }

    $sql .= " ORDER BY name ASC";

    $departments = $DB->get_records_sql($sql, $params);

    $result = [];
    foreach ($departments as $dept) {
        $result[$dept->id] = $dept->name;
    }

    return $result;
}

/**
 * Get department name by ID.
 *
 * @param int $departmentid The department ID.
 * @return string Department name or empty string.
 */
function local_jobboard_get_department_name(int $departmentid): string {
    global $DB;

    if (!$departmentid) {
        return '';
    }

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('department')) {
        return '';
    }

    $dept = $DB->get_record('department', ['id' => $departmentid], 'name');
    return $dept ? $dept->name : '';
}

/**
 * Get list of convocatorias for select dropdown.
 *
 * @param int $companyid Optional company ID to filter by.
 * @param string $status Optional status filter ('draft', 'open', 'closed', 'archived').
 * @param bool $includeall Whether to include all convocatorias regardless of status.
 * @return array Array of convocatoria ID => name.
 */
function local_jobboard_get_convocatorias(int $companyid = 0, string $status = '', bool $includeall = false): array {
    global $DB;

    $conditions = [];
    $params = [];

    if ($companyid > 0) {
        $conditions[] = 'companyid = :companyid';
        $params['companyid'] = $companyid;
    }

    if (!$includeall && empty($status)) {
        // By default, only show open convocatorias.
        $conditions[] = "status = 'open'";
    } elseif (!empty($status)) {
        $conditions[] = 'status = :status';
        $params['status'] = $status;
    }

    $where = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

    $sql = "SELECT id, code, name, startdate, enddate, status
            FROM {local_jobboard_convocatoria}
            {$where}
            ORDER BY startdate DESC, name ASC";

    $records = $DB->get_records_sql($sql, $params);

    $result = [];
    foreach ($records as $conv) {
        $dates = userdate($conv->startdate, '%d/%m/%Y') . ' - ' . userdate($conv->enddate, '%d/%m/%Y');
        $result[$conv->id] = $conv->name . ' (' . $conv->code . ') - ' . $dates;
    }

    return $result;
}

/**
 * Get convocatoria by ID.
 *
 * @param int $convocatoriaid The convocatoria ID.
 * @return stdClass|false The convocatoria record or false.
 */
function local_jobboard_get_convocatoria(int $convocatoriaid) {
    global $DB;

    if (!$convocatoriaid) {
        return false;
    }

    return $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
}

/**
 * Check if user can apply to a vacancy based on convocatoria restrictions.
 *
 * This checks:
 * - Single application restriction per convocatoria (if allow_multiple_applications is false)
 * - Maximum applications per user per convocatoria
 *
 * @param int $userid The user ID.
 * @param int $vacancyid The vacancy ID they want to apply to.
 * @return array ['can_apply' => bool, 'reason' => string|null]
 */
function local_jobboard_can_user_apply_to_vacancy(int $userid, int $vacancyid): array {
    global $DB;

    // Get vacancy and its convocatoria.
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
    if (!$vacancy || empty($vacancy->convocatoriaid)) {
        // No convocatoria restrictions - allow.
        return ['can_apply' => true, 'reason' => null];
    }

    $convocatoria = local_jobboard_get_convocatoria($vacancy->convocatoriaid);
    if (!$convocatoria) {
        return ['can_apply' => true, 'reason' => null];
    }

    // Check if multiple applications are allowed.
    $allowmultiple = !empty($convocatoria->allow_multiple_applications);
    $maxapplications = isset($convocatoria->max_applications_per_user) ? (int) $convocatoria->max_applications_per_user : 0;

    // Count user's existing applications to vacancies in this convocatoria.
    $sql = "SELECT COUNT(a.id)
            FROM {local_jobboard_application} a
            JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
            WHERE a.userid = :userid
              AND v.convocatoriaid = :convocatoriaid
              AND a.status != 'withdrawn'";

    $existingcount = $DB->count_records_sql($sql, [
        'userid' => $userid,
        'convocatoriaid' => $convocatoria->id,
    ]);

    // If multiple applications are not allowed, check if user already has one.
    if (!$allowmultiple && $existingcount > 0) {
        return [
            'can_apply' => false,
            'reason' => get_string('error:singleapplicationonly', 'local_jobboard'),
        ];
    }

    // If there's a maximum limit, check if user has reached it.
    if ($maxapplications > 0 && $existingcount >= $maxapplications) {
        return [
            'can_apply' => false,
            'reason' => get_string('error:applicationlimitreached', 'local_jobboard', $maxapplications),
        ];
    }

    return ['can_apply' => true, 'reason' => null];
}

/**
 * Check if user meets experience requirements for a vacancy.
 *
 * Occasional contracts require a minimum of 2 years of related work experience.
 *
 * @param int $userid The user ID.
 * @param int $vacancyid The vacancy ID.
 * @return array ['meets_requirements' => bool, 'reason' => string|null]
 */
function local_jobboard_check_experience_requirements(int $userid, int $vacancyid): array {
    global $DB;

    // Get vacancy.
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
    if (!$vacancy) {
        return ['meets_requirements' => true, 'reason' => null];
    }

    // Check if this is an occasional contract.
    if (empty($vacancy->contracttype) || $vacancy->contracttype !== 'occasional') {
        // Not an occasional contract - no experience requirement.
        return ['meets_requirements' => true, 'reason' => null];
    }

    // Occasional contracts require minimum 2 years experience.
    // Get user's experience from applicant profile.
    $profile = $DB->get_record('local_jobboard_applicant_profile', ['userid' => $userid]);
    if (!$profile) {
        // No profile - fail the check.
        return [
            'meets_requirements' => false,
            'reason' => get_string('error:occasionalrequiresexperience', 'local_jobboard'),
        ];
    }

    // Experience is stored as years in the profile.
    $experienceyears = !empty($profile->experience_years) ? (int) $profile->experience_years : 0;
    $minrequired = 2;

    if ($experienceyears < $minrequired) {
        return [
            'meets_requirements' => false,
            'reason' => get_string('error:occasionalrequiresexperience', 'local_jobboard'),
        ];
    }

    return ['meets_requirements' => true, 'reason' => null];
}

/**
 * Get IOMAD installation type and details.
 *
 * @return array Installation info with keys: is_iomad, version, has_departments.
 */
function local_jobboard_get_iomad_info(): array {
    global $DB, $CFG;

    $info = [
        'is_iomad' => false,
        'version' => null,
        'has_departments' => false,
        'has_companies' => false,
    ];

    $dbman = $DB->get_manager();

    // Check for IOMAD tables.
    if (!$dbman->table_exists('company')) {
        return $info;
    }

    $info['is_iomad'] = true;
    $info['has_companies'] = true;

    // Check for departments.
    if ($dbman->table_exists('department')) {
        $info['has_departments'] = true;
    }

    // Get IOMAD version if available.
    $iomadversion = get_config('local_iomad', 'version');
    if ($iomadversion) {
        $info['version'] = $iomadversion;
    }

    return $info;
}

/**
 * Get the default document types to be exempted for ISER historic personnel.
 *
 * @return array Array of document type codes.
 */
function local_jobboard_get_iser_exempted_doctypes(): array {
    return [
        'titulo_academico',
        'cedula',
        'tarjeta_profesional',
        'libreta_militar',
        'certificacion_laboral',
    ];
}

/**
 * Check if user has an active ISER exemption.
 *
 * @param int|null $userid User ID or null for current user.
 * @return stdClass|false Exemption record or false.
 */
function local_jobboard_get_user_exemption(?int $userid = null) {
    global $DB, $USER;

    $userid = $userid ?? $USER->id;
    $now = time();

    return $DB->get_record_sql(
        "SELECT * FROM {local_jobboard_exemption}
         WHERE userid = :userid
           AND validfrom <= :now1
           AND (validuntil IS NULL OR validuntil >= :now2)
         ORDER BY timecreated DESC
         LIMIT 1",
        ['userid' => $userid, 'now1' => $now, 'now2' => $now]
    );
}

/**
 * Get user's age in years based on birthdate.
 *
 * @param int|null $userid User ID or null for current user.
 * @return int|null Age in years or null if birthdate not set.
 */
function local_jobboard_get_user_age(?int $userid = null): ?int {
    global $DB, $USER;

    $userid = $userid ?? $USER->id;

    // Check for birthdate in user_info_data (custom profile field).
    $birthdatefield = $DB->get_record('user_info_field', ['shortname' => 'birthdate']);
    if (!$birthdatefield) {
        // Try alternative field names.
        $birthdatefield = $DB->get_record('user_info_field', ['shortname' => 'fecha_nacimiento']);
    }

    if (!$birthdatefield) {
        return null;
    }

    $birthdatedata = $DB->get_record('user_info_data', [
        'userid' => $userid,
        'fieldid' => $birthdatefield->id,
    ]);

    if (!$birthdatedata || empty($birthdatedata->data)) {
        return null;
    }

    $birthdate = $birthdatedata->data;

    // Handle different date formats.
    $timestamp = null;
    if (is_numeric($birthdate)) {
        // Unix timestamp.
        $timestamp = (int) $birthdate;
    } else {
        // Try to parse as date string.
        $timestamp = strtotime($birthdate);
    }

    if (!$timestamp || $timestamp <= 0) {
        return null;
    }

    // Calculate age.
    $birthDateTime = new \DateTime();
    $birthDateTime->setTimestamp($timestamp);
    $now = new \DateTime();
    $age = $now->diff($birthDateTime)->y;

    return $age;
}

/**
 * Format a timestamp for display.
 *
 * @param int $timestamp Unix timestamp.
 * @param string $format Date format (strftime format).
 * @return string Formatted date.
 */
function local_jobboard_format_date(int $timestamp, string $format = '%d/%m/%Y'): string {
    return userdate($timestamp, $format);
}

/**
 * Format a timestamp for datetime display.
 *
 * @param int $timestamp Unix timestamp.
 * @return string Formatted datetime.
 */
function local_jobboard_format_datetime(int $timestamp): string {
    return userdate($timestamp, '%d/%m/%Y %H:%M');
}

/**
 * Calculate days between two timestamps.
 *
 * @param int $from Start timestamp.
 * @param int $to End timestamp.
 * @return int Number of days.
 */
function local_jobboard_days_between(int $from, int $to): int {
    return (int) floor(abs($to - $from) / 86400);
}

/**
 * Get the maximum file size allowed for uploads.
 *
 * @return int File size in bytes.
 */
function local_jobboard_get_max_filesize(): int {
    $configsize = get_config('local_jobboard', 'maxfilesize');

    if ($configsize) {
        return (int) $configsize * 1024 * 1024; // MB to bytes.
    }

    // Default 10MB.
    return 10 * 1024 * 1024;
}

/**
 * Get allowed file extensions.
 *
 * @return array Array of allowed extensions.
 */
function local_jobboard_get_allowed_extensions(): array {
    $config = get_config('local_jobboard', 'allowedformats');

    if ($config) {
        return array_map('trim', explode(',', $config));
    }

    return ['pdf', 'jpg', 'jpeg', 'png'];
}

/**
 * Validate uploaded file type.
 *
 * @param stored_file $file The file to validate.
 * @param array|null $allowedformats Allowed formats or null for defaults.
 * @return bool True if valid.
 */
function local_jobboard_validate_file_type(stored_file $file, ?array $allowedformats = null): bool {
    $allowedformats = $allowedformats ?? local_jobboard_get_allowed_extensions();

    // Get the extension.
    $filename = $file->get_filename();
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedformats)) {
        return false;
    }

    // Check MIME type.
    $mimetype = $file->get_mimetype();
    $allowedmimes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    if (isset($allowedmimes[$extension])) {
        if ($mimetype !== $allowedmimes[$extension]) {
            return false;
        }
    }

    return true;
}

/**
 * Get all enabled document types.
 *
 * @return array Array of document type objects.
 */
function local_jobboard_get_all_doctypes(): array {
    global $DB;

    return $DB->get_records('local_jobboard_doctype', ['enabled' => 1], 'sortorder ASC');
}

/**
 * Get required document types for an applicant based on their profile.
 *
 * This function filters document types based on:
 * - Gender condition (e.g., libreta_militar only for men)
 * - Profession exemptions (e.g., tarjeta_profesional not for licenciados)
 * - ISER exemption status (documents already in file for previous ISER employees)
 * - Age exemption (e.g., libreta_militar exempt for 50+ years)
 *
 * @param string|null $gender The applicant's gender (M, F, O, N).
 * @param string|null $educationlevel The applicant's education level code.
 * @param bool $isiserexempted Whether the applicant is ISER exempted.
 * @param int|null $userage The applicant's age in years (for age exemptions).
 * @return array Array of document type objects that are required for this applicant.
 */
function local_jobboard_get_required_doctypes_for_applicant(
    ?string $gender = null,
    ?string $educationlevel = null,
    bool $isiserexempted = false,
    ?int $userage = null
): array {
    global $DB;

    $doctypes = local_jobboard_get_all_doctypes();
    $required = [];

    foreach ($doctypes as $doctype) {
        // Check if document is required at all.
        if (empty($doctype->isrequired)) {
            continue;
        }

        // Check gender condition.
        if (!empty($doctype->gender_condition)) {
            // If gender condition is set, check if it matches.
            if ($gender !== $doctype->gender_condition) {
                // Document not required for this gender.
                continue;
            }
        }

        // Check profession exemption.
        if (!empty($doctype->profession_exempt) && !empty($educationlevel)) {
            $exemptprofessions = json_decode($doctype->profession_exempt, true);
            if (is_array($exemptprofessions) && in_array($educationlevel, $exemptprofessions)) {
                // Document not required for this profession.
                continue;
            }
        }

        // Check ISER exemption.
        if ($isiserexempted && !empty($doctype->iserexempted)) {
            // Document is exempted for ISER previous employees.
            continue;
        }

        // Check age exemption (e.g., libreta_militar exempt for 50+ years).
        if ($userage !== null && !empty($doctype->age_exemption_threshold)) {
            $threshold = (int) $doctype->age_exemption_threshold;
            if ($userage >= $threshold) {
                // Document not required for users at or above age threshold.
                // Mark as age-exempted for display purposes but don't include in required list.
                $doctype->is_age_exempted = true;
                $doctype->age_exemption_reason = get_string('age_exempt_notice', 'local_jobboard', $threshold);
                continue;
            }
        }

        $required[$doctype->code] = $doctype;
    }

    return $required;
}

/**
 * Get document types grouped by category.
 *
 * @param string|null $gender The applicant's gender for filtering.
 * @param string|null $educationlevel The applicant's education level for filtering.
 * @param bool $isiserexempted Whether the applicant is ISER exempted.
 * @param int|null $userage The applicant's age in years (for age exemptions).
 * @return array Associative array of category => doctypes.
 */
function local_jobboard_get_doctypes_by_category(
    ?string $gender = null,
    ?string $educationlevel = null,
    bool $isiserexempted = false,
    ?int $userage = null
): array {
    $doctypes = local_jobboard_get_required_doctypes_for_applicant($gender, $educationlevel, $isiserexempted, $userage);
    $grouped = [];

    foreach ($doctypes as $doctype) {
        $category = $doctype->category ?? 'other';
        if (!isset($grouped[$category])) {
            $grouped[$category] = [];
        }
        $grouped[$category][] = $doctype;
    }

    // Sort categories in a logical order.
    $categoryorder = ['identification', 'academic', 'employment', 'financial', 'health', 'legal', 'other'];
    $sorted = [];
    foreach ($categoryorder as $cat) {
        if (isset($grouped[$cat])) {
            $sorted[$cat] = $grouped[$cat];
        }
    }
    // Add any remaining categories not in the predefined order.
    foreach ($grouped as $cat => $docs) {
        if (!isset($sorted[$cat])) {
            $sorted[$cat] = $docs;
        }
    }

    return $sorted;
}

/**
 * Get the localized category name for a document category.
 *
 * @param string $category The category code.
 * @return string The localized category name.
 */
function local_jobboard_get_category_name(string $category): string {
    $stringkey = 'doccategory_' . $category;
    if (get_string_manager()->string_exists($stringkey, 'local_jobboard')) {
        return get_string($stringkey, 'local_jobboard');
    }
    return ucfirst($category);
}

/**
 * Check if a specific document is required for an applicant.
 *
 * @param string $doccode The document type code.
 * @param string|null $gender The applicant's gender.
 * @param string|null $educationlevel The applicant's education level.
 * @param bool $isiserexempted Whether the applicant is ISER exempted.
 * @param int|null $userage The applicant's age in years.
 * @return bool True if the document is required.
 */
function local_jobboard_is_document_required(
    string $doccode,
    ?string $gender = null,
    ?string $educationlevel = null,
    bool $isiserexempted = false,
    ?int $userage = null
): bool {
    $required = local_jobboard_get_required_doctypes_for_applicant($gender, $educationlevel, $isiserexempted, $userage);
    return isset($required[$doccode]);
}

/**
 * Get dashboard statistics for the current user.
 *
 * @param int $userid The user ID.
 * @param bool $isadmin Whether the user is an admin.
 * @param bool $isreviewer Whether the user is a reviewer.
 * @return array Statistics array.
 */
function local_jobboard_get_dashboard_stats(int $userid, bool $isadmin = false, bool $isreviewer = false): array {
    global $DB;

    $stats = [];
    $now = time();

    // Admin/manager statistics.
    if ($isadmin) {
        // Active convocatorias (status = 'open').
        $stats['active_convocatorias'] = $DB->count_records('local_jobboard_convocatoria', ['status' => 'open']);

        // Published vacancies.
        $stats['published_vacancies'] = $DB->count_records('local_jobboard_vacancy', ['status' => 'published']);

        // Total applications.
        $stats['total_applications'] = $DB->count_records('local_jobboard_application');

        // Pending reviews (documents pending validation).
        $sql = "SELECT COUNT(DISTINCT d.id)
                FROM {local_jobboard_document} d
                LEFT JOIN {local_jobboard_doc_validation} v ON v.documentid = d.id
                WHERE v.id IS NULL OR v.status = 'pending'";
        $stats['pending_reviews'] = (int) $DB->count_records_sql($sql);

        // Recent activity (last 10 audit entries).
        $stats['recent_activity'] = $DB->get_records('local_jobboard_audit', [], 'timecreated DESC', '*', 0, 10);
    }

    // Reviewer statistics.
    if ($isreviewer) {
        // My pending reviews.
        $sql = "SELECT COUNT(DISTINCT a.id)
                FROM {local_jobboard_application} a
                WHERE a.reviewerid = :userid
                AND a.status IN ('submitted', 'under_review')";
        $stats['my_pending_reviews'] = (int) $DB->count_records_sql($sql, ['userid' => $userid]);
    }

    // Applicant statistics (for all authenticated users).
    // My applications count.
    $stats['my_applications'] = $DB->count_records('local_jobboard_application', ['userid' => $userid]);

    // Available vacancies (published and open).
    $sql = "SELECT COUNT(*)
            FROM {local_jobboard_vacancy}
            WHERE status = 'published'
            AND opendate <= :now1
            AND closedate >= :now2";
    $stats['available_vacancies'] = (int) $DB->count_records_sql($sql, ['now1' => $now, 'now2' => $now]);

    // Pending documents for my applications.
    $sql = "SELECT COUNT(d.id)
            FROM {local_jobboard_document} d
            JOIN {local_jobboard_application} a ON a.id = d.applicationid
            LEFT JOIN {local_jobboard_doc_validation} v ON v.documentid = d.id
            WHERE a.userid = :userid
            AND (v.id IS NULL OR v.status = 'pending')";
    $stats['pending_docs'] = (int) $DB->count_records_sql($sql, ['userid' => $userid]);

    return $stats;
}
