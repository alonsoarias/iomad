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
 * This file is included by index.php and should not be accessed directly.
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
// Only applicants need to have complete profiles - this doesn't affect regular users/students.
$applicantprofile = $DB->get_record('local_jobboard_applicant_profile', ['userid' => $USER->id]);
if (!$applicantprofile || empty($applicantprofile->profile_complete)) {
    // Redirect to profile update page with vacancy context.
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

// Get required document types.
$requireddocs = exemption::get_required_doctypes($USER->id, true);

// Set up page.
$PAGE->set_title(get_string('applytovacancy', 'local_jobboard'));
$PAGE->set_heading(get_string('applytovacancy', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

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
];

$mform = new application_form(null, $customdata);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]));
}

if ($data = $mform->get_data()) {
    // Process the application.
    try {
        // Create application record.
        // Handle editor format for coverletter (array with 'text' and 'format' keys).
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
                // Log but don't fail the whole application.
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

// Output page.
echo $OUTPUT->header();

// Include inline styles.
echo \local_jobboard\output\ui_helper::get_inline_styles();

// Calculate days until close (dates now come from convocatoria).
$closedate = $vacancy->get_close_date();
$daysuntilclose = ($closedate - time()) / (24 * 60 * 60);

// Main container with two-column layout.
echo '<div class="jb-apply-container">';
echo '<div class="row">';

// Main content area (left column).
echo '<div class="col-lg-8">';
echo '<div class="jb-content-area">';

// Page header.
echo '<div class="jb-page-header mb-4">';
echo '<h2 class="mb-2">' . get_string('applytovacancy', 'local_jobboard') . '</h2>';
echo '<p class="lead text-muted">' . format_string($vacancy->title) . '</p>';
echo '</div>';

// Progress indicator - Tab navigation with steps.
echo '<div class="jb-progress-steps mb-4" id="application-progress">';
echo '<div class="d-flex justify-content-between align-items-center">';
$steps = [
    ['icon' => 'fa-file-signature', 'label' => get_string('step_consent', 'local_jobboard'), 'target' => 'id_consentheader'],
    ['icon' => 'fa-upload', 'label' => get_string('step_documents', 'local_jobboard'), 'target' => 'id_documentsheader'],
    ['icon' => 'fa-envelope-open-text', 'label' => get_string('step_coverletter', 'local_jobboard'), 'target' => 'id_additionalheader'],
    ['icon' => 'fa-paper-plane', 'label' => get_string('step_submit', 'local_jobboard'), 'target' => 'id_declarationheader'],
];
foreach ($steps as $i => $step) {
    $activeclass = $i === 0 ? 'active' : '';
    echo '<a href="#" class="jb-step text-center text-decoration-none ' . $activeclass . '" ';
    echo 'data-step="' . ($i + 1) . '" data-target="' . $step['target'] . '" role="tab" ';
    echo 'aria-selected="' . ($i === 0 ? 'true' : 'false') . '">';
    echo '<div class="jb-step-icon rounded-circle d-inline-flex align-items-center justify-content-center mb-1">';
    echo '<span class="jb-step-number">' . ($i + 1) . '</span>';
    echo '<i class="fa fa-check jb-step-checkmark d-none"></i>';
    echo '</div>';
    echo '<small class="d-block text-truncate" style="max-width:80px;">' . $step['label'] . '</small>';
    echo '</a>';
    if ($i < count($steps) - 1) {
        echo '<div class="jb-step-connector flex-grow-1 mx-2"></div>';
    }
}
echo '</div>';
echo '</div>';

// Tab navigation buttons container (will be populated by JS).
echo '<div id="jb-tab-navigation" class="d-none mb-3"></div>';

// Deadline warning.
if ($daysuntilclose <= 3 && $daysuntilclose > 0) {
    echo '<div class="alert alert-danger d-flex align-items-center mb-4" role="alert">';
    echo '<i class="fa fa-exclamation-triangle fa-2x mr-3"></i>';
    echo '<div>';
    echo '<strong>' . get_string('deadlinewarning_title', 'local_jobboard') . '</strong><br>';
    echo get_string('deadlinewarning', 'local_jobboard', ceil($daysuntilclose));
    echo '</div>';
    echo '</div>';
}

// Guidelines card (collapsible).
echo '<div class="card jb-guidelines-card mb-4" id="guidelines-card">';
echo '<div class="card-header bg-info text-white" style="cursor:pointer;" ';
echo 'data-toggle="collapse" data-target="#guidelines-collapse" aria-expanded="true">';
echo '<div class="d-flex justify-content-between align-items-center">';
echo '<span><i class="fa fa-info-circle mr-2"></i>' . get_string('applicationguidelines', 'local_jobboard') . '</span>';
echo '<i class="fa fa-chevron-down jb-collapse-icon"></i>';
echo '</div>';
echo '</div>';
echo '<div class="collapse show" id="guidelines-collapse">';
echo '<div class="card-body">';
echo '<ul class="mb-0 pl-3">';
echo '<li class="mb-2">' . get_string('guideline1', 'local_jobboard') . '</li>';
echo '<li class="mb-2">' . get_string('guideline2', 'local_jobboard') . '</li>';
echo '<li class="mb-2">' . get_string('guideline3', 'local_jobboard') . '</li>';
echo '<li>' . get_string('guideline4', 'local_jobboard') . '</li>';
echo '</ul>';
echo '</div>';
echo '</div>';
echo '</div>';

// Display the form.
$mform->display();

echo '</div>'; // .jb-content-area
echo '</div>'; // .col-lg-8

// Sidebar (right column).
echo '<div class="col-lg-4">';

// Vacancy summary card.
echo '<div class="card jb-sidebar-card shadow-sm mb-4 sticky-top" style="top:20px;">';
echo '<div class="card-header bg-primary text-white">';
echo '<i class="fa fa-briefcase mr-2"></i>' . get_string('vacancysummary', 'local_jobboard');
echo '</div>';
echo '<div class="card-body">';
echo '<p class="mb-2"><strong>' . get_string('code', 'local_jobboard') . ':</strong><br>';
echo '<code class="bg-light p-1">' . format_string($vacancy->code) . '</code></p>';
echo '<p class="mb-2"><strong>' . get_string('title', 'local_jobboard') . ':</strong><br>';
echo '<small>' . format_string($vacancy->title) . '</small></p>';

// Location from IOMAD company.
$companyName = $vacancy->get_company_name();
if (!empty($companyName)) {
    echo '<p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-2"></i>';
    echo '<strong>' . get_string('location', 'local_jobboard') . ':</strong> ';
    echo format_string($companyName) . '</p>';
} elseif (!empty($vacancy->location)) {
    // Fallback to location field.
    echo '<p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-2"></i>';
    echo format_string($vacancy->location) . '</p>';
}

// Modality from IOMAD department.
$departmentName = $vacancy->get_department_name();
if (!empty($departmentName)) {
    echo '<p class="mb-2"><i class="fa fa-laptop-house text-info mr-2"></i>';
    echo '<strong>' . get_string('modality', 'local_jobboard') . ':</strong> ';
    echo format_string($departmentName) . '</p>';
} else {
    $vacancyrecord = $vacancy->get_record();
    if (!empty($vacancyrecord->modality)) {
        // Fallback to modality field.
        $modalities = \local_jobboard_get_modalities();
        $modalityName = $modalities[$vacancyrecord->modality] ?? $vacancyrecord->modality;
        echo '<p class="mb-2"><i class="fa fa-laptop-house text-info mr-2"></i>';
        echo '<strong>' . get_string('modality', 'local_jobboard') . ':</strong> ';
        echo $modalityName . '</p>';
    }
}

echo '<hr>';
echo '<p class="mb-2"><i class="fa fa-calendar-times text-danger mr-2"></i>';
echo '<strong>' . get_string('closedate', 'local_jobboard') . ':</strong><br>';
echo userdate($closedate, get_string('strftimedatetime', 'langconfig')) . '</p>';

// Time remaining badge.
if ($daysuntilclose > 0) {
    $badgeclass = $daysuntilclose <= 3 ? 'badge-danger' : ($daysuntilclose <= 7 ? 'badge-warning' : 'badge-success');
    echo '<span class="badge ' . $badgeclass . ' p-2">';
    echo '<i class="fa fa-clock mr-1"></i>';
    echo get_string('daysremaining', 'local_jobboard', ceil($daysuntilclose));
    echo '</span>';
}
echo '</div>';
echo '</div>';

// Help card - Enhanced with more resources.
echo '<div class="card jb-sidebar-card shadow-sm mb-4">';
echo '<div class="card-header bg-secondary text-white">';
echo '<i class="fa fa-question-circle mr-2"></i>' . get_string('needhelp', 'local_jobboard');
echo '</div>';
echo '<div class="card-body">';
echo '<p class="small mb-3">' . get_string('applyhelp_text', 'local_jobboard') . '</p>';

// Quick tips.
echo '<div class="small mb-3">';
echo '<p class="font-weight-bold mb-2"><i class="fa fa-lightbulb text-warning mr-1"></i>' . get_string('quicktips', 'local_jobboard') . '</p>';
echo '<ul class="mb-0 pl-3">';
echo '<li class="mb-1">' . get_string('tip_saveoften', 'local_jobboard') . '</li>';
echo '<li class="mb-1">' . get_string('tip_checkdocs', 'local_jobboard') . '</li>';
echo '<li>' . get_string('tip_deadline', 'local_jobboard') . '</li>';
echo '</ul>';
echo '</div>';

// Back to vacancy details.
echo '<a href="' . (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancyid]))->out() . '" ';
echo 'class="btn btn-outline-secondary btn-sm btn-block">';
echo '<i class="fa fa-info-circle mr-1"></i>' . get_string('viewvacancydetails', 'local_jobboard');
echo '</a>';

