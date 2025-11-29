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
 * Platform Usage Report main page.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Require login.
require_login();

// Check capability.
$context = context_system::instance();
require_capability('report/platform_usage:view', $context);

// Get parameters.
$companyid = optional_param('companyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', strtotime('-30 days midnight'), PARAM_INT);
$dateto = optional_param('dateto', time(), PARAM_INT);

// Page setup.
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/report/platform_usage/index.php'));
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'report_platform_usage'));
$PAGE->set_heading(get_string('pluginname', 'report_platform_usage'));

// Create report instance.
$report = new \report_platform_usage\report($companyid, $datefrom, $dateto);

// Get companies for filter.
$companies = \report_platform_usage\report::get_companies();

// Get initial report data.
$loginSummary = $report->get_login_summary();
$userSummary = $report->get_user_activity_summary();
$dailyLogins = $report->get_daily_logins(30);
$courseAccessTrends = $report->get_course_access_trends(30);
$topCourses = $report->get_top_courses(10);
$topActivities = $report->get_top_activities(10);
$completionsSummary = $report->get_course_completions_summary();
$dashboardAccess = $report->get_dashboard_access();
$completionTrends = $report->get_completion_trends(30);

// Language strings for JavaScript.
$jsstrings = [
    'logins' => get_string('logins', 'report_platform_usage'),
    'uniqueusers' => get_string('uniqueusers', 'report_platform_usage'),
    'courseaccesses' => get_string('courseaccesses', 'report_platform_usage'),
    'activeusers' => get_string('activeusers', 'report_platform_usage'),
    'inactiveusers' => get_string('inactiveusers', 'report_platform_usage'),
    'nodata' => get_string('nodata', 'report_platform_usage'),
    'loadingreport' => get_string('loadingreport', 'report_platform_usage'),
    'coursename' => get_string('coursename', 'report_platform_usage'),
    'activityname' => get_string('activityname', 'report_platform_usage'),
    'activitytype' => get_string('activitytype', 'report_platform_usage'),
    'activityaccesses' => get_string('activityaccesses', 'report_platform_usage'),
    'completions' => get_string('completions', 'report_platform_usage'),
];

// Output header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('platformusagereport', 'report_platform_usage'));

// Company filter form.
echo '<div class="card mb-4">';
echo '<div class="card-body">';
echo '<div class="d-flex flex-wrap align-items-center">';
echo '<div class="form-group mr-3 mb-2">';
echo '<label for="companyid" class="mr-2">' . get_string('company', 'report_platform_usage') . ':</label>';
echo '<select name="companyid" id="companyid" class="form-control">';
echo '<option value="0">' . get_string('allcompanies', 'report_platform_usage') . '</option>';
foreach ($companies as $id => $name) {
    $selected = ($id == $companyid) ? 'selected' : '';
    echo "<option value=\"{$id}\" {$selected}>" . format_string($name) . '</option>';
}
echo '</select>';
echo '</div>';

// Loading indicator.
echo '<div id="loading-indicator" class="mb-2 mr-3" style="display: none;">';
echo '<span class="spinner-border spinner-border-sm text-primary" role="status"></span>';
echo ' <span class="text-muted">' . get_string('loadingreport', 'report_platform_usage') . '</span>';
echo '</div>';

// Export buttons.
if (has_capability('report/platform_usage:export', $context)) {
    $exporturl = new moodle_url('/report/platform_usage/export.php', [
        'datefrom' => $datefrom,
        'dateto' => $dateto,
        'sesskey' => sesskey(),
    ]);
    echo '<div class="ml-auto">';
    echo '<a href="' . $exporturl->out() . '&companyid=' . $companyid . '&type=summary&format=excel" id="export-excel" class="btn btn-success mb-2 mr-2">';
    echo '<i class="fa fa-download"></i> ' . get_string('exportexcel', 'report_platform_usage');
    echo '</a>';
    echo '<a href="' . $exporturl->out() . '&companyid=' . $companyid . '&type=summary&format=csv" id="export-csv" class="btn btn-secondary mb-2">';
    echo '<i class="fa fa-file-text"></i> ' . get_string('exportcsv', 'report_platform_usage');
    echo '</a>';
    echo '</div>';
}
echo '</div>';
echo '</div>';
echo '</div>';

