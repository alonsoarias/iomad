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
 * My reviews view for local_jobboard.
 *
 * Modern redesign with card-based layout and reviewer statistics.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\reviewer;
use local_jobboard\output\ui_helper;

// Require review capability.
require_capability('local/jobboard:reviewdocuments', $context);

// Filter parameters.
$status = optional_param('status', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancy', 0, PARAM_INT);
$priority = optional_param('priority', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Set up page.
$PAGE->set_title(get_string('myreviews', 'local_jobboard'));
$PAGE->set_heading(get_string('myreviews', 'local_jobboard'));
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/jobboard/styles.css');

// Get my stats.
$mystats = reviewer::get_reviewer_stats($USER->id);
$myworkload = reviewer::get_reviewer_workload($USER->id);

// Build query for assigned applications.
$params = ['reviewerid' => $USER->id];
$whereclauses = ['a.reviewerid = :reviewerid'];

if ($status) {
    $whereclauses[] = 'a.status = :status';
    $params['status'] = $status;
}

if ($vacancyid) {
    $whereclauses[] = 'a.vacancyid = :vacancyid';
    $params['vacancyid'] = $vacancyid;
}

$whereclause = implode(' AND ', $whereclauses);

// Priority ordering.
// Note: Document status is in local_jobboard_doc_validation table, not document table.
$orderby = 'a.timecreated ASC';
if ($priority === 'closing') {
    $orderby = 'v.closedate ASC, a.timecreated ASC';
} else if ($priority === 'pending') {
    $orderby = "(SELECT COUNT(*) FROM {local_jobboard_document} d
                  LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                  WHERE d.applicationid = a.id AND d.issuperseded = 0
                  AND (dv.id IS NULL OR dv.status = 'pending')) DESC,
                 a.timecreated ASC";
}

// Count total.
$countsql = "SELECT COUNT(*)
               FROM {local_jobboard_application} a
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
              WHERE $whereclause";
$totalcount = $DB->count_records_sql($countsql, $params);

// Get assigned applications.
// Document validation status is in local_jobboard_doc_validation table.
$sql = "SELECT a.*, v.code as vacancy_code, v.title as vacancy_title, v.closedate,
               u.firstname, u.lastname, u.email,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 WHERE d.applicationid = a.id AND d.issuperseded = 0) as total_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                 WHERE d.applicationid = a.id AND d.issuperseded = 0 AND dv.status = 'approved') as validated_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                 WHERE d.applicationid = a.id AND d.issuperseded = 0 AND dv.status = 'rejected') as rejected_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                 WHERE d.applicationid = a.id AND d.issuperseded = 0
                 AND (dv.id IS NULL OR dv.status = 'pending')) as pending_docs
          FROM {local_jobboard_application} a
          JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
          JOIN {user} u ON u.id = a.userid
         WHERE $whereclause
         ORDER BY $orderby";

$applications = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Get vacancies for filter.
$vacancysql = "SELECT DISTINCT v.id, v.code, v.title
                 FROM {local_jobboard_vacancy} v
                 JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                WHERE a.reviewerid = :reviewerid
                ORDER BY v.code";
$vacancies = $DB->get_records_sql($vacancysql, ['reviewerid' => $USER->id]);

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-myreviews');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
    get_string('myreviews', 'local_jobboard') => null,
];

echo ui_helper::page_header(
    get_string('myreviews', 'local_jobboard'),
    $breadcrumbs,
    [[
        'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
        'label' => get_string('reviewdocuments', 'local_jobboard'),
        'icon' => 'clipboard-check',
        'class' => 'btn btn-primary',
    ]]
);

// ============================================================================
// STATISTICS ROW
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string)$myworkload,
    get_string('pendingassignments', 'local_jobboard'),
    'primary', 'tasks'
);
echo ui_helper::stat_card(
    (string)$mystats['validated'],
    get_string('documentsvalidated', 'local_jobboard'),
    'success', 'check-circle'
);
echo ui_helper::stat_card(
    (string)$mystats['rejected'],
    get_string('documentsrejected', 'local_jobboard'),
    'danger', 'times-circle'
);
echo ui_helper::stat_card(
    round($mystats['avg_time_hours'], 1) . 'h',
    get_string('avgvalidationtime', 'local_jobboard'),
    'info', 'clock'
);
echo html_writer::end_div();

// ============================================================================
// FILTER FORM
// ============================================================================
$statusOptions = ['' => get_string('allstatuses', 'local_jobboard')];
foreach (['submitted', 'under_review', 'docs_validated', 'docs_rejected'] as $s) {
    $statusOptions[$s] = get_string('status_' . $s, 'local_jobboard');
}

