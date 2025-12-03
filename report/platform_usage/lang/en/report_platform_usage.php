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
 * English language strings for Platform Usage Report.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin core.
$string['pluginname'] = 'Platform Usage Report';
$string['platform_usage:view'] = 'View the platform usage report';
$string['platform_usage:export'] = 'Export the platform usage report';
$string['platformusagereport'] = 'Platform Usage Report';
$string['reporttitle'] = 'Platform Usage Report';

// Filters.
$string['company'] = 'Company';
$string['allcompanies'] = 'All companies';
$string['selectcompany'] = 'Select a company';
$string['filter'] = 'Apply filters';
$string['filter_company'] = 'Company';
$string['filter_daterange'] = 'Date range';

// Time periods.
$string['today'] = 'Today';
$string['lastweek'] = 'Last 7 days';
$string['lastmonth'] = 'Last 30 days';
$string['lastquarter'] = 'Last 90 days';
$string['lastyear'] = 'Last 365 days';
$string['custom'] = 'Custom range';
$string['datefrom'] = 'From';
$string['dateto'] = 'To';
$string['daterange'] = 'Date range';

// Export.
$string['exportexcel'] = 'Download as Excel';
$string['exportcsv'] = 'Download as CSV';

// Login metrics.
$string['loginstoday'] = 'Logins today';
$string['loginsweek'] = 'Logins (7 days)';
$string['loginsmonth'] = 'Logins (30 days)';
$string['uniqueuserstoday'] = 'Unique users today';
$string['uniqueusersweek'] = 'Unique users (7 days)';
$string['uniqueusersmonth'] = 'Unique users (30 days)';
$string['loginsummary'] = 'Login summary';
$string['logintrends'] = 'Login trends';
$string['dailylogins'] = 'Daily logins';
$string['weeklylogins'] = 'Weekly logins';
$string['monthlylogins'] = 'Monthly logins';

// User metrics.
$string['totalusers'] = 'Total users';
$string['activeusers'] = 'Active users';
$string['inactiveusers'] = 'Inactive users';
$string['usersummary'] = 'User summary';
$string['usersbyactivity'] = 'Users by activity';
$string['userdetails'] = 'User details';
$string['usercount'] = 'Users';
$string['dailyusers'] = 'Daily unique users';
$string['dailyuserstable'] = 'Daily users';

// Course metrics.
$string['courses'] = 'Courses';
$string['coursename'] = 'Course';
$string['courseaccesses'] = 'Accesses';
$string['courseusage'] = 'Course usage';
$string['topcourses'] = 'Most accessed courses';
$string['courseaccesstrends'] = 'Access trends';
$string['courseaccessdetails'] = 'Course access details';
$string['coursesummary'] = 'Course summary';
$string['courseaccess'] = 'Course access';

// Activity metrics.
$string['activities'] = 'Activities';
$string['activityname'] = 'Activity';
$string['activitytype'] = 'Type';
$string['activityaccesses'] = 'Views';
$string['activityusage'] = 'Activity usage';
$string['topactivities'] = 'Most accessed activities';
$string['activityaccessdetails'] = 'Activity details';
$string['activityaccess'] = 'Activity access';

// Completion metrics.
$string['completions'] = 'Completions';
$string['completionstoday'] = 'Today';
$string['completionsweek'] = 'Last 7 days';
$string['completionsmonth'] = 'Last 30 days';
$string['totalcompletions'] = 'Total';
$string['completiontrends'] = 'Completion trends';

// Dedication metrics.
$string['topdedication'] = 'Courses by dedication';
$string['dedicationdetails'] = 'Dedication details';
$string['totaldedication'] = 'Total time';
$string['enrolledusers'] = 'Enrolled';
$string['dedicationpercent'] = 'Share';

// Session metrics.
$string['avgsessionduration'] = 'Avg. session duration';
$string['totalsessions'] = 'Total sessions';
$string['logoutstoday'] = 'Logouts today';
$string['logoutsweek'] = 'Logouts (7 days)';
$string['logoutsmonth'] = 'Logouts (30 days)';

// Table headers.
$string['date'] = 'Date';
$string['logins'] = 'Logins';
$string['uniqueusers'] = 'Unique users';
$string['username'] = 'Username';
$string['fullname'] = 'Name';
$string['email'] = 'Email';
$string['lastaccess'] = 'Last access';
$string['logincount'] = 'Logins';
$string['shortname'] = 'Short name';
$string['avgaccessperuser'] = 'Avg. per user';

// Status.
$string['status'] = 'Status';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';
$string['total'] = 'Total';
$string['created'] = 'Created';

// Statistics.
$string['summary'] = 'Summary';
$string['platformsummary'] = 'Platform overview';
$string['platformaccess'] = 'Platform access';
$string['average'] = 'Average';
$string['maximum'] = 'Maximum';
$string['period'] = 'Period';
$string['accesscount'] = 'Accesses';

