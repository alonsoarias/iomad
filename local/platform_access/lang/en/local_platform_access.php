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
 * @package   local_platform_access
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Platform Access Generator';
$string['platform_access:generate'] = 'Generate platform access records';
$string['platform_access:view'] = 'View platform access generator';
$string['generateaccess'] = 'Generate Access Records';
$string['generateaccessdesc'] = 'This tool generates login, course and activity access records for users in the platform. Select a company to generate access records for all its users and their enrolled courses.';
$string['generatelogins'] = 'Generate login records';
$string['generatecourseaccess'] = 'Generate course access records';
$string['datefrom'] = 'Date from';
$string['dateto'] = 'Date to';
$string['generatebutton'] = 'Generate Access Records';
$string['generating'] = 'Generating access records...';
$string['success'] = 'Access records generated successfully';
$string['error'] = 'Error generating access records';
$string['totalusers'] = 'Total users processed';
$string['totallogins'] = 'Total login records generated';
$string['totalcourseaccess'] = 'Total course access records generated';
$string['confirmgenerate'] = 'Are you sure you want to generate access records? This will insert records into the database.';
$string['loginsperuser'] = 'Logins per user';
$string['courseaccessperuser'] = 'Course accesses per user';
$string['activityaccesspercourse'] = 'Activity accesses per course';
$string['randomize'] = 'Randomize timestamps';
$string['randomizedesc'] = 'If enabled, timestamps will be randomly distributed within the specified date range.';
$string['includeadmins'] = 'Include admin users';
$string['includeguests'] = 'Include guest users';
$string['onlyactiveusers'] = 'Only active users (not suspended/deleted)';
$string['processinguser'] = 'Processing user: {$a}';
$string['nousers'] = 'No users found matching the criteria. Make sure the selected company has users assigned.';
$string['nocourses'] = 'No courses found for the selected company';
$string['summary'] = 'Generation Summary';
$string['usersprocessed'] = 'Users processed';
$string['loginsgenerated'] = 'Login records generated';
$string['courseaccessgenerated'] = 'Course access records generated';
$string['activityaccessgenerated'] = 'Activity access records generated';
$string['lastaccessupdated'] = 'User last access updated';
$string['timecompleted'] = 'Time completed (seconds)';
$string['specifycourses'] = 'Specific courses (leave empty for all)';
$string['specifyusers'] = 'Specific users (leave empty for all)';
$string['accesstype'] = 'Access type to generate';
$string['loginonly'] = 'Login records only';
$string['courseonly'] = 'Course access records only';
$string['activityonly'] = 'Activity access records only';
$string['both'] = 'Login and course access';
$string['all'] = 'All companies';
$string['all_access'] = 'All (login, course and activity)';
$string['company'] = 'Company';
$string['company_help'] = 'Select a company to generate access records for all its users. If you select "All companies", records will be generated for users from all companies in the system.';
$string['privacy:metadata'] = 'The Platform Access Generator plugin does not store any personal data.';
$string['randomize_help'] = 'When enabled, timestamps for login and course access records will be randomly distributed within the specified date range. When disabled, all records will use the end date.';
$string['usersettings'] = 'User Settings';
$string['updateusercreated'] = 'Update user creation date';
$string['updateusercreated_help'] = 'If enabled, the user creation date (timecreated) will be updated to the specified date. This affects the user table and the local_report_user_logins table if it exists.';
$string['usercreateddate'] = 'User creation date';
$string['daterange'] = 'Date Range';
$string['accesscounts'] = 'Access Counts (Random Ranges)';
$string['minmaxerror'] = 'Minimum value must be less than or equal to maximum value';
$string['usersupdated'] = 'Users creation date updated';
$string['cleanbeforegenerate'] = 'Clean existing records before generating';
$string['cleanbeforegenerate_help'] = 'If enabled, all existing access records (logins, course views, activity views) for the selected users will be deleted before generating new records. This includes resetting user access fields (firstaccess, lastaccess, lastlogin, currentlogin).';
$string['recordsdeleted'] = 'Existing records deleted';
$string['userswithoutenrollments'] = 'Users without course enrollments';

// Advanced events.
$string['advancedevents'] = 'Advanced Events';
$string['generatedashboard'] = 'Generate dashboard access records';
$string['generatedashboard_help'] = 'If enabled, dashboard/area personal access events will be generated after each login (with 70% probability).';
$string['generatelogouts'] = 'Generate logout records';
$string['generatelogouts_help'] = 'If enabled, logout events will be generated for 50% of login sessions with a realistic session duration (5 min - 2 hours).';
$string['generatecompletions'] = 'Generate course completion records';
$string['generatecompletions_help'] = 'If enabled, course completion records will be generated for a percentage of courses based on the completion percentage range specified below.';
$string['completionpercent'] = 'Completion percentage';
$string['percentageerror'] = 'Percentage must be between 0 and 100';
$string['dashboardaccessgenerated'] = 'Dashboard access records generated';
$string['logoutsgenerated'] = 'Logout records generated';
$string['completionsgenerated'] = 'Course completions generated';

// Session Duration Tracking (HIGH PRIORITY).
$string['sessionduration'] = 'Session Duration Tracking';
$string['calculatesessionduration'] = 'Track session duration';
$string['calculatesessionduration_help'] = 'When enabled, generates realistic sessions with proper duration tracking. Each login will be paired with a logout event at a calculated time, allowing accurate session duration metrics. This is essential for engagement analytics.';
$string['sessiondurationminutes'] = 'Session duration (minutes)';
$string['sessiondurationminerror'] = 'Minimum session duration must be at least 1 minute';
$string['sessiondurationmaxerror'] = 'Maximum session duration cannot exceed 480 minutes (8 hours)';
$string['sessionswithdurations'] = 'Sessions with duration tracking';
$string['avgsessionduration'] = 'Average session duration (minutes)';
$string['totalsessionminutes'] = 'Total session time (minutes)';

// Security Monitoring (MEDIUM PRIORITY).
$string['securitymonitoring'] = 'Security Monitoring';
$string['generatefailedlogins'] = 'Generate failed login attempts';
$string['generatefailedlogins_help'] = 'When enabled, generates failed login events for security monitoring and reporting. Failed logins include various reasons such as wrong password (80%), user not found, user suspended, user locked out, etc.';
$string['failedloginsperuser'] = 'Failed logins per user';
$string['failedloginsgenerated'] = 'Failed login records generated';

// Failed login reasons.
$string['failedloginreason1'] = 'User does not exist';
$string['failedloginreason2'] = 'User is suspended';
$string['failedloginreason3'] = 'Wrong password';
$string['failedloginreason4'] = 'User is locked out';
$string['failedloginreason5'] = 'User is not authorized';
