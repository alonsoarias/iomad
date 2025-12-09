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
 * Alternative signup page for Job Board with company selection.
 *
 * This page allows new users to register with complete profile information
 * and optionally select a company when applying for vacancies. It works
 * alongside IOMAD's multi-tenant architecture and replicates Moodle's
 * email confirmation workflow.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Allow access without login.
define('NO_MOODLE_COOKIES', false);
define('ALLOW_GET_PARAMETERS', true);

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/login/lib.php');

// Check if user is already logged in.
if (isloggedin() && !isguestuser()) {
    $vacancyid = optional_param('vacancyid', 0, PARAM_INT);
    if ($vacancyid) {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancyid]));
    } else {
        redirect(new moodle_url('/local/jobboard/index.php'));
    }
}

// Check if plugin-specific self-registration is enabled.
// This allows registration through the jobboard even when global registration is disabled.
$pluginselfreg = get_config('local_jobboard', 'enable_self_registration');
if (empty($pluginselfreg)) {
    // Fallback to Moodle's global setting.
    if (empty($CFG->registerauth) || $CFG->registerauth === 'none') {
        throw new moodle_exception('registrationdisabled', 'local_jobboard');
    }
}

// Parameters.
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);

// Get vacancy info if provided.
$vacancy = null;
if ($vacancyid) {
    $vacancy = $DB->get_record('local_jobboard_vacancy', [
        'id' => $vacancyid,
        'status' => 'published',
    ]);
}

// Set up the page.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/signup.php', ['vacancyid' => $vacancyid]));
$PAGE->set_pagelayout('base'); // Use 'base' instead of 'login' to enable help button tooltips.
$PAGE->set_title(get_string('signup_title', 'local_jobboard'));
$PAGE->set_heading(get_string('signup_title', 'local_jobboard'));

// Add custom CSS for the signup form.
$PAGE->requires->css('/local/jobboard/styles.css');

// Check IOMAD installation and get companies.
$isiomad = local_jobboard_is_iomad_installed();
$companies = [];

if ($isiomad) {
    $companies = local_jobboard_get_companies();
}

// If vacancy has a company, pre-select it.
$defaultcompanyid = 0;
if ($vacancy && !empty($vacancy->companyid)) {
    $defaultcompanyid = $vacancy->companyid;
}

// Create the form.
$formdata = [
    'vacancyid' => $vacancyid,
    'companies' => $companies,
    'isiomad' => $isiomad,
];

$mform = new \local_jobboard\forms\signup_form(null, $formdata);

// Set default company if available.
if ($defaultcompanyid) {
    $mform->set_data(['companyid' => $defaultcompanyid]);
}

// Handle form cancellation.
if ($mform->is_cancelled()) {
    if ($vacancyid) {
        redirect(new moodle_url('/local/jobboard/public.php', ['id' => $vacancyid]));
    } else {
        redirect(new moodle_url('/local/jobboard/public.php'));
    }
}

