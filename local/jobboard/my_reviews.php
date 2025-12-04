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
 * My review assignments page.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\reviewer;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Filter parameters.
$status = optional_param('status', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancy', 0, PARAM_INT);
$priority = optional_param('priority', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/my_reviews.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('myreviews', 'local_jobboard'));
$PAGE->set_heading(get_string('myreviews', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('myreviews', 'local_jobboard'));

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
$orderby = 'a.timecreated ASC';
if ($priority === 'closing') {
    $orderby = 'v.closedate ASC, a.timecreated ASC';
} else if ($priority === 'pending') {
    $orderby = "(SELECT COUNT(*) FROM {local_jobboard_document} d
                  WHERE d.applicationid = a.id AND d.issuperseded = 0
                  AND NOT EXISTS (SELECT 1 FROM {local_jobboard_doc_validation} dv WHERE dv.documentid = d.id)) DESC,
                 a.timecreated ASC";
}

// Count total.
$countsql = "SELECT COUNT(*)
               FROM {local_jobboard_application} a
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
              WHERE $whereclause";
$totalcount = $DB->count_records_sql($countsql, $params);

// Get assigned applications.
$sql = "SELECT a.*, v.code as vacancy_code, v.title as vacancy_title, v.closedate,
               u.firstname, u.lastname, u.email,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 WHERE d.applicationid = a.id AND d.issuperseded = 0) as total_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 WHERE d.applicationid = a.id AND d.issuperseded = 0
                 AND EXISTS (SELECT 1 FROM {local_jobboard_doc_validation} dv
                             WHERE dv.documentid = d.id AND dv.isvalid = 1)) as validated_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 WHERE d.applicationid = a.id AND d.issuperseded = 0
                 AND EXISTS (SELECT 1 FROM {local_jobboard_doc_validation} dv
                             WHERE dv.documentid = d.id AND dv.isvalid = 0)) as rejected_docs
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

echo $OUTPUT->heading(get_string('myreviews', 'local_jobboard'));

// Stats cards.
echo '<div class="row mb-4">';

echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card bg-primary text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $myworkload . '</h2>';
echo '<p class="mb-0">' . get_string('pendingassignments', 'local_jobboard') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card bg-success text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $mystats['validated'] . '</h2>';
echo '<p class="mb-0">' . get_string('documentsvalidated', 'local_jobboard') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card bg-danger text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $mystats['rejected'] . '</h2>';
echo '<p class="mb-0">' . get_string('documentsrejected', 'local_jobboard') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card bg-info text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . round($mystats['avg_time_hours'], 1) . 'h</h2>';
echo '<p class="mb-0">' . get_string('avgvalidationtime', 'local_jobboard') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Filters.
echo '<div class="card mb-4">';
echo '<div class="card-body">';
echo '<form method="get" action="" class="form-inline">';

// Status filter.
echo '<div class="form-group mr-3 mb-2">';
echo '<label for="status" class="mr-2">' . get_string('status') . ':</label>';
echo '<select name="status" id="status" class="form-control">';
echo '<option value="">' . get_string('all') . '</option>';
$statuses = ['submitted', 'under_review', 'docs_validated', 'docs_rejected'];
foreach ($statuses as $s) {
    $selected = ($status === $s) ? 'selected' : '';
    echo '<option value="' . $s . '" ' . $selected . '>' .
        get_string('status_' . $s, 'local_jobboard') . '</option>';
}
echo '</select>';
echo '</div>';

// Vacancy filter.
echo '<div class="form-group mr-3 mb-2">';
echo '<label for="vacancy" class="mr-2">' . get_string('vacancy', 'local_jobboard') . ':</label>';
echo '<select name="vacancy" id="vacancy" class="form-control">';
echo '<option value="0">' . get_string('all') . '</option>';
foreach ($vacancies as $v) {
    $selected = ($vacancyid == $v->id) ? 'selected' : '';
    echo '<option value="' . $v->id . '" ' . $selected . '>' .
        format_string($v->code . ' - ' . $v->title) . '</option>';
}
echo '</select>';
echo '</div>';

// Priority ordering.
echo '<div class="form-group mr-3 mb-2">';
echo '<label for="priority" class="mr-2">' . get_string('sortby', 'local_jobboard') . ':</label>';
echo '<select name="priority" id="priority" class="form-control">';
echo '<option value="">' . get_string('datesubmitted', 'local_jobboard') . '</option>';
echo '<option value="closing"' . ($priority === 'closing' ? ' selected' : '') . '>' .
    get_string('closingdate', 'local_jobboard') . '</option>';
echo '<option value="pending"' . ($priority === 'pending' ? ' selected' : '') . '>' .
    get_string('pendingdocuments', 'local_jobboard') . '</option>';
echo '</select>';
echo '</div>';

echo '<button type="submit" class="btn btn-primary mb-2">' . get_string('filter') . '</button>';
echo '</form>';
echo '</div>';
echo '</div>';

