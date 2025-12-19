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
 * Application submission view - Rebuilt for reliability.
 *
 * This view handles the complete application submission flow:
 * 1. Validates user eligibility
 * 2. Displays the application form
 * 3. Processes form submission
 * 4. Creates application and stores documents
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_jobboard\vacancy;
use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\forms\application_form;

global $DB, $USER, $PAGE, $OUTPUT;

// ============================================================================
// STEP 1: Get and validate vacancy ID
// ============================================================================
$vacancyid = required_param('vacancyid', PARAM_INT);

// Load vacancy.
$vacancy = vacancy::get($vacancyid);
if (!$vacancy) {
    throw new moodle_exception('vacancynotfound', 'local_jobboard');
}

// ============================================================================
// STEP 2: Validate user eligibility
// ============================================================================

// Check vacancy is open.
if (!$vacancy->is_open()) {
    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]),
        get_string('vacancyclosed', 'local_jobboard'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

// Check user hasn't already applied. IMPORTANT: Parameters are (vacancyid, userid).
if (application::user_has_applied($vacancyid, $USER->id)) {
    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        get_string('alreadyapplied', 'local_jobboard'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Check convocatoria application limits.
$convocatoriaid = $vacancy->convocatoriaid ?? 0;
if ($convocatoriaid > 0) {
    $eligibility = local_jobboard_can_user_apply_to_vacancy($USER->id, $vacancyid);
    if (!$eligibility['can_apply']) {
        redirect(
            new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
            $eligibility['reason'],
            null,
            \core\output\notification::NOTIFY_WARNING
        );
    }
}

// Check experience requirements (for occasional contracts).
$experiencecheck = local_jobboard_check_experience_requirements($USER->id, $vacancyid);
if (!$experiencecheck['meets_requirements']) {
    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]),
        $experiencecheck['reason'],
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Check user profile is complete.
$applicantprofile = $DB->get_record('local_jobboard_applicant_profile', ['userid' => $USER->id]);
if (!$applicantprofile || empty($applicantprofile->profile_complete)) {
    redirect(
        new moodle_url('/local/jobboard/updateprofile.php', ['vacancyid' => $vacancyid]),
        get_string('completeprofile_required', 'local_jobboard'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// ============================================================================
// STEP 3: Check for exemption status
// ============================================================================
$isexemption = false;
$exemptioninfo = null;

// An exemption is active if it's not revoked (timerevoked IS NULL) and within validity period.
$now = time();
$exemption = $DB->get_record_sql(
    "SELECT *
       FROM {local_jobboard_exemption}
      WHERE userid = :userid
        AND timerevoked IS NULL
        AND validfrom <= :now1
        AND (validuntil IS NULL OR validuntil > :now2)
      ORDER BY timecreated DESC
      LIMIT 1",
    ['userid' => $USER->id, 'now1' => $now, 'now2' => $now]
);

if ($exemption) {
    $isexemption = true;
    $exemptioninfo = $exemption;
}

// ============================================================================
// STEP 4: Set up page
// ============================================================================
$PAGE->set_pagelayout('standard');
$PAGE->activityheader->disable();
$PAGE->set_title(get_string('applytovacancy', 'local_jobboard') . ': ' . format_string($vacancy->title));
$PAGE->set_heading(get_string('applytovacancy', 'local_jobboard'));

// Add breadcrumbs.
$PAGE->navbar->add(get_string('vacancies', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']));
$PAGE->navbar->add(format_string($vacancy->title),
    new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]));
$PAGE->navbar->add(get_string('apply', 'local_jobboard'));

// ============================================================================
// STEP 5: Create form
// ============================================================================
$customdata = [
    'vacancy' => $vacancy,
    'isexemption' => $isexemption,
    'convocatoriaid' => $convocatoriaid,
];

// Form action URL must include vacancyid.
$formaction = new moodle_url('/local/jobboard/index.php', [
    'view' => 'apply',
    'vacancyid' => $vacancyid,
]);

$mform = new application_form($formaction->out(false), $customdata);

// ============================================================================
// STEP 6: Handle form cancellation
// ============================================================================
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]));
}

// ============================================================================
// STEP 7: Process form submission
// ============================================================================
if ($data = $mform->get_data()) {
    try {
        // Start transaction.
        $transaction = $DB->start_delegated_transaction();

        // Prepare application data.
        $applicationdata = [
            'vacancyid' => $vacancyid,
            'userid' => $USER->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => getremoteaddr(),
            'digitalsignature' => trim($data->digitalsignature),
            'isexemption' => $isexemption ? 1 : 0,
            'coverletter' => trim($data->coverletter ?? ''),
            'status' => 'submitted',
        ];

        // Create the application.
        $application = application::create($applicationdata);

        if (!$application || !$application->id) {
            throw new moodle_exception('applicationcreatefailed', 'local_jobboard');
        }

        // Store uploaded documents and text content.
        $submitteddocs = $mform->get_submitted_documents();
        $textdocuments = []; // Store text/editor documents in applicationdata.

        foreach ($submitteddocs as $doccode => $docdata) {
            if ($docdata['type'] === 'file' && !empty($docdata['draftitemid'])) {
                // File documents go to the document storage.
                try {
                    document::store_from_draft(
                        $application->id,
                        $doccode,
                        $docdata['draftitemid']
                    );
                } catch (Exception $e) {
                    debugging('Failed to store document ' . $doccode . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
                    // Continue with other documents.
                }
            } else if (in_array($docdata['type'], ['text', 'textarea', 'editor']) && !empty($docdata['value'])) {
                // Text/editor documents: store content in applicationdata AND create document record for validation.
                $textdocuments[$doccode] = [
                    'type' => $docdata['type'],
                    'value' => $docdata['value'],
                    'doctypeid' => $docdata['doctypeid'] ?? 0,
                ];

                // Create document record for text document so it can be validated.
                try {
                    document::create_text_document(
                        $application->id,
                        $doccode,
                        $docdata['value']
                    );
                } catch (Exception $e) {
                    debugging('Failed to create text document record ' . $doccode . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            }
        }

        // Update applicationdata with text documents if any (for content retrieval).
        if (!empty($textdocuments)) {
            $DB->set_field('local_jobboard_application', 'applicationdata',
                json_encode(['text_documents' => $textdocuments]),
                ['id' => $application->id]
            );
        }

        // Commit transaction.
        $transaction->allow_commit();

        // Log success.
        \local_jobboard\audit::log(
            \local_jobboard\audit::ACTION_SUBMIT,
            \local_jobboard\audit::ENTITY_APPLICATION,
            $application->id,
            ['vacancyid' => $vacancyid, 'userid' => $USER->id]
        );

        // Redirect to success page.
        redirect(
            new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]),
            get_string('applicationsubmitted', 'local_jobboard'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );

    } catch (Exception $e) {
        // Rollback on error.
        if (isset($transaction)) {
            try {
                $transaction->rollback($e);
            } catch (Exception $re) {
                // Already rolled back.
            }
        }

        debugging('Application submission failed: ' . $e->getMessage(), DEBUG_DEVELOPER);

        \core\notification::error(get_string('applicationsubmitfailed', 'local_jobboard'));
    }
}

// ============================================================================
// STEP 8: Prepare template data and render
// ============================================================================

// Get renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Calculate days until close.
$closedate = $vacancy->get_close_date();
$daysuntilclose = $closedate ? ceil(($closedate - time()) / DAYSECS) : 30;

// Get document types for checklist.
$requireddocs = $mform->get_filtered_documents();

// Capture form HTML.
ob_start();
$mform->display();
$formhtml = ob_get_clean();

// Prepare template data.
$templatedata = $renderer->prepare_apply_page_data(
    $vacancy,
    $requireddocs,
    $isexemption,
    $exemptioninfo,
    $formhtml,
    $daysuntilclose
);

// Initialize document checklist JavaScript for auto-marking.
$PAGE->requires->js_call_amd('local_jobboard/document_checklist', 'init', [[
    'totalDocs' => count($requireddocs),
]]);

// Output page.
echo $OUTPUT->header();
echo $renderer->render_apply_page($templatedata);
echo $OUTPUT->footer();