// Course report.
$string['coursereport'] = 'Course Usage Report';
$string['coursereport_desc'] = 'Usage statistics for this course including activity, engagement, and dedication.';
$string['courseenrolledusers'] = 'Enrolled users';
$string['courseactiveusers'] = 'Active users';
$string['courseinactiveusers'] = 'Inactive users';
$string['courselogins'] = 'Course accesses';
$string['coursecompletions'] = 'Completions';

// Messages.
$string['nodata'] = 'No data available for the selected filters.';
$string['nodataforperiod'] = 'No data available for this period.';
$string['selectcompanyfirst'] = 'Select a company to view the report.';
$string['loadingreport'] = 'Loading...';

// Navigation.
$string['backtoreport'] = 'Back to report';
$string['viewdetails'] = 'View details';
$string['generateddate'] = 'Generated';
$string['generated_by'] = 'Generated by';

// Privacy.
$string['privacy:metadata'] = 'This plugin does not store personal data.';

// Section descriptions for UI.
$string['logintrends_desc'] = 'Login activity over the last 30 days';
$string['usersbyactivity_desc'] = 'Active vs inactive users (30-day threshold)';
$string['coursetrends_desc'] = 'Course access during selected period';
$string['dedication_desc'] = 'Time spent per course based on session activity';
$string['topcourses_desc'] = 'Courses ranked by access count';
$string['topactivities_desc'] = 'Most viewed learning activities';
$string['completiontrends_desc'] = 'Course completions over 30 days';

// Export descriptions.
$string['export_report_desc'] = 'Platform usage analysis with activity metrics and engagement data.';
$string['export_logins_desc'] = 'Total logins and unique users by period. Each authentication counts as one login.';
$string['export_users_desc'] = 'Registered users, active users (accessed in 30 days), and inactive users.';
$string['export_courses_desc'] = 'Courses ranked by views, unique users, and dedication time.';
$string['export_activities_desc'] = 'Most accessed activities with view counts and user engagement.';
$string['export_daily_desc'] = 'Day-by-day login breakdown for pattern analysis.';
$string['export_completions_desc'] = 'Course completions by period. Recorded when completion criteria are met.';
$string['export_dedication_desc'] = 'Time per course based on sessions. Sessions end after 1 hour of inactivity.';
$string['export_dailyusers_desc'] = 'Daily unique user counts for tracking engagement trends.';

// Misc.
$string['metric_explanation'] = 'Metric explanation';
$string['data_section'] = 'Data section';
$string['users'] = 'Users';
$string['coursemetrics'] = 'Course metrics';
$string['metric'] = 'Metric';
$string['value'] = 'Value';
$string['completionrate'] = 'Completion rate';
$string['coursereport'] = 'Course Usage Report';

// Settings.
$string['settings'] = 'Platform usage settings';
$string['setting_session_limit'] = 'Session timeout';
$string['setting_session_limit_desc'] = 'Maximum time between actions to consider them part of the same session. Used for calculating time dedication.';
$string['setting_ignore_sessions_limit'] = 'Minimum session duration';
$string['setting_ignore_sessions_limit_desc'] = 'Ignore sessions shorter than this duration. Helps filter out brief page visits that do not represent real work time.';
$string['setting_default_period'] = 'Default time period';
$string['setting_default_period_desc'] = 'Default time range for report data when no custom dates are selected.';
$string['setting_top_items_limit'] = 'Top items limit';
$string['setting_top_items_limit_desc'] = 'Number of items to display in course and activity rankings.';
$string['setting_enable_cache'] = 'Enable cache';
$string['setting_enable_cache_desc'] = 'Store report data in cache to improve performance. Disable only for debugging.';