// Handle form submission.
if ($data = $mform->get_data()) {
    try {
        // Create the user account.
        $user = create_user_from_form($data, $isiomad);

        if ($user) {
            // Send confirmation email using Moodle's standard function.
            $confirmationemail = send_confirmation_email_to_user($user);

            if (!$confirmationemail) {
                // Email failed but user was created - log this.
                debugging('Failed to send confirmation email to user ' . $user->id, DEBUG_NORMAL);
            }

            // Show success message.
            $PAGE->set_title(get_string('signup_success_title', 'local_jobboard'));
            $PAGE->set_heading(get_string('signup_success_title', 'local_jobboard'));

            echo $OUTPUT->header();

            echo html_writer::start_div('signup-success text-center my-5');
            echo html_writer::tag('div', html_writer::tag('i', '', ['class' => 'fa fa-check-circle fa-5x']),
                ['class' => 'text-success mb-4']);
            echo html_writer::tag('h2', get_string('signup_success_title', 'local_jobboard'), ['class' => 'mb-3']);
            echo html_writer::tag('p', get_string('signup_success_message', 'local_jobboard', $user->email),
                ['class' => 'lead text-muted']);

            // Detailed instructions.
            echo html_writer::start_div('alert alert-info text-start mt-4 mx-auto', ['style' => 'max-width: 600px;']);
            echo html_writer::tag('h5', html_writer::tag('i', '', ['class' => 'fa fa-envelope me-2']) .
                get_string('signup_email_instructions_title', 'local_jobboard'));
            echo html_writer::start_tag('ol', ['class' => 'mb-0']);
            echo html_writer::tag('li', get_string('signup_email_instruction_1', 'local_jobboard'));
            echo html_writer::tag('li', get_string('signup_email_instruction_2', 'local_jobboard'));
            echo html_writer::tag('li', get_string('signup_email_instruction_3', 'local_jobboard'));
            echo html_writer::end_tag('ol');
            echo html_writer::end_div();

            // Spam notice.
            echo html_writer::tag('p', html_writer::tag('small',
                html_writer::tag('i', '', ['class' => 'fa fa-exclamation-triangle me-1']) .
                get_string('signup_check_spam', 'local_jobboard')),
                ['class' => 'text-muted mt-3']);

            // Buttons.
            echo html_writer::start_div('mt-4');
            echo html_writer::link(
                new moodle_url('/local/jobboard/public.php'),
                html_writer::tag('i', '', ['class' => 'fa fa-arrow-left me-2']) .
                get_string('backtovacancies', 'local_jobboard'),
                ['class' => 'btn btn-primary me-2']
            );
            echo html_writer::link(
                new moodle_url('/login/index.php'),
                html_writer::tag('i', '', ['class' => 'fa fa-sign-in me-2']) .
                get_string('login'),
                ['class' => 'btn btn-outline-secondary']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();

            echo $OUTPUT->footer();
            exit;
        }
    } catch (Exception $e) {
        // Show error message.
        \core\notification::error(get_string('signup_error_creating', 'local_jobboard') . ': ' . $e->getMessage());
    }
}

// Display the form.
echo $OUTPUT->header();

// Build login URL with redirect.
$loginurl = new moodle_url('/login/index.php');
if ($vacancyid) {
    $loginurl->param('wantsurl', (new moodle_url('/local/jobboard/index.php', [
        'view' => 'apply',
        'vacancyid' => $vacancyid,
    ]))->out(false));
}

// Build template context.
$templatecontext = [
    'title' => get_string('signup_title', 'local_jobboard'),
    'intro' => get_string('signup_intro', 'local_jobboard'),
    'alreadyaccounttext' => get_string('signup_already_account', 'local_jobboard'),
    'loginurl' => $loginurl->out(false),
    'logintext' => get_string('login'),
    'requiredfieldstext' => get_string('signup_required_fields', 'local_jobboard'),
    'formhtml' => $mform->render(),
];

// Add vacancy info if available.
if ($vacancy) {
    $templatecontext['vacancy'] = [
        'title' => s($vacancy->title),
        'code' => s($vacancy->code),
        'location' => !empty($vacancy->location) ? s($vacancy->location) : '',
    ];
    $templatecontext['applyingfortext'] = get_string('signup_applying_for', 'local_jobboard');
    $templatecontext['codetext'] = get_string('code', 'local_jobboard');
}

// Render using template.
echo $OUTPUT->render_from_template('local_jobboard/signup_page', $templatecontext);

// JavaScript for dynamic department loading.
if ($isiomad) {
    $PAGE->requires->js_call_amd('local_jobboard/signup_form', 'init', []);
}

echo $OUTPUT->footer();

/**
 * Create a new user from form data.
 *
 * @param stdClass $data Form data.
 * @param bool $isiomad Whether IOMAD is installed.
 * @return stdClass|false The created user or false on failure.
 * @throws Exception If user creation fails.
 */
function create_user_from_form($data, $isiomad) {
    global $CFG, $DB;

    // Build user object with all Moodle standard fields.
    $user = new stdClass();
    // Use Moodle's registerauth if set, otherwise default to 'email' for plugin-specific registration.
    $user->auth = (!empty($CFG->registerauth) && $CFG->registerauth !== 'none') ? $CFG->registerauth : 'email';
    $user->confirmed = 0; // Requires email confirmation.
    $user->mnethostid = $CFG->mnet_localhost_id;

    // Username is the ID number (cleaned for use as username).
    $idnumber = trim($data->idnumber ?? '');
    $user->username = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($idnumber));
    $user->idnumber = $idnumber;

    $user->password = hash_internal_user_password($data->password);
    $user->email = trim($data->email);
    $user->firstname = trim($data->firstname);
    $user->lastname = trim($data->lastname);
    $user->phone1 = trim($data->phone1 ?? '');
    $user->phone2 = trim($data->phone2 ?? '');
    $user->address = trim($data->address ?? '');
    $user->city = trim($data->city ?? '');
    $user->country = $data->country ?? '';
    $user->institution = trim($data->institution ?? '');
    $user->department = trim($data->department_region ?? '');
    // Handle editor data format (array with 'text' and 'format' keys).
    if (is_array($data->description ?? null)) {
        $user->description = $data->description['text'] ?? '';
        $user->descriptionformat = $data->description['format'] ?? FORMAT_HTML;
    } else {
        $user->description = trim($data->description ?? '');
        $user->descriptionformat = FORMAT_HTML;
    }
    $user->lang = current_language();
    $user->calendartype = $CFG->calendartype;
    $user->timezone = $CFG->timezone ?? '99';
    $user->timecreated = time();
    $user->timemodified = $user->timecreated;
    $user->policyagreed = 1;
    $user->secret = random_string(15);

    // Start transaction for data integrity.
    $transaction = $DB->start_delegated_transaction();

    try {
        // Create the user in the database.
        $user->id = $DB->insert_record('user', $user);

        if (!$user->id) {
            throw new Exception('Failed to insert user record');
        }

        // Update user context.
        $usercontext = context_user::instance($user->id);

        // Trigger user created event.
        $event = \core\event\user_created::create([
            'objectid' => $user->id,
            'context' => context_system::instance(),
            'relateduserid' => $user->id,
            'other' => [
                'auth' => $user->auth,
            ],
        ]);
        $event->trigger();

        // Store extended profile data in jobboard profile table.
        store_extended_profile($user->id, $data);

        // Store consent records.
        store_user_consents($user->id, $data);

        // Assign to company in IOMAD if selected.
        if ($isiomad && !empty($data->companyid)) {
            assign_user_to_company($user->id, $data->companyid, $data->departmentid ?? 0);
        }

        // Store the vacancy they were applying for as user preference.
        if (!empty($data->vacancyid)) {
            set_user_preference('local_jobboard_pending_vacancy', $data->vacancyid, $user->id);
        }

        // Commit transaction.
        $transaction->allow_commit();

        // Log the signup in the audit table.
        log_signup_audit($user->id, $data);

        return $user;

    } catch (Exception $e) {
        $transaction->rollback($e);
        throw $e;
    }
}

