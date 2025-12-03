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

// Plugin information.
$string['pluginname'] = 'Platform Usage Report';
$string['platform_usage:view'] = 'View platform usage report';
$string['platform_usage:export'] = 'Export platform usage report';
$string['platformusagereport'] = 'Platform Usage Report';

// Company filter.
$string['company'] = 'Company';
$string['allcompanies'] = 'All companies';
$string['selectcompany'] = 'Select a company';
$string['filter'] = 'Apply filter';

// Export options.
$string['exportexcel'] = 'Export to Excel';
$string['exportcsv'] = 'Export to CSV';

// Time periods.
$string['today'] = 'Today';
$string['lastweek'] = 'Last 7 days';
$string['lastmonth'] = 'Last 30 days';
$string['lastquarter'] = 'Last 90 days';
$string['lastyear'] = 'Last year';
$string['custom'] = 'Custom date range';
$string['datefrom'] = 'From date';
$string['dateto'] = 'To date';

// Login statistics.
$string['loginstoday'] = 'Logins today';
$string['loginsweek'] = 'Logins in last 7 days';
$string['loginsmonth'] = 'Logins in last 30 days';
$string['uniqueuserstoday'] = 'Unique users today';
$string['uniqueusersweek'] = 'Unique users in last 7 days';
$string['uniqueusersmonth'] = 'Unique users in last 30 days';

// User statistics.
$string['totalusers'] = 'Total registered users';
$string['activeusers'] = 'Active users (accessed in last 30 days)';
$string['inactiveusers'] = 'Inactive users (no access in last 30 days)';

// Chart titles.
$string['logintrends'] = 'Login Trends';
$string['dailylogins'] = 'Daily Logins';
$string['weeklylogins'] = 'Weekly Logins';
$string['monthlylogins'] = 'Monthly Logins';
$string['usersbyactivity'] = 'Users by Activity Status';
$string['courseusage'] = 'Course Usage';
$string['topcourses'] = 'Top Courses by Access';
$string['courseaccesstrends'] = 'Course Access Trends';
$string['activityusage'] = 'Activity Usage';
$string['topactivities'] = 'Top Activities by Access';

// Table headers.
$string['date'] = 'Date';
$string['logins'] = 'Logins';
$string['uniqueusers'] = 'Unique users';
$string['coursename'] = 'Course name';
$string['courseaccesses'] = 'Course accesses';
$string['activityname'] = 'Activity name';
$string['activitytype'] = 'Activity type';
$string['activityaccesses'] = 'Activity accesses';
$string['username'] = 'Username';
$string['fullname'] = 'Full name';
$string['email'] = 'Email address';
$string['lastaccess'] = 'Last access';
$string['logincount'] = 'Login count';

// Summary sections.
$string['summary'] = 'Summary';
$string['platformsummary'] = 'Platform Summary';
$string['coursesummary'] = 'Course Summary';
$string['usersummary'] = 'User Summary';

// Report sections.
$string['platformaccess'] = 'Platform Access';
$string['courseaccess'] = 'Course Access';
$string['activityaccess'] = 'Activity Access';
$string['userdetails'] = 'User Details';

// Messages and notifications.
$string['nodata'] = 'No data available for the selected filters';
$string['nodataforperiod'] = 'No data available for the selected time period';
$string['selectcompanyfirst'] = 'Please select a company to view the report';
$string['loadingreport'] = 'Loading report data...';

// Privacy.
$string['privacy:metadata'] = 'The Platform Usage Report plugin does not store any personal data.';

// Navigation.
$string['backtoreport'] = 'Back to report';
$string['viewdetails'] = 'View details';

// Chart labels.
$string['period'] = 'Period';
$string['accesscount'] = 'Access count';
$string['usercount'] = 'User count';

// Excel export.
$string['reporttitle'] = 'Platform Usage Report';
$string['daterange'] = 'Date range';
$string['generateddate'] = 'Generated on';
$string['loginsummary'] = 'Login Summary';
$string['shortname'] = 'Short name';
$string['courses'] = 'Courses';
$string['activities'] = 'Activities';
$string['users'] = 'Users';
$string['courseaccessdetails'] = 'Course Access Details';
$string['activityaccessdetails'] = 'Activity Access Details';
$string['avgaccessperuser'] = 'Avg. accesses per user';
$string['total'] = 'Total';
$string['created'] = 'Created on';
$string['status'] = 'Status';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';

