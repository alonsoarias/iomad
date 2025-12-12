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
 * View application details view.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\vacancy;
use local_jobboard\exemption;

// Parameters.
$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Get application.
$application = application::get($id);
if (!$application) {
    throw new moodle_exception('applicationnotfound', 'local_jobboard');
}

// Check access - either own application or has review capability.
$isowner = ($application->userid == $USER->id);
$canreview = has_capability('local/jobboard:reviewdocuments', $context);
$canmanageworkflow = has_capability('local/jobboard:manageworkflow', $context);

if (!$isowner && !$canreview && !$canmanageworkflow) {
    throw new moodle_exception('noaccess', 'local_jobboard');
}

// Get vacancy.
$vacancy = vacancy::get($application->vacancyid);

// Set up page.
$PAGE->set_title(get_string('viewapplication', 'local_jobboard'));
$PAGE->set_heading(get_string('viewapplication', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Handle withdraw action.
if ($action === 'withdraw' && $isowner) {
    if (!in_array($application->status, ['submitted', 'under_review'])) {
        throw new moodle_exception('cannotwithdraw', 'local_jobboard');
    }

    if ($confirm) {
        require_sesskey();
        $application->withdraw();
        redirect(
            new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
            get_string('applicationwithdrawn', 'local_jobboard'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(
            get_string('confirmwithdraw', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php',
                ['view' => 'application', 'id' => $id, 'action' => 'withdraw', 'confirm' => 1]),
            new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $id])
        );
        echo $OUTPUT->footer();
        exit;
    }
}

// Handle status change for managers.
if ($action === 'changestatus' && $canmanageworkflow) {
    $newstatus = required_param('newstatus', PARAM_ALPHA);
    $notes = optional_param('notes', '', PARAM_TEXT);

    require_sesskey();

    if ($application->can_transition_to($newstatus)) {
        $application->transition_to($newstatus, $notes);
        redirect(
            new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $id]),
            get_string('statuschanged', 'local_jobboard'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        throw new moodle_exception('invalidtransition', 'local_jobboard');
    }
}

// Get documents.
$documents = document::get_for_application($application->id);

// Get applicant info.
$applicant = $DB->get_record('user', ['id' => $application->userid]);
if (!$applicant) {
    throw new moodle_exception('error:usernotfound', 'local_jobboard');
}

// Check for exemption.
$exemption = exemption::get_active_for_user($application->userid);

// Get status history.
$history = $application->get_status_history();

// Get available workflow actions for managers.
$workflowactions = [];
if ($canmanageworkflow) {
    $workflowactions = $application->get_available_transitions();
}

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_application_detail_page_data(
    $application,
    $vacancy,
    $applicant,
    $documents,
    $history,
    $isowner,
    $canreview,
    $canmanageworkflow,
    $workflowactions,
    $exemption
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_application_detail_page($data);
echo $OUTPUT->footer();
