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
 * Reports view for local_jobboard.
 *
 * Modern redesign with consistent UX pattern using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_jobboard\bulk_validator;
use local_jobboard\reviewer;
use local_jobboard\output\ui_helper;

// Require reports capability.
require_capability('local/jobboard:viewreports', $context);

// Parameters.
$reporttype = optional_param('report', 'overview', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', 0, PARAM_INT);
$dateto = optional_param('dateto', 0, PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);

// Default date range: last 30 days.
if (!$datefrom) {
    $datefrom = strtotime('-30 days');
}
if (!$dateto) {
    $dateto = time();
}

// Set up page.
$PAGE->set_title(get_string('reports', 'local_jobboard'));
$PAGE->set_heading(get_string('reports', 'local_jobboard'));
$PAGE->set_pagelayout('report');
$PAGE->requires->css('/local/jobboard/styles.css');

// Handle export.
if ($format === 'csv' || $format === 'excel' || $format === 'pdf') {
    local_jobboard_export_report($reporttype, $vacancyid, $datefrom, $dateto, $format);
    exit;
}

// Get vacancy list for filter.
$vacancies = $DB->get_records_select('local_jobboard_vacancy', '1=1', null, 'code ASC', 'id, code, title');

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-reports');

// ============================================================================
// PAGE HEADER WITH EXPORT ACTIONS
// ============================================================================
$exportbaseurl = new moodle_url('/local/jobboard/index.php', [
    'view' => 'reports',
    'report' => $reporttype,
    'vacancyid' => $vacancyid,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
]);

echo ui_helper::page_header(
    get_string('reports', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url($exportbaseurl, ['format' => 'csv']),
            'label' => 'CSV',
            'icon' => 'file-csv',
            'class' => 'btn btn-outline-secondary btn-sm',
        ],
        [
            'url' => new moodle_url($exportbaseurl, ['format' => 'excel']),
            'label' => 'Excel',
            'icon' => 'file-excel',
            'class' => 'btn btn-outline-success btn-sm',
        ],
        [
            'url' => new moodle_url($exportbaseurl, ['format' => 'pdf']),
            'label' => 'PDF',
            'icon' => 'file-pdf',
            'class' => 'btn btn-outline-danger btn-sm',
        ],
    ]
);

// ============================================================================
// REPORT TYPE TABS
// ============================================================================
$reporttypes = [
    'overview' => ['label' => get_string('reportoverview', 'local_jobboard'), 'icon' => 'chart-pie'],
    'applications' => ['label' => get_string('reportapplications', 'local_jobboard'), 'icon' => 'file-alt'],
    'documents' => ['label' => get_string('reportdocuments', 'local_jobboard'), 'icon' => 'folder-open'],
    'reviewers' => ['label' => get_string('reportreviewers', 'local_jobboard'), 'icon' => 'user-check'],
    'timeline' => ['label' => get_string('reporttimeline', 'local_jobboard'), 'icon' => 'calendar-alt'],
];

echo html_writer::start_tag('ul', ['class' => 'nav nav-pills mb-4 flex-wrap']);
foreach ($reporttypes as $type => $info) {
    $active = ($reporttype === $type) ? 'active' : '';
    $url = new moodle_url('/local/jobboard/index.php', [
        'view' => 'reports',
        'report' => $type,
        'vacancyid' => $vacancyid,
        'datefrom' => $datefrom,
        'dateto' => $dateto,
    ]);
    echo html_writer::start_tag('li', ['class' => 'nav-item']);
    echo html_writer::link($url,
        '<i class="fa fa-' . $info['icon'] . ' mr-2"></i>' . $info['label'],
        ['class' => 'nav-link ' . $active]
    );
    echo html_writer::end_tag('li');
}
echo html_writer::end_tag('ul');

// ============================================================================
// FILTERS
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
        'col' => 'col-md-4',
    ],
    [
        'type' => 'date',
        'name' => 'datefrom',
        'label' => get_string('datefrom', 'local_jobboard'),
        'col' => 'col-md-3',
    ],
    [
        'type' => 'date',
        'name' => 'dateto',
        'label' => get_string('dateto', 'local_jobboard'),
        'col' => 'col-md-3',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/index.php'))->out(false),
    $filterDefinitions,
    [
        'vacancyid' => $vacancyid,
        'datefrom' => date('Y-m-d', $datefrom),
        'dateto' => date('Y-m-d', $dateto),
    ],
    ['view' => 'reports', 'report' => $reporttype]
);