echo '</div>';
echo '</div>';

// Document checklist.
if (!empty($requireddocs)) {
    echo '<div class="card jb-sidebar-card shadow-sm">';
    echo '<div class="card-header bg-warning text-dark">';
    echo '<i class="fa fa-clipboard-list mr-2"></i>' . get_string('documentchecklist', 'local_jobboard');
    echo '</div>';
    echo '<ul class="list-group list-group-flush">';
    foreach ($requireddocs as $doc) {
        $requiredmark = !empty($doc->isrequired) ? '<span class="text-danger">*</span>' : '';
        echo '<li class="list-group-item d-flex justify-content-between align-items-center py-2">';
        echo '<small>' . format_string($doc->name) . ' ' . $requiredmark . '</small>';
        echo '<i class="fa fa-circle text-muted" style="font-size:8px;"></i>';
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
}

echo '</div>'; // .col-lg-4
echo '</div>'; // .row

// ============================================================================
// NAVIGATION FOOTER - Back to vacancy
// ============================================================================
echo html_writer::start_div('row mt-4');
echo html_writer::start_div('col-12');
echo html_writer::start_div('jb-navigation-footer d-flex justify-content-between align-items-center py-3 border-top');

// Back button (left side).
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]),
    '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtovacancy', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary']
);

// Browse vacancies link (right side).
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
    get_string('browservacancies', 'local_jobboard') . ' <i class="fa fa-arrow-right ml-2"></i>',
    ['class' => 'btn btn-link text-muted']
);

echo html_writer::end_div(); // .jb-navigation-footer
echo html_writer::end_div(); // .col-12
echo html_writer::end_div(); // .row

echo '</div>'; // .jb-apply-container

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

// Initialize form unsaved warning.
$PAGE->requires->js_call_amd('local_jobboard/form_unsaved_warning', 'init', [[
    'formSelector' => 'form.mform',
    'excludedLinks' => ['.jb-navigation-footer a.btn-link'],
]]);

echo $OUTPUT->footer();