// Summary cards row.
echo '<div class="row mb-4">';

// Logins Today card.
echo '<div class="col-md-4 col-sm-6 mb-3">';
echo '<div class="card h-100 border-primary">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-primary">' . get_string('loginstoday', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="logins-today">' . number_format($loginSummary['logins_today']) . '</h2>';
echo '<p class="text-muted">' . get_string('uniqueuserstoday', 'report_platform_usage') . ': <strong id="unique-today">' . number_format($loginSummary['unique_users_today']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Logins Last 7 Days card.
echo '<div class="col-md-4 col-sm-6 mb-3">';
echo '<div class="card h-100 border-success">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-success">' . get_string('loginsweek', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="logins-week">' . number_format($loginSummary['logins_week']) . '</h2>';
echo '<p class="text-muted">' . get_string('uniqueusersweek', 'report_platform_usage') . ': <strong id="unique-week">' . number_format($loginSummary['unique_users_week']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Logins Last 30 Days card.
echo '<div class="col-md-4 col-sm-6 mb-3">';
echo '<div class="card h-100 border-warning">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-warning">' . get_string('loginsmonth', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="logins-month">' . number_format($loginSummary['logins_month']) . '</h2>';
echo '<p class="text-muted">' . get_string('uniqueusersmonth', 'report_platform_usage') . ': <strong id="unique-month">' . number_format($loginSummary['unique_users_month']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Engagement metrics row.
echo '<div class="row mb-4">';

// Course Completions card.
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-info">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-info">' . get_string('completionsmonth', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="completions-month">' . number_format($completionsSummary['completions_month']) . '</h2>';
echo '<p class="text-muted">' . get_string('totalcompletions', 'report_platform_usage') . ': <strong id="total-completions">' . number_format($completionsSummary['total_completions']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Dashboard Access card.
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-secondary">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-secondary">' . get_string('dashboardusers', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="dashboard-month">' . number_format($dashboardAccess['month']) . '</h2>';
echo '<p class="text-muted">' . get_string('dashboardtoday', 'report_platform_usage') . ': <strong id="dashboard-today">' . number_format($dashboardAccess['today']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Completions Today card.
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-dark">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-dark">' . get_string('completionstoday', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="completions-today">' . number_format($completionsSummary['completions_today']) . '</h2>';
echo '<p class="text-muted">' . get_string('completionsweek', 'report_platform_usage') . ': <strong id="completions-week">' . number_format($completionsSummary['completions_week']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Dashboard Week card.
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-danger">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-danger">' . get_string('dashboardweek', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="dashboard-week">' . number_format($dashboardAccess['week']) . '</h2>';
echo '<p class="text-muted">' . get_string('dashboardtoday', 'report_platform_usage') . ': <strong>' . number_format($dashboardAccess['today']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Charts row 1: Daily Logins and User Activity.
echo '<div class="row mb-4">';

// Daily Logins Line Chart.
echo '<div class="col-lg-8 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('dailylogins', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="dailyLoginsChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

// User Activity Doughnut Chart.
echo '<div class="col-lg-4 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('usersbyactivity', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="userActivityChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Charts row 2: Course Access Trends and Top Courses.
echo '<div class="row mb-4">';

// Course Access Trends.
echo '<div class="col-lg-8 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('courseaccesstrends', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="courseAccessChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

// Top Courses Bar Chart.
echo '<div class="col-lg-4 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('topcourses', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="topCoursesChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Charts row 3: Completion Trends.
echo '<div class="row mb-4">';

// Completion Trends Chart.
echo '<div class="col-lg-12 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('completiontrends', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="completionTrendsChart" height="200"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Tables row.
echo '<div class="row mb-4">';

// Top Courses Table.
echo '<div class="col-lg-6 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('courseusage', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body" id="top-courses-table">';
if (empty($topCourses)) {
    echo '<p class="text-muted">' . get_string('nodata', 'report_platform_usage') . '</p>';
} else {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-sm">';
    echo '<thead><tr>';
    echo '<th>' . get_string('coursename', 'report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('courseaccesses', 'report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('uniqueusers', 'report_platform_usage') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($topCourses as $course) {
        echo '<tr>';
        echo '<td>' . format_string($course->fullname) . '</td>';
        echo '<td class="text-right">' . number_format($course->access_count) . '</td>';
        echo '<td class="text-right">' . number_format($course->unique_users) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}
echo '</div>';
echo '</div>';
echo '</div>';

// Top Activities Table.
echo '<div class="col-lg-6 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('topactivities', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body" id="top-activities-table">';
if (empty($topActivities)) {
    echo '<p class="text-muted">' . get_string('nodata', 'report_platform_usage') . '</p>';
} else {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-sm">';
    echo '<thead><tr>';
    echo '<th>' . get_string('activityname', 'report_platform_usage') . '</th>';
    echo '<th>' . get_string('coursename', 'report_platform_usage') . '</th>';
    echo '<th>' . get_string('activitytype', 'report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('activityaccesses', 'report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('uniqueusers', 'report_platform_usage') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($topActivities as $activity) {
        echo '<tr>';
        echo '<td>' . format_string($activity->name) . '</td>';
        echo '<td><small class="text-muted">' . format_string($activity->course_name) . '</small></td>';
        echo '<td><span class="badge badge-secondary">' . $activity->type_name . '</span></td>';
        echo '<td class="text-right">' . number_format($activity->access_count) . '</td>';
        echo '<td class="text-right">' . number_format($activity->unique_users) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Chart.js and AJAX initialization.
$ajaxurl = $CFG->wwwroot . '/report/platform_usage/ajax.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Language strings.
    var STRINGS = <?php echo json_encode($jsstrings); ?>;

    // AJAX URL.
    var AJAX_URL = '<?php echo $ajaxurl; ?>';

    // Initial data.
    var currentData = {
        login_summary: <?php echo json_encode($loginSummary); ?>,
        user_summary: <?php echo json_encode($userSummary); ?>,
        daily_logins: <?php echo json_encode($dailyLogins); ?>,
        course_access_trends: <?php echo json_encode($courseAccessTrends); ?>,
        top_courses: <?php echo json_encode(array_values($topCourses)); ?>,
        top_activities: <?php echo json_encode($topActivities); ?>,
        completions_summary: <?php echo json_encode($completionsSummary); ?>,
        dashboard_access: <?php echo json_encode($dashboardAccess); ?>,
        completion_trends: <?php echo json_encode($completionTrends); ?>
    };

    // Chart instances.
    var charts = {};

    // Initialize charts.
    initCharts(currentData);

    // Company filter change event - automatic AJAX loading.
    document.getElementById('companyid').addEventListener('change', function() {
        var companyId = this.value;
        loadReportData(companyId);
        updateExportLinks(companyId);
    });

    /**
     * Initialize all charts.
     */
    function initCharts(data) {
        // Daily Logins Line Chart.
        var dailyLoginsCtx = document.getElementById('dailyLoginsChart').getContext('2d');
        charts.dailyLogins = new Chart(dailyLoginsCtx, {
            type: 'line',
            data: {
                labels: data.daily_logins.labels,
                datasets: [
                    {
                        label: STRINGS.logins,
                        data: data.daily_logins.logins,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: STRINGS.uniqueusers,
                        data: data.daily_logins.unique_users,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // User Activity Doughnut Chart.
        var userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
        charts.userActivity = new Chart(userActivityCtx, {
            type: 'doughnut',
            data: {
                labels: [STRINGS.activeusers, STRINGS.inactiveusers],
                datasets: [{
                    data: [data.user_summary.active, data.user_summary.inactive],
                    backgroundColor: ['#28a745', '#dc3545'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Course Access Trends Line Chart.
        var courseAccessCtx = document.getElementById('courseAccessChart').getContext('2d');
        charts.courseAccess = new Chart(courseAccessCtx, {
            type: 'line',
            data: {
                labels: data.course_access_trends.labels,
                datasets: [{
                    label: STRINGS.courseaccesses,
                    data: data.course_access_trends.data,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Top Courses Bar Chart.
        var topCoursesCtx = document.getElementById('topCoursesChart').getContext('2d');
        charts.topCourses = new Chart(topCoursesCtx, {
            type: 'bar',
            data: {
                labels: data.top_courses.map(function(c) {
                    return c.shortname && c.shortname.length > 15 ? c.shortname.substring(0, 12) + '...' : (c.shortname || '');
                }),
                datasets: [{
                    label: STRINGS.courseaccesses,
                    data: data.top_courses.map(function(c) { return c.access_count; }),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)', 'rgba(83, 102, 255, 0.7)',
                        'rgba(255, 99, 255, 0.7)', 'rgba(99, 255, 132, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true } }
            }
        });

        // Completion Trends Line Chart.
        var completionTrendsCtx = document.getElementById('completionTrendsChart').getContext('2d');
        charts.completionTrends = new Chart(completionTrendsCtx, {
            type: 'line',
            data: {
                labels: data.completion_trends.labels,
                datasets: [{
                    label: STRINGS.completions,
                    data: data.completion_trends.data,
                    borderColor: 'rgba(23, 162, 184, 1)',
                    backgroundColor: 'rgba(23, 162, 184, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    /**
     * Update all charts with new data.
     */
    function updateCharts(data) {
        // Update Daily Logins chart.
        charts.dailyLogins.data.labels = data.daily_logins.labels;
        charts.dailyLogins.data.datasets[0].data = data.daily_logins.logins;
        charts.dailyLogins.data.datasets[1].data = data.daily_logins.unique_users;
        charts.dailyLogins.update();

        // Update User Activity chart.
        charts.userActivity.data.datasets[0].data = [data.user_summary.active, data.user_summary.inactive];
        charts.userActivity.update();

        // Update Course Access Trends chart.
        charts.courseAccess.data.labels = data.course_access_trends.labels;
        charts.courseAccess.data.datasets[0].data = data.course_access_trends.data;
        charts.courseAccess.update();

        // Update Top Courses chart.
        charts.topCourses.data.labels = data.top_courses.map(function(c) {
            return c.shortname && c.shortname.length > 15 ? c.shortname.substring(0, 12) + '...' : (c.shortname || '');
        });
        charts.topCourses.data.datasets[0].data = data.top_courses.map(function(c) { return c.access_count; });
        charts.topCourses.update();

        // Update Completion Trends chart.
        if (data.completion_trends) {
            charts.completionTrends.data.labels = data.completion_trends.labels;
            charts.completionTrends.data.datasets[0].data = data.completion_trends.data;
            charts.completionTrends.update();
        }
    }

    /**
     * Update summary cards.
     */
    function updateSummaryCards(data) {
        document.getElementById('logins-today').textContent = numberFormat(data.login_summary.logins_today);
        document.getElementById('unique-today').textContent = numberFormat(data.login_summary.unique_users_today);
        document.getElementById('logins-week').textContent = numberFormat(data.login_summary.logins_week);
        document.getElementById('unique-week').textContent = numberFormat(data.login_summary.unique_users_week);
        document.getElementById('logins-month').textContent = numberFormat(data.login_summary.logins_month);
        document.getElementById('unique-month').textContent = numberFormat(data.login_summary.unique_users_month);

        // Update completions cards.
        if (data.completions_summary) {
            document.getElementById('completions-month').textContent = numberFormat(data.completions_summary.completions_month);
            document.getElementById('total-completions').textContent = numberFormat(data.completions_summary.total_completions);
            document.getElementById('completions-today').textContent = numberFormat(data.completions_summary.completions_today);
            document.getElementById('completions-week').textContent = numberFormat(data.completions_summary.completions_week);
        }

        // Update dashboard cards.
        if (data.dashboard_access) {
            document.getElementById('dashboard-month').textContent = numberFormat(data.dashboard_access.month);
            document.getElementById('dashboard-today').textContent = numberFormat(data.dashboard_access.today);
            document.getElementById('dashboard-week').textContent = numberFormat(data.dashboard_access.week);
        }
    }

    /**
     * Update tables.
     */
    function updateTables(data) {
        // Update courses table.
        var coursesHtml = '';
        if (data.top_courses.length === 0) {
            coursesHtml = '<p class="text-muted">' + STRINGS.nodata + '</p>';
        } else {
            coursesHtml = '<div class="table-responsive"><table class="table table-striped table-sm">';
            coursesHtml += '<thead><tr><th>' + STRINGS.coursename + '</th>';
            coursesHtml += '<th class="text-right">' + STRINGS.courseaccesses + '</th>';
            coursesHtml += '<th class="text-right">' + STRINGS.uniqueusers + '</th></tr></thead><tbody>';
            data.top_courses.forEach(function(course) {
                coursesHtml += '<tr><td>' + escapeHtml(course.fullname) + '</td>';
                coursesHtml += '<td class="text-right">' + numberFormat(course.access_count) + '</td>';
                coursesHtml += '<td class="text-right">' + numberFormat(course.unique_users) + '</td></tr>';
            });
            coursesHtml += '</tbody></table></div>';
        }
        document.getElementById('top-courses-table').innerHTML = coursesHtml;

        // Update activities table.
        var activitiesHtml = '';
        if (data.top_activities.length === 0) {
            activitiesHtml = '<p class="text-muted">' + STRINGS.nodata + '</p>';
        } else {
            activitiesHtml = '<div class="table-responsive"><table class="table table-striped table-sm">';
            activitiesHtml += '<thead><tr><th>' + STRINGS.activityname + '</th>';
            activitiesHtml += '<th>' + STRINGS.coursename + '</th>';
            activitiesHtml += '<th>' + STRINGS.activitytype + '</th>';
            activitiesHtml += '<th class="text-right">' + STRINGS.activityaccesses + '</th>';
            activitiesHtml += '<th class="text-right">' + STRINGS.uniqueusers + '</th></tr></thead><tbody>';
            data.top_activities.forEach(function(activity) {
                activitiesHtml += '<tr><td>' + escapeHtml(activity.name) + '</td>';
                activitiesHtml += '<td><small class="text-muted">' + escapeHtml(activity.course_name || '') + '</small></td>';
                activitiesHtml += '<td><span class="badge badge-secondary">' + escapeHtml(activity.type_name || activity.type) + '</span></td>';
                activitiesHtml += '<td class="text-right">' + numberFormat(activity.access_count) + '</td>';
                activitiesHtml += '<td class="text-right">' + numberFormat(activity.unique_users) + '</td></tr>';
            });
            activitiesHtml += '</tbody></table></div>';
        }
        document.getElementById('top-activities-table').innerHTML = activitiesHtml;
    }

    /**
     * Load report data via AJAX.
     */
    function loadReportData(companyId) {
        var loading = document.getElementById('loading-indicator');
        loading.style.display = 'inline-block';

        var url = AJAX_URL + '?companyid=' + companyId;

        fetch(url)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                currentData = data;
                updateSummaryCards(data);
                updateCharts(data);
                updateTables(data);
                loading.style.display = 'none';
            })
            .catch(function(error) {
                console.error('Error loading report data:', error);
                loading.style.display = 'none';
            });
    }

    /**
     * Update export links with current company.
     */
    function updateExportLinks(companyId) {
        var excelLink = document.getElementById('export-excel');
        var csvLink = document.getElementById('export-csv');

        if (excelLink) {
            var url = excelLink.href.replace(/companyid=\d+/, 'companyid=' + companyId);
            excelLink.href = url;
        }
        if (csvLink) {
            var url = csvLink.href.replace(/companyid=\d+/, 'companyid=' + companyId);
            csvLink.href = url;
        }
    }

    /**
     * Format number with thousands separator.
     */
    function numberFormat(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    /**
     * Escape HTML special characters.
     */
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
<?php

echo $OUTPUT->footer();