/**
 * Store extended profile data for the applicant.
 *
 * @param int $userid The user ID.
 * @param stdClass $data The form data.
 */
function store_extended_profile($userid, $data) {
    global $DB;

    // Create applicant profile record.
    $profile = new stdClass();
    $profile->userid = $userid;
    $profile->doctype = $data->doctype ?? '';
    $profile->birthdate = $data->birthdate ?? 0;
    $profile->gender = $data->gender ?? '';
    $profile->education_level = $data->education_level ?? '';
    $profile->degree_title = trim($data->degree_title ?? '');
    $profile->expertise_area = trim($data->expertise_area ?? '');
    $profile->experience_years = $data->experience_years ?? '';
    $profile->timecreated = time();
    $profile->timemodified = time();

    // Check if our profile table exists.
    $dbman = $DB->get_manager();
    if ($dbman->table_exists('local_jobboard_applicant_profile')) {
        $DB->insert_record('local_jobboard_applicant_profile', $profile);
    } else {
        // Fallback: Store in user preferences.
        set_user_preference('local_jobboard_doctype', $profile->doctype, $userid);
        set_user_preference('local_jobboard_birthdate', $profile->birthdate, $userid);
        set_user_preference('local_jobboard_gender', $profile->gender, $userid);
        set_user_preference('local_jobboard_education_level', $profile->education_level, $userid);
        set_user_preference('local_jobboard_degree_title', $profile->degree_title, $userid);
        set_user_preference('local_jobboard_expertise_area', $profile->expertise_area, $userid);
        set_user_preference('local_jobboard_experience_years', $profile->experience_years, $userid);
    }
}

/**
 * Store user consent records.
 *
 * @param int $userid The user ID.
 * @param stdClass $data The form data.
 */
