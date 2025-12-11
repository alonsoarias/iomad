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
 * Modern redesign with card-based layout and workflow management.
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
use local_jobboard\output\ui_helper;

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

// Check for exemption.
$exemption = exemption::get_active_for_user($application->userid);

// Get status history.
$history = $application->get_status_history();

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-application-detail');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
];
if ($isowner) {
    $breadcrumbs[get_string('myapplications', 'local_jobboard')] =
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']);
} else {
    $breadcrumbs[get_string('reviewapplications', 'local_jobboard')] =
        new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $application->vacancyid]);
}
$breadcrumbs[get_string('application', 'local_jobboard') . ' #' . $application->id] = null;

$headerActions = [];
if ($isowner && in_array($application->status, ['submitted', 'under_review'])) {
    $headerActions[] = [
        'url' => new moodle_url('/local/jobboard/index.php',
            ['view' => 'application', 'id' => $id, 'action' => 'withdraw']),
        'label' => get_string('withdrawapplication', 'local_jobboard'),
        'icon' => 'times',
        'class' => 'btn btn-outline-danger',
    ];
}

echo ui_helper::page_header(
    get_string('application', 'local_jobboard') . ' #' . $application->id,
    $breadcrumbs,
    $headerActions
);

// ============================================================================
// STATUS BANNER
// ============================================================================
$statusClass = 'alert-info';
$statusIcon = 'info-circle';
switch ($application->status) {
    case 'docs_validated':
    case 'selected':
        $statusClass = 'alert-success';
        $statusIcon = 'check-circle';
        break;
    case 'docs_rejected':
    case 'rejected':
        $statusClass = 'alert-danger';
        $statusIcon = 'times-circle';
        break;
    case 'withdrawn':
        $statusClass = 'alert-secondary';
        $statusIcon = 'ban';
        break;
    case 'interview':
        $statusClass = 'alert-warning';
        $statusIcon = 'calendar-check';
        break;
}

echo html_writer::start_div('alert ' . $statusClass . ' d-flex justify-content-between align-items-center mb-4');
echo html_writer::start_div('d-flex align-items-center');
echo html_writer::tag('i', '', ['class' => 'fa fa-' . $statusIcon . ' fa-2x mr-3']);
echo html_writer::start_div();
echo html_writer::tag('strong', get_string('currentstatus', 'local_jobboard') . ': ', ['class' => 'd-block']);
echo html_writer::tag('span', get_string('status_' . $application->status, 'local_jobboard'),
    ['class' => 'h5 mb-0']);
echo html_writer::end_div();
echo html_writer::end_div();
if (!empty($application->statusnotes)) {
    echo html_writer::tag('em', format_string($application->statusnotes), ['class' => 'text-muted']);
}
echo html_writer::end_div();

// ============================================================================
// MAIN CONTENT
// ============================================================================
echo html_writer::start_div('row');

// Left column.
echo html_writer::start_div('col-lg-8');

// Applicant information (for reviewers).
if ($canreview || $canmanageworkflow) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-user mr-2"></i>' . get_string('applicantinfo', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::start_div('row');
    echo html_writer::start_div('col-md-6');
    echo html_writer::tag('p', '<strong>' . get_string('name') . ':</strong> ' . fullname($applicant));
    echo html_writer::tag('p', '<strong>' . get_string('email') . ':</strong> ' . $applicant->email);
    if (!empty($applicant->phone1)) {
        echo html_writer::tag('p', '<strong>' . get_string('phone') . ':</strong> ' . format_string($applicant->phone1));
    }
    echo html_writer::end_div();
    echo html_writer::start_div('col-md-6');
    if (!empty($applicant->idnumber)) {
        echo html_writer::tag('p', '<strong>' . get_string('idnumber') . ':</strong> ' . format_string($applicant->idnumber));
    }
    echo html_writer::tag('p', '<strong>' . get_string('dateapplied', 'local_jobboard') . ':</strong> ' .
        userdate($application->timecreated, get_string('strftimedatetime', 'langconfig')));
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Exemption info.
    if ($exemption) {
        echo html_writer::start_div('alert alert-success mt-3 mb-0');
        echo html_writer::tag('strong',
            '<i class="fa fa-certificate mr-2"></i>' . get_string('exemptionapplied', 'local_jobboard'));
        echo html_writer::tag('br', '');
        echo get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard');
        if (!empty($exemption->documentref)) {
            echo ' (' . format_string($exemption->documentref) . ')';
        }
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Vacancy information.
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white');
echo html_writer::tag('h5',
    '<i class="fa fa-briefcase text-primary mr-2"></i>' . get_string('vacancyinfo', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body');
if ($vacancy) {
    echo html_writer::start_div('d-flex justify-content-between align-items-start');
    echo html_writer::start_div();
    echo html_writer::tag('h5', format_string($vacancy->title), ['class' => 'mb-1']);
    echo html_writer::tag('code', format_string($vacancy->code), ['class' => 'small']);
    if (!empty($vacancy->location)) {
        echo html_writer::tag('p', '<i class="fa fa-map-marker-alt mr-1"></i>' . format_string($vacancy->location),
            ['class' => 'text-muted mt-2 mb-0']);
    }
    echo html_writer::end_div();
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]),
        get_string('viewvacancy', 'local_jobboard'),
        ['class' => 'btn btn-sm btn-outline-primary']
    );
    echo html_writer::end_div();
} else {
    echo html_writer::tag('p', get_string('vacancynotfound', 'local_jobboard'), ['class' => 'text-muted']);
}
echo html_writer::end_div();
echo html_writer::end_div();

