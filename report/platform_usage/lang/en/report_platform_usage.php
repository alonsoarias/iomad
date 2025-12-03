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

// Dashboard metrics.
$string['dashboardusers'] = 'Dashboard users (30 days)';
$string['dashboardweek'] = 'Dashboard users (7 days)';
$string['dashboardtoday'] = 'Dashboard users today';

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
