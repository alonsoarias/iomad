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
 * @package   local_report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Require login.
require_login();

// Check capability.
$context = context_system::instance();
require_capability('local/report_platform_usage:view', $context);

// Get parameters.
$companyid = optional_param('companyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', strtotime('-30 days midnight'), PARAM_INT);
$dateto = optional_param('dateto', time(), PARAM_INT);

// Page setup.
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/report_platform_usage/index.php'));
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'local_report_platform_usage'));
$PAGE->set_heading(get_string('pluginname', 'local_report_platform_usage'));

// Create report instance.
$report = new \local_report_platform_usage\report($companyid, $datefrom, $dateto);

// Get companies for filter.
$companies = \local_report_platform_usage\report::get_companies();

// Get report data.
$loginSummary = $report->get_login_summary();
$userSummary = $report->get_user_activity_summary();
$dailyLogins = $report->get_daily_logins(30);
$courseAccessTrends = $report->get_course_access_trends(30);
$topCourses = $report->get_top_courses(10);
$topActivities = $report->get_top_activities(10);

// Output header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('platformusagereport', 'local_report_platform_usage'));

// Company filter form.
echo '<div class="card mb-4">';
echo '<div class="card-body">';
echo '<form method="get" action="" class="form-inline">';
echo '<div class="form-group mr-3 mb-2">';
echo '<label for="companyid" class="mr-2">' . get_string('company', 'local_report_platform_usage') . ':</label>';
echo '<select name="companyid" id="companyid" class="form-control">';
echo '<option value="0">' . get_string('allcompanies', 'local_report_platform_usage') . '</option>';
foreach ($companies as $id => $name) {
    $selected = ($id == $companyid) ? 'selected' : '';
    echo "<option value=\"{$id}\" {$selected}>" . format_string($name) . '</option>';
}
echo '</select>';
echo '</div>';
echo '<button type="submit" class="btn btn-primary mb-2 mr-3">' . get_string('filter', 'local_report_platform_usage') . '</button>';

// Export buttons.
if (has_capability('local/report_platform_usage:export', $context)) {
    $exporturl = new moodle_url('/local/report_platform_usage/export.php', [
        'companyid' => $companyid,
        'datefrom' => $datefrom,
        'dateto' => $dateto,
        'sesskey' => sesskey(),
    ]);
    echo '<a href="' . $exporturl->out() . '&type=summary&format=excel" class="btn btn-success mb-2 mr-2">';
    echo '<i class="fa fa-download"></i> ' . get_string('exportexcel', 'local_report_platform_usage');
    echo '</a>';
    echo '<a href="' . $exporturl->out() . '&type=summary&format=csv" class="btn btn-secondary mb-2">';
    echo '<i class="fa fa-file-text"></i> ' . get_string('exportcsv', 'local_report_platform_usage');
    echo '</a>';
}
echo '</form>';
echo '</div>';
echo '</div>';

// Summary cards row.
echo '<div class="row mb-4">';