$vacancyOptions = [0 => get_string('allvacancies', 'local_jobboard')];
foreach ($vacancies as $v) {
    $vacancyOptions[$v->id] = format_string($v->code . ' - ' . $v->title);
}

$priorityOptions = [
    '' => get_string('datesubmitted', 'local_jobboard'),
    'closing' => get_string('closingdate', 'local_jobboard'),
    'pending' => get_string('pendingdocuments', 'local_jobboard'),
];

$filterDefinitions = [
    [
        'type' => 'select',
        'name' => 'status',
        'options' => $statusOptions,
        'col' => 'col-md-3',
    ],
    [
        'type' => 'select',
        'name' => 'vacancy',
        'options' => $vacancyOptions,
        'col' => 'col-md-4',
    ],
    [
        'type' => 'select',
        'name' => 'priority',
        'label' => get_string('sortby', 'local_jobboard'),
        'options' => $priorityOptions,
        'col' => 'col-md-3',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/index.php'))->out(false),
    $filterDefinitions,
    ['status' => $status, 'vacancy' => $vacancyid, 'priority' => $priority],
    ['view' => 'myreviews']
);

// ============================================================================
// RESULTS INFO
// ============================================================================
$showing = new stdClass();
$showing->from = $totalcount > 0 ? ($page * $perpage) + 1 : 0;
$showing->to = min(($page + 1) * $perpage, $totalcount);
$showing->total = $totalcount;
echo html_writer::tag('p', get_string('showing', 'local_jobboard', $showing),
    ['class' => 'text-muted small mb-3']);

// ============================================================================
// APPLICATIONS LIST
// ============================================================================
if (empty($applications)) {
    echo ui_helper::empty_state(
        get_string('noassignments', 'local_jobboard'),
        'clipboard-list'
    );
} else {
    echo html_writer::start_div('row');

    foreach ($applications as $app) {
        // Calculate stats.
        $totaldocs = (int)$app->total_docs;
        $validatedocs = (int)$app->validated_docs;
        $rejecteddocs = (int)$app->rejected_docs;
        $pendingdocs = (int)$app->pending_docs;

        // Urgency check.
        $daysUntilClose = ceil(($app->closedate - time()) / 86400);
        $isUrgent = $daysUntilClose <= 3 && $daysUntilClose > 0;
        $isClosed = $daysUntilClose <= 0;

        // Status color.
        $statusClass = 'badge-secondary';
        $headerClass = 'bg-secondary';
        if (in_array($app->status, ['docs_validated', 'selected'])) {
            $statusClass = 'badge-success';
            $headerClass = 'bg-success';
        } else if (in_array($app->status, ['docs_rejected', 'rejected'])) {
            $statusClass = 'badge-danger';
            $headerClass = 'bg-danger';
        } else if ($app->status === 'under_review') {
            $statusClass = 'badge-warning';
            $headerClass = 'bg-warning';
        } else if ($app->status === 'submitted') {
            $statusClass = 'badge-info';
            $headerClass = 'bg-info';
        }

        echo html_writer::start_div('col-lg-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm' . ($isUrgent ? ' border-danger' : ''));

        // Card header.
        echo html_writer::start_div('card-header d-flex justify-content-between align-items-center ' . $headerClass . ' text-white');
        echo html_writer::tag('span',
            get_string('status_' . $app->status, 'local_jobboard'),
            ['class' => 'font-weight-bold']
        );
        if ($isUrgent) {
            echo html_writer::tag('span',
                '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('closingsoon', 'local_jobboard', $daysUntilClose),
                ['class' => 'badge badge-danger']
            );
        } else if ($isClosed) {
            echo html_writer::tag('span',
                get_string('closed'),
                ['class' => 'badge badge-dark']
            );
        }
        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('card-body');

        // Applicant info.
        echo html_writer::tag('h5',
            '<i class="fa fa-user text-muted mr-2"></i>' . format_string($app->firstname . ' ' . $app->lastname),
            ['class' => 'card-title mb-1']
        );
        echo html_writer::tag('p',
            '<i class="fa fa-envelope text-muted mr-2"></i>' .
            html_writer::tag('a', $app->email, ['href' => 'mailto:' . $app->email, 'class' => 'text-muted']),
            ['class' => 'small mb-2']
        );

        // Vacancy info.
        echo html_writer::start_div('mb-3');
        echo html_writer::tag('span', format_string($app->vacancy_code), ['class' => 'badge badge-secondary mr-2']);
        echo html_writer::tag('span', format_string($app->vacancy_title), ['class' => 'text-muted small']);
        echo html_writer::end_div();

        // Document progress.
        echo html_writer::start_div('mb-3');
        echo html_writer::tag('small', get_string('documents', 'local_jobboard') . ':', ['class' => 'd-block mb-1 text-muted']);

        // Progress bar.
        if ($totaldocs > 0) {
            $vpct = round(($validatedocs / $totaldocs) * 100);
            $rpct = round(($rejecteddocs / $totaldocs) * 100);

            echo html_writer::start_div('progress mb-2', ['style' => 'height: 20px;']);
            echo html_writer::div($validatedocs, 'progress-bar bg-success', [
                'role' => 'progressbar',
                'style' => 'width: ' . $vpct . '%',
                'title' => get_string('docstatus:approved', 'local_jobboard'),
            ]);
            echo html_writer::div($rejecteddocs, 'progress-bar bg-danger', [
                'role' => 'progressbar',
                'style' => 'width: ' . $rpct . '%',
                'title' => get_string('docstatus:rejected', 'local_jobboard'),
            ]);
            echo html_writer::end_div();
        }

        // Document summary badges.
        echo html_writer::start_div('d-flex flex-wrap');
        echo html_writer::tag('span',
            '<i class="fa fa-check mr-1"></i>' . $validatedocs,
            ['class' => 'badge badge-success mr-2 mb-1', 'title' => get_string('docstatus:approved', 'local_jobboard')]
        );
        echo html_writer::tag('span',
            '<i class="fa fa-times mr-1"></i>' . $rejecteddocs,
            ['class' => 'badge badge-danger mr-2 mb-1', 'title' => get_string('docstatus:rejected', 'local_jobboard')]
        );
        echo html_writer::tag('span',
            '<i class="fa fa-clock mr-1"></i>' . $pendingdocs,
            ['class' => 'badge badge-warning mb-1', 'title' => get_string('docstatus:pending', 'local_jobboard')]
        );
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Closing date.
        echo html_writer::start_div('small');
        $closingClass = $isUrgent ? 'text-danger font-weight-bold' : 'text-muted';
        $closingIcon = $isUrgent ? 'fa-exclamation-triangle' : 'fa-calendar-alt';
        echo html_writer::tag('i', '', ['class' => 'fa ' . $closingIcon . ' mr-2 ' . $closingClass]);
        echo html_writer::tag('span',
            get_string('closedate', 'local_jobboard') . ': ' .
            userdate($app->closedate, get_string('strftimedate', 'langconfig')),
            ['class' => $closingClass]
        );
        echo html_writer::end_div();

        echo html_writer::end_div(); // card-body

        // Card footer.
        echo html_writer::start_div('card-footer bg-white d-flex justify-content-between');

        // Review button.
        $reviewUrl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $app->id,
        ]);
        $reviewClass = $pendingdocs > 0 ? 'btn btn-primary btn-sm' : 'btn btn-outline-primary btn-sm';
        echo html_writer::link($reviewUrl,
            '<i class="fa fa-search mr-1"></i>' . get_string('reviewdocuments', 'local_jobboard'),
            ['class' => $reviewClass]
        );

        // View application button.
        $viewUrl = new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id]);
        echo html_writer::link($viewUrl,
            '<i class="fa fa-eye"></i>',
            ['class' => 'btn btn-outline-secondary btn-sm', 'title' => get_string('viewapplication', 'local_jobboard')]
        );

        echo html_writer::end_div(); // card-footer

        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }

    echo html_writer::end_div(); // row
}

// ============================================================================
// PAGINATION
// ============================================================================
if ($totalcount > $perpage) {
    $baseurl = new moodle_url('/local/jobboard/index.php', [
        'view' => 'myreviews',
        'status' => $status,
        'vacancy' => $vacancyid,
        'priority' => $priority,
    ]);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
}

// ============================================================================
// QUICK LINKS
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
    '<i class="fa fa-clipboard-check mr-2"></i>' . get_string('reviewdocuments', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

if (has_capability('local/jobboard:createvacancy', $context)) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
        '<i class="fa fa-cog mr-2"></i>' . get_string('managevacancies', 'local_jobboard'),
        ['class' => 'btn btn-outline-info m-1']
    );
}

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-myreviews

// Additional styles.
echo html_writer::tag('style', '
.local-jobboard-myreviews .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.local-jobboard-myreviews .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
}
.local-jobboard-myreviews .progress-bar {
    font-size: 0.75rem;
    font-weight: bold;
}
');

echo $OUTPUT->footer();
