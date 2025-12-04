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
 * Apply for a vacancy page.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\vacancy;
use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\exemption;
use local_jobboard\forms\application_form;

$vacancyid = required_param('vacancyid', PARAM_INT);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:apply', $context);

// Get vacancy.
$vacancy = vacancy::get($vacancyid);
if (!$vacancy) {
    throw new moodle_exception('vacancynotfound', 'local_jobboard');
}

// Check vacancy is open for applications.
if (!$vacancy->is_open_for_applications()) {
    throw new moodle_exception('vacancynotopen', 'local_jobboard');
}

// Check user hasn't already applied.
if (application::user_has_applied($USER->id, $vacancyid)) {
    redirect(
        new moodle_url('/local/jobboard/applications.php'),
        get_string('alreadyapplied', 'local_jobboard'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Check for ISER exemption.
$isexemption = false;
$exemptioninfo = null;
$exemption = exemption::get_active_for_user($USER->id);
if ($exemption) {
    $isexemption = true;
    $exemptioninfo = $exemption;
}

// Get required document types.
$requireddocs = exemption::get_required_doctypes($USER->id, true);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/apply.php', ['vacancyid' => $vacancyid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('applytovacancy', 'local_jobboard'));
$PAGE->set_heading(get_string('applytovacancy', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('vacancies', 'local_jobboard'), new moodle_url('/local/jobboard/vacancies.php'));
$PAGE->navbar->add(format_string($vacancy->title), new moodle_url('/local/jobboard/vacancy.php', ['id' => $vacancyid]));
$PAGE->navbar->add(get_string('apply', 'local_jobboard'));

// Create form.
$customdata = [
    'vacancy' => $vacancy->get_record(),
    'requireddocs' => $requireddocs,
    'isexemption' => $isexemption,
    'exemptioninfo' => $exemptioninfo ? (object) [
        'exemptiontype' => $exemptioninfo->exemptiontype,
        'documentref' => $exemptioninfo->documentref,
    ] : null,
];

$mform = new application_form(null, $customdata);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/vacancy.php', ['id' => $vacancyid]));
}

if ($data = $mform->get_data()) {
    // Process the application.
    try {
        // Create application record.
        $applicationdata = [
            'vacancyid' => $vacancyid,
            'userid' => $USER->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => getremoteaddr(),
            'digitalsignature' => $data->digitalsignature,
            'isexemption' => $isexemption ? 1 : 0,
            'coverletter' => $data->coverletter ?? null,
        ];

        $application = application::create($applicationdata);

        if (!$application) {
            throw new moodle_exception('applicationcreatefailed', 'local_jobboard');
        }

        // Process document uploads.
        $documentdata = $mform->get_document_data();
        foreach ($documentdata as $doctypecode => $docinfo) {
            try {
                document::store_from_draft(
                    $application->id,
                    $doctypecode,
                    $docinfo['draftitemid'],
                    $docinfo['issuedate'] ?? null
                );
            } catch (\Exception $e) {
                // Log but don't fail the whole application.
                debugging('Document upload failed for ' . $doctypecode . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        // Redirect to applications list with success message.
        redirect(
            new moodle_url('/local/jobboard/applications.php'),
            get_string('applicationsubmitted', 'local_jobboard'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );

    } catch (\Exception $e) {
        \core\notification::error(get_string('applicationerror', 'local_jobboard') . ': ' . $e->getMessage());
    }
}

// Output page.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('applytovacancy', 'local_jobboard') . ': ' . format_string($vacancy->title));

// Show deadline warning if close to deadline.
$daysuntilclose = ($vacancy->closedate - time()) / (24 * 60 * 60);
if ($daysuntilclose <= 3) {
    echo $OUTPUT->notification(
        get_string('deadlinewarning', 'local_jobboard', ceil($daysuntilclose)),
        \core\output\notification::NOTIFY_WARNING
    );
}

// Show application guidelines.
echo '<div class="application-guidelines mb-4">';
echo '<h4>' . get_string('applicationguidelines', 'local_jobboard') . '</h4>';
echo '<ul>';
echo '<li>' . get_string('guideline1', 'local_jobboard') . '</li>';
echo '<li>' . get_string('guideline2', 'local_jobboard') . '</li>';
echo '<li>' . get_string('guideline3', 'local_jobboard') . '</li>';
echo '<li>' . get_string('guideline4', 'local_jobboard') . '</li>';
echo '</ul>';
echo '</div>';

$mform->display();

echo $OUTPUT->footer();
