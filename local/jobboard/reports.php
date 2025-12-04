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
 * Reports page for Job Board.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\bulk_validator;
use local_jobboard\reviewer;

$reporttype = optional_param('report', 'overview', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', 0, PARAM_INT);
$dateto = optional_param('dateto', 0, PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:viewreports', $context);

// Default date range: last 30 days.
if (!$datefrom) {
    $datefrom = strtotime('-30 days');
}
if (!$dateto) {
    $dateto = time();
}

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/reports.php', [
    'report' => $reporttype,
    'vacancyid' => $vacancyid,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('reports', 'local_jobboard'));
$PAGE->set_heading(get_string('reports', 'local_jobboard'));
$PAGE->set_pagelayout('report');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('reports', 'local_jobboard'));

// Handle export.
if ($format === 'csv' || $format === 'excel') {
    export_report($reporttype, $vacancyid, $datefrom, $dateto, $format);
    exit;
}

// Get vacancy list for filter.
$vacancies = $DB->get_records_select('local_jobboard_vacancy', '1=1', null, 'code ASC', 'id, code, title');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('reports', 'local_jobboard'));

// Report type tabs.
$reporttypes = [
    'overview' => get_string('reportoverview', 'local_jobboard'),
    'applications' => get_string('reportapplications', 'local_jobboard'),
    'documents' => get_string('reportdocuments', 'local_jobboard'),
    'reviewers' => get_string('reportreviewers', 'local_jobboard'),
    'timeline' => get_string('reporttimeline', 'local_jobboard'),
];

echo '<ul class="nav nav-tabs mb-4">';
foreach ($reporttypes as $type => $name) {
    $active = ($reporttype === $type) ? 'active' : '';
    $url = new moodle_url('/local/jobboard/reports.php', [
        'report' => $type,
        'vacancyid' => $vacancyid,
        'datefrom' => $datefrom,
        'dateto' => $dateto,
    ]);
    echo '<li class="nav-item">';
    echo '<a class="nav-link ' . $active . '" href="' . $url . '">' . $name . '</a>';
    echo '</li>';
}
echo '</ul>';

// Filters.
echo '<form class="form-inline mb-4" method="get">';
echo '<input type="hidden" name="report" value="' . $reporttype . '">';

echo '<div class="form-group mr-3">';
echo '<label class="mr-2">' . get_string('vacancy', 'local_jobboard') . '</label>';
echo '<select name="vacancyid" class="form-control">';
echo '<option value="0">' . get_string('allvacancies', 'local_jobboard') . '</option>';
foreach ($vacancies as $v) {
    $selected = ($vacancyid == $v->id) ? 'selected' : '';
    echo "<option value=\"{$v->id}\" {$selected}>" . format_string($v->code) . '</option>';
}
echo '</select>';
echo '</div>';

echo '<div class="form-group mr-3">';
echo '<label class="mr-2">' . get_string('datefrom', 'local_jobboard') . '</label>';
echo '<input type="date" name="datefrom" class="form-control" value="' . date('Y-m-d', $datefrom) . '">';
echo '</div>';

echo '<div class="form-group mr-3">';
echo '<label class="mr-2">' . get_string('dateto', 'local_jobboard') . '</label>';
echo '<input type="date" name="dateto" class="form-control" value="' . date('Y-m-d', $dateto) . '">';
echo '</div>';

echo '<button type="submit" class="btn btn-primary mr-2">' . get_string('filter') . '</button>';

// Export buttons.
echo '<div class="btn-group">';
echo '<a href="' . $PAGE->url . '&format=csv" class="btn btn-outline-secondary">' .
    get_string('exportcsv', 'local_jobboard') . '</a>';
echo '<a href="' . $PAGE->url . '&format=excel" class="btn btn-outline-secondary">' .
    get_string('exportexcel', 'local_jobboard') . '</a>';
echo '</div>';

echo '</form>';

// Render specific report.
switch ($reporttype) {
    case 'overview':
        render_overview_report($vacancyid, $datefrom, $dateto);
        break;
    case 'applications':
        render_applications_report($vacancyid, $datefrom, $dateto);
        break;
    case 'documents':
        render_documents_report($vacancyid, $datefrom, $dateto);
        break;
    case 'reviewers':
        render_reviewers_report($vacancyid, $datefrom, $dateto);
        break;
    case 'timeline':
        render_timeline_report($vacancyid, $datefrom, $dateto);
        break;
}

