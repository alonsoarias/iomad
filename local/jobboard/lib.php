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
 * @param global_navigation $navigation The global navigation object.
 */
function local_jobboard_extend_navigation(global_navigation $navigation) {
    global $USER, $PAGE;

    $context = context_system::instance();
    $isloggedin = isloggedin() && !isguestuser();

    // Check if public page is enabled and navigation link should be shown.
    $enablepublic = get_config('local_jobboard', 'enable_public_page');
    $showpublicnav = get_config('local_jobboard', 'show_public_nav_link');

    // For non-logged in users, show public vacancies link if enabled.
    if (!$isloggedin) {
        if ($enablepublic && $showpublicnav) {
            $navigation->add(
                get_string('publicvacancies', 'local_jobboard'),
                new moodle_url('/local/jobboard/public/index.php'),
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
            new moodle_url('/local/jobboard/public/index.php'),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add vacancies link for users who can view or apply.
    if (has_capability('local/jobboard:apply', $context) || has_capability('local/jobboard:viewallvacancies', $context)) {
        $jobboardnode->add(
            get_string('vacancies', 'local_jobboard'),
            new moodle_url('/local/jobboard/vacancies.php'),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add "My Applications" for users who can apply.
    if (has_capability('local/jobboard:apply', $context)) {
        $jobboardnode->add(
            get_string('myapplications', 'local_jobboard'),
            new moodle_url('/local/jobboard/applications.php'),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add management links for managers.
    if (has_capability('local/jobboard:createvacancy', $context)) {
        $jobboardnode->add(
            get_string('managevacancies', 'local_jobboard'),
            new moodle_url('/local/jobboard/manage.php'),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add review applications for reviewers.
    if (has_capability('local/jobboard:reviewdocuments', $context)) {
        $jobboardnode->add(
            get_string('reviewapplications', 'local_jobboard'),
            new moodle_url('/local/jobboard/review.php'),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add reports for those with view reports capability.
    if (has_capability('local/jobboard:viewreports', $context)) {
        $jobboardnode->add(
            get_string('reports', 'local_jobboard'),
            new moodle_url('/local/jobboard/reports.php'),
            navigation_node::TYPE_CUSTOM
        );
    }

    // Add settings for admins.
    if (has_capability('local/jobboard:configure', $context)) {
        $jobboardnode->add(
            get_string('settings', 'local_jobboard'),
            new moodle_url('/local/jobboard/settings.php'),
            navigation_node::TYPE_CUSTOM
        );
    }
}

/**
 * Adds settings to the site admin menu.
 *
 * @param settings_navigation $settingsnav The settings navigation object.
 * @param context $context The context.
 */
function local_jobboard_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    global $PAGE;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return;
    }

    if (!has_capability('local/jobboard:configure', $context)) {
        return;
    }

    $siteadmin = $settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);
    if (!$siteadmin) {
        return;
    }

    // Add Job Board settings category.
    $jobboardsettings = $siteadmin->add(
        get_string('pluginname', 'local_jobboard'),
        null,
        navigation_node::TYPE_CONTAINER,
        null,
        'local_jobboard_settings'
    );

    $jobboardsettings->add(
        get_string('generalsettings', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/settings.php'),
        navigation_node::TYPE_SETTING
    );

    $jobboardsettings->add(
        get_string('managedoctypes', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/doctypes.php'),
        navigation_node::TYPE_SETTING
    );

    $jobboardsettings->add(
        get_string('emailtemplates', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/templates.php'),
        navigation_node::TYPE_SETTING
    );

    $jobboardsettings->add(
        get_string('manageexemptions', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/exemptions.php'),
        navigation_node::TYPE_SETTING
    );

    $jobboardsettings->add(
        get_string('apitokens', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/tokens.php'),
        navigation_node::TYPE_SETTING
    );
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
    $file = $fs->get_file($context->id, 'local_jobboard', $filearea, $itemid, $filepath, $filename);

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