// Tooltips for report elements.
$string['tooltip_platformaccess'] = 'Number of times users logged into the platform during the selected period.';
$string['tooltip_loginstoday'] = 'Total login events recorded today (a user can log in multiple times).';
$string['tooltip_loginsweek'] = 'Total login events in the last 7 days.';
$string['tooltip_loginsmonth'] = 'Total login events in the last 30 days.';
$string['tooltip_uniqueusers'] = 'Number of different users who logged in at least once during the period.';
$string['tooltip_usersummary'] = 'Breakdown of users by their activity status on the platform.';
$string['tooltip_totalusers'] = 'Total number of registered users in the selected scope.';
$string['tooltip_activeusers'] = 'Users who accessed the platform within the last 30 days.';
$string['tooltip_inactiveusers'] = 'Users with no platform access in the last 30 days.';
$string['tooltip_completions'] = 'Number of courses completed by users during the selected period.';
$string['tooltip_completionstoday'] = 'Courses completed today.';
$string['tooltip_completionsweek'] = 'Courses completed in the last 7 days.';
$string['tooltip_completionsmonth'] = 'Courses completed in the last 30 days.';
$string['tooltip_totalcompletions'] = 'Total number of course completions recorded.';
$string['tooltip_dailyusers'] = 'Daily breakdown of unique users who accessed the platform.';
$string['tooltip_avgdaily'] = 'Average number of unique users per day.';
$string['tooltip_maxdaily'] = 'Highest number of unique users in a single day.';
$string['tooltip_dailylogins'] = 'Trend of logins and unique users over the last 30 days.';
$string['tooltip_courseaccess'] = 'Course access and dedication metrics for the selected course.';
$string['tooltip_courseaccesses'] = 'Number of times the course was accessed during the selected period.';
$string['tooltip_enrolledusers'] = 'Total number of users enrolled in the course.';
$string['tooltip_courseactiveusers'] = 'Enrolled users who accessed the platform in the last 30 days.';
$string['tooltip_courseinactiveusers'] = 'Enrolled users with no platform access in the last 30 days.';
$string['tooltip_coursecompletions'] = 'Number of users who completed all course requirements.';
$string['tooltip_totaldedication'] = 'Total estimated time spent in the course by all users.';
$string['tooltip_avgdedication'] = 'Average time spent per enrolled user.';
$string['tooltip_dedication_calc'] = 'Time is calculated based on user activity sessions. A session ends after {$a} of inactivity.';
$string['tooltip_topcourses'] = 'Most accessed courses ranked by number of views.';
$string['tooltip_topactivities'] = 'Most accessed activities ranked by number of views.';
$string['tooltip_completiontrends'] = 'Daily course completions over the last 30 days.';
$string['tooltip_dailyuserstable'] = 'Recent daily unique user counts.';
$string['tooltip_dedicationchart'] = 'Top courses ranked by total time dedication.';
$string['tooltip_courseaccesstrends'] = 'Daily trend of course accesses and unique users over the selected time period.';
$string['tooltip_coursededicationsummary'] = 'Summary of time spent in the course by all enrolled users.';
$string['tooltip_coursecompletionpercent'] = 'Percentage of enrolled users who have completed the course.';
$string['tooltip_activepercent'] = 'Percentage of enrolled users who accessed the platform in the last 30 days.';
$string['tooltip_courseactivities'] = 'Most viewed activities within this course.';
$string['tooltip_accesshistory'] = 'Daily access pattern for this course.';
$string['tooltip_userdedicationlist'] = 'Time spent by each enrolled user in this course.';

// Scheduled task.
$string['task_collect_dedication'] = 'Collect dedication time data';

// Context-aware strings (course vs platform).
$string['courseaccesstrends_desc'] = 'Daily course accesses and unique users over the selected period.';
$string['courseenrolledusers_desc'] = 'Distribution of enrolled users by activity status.';
$string['coursededicationsummary'] = 'Course dedication summary';
$string['coursededication_desc'] = 'Summary of time dedication metrics for this course.';
$string['avgdedicationperuser'] = 'Average per user';
$string['topcourseaccess'] = 'Top course access';
$string['coursecompletiontrends'] = 'Course completion trends';
$string['coursecompletiontrends_desc'] = 'Daily completions for this course over the selected period.';
$string['coursedailyusers'] = 'Course daily users';

// Export detailed sheets.
$string['courseusersdetails'] = 'Enrolled users details';
$string['courseusersdetails_desc'] = 'Detailed breakdown of all enrolled users including completion status, dedication time, and last access.';
$string['courseaccesshistory'] = 'Course access history';
$string['courseaccesshistory_desc'] = 'Daily access trends for this course.';
$string['userdedication'] = 'User dedication';
$string['lastcourseaccess'] = 'Last course access';
$string['completionstatus'] = 'Completion status';
$string['notcompleted'] = 'Not completed';
$string['completed'] = 'Completed';
$string['never'] = 'Never';

// Excel sheet names (platform context).
$string['sheet_summary'] = 'Summary';
$string['sheet_daily_logins'] = 'Daily Logins';
$string['sheet_daily_users'] = 'Daily Users';
$string['sheet_completions'] = 'Completions';
$string['sheet_courses'] = 'Courses';
$string['sheet_activities'] = 'Activities';
$string['sheet_users'] = 'Users';
$string['sheet_dedication'] = 'Dedication';

// Excel sheet names (course context).
$string['sheet_course_summary'] = 'Course Summary';
$string['sheet_enrolled_users'] = 'Enrolled Users';
$string['sheet_access_history'] = 'Access History';
$string['sheet_course_activities'] = 'Course Activities';
$string['sheet_course_completions'] = 'Course Completions';
$string['sheet_course_dedication'] = 'Course Dedication';

// Excel additional information.
$string['export_generated_by'] = 'Report generated by IOMAD Platform';
$string['export_date_format'] = '%d/%m/%Y %H:%M';
$string['export_period_info'] = 'Reporting period';
$string['export_total_records'] = 'Total records';
$string['export_data_notes'] = 'Data notes';
$string['export_login_note'] = 'Each login event is counted separately. A user may have multiple logins per day.';
$string['export_dedication_note'] = 'Time is calculated based on activity sessions. Sessions end after {$a} of inactivity.';
$string['export_completion_note'] = 'Completions are recorded when all course completion criteria are met.';
$string['export_active_note'] = 'Active users are those who accessed the platform within the last 30 days.';
$string['percentage'] = 'Percentage';
$string['variation'] = 'Variation';
$string['trend'] = 'Trend';
$string['increasing'] = 'Increasing';
$string['decreasing'] = 'Decreasing';
$string['stable'] = 'Stable';
