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
 * Assign reviewers to applications.
 *
 * Modern redesign with consistent UX pattern using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\reviewer;
use local_jobboard\application;
use local_jobboard\output\ui_helper;

$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/assign_reviewer.php', ['vacancyid' => $vacancyid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('assignreviewer', 'local_jobboard'));
$PAGE->set_heading(get_string('assignreviewer', 'local_jobboard'));
$PAGE->set_pagelayout('admin');
$PAGE->requires->css('/local/jobboard/styles.css');

// Handle actions.
if ($action === 'assign') {
    require_sesskey();

    $applicationids = required_param_array('applications', PARAM_INT);
    $reviewerid = required_param('reviewerid', PARAM_INT);

    $results = reviewer::bulk_assign($applicationids, $reviewerid);

    $message = get_string('reviewerassigned', 'local_jobboard', $results);
    redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'autoassign') {
    require_sesskey();

    $maxperreviewer = optional_param('maxperreviewer', 20, PARAM_INT);
    $assigned = reviewer::auto_assign($vacancyid ?: null, $maxperreviewer);

    $message = get_string('autoassigncomplete', 'local_jobboard', $assigned);
    redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'unassign') {
    require_sesskey();

    $applicationid = required_param('applicationid', PARAM_INT);
    reviewer::unassign($applicationid);

    redirect($PAGE->url, get_string('reviewerunassigned', 'local_jobboard'), null,
        \core\output\notification::NOTIFY_SUCCESS);
}

// Get vacancy list for filter.
$vacancies = $DB->get_records_select('local_jobboard_vacancy',
    "status IN ('published', 'closed')", null, 'code ASC', 'id, code, title');

// Get reviewers with workload.
$reviewers = reviewer::get_all_with_workload();

// Get unassigned applications.
$filters = ['reviewerid_null' => true];
if ($vacancyid) {
    $filters['vacancyid'] = $vacancyid;
}
$unassignedresult = application::get_list($filters, 'timecreated', 'ASC', 0, 100);
$unassigned = $unassignedresult['applications'];

// Calculate stats.
$totalUnassigned = count($unassigned);
$totalReviewers = count($reviewers);
$avgWorkload = 0;
$totalAssigned = 0;
foreach ($reviewers as $rev) {
    $totalAssigned += $rev->workload;
}
if ($totalReviewers > 0) {
    $avgWorkload = round($totalAssigned / $totalReviewers, 1);
}

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-assign-reviewer');

// ============================================================================
// PAGE HEADER WITH ACTIONS
// ============================================================================
echo ui_helper::page_header(
    get_string('assignreviewer', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
        [
            'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
            'label' => get_string('reviewapplications', 'local_jobboard'),
            'icon' => 'clipboard-check',
            'class' => 'btn btn-outline-primary',
        ],
    ]
);

// ============================================================================
// STATS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string)$totalUnassigned, get_string('unassignedapplications', 'local_jobboard'), 'warning', 'exclamation-triangle');
echo ui_helper::stat_card((string)$totalReviewers, get_string('availablereviewers', 'local_jobboard'), 'primary', 'users');
echo ui_helper::stat_card((string)$totalAssigned, get_string('totalassigned', 'local_jobboard'), 'success', 'check-circle');
echo ui_helper::stat_card((string)$avgWorkload, get_string('avgworkload', 'local_jobboard'), 'info', 'chart-bar');
echo html_writer::end_div();

// ============================================================================
// VACANCY FILTER
// ============================================================================
$vacancyOptions = [0 => get_string('allvacancies', 'local_jobboard')];
foreach ($vacancies as $v) {
    $vacancyOptions[$v->id] = format_string($v->code . ' - ' . $v->title);
}

