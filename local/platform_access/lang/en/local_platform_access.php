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
$string['generateaccessdesc'] = 'This tool generates login and course access records for all users in the platform. This is useful for testing and demonstration purposes.';
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
$string['randomize'] = 'Randomize timestamps';
$string['randomizedesc'] = 'If enabled, timestamps will be randomly distributed within the specified date range.';
$string['includeadmins'] = 'Include admin users';
$string['includeguests'] = 'Include guest users';
$string['onlyactiveusers'] = 'Only active users (not suspended/deleted)';
$string['processinguser'] = 'Processing user: {$a}';
$string['nousers'] = 'No users found matching the criteria';
$string['nocourses'] = 'No courses found';
$string['summary'] = 'Generation Summary';
$string['usersprocessed'] = 'Users processed: {$a}';
$string['loginsgenerated'] = 'Login records generated: {$a}';
$string['courseaccessgenerated'] = 'Course access records generated: {$a}';
$string['lastaccessupdated'] = 'User last access updated: {$a}';
$string['timecompleted'] = 'Time completed: {$a} seconds';
$string['specifycourses'] = 'Specific courses (leave empty for all)';
$string['specifyusers'] = 'Specific users (leave empty for all)';
$string['accesstype'] = 'Access type to generate';
$string['loginonly'] = 'Login records only';
$string['courseonly'] = 'Course access records only';
$string['both'] = 'Both login and course access';
$string['privacy:metadata'] = 'The Platform Access Generator plugin does not store any personal data.';
$string['randomize_help'] = 'When enabled, timestamps for login and course access records will be randomly distributed within the specified date range. When disabled, all records will use the end date.';
