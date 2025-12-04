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
 * Workflow dashboard for managers.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\vacancy;
use local_jobboard\application;
use local_jobboard\bulk_validator;
use local_jobboard\reviewer;

require_login();

$context = context_system::instance();

// Check if user has any management capabilities.
$canreview = has_capability('local/jobboard:reviewdocuments', $context);
$canmanage = has_capability('local/jobboard:manageworkflow', $context);

if (!$canreview && !$canmanage) {
    throw new moodle_exception('noaccess', 'local_jobboard');
}

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/dashboard.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('dashboard', 'local_jobboard'));
$PAGE->set_heading(get_string('dashboard', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('dashboard', 'local_jobboard'));

// Get statistics.
$stats = [];

// Active vacancies.
$stats['active_vacancies'] = $DB->count_records('local_jobboard_vacancy', ['status' => 'published']);

// Applications by status.
$appstatuses = ['submitted', 'under_review', 'docs_validated', 'docs_rejected', 'interview', 'selected', 'rejected'];
$stats['applications'] = [];
foreach ($appstatuses as $status) {
    $stats['applications'][$status] = $DB->count_records('local_jobboard_application', ['status' => $status]);
}
$stats['total_applications'] = array_sum($stats['applications']);

// Documents pending validation.
$stats['pending_documents'] = $DB->count_records_sql(
    "SELECT COUNT(*)
       FROM {local_jobboard_document} d
      WHERE d.issuperseded = 0
        AND NOT EXISTS (SELECT 1 FROM {local_jobboard_doc_validation} v WHERE v.documentid = d.id)"
);

// Today's activity.
$today = strtotime('today');
$stats['applications_today'] = $DB->count_records_select('local_jobboard_application',
    'timecreated >= ?', [$today]);
$stats['validations_today'] = $DB->count_records_select('local_jobboard_doc_validation',
    'timecreated >= ?', [$today]);

// My assignments (if reviewer).
if ($canreview) {
    $stats['my_assignments'] = reviewer::get_reviewer_workload($USER->id);
    $stats['my_stats'] = reviewer::get_reviewer_stats($USER->id);
}

// Validation stats.
$validationstats = bulk_validator::get_validation_stats();

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('dashboard', 'local_jobboard'));

// Summary cards row.
echo '<div class="row mb-4">';

// Active vacancies.
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card bg-primary text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $stats['active_vacancies'] . '</h2>';
echo '<p class="mb-0">' . get_string('activevacancies', 'local_jobboard') . '</p>';
echo '</div>';
echo '<div class="card-footer bg-transparent border-0 text-center">';
echo '<a href="' . new moodle_url('/local/jobboard/manage.php') . '" class="text-white">' .
    get_string('manage') . ' &rarr;</a>';
echo '</div>';
echo '</div>';
echo '</div>';

// Total applications.
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card bg-info text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $stats['total_applications'] . '</h2>';
echo '<p class="mb-0">' . get_string('totalapplications', 'local_jobboard') . '</p>';
echo '</div>';
echo '<div class="card-footer bg-transparent border-0 text-center">';
echo '<small>' . $stats['applications_today'] . ' ' . get_string('today', 'local_jobboard') . '</small>';
echo '</div>';
echo '</div>';
echo '</div>';

// Pending documents.
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card bg-warning text-dark h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $stats['pending_documents'] . '</h2>';
echo '<p class="mb-0">' . get_string('pendingdocuments', 'local_jobboard') . '</p>';
echo '</div>';
echo '<div class="card-footer bg-transparent border-0 text-center">';
echo '<a href="' . new moodle_url('/local/jobboard/bulk_validate.php') . '" class="text-dark">' .
    get_string('validate', 'local_jobboard') . ' &rarr;</a>';
echo '</div>';
echo '</div>';
echo '</div>';

