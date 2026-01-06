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

// Check if IOMAD is installed.
$isiomad = \report_platform_usage\report::is_iomad_installed();

// Reset company filter if IOMAD is not installed.
if (!$isiomad) {
    $companyid = 0;
}

// Create report instance with course ID.
$report = new \report_platform_usage\report($companyid, $datefrom, $dateto, true, $courseid);

// Get companies for filter (only if IOMAD is installed).
$companies = $isiomad ? \report_platform_usage\report::get_companies() : [];

// Get initial report data.
$loginSummary = $report->get_login_summary();
$userSummary = $report->get_user_activity_summary();
$dailyLogins = $report->get_daily_logins(30);
$courseAccessTrends = $report->get_course_access_trends(30);
$topCourses = $report->get_top_courses(10);
$topActivities = $report->get_top_activities(10);
$completionsSummary = $report->get_course_completions_summary();
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

// Filter form (only show in system context).
if (!$incoursecontext) {
    echo '<div class="card mb-4 filter-section">';
    echo '<div class="card-body">';
    echo '<div class="d-flex flex-wrap align-items-center">';

    // Company filter (only show if IOMAD is installed).
    if ($isiomad && !empty($companies)) {
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
    }

    // Date filters for global context.
    echo '<div class="filter-section">';
    echo '<div class="form-group">';
    echo '<label for="global-datefrom">' . get_string('datefrom', 'report_platform_usage') . '</label>';
    echo '<input type="date" id="global-datefrom" class="form-control" value="' . date('Y-m-d', $datefrom) . '">';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label for="global-dateto">' . get_string('dateto', 'report_platform_usage') . '</label>';
    echo '<input type="date" id="global-dateto" class="form-control" value="' . date('Y-m-d', $dateto) . '">';
    echo '</div>';
    echo '<button type="button" id="apply-global-filter" class="btn btn-filter">';
    echo '<i class="fa fa-filter"></i> ' . get_string('filter', 'report_platform_usage');
    echo '</button>';

    // Loading indicator.
    echo '<div id="loading-indicator" style="display: none;">';
    echo '<span class="spinner-border spinner-border-sm text-primary" role="status"></span>';
    echo ' <span class="text-muted">' . get_string('loadingreport', 'report_platform_usage') . '</span>';
    echo '</div>';
    echo '</div>';

    // Export buttons.
    if (has_capability('report/platform_usage:export', $context)) {
        $exporturl = new moodle_url('/report/platform_usage/export.php', [
            'datefrom' => $datefrom,
            'dateto' => $dateto,
            'sesskey' => sesskey(),
        ]);
        echo '<div class="export-buttons ml-auto">';
        echo '<a href="' . $exporturl->out() . '&companyid=' . $companyid . '&type=summary&format=excel" id="export-excel" class="btn-export btn-export-excel">';
        echo '<i class="fa fa-file-excel-o"></i> ' . get_string('exportexcel', 'report_platform_usage');
        echo '</a>';
        echo '<a href="' . $exporturl->out() . '&companyid=' . $companyid . '&type=summary&format=pdf" id="export-pdf" class="btn-export btn-export-pdf">';
        echo '<i class="fa fa-file-pdf-o"></i> ' . get_string('exportpdf', 'report_platform_usage');
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
        echo '<div class="export-buttons">';
        echo '<a href="' . $exporturl->out() . '&type=summary&format=excel" id="export-excel-course" class="btn-export btn-export-excel">';
        echo '<i class="fa fa-file-excel-o"></i> ' . get_string('exportexcel', 'report_platform_usage');
        echo '</a>';
        echo '<a href="' . $exporturl->out() . '&type=summary&format=pdf" id="export-pdf-course" class="btn-export btn-export-pdf">';
        echo '<i class="fa fa-file-pdf-o"></i> ' . get_string('exportpdf', 'report_platform_usage');
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';

    // Date filter row for course context.
    echo '<div class="filter-section">';
    echo '<div class="form-group">';
    echo '<label for="course-datefrom">' . get_string('datefrom', 'report_platform_usage') . '</label>';
    echo '<input type="date" id="course-datefrom" class="form-control" value="' . date('Y-m-d', $datefrom) . '">';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label for="course-dateto">' . get_string('dateto', 'report_platform_usage') . '</label>';
    echo '<input type="date" id="course-dateto" class="form-control" value="' . date('Y-m-d', $dateto) . '">';
    echo '</div>';
    echo '<button type="button" id="apply-course-filter" class="btn btn-filter">';
    echo '<i class="fa fa-filter"></i> ' . get_string('filter', 'report_platform_usage');
    echo '</button>';
    echo '<div id="course-loading-indicator" style="display: none;">';
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

    // Platform Access Summary card - uses date range totals.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-primary">';
    echo '<div class="card-header bg-primary text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-sign-in mr-2"></i>' . get_string('platformaccess', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['platformaccess'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small">' . get_string('totallogins', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-primary" id="total-logins">' . number_format($loginSummary['total_logins']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small">' . get_string('uniqueusers', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-info" id="unique-users-login">' . number_format($loginSummary['unique_users']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<span class="text-muted small">' . get_string('avgperday', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-secondary" id="avg-logins-day">' . number_format($loginSummary['avg_logins_per_day'], 1) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    echo '<div class="text-center">';
    echo '<small class="text-muted">' . get_string('avgperuser', 'report_platform_usage') . '</small>';
    echo '<h4 class="text-primary mb-0" id="avg-logins-user">' . number_format($loginSummary['avg_logins_per_user'], 1) . '</h4>';
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
    // Show active users percentage.
    $activePercent = $userSummary['total'] > 0 ? round(($userSummary['active'] / $userSummary['total']) * 100) : 0;
    echo '<div class="progress" style="height: 20px;">';
    echo '<div class="progress-bar bg-success" role="progressbar" style="width: ' . $activePercent . '%" aria-valuenow="' . $activePercent . '" aria-valuemin="0" aria-valuemax="100">' . $activePercent . '% ' . get_string('active', 'report_platform_usage') . '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Course Completions Summary card - uses date range totals.
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-info">';
    echo '<div class="card-header bg-info text-white py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-graduation-cap mr-2"></i>' . get_string('completions', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['completions'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small">' . get_string('totalcompletions', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-info" id="total-completions">' . number_format($completionsSummary['total_completions']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small">' . get_string('uniquecourses', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-primary" id="unique-courses">' . number_format($completionsSummary['unique_courses']) . '</span>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<span class="text-muted small">' . get_string('avgperday', 'report_platform_usage') . '</span>';
    echo '<span class="badge badge-secondary" id="completions-avg">' . number_format($completionsSummary['avg_per_day'], 1) . '</span>';
    echo '</div>';
    echo '<hr class="my-2">';
    // Calculate completion rate if user summary is available.
    $completionRate = $userSummary['total'] > 0 ? round(($completionsSummary['total_completions'] / $userSummary['total']) * 100, 1) : 0;
    echo '<div class="text-center">';
    echo '<small class="text-muted">' . get_string('completionrate', 'report_platform_usage') . '</small>';
    echo '<h4 class="text-info mb-0" id="completion-rate">' . $completionRate . '%</h4>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Daily Users Summary card.
    $maxDailyUsers = !empty($dailyUsers['data']) ? max($dailyUsers['data']) : 0;
    $avgDailyUsers = !empty($dailyUsers['data']) ? round(array_sum($dailyUsers['data']) / count($dailyUsers['data'])) : 0;
    $todayUsers = !empty($dailyUsers['data']) ? end($dailyUsers['data']) : 0;
    $totalDailyUsers = !empty($dailyUsers['data']) ? array_sum($dailyUsers['data']) : 0;
    echo '<div class="col-lg-3 col-md-6 mb-3">';
    echo '<div class="card h-100 border-warning">';
    echo '<div class="card-header bg-warning text-dark py-2">';
    echo '<h6 class="mb-0 d-flex justify-content-between align-items-center"><span><i class="fa fa-calendar mr-2"></i>' . get_string('dailyusers', 'report_platform_usage') . '</span><i class="fa fa-question-circle ml-2" data-toggle="tooltip" title="' . $tooltips['dailyusers'] . '"></i></h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<span class="text-muted small">' . get_string('lastday', 'report_platform_usage') . '</span>';
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
    echo '<small class="text-muted">' . get_string('totaldaysdata', 'report_platform_usage') . '</small>';
    echo '<h4 class="text-warning mb-0" id="total-daily">' . count($dailyUsers['data']) . ' ' . get_string('days', 'report_platform_usage') . '</h4>';
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
if ($incoursecontext) {
    echo '<h5 class="mb-0"><i class="fa fa-line-chart mr-2"></i>' . get_string('courseaccesstrends', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('courseaccesstrends_desc', 'report_platform_usage') . '</small>';
} else {
    echo '<h5 class="mb-0"><i class="fa fa-line-chart mr-2"></i>' . get_string('logintrends', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('logintrends_desc', 'report_platform_usage') . '</small>';
}
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
if ($incoursecontext) {
    echo '<h5 class="mb-0"><i class="fa fa-pie-chart mr-2"></i>' . get_string('courseenrolledusers', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('courseenrolledusers_desc', 'report_platform_usage') . '</small>';
} else {
    echo '<h5 class="mb-0"><i class="fa fa-pie-chart mr-2"></i>' . get_string('usersbyactivity', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('usersbyactivity_desc', 'report_platform_usage') . '</small>';
}
echo '</div>';
echo '<div class="card-body">';
echo '<canvas id="userActivityChart" height="200"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Section 2: Course Analysis (Access + Dedication combined).
echo '<div class="row mb-4">';

if (!$incoursecontext) {
    // Course Access and Completion Trends (Combined chart) - Only in global context.
    $hasCourseAccessData = !empty($courseAccessTrends['data']) && array_sum($courseAccessTrends['data']) > 0;
    echo '<div class="col-lg-6 mb-3">';
    echo '<div class="card h-100">';
    echo '<div class="card-header bg-light">';
    echo '<h5 class="mb-0"><i class="fa fa-book mr-2"></i>' . get_string('topcourseaccess', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('coursetrends_desc', 'report_platform_usage') . '</small>';
    echo '</div>';
    echo '<div class="card-body">';
    if ($hasCourseAccessData) {
        echo '<canvas id="courseAccessChart" height="260"></canvas>';
    } else {
        echo '<div class="d-flex align-items-center justify-content-center" style="height: 260px;">';
        echo '<div class="text-center text-muted">';
        echo '<i class="fa fa-info-circle fa-3x mb-3"></i>';
        echo '<p>' . get_string('nodata', 'report_platform_usage') . '</p>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Course Dedication Chart - Only in global context.
    echo '<div class="col-lg-6 mb-3">';
    echo '<div class="card h-100">';
    echo '<div class="card-header bg-light">';
    echo '<h5 class="mb-0"><i class="fa fa-clock-o mr-2"></i>' . get_string('topdedication', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('dedication_desc', 'report_platform_usage') . '</small>';
    echo '</div>';
    echo '<div class="card-body">';
    if (!empty($topDedication)) {
        echo '<canvas id="dedicationChart" height="260"></canvas>';
    } else {
        echo '<div class="d-flex align-items-center justify-content-center" style="height: 260px;">';
        echo '<div class="text-center text-muted">';
        echo '<i class="fa fa-info-circle fa-3x mb-3"></i>';
        echo '<p>' . get_string('nodata', 'report_platform_usage') . '</p>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
} else {
    // In course context, show course dedication summary.
    echo '<div class="col-lg-12 mb-3">';
    echo '<div class="card h-100">';
    echo '<div class="card-header bg-light">';
    echo '<h5 class="mb-0"><i class="fa fa-clock-o mr-2"></i>' . get_string('coursededicationsummary', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('coursededication_desc', 'report_platform_usage') . '</small>';
    echo '</div>';
    echo '<div class="card-body">';
    if (!empty($courseStats) && isset($courseStats['total_dedication_formatted'])) {
        echo '<div class="row">';
        echo '<div class="col-md-4 text-center">';
        echo '<h3 class="text-primary">' . $courseStats['total_dedication_formatted'] . '</h3>';
        echo '<p class="text-muted">' . get_string('totaldedication', 'report_platform_usage') . '</p>';
        echo '</div>';
        echo '<div class="col-md-4 text-center">';
        echo '<h3 class="text-success">' . $courseStats['avg_dedication_formatted'] . '</h3>';
        echo '<p class="text-muted">' . get_string('avgdedicationperuser', 'report_platform_usage') . '</p>';
        echo '</div>';
        echo '<div class="col-md-4 text-center">';
        echo '<h3 class="text-info">' . number_format($courseStats['enrolled_users'] ?? 0) . '</h3>';
        echo '<p class="text-muted">' . get_string('enrolledusers', 'report_platform_usage') . '</p>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="d-flex align-items-center justify-content-center" style="height: 150px;">';
        echo '<div class="text-center text-muted">';
        echo '<i class="fa fa-info-circle fa-3x mb-3"></i>';
        echo '<p>' . get_string('nodata', 'report_platform_usage') . '</p>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

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
if ($incoursecontext) {
    echo '<h5 class="mb-0"><i class="fa fa-check-circle mr-2"></i>' . get_string('coursecompletiontrends', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('coursecompletiontrends_desc', 'report_platform_usage') . '</small>';
} else {
    echo '<h5 class="mb-0"><i class="fa fa-check-circle mr-2"></i>' . get_string('completiontrends', 'report_platform_usage') . '</h5>';
    echo '<small class="text-muted">' . get_string('completiontrends_desc', 'report_platform_usage') . '</small>';
}
echo '</div>';
echo '<div class="card-body">';
// Check if there's completion data.
$hasCompletionData = false;
if (!empty($completionTrends['data'])) {
    foreach ($completionTrends['data'] as $value) {
        if ($value > 0) {
            $hasCompletionData = true;
            break;
        }
    }
}
if ($hasCompletionData) {
    echo '<canvas id="completionTrendsChart" height="180"></canvas>';
} else {
    echo '<div class="d-flex align-items-center justify-content-center" style="height: 180px;">';
    echo '<div class="text-center text-muted">';
    echo '<i class="fa fa-info-circle fa-3x mb-3"></i>';
    echo '<p>' . get_string('nodata', 'report_platform_usage') . '</p>';
    echo '</div>';
    echo '</div>';
}
echo '</div>';
echo '</div>';
echo '</div>';

// Daily Users History Table.
echo '<div class="col-lg-4 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header bg-light">';
if ($incoursecontext) {
    echo '<h5 class="mb-0"><i class="fa fa-calendar-check-o mr-2"></i>' . get_string('coursedailyusers', 'report_platform_usage') . '</h5>';
} else {
    echo '<h5 class="mb-0"><i class="fa fa-calendar-check-o mr-2"></i>' . get_string('dailyuserstable', 'report_platform_usage') . '</h5>';
}
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

// Chart.js and AMD module initialization.
$ajaxurl = $CFG->wwwroot . '/report/platform_usage/ajax.php';

// Prepare initial data for JavaScript.
$initialdata = [
    'login_summary' => $loginSummary,
    'user_summary' => $userSummary,
    'course_stats' => $courseStats,
    'daily_logins' => $dailyLogins,
    'course_access_trends' => $courseAccessTrends,
    'top_courses' => array_values($topCourses),
    'top_activities' => $topActivities,
    'completions_summary' => $completionsSummary,
    'completion_trends' => $completionTrends,
    'daily_users' => $dailyUsers,
    'top_dedication' => $topDedication,
];

// Configuration for the AMD module.
$config = [
    'ajaxUrl' => $ajaxurl,
    'courseId' => (int)$courseid,
    'inCourseContext' => $incoursecontext,
];

// Load Chart.js from CDN.
echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

// Initialize the AMD module.
$PAGE->requires->js_call_amd(
    'report_platform_usage/dashboard',
    'init',
    [$config, $initialdata, $jsstrings]
);

// Close report wrapper div.
echo '</div>';

echo $OUTPUT->footer();