// ============================================================================
// RENDER SPECIFIC REPORT
// ============================================================================
switch ($reporttype) {
    case 'overview':
        local_jobboard_render_overview_report($vacancyid, $datefrom, $dateto);
        break;
    case 'applications':
        local_jobboard_render_applications_report($vacancyid, $datefrom, $dateto);
        break;
    case 'documents':
        local_jobboard_render_documents_report($vacancyid, $datefrom, $dateto);
        break;
    case 'reviewers':
        local_jobboard_render_reviewers_report($vacancyid, $datefrom, $dateto);
        break;
    case 'timeline':
        local_jobboard_render_timeline_report($vacancyid, $datefrom, $dateto);
        break;
}

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

if (has_capability('local/jobboard:viewallapplications', $context)) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
        '<i class="fa fa-briefcase mr-2"></i>' . get_string('managevacancies', 'local_jobboard'),
        ['class' => 'btn btn-outline-primary m-1']
    );
}

if (has_capability('local/jobboard:reviewdocuments', $context)) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/bulk_validate.php'),
        '<i class="fa fa-check-double mr-2"></i>' . get_string('bulkvalidation', 'local_jobboard'),
        ['class' => 'btn btn-outline-success m-1']
    );
}

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-reports

echo $OUTPUT->footer();

/**
 * Render overview report.
 *
 * @param int $vacancyid Vacancy ID filter.
 * @param int $datefrom Start date timestamp.
 * @param int $dateto End date timestamp.
 */
