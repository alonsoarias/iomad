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
 * This page allows new users to register and optionally select a company
 * when they want to apply for vacancies. It works alongside IOMAD's
 * multi-tenant architecture.
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

// Check if user is already logged in.
if (isloggedin() && !isguestuser()) {
    $vacancyid = optional_param('vacancyid', 0, PARAM_INT);
    if ($vacancyid) {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancyid]));
    } else {
        redirect(new moodle_url('/local/jobboard/index.php'));
    }
}

// Check if self-registration is allowed.
if (empty($CFG->registerauth) || $CFG->registerauth === 'none') {
    // Self-registration is disabled - show error.
    throw new moodle_exception('registrationdisabled', 'local_jobboard');
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
$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string('signup_title', 'local_jobboard'));
$PAGE->set_heading(get_string('signup_title', 'local_jobboard'));

// Add navigation.
$PAGE->navbar->add(get_string('publicvacancies', 'local_jobboard'), new moodle_url('/local/jobboard/public.php'));
$PAGE->navbar->add(get_string('signup_title', 'local_jobboard'));

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
    // Create the user account.
    $user = create_user_from_form($data, $isiomad);

    if ($user) {
        // Send confirmation email.
        if (!send_confirmation_email($user)) {
            throw new moodle_exception('signup_email_error', 'local_jobboard');
        }

        // Show success message.
        $PAGE->set_title(get_string('signup_success_title', 'local_jobboard'));
        $PAGE->set_heading(get_string('signup_success_title', 'local_jobboard'));

        echo $OUTPUT->header();

        echo html_writer::start_div('signup-success text-center my-5');
        echo html_writer::tag('i', '', ['class' => 'fa fa-check-circle text-success fa-5x mb-3']);
        echo html_writer::tag('h2', get_string('signup_success_title', 'local_jobboard'));
        echo html_writer::tag('p', get_string('signup_success_message', 'local_jobboard', $user->email), ['class' => 'lead']);
        echo html_writer::tag('p', get_string('signup_success_instructions', 'local_jobboard'));

        echo html_writer::start_div('mt-4');
        echo html_writer::link(
            new moodle_url('/local/jobboard/public.php'),
            get_string('backtovacancies', 'local_jobboard'),
            ['class' => 'btn btn-primary']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();

        echo $OUTPUT->footer();
        exit;
    }
}

// Display the form.
echo $OUTPUT->header();

// Page header.
echo html_writer::start_div('signup-header mb-4');
echo html_writer::tag('h1', get_string('signup_title', 'local_jobboard'), ['class' => 'h2']);
echo html_writer::tag('p', get_string('signup_intro', 'local_jobboard'), ['class' => 'lead']);
echo html_writer::end_div();

// Show vacancy info if available.
if ($vacancy) {
    echo html_writer::start_div('card mb-4');
    echo html_writer::start_div('card-header');
    echo html_writer::tag('h5', get_string('signup_applying_for', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', s($vacancy->title), ['class' => 'card-title']);
    echo html_writer::tag('p',
        html_writer::tag('strong', get_string('code', 'local_jobboard') . ': ') . s($vacancy->code),
        ['class' => 'card-text mb-1']
    );
    if (!empty($vacancy->location)) {
        echo html_writer::tag('p',
            html_writer::tag('i', '', ['class' => 'fa fa-map-marker me-1']) . s($vacancy->location),
            ['class' => 'card-text text-muted']
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Login alternative.
echo html_writer::start_div('alert alert-info mb-4');
echo html_writer::tag('strong', get_string('signup_already_account', 'local_jobboard'));
echo ' ';
$loginurl = new moodle_url('/login/index.php');
if ($vacancyid) {
    $loginurl->param('wantsurl', (new moodle_url('/local/jobboard/index.php', [
        'view' => 'apply',
        'vacancyid' => $vacancyid,
    ]))->out(false));
}
echo html_writer::link($loginurl, get_string('login'), ['class' => 'btn btn-sm btn-primary']);
echo html_writer::end_div();

// Display the form.
$mform->display();

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
 */
function create_user_from_form($data, $isiomad) {
    global $CFG, $DB;

    // Build user object.
    $user = new stdClass();
    $user->auth = $CFG->registerauth;
    $user->confirmed = 0;
    $user->mnethostid = $CFG->mnet_localhost_id;
    $user->username = trim(strtolower($data->username));
    $user->password = hash_internal_user_password($data->password);
    $user->email = trim($data->email);
    $user->firstname = trim($data->firstname);
    $user->lastname = trim($data->lastname);
    $user->phone1 = trim($data->phone1 ?? '');
    $user->city = trim($data->city ?? '');
    $user->country = $data->country ?? '';
    $user->idnumber = trim($data->idnumber ?? '');
    $user->lang = current_language();
    $user->calendartype = $CFG->calendartype;
    $user->timecreated = time();
    $user->timemodified = $user->timecreated;
    $user->policyagreed = 1;
    $user->secret = random_string(15);

    try {
        // Create the user.
        $user->id = $DB->insert_record('user', $user);

        // Trigger user created event.
        $event = \core\event\user_created::create([
            'objectid' => $user->id,
            'context' => context_system::instance(),
            'relateduserid' => $user->id,
        ]);
        $event->trigger();

        // Assign to company in IOMAD if selected.
        if ($isiomad && !empty($data->companyid)) {
            assign_user_to_company($user->id, $data->companyid, $data->departmentid ?? 0);
        }

        // Store job board specific data.
        store_jobboard_user_data($user->id, $data);

        return $user;
    } catch (Exception $e) {
        debugging('Error creating user: ' . $e->getMessage(), DEBUG_DEVELOPER);
        return false;
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
 * Store job board specific user data.
 *
 * @param int $userid The user ID.
 * @param stdClass $data The form data.
 */
function store_jobboard_user_data($userid, $data) {
    global $DB;

    // Store the data treatment consent.
    $consent = new stdClass();
    $consent->userid = $userid;
    $consent->consenttype = 'datatreatment';
    $consent->consentgiven = !empty($data->datatreatmentagreed) ? 1 : 0;
    $consent->timecreated = time();
    $consent->ipaddress = getremoteaddr();
    $consent->useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Check if jobboard consent table exists.
    $dbman = $DB->get_manager();
    if ($dbman->table_exists('local_jobboard_consent')) {
        $DB->insert_record('local_jobboard_consent', $consent);
    }

    // Store the vacancy they were applying for.
    if (!empty($data->vacancyid)) {
        // Store as user preference for later redirect.
        set_user_preference('local_jobboard_pending_vacancy', $data->vacancyid, $userid);
    }
}
