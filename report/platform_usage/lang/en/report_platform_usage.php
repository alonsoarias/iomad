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
 * English language strings.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Platform Usage Report';
$string['platform_usage:view'] = 'View platform usage report';
$string['platform_usage:export'] = 'Export platform usage report';
$string['platformusagereport'] = 'Platform Usage Report';
$string['company'] = 'Company';
$string['allcompanies'] = 'All companies';
$string['selectcompany'] = 'Select a company';
$string['filter'] = 'Filter';
$string['exportexcel'] = 'Export to Excel';
$string['exportcsv'] = 'Export to CSV';

// Time periods.
$string['today'] = 'Today';
$string['lastweek'] = 'Last 7 days';
$string['lastmonth'] = 'Last 30 days';
$string['lastquarter'] = 'Last 90 days';
$string['lastyear'] = 'Last year';
$string['custom'] = 'Custom range';
$string['datefrom'] = 'Date from';
$string['dateto'] = 'Date to';

// Statistics labels.
$string['loginstoday'] = 'Logins today';
$string['loginsweek'] = 'Logins last 7 days';
$string['loginsmonth'] = 'Logins last 30 days';
$string['uniqueuserstoday'] = 'Unique users today';
$string['uniqueusersweek'] = 'Unique users last 7 days';
$string['uniqueusersmonth'] = 'Unique users last 30 days';
$string['totalusers'] = 'Total users';
$string['activeusers'] = 'Active users (logged in last 30 days)';
$string['inactiveusers'] = 'Users without recent activity (no login in 30 days)';

// Charts titles.
$string['logintrends'] = 'Login Trends';
$string['dailylogins'] = 'Daily Logins';
$string['weeklylogins'] = 'Weekly Logins';
$string['monthlylogins'] = 'Monthly Logins';
$string['usersbyactivity'] = 'Users by Recent Login Activity';
$string['courseusage'] = 'Course Usage';
$string['topcourses'] = 'Top Courses by Access';
$string['courseaccesstrends'] = 'Course Access Trends';
$string['activityusage'] = 'Activity Usage';
$string['topactivities'] = 'Top Activities by Access';

// Table headers.
$string['date'] = 'Date';
$string['logins'] = 'Logins';
$string['uniqueusers'] = 'Unique Users';
$string['coursename'] = 'Course Name';
$string['courseaccesses'] = 'Course Accesses';
$string['activityname'] = 'Activity Name';
$string['activitytype'] = 'Activity Type';
$string['activityaccesses'] = 'Activity Accesses';
$string['username'] = 'Username';
$string['fullname'] = 'Full Name';
$string['email'] = 'Email';
$string['lastaccess'] = 'Last Access';
$string['logincount'] = 'Login Count';

// Summary section.
$string['summary'] = 'Summary';
$string['platformsummary'] = 'Platform Summary';
$string['coursesummary'] = 'Course Summary';
$string['usersummary'] = 'User Summary';

// Report sections.
$string['platformaccess'] = 'Platform Access';
$string['courseaccess'] = 'Course Access';
$string['activityaccess'] = 'Activity Access';
$string['userdetails'] = 'User Details';

// Messages.
$string['nodata'] = 'No data available for the selected criteria';
$string['nodataforperiod'] = 'No data available for the selected time period';
$string['selectcompanyfirst'] = 'Please select a company to view the report';
$string['loadingreport'] = 'Loading report data...';

// Privacy.
$string['privacy:metadata'] = 'The Platform Usage Report plugin does not store any personal data.';

// Navigation.
$string['backtoreport'] = 'Back to report';
$string['viewdetails'] = 'View details';

// Period labels for charts.
$string['period'] = 'Period';
$string['accesscount'] = 'Access Count';
$string['usercount'] = 'User Count';

// Excel export strings.
$string['reporttitle'] = 'Platform Usage Report';
$string['daterange'] = 'Date Range';
$string['generateddate'] = 'Generated Date';
$string['loginsummary'] = 'Login Summary';
$string['shortname'] = 'Short Name';
$string['courses'] = 'Courses';
$string['activities'] = 'Activities';
$string['users'] = 'Users';
$string['courseaccessdetails'] = 'Course Access Details';
$string['activityaccessdetails'] = 'Activity Access Details';
$string['avgaccessperuser'] = 'Avg. Access/User';
$string['total'] = 'Total';
$string['created'] = 'Created';
$string['status'] = 'Status';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';