// Cover letter.
if (!empty($application->coverletter)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-envelope-open-text text-primary mr-2"></i>' . get_string('coverletter', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo html_writer::div(nl2br(format_string($application->coverletter)), 'border-left pl-3 py-2');
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Documents.
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
echo html_writer::tag('h5',
    '<i class="fa fa-file-alt text-primary mr-2"></i>' . get_string('uploadeddocuments', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::tag('span', count($documents), ['class' => 'badge badge-primary']);
echo html_writer::end_div();
echo html_writer::start_div('card-body p-0');

if (empty($documents)) {
    echo html_writer::div(
        '<i class="fa fa-info-circle mr-2"></i>' . get_string('nodocuments', 'local_jobboard'),
        'p-4 text-muted text-center'
    );
} else {
    echo html_writer::start_tag('table', ['class' => 'table table-hover mb-0']);
    echo html_writer::start_tag('thead', ['class' => 'thead-light']);
    echo html_writer::start_tag('tr');
    echo html_writer::tag('th', get_string('documenttype', 'local_jobboard'));
    echo html_writer::tag('th', get_string('filename', 'local_jobboard'));
    echo html_writer::tag('th', get_string('status', 'local_jobboard'));
    echo html_writer::tag('th', get_string('actions'), ['class' => 'text-right']);
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('thead');
    echo html_writer::start_tag('tbody');

    foreach ($documents as $doc) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', get_string('doctype_' . $doc->documenttype, 'local_jobboard'));
        echo html_writer::tag('td', format_string($doc->filename));

        // Validation status.
        $validation = $doc->get_validation();
        $statusHtml = '';
        if ($validation) {
            if ($validation->isvalid) {
                $statusHtml = '<span class="badge badge-success"><i class="fa fa-check mr-1"></i>' .
                    get_string('validated', 'local_jobboard') . '</span>';
            } else {
                $statusHtml = '<span class="badge badge-danger"><i class="fa fa-times mr-1"></i>' .
                    get_string('rejected', 'local_jobboard') . '</span>';
                if (!empty($validation->rejectreason)) {
                    $statusHtml .= '<br><small class="text-muted">' . format_string($validation->rejectreason) . '</small>';
                }
            }
        } else {
            $statusHtml = '<span class="badge badge-warning"><i class="fa fa-clock mr-1"></i>' .
                get_string('pendingvalidation', 'local_jobboard') . '</span>';
        }
        echo html_writer::tag('td', $statusHtml);

        // Actions.
        $actionsHtml = '';
        $downloadurl = $doc->get_download_url();
        if ($downloadurl) {
            $actionsHtml .= html_writer::link($downloadurl,
                '<i class="fa fa-download"></i>',
                ['class' => 'btn btn-sm btn-outline-primary mr-1', 'title' => get_string('download')]);
        }
        if ($canreview && !$validation) {
            $actionsHtml .= html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'documentid' => $doc->id]),
                '<i class="fa fa-check-double"></i>',
                ['class' => 'btn btn-sm btn-outline-success', 'title' => get_string('validate', 'local_jobboard')]);
        }
        echo html_writer::tag('td', $actionsHtml, ['class' => 'text-right']);

        echo html_writer::end_tag('tr');
    }

    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // col-lg-8