function local_jobboard_render_overview_report(int $vacancyid, int $datefrom, int $dateto): void {
    global $DB;

    $params = ['from' => $datefrom, 'to' => $dateto];
    $vacancywhere = '';
    if ($vacancyid) {
        $vacancywhere = ' AND a.vacancyid = :vacancyid';
        $params['vacancyid'] = $vacancyid;
    }

    // Summary stats.
    $totalapps = $DB->count_records_sql(
        "SELECT COUNT(*) FROM {local_jobboard_application} a
          WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}", $params);

    $selectedapps = $DB->count_records_sql(
        "SELECT COUNT(*) FROM {local_jobboard_application} a
          WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'selected' {$vacancywhere}", $params);

    $rejectedapps = $DB->count_records_sql(
        "SELECT COUNT(*) FROM {local_jobboard_application} a
          WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'rejected' {$vacancywhere}", $params);

    $pendingapps = $DB->count_records_sql(
        "SELECT COUNT(*) FROM {local_jobboard_application} a
          WHERE a.timecreated BETWEEN :from AND :to AND a.status IN ('submitted', 'under_review') {$vacancywhere}", $params);

    $selectionrate = $totalapps > 0 ? round(($selectedapps / $totalapps) * 100, 1) : 0;

    // Stats cards using ui_helper.
    echo html_writer::start_div('row mb-4');
    echo ui_helper::stat_card((string)$totalapps, get_string('totalapplications', 'local_jobboard'), 'primary', 'file-alt');
    echo ui_helper::stat_card((string)$selectedapps, get_string('selected', 'local_jobboard'), 'success', 'trophy');
    echo ui_helper::stat_card((string)$rejectedapps, get_string('rejected', 'local_jobboard'), 'danger', 'times-circle');
    echo ui_helper::stat_card($selectionrate . '%', get_string('selectionrate', 'local_jobboard'), 'info', 'chart-line');
    echo html_writer::end_div();

    // Applications by status chart data.
    $statusdata = $DB->get_records_sql(
        "SELECT a.status, COUNT(*) as count
           FROM {local_jobboard_application} a
          WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
          GROUP BY a.status", $params);

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-chart-bar text-primary mr-2"></i>' .
        get_string('applicationsbystatus', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($statusdata)) {
        echo ui_helper::empty_state(get_string('nodata', 'local_jobboard'), 'chart-bar');
    } else {
        echo html_writer::start_div('table-responsive');
        echo html_writer::start_tag('table', ['class' => 'table table-hover']);
        echo html_writer::start_tag('thead', ['class' => 'thead-light']);
        echo html_writer::tag('tr',
            html_writer::tag('th', get_string('status', 'local_jobboard')) .
            html_writer::tag('th', get_string('count', 'local_jobboard'), ['class' => 'text-center']) .
            html_writer::tag('th', get_string('percentage', 'local_jobboard'))
        );
        echo html_writer::end_tag('thead');
        echo html_writer::start_tag('tbody');

        foreach ($statusdata as $row) {
            $pct = $totalapps > 0 ? round(($row->count / $totalapps) * 100) : 0;
            $statusColor = local_jobboard_get_status_color($row->status);

            echo html_writer::start_tag('tr');
            echo html_writer::tag('td',
                html_writer::tag('span', get_string('status_' . $row->status, 'local_jobboard'),
                    ['class' => 'badge badge-' . $statusColor])
            );
            echo html_writer::tag('td', html_writer::tag('strong', $row->count), ['class' => 'text-center']);
            echo html_writer::tag('td',
                html_writer::start_div('progress', ['style' => 'height: 20px;']) .
                html_writer::div($pct . '%', 'progress-bar bg-' . $statusColor, [
                    'role' => 'progressbar',
                    'style' => 'width: ' . $pct . '%',
                ]) .
                html_writer::end_div()
            );
            echo html_writer::end_tag('tr');
        }

        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

/**
 * Get Bootstrap color class for status.
 *
 * @param string $status Application status.
 * @return string Bootstrap color class.
 */
function local_jobboard_get_status_color(string $status): string {
    $colors = [
        'submitted' => 'info',
        'under_review' => 'warning',
        'docs_validated' => 'success',
        'docs_rejected' => 'danger',
        'interview' => 'purple',
        'selected' => 'success',
        'rejected' => 'secondary',
        'withdrawn' => 'dark',
    ];
    return $colors[$status] ?? 'secondary';
}

/**
 * Render applications report.
 *
 * @param int $vacancyid Vacancy ID filter.
 * @param int $datefrom Start date timestamp.
 * @param int $dateto End date timestamp.
 */
function local_jobboard_render_applications_report(int $vacancyid, int $datefrom, int $dateto): void {
    global $DB;

    $params = ['from' => $datefrom, 'to' => $dateto];
    $vacancywhere = '';
    if ($vacancyid) {
        $vacancywhere = ' AND a.vacancyid = :vacancyid';
        $params['vacancyid'] = $vacancyid;
    }

    // Applications by vacancy.
    $byvacancy = $DB->get_records_sql(
        "SELECT v.id, v.code, v.title, COUNT(a.id) as total,
                SUM(CASE WHEN a.status = 'selected' THEN 1 ELSE 0 END) as selected,
                SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) as rejected
           FROM {local_jobboard_vacancy} v
           LEFT JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                AND a.timecreated BETWEEN :from AND :to
          GROUP BY v.id, v.code, v.title
          ORDER BY total DESC", $params);

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-briefcase text-primary mr-2"></i>' .
        get_string('applicationsbyvacancy', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($byvacancy)) {
        echo ui_helper::empty_state(get_string('nodata', 'local_jobboard'), 'briefcase');
    } else {
        $headers = [
            get_string('vacancy', 'local_jobboard'),
            get_string('total'),
            get_string('selected', 'local_jobboard'),
            get_string('rejected', 'local_jobboard'),
            get_string('pending', 'local_jobboard'),
        ];

        $rows = [];
        foreach ($byvacancy as $row) {
            $pending = $row->total - $row->selected - $row->rejected;
            $rows[] = [
                html_writer::tag('strong', format_string($row->code)) .
                    html_writer::tag('span', ' - ' . format_string($row->title), ['class' => 'text-muted']),
                html_writer::tag('span', $row->total, ['class' => 'badge badge-primary']),
                html_writer::tag('span', $row->selected, ['class' => 'badge badge-success']),
                html_writer::tag('span', $row->rejected, ['class' => 'badge badge-danger']),
                html_writer::tag('span', $pending, ['class' => 'badge badge-warning']),
            ];
        }

        echo ui_helper::data_table($headers, $rows);
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

/**
 * Render documents report.
 *
 * @param int $vacancyid Vacancy ID filter.
 * @param int $datefrom Start date timestamp.
 * @param int $dateto End date timestamp.
 */
function local_jobboard_render_documents_report(int $vacancyid, int $datefrom, int $dateto): void {
    $stats = bulk_validator::get_validation_stats($vacancyid ?: null, $datefrom);
    $rejectionreasons = bulk_validator::get_rejection_reasons_stats($vacancyid ?: null, $datefrom);

    // Stats cards.
    echo html_writer::start_div('row mb-4');
    echo ui_helper::stat_card((string)$stats['total'], get_string('totaldocuments', 'local_jobboard'), 'primary', 'folder');
    echo ui_helper::stat_card($stats['validated'] . ' (' . $stats['validation_rate'] . '%)',
        get_string('validated', 'local_jobboard'), 'success', 'check-circle');
    echo ui_helper::stat_card($stats['rejected'] . ' (' . $stats['rejection_rate'] . '%)',
        get_string('rejected', 'local_jobboard'), 'danger', 'times-circle');
    echo ui_helper::stat_card($stats['avg_validation_time_hours'] . 'h',
        get_string('avgvalidationtime', 'local_jobboard'), 'info', 'clock');
    echo html_writer::end_div();

    echo html_writer::start_div('row');

    // Rejection reasons.
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('card shadow-sm h-100');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-exclamation-triangle text-warning mr-2"></i>' .
        get_string('rejectionreasons', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($rejectionreasons)) {
        echo html_writer::tag('p', get_string('norejections', 'local_jobboard'), ['class' => 'text-muted mb-0']);
    } else {
        echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);
        foreach ($rejectionreasons as $reason) {
            $reasontext = get_string('rejectreason_' . $reason->rejectreason, 'local_jobboard');
            echo html_writer::tag('li',
                html_writer::tag('span', $reasontext) .
                html_writer::tag('span', $reason->count, ['class' => 'badge badge-danger badge-pill float-right']),
                ['class' => 'list-group-item d-flex justify-content-between align-items-center']
            );
        }
        echo html_writer::end_tag('ul');
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
    echo html_writer::end_div(); // col

    // Pending by type.
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('card shadow-sm h-100');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-clock text-info mr-2"></i>' .
        get_string('pendingbytype', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($stats['by_type'])) {
        echo html_writer::tag('p', get_string('nodata', 'local_jobboard'), ['class' => 'text-muted mb-0']);
    } else {
        echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);
        foreach ($stats['by_type'] as $row) {
            if ($row->pending > 0) {
                $typename = get_string('doctype_' . $row->documenttype, 'local_jobboard');
                echo html_writer::tag('li',
                    html_writer::tag('span', $typename) .
                    html_writer::tag('span', $row->pending, ['class' => 'badge badge-warning badge-pill float-right']),
                    ['class' => 'list-group-item d-flex justify-content-between align-items-center']
                );
            }
        }
        echo html_writer::end_tag('ul');
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
    echo html_writer::end_div(); // col

    echo html_writer::end_div(); // row

    // By document type table.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-file-alt text-primary mr-2"></i>' .
        get_string('bydocumenttype', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    $headers = [
        get_string('documenttype', 'local_jobboard'),
        get_string('total'),
        get_string('validated', 'local_jobboard'),
        get_string('rejected', 'local_jobboard'),
        get_string('pending', 'local_jobboard'),
    ];

    $rows = [];
    foreach ($stats['by_type'] as $row) {
        $typename = get_string('doctype_' . $row->documenttype, 'local_jobboard');
        $rows[] = [
            html_writer::tag('strong', $typename),
            html_writer::tag('span', $row->total, ['class' => 'badge badge-secondary']),
            html_writer::tag('span', $row->validated, ['class' => 'badge badge-success']),
            html_writer::tag('span', $row->rejected, ['class' => 'badge badge-danger']),
            html_writer::tag('span', $row->pending, ['class' => 'badge badge-warning']),
        ];
    }

    echo ui_helper::data_table($headers, $rows);

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

/**
 * Render reviewers report.
 *
 * @param int $vacancyid Vacancy ID filter.
 * @param int $datefrom Start date timestamp.
 * @param int $dateto End date timestamp.
 */
function local_jobboard_render_reviewers_report(int $vacancyid, int $datefrom, int $dateto): void {
    $reviewers = reviewer::get_all_with_workload();

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-users text-primary mr-2"></i>' .
        get_string('reviewerperformance', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($reviewers)) {
        echo ui_helper::empty_state(get_string('noreviewers', 'local_jobboard'), 'users');
    } else {
        $headers = [
            get_string('reviewer', 'local_jobboard'),
            get_string('currentworkload', 'local_jobboard'),
            get_string('reviewed', 'local_jobboard'),
            get_string('validated', 'local_jobboard'),
            get_string('rejected', 'local_jobboard'),
            get_string('avgtime', 'local_jobboard'),
        ];

        $rows = [];
        foreach ($reviewers as $rev) {
            $workloadClass = 'success';
            if ($rev->workload > 15) {
                $workloadClass = 'danger';
            } elseif ($rev->workload > 10) {
                $workloadClass = 'warning';
            }

            $rows[] = [
                html_writer::tag('strong', fullname($rev)),
                html_writer::tag('span', $rev->workload, ['class' => 'badge badge-' . $workloadClass]),
                ($rev->stats['reviewed'] ?? 0),
                html_writer::tag('span', ($rev->stats['validated'] ?? 0), ['class' => 'text-success']),
                html_writer::tag('span', ($rev->stats['rejected'] ?? 0), ['class' => 'text-danger']),
                ($rev->stats['avg_review_time'] ?? 0) . 'h',
            ];
        }

        echo ui_helper::data_table($headers, $rows);
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

/**
 * Render timeline report.
 *
 * @param int $vacancyid Vacancy ID filter.
 * @param int $datefrom Start date timestamp.
 * @param int $dateto End date timestamp.
 */
function local_jobboard_render_timeline_report(int $vacancyid, int $datefrom, int $dateto): void {
    global $DB;

    $params = ['from' => $datefrom, 'to' => $dateto];
    $vacancywhere = '';
    if ($vacancyid) {
        $vacancywhere = ' AND a.vacancyid = :vacancyid';
        $params['vacancyid'] = $vacancyid;
    }

    // Daily application counts.
    $sql = "SELECT DATE(FROM_UNIXTIME(a.timecreated)) as day, COUNT(*) as count
              FROM {local_jobboard_application} a
             WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
             GROUP BY DATE(FROM_UNIXTIME(a.timecreated))
             ORDER BY day ASC";

    $daily = $DB->get_records_sql($sql, $params);

    // Calculate max for visual bar.
    $maxcount = 0;
    foreach ($daily as $row) {
        if ($row->count > $maxcount) {
            $maxcount = $row->count;
        }
    }

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-calendar-alt text-primary mr-2"></i>' .
        get_string('dailyapplications', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($daily)) {
        echo ui_helper::empty_state(get_string('nodata', 'local_jobboard'), 'calendar-times');
    } else {
        echo html_writer::start_div('table-responsive');
        echo html_writer::start_tag('table', ['class' => 'table table-hover']);
        echo html_writer::start_tag('thead', ['class' => 'thead-light']);
        echo html_writer::tag('tr',
            html_writer::tag('th', get_string('date')) .
            html_writer::tag('th', get_string('applications', 'local_jobboard'), ['class' => 'text-center']) .
            html_writer::tag('th', '', ['style' => 'width: 50%;'])
        );
        echo html_writer::end_tag('thead');
        echo html_writer::start_tag('tbody');

        foreach ($daily as $row) {
            $pct = $maxcount > 0 ? round(($row->count / $maxcount) * 100) : 0;
            echo html_writer::start_tag('tr');
            echo html_writer::tag('td', html_writer::tag('code', $row->day));
            echo html_writer::tag('td', html_writer::tag('strong', $row->count), ['class' => 'text-center']);
            echo html_writer::tag('td',
                html_writer::start_div('progress', ['style' => 'height: 20px;']) .
                html_writer::div('', 'progress-bar bg-primary', [
                    'role' => 'progressbar',
                    'style' => 'width: ' . $pct . '%',
                ]) .
                html_writer::end_div()
            );
            echo html_writer::end_tag('tr');
        }

        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

/**
 * Export report data.
 *
 * @param string $reporttype Report type.
 * @param int $vacancyid Vacancy ID filter.
 * @param int $datefrom Start date timestamp.
 * @param int $dateto End date timestamp.
 * @param string $format Export format.
 */
function local_jobboard_export_report(string $reporttype, int $vacancyid, int $datefrom, int $dateto, string $format): void {
    global $DB;

    $params = ['from' => $datefrom, 'to' => $dateto];
    $vacancywhere = '';
    if ($vacancyid) {
        $vacancywhere = ' AND a.vacancyid = :vacancyid';
        $params['vacancyid'] = $vacancyid;
    }

    // Get data based on report type.
    $data = [];
    $headers = [];

    switch ($reporttype) {
        case 'applications':
            $headers = ['Vacancy', 'Applicant', 'Email', 'Status', 'Date Applied'];
            $records = $DB->get_records_sql(
                "SELECT a.id, v.code, v.title, u.firstname, u.lastname, u.email, a.status, a.timecreated
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                   JOIN {user} u ON u.id = a.userid
                  WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
                  ORDER BY a.timecreated DESC", $params);

            foreach ($records as $r) {
                $data[] = [
                    $r->code . ' - ' . $r->title,
                    $r->firstname . ' ' . $r->lastname,
                    $r->email,
                    get_string('status_' . $r->status, 'local_jobboard'),
                    userdate($r->timecreated, '%Y-%m-%d %H:%M'),
                ];
            }
            break;

        default:
            $headers = ['Metric', 'Value'];
            $data = [
                ['Total Applications', $DB->count_records_sql(
                    "SELECT COUNT(*) FROM {local_jobboard_application} a WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}", $params)],
            ];
    }

    // Output.
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . $reporttype . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    } else if ($format === 'excel') {
        global $CFG;
        require_once($CFG->libdir . '/excellib.class.php');

        $filename = 'report_' . $reporttype . '_' . date('Y-m-d') . '.xlsx';
        $workbook = new MoodleExcelWorkbook($filename);
        $worksheet = $workbook->add_worksheet($reporttype);

        // Write headers.
        $col = 0;
        foreach ($headers as $header) {
            $worksheet->write(0, $col++, $header);
        }

        // Write data.
        $row = 1;
        foreach ($data as $rowdata) {
            $col = 0;
            foreach ($rowdata as $cell) {
                $worksheet->write($row, $col++, $cell);
            }
            $row++;
        }

        $workbook->close();
    } else if ($format === 'pdf') {
        global $CFG;
        require_once($CFG->libdir . '/pdflib.php');

        $filename = 'report_' . $reporttype . '_' . date('Y-m-d') . '.pdf';

        // Create PDF document.
        $pdf = new pdf('L', 'mm', 'A4', true, 'UTF-8');

        // Set document information.
        $pdf->SetCreator('Job Board');
        $pdf->SetAuthor('Job Board');
        $pdf->SetTitle(get_string('reports', 'local_jobboard') . ' - ' . $reporttype);
        $pdf->SetSubject(get_string('reports', 'local_jobboard'));

        // Set margins.
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks.
        $pdf->SetAutoPageBreak(true, 25);

        // Add a page.
        $pdf->AddPage();

        // Report title.
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, get_string('reports', 'local_jobboard') . ': ' . ucfirst($reporttype), 0, 1, 'C');
        $pdf->Ln(5);

        // Date range.
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, get_string('datefrom', 'local_jobboard') . ': ' . date('Y-m-d', $datefrom) .
            ' - ' . get_string('dateto', 'local_jobboard') . ': ' . date('Y-m-d', $dateto), 0, 1, 'C');
        $pdf->Ln(10);

        // Build HTML table.
        $html = '<table border="1" cellpadding="5">';
        $html .= '<thead><tr style="background-color: #4472C4; color: white; font-weight: bold;">';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        $rownum = 0;
        foreach ($data as $rowdata) {
            $bgcolor = ($rownum % 2 == 0) ? '#ffffff' : '#f2f2f2';
            $html .= '<tr style="background-color: ' . $bgcolor . ';">';
            foreach ($rowdata as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
            $rownum++;
        }

        $html .= '</tbody></table>';

        // Write HTML table.
        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');

        // Footer with generation date.
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 6, get_string('generatedon', 'local_jobboard') . ': ' . date('Y-m-d H:i:s'), 0, 1, 'R');

        // Output PDF.
        $pdf->Output($filename, 'D');
    }
}