// My assignments.
if ($canreview) {
    echo '<div class="col-md-3 col-sm-6 mb-3">';
    echo '<div class="card bg-success text-white h-100">';
    echo '<div class="card-body text-center">';
    echo '<h2>' . $stats['my_assignments'] . '</h2>';
    echo '<p class="mb-0">' . get_string('myassignments', 'local_jobboard') . '</p>';
    echo '</div>';
    echo '<div class="card-footer bg-transparent border-0 text-center">';
    echo '<a href="' . new moodle_url('/local/jobboard/my_reviews.php') . '" class="text-white">' .
        get_string('view') . ' &rarr;</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

echo '</div>'; // End summary row.

// Application pipeline.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('applicationpipeline', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

echo '<div class="d-flex justify-content-between align-items-center flex-wrap">';
$pipelinestages = [
    'submitted' => 'info',
    'under_review' => 'primary',
    'docs_validated' => 'success',
    'docs_rejected' => 'danger',
    'interview' => 'warning',
    'selected' => 'success',
    'rejected' => 'secondary',
];

foreach ($pipelinestages as $stage => $color) {
    $count = $stats['applications'][$stage] ?? 0;
    echo '<div class="text-center p-2">';
    echo '<div class="badge badge-' . $color . ' p-3 mb-1" style="font-size: 1.5rem;">' . $count . '</div>';
    echo '<br><small>' . get_string('status_' . $stage, 'local_jobboard') . '</small>';
    echo '</div>';
    if ($stage !== 'rejected') {
        echo '<div class="text-muted">&rarr;</div>';
    }
}
echo '</div>';

echo '</div>';
echo '</div>';

// Two column layout for details.
echo '<div class="row">';

// Left column - Recent activity.
echo '<div class="col-md-6">';

// Recent applications.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('recentapplications', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body p-0">';

$recentapps = $DB->get_records_sql(
    "SELECT a.*, v.code as vacancy_code, v.title as vacancy_title,
            u.firstname, u.lastname
       FROM {local_jobboard_application} a
       JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
       JOIN {user} u ON u.id = a.userid
      ORDER BY a.timecreated DESC",
    null, 0, 10);

if (empty($recentapps)) {
    echo '<p class="p-3">' . get_string('norecentapplications', 'local_jobboard') . '</p>';
} else {
    echo '<ul class="list-group list-group-flush">';
    foreach ($recentapps as $app) {
        $statusclass = 'secondary';
        if (in_array($app->status, ['docs_validated', 'selected'])) {
            $statusclass = 'success';
        } else if (in_array($app->status, ['docs_rejected', 'rejected'])) {
            $statusclass = 'danger';
        } else if ($app->status === 'submitted') {
            $statusclass = 'info';
        }

        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
        echo '<div>';
        echo '<strong>' . format_string($app->firstname . ' ' . $app->lastname) . '</strong>';
        echo '<br><small class="text-muted">' . format_string($app->vacancy_code) . ' - ' .
            userdate($app->timecreated, get_string('strftimedatetime', 'langconfig')) . '</small>';
        echo '</div>';
        echo '<span class="badge badge-' . $statusclass . '">' .
            get_string('status_' . $app->status, 'local_jobboard') . '</span>';
        echo '</li>';
    }
    echo '</ul>';
}

echo '</div>';
echo '</div>';

echo '</div>'; // End left column.

// Right column - Validation stats.
echo '<div class="col-md-6">';

// Validation statistics.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('validationstats', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

echo '<div class="row text-center mb-3">';
echo '<div class="col-4">';
echo '<h4 class="text-success">' . $validationstats['validated'] . '</h4>';
echo '<small>' . get_string('validated', 'local_jobboard') . '</small>';
echo '</div>';
echo '<div class="col-4">';
echo '<h4 class="text-danger">' . $validationstats['rejected'] . '</h4>';
echo '<small>' . get_string('rejected', 'local_jobboard') . '</small>';
echo '</div>';
echo '<div class="col-4">';
echo '<h4 class="text-warning">' . $validationstats['pending'] . '</h4>';
echo '<small>' . get_string('pending', 'local_jobboard') . '</small>';
echo '</div>';
echo '</div>';

// Progress bar.
$total = $validationstats['total'] ?: 1;
$validatedpct = round(($validationstats['validated'] / $total) * 100);
$rejectedpct = round(($validationstats['rejected'] / $total) * 100);
$pendingpct = 100 - $validatedpct - $rejectedpct;

echo '<div class="progress mb-3" style="height: 25px;">';
echo '<div class="progress-bar bg-success" style="width: ' . $validatedpct . '%">' . $validatedpct . '%</div>';
echo '<div class="progress-bar bg-danger" style="width: ' . $rejectedpct . '%">' . $rejectedpct . '%</div>';
echo '<div class="progress-bar bg-warning" style="width: ' . $pendingpct . '%">' . $pendingpct . '%</div>';
echo '</div>';

echo '<p><small class="text-muted">' . get_string('avgvalidationtime', 'local_jobboard') . ': ' .
    $validationstats['avg_validation_time_hours'] . ' ' . get_string('hours') . '</small></p>';

echo '</div>';
echo '</div>';

// Quick actions.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('quickactions', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

echo '<div class="list-group">';

if ($canmanage) {
    echo '<a href="' . new moodle_url('/local/jobboard/assign_reviewer.php') . '" class="list-group-item list-group-item-action">';
    echo '<i class="fa fa-user-plus mr-2"></i>' . get_string('assignreviewers', 'local_jobboard');
    echo '</a>';
}

echo '<a href="' . new moodle_url('/local/jobboard/bulk_validate.php') . '" class="list-group-item list-group-item-action">';
echo '<i class="fa fa-check-square mr-2"></i>' . get_string('bulkvalidation', 'local_jobboard');
echo '</a>';

echo '<a href="' . new moodle_url('/local/jobboard/reports.php') . '" class="list-group-item list-group-item-action">';
echo '<i class="fa fa-chart-bar mr-2"></i>' . get_string('viewreports', 'local_jobboard');
echo '</a>';

if ($canmanage) {
    echo '<a href="' . new moodle_url('/local/jobboard/edit.php') . '" class="list-group-item list-group-item-action">';
    echo '<i class="fa fa-plus mr-2"></i>' . get_string('createvacancy', 'local_jobboard');
    echo '</a>';
}

echo '</div>';

echo '</div>';
echo '</div>';

echo '</div>'; // End right column.

echo '</div>'; // End row.

echo $OUTPUT->footer();