// Logins Today card.
echo '<div class="col-md-4 col-sm-6 mb-3">';
echo '<div class="card h-100 border-primary">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-primary">' . get_string('loginstoday', 'local_report_platform_usage') . '</h5>';
echo '<h2 class="display-4">' . number_format($loginSummary['logins_today']) . '</h2>';
echo '<p class="text-muted">' . get_string('uniqueuserstoday', 'local_report_platform_usage') . ': <strong>' . number_format($loginSummary['unique_users_today']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Logins Last 7 Days card.
echo '<div class="col-md-4 col-sm-6 mb-3">';
echo '<div class="card h-100 border-success">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-success">' . get_string('loginsweek', 'local_report_platform_usage') . '</h5>';
echo '<h2 class="display-4">' . number_format($loginSummary['logins_week']) . '</h2>';
echo '<p class="text-muted">' . get_string('uniqueusersweek', 'local_report_platform_usage') . ': <strong>' . number_format($loginSummary['unique_users_week']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Logins Last 30 Days card.
echo '<div class="col-md-4 col-sm-6 mb-3">';
echo '<div class="card h-100 border-warning">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-warning">' . get_string('loginsmonth', 'local_report_platform_usage') . '</h5>';
echo '<h2 class="display-4">' . number_format($loginSummary['logins_month']) . '</h2>';
echo '<p class="text-muted">' . get_string('uniqueusersmonth', 'local_report_platform_usage') . ': <strong>' . number_format($loginSummary['unique_users_month']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Charts row 1: Daily Logins and User Activity.
echo '<div class="row mb-4">';

// Daily Logins Line Chart.
echo '<div class="col-lg-8 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('dailylogins', 'local_report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="dailyLoginsChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

// User Activity Doughnut Chart.
echo '<div class="col-lg-4 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('usersbyactivity', 'local_report_platform_usage') . '</h5></div>';
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
echo '<div class="card-header"><h5 class="mb-0">' . get_string('courseaccesstrends', 'local_report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="courseAccessChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

// Top Courses Bar Chart.
echo '<div class="col-lg-4 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('topcourses', 'local_report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="topCoursesChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Top Courses Table.
echo '<div class="row mb-4">';
echo '<div class="col-lg-6 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('courseusage', 'local_report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
if (empty($topCourses)) {
    echo '<p class="text-muted">' . get_string('nodata', 'local_report_platform_usage') . '</p>';
} else {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-sm">';
    echo '<thead><tr>';
    echo '<th>' . get_string('coursename', 'local_report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('courseaccesses', 'local_report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('uniqueusers', 'local_report_platform_usage') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($topCourses as $course) {
        echo '<tr>';
        echo '<td>' . format_string($course->fullname) . '</td>';
        echo '<td class="text-right">' . number_format($course->access_count) . '</td>';
        echo '<td class="text-right">' . number_format($course->unique_users) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
echo '</div>';
echo '</div>';
echo '</div>';

// Top Activities Table.
echo '<div class="col-lg-6 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('topactivities', 'local_report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
if (empty($topActivities)) {
    echo '<p class="text-muted">' . get_string('nodata', 'local_report_platform_usage') . '</p>';
} else {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-sm">';
    echo '<thead><tr>';
    echo '<th>' . get_string('activityname', 'local_report_platform_usage') . '</th>';
    echo '<th>' . get_string('activitytype', 'local_report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('activityaccesses', 'local_report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('uniqueusers', 'local_report_platform_usage') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($topActivities as $activity) {
        echo '<tr>';
        echo '<td>' . format_string($activity->name) . '</td>';
        echo '<td>' . $activity->type . '</td>';
        echo '<td class="text-right">' . number_format($activity->access_count) . '</td>';
        echo '<td class="text-right">' . number_format($activity->unique_users) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Chart.js initialization.
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Logins Line Chart.
    var dailyLoginsCtx = document.getElementById('dailyLoginsChart').getContext('2d');
    new Chart(dailyLoginsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dailyLogins['labels']); ?>,
            datasets: [
                {
                    label: '<?php echo get_string('logins', 'local_report_platform_usage'); ?>',
                    data: <?php echo json_encode($dailyLogins['logins']); ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: '<?php echo get_string('uniqueusers', 'local_report_platform_usage'); ?>',
                    data: <?php echo json_encode($dailyLogins['unique_users']); ?>,
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
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // User Activity Doughnut Chart.
    var userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
    new Chart(userActivityCtx, {
        type: 'doughnut',
        data: {
            labels: [
                '<?php echo get_string('activeusers', 'local_report_platform_usage'); ?>',
                '<?php echo get_string('inactiveusers', 'local_report_platform_usage'); ?>'
            ],
            datasets: [{
                data: [<?php echo $userSummary['active']; ?>, <?php echo $userSummary['inactive']; ?>],
                backgroundColor: ['#28a745', '#dc3545'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Course Access Trends Line Chart.
    var courseAccessCtx = document.getElementById('courseAccessChart').getContext('2d');
    new Chart(courseAccessCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($courseAccessTrends['labels']); ?>,
            datasets: [{
                label: '<?php echo get_string('courseaccesses', 'local_report_platform_usage'); ?>',
                data: <?php echo json_encode($courseAccessTrends['data']); ?>,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Top Courses Bar Chart.
    var topCoursesCtx = document.getElementById('topCoursesChart').getContext('2d');
    new Chart(topCoursesCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_map(function($c) {
                return strlen($c->shortname) > 15 ? substr($c->shortname, 0, 12) . '...' : $c->shortname;
            }, array_values($topCourses))); ?>,
            datasets: [{
                label: '<?php echo get_string('courseaccesses', 'local_report_platform_usage'); ?>',
                data: <?php echo json_encode(array_map(function($c) { return $c->access_count; }, array_values($topCourses))); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)',
                    'rgba(83, 102, 255, 0.7)',
                    'rgba(255, 99, 255, 0.7)',
                    'rgba(99, 255, 132, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
<?php

echo $OUTPUT->footer();
