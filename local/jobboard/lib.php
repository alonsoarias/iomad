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
 * Adds navigation nodes to the main navigation.
 *
 * This function adds Job Board to both:
 * 1. The Moodle navigation drawer (side navigation)
 * 2. The main navigation menu (custom menu items in top bar)
 *
 * @param global_navigation $navigation The global navigation object.
 */
function local_jobboard_extend_navigation(global_navigation $navigation) {
    global $USER, $PAGE, $CFG;

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
            // Vacancies submenu.
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
                $managelabel = get_string('managevacancies', 'local_jobboard');
                $menuentry .= "\n-$managelabel|/local/jobboard/index.php?view=manage";
            }

            if (has_capability('local/jobboard:reviewdocuments', $context)) {
                $reviewlabel = get_string('reviewapplications', 'local_jobboard');
                $menuentry .= "\n-$reviewlabel|/local/jobboard/index.php?view=review";
            }

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

    // Add management links for managers.
    if (has_capability('local/jobboard:createvacancy', $context)) {
        $jobboardnode->add(
            get_string('managevacancies', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add review applications for reviewers.
    if (has_capability('local/jobboard:reviewdocuments', $context)) {
        $jobboardnode->add(
            get_string('reviewapplications', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
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
 * @param int $companyid The company ID.
 * @return array Department ID => name.
 */
function local_jobboard_get_departments(int $companyid): array {
    global $DB;

    if (!$companyid) {
        return [];
    }

    // Check if department table exists (IOMAD).
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('department')) {
        return [];
    }

    $departments = $DB->get_records('department', ['company' => $companyid], 'name ASC', 'id, name');

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
 *
 * @param string|null $gender The applicant's gender (M, F, O, N).
 * @param string|null $educationlevel The applicant's education level code.
 * @param bool $isiserexempted Whether the applicant is ISER exempted.
 * @return array Array of document type objects that are required for this applicant.
 */
function local_jobboard_get_required_doctypes_for_applicant(
    ?string $gender = null,
    ?string $educationlevel = null,
    bool $isiserexempted = false
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
 * @return array Associative array of category => doctypes.
 */
function local_jobboard_get_doctypes_by_category(
    ?string $gender = null,
    ?string $educationlevel = null,
    bool $isiserexempted = false
): array {
    $doctypes = local_jobboard_get_required_doctypes_for_applicant($gender, $educationlevel, $isiserexempted);
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
 * @return bool True if the document is required.
 */
function local_jobboard_is_document_required(
    string $doccode,
    ?string $gender = null,
    ?string $educationlevel = null,
    bool $isiserexempted = false
): bool {
    $required = local_jobboard_get_required_doctypes_for_applicant($gender, $educationlevel, $isiserexempted);
    return isset($required[$doccode]);
}