// Course completions.
$string['completions'] = 'Completions';
$string['completionsmonth'] = 'Completions (last 30 days)';
$string['completionsweek'] = 'Completions (last 7 days)';
$string['completionstoday'] = 'Completions today';
$string['totalcompletions'] = 'Total completions';
$string['completiontrends'] = 'Completion Trends';

// Dashboard access.
$string['dashboardusers'] = 'Dashboard users (last 30 days)';
$string['dashboardweek'] = 'Dashboard users (last 7 days)';
$string['dashboardtoday'] = 'Today';

// Session metrics.
$string['avgsessionduration'] = 'Average session duration';
$string['totalsessions'] = 'Total sessions';

// Logout metrics.
$string['logoutsmonth'] = 'Logouts (last 30 days)';
$string['logoutsweek'] = 'Logouts (last 7 days)';
$string['logoutstoday'] = 'Logouts today';

// Daily users.
$string['dailyusers'] = 'Daily Unique Users';
$string['dailyuserstable'] = 'Daily Users History';

// Dedication metrics.
$string['topdedication'] = 'Top Courses by Dedication Time';
$string['dedicationdetails'] = 'Dedication Details';
$string['totaldedication'] = 'Total dedication time';
$string['enrolledusers'] = 'Enrolled users';
$string['dedicationpercent'] = 'Dedication percentage';

// Course report.
$string['coursereport'] = 'Course Usage Report';
$string['coursereport_desc'] = 'Detailed usage statistics for this course, including user activity, engagement metrics, and dedication time analysis.';
$string['courseenrolledusers'] = 'Enrolled users';
$string['courseactiveusers'] = 'Active users in course';
$string['courseinactiveusers'] = 'Inactive users in course';
$string['courselogins'] = 'Course accesses';
$string['coursecompletions'] = 'Course completions';

// Section descriptions.
$string['logintrends_desc'] = 'Daily login trends and unique user counts over the last 30 days.';
$string['usersbyactivity_desc'] = 'Distribution of users by activity status (active vs. inactive based on 30-day threshold).';
$string['coursetrends_desc'] = 'Daily course access trends during the selected period.';
$string['dedication_desc'] = 'Time spent by students on each course, calculated from session activity logs.';
$string['topcourses_desc'] = 'Courses ranked by access count, with dedication time metrics.';
$string['topactivities_desc'] = 'Most accessed learning activities across all courses.';
$string['completiontrends_desc'] = 'Daily course completion trends over the last 30 days.';

// Statistics.
$string['average'] = 'Average';
$string['maximum'] = 'Maximum';

// Export descriptions.
$string['export_report_desc'] = 'Comprehensive platform usage analysis with detailed activity metrics and user engagement data.';
$string['export_logins_desc'] = 'Login Summary: Total logins and unique users for today, last 7 days, and last 30 days. Each successful authentication to the platform counts as one login.';
$string['export_users_desc'] = 'User Activity Summary: Total registered users, active users (logged in within 30 days), and inactive users. Active users are defined as those who accessed the platform in the last 30 days.';
$string['export_courses_desc'] = 'Course Access Details: Courses ranked by total views, unique users, and dedication time. Access count represents the number of times users viewed each course page.';
$string['export_activities_desc'] = 'Activity Access Details: Most accessed learning activities (assignments, forums, quizzes, etc.) with view counts and unique user engagement. Identifies which learning materials are most utilized.';
$string['export_daily_desc'] = 'Daily Login History: Day-by-day breakdown of platform logins and unique users. Useful for identifying usage patterns and peak activity periods.';
$string['export_completions_desc'] = 'Course Completion Summary: Number of course completions across different time periods. A completion is recorded when a user meets all course completion criteria.';
$string['export_dedication_desc'] = 'Course Dedication Analysis: Time spent by students on each course based on session activity. Sessions end after 1 hour of inactivity. Shows total dedication time and percentage distribution across courses.';
$string['export_dailyusers_desc'] = 'Daily Unique Users: Historical record of unique user counts per day. Tracks engagement trends and platform growth over time.';

// Filter labels.
$string['generated_by'] = 'Generated by';
$string['filter_company'] = 'Company filter';
$string['filter_daterange'] = 'Date range filter';
$string['metric_explanation'] = 'Metric explanation';
$string['data_section'] = 'Data section';
