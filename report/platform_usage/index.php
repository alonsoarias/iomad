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

// Get parameters.
$courseid = optional_param('courseid', 0, PARAM_INT);
$companyid = optional_param('companyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', strtotime('-30 days midnight'), PARAM_INT);
$dateto = optional_param('dateto', time(), PARAM_INT);

// Determine context based on course ID.
if ($courseid > 0) {
    // Course context - require course login.
    $course = get_course($courseid);
    require_login($course);
    $context = context_course::instance($courseid);
    $incoursecontext = true;
} else {
    // System context - require login.
    require_login();
    $context = context_system::instance();
    $incoursecontext = false;
    $course = null;
}

// Check capability.
require_capability('report/platform_usage:view', $context);

// Page setup.
$PAGE->set_context($context);
$pageurl = new moodle_url('/report/platform_usage/index.php');
if ($courseid > 0) {
    $pageurl->param('courseid', $courseid);
}
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('report');

// Set page title based on context.
if ($incoursecontext) {
    $pagetitle = get_string('coursereport', 'report_platform_usage') . ': ' . format_string($course->shortname);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($pagetitle);
    $PAGE->navbar->add(get_string('pluginname', 'report_platform_usage'));
} else {
    $PAGE->set_title(get_string('pluginname', 'report_platform_usage'));
    $PAGE->set_heading(get_string('pluginname', 'report_platform_usage'));
}

// Add CSS.
$PAGE->requires->css('/report/platform_usage/styles.css');

// Create report instance with course ID.
$report = new \report_platform_usage\report($companyid, $datefrom, $dateto, true, $courseid);

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
$dailyUsers = $report->get_daily_users(10);
$topDedication = $report->get_top_courses_dedication(10);

// Get course-specific statistics if in course context.
$courseStats = $incoursecontext ? $report->get_course_statistics() : [];

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
    'dailyusers' => get_string('dailyusers', 'report_platform_usage'),
    'dedicationpercent' => get_string('dedicationpercent', 'report_platform_usage'),
];

// Tooltip strings.
$tooltips = [
    'platformaccess' => get_string('tooltip_platformaccess', 'report_platform_usage'),
    'loginstoday' => get_string('tooltip_loginstoday', 'report_platform_usage'),
    'loginsweek' => get_string('tooltip_loginsweek', 'report_platform_usage'),
    'loginsmonth' => get_string('tooltip_loginsmonth', 'report_platform_usage'),
    'uniqueusers' => get_string('tooltip_uniqueusers', 'report_platform_usage'),
    'usersummary' => get_string('tooltip_usersummary', 'report_platform_usage'),
    'totalusers' => get_string('tooltip_totalusers', 'report_platform_usage'),
    'activeusers' => get_string('tooltip_activeusers', 'report_platform_usage'),
    'inactiveusers' => get_string('tooltip_inactiveusers', 'report_platform_usage'),
    'dashboardusers' => get_string('tooltip_dashboardusers', 'report_platform_usage'),
    'completions' => get_string('tooltip_completions', 'report_platform_usage'),
    'completionstoday' => get_string('tooltip_completionstoday', 'report_platform_usage'),
    'completionsweek' => get_string('tooltip_completionsweek', 'report_platform_usage'),
    'completionsmonth' => get_string('tooltip_completionsmonth', 'report_platform_usage'),
    'totalcompletions' => get_string('tooltip_totalcompletions', 'report_platform_usage'),
    'dailyusers' => get_string('tooltip_dailyusers', 'report_platform_usage'),
    'avgdaily' => get_string('tooltip_avgdaily', 'report_platform_usage'),
    'maxdaily' => get_string('tooltip_maxdaily', 'report_platform_usage'),
    'dailylogins' => get_string('tooltip_dailylogins', 'report_platform_usage'),
    'courseaccess' => get_string('tooltip_courseaccess', 'report_platform_usage'),
    'courseaccesses' => get_string('tooltip_courseaccesses', 'report_platform_usage'),
    'enrolledusers' => get_string('tooltip_enrolledusers', 'report_platform_usage'),
    'courseactiveusers' => get_string('tooltip_courseactiveusers', 'report_platform_usage'),
    'courseinactiveusers' => get_string('tooltip_courseinactiveusers', 'report_platform_usage'),
    'coursecompletions' => get_string('tooltip_coursecompletions', 'report_platform_usage'),
    'totaldedication' => get_string('tooltip_totaldedication', 'report_platform_usage'),
    'avgdedication' => get_string('tooltip_avgdedication', 'report_platform_usage'),
    'topcourses' => get_string('tooltip_topcourses', 'report_platform_usage'),
    'topactivities' => get_string('tooltip_topactivities', 'report_platform_usage'),
    'completiontrends' => get_string('tooltip_completiontrends', 'report_platform_usage'),
    'dailyuserstable' => get_string('tooltip_dailyuserstable', 'report_platform_usage'),
    'dedicationchart' => get_string('tooltip_dedicationchart', 'report_platform_usage'),
];