// Right column - Sidebar.
echo html_writer::start_div('col-lg-4');

// Application details card.
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white');
echo html_writer::tag('h5',
    '<i class="fa fa-info-circle text-primary mr-2"></i>' . get_string('applicationdetails', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body p-0');
echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);

echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between']);
echo html_writer::tag('span', get_string('dateapplied', 'local_jobboard'));
echo html_writer::tag('strong', userdate($application->timecreated, get_string('strftimedate', 'langconfig')));
echo html_writer::end_tag('li');

echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between']);
echo html_writer::tag('span', get_string('digitalsignature', 'local_jobboard'));
echo html_writer::tag('code', format_string($application->digitalsignature));
echo html_writer::end_tag('li');

echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between']);
echo html_writer::tag('span', get_string('consentgiven', 'local_jobboard'));
echo html_writer::tag('strong', userdate($application->consenttimestamp, get_string('strftimedate', 'langconfig')));
echo html_writer::end_tag('li');

echo html_writer::end_tag('ul');
echo html_writer::end_div();
echo html_writer::end_div();

// Status history card.
if (!empty($history)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-history text-primary mr-2"></i>' . get_string('statushistory', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::start_div('timeline');
    foreach ($history as $entry) {
        echo html_writer::start_div('timeline-item mb-3 pb-3 border-bottom');
        echo html_writer::start_div('d-flex justify-content-between');
        echo ui_helper::status_badge($entry->newstatus, 'application');
        echo html_writer::tag('small',
            userdate($entry->timecreated, get_string('strftimedatetime', 'langconfig')),
            ['class' => 'text-muted']
        );
        echo html_writer::end_div();
        if (!empty($entry->notes)) {
            echo html_writer::tag('p', format_string($entry->notes), ['class' => 'small text-muted mt-2 mb-0']);
        }
        echo html_writer::end_div();
    }
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Workflow actions (for managers).
if ($canmanageworkflow) {
    $availabletransitions = $application->get_available_transitions();
    if (!empty($availabletransitions)) {
        echo html_writer::start_div('card shadow-sm mb-4 border-warning');
        echo html_writer::start_div('card-header bg-warning text-dark');
        echo html_writer::tag('h5',
            '<i class="fa fa-tasks mr-2"></i>' . get_string('workflowactions', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        $actionurl = new moodle_url('/local/jobboard/index.php',
            ['view' => 'application', 'id' => $id, 'action' => 'changestatus']);
        echo html_writer::start_tag('form', ['method' => 'post', 'action' => $actionurl]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

        echo html_writer::start_div('form-group');
        echo html_writer::tag('label', get_string('changestatus', 'local_jobboard'), ['for' => 'newstatus']);
        echo html_writer::start_tag('select', ['name' => 'newstatus', 'id' => 'newstatus', 'class' => 'form-control']);
        foreach ($availabletransitions as $status) {
            echo html_writer::tag('option', get_string('status_' . $status, 'local_jobboard'), ['value' => $status]);
        }
        echo html_writer::end_tag('select');
        echo html_writer::end_div();

        echo html_writer::start_div('form-group');
        echo html_writer::tag('label', get_string('notes', 'local_jobboard'), ['for' => 'notes']);
        echo html_writer::tag('textarea', '', [
            'name' => 'notes', 'id' => 'notes', 'class' => 'form-control', 'rows' => 3,
            'placeholder' => get_string('optionalnotes', 'local_jobboard'),
        ]);
        echo html_writer::end_div();

        echo html_writer::tag('button', get_string('updatestatus', 'local_jobboard'),
            ['type' => 'submit', 'class' => 'btn btn-warning btn-block']);

        echo html_writer::end_tag('form');

        echo html_writer::end_div();
        echo html_writer::end_div();
    }
}

// Back button.
echo html_writer::start_div('card shadow-sm');
echo html_writer::start_div('card-body');
if ($isowner) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtoapplications', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary btn-block']
    );
} else {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $application->vacancyid]),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtoapplications', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary btn-block']
    );
}
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // col-lg-4
echo html_writer::end_div(); // row

echo html_writer::end_div(); // local-jobboard-application-detail

echo $OUTPUT->footer();
