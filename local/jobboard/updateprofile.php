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
 * Profile update page for Job Board applicants.
 *
 * This page allows logged-in users to update their profile and select
 * a company/department before applying for a vacancy.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');

// Require login.
require_login();

// Parameters.
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_URL);

// Get current user.
global $USER, $DB;
$user = $DB->get_record('user', ['id' => $USER->id], '*', MUST_EXIST);

// Set up the page.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/updateprofile.php', ['vacancyid' => $vacancyid]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('updateprofile_title', 'local_jobboard'));
$PAGE->set_heading(get_string('updateprofile_title', 'local_jobboard'));

// Add navigation.
$PAGE->navbar->add(get_string('publicvacancies', 'local_jobboard'), new moodle_url('/local/jobboard/public.php'));
$PAGE->navbar->add(get_string('updateprofile_title', 'local_jobboard'));

// Check IOMAD installation.
$isiomad = local_jobboard_is_iomad_installed();
$companies = [];
$usercompanyid = 0;
$userdepartmentid = 0;

if ($isiomad) {
    $companies = local_jobboard_get_companies();

    // Get user's current company assignment.
    $companyuser = $DB->get_record('company_users', ['userid' => $USER->id], 'companyid, departmentid');
    if ($companyuser) {
        $usercompanyid = $companyuser->companyid;
        $userdepartmentid = $companyuser->departmentid;
    }
}

// Get vacancy info if provided.
$vacancy = null;
if ($vacancyid) {
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid, 'status' => 'published']);
}

// Get user's extended profile if exists.
$userprofile = $DB->get_record('local_jobboard_applicant_profile', ['userid' => $USER->id]);

// Create the form.
$formdata = [
    'vacancyid' => $vacancyid,
    'companies' => $companies,
    'isiomad' => $isiomad,
    'userid' => $USER->id,
    'usercompanyid' => $usercompanyid,
];

$mform = new \local_jobboard\forms\updateprofile_form(null, $formdata);

// Set default values from existing user data.
$defaultdata = new stdClass();
$defaultdata->firstname = $user->firstname;
$defaultdata->lastname = $user->lastname;
$defaultdata->email = $user->email;
$defaultdata->idnumber = $user->idnumber;
$defaultdata->phone1 = $user->phone1;
$defaultdata->phone2 = $user->phone2;
$defaultdata->address = $user->address;
$defaultdata->city = $user->city;
$defaultdata->country = $user->country;
$defaultdata->institution = $user->institution;
$defaultdata->department_region = $user->department;
$defaultdata->description = $user->description;
$defaultdata->companyid = $usercompanyid;
$defaultdata->departmentid = $userdepartmentid;

// Add extended profile data if exists.
if ($userprofile) {
    $defaultdata->doctype = $userprofile->doctype;
    $defaultdata->birthdate = $userprofile->birthdate;
    $defaultdata->gender = $userprofile->gender;
    $defaultdata->education_level = $userprofile->education_level;
    $defaultdata->degree_title = $userprofile->degree_title;
    $defaultdata->expertise_area = $userprofile->expertise_area;
    $defaultdata->experience_years = $userprofile->experience_years;
}