// Output header.
echo $OUTPUT->header();

// Start report wrapper with custom class.
echo '<div class="report-platform-usage">';

// Show appropriate heading based on context.
if ($incoursecontext) {
    echo $OUTPUT->heading(get_string('coursereport', 'report_platform_usage'));
    echo '<p class="lead text-muted mb-4">' . get_string('coursereport_desc', 'report_platform_usage') . '</p>';
} else {
    echo $OUTPUT->heading(get_string('platformusagereport', 'report_platform_usage'));
}

// Company filter form (only show in system context).
if (!$incoursecontext) {
    echo '<div class="card mb-4 filter-section">';
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
        echo '<a href="' . $exporturl->out() . '&companyid=' . $companyid . '&type=summary&format=excel" id="export-excel" class="btn btn-success btn-export mb-2 mr-2">';
        echo '<i class="fa fa-download"></i> ' . get_string('exportexcel', 'report_platform_usage');
        echo '</a>';
        echo '<a href="' . $exporturl->out() . '&companyid=' . $companyid . '&type=summary&format=csv" id="export-csv" class="btn btn-secondary btn-export mb-2">';
        echo '<i class="fa fa-file-text"></i> ' . get_string('exportcsv', 'report_platform_usage');
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
} else {
    // Course context - show course info, date filters, and export buttons.
    echo '<div class="card mb-4 bg-light">';
    echo '<div class="card-body">';
    echo '<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">';
    echo '<div>';
    echo '<h5 class="mb-1"><i class="fa fa-book mr-2"></i>' . format_string($course->fullname) . '</h5>';
    echo '<small class="text-muted">' . format_string($course->shortname) . '</small>';
    echo '</div>';

    // Export buttons for course context.
    if (has_capability('report/platform_usage:export', $context)) {
        $exporturl = new moodle_url('/report/platform_usage/export.php', [
            'courseid' => $courseid,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
            'sesskey' => sesskey(),
        ]);
        echo '<div>';
        echo '<a href="' . $exporturl->out() . '&type=summary&format=excel" id="export-excel-course" class="btn btn-success btn-export mr-2">';
        echo '<i class="fa fa-download"></i> ' . get_string('exportexcel', 'report_platform_usage');
        echo '</a>';
        echo '<a href="' . $exporturl->out() . '&type=summary&format=csv" id="export-csv-course" class="btn btn-secondary btn-export">';
        echo '<i class="fa fa-file-text"></i> ' . get_string('exportcsv', 'report_platform_usage');
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';

    // Date filter row for course context.
    echo '<div class="d-flex flex-wrap align-items-center">';
    echo '<div class="form-group mr-3 mb-2">';
    echo '<label for="course-datefrom" class="mr-2">' . get_string('datefrom', 'report_platform_usage') . ':</label>';
    echo '<input type="date" id="course-datefrom" class="form-control" value="' . date('Y-m-d', $datefrom) . '">';
    echo '</div>';
    echo '<div class="form-group mr-3 mb-2">';
    echo '<label for="course-dateto" class="mr-2">' . get_string('dateto', 'report_platform_usage') . ':</label>';
    echo '<input type="date" id="course-dateto" class="form-control" value="' . date('Y-m-d', $dateto) . '">';
    echo '</div>';
    echo '<div class="form-group mr-3 mb-2">';
    echo '<button type="button" id="apply-course-filter" class="btn btn-primary">';
    echo '<i class="fa fa-filter"></i> ' . get_string('filter', 'report_platform_usage');
    echo '</button>';
    echo '</div>';
    echo '<div id="course-loading-indicator" class="mb-2 mr-3" style="display: none;">';
    echo '<span class="spinner-border spinner-border-sm text-primary" role="status"></span>';
    echo ' <span class="text-muted">' . get_string('loadingreport', 'report_platform_usage') . '</span>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '</div>';
}

// Consolidated Summary Cards - 4 key metrics (different for course context).
echo '<div class="row mb-4">';

if ($incoursecontext && !empty($courseStats)) {
    // Course Context: Show course-specific metrics.

    // Course Access card.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-primary">';
    echo '<div class="card-header bg-primary text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-sign-in mr-2"></i>' . get_string('courseaccess', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['courseaccess'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-3">';
    echo '<span class="text-muted" data-toggle="tooltip" title="' . $tooltips['courseaccesses'] . '">' . get_string('courseaccesses', 'report_platform_usage') . ' <i class="fa fa-info-circle small"></i></span>';
    echo '<span class="badge badge-primary badge-lg" id="course-accesses">' . number_format($courseStats['accesses']) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    echo '<div class="text-center">';
    echo '<small class="text-muted" data-toggle="tooltip" title="' . $tooltips['totaldedication'] . '">' . get_string('totaldedication', 'report_platform_usage') . ' <i class="fa fa-info-circle"></i></small>';
    echo '<h4 class="text-primary mb-0">' . $courseStats['total_dedication_formatted'] . '</h4>';
    echo '<small class="text-muted" data-toggle="tooltip" title="' . $tooltips['avgdedication'] . '">' . get_string('avgaccessperuser', 'report_platform_usage') . ': ' . $courseStats['avg_dedication_formatted'] . '</small>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Course Users card.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-success">';
    echo '<div class="card-header bg-success text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-users mr-2"></i>' . get_string('courseenrolledusers', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['enrolledusers'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['enrolledusers'] . '">' . get_string('enrolledusers', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-secondary">' . number_format($courseStats['enrolled_users']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-success small" data-toggle="tooltip" title="' . $tooltips['courseactiveusers'] . '">' . get_string('active', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-success">' . number_format($courseStats['active_users']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<span class="text-danger small" data-toggle="tooltip" title="' . $tooltips['courseinactiveusers'] . '">' . get_string('inactive', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-danger">' . number_format($courseStats['inactive_users']) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    $activePercent = $courseStats['enrolled_users'] > 0 ? round(($courseStats['active_users'] / $courseStats['enrolled_users']) * 100) : 0;
    echo '<div class="progress" style="height: 20px;">';
    echo '<div class="progress-bar bg-success" role="progressbar" style="width: ' . $activePercent . '%" aria-valuenow="' . $activePercent . '" aria-valuemin="0" aria-valuemax="100">' . $activePercent . '% ' . get_string('active', 'report_platform_usage') . '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Course Completions card.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-info">';
    echo '<div class="card-header bg-info text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-graduation-cap mr-2"></i>' . get_string('coursecompletions', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['coursecompletions'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="text-center mb-3">';
    echo '<h2 class="text-info mb-0">' . number_format($courseStats['completions']) . '</h2>';
    echo '<small class="text-muted">' . get_string('totalcompletions', 'report_platform_usage') . '</small>';
    echo '</div>';
    echo '<hr class="my-2">';
    $completionPercent = $courseStats['enrolled_users'] > 0 ? round(($courseStats['completions'] / $courseStats['enrolled_users']) * 100) : 0;
    echo '<div class="progress" style="height: 20px;">';
    echo '<div class="progress-bar bg-info" role="progressbar" style="width: ' . $completionPercent . '%" aria-valuenow="' . $completionPercent . '" aria-valuemin="0" aria-valuemax="100">' . $completionPercent . '%</div>';
    echo '</div>';
    echo '<small class="text-muted text-center d-block mt-1">' . get_string('completions', 'report_platform_usage') . ' / ' . get_string('enrolledusers', 'report_platform_usage') . '</small>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Course Activity card (top activities preview).
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-warning">';
    echo '<div class="card-header bg-warning text-dark py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-tasks mr-2"></i>' . get_string('activities', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['topactivities'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body p-2">';
    if (empty($topActivities)) {
        echo '<p class="text-muted text-center">' . get_string('nodata', 'report_platform_usage') . '</p>';
    } else {
        echo '<div class="list-group list-group-flush small">';
        $actCount = 0;
        foreach ($topActivities as $activity) {
            if ($actCount >= 4) break;
            echo '<div class="list-group-item d-flex justify-content-between align-items-center px-1 py-1">';
            echo '<span class="text-truncate" style="max-width: 140px;" title="' . format_string($activity->name) . '">' . format_string(mb_strimwidth($activity->name, 0, 20, '...')) . '</span>';
            echo '<span class="badge badge-warning">' . number_format($activity->access_count) . '</span>';
            echo '</div>';
            $actCount++;
        }
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

} else {
    // System Context: Show platform-wide metrics.

    // Platform Access Summary card - combines logins.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-primary">';
    echo '<div class="card-header bg-primary text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-sign-in mr-2"></i>' . get_string('platformaccess', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['platformaccess'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['loginstoday'] . '">' . get_string('today', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-primary" id="logins-today">' . number_format($loginSummary['logins_today']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['loginsweek'] . '">' . get_string('lastweek', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-info" id="logins-week">' . number_format($loginSummary['logins_week']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['loginsmonth'] . '">' . get_string('lastmonth', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-secondary" id="logins-month">' . number_format($loginSummary['logins_month']) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    echo '<div class="text-center">';
    echo '<small class="text-muted" data-toggle="tooltip" title="' . $tooltips['uniqueusers'] . '">' . get_string('uniqueusersmonth', 'report_platform_usage') . ' <i class="fa fa-info-circle"></i></small>';
    echo '<h4 class="text-primary mb-0" id="unique-month">' . number_format($loginSummary['unique_users_month']) . '</h4>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // User Activity Summary card.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-success">';
    echo '<div class="card-header bg-success text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-users mr-2"></i>' . get_string('usersummary', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['usersummary'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['totalusers'] . '">' . get_string('totalusers', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-secondary" id="total-users">' . number_format($userSummary['total']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-success small" data-toggle="tooltip" title="' . $tooltips['activeusers'] . '">' . get_string('active', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-success" id="active-users">' . number_format($userSummary['active']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<span class="text-danger small" data-toggle="tooltip" title="' . $tooltips['inactiveusers'] . '">' . get_string('inactive', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-danger" id="inactive-users">' . number_format($userSummary['inactive']) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    echo '<div class="text-center">';
    echo '<small class="text-muted" data-toggle="tooltip" title="' . $tooltips['dashboardusers'] . '">' . get_string('dashboardusers', 'report_platform_usage') . ' <i class="fa fa-info-circle"></i></small>';
    echo '<h4 class="text-success mb-0" id="dashboard-month">' . number_format($dashboardAccess['month']) . '</h4>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Course Completions Summary card.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-info">';
    echo '<div class="card-header bg-info text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-graduation-cap mr-2"></i>' . get_string('completions', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['completions'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['completionstoday'] . '">' . get_string('today', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-info" id="completions-today">' . number_format($completionsSummary['completions_today']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['completionsweek'] . '">' . get_string('lastweek', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-primary" id="completions-week">' . number_format($completionsSummary['completions_week']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<span class="text-muted small" data-toggle="tooltip" title="' . $tooltips['completionsmonth'] . '">' . get_string('lastmonth', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-secondary" id="completions-month">' . number_format($completionsSummary['completions_month']) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    echo '<div class="text-center">';
    echo '<small class="text-muted" data-toggle="tooltip" title="' . $tooltips['totalcompletions'] . '">' . get_string('totalcompletions', 'report_platform_usage') . ' <i class="fa fa-info-circle"></i></small>';
    echo '<h4 class="text-info mb-0" id="total-completions">' . number_format($completionsSummary['total_completions']) . '</h4>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Daily Users Summary card.
    $maxDailyUsers = !empty($dailyUsers['data']) ? max($dailyUsers['data']) : 0;
    $avgDailyUsers = !empty($dailyUsers['data']) ? round(array_sum($dailyUsers['data']) / count($dailyUsers['data'])) : 0;
    $todayUsers = !empty($dailyUsers['data']) ? end($dailyUsers['data']) : 0;
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-warning">';
    echo '<div class="card-header bg-warning text-dark py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-calendar mr-2"></i>' . get_string('dailyusers', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['dailyusers'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small">' . get_string('today', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-warning" id="daily-today">' . number_format($todayUsers) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small">' . get_string('average', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-secondary" id="daily-avg">' . number_format($avgDailyUsers) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<span class="text-muted small">' . get_string('maximum', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-dark" id="daily-max">' . number_format($maxDailyUsers) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    echo '<div class="text-center">';
    echo '<small class="text-muted">' . get_string('uniqueusersweek', 'report_platform_usage') . '</small>';
    echo '<h4 class="text-warning mb-0" id="unique-week">' . number_format($loginSummary['unique_users_week']) . '</h4>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

echo '</div>';

// Section 1: Platform Access Trends (Login + Daily Users combined).
echo '<div class="row mb-4">';

// Combined Login and Users Trends Chart.
echo '<div class="col-lg-8 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
echo '<h5 class="mb-0"><i class="fa fa-line-chart mr-2"></i>' . get_string('logintrends', 'report_platform_usage') . '</h5>';
echo '<small class="text-muted">' . get_string('logintrends_desc', 'report_platform_usage') . '</small>';
echo '</div>';
echo '<div class="card-body">';
echo '<canvas id="dailyLoginsChart" height="280"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

// User Activity Summary (Pie + Table combined).
echo '<div class="col-lg-4 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
echo '<h5 class="mb-0"><i class="fa fa-pie-chart mr-2"></i>' . get_string('usersbyactivity', 'report_platform_usage') . '</h5>';
echo '<small class="text-muted">' . get_string('usersbyactivity_desc', 'report_platform_usage') . '</small>';
echo '</div>';
echo '<div class="card-body">';
echo '<canvas id="userActivityChart" height="200"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Section 2: Course Analysis (Access + Dedication combined).
echo '<div class="row mb-4">';

// Course Access and Completion Trends (Combined chart).
echo '<div class="col-lg-6 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
echo '<h5 class="mb-0"><i class="fa fa-book mr-2"></i>' . get_string('courseaccesstrends', 'report_platform_usage') . '</h5>';
echo '<small class="text-muted">' . get_string('coursetrends_desc', 'report_platform_usage') . '</small>';
echo '</div>';
echo '<div class="card-body">';
echo '<canvas id="courseAccessChart" height="260"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

// Course Dedication Chart.
echo '<div class="col-lg-6 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
echo '<h5 class="mb-0"><i class="fa fa-clock-o mr-2"></i>' . get_string('topdedication', 'report_platform_usage') . '</h5>';
echo '<small class="text-muted">' . get_string('dedication_desc', 'report_platform_usage') . '</small>';
echo '</div>';
echo '<div class="card-body">';
echo '<canvas id="dedicationChart" height="260"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Section 3: Combined Data Tables.
echo '<div class="row mb-4">';

if (!$incoursecontext) {
    // System context: Show Top Courses Table with Dedication.
    echo '<div class="col-lg-6 mb-3">';
    echo '<div class="card h-100">';
    echo '<div class="card-header bg-light">';
    echo '<h5 class="mb-0"><i class="fa fa-trophy mr-2"></i>' . get_string('topcourses', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('topcourses_desc', 'report_platform_usage') . '</small>';
    echo '</div>';
    echo '<div class="card-body" id="top-courses-table">';
    if (empty($topCourses)) {
        echo '<p class="text-muted">' . get_string('nodata', 'report_platform_usage') . '</p>';
    } else {
        // Merge course access data with dedication data.
        $courseMap = [];
        foreach ($topDedication as $ded) {
            $courseMap[$ded['id']] = $ded;
        }
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-sm">';
        echo '<thead class="thead-light"><tr>';
        echo '<th>' . get_string('coursename', 'report_platform_usage') . '</th>';
        echo '<th class="text-right">' . get_string('courseaccesses', 'report_platform_usage') . '</th>';
        echo '<th class="text-right">' . get_string('uniqueusers', 'report_platform_usage') . '</th>';
        echo '<th class="text-right">' . get_string('totaldedication', 'report_platform_usage') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        foreach ($topCourses as $courseitem) {
            $dedication = isset($courseMap[$courseitem->id]) ? $courseMap[$courseitem->id]['total_dedication_formatted'] : '-';
            echo '<tr>';
            echo '<td><span title="' . format_string($courseitem->fullname) . '">' . format_string(mb_strimwidth($courseitem->fullname, 0, 35, '...')) . '</span></td>';
            echo '<td class="text-right">' . number_format($courseitem->access_count) . '</td>';
            echo '<td class="text-right">' . number_format($courseitem->unique_users) . '</td>';
            echo '<td class="text-right"><span class="badge badge-info">' . $dedication . '</span></td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

// Top Activities Table (full width in course context).
$activityColClass = $incoursecontext ? 'col-lg-12' : 'col-lg-6';
echo '<div class="' . $activityColClass . ' mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
echo '<h5 class="mb-0"><i class="fa fa-tasks mr-2"></i>' . get_string('topactivities', 'report_platform_usage') . '</h5>';
echo '<small class="text-muted">' . get_string('topactivities_desc', 'report_platform_usage') . '</small>';
echo '</div>';
echo '<div class="card-body" id="top-activities-table">';
if (empty($topActivities)) {
    echo '<p class="text-muted">' . get_string('nodata', 'report_platform_usage') . '</p>';
} else {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-sm">';
    echo '<thead class="thead-light"><tr>';
    echo '<th>' . get_string('activityname', 'report_platform_usage') . '</th>';
    echo '<th>' . get_string('activitytype', 'report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('activityaccesses', 'report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('uniqueusers', 'report_platform_usage') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($topActivities as $activity) {
        echo '<tr>';
        echo '<td><span title="' . format_string($activity->name) . '">' . format_string(mb_strimwidth($activity->name, 0, 50, '...')) . '</span></td>';
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

// Section 4: Completion and Daily Trends.
echo '<div class="row mb-4">';

// Completion Trends Chart.
echo '<div class="col-lg-8 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
echo '<h5 class="mb-0"><i class="fa fa-check-circle mr-2"></i>' . get_string('completiontrends', 'report_platform_usage') . '</h5>';
echo '<small class="text-muted">' . get_string('completiontrends_desc', 'report_platform_usage') . '</small>';
echo '</div>';
echo '<div class="card-body">';
echo '<canvas id="completionTrendsChart" height="180"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

// Daily Users History Table.
echo '<div class="col-lg-4 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
echo '<h5 class="mb-0"><i class="fa fa-calendar-check-o mr-2"></i>' . get_string('dailyuserstable', 'report_platform_usage') . '</h5>';
echo '</div>';
echo '<div class="card-body p-0" id="daily-users-table">';
if (empty($dailyUsers['records'])) {
    echo '<p class="text-muted p-3">' . get_string('nodata', 'report_platform_usage') . '</p>';
} else {
    echo '<div class="table-responsive" style="max-height: 250px; overflow-y: auto;">';
    echo '<table class="table table-striped table-sm mb-0">';
    echo '<thead class="thead-light" style="position: sticky; top: 0;"><tr>';
    echo '<th>' . get_string('date', 'report_platform_usage') . '</th>';
    echo '<th class="text-right">' . get_string('uniqueusers', 'report_platform_usage') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($dailyUsers['records'] as $record) {
        echo '<tr>';
        echo '<td>' . $record['fecha_formateada'] . '</td>';
        echo '<td class="text-right"><span class="badge badge-primary">' . number_format($record['cantidad_usuarios']) . '</span></td>';
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

    // Context flag.
    var inCourseContext = <?php echo $incoursecontext ? 'true' : 'false'; ?>;
    var courseId = <?php echo (int)$courseid; ?>;

    // Initial data.
    var currentData = {
        login_summary: <?php echo json_encode($loginSummary); ?>,
        user_summary: <?php echo json_encode($userSummary); ?>,
        course_stats: <?php echo json_encode($courseStats); ?>,
        daily_logins: <?php echo json_encode($dailyLogins); ?>,
        course_access_trends: <?php echo json_encode($courseAccessTrends); ?>,
        top_courses: <?php echo json_encode(array_values($topCourses)); ?>,
        top_activities: <?php echo json_encode($topActivities); ?>,
        completions_summary: <?php echo json_encode($completionsSummary); ?>,
        dashboard_access: <?php echo json_encode($dashboardAccess); ?>,
        completion_trends: <?php echo json_encode($completionTrends); ?>,
        daily_users: <?php echo json_encode($dailyUsers); ?>,
        top_dedication: <?php echo json_encode($topDedication); ?>
    };

    // Chart instances.
    var charts = {};

    // Initialize charts.
    initCharts(currentData);

    // Company filter change event - automatic AJAX loading (only in system context).
    var companyFilter = document.getElementById('companyid');
    if (companyFilter) {
        companyFilter.addEventListener('change', function() {
            var companyId = this.value;
            loadReportData(companyId);
            updateExportLinks(companyId);
        });
    }

    // Course context date filter event.
    var courseFilterBtn = document.getElementById('apply-course-filter');
    if (courseFilterBtn) {
        courseFilterBtn.addEventListener('click', function() {
            var datefrom = document.getElementById('course-datefrom').value;
            var dateto = document.getElementById('course-dateto').value;
            loadCourseReportData(datefrom, dateto);
            updateCourseExportLinks(datefrom, dateto);
        });
    }

    /**
     * Initialize all charts.
     */
    function initCharts(data) {
        // Daily Logins Line Chart (with unique users).
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
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: STRINGS.uniqueusers,
                        data: data.daily_logins.unique_users,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // User Activity Doughnut Chart.
        // In course context, use course_stats; in platform context, use user_summary.
        var userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
        var activeUsers, inactiveUsers;
        if (inCourseContext && data.course_stats) {
            activeUsers = data.course_stats.active_users || 0;
            inactiveUsers = data.course_stats.inactive_users || 0;
        } else {
            activeUsers = data.user_summary.active || 0;
            inactiveUsers = data.user_summary.inactive || 0;
        }
        charts.userActivity = new Chart(userActivityCtx, {
            type: 'doughnut',
            data: {
                labels: [STRINGS.activeusers, STRINGS.inactiveusers],
                datasets: [{
                    data: [activeUsers, inactiveUsers],
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
                    backgroundColor: 'rgba(255, 159, 64, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
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
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Dedication Chart (horizontal bar).
        if (data.top_dedication && data.top_dedication.length > 0) {
            var dedicationCtx = document.getElementById('dedicationChart').getContext('2d');
            charts.dedication = new Chart(dedicationCtx, {
                type: 'bar',
                data: {
                    labels: data.top_dedication.map(function(c) {
                        return c.shortname && c.shortname.length > 18 ? c.shortname.substring(0, 15) + '...' : (c.shortname || c.fullname.substring(0, 15));
                    }),
                    datasets: [{
                        label: STRINGS.dedicationpercent,
                        data: data.top_dedication.map(function(c) { return c.dedication_percent; }),
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) { return value + '%'; }
                            }
                        }
                    }
                }
            });
        }
    }

    /**
     * Update all charts with new data.
     */
    function updateCharts(data) {
        // Update Daily Logins chart.
        if (charts.dailyLogins) {
            charts.dailyLogins.data.labels = data.daily_logins.labels;
            charts.dailyLogins.data.datasets[0].data = data.daily_logins.logins;
            charts.dailyLogins.data.datasets[1].data = data.daily_logins.unique_users;
            charts.dailyLogins.update();
        }

        // Update User Activity chart.
        if (charts.userActivity) {
            var activeUsers, inactiveUsers;
            if (inCourseContext && data.course_stats) {
                activeUsers = data.course_stats.active_users || 0;
                inactiveUsers = data.course_stats.inactive_users || 0;
            } else {
                activeUsers = data.user_summary.active || 0;
                inactiveUsers = data.user_summary.inactive || 0;
            }
            charts.userActivity.data.datasets[0].data = [activeUsers, inactiveUsers];
            charts.userActivity.update();
        }

        // Update Course Access Trends chart.
        if (charts.courseAccess) {
            charts.courseAccess.data.labels = data.course_access_trends.labels;
            charts.courseAccess.data.datasets[0].data = data.course_access_trends.data;
            charts.courseAccess.update();
        }

        // Update Completion Trends chart.
        if (charts.completionTrends && data.completion_trends) {
            charts.completionTrends.data.labels = data.completion_trends.labels;
            charts.completionTrends.data.datasets[0].data = data.completion_trends.data;
            charts.completionTrends.update();
        }

        // Update Dedication chart.
        if (charts.dedication && data.top_dedication) {
            charts.dedication.data.labels = data.top_dedication.map(function(c) {
                return c.shortname && c.shortname.length > 18 ? c.shortname.substring(0, 15) + '...' : (c.shortname || c.fullname.substring(0, 15));
            });
            charts.dedication.data.datasets[0].data = data.top_dedication.map(function(c) { return c.dedication_percent; });
            charts.dedication.update();
        }
    }

    /**
     * Update summary cards.
     */
    function updateSummaryCards(data) {
        // Platform Access card.
        updateElement('logins-today', numberFormat(data.login_summary.logins_today));
        updateElement('logins-week', numberFormat(data.login_summary.logins_week));
        updateElement('logins-month', numberFormat(data.login_summary.logins_month));
        updateElement('unique-month', numberFormat(data.login_summary.unique_users_month));
        updateElement('unique-week', numberFormat(data.login_summary.unique_users_week));

        // User Summary card.
        if (data.user_summary) {
            updateElement('total-users', numberFormat(data.user_summary.total));
            updateElement('active-users', numberFormat(data.user_summary.active));
            updateElement('inactive-users', numberFormat(data.user_summary.inactive));
        }

        // Completions card.
        if (data.completions_summary) {
            updateElement('completions-today', numberFormat(data.completions_summary.completions_today));
            updateElement('completions-week', numberFormat(data.completions_summary.completions_week));
            updateElement('completions-month', numberFormat(data.completions_summary.completions_month));
            updateElement('total-completions', numberFormat(data.completions_summary.total_completions));
        }

        // Dashboard card.
        if (data.dashboard_access) {
            updateElement('dashboard-month', numberFormat(data.dashboard_access.month));
        }

        // Daily Users card.
        if (data.daily_users && data.daily_users.data) {
            var maxDaily = Math.max.apply(null, data.daily_users.data) || 0;
            var avgDaily = data.daily_users.data.length > 0 ? Math.round(data.daily_users.data.reduce(function(a,b) { return a+b; }, 0) / data.daily_users.data.length) : 0;
            var todayDaily = data.daily_users.data[data.daily_users.data.length - 1] || 0;
            updateElement('daily-today', numberFormat(todayDaily));
            updateElement('daily-avg', numberFormat(avgDaily));
            updateElement('daily-max', numberFormat(maxDaily));
        }
    }

    /**
     * Safely update element text content.
     */
    function updateElement(id, value) {
        var el = document.getElementById(id);
        if (el) {
            el.textContent = value;
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
     * Load course report data via AJAX.
     */
    function loadCourseReportData(datefrom, dateto) {
        var loading = document.getElementById('course-loading-indicator');
        if (loading) {
            loading.style.display = 'inline-block';
        }

        var datefromTs = new Date(datefrom).getTime() / 1000;
        var datetoTs = new Date(dateto).getTime() / 1000 + 86399; // End of day

        var url = AJAX_URL + '?courseid=' + courseId + '&datefrom=' + Math.floor(datefromTs) + '&dateto=' + Math.floor(datetoTs);

        fetch(url)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                currentData = data;
                updateSummaryCards(data);
                updateCharts(data);
                updateTables(data);
                updateCourseCards(data);
                if (loading) {
                    loading.style.display = 'none';
                }
            })
            .catch(function(error) {
                console.error('Error loading course report data:', error);
                if (loading) {
                    loading.style.display = 'none';
                }
            });
    }

    /**
     * Update course-specific summary cards.
     */
    function updateCourseCards(data) {
        if (!inCourseContext || !data.course_stats) {
            return;
        }

        // Update course accesses.
        updateElement('course-accesses', numberFormat(data.course_stats.accesses || 0));
    }

    /**
     * Update course export links with new date range.
     */
    function updateCourseExportLinks(datefrom, dateto) {
        var excelLink = document.getElementById('export-excel-course');
        var csvLink = document.getElementById('export-csv-course');

        var datefromTs = new Date(datefrom).getTime() / 1000;
        var datetoTs = new Date(dateto).getTime() / 1000 + 86399;

        if (excelLink) {
            var url = excelLink.href
                .replace(/datefrom=\d+/, 'datefrom=' + Math.floor(datefromTs))
                .replace(/dateto=\d+/, 'dateto=' + Math.floor(datetoTs));
            excelLink.href = url;
        }
        if (csvLink) {
            var url = csvLink.href
                .replace(/datefrom=\d+/, 'datefrom=' + Math.floor(datefromTs))
                .replace(/dateto=\d+/, 'dateto=' + Math.floor(datetoTs));
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

// Close report wrapper div.
echo '</div>';

echo $OUTPUT->footer();