// Engagement metrics.
$string['completions'] = 'Completions';
$string['completionsmonth'] = 'Completions (30 days)';
$string['completionsweek'] = 'Completions (7 days)';
$string['completionstoday'] = 'Completions today';
$string['totalcompletions'] = 'Total completions';
$string['completiontrends'] = 'Course Completion Trends';

// Dashboard access metrics.
$string['dashboardusers'] = 'Dashboard users (30 days)';
$string['dashboardweek'] = 'Dashboard users (7 days)';
$string['dashboardtoday'] = 'Today';

// Session metrics.
$string['avgsessionduration'] = 'Avg session duration';
$string['totalsessions'] = 'Total sessions';

// Logout metrics.
$string['logoutsmonth'] = 'Logouts (30 days)';
$string['logoutsweek'] = 'Logouts (7 days)';
$string['logoutstoday'] = 'Logouts today';

// Daily users.
$string['dailyusers'] = 'Daily Users (last 10 days)';
$string['dailyuserstable'] = 'Daily Users History';

// Dedication metrics.
$string['topdedication'] = 'Top Courses by Dedication';
$string['dedicationdetails'] = 'Dedication Details';
$string['totaldedication'] = 'Total Dedication';
$string['enrolledusers'] = 'Enrolled Users';
$string['dedicationpercent'] = 'Dedication %';

// Section descriptions.
$string['logintrends_desc'] = 'Daily login trends and unique users over the last 30 days';
$string['usersbyactivity_desc'] = 'Distribution of active vs inactive users (30-day threshold)';
$string['coursetrends_desc'] = 'Daily course access trends over the selected period';
$string['dedication_desc'] = 'Time spent by students on each course based on session activity';
$string['topcourses_desc'] = 'Courses ranked by access count with dedication metrics';
$string['topactivities_desc'] = 'Most accessed activities across all courses';
$string['completiontrends_desc'] = 'Daily course completion trends over the last 30 days';

// Daily metrics.
$string['average'] = 'Average';
$string['maximum'] = 'Maximum';

// Export descriptions.
$string['export_report_desc'] = 'Platform Usage Report - Comprehensive analysis of platform activity';
$string['export_logins_desc'] = 'Login Summary: Total logins and unique users for today, last 7 days, and last 30 days. A login is counted each time a user successfully authenticates to the platform.';
$string['export_users_desc'] = 'User Activity Summary: Total registered users, active users (logged in within 30 days), and inactive users. An active user is defined as someone who has accessed the platform in the last 30 days.';
$string['export_courses_desc'] = 'Course Access Details: Courses ranked by total views, unique users who accessed each course, and dedication time. Access count represents the number of times users viewed the course page.';
$string['export_activities_desc'] = 'Activity Access Details: Most accessed activities (assignments, forums, quizzes, etc.) with view counts and unique user engagement. Helps identify which learning materials are most utilized.';
$string['export_daily_desc'] = 'Daily Login History: Day-by-day breakdown of platform logins and unique users. Useful for identifying usage patterns and peak activity periods.';
$string['export_completions_desc'] = 'Course Completion Summary: Number of course completions for different time periods. A completion is recorded when a user meets all course completion criteria.';
$string['export_dedication_desc'] = 'Course Dedication Analysis: Time spent by students on each course based on session analysis. A session ends when a user is inactive for more than 1 hour. Shows total dedication time and percentage distribution across courses.';
$string['export_dailyusers_desc'] = 'Daily Unique Users: Historical record of daily unique user counts. Helps track engagement trends and platform growth.';
$string['generated_by'] = 'Generated by';
$string['filter_company'] = 'Filter - Company';
$string['filter_daterange'] = 'Filter - Date Range';
$string['metric_explanation'] = 'Metric Explanation';
$string['data_section'] = 'Data Section';