function store_user_consents($userid, $data) {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('local_jobboard_consent')) {
        return;
    }

    $now = time();
    $ipaddress = getremoteaddr();
    // Sanitize user agent to prevent XSS in logs.
    $useragent = substr(clean_param($_SERVER['HTTP_USER_AGENT'] ?? '', PARAM_TEXT), 0, 512);

    // Terms consent.
    if (!empty($data->policyagreed)) {
        $consent = new stdClass();
        $consent->userid = $userid;
        $consent->consenttype = 'terms';
        $consent->consentgiven = 1;
        $consent->consentversion = get_config('local_jobboard', 'terms_version') ?: '1.0';
        $consent->ipaddress = $ipaddress;
        $consent->useragent = $useragent;
        $consent->timecreated = $now;
        $DB->insert_record('local_jobboard_consent', $consent);
    }

    // Data treatment consent.
    if (!empty($data->datatreatmentagreed)) {
        $consent = new stdClass();
        $consent->userid = $userid;
        $consent->consenttype = 'datatreatment';
        $consent->consentgiven = 1;
        $consent->consentversion = get_config('local_jobboard', 'datatreatment_version') ?: '1.0';
        $consent->ipaddress = $ipaddress;
        $consent->useragent = $useragent;
        $consent->timecreated = $now;
        $DB->insert_record('local_jobboard_consent', $consent);
    }

    // Data accuracy consent.
    if (!empty($data->dataaccuracy)) {
        $consent = new stdClass();
        $consent->userid = $userid;
        $consent->consenttype = 'dataaccuracy';
        $consent->consentgiven = 1;
        $consent->consentversion = '1.0';
        $consent->ipaddress = $ipaddress;
        $consent->useragent = $useragent;
        $consent->timecreated = $now;
        $DB->insert_record('local_jobboard_consent', $consent);
    }
}

/**
 * Assign user to an IOMAD company.
 *
 * @param int $userid The user ID.
 * @param int $companyid The company ID.
 * @param int $departmentid The department ID (optional).
 */
function assign_user_to_company($userid, $companyid, $departmentid = 0) {
    global $DB;

    // Check if company_users table exists.
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('company_users')) {
        return;
    }

    // Get the default department for the company if none specified.
    if (!$departmentid && $dbman->table_exists('department')) {
        $dept = $DB->get_record('department', [
            'company' => $companyid,
            'parent' => 0,
        ], 'id', IGNORE_MULTIPLE);
        if ($dept) {
            $departmentid = $dept->id;
        }
    }

    // Check if user is already assigned.
    if ($DB->record_exists('company_users', ['userid' => $userid, 'companyid' => $companyid])) {
        return;
    }

    // Create the company user record.
    $companyuser = new stdClass();
    $companyuser->companyid = $companyid;
    $companyuser->userid = $userid;
    $companyuser->departmentid = $departmentid;
    $companyuser->managertype = 0;
    $companyuser->educator = 0;
    $companyuser->lastused = time();

    $DB->insert_record('company_users', $companyuser);
}

/**
 * Send confirmation email to the new user.
 *
 * This function uses Moodle's standard confirmation email mechanism.
 *
 * @param stdClass $user The user object.
 * @return bool True if email was sent successfully.
 */
function send_confirmation_email_to_user($user) {
    global $CFG;

    // Use Moodle's standard confirmation email function.
    return send_confirmation_email($user);
}

/**
 * Log the signup in the audit table.
 *
 * @param int $userid The user ID.
 * @param stdClass $data The form data.
 */
function log_signup_audit($userid, $data) {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('local_jobboard_audit')) {
        return;
    }

    $audit = new stdClass();
    $audit->userid = $userid;
    $audit->action = 'user_signup';
    $audit->entitytype = 'user';
    $audit->entityid = $userid;
    $audit->ipaddress = getremoteaddr();
    // Sanitize user agent to prevent XSS in logs.
    $audit->useragent = substr(clean_param($_SERVER['HTTP_USER_AGENT'] ?? '', PARAM_TEXT), 0, 512);
    $audit->extradata = json_encode([
        'vacancyid' => $data->vacancyid ?? 0,
        'companyid' => $data->companyid ?? 0,
        'source' => 'jobboard_signup',
    ]);
    $audit->timecreated = time();

    $DB->insert_record('local_jobboard_audit', $audit);
}