echo $OUTPUT->footer();

/**
 * Render overview report.
 */
function render_overview_report(int $vacancyid, int $datefrom, int $dateto): void {
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

    echo '<div class="row mb-4">';

    echo '<div class="col-md-3">';
    echo '<div class="card text-center">';
    echo '<div class="card-body">';
    echo '<h2>' . $totalapps . '</h2>';
    echo '<p>' . get_string('totalapplications', 'local_jobboard') . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<div class="card text-center bg-success text-white">';
    echo '<div class="card-body">';
    echo '<h2>' . $selectedapps . '</h2>';
    echo '<p>' . get_string('selected', 'local_jobboard') . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<div class="card text-center bg-danger text-white">';
    echo '<div class="card-body">';
    echo '<h2>' . $rejectedapps . '</h2>';
    echo '<p>' . get_string('rejected', 'local_jobboard') . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    $selectionrate = $totalapps > 0 ? round(($selectedapps / $totalapps) * 100, 1) : 0;
    echo '<div class="col-md-3">';
    echo '<div class="card text-center">';
    echo '<div class="card-body">';
    echo '<h2>' . $selectionrate . '%</h2>';
    echo '<p>' . get_string('selectionrate', 'local_jobboard') . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    // Applications by status chart data.
    $statusdata = $DB->get_records_sql(
        "SELECT a.status, COUNT(*) as count
           FROM {local_jobboard_application} a
          WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
          GROUP BY a.status", $params);

    echo '<div class="card mb-4">';
    echo '<div class="card-header">' . get_string('applicationsbystatus', 'local_jobboard') . '</div>';
    echo '<div class="card-body">';
    echo '<table class="table">';
    echo '<thead><tr><th>' . get_string('status', 'local_jobboard') . '</th><th>' . get_string('count') . '</th><th></th></tr></thead>';
    echo '<tbody>';
    foreach ($statusdata as $row) {
        $pct = $totalapps > 0 ? round(($row->count / $totalapps) * 100) : 0;
        echo '<tr>';
        echo '<td>' . get_string('status_' . $row->status, 'local_jobboard') . '</td>';
        echo '<td>' . $row->count . '</td>';
        echo '<td><div class="progress"><div class="progress-bar" style="width: ' . $pct . '%">' . $pct . '%</div></div></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

/**
 * Render applications report.
 */
function render_applications_report(int $vacancyid, int $datefrom, int $dateto): void {
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

    echo '<div class="card mb-4">';
    echo '<div class="card-header">' . get_string('applicationsbyvacancy', 'local_jobboard') . '</div>';
    echo '<div class="card-body">';
    echo '<table class="table table-striped">';
    echo '<thead><tr>';
    echo '<th>' . get_string('vacancy', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('total') . '</th>';
    echo '<th>' . get_string('selected', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('rejected', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('pending', 'local_jobboard') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($byvacancy as $row) {
        $pending = $row->total - $row->selected - $row->rejected;
        echo '<tr>';
        echo '<td>' . format_string($row->code . ' - ' . $row->title) . '</td>';
        echo '<td>' . $row->total . '</td>';
        echo '<td class="text-success">' . $row->selected . '</td>';
        echo '<td class="text-danger">' . $row->rejected . '</td>';
        echo '<td class="text-warning">' . $pending . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

/**
 * Render documents report.
 */
function render_documents_report(int $vacancyid, int $datefrom, int $dateto): void {
    $stats = bulk_validator::get_validation_stats($vacancyid ?: null, $datefrom);
    $rejectionreasons = bulk_validator::get_rejection_reasons_stats($vacancyid ?: null, $datefrom);

    echo '<div class="row mb-4">';

    // Validation stats.
    echo '<div class="col-md-6">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">' . get_string('validationsummary', 'local_jobboard') . '</div>';
    echo '<div class="card-body">';
    echo '<table class="table">';
    echo '<tr><td>' . get_string('totaldocuments', 'local_jobboard') . '</td><td><strong>' . $stats['total'] . '</strong></td></tr>';
    echo '<tr class="text-success"><td>' . get_string('validated', 'local_jobboard') . '</td><td>' . $stats['validated'] . ' (' . $stats['validation_rate'] . '%)</td></tr>';
    echo '<tr class="text-danger"><td>' . get_string('rejected', 'local_jobboard') . '</td><td>' . $stats['rejected'] . ' (' . $stats['rejection_rate'] . '%)</td></tr>';
    echo '<tr class="text-warning"><td>' . get_string('pending', 'local_jobboard') . '</td><td>' . $stats['pending'] . '</td></tr>';
    echo '<tr><td>' . get_string('avgvalidationtime', 'local_jobboard') . '</td><td>' . $stats['avg_validation_time_hours'] . ' ' . get_string('hours') . '</td></tr>';
    echo '</table>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Rejection reasons.
    echo '<div class="col-md-6">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">' . get_string('rejectionreasons', 'local_jobboard') . '</div>';
    echo '<div class="card-body">';
    if (empty($rejectionreasons)) {
        echo '<p>' . get_string('norejections', 'local_jobboard') . '</p>';
    } else {
        echo '<table class="table">';
        foreach ($rejectionreasons as $reason) {
            $reasontext = get_string('rejectreason_' . $reason->rejectreason, 'local_jobboard');
            echo '<tr><td>' . $reasontext . '</td><td>' . $reason->count . '</td></tr>';
        }
        echo '</table>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    // By document type.
    echo '<div class="card mb-4">';
    echo '<div class="card-header">' . get_string('bydocumenttype', 'local_jobboard') . '</div>';
    echo '<div class="card-body">';
    echo '<table class="table table-striped">';
    echo '<thead><tr>';
    echo '<th>' . get_string('documenttype', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('total') . '</th>';
    echo '<th>' . get_string('validated', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('rejected', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('pending', 'local_jobboard') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($stats['by_type'] as $row) {
        $typename = get_string('doctype_' . $row->documenttype, 'local_jobboard');
        echo '<tr>';
        echo '<td>' . $typename . '</td>';
        echo '<td>' . $row->total . '</td>';
        echo '<td class="text-success">' . $row->validated . '</td>';
        echo '<td class="text-danger">' . $row->rejected . '</td>';
        echo '<td class="text-warning">' . $row->pending . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

/**
 * Render reviewers report.
 */
function render_reviewers_report(int $vacancyid, int $datefrom, int $dateto): void {
    $reviewers = reviewer::get_all_with_workload();

    echo '<div class="card mb-4">';
    echo '<div class="card-header">' . get_string('reviewerperformance', 'local_jobboard') . '</div>';
    echo '<div class="card-body">';
    echo '<table class="table table-striped">';
    echo '<thead><tr>';
    echo '<th>' . get_string('reviewer', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('currentworkload', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('reviewed', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('validated', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('rejected', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('avgtime', 'local_jobboard') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($reviewers as $rev) {
        echo '<tr>';
        echo '<td>' . fullname($rev) . '</td>';
        echo '<td>' . $rev->workload . '</td>';
        echo '<td>' . ($rev->stats['reviewed'] ?? 0) . '</td>';
        echo '<td class="text-success">' . ($rev->stats['validated'] ?? 0) . '</td>';
        echo '<td class="text-danger">' . ($rev->stats['rejected'] ?? 0) . '</td>';
        echo '<td>' . ($rev->stats['avg_review_time'] ?? 0) . 'h</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

/**
 * Render timeline report.
 */
function render_timeline_report(int $vacancyid, int $datefrom, int $dateto): void {
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

    echo '<div class="card mb-4">';
    echo '<div class="card-header">' . get_string('dailyapplications', 'local_jobboard') . '</div>';
    echo '<div class="card-body">';
    echo '<table class="table table-striped">';
    echo '<thead><tr><th>' . get_string('date') . '</th><th>' . get_string('applications', 'local_jobboard') . '</th></tr></thead>';
    echo '<tbody>';
    foreach ($daily as $row) {
        echo '<tr><td>' . $row->day . '</td><td>' . $row->count . '</td></tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

/**
 * Export report data.
 */
function export_report(string $reporttype, int $vacancyid, int $datefrom, int $dateto, string $format): void {
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
    }
}