$filterDefinitions = [
    [
        'type' => 'select',
        'name' => 'vacancyid',
        'label' => get_string('vacancy', 'local_jobboard'),
        'options' => $vacancyOptions,
        'col' => 'col-md-6',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/assign_reviewer.php'))->out(false),
    $filterDefinitions,
    ['vacancyid' => $vacancyid],
    []
);

// ============================================================================
// REVIEWER WORKLOAD SUMMARY
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
echo html_writer::tag('h5', '<i class="fa fa-users text-primary mr-2"></i>' .
    get_string('reviewerworkload', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::tag('span', $totalReviewers . ' ' . get_string('reviewers', 'local_jobboard'),
    ['class' => 'badge badge-secondary']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

if (empty($reviewers)) {
    echo ui_helper::empty_state(get_string('noreviewers', 'local_jobboard'), 'users');
} else {
    echo html_writer::start_div('row');
    foreach ($reviewers as $rev) {
        $workloadclass = 'success';
        if ($rev->workload > 15) {
            $workloadclass = 'danger';
        } else if ($rev->workload > 10) {
            $workloadclass = 'warning';
        }

        echo html_writer::start_div('col-lg-4 col-md-6 mb-3');
        echo html_writer::start_div('card h-100 border-left border-' . $workloadclass);
        echo html_writer::start_div('card-body');

        echo html_writer::tag('h6', '<i class="fa fa-user text-muted mr-2"></i>' . fullname($rev), ['class' => 'mb-2']);

        echo html_writer::tag('span', $rev->workload . ' ' . get_string('activeassignments', 'local_jobboard'),
            ['class' => 'badge badge-' . $workloadclass . ' mb-2']);

        if (isset($rev->stats)) {
            echo html_writer::start_div('small text-muted mt-2');
            echo '<i class="fa fa-check mr-1"></i>' . get_string('reviewed', 'local_jobboard') . ': ' .
                html_writer::tag('strong', $rev->stats['reviewed']);
            echo ' <span class="mx-2">|</span> ';
            echo '<i class="fa fa-clock mr-1"></i>' . get_string('avgtime', 'local_jobboard') . ': ' .
                html_writer::tag('strong', $rev->stats['avg_review_time'] . 'h');
            echo html_writer::end_div();
        }

        echo html_writer::end_div(); // card-body
        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }
    echo html_writer::end_div(); // row
}

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// ============================================================================
// AUTO-ASSIGN SECTION
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4 border-info');
echo html_writer::start_div('card-header bg-info text-white');
echo html_writer::tag('h5', '<i class="fa fa-magic mr-2"></i>' .
    get_string('autoassign', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::start_tag('form', [
    'method' => 'post',
    'action' => $PAGE->url,
    'class' => 'd-flex flex-wrap align-items-center',
]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'autoassign']);

echo html_writer::start_div('form-group mr-3 mb-2');
echo html_writer::tag('label', get_string('maxperreviewer', 'local_jobboard'), ['for' => 'maxperreviewer', 'class' => 'mr-2']);
echo html_writer::empty_tag('input', [
    'type' => 'number',
    'name' => 'maxperreviewer',
    'id' => 'maxperreviewer',
    'value' => '20',
    'min' => '1',
    'max' => '100',
    'class' => 'form-control',
    'style' => 'width: 100px;',
]);
echo html_writer::end_div();

echo html_writer::tag('button',
    '<i class="fa fa-magic mr-2"></i>' . get_string('autoassignall', 'local_jobboard'),
    ['type' => 'submit', 'class' => 'btn btn-light mb-2']
);

echo html_writer::end_tag('form');

echo html_writer::tag('small',
    '<i class="fa fa-info-circle mr-1"></i>' . get_string('autoassignhelp', 'local_jobboard'),
    ['class' => 'text-white-50 mt-2 d-block']
);

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// ============================================================================
// MANUAL ASSIGNMENT SECTION
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
echo html_writer::tag('h5', '<i class="fa fa-hand-pointer text-primary mr-2"></i>' .
    get_string('manualassign', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::tag('span', $totalUnassigned . ' ' . get_string('pendingassignment', 'local_jobboard'),
    ['class' => 'badge badge-warning']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

if (empty($unassigned)) {
    echo ui_helper::empty_state(get_string('nounassignedapplications', 'local_jobboard'), 'check-circle');
} else {
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'assign']);

    // Reviewer selection.
    echo html_writer::start_div('row mb-4');
    echo html_writer::start_div('col-md-6');
    echo html_writer::start_div('form-group');
    echo html_writer::tag('label',
        '<i class="fa fa-user-plus mr-2"></i>' . get_string('assignto', 'local_jobboard'),
        ['for' => 'reviewerid', 'class' => 'font-weight-bold']);
    echo html_writer::start_tag('select', [
        'name' => 'reviewerid',
        'id' => 'reviewerid',
        'class' => 'form-control',
        'required' => 'required',
    ]);
    echo html_writer::tag('option', get_string('selectreviewer', 'local_jobboard'), ['value' => '']);
    foreach ($reviewers as $rev) {
        $workloadIndicator = '';
        if ($rev->workload > 15) {
            $workloadIndicator = ' [HIGH]';
        } else if ($rev->workload > 10) {
            $workloadIndicator = ' [MEDIUM]';
        }
        echo html_writer::tag('option',
            fullname($rev) . ' (' . $rev->workload . ' ' . get_string('assigned', 'local_jobboard') . ')' . $workloadIndicator,
            ['value' => $rev->id]
        );
    }
    echo html_writer::end_tag('select');
    echo html_writer::end_div(); // form-group
    echo html_writer::end_div(); // col
    echo html_writer::end_div(); // row

    // Select all checkbox.
    echo html_writer::start_div('custom-control custom-checkbox mb-3');
    echo html_writer::empty_tag('input', [
        'type' => 'checkbox',
        'class' => 'custom-control-input',
        'id' => 'selectallapp',
    ]);
    echo html_writer::tag('label',
        get_string('selectall'),
        ['class' => 'custom-control-label font-weight-bold', 'for' => 'selectallapp']
    );
    echo html_writer::end_div();

    // Applications table.
    $headers = [
        '',
        get_string('applicant', 'local_jobboard'),
        get_string('vacancy', 'local_jobboard'),
        get_string('status', 'local_jobboard'),
        get_string('dateapplied', 'local_jobboard'),
    ];

    $rows = [];
    foreach ($unassigned as $app) {
        $checkbox = html_writer::empty_tag('input', [
            'type' => 'checkbox',
            'name' => 'applications[]',
            'value' => $app->id,
            'class' => 'app-checkbox',
        ]);

        $applicantName = format_string($app->userfirstname . ' ' . $app->userlastname);

        $statusClass = 'secondary';
        if ($app->status === 'submitted') {
            $statusClass = 'info';
        } else if ($app->status === 'under_review') {
            $statusClass = 'warning';
        }
        $statusBadge = html_writer::tag('span',
            get_string('status_' . $app->status, 'local_jobboard'),
            ['class' => 'badge badge-' . $statusClass]
        );

        $rows[] = [
            $checkbox,
            html_writer::tag('strong', $applicantName),
            format_string($app->vacancy_code ?? ''),
            $statusBadge,
            userdate($app->timecreated, '%Y-%m-%d %H:%M'),
        ];
    }

    echo ui_helper::data_table($headers, $rows);

    echo html_writer::start_div('mt-3');
    echo html_writer::tag('button',
        '<i class="fa fa-user-plus mr-2"></i>' . get_string('assignselected', 'local_jobboard'),
        ['type' => 'submit', 'class' => 'btn btn-primary']
    );
    echo html_writer::end_div();

    echo html_writer::end_tag('form');

    // JavaScript for select all.
    echo html_writer::script('
        document.getElementById("selectallapp").addEventListener("change", function() {
            var checkboxes = document.querySelectorAll(".app-checkbox");
            var self = this;
            checkboxes.forEach(function(cb) {
                cb.checked = self.checked;
            });
        });
    ');
}

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// ============================================================================
// NAVIGATION FOOTER
// ============================================================================
echo html_writer::start_div('card mt-4 bg-light');
echo html_writer::start_div('card-body d-flex flex-wrap align-items-center justify-content-center');

echo html_writer::link(
    new moodle_url('/local/jobboard/index.php'),
    '<i class="fa fa-tachometer-alt mr-2"></i>' . get_string('dashboard', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
    '<i class="fa fa-clipboard-check mr-2"></i>' . get_string('reviewapplications', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/bulk_validate.php'),
    '<i class="fa fa-check-double mr-2"></i>' . get_string('bulkvalidation', 'local_jobboard'),
    ['class' => 'btn btn-outline-success m-1']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-assign-reviewer

echo $OUTPUT->footer();
