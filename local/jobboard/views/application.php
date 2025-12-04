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
 * This file is included by index.php and should not be accessed directly.
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

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/index.php'));
if ($isowner) {
    $PAGE->navbar->add(get_string('myapplications', 'local_jobboard'), new moodle_url('/local/jobboard/index.php', ['view' => 'applications']));
} else {
    $PAGE->navbar->add(get_string('managevacancies', 'local_jobboard'), new moodle_url('/local/jobboard/index.php', ['view' => 'manage']));
}
$PAGE->navbar->add(get_string('viewapplication', 'local_jobboard'));

// Handle actions.
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
            new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $id, 'action' => 'withdraw', 'confirm' => 1]),
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

// Check for exemption.
$exemption = exemption::get_active_for_user($application->userid);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('application', 'local_jobboard') . ' #' . $application->id);

// Application status box.
$statusclass = 'alert-info';
switch ($application->status) {
    case 'docs_validated':
    case 'selected':
        $statusclass = 'alert-success';
        break;
    case 'docs_rejected':
    case 'rejected':
        $statusclass = 'alert-danger';
        break;
    case 'withdrawn':
        $statusclass = 'alert-secondary';
        break;
    case 'interview':
        $statusclass = 'alert-warning';
        break;
}

echo '<div class="alert ' . $statusclass . '">';
echo '<strong>' . get_string('currentstatus', 'local_jobboard') . ':</strong> ';
echo get_string('status_' . $application->status, 'local_jobboard');
if (!empty($application->statusnotes)) {
    echo '<br><em>' . format_string($application->statusnotes) . '</em>';
}
echo '</div>';

// Applicant information (for reviewers).
if ($canreview || $canmanageworkflow) {
    echo '<div class="card mb-3">';
    echo '<div class="card-header"><h5>' . get_string('applicantinfo', 'local_jobboard') . '</h5></div>';
    echo '<div class="card-body">';
    echo '<p><strong>' . get_string('name') . ':</strong> ' . fullname($applicant) . '</p>';
    echo '<p><strong>' . get_string('email') . ':</strong> ' . $applicant->email . '</p>';
    if (!empty($applicant->phone1)) {
        echo '<p><strong>' . get_string('phone') . ':</strong> ' . format_string($applicant->phone1) . '</p>';
    }
    if (!empty($applicant->idnumber)) {
        echo '<p><strong>' . get_string('idnumber') . ':</strong> ' . format_string($applicant->idnumber) . '</p>';
    }

    // Exemption info.
    if ($exemption) {
        echo '<div class="alert alert-info mt-3">';
        echo '<strong>' . get_string('exemptionapplied', 'local_jobboard') . '</strong><br>';
        echo get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard');
        if (!empty($exemption->documentref)) {
            echo ' (' . format_string($exemption->documentref) . ')';
        }
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}

// Vacancy information.
echo '<div class="card mb-3">';
echo '<div class="card-header"><h5>' . get_string('vacancyinfo', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';
if ($vacancy) {
    echo '<p><strong>' . get_string('code', 'local_jobboard') . ':</strong> ' . format_string($vacancy->code) . '</p>';
    echo '<p><strong>' . get_string('title', 'local_jobboard') . ':</strong> ' . format_string($vacancy->title) . '</p>';
    if (!empty($vacancy->location)) {
        echo '<p><strong>' . get_string('location', 'local_jobboard') . ':</strong> ' . format_string($vacancy->location) . '</p>';
    }
    $vacancyurl = new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]);
    echo '<p><a href="' . $vacancyurl . '">' . get_string('viewvacancy', 'local_jobboard') . '</a></p>';
} else {
    echo '<p>' . get_string('vacancynotfound', 'local_jobboard') . '</p>';
}
echo '</div>';
echo '</div>';