// Applications table.
if (empty($applications)) {
    echo $OUTPUT->notification(get_string('noassignments', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('applicant', 'local_jobboard'),
        get_string('vacancy', 'local_jobboard'),
        get_string('status'),
        get_string('documents', 'local_jobboard'),
        get_string('progress', 'local_jobboard'),
        get_string('closingdate', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'table table-striped table-hover';

    foreach ($applications as $app) {
        // Calculate progress.
        $totaldocs = $app->total_docs ?: 0;
        $validatedocs = $app->validated_docs ?: 0;
        $rejecteddocs = $app->rejected_docs ?: 0;
        $pendingdocs = $totaldocs - $validatedocs - $rejecteddocs;

        $progresspct = $totaldocs > 0 ? round(($validatedocs / $totaldocs) * 100) : 0;

        // Progress bar.
        $progressbar = '<div class="progress" style="height: 20px; min-width: 100px;">';
        if ($totaldocs > 0) {
            $vpct = round(($validatedocs / $totaldocs) * 100);
            $rpct = round(($rejecteddocs / $totaldocs) * 100);
            $progressbar .= '<div class="progress-bar bg-success" style="width: ' . $vpct . '%">' . $validatedocs . '</div>';
            $progressbar .= '<div class="progress-bar bg-danger" style="width: ' . $rpct . '%">' . $rejecteddocs . '</div>';
        }
        $progressbar .= '</div>';
        $progressbar .= '<small>' . $validatedocs . '/' . $totaldocs . ' ' . get_string('validated', 'local_jobboard') . '</small>';

        // Status badge.
        $statusclass = 'secondary';
        if (in_array($app->status, ['docs_validated', 'selected'])) {
            $statusclass = 'success';
        } else if (in_array($app->status, ['docs_rejected', 'rejected'])) {
            $statusclass = 'danger';
        } else if ($app->status === 'under_review') {
            $statusclass = 'primary';
        } else if ($app->status === 'submitted') {
            $statusclass = 'info';
        }

        // Document summary.
        $docsummary = '<span class="badge badge-success">' . $validatedocs . ' ✓</span> ';
        $docsummary .= '<span class="badge badge-danger">' . $rejecteddocs . ' ✗</span> ';
        $docsummary .= '<span class="badge badge-warning">' . $pendingdocs . ' ⏳</span>';

        // Closing date warning.
        $closingdate = userdate($app->closedate, get_string('strftimedate', 'langconfig'));
        $daysuntilclose = ceil(($app->closedate - time()) / 86400);
        if ($daysuntilclose <= 3 && $daysuntilclose > 0) {
            $closingdate = '<span class="text-danger font-weight-bold">' . $closingdate . '</span>';
            $closingdate .= '<br><small class="text-danger">' .
                get_string('closingsoon', 'local_jobboard', $daysuntilclose) . '</small>';
        } else if ($daysuntilclose <= 0) {
            $closingdate = '<span class="text-muted">' . $closingdate . '</span>';
            $closingdate .= '<br><small class="text-muted">' . get_string('closed') . '</small>';
        }

        // Actions.
        $actions = '';
        $viewurl = new moodle_url('/local/jobboard/application.php', ['id' => $app->id]);
        $actions .= '<a href="' . $viewurl . '" class="btn btn-sm btn-primary mr-1">' .
            get_string('reviewdocuments', 'local_jobboard') . '</a>';

        if ($pendingdocs > 0) {
            $bulkurl = new moodle_url('/local/jobboard/bulk_validate.php', ['application' => $app->id]);
            $actions .= '<a href="' . $bulkurl . '" class="btn btn-sm btn-outline-secondary">' .
                get_string('bulkvalidate', 'local_jobboard') . '</a>';
        }

        $row = [
            '<strong>' . format_string($app->firstname . ' ' . $app->lastname) . '</strong>' .
                '<br><small class="text-muted">' . $app->email . '</small>',
            '<span class="badge badge-secondary">' . format_string($app->vacancy_code) . '</span>' .
                '<br><small>' . format_string($app->vacancy_title) . '</small>',
            '<span class="badge badge-' . $statusclass . '">' .
                get_string('status_' . $app->status, 'local_jobboard') . '</span>',
            $docsummary,
            $progressbar,
            $closingdate,
            $actions,
        ];

        $table->data[] = $row;
    }

    echo html_writer::table($table);

    // Pagination.
    $baseurl = new moodle_url('/local/jobboard/my_reviews.php', [
        'status' => $status,
        'vacancy' => $vacancyid,
        'priority' => $priority,
    ]);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
}

// Quick links.
echo '<div class="mt-4">';
echo '<a href="' . new moodle_url('/local/jobboard/dashboard.php') . '" class="btn btn-outline-secondary mr-2">' .
    '<i class="fa fa-tachometer-alt mr-1"></i>' . get_string('dashboard', 'local_jobboard') . '</a>';
echo '<a href="' . new moodle_url('/local/jobboard/bulk_validate.php') . '" class="btn btn-outline-secondary">' .
    '<i class="fa fa-check-double mr-1"></i>' . get_string('bulkvalidation', 'local_jobboard') . '</a>';
echo '</div>';

echo $OUTPUT->footer();