$mform->set_data($defaultdata);

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
    // Update user record.
    $updateuser = new stdClass();
    $updateuser->id = $USER->id;
    $updateuser->firstname = trim($data->firstname);
    $updateuser->lastname = trim($data->lastname);
    $updateuser->phone1 = trim($data->phone1 ?? '');
    $updateuser->phone2 = trim($data->phone2 ?? '');
    $updateuser->address = trim($data->address ?? '');
    $updateuser->city = trim($data->city ?? '');
    $updateuser->country = $data->country ?? '';
    $updateuser->institution = trim($data->institution ?? '');
    $updateuser->department = trim($data->department_region ?? '');
    $updateuser->description = trim($data->description ?? '');
    $updateuser->timemodified = time();

    // Update idnumber if empty.
    if (empty($user->idnumber) && !empty($data->idnumber)) {
        $updateuser->idnumber = trim($data->idnumber);
    }

    $DB->update_record('user', $updateuser);

    // Update or create extended profile.
    $profile = new stdClass();
    $profile->userid = $USER->id;
    $profile->doctype = $data->doctype ?? '';
    $profile->birthdate = $data->birthdate ?? 0;
    $profile->gender = $data->gender ?? '';
    $profile->education_level = $data->education_level ?? '';
    $profile->degree_title = trim($data->degree_title ?? '');
    $profile->expertise_area = trim($data->expertise_area ?? '');
    $profile->experience_years = $data->experience_years ?? '';
    $profile->profile_complete = 1;
    $profile->timemodified = time();

    $dbman = $DB->get_manager();
    if ($dbman->table_exists('local_jobboard_applicant_profile')) {
        if ($userprofile) {
            $profile->id = $userprofile->id;
            $DB->update_record('local_jobboard_applicant_profile', $profile);
        } else {
            $profile->timecreated = time();
            $DB->insert_record('local_jobboard_applicant_profile', $profile);
        }
    }

    // Update company assignment in IOMAD.
    if ($isiomad && !empty($data->companyid)) {
        update_user_company($USER->id, $data->companyid, $data->departmentid ?? 0);
    }

    // Show success and redirect.
    \core\notification::success(get_string('updateprofile_success', 'local_jobboard'));

    if ($vacancyid) {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancyid]));
    } else {
        redirect(new moodle_url('/local/jobboard/index.php'));
    }
}

// Display the form.
echo $OUTPUT->header();

// Page header.
echo html_writer::start_div('updateprofile-header mb-4');
echo html_writer::tag('h1', get_string('updateprofile_title', 'local_jobboard'), ['class' => 'h2']);
echo html_writer::tag('p', get_string('updateprofile_intro', 'local_jobboard'), ['class' => 'lead text-muted']);
echo html_writer::end_div();

// Show vacancy info if available.
if ($vacancy) {
    echo html_writer::start_div('card mb-4 border-primary');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::tag('h5', html_writer::tag('i', '', ['class' => 'fa fa-briefcase me-2']) .
        get_string('signup_applying_for', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', s($vacancy->title), ['class' => 'card-title mb-2']);
    echo html_writer::tag('p',
        html_writer::tag('span', get_string('code', 'local_jobboard') . ': ', ['class' => 'text-muted']) .
        html_writer::tag('strong', s($vacancy->code)),
        ['class' => 'card-text mb-1']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Display the form.
$mform->display();

// JavaScript for dynamic department loading.
if ($isiomad) {
    $PAGE->requires->js_call_amd('local_jobboard/signup_form', 'init', []);
}

echo $OUTPUT->footer();

/**
 * Update user's company assignment in IOMAD.
 *
 * @param int $userid The user ID.
 * @param int $companyid The company ID.
 * @param int $departmentid The department ID.
 */
function update_user_company($userid, $companyid, $departmentid = 0) {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('company_users')) {
        return;
    }

    // Get default department if not specified.
    if (!$departmentid && $dbman->table_exists('department')) {
        $dept = $DB->get_record('department', ['company' => $companyid, 'parent' => 0], 'id', IGNORE_MULTIPLE);
        if ($dept) {
            $departmentid = $dept->id;
        }
    }

    // Check if user already has a company assignment.
    $existing = $DB->get_record('company_users', ['userid' => $userid]);

    if ($existing) {
        // Update existing assignment.
        $existing->companyid = $companyid;
        $existing->departmentid = $departmentid;
        $existing->lastused = time();
        $DB->update_record('company_users', $existing);
    } else {
        // Create new assignment.
        $companyuser = new stdClass();
        $companyuser->companyid = $companyid;
        $companyuser->userid = $userid;
        $companyuser->departmentid = $departmentid;
        $companyuser->managertype = 0;
        $companyuser->educator = 0;
        $companyuser->lastused = time();
        $DB->insert_record('company_users', $companyuser);
    }
}
