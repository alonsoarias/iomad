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
 * Apply for a vacancy view.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\vacancy;
use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\exemption;
use local_jobboard\convocatoria_exemption;
use local_jobboard\forms\application_form;

// Parameters.
$vacancyid = required_param('vacancyid', PARAM_INT);

// Require apply capability.
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

// Check user hasn't already applied to this specific vacancy.
if (application::user_has_applied($USER->id, $vacancyid)) {
    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        get_string('alreadyapplied', 'local_jobboard'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Check convocatoria-level application restrictions.
$applicationcheck = \local_jobboard_can_user_apply_to_vacancy($USER->id, $vacancyid);
if (!$applicationcheck['can_apply']) {
    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
        $applicationcheck['reason'],
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Check experience requirements for occasional contracts.
$experiencecheck = \local_jobboard_check_experience_requirements($USER->id, $vacancyid);
if (!$experiencecheck['meets_requirements']) {
    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]),
        $experiencecheck['reason'],
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Check if user has completed their applicant profile.
$applicantprofile = $DB->get_record('local_jobboard_applicant_profile', ['userid' => $USER->id]);
if (!$applicantprofile || empty($applicantprofile->profile_complete)) {
    redirect(
        new moodle_url('/local/jobboard/updateprofile.php', ['vacancyid' => $vacancyid]),
        get_string('completeprofile_required', 'local_jobboard'),
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

// Get user's gender from applicant profile.
$usergender = $applicantprofile->gender ?? '';

// Get user's age for document exemptions.
$userage = \local_jobboard_get_user_age($USER->id);

// Get convocatoria ID from vacancy for exemption filtering.
$convocatoriaid = $vacancy->convocatoriaid ?? 0;

// Get all document types filtered by convocatoria and user exemptions.
if ($convocatoriaid) {
    $requireddocs = convocatoria_exemption::get_required_doctypes_for_convocatoria($convocatoriaid, false, true);
} else {
    $requireddocs = $DB->get_records('local_jobboard_doctype', ['enabled' => 1], 'sortorder ASC');
}

// Further filter by user exemption if user has one.
if ($isexemption && $exemption) {
    $exemptedcodes = $exemption->get_required_document_codes();
    if (!empty($exemptedcodes)) {
        $requireddocs = array_filter($requireddocs, fn($dt) => in_array($dt->code, $exemptedcodes));
    }
}

// Set up page.
$PAGE->set_title(get_string('applytovacancy', 'local_jobboard'));
$PAGE->set_heading(get_string('applytovacancy', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Get convocatoria exemption summary for display.
$convexemptionsummary = null;
if ($convocatoriaid) {
    $convexemptionsummary = convocatoria_exemption::get_exemption_summary($convocatoriaid);
}

// Create form.
$customdata = [
    'vacancy' => $vacancy->get_record(),
    'requireddocs' => $requireddocs,
    'isexemption' => $isexemption,
    'exemptioninfo' => $exemptioninfo ? (object) [
        'exemptiontype' => $exemptioninfo->exemptiontype,
        'documentref' => $exemptioninfo->documentref,
    ] : null,
    'usergender' => $usergender,
    'userage' => $userage,
    'convocatoriaid' => $convocatoriaid,
    'convexemptionsummary' => $convexemptionsummary,
];

$mform = new application_form(null, $customdata);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]));
}

if ($data = $mform->get_data()) {
    // Process the application.
    try {
        // Handle editor format for coverletter.
        $coverletter = null;
        if (!empty($data->coverletter)) {
            if (is_array($data->coverletter)) {
                $coverletter = $data->coverletter['text'] ?? '';
            } else {
                $coverletter = $data->coverletter;
            }
        }

        $applicationdata = [
            'vacancyid' => $vacancyid,
            'userid' => $USER->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => getremoteaddr(),
            'digitalsignature' => $data->digitalsignature,
            'isexemption' => $isexemption ? 1 : 0,
            'coverletter' => $coverletter,
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
                debugging('Document upload failed for ' . $doctypecode . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        // Redirect to applications list with success message.
        redirect(
            new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
            get_string('applicationsubmitted', 'local_jobboard'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );

    } catch (\Exception $e) {
        \core\notification::error(get_string('applicationerror', 'local_jobboard') . ': ' . $e->getMessage());
    }
}

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Calculate days until close.
$closedate = $vacancy->get_close_date();
$daysuntilclose = (int) ceil(($closedate - time()) / 86400);

// Capture form HTML.
ob_start();
$mform->display();
$formhtml = ob_get_clean();

// Prepare template data.
$data = $renderer->prepare_apply_page_data(
    $vacancy,
    $requireddocs,
    $isexemption,
    $exemptioninfo,
    $formhtml,
    $daysuntilclose
);

// Output page.
echo $OUTPUT->header();
echo $renderer->render_apply_page($data);

// Initialize JavaScript for tab-based navigation via AMD module.
$PAGE->requires->js_call_amd('local_jobboard/apply_progress', 'init', [[
    'initialStep' => 0,
    'tabMode' => true,
    'strings' => [
        'previous' => get_string('previous'),
        'next' => get_string('next'),
        'submit' => get_string('submit', 'local_jobboard'),
        'step' => get_string('step', 'local_jobboard'),
        'of' => get_string('of', 'local_jobboard'),
        'completerequiredfields' => get_string('completerequiredfields', 'local_jobboard'),
    ],
]]);

// Initialize application confirmation modal.
$PAGE->requires->js_call_amd('local_jobboard/application_confirm', 'init', [[
    'formSelector' => 'form.mform',
    'totalDocs' => count($requireddocs),
    'uploadedDocs' => 0,
]]);

// Initialize loading states.
$PAGE->requires->js_call_amd('local_jobboard/loading_states', 'init', []);

echo $OUTPUT->footer();