// Application details.
echo '<div class="card mb-3">';
echo '<div class="card-header"><h5>' . get_string('applicationdetails', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';
echo '<p><strong>' . get_string('dateapplied', 'local_jobboard') . ':</strong> ' .
    userdate($application->timecreated, get_string('strftimedatetime', 'langconfig')) . '</p>';
if (!empty($application->coverletter)) {
    echo '<p><strong>' . get_string('coverletter', 'local_jobboard') . ':</strong></p>';
    echo '<div class="border p-3 mb-3">' . nl2br(format_string($application->coverletter)) . '</div>';
}
echo '<p><strong>' . get_string('digitalsignature', 'local_jobboard') . ':</strong> ' .
    format_string($application->digitalsignature) . '</p>';
echo '<p><strong>' . get_string('consentgiven', 'local_jobboard') . ':</strong> ' .
    userdate($application->consenttimestamp, get_string('strftimedatetime', 'langconfig')) . '</p>';
echo '</div>';
echo '</div>';

// Documents.
echo '<div class="card mb-3">';
echo '<div class="card-header"><h5>' . get_string('uploadeddocuments', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

if (empty($documents)) {
    echo '<p>' . get_string('nodocuments', 'local_jobboard') . '</p>';
} else {
    echo '<table class="table table-striped">';
    echo '<thead><tr>';
    echo '<th>' . get_string('documenttype', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('filename', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('uploaded', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('status', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('actions') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($documents as $doc) {
        echo '<tr>';
        echo '<td>' . get_string('doctype_' . $doc->documenttype, 'local_jobboard') . '</td>';
        echo '<td>' . format_string($doc->filename) . '</td>';
        echo '<td>' . userdate($doc->timecreated, get_string('strftimedatetime', 'langconfig')) . '</td>';

        // Validation status.
        $validation = $doc->get_validation();
        if ($validation) {
            if ($validation->isvalid) {
                echo '<td><span class="badge badge-success">' . get_string('validated', 'local_jobboard') . '</span></td>';
            } else {
                echo '<td><span class="badge badge-danger">' . get_string('rejected', 'local_jobboard') . '</span>';
                if (!empty($validation->rejectreason)) {
                    echo '<br><small>' . format_string($validation->rejectreason) . '</small>';
                }
                echo '</td>';
            }
        } else {
            echo '<td><span class="badge badge-warning">' . get_string('pendingvalidation', 'local_jobboard') . '</span></td>';
        }

        // Actions.
        echo '<td>';
        $downloadurl = $doc->get_download_url();
        if ($downloadurl) {
            echo '<a href="' . $downloadurl . '" class="btn btn-sm btn-outline-primary">' .
                get_string('download') . '</a> ';
        }

        // Validation buttons for reviewers.
        if ($canreview && !$validation) {
            echo '<a href="' . new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'documentid' => $doc->id]) .
                '" class="btn btn-sm btn-outline-success">' . get_string('validate', 'local_jobboard') . '</a>';
        }
        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody></table>';
}
echo '</div>';
echo '</div>';

// Status history.
$history = $application->get_status_history();
if (!empty($history)) {
    echo '<div class="card mb-3">';
    echo '<div class="card-header"><h5>' . get_string('statushistory', 'local_jobboard') . '</h5></div>';
    echo '<div class="card-body">';
    echo '<ul class="timeline">';
    foreach ($history as $entry) {
        echo '<li>';
        echo '<strong>' . get_string('status_' . $entry->newstatus, 'local_jobboard') . '</strong>';
        echo ' - ' . userdate($entry->timecreated, get_string('strftimedatetime', 'langconfig'));
        if (!empty($entry->notes)) {
            echo '<br><em>' . format_string($entry->notes) . '</em>';
        }
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
}

// Workflow actions for managers.
if ($canmanageworkflow) {
    $availabletransitions = $application->get_available_transitions();
    if (!empty($availabletransitions)) {
        echo '<div class="card mb-3">';
        echo '<div class="card-header"><h5>' . get_string('workflowactions', 'local_jobboard') . '</h5></div>';
        echo '<div class="card-body">';
        echo '<form method="post" action="' . new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $id, 'action' => 'changestatus']) . '">';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

        echo '<div class="form-group">';
        echo '<label for="newstatus">' . get_string('changestatus', 'local_jobboard') . '</label>';
        echo '<select name="newstatus" id="newstatus" class="form-control">';
        foreach ($availabletransitions as $status) {
            echo '<option value="' . $status . '">' . get_string('status_' . $status, 'local_jobboard') . '</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="notes">' . get_string('notes', 'local_jobboard') . '</label>';
        echo '<textarea name="notes" id="notes" class="form-control" rows="3"></textarea>';
        echo '</div>';

        echo '<button type="submit" class="btn btn-primary">' . get_string('updatestatus', 'local_jobboard') . '</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }
}

// Action buttons.
echo '<div class="action-buttons mt-4">';

if ($isowner) {
    echo '<a href="' . new moodle_url('/local/jobboard/index.php', ['view' => 'applications']) . '" class="btn btn-secondary">' .
        get_string('backtoapplications', 'local_jobboard') . '</a> ';

    if (in_array($application->status, ['submitted', 'under_review'])) {
        echo '<a href="' . new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $id, 'action' => 'withdraw']) .
            '" class="btn btn-danger">' . get_string('withdrawapplication', 'local_jobboard') . '</a>';
    }
} else {
    echo '<a href="' . new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $application->vacancyid]) .
        '" class="btn btn-secondary">' . get_string('backtoapplications', 'local_jobboard') . '</a>';
}

echo '</div>';

echo $OUTPUT->footer();
