<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     report_usage_monitor
 * @category    string
 * @copyright   2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin general strings
$string['pluginname'] = 'Usage Report';
$string['reportinfotext'] = 'This plugin has been created for another success story of <strong>IngeWeb</strong>. Visit us at <a target="_blank" href="http://ingeweb.co/">IngeWeb - Solutions to succeed on the Internet</a>.';
$string['exclusivedisclaimer'] = 'This plugin is part of, and is to be exclusively used with the Moodle hosting service provided by <a target="_blank" href="http://ingeweb.co/">IngeWeb</a>.';

// Dashboard strings
$string['dashboard'] = 'Dashboard';
$string['dashboard_title'] = 'Usage Monitor Dashboard';
$string['diskusage'] = 'Disk usage';
$string['users_today_card'] = 'Daily Users Today';
$string['max_userdaily_for_90_days'] = 'Maximum daily users in the last 90 days';
$string['notcalculatedyet'] = 'Not calculated yet';
$string['lastexecution'] = 'Last daily users calculation run: {$a}';
$string['lastexecutioncalculate'] = 'Last disk space calculation: {$a}';
$string['users_today'] = 'Number of daily users today: {$a}';
$string['date'] = 'Date';
$string['last_calculation'] = 'Last calculation';
$string['usersquantity'] = 'Number of daily users';
$string['disk_usage_distribution'] = 'Disk Usage Distribution';
$string['disk_usage_history'] = 'Disk Usage History (Last 30 Days)';
$string['percentage_used'] = 'Percentage Used';

// Dashboard sections
$string['disk_usage_by_directory'] = 'Disk Usage by Directory';
$string['largest_courses'] = 'Largest Courses';
$string['database'] = 'Database';
$string['files_dir'] = 'Files (filedir)';
$string['cache'] = 'Cache';
$string['others'] = 'Others';
$string['directory'] = 'Directory';
$string['size'] = 'Size';
$string['percentage'] = 'Percentage';
$string['course'] = 'Course';
$string['backup_count'] = 'Backup Count';
$string['topuser'] = 'Top 10 Daily Users';
$string['lastusers'] = 'Daily users of the last 10 days';
$string['usertable'] = 'Top users table';
$string['userchart'] = 'Graph top users';
$string['system_info'] = 'System Information';
$string['moodle_version'] = 'Moodle Version';
$string['total_courses'] = 'Total Courses';
$string['backup_per_course'] = 'Backups per Course';
$string['registered_users'] = 'Registered Users';
$string['active_users'] = 'active users';
$string['suspended_users'] = 'suspended users';
$string['recommendations'] = 'Recommendations';

// Warning levels and indicator labels
$string['warning70'] = 'Warning (70%)';
$string['critical90'] = 'Critical (90%)';
$string['limit100'] = 'Limit (100%)';
$string['percent_of_threshold'] = '% of threshold';

// Recommendation tips
$string['space_saving_tips'] = 'Tips to save disk space:';
$string['tip_backups'] = 'Reduce the number of automatic backups per course (currently: {$a})';
$string['tip_files'] = 'Clean up old unused files using the file cleanup tool';
$string['tip_courses'] = 'Archive or delete old courses that are no longer used';
$string['tip_cache'] = 'Purge the system cache to free up temporary space';
$string['disk_usage_ok'] = 'Disk usage is at an acceptable level. No immediate action required.';
$string['user_count_ok'] = 'User count is at an acceptable level. No immediate action required.';
$string['user_limit_tips'] = 'Tips for managing user limit:';
$string['tip_user_inactive'] = 'Consider cleaning up inactive user accounts that haven\'t logged in for a long time.';
$string['tip_user_limit'] = 'If the number of users is consistently approaching the limit, consider increasing your quota.';

// Task strings
$string['calculatediskusagetask'] = 'Task to calculate the disk usage';
$string['getlastusers'] = 'Task to calculate the top of unique accesses';
$string['getlastusers90days'] = 'Task to get the top users in the last 90 days';
$string['getlastusersconnected'] = 'Task to calculate the number of daily users today';
$string['processdisknotificationtask'] = 'Process disk usage notification task';
$string['processuserlimitnotificationtask'] = 'Process daily user limit notification task';

// Settings strings
$string['mainsettings'] = 'Main settings';
$string['email'] = 'Email for notifications';
$string['configemail'] = 'Email address where you want to send the attendance notifications.';
$string['max_daily_users_threshold'] = 'Limit Users';
$string['configmax_daily_users_threshold'] = 'Number of Limit Users.';
$string['disk_quota'] = 'Disk Quota';
$string['configdisk_quota'] = 'Disk Quota in gigabytes';
$string['notificationsettings'] = 'Notification settings';
$string['notificationsettingsinfo'] = 'Configure when and how notifications are sent.';
$string['disk_warning_level'] = 'Disk warning level';
$string['configdisk_warning_level'] = 'Percentage of disk usage that triggers warnings.';
$string['users_warning_level'] = 'Users warning level';
$string['configusers_warning_level'] = 'Percentage of user limit that triggers warnings.';
$string['pathtodu'] = 'Path to du command';
$string['configpathtodu'] = 'Configure the path to the du command (disk usage). This is necessary for calculating disk usage. <strong>This setting is reflected in Moodle system paths</strong>)';
$string['pathtodurecommendation'] = 'We recommend that you review and configure the path to \'du\' in the Moodle System Paths. You can find this setting under Site administration > Server > System Paths. <a target="_blank" href="settings.php?section=systempaths#id_s__pathtodu">Click here to go to System Paths</a>.';
$string['pathtodunote'] = 'Note: The path to \'du\' will be automatically detected only if this plugin is on a Linux system and if the location of \'du\' can be successfully detected.';
$string['activateshellexec'] = 'The shell_exec function is not active on this server. To use the auto-detection of the path to du, you need to enable shell_exec in your server configuration.';
$string['enable_api'] = 'Enable API';
$string['configenable_api'] = 'Enable API access for external systems to retrieve usage information.';

// Email notification strings
$string['subjectemail1'] = 'Daily User Limit Exceeded on Platform:';
$string['subjectemail2'] = 'Disk Space Alert on Platform:';

// API strings
$string['get_usage_data'] = 'Get usage data';
$string['get_usage_data_desc'] = 'Retrieves precalculated usage data for disk and users with minimal overhead.';
$string['set_usage_thresholds'] = 'Set usage thresholds';
$string['set_usage_thresholds_desc'] = 'Updates the configured thresholds for users and disk space.';
$string['user_threshold_updated'] = 'User threshold updated successfully.';
$string['disk_threshold_updated'] = 'Disk threshold updated successfully.';
$string['error_user_threshold_negative'] = 'User threshold must be greater than 0.';
$string['error_disk_threshold_negative'] = 'Disk threshold must be greater than 0.';
$string['error_no_thresholds_provided'] = 'No thresholds provided to update.';

// API response field descriptions
$string['site_name'] = 'Site name';
$string['site_shortname'] = 'Site short name';
$string['moodle_release'] = 'Human-readable Moodle version';
$string['course_count'] = 'Number of courses';
$string['user_count'] = 'Number of users';
$string['backup_auto_max_kept'] = 'Number of automatic backups kept';
$string['total_bytes'] = 'Total disk usage in bytes';
$string['total_readable'] = 'Human-readable disk usage';
$string['quota_bytes'] = 'Disk quota in bytes';
$string['quota_readable'] = 'Human-readable disk quota';
$string['disk_percentage'] = 'Disk usage percentage';
$string['database_bytes'] = 'Database size in bytes';
$string['database_readable'] = 'Human-readable database size';
$string['database_percentage'] = 'Database size percentage';
$string['filedir_bytes'] = 'File directory size in bytes';
$string['filedir_readable'] = 'Human-readable file directory size';
$string['filedir_percentage'] = 'File directory size percentage';
$string['cache_bytes'] = 'Cache size in bytes';
$string['cache_readable'] = 'Human-readable cache size';
$string['cache_percentage'] = 'Cache size percentage';
$string['backup_bytes'] = 'Backup size in bytes';
$string['backup_readable'] = 'Human-readable backup size';
$string['backup_percentage'] = 'Backup size percentage';
$string['others_bytes'] = 'Other directories size in bytes';
$string['others_readable'] = 'Human-readable other directories size';
$string['others_percentage'] = 'Other directories size percentage';
$string['user_threshold'] = 'User threshold';
$string['user_percentage'] = 'User usage percentage';
$string['course_id'] = 'Course ID';
$string['course_fullname'] = 'Course full name';
$string['course_shortname'] = 'Course short name';
$string['course_size_bytes'] = 'Course size in bytes';
$string['course_size_readable'] = 'Human-readable course size';
$string['course_backup_size_bytes'] = 'Course backup size in bytes';
$string['course_backup_size_readable'] = 'Human-readable course backup size';
$string['course_percentage'] = 'Course size percentage';
$string['course_backup_count'] = 'Course backup count';
$string['disk_calculation_timestamp'] = 'Disk calculation timestamp';
$string['users_calculation_timestamp'] = 'Users calculation timestamp';

// Notification history API strings
$string['notification_type'] = 'Notification type (disk, users, or all)';
$string['notification_limit'] = 'Maximum number of records to return';
$string['notification_offset'] = 'Offset for pagination';
$string['notification_total'] = 'Total number of records available';
$string['notification_limit_value'] = 'Requested maximum number of records';
$string['notification_offset_value'] = 'Requested offset';
$string['notification_id'] = 'Notification ID';
$string['notification_type_value'] = 'Notification type (disk or users)';
$string['notification_percentage'] = 'Usage percentage';
$string['notification_value'] = 'Human-readable value';
$string['notification_value_raw'] = 'Value in bytes or user count';
$string['notification_threshold'] = 'Human-readable threshold';
$string['notification_threshold_raw'] = 'Threshold in bytes or user count';
$string['notification_timecreated'] = 'Creation timestamp';
$string['notification_timereadable'] = 'Human-readable date and time';

// Projections and growth rates
$string['api_projections_title'] = 'Growth projections';
$string['api_projections_desc'] = 'Growth projection data and estimated days to reach thresholds';
$string['api_monthly_growth_rate'] = 'Monthly growth rate';
$string['api_projection_days'] = 'Days to reach threshold';
$string['growth_rate_disk'] = 'Disk growth rate';
$string['growth_rate_disk_desc'] = 'Monthly growth rate of disk usage in percentage';
$string['growth_rate_users'] = 'User growth rate';
$string['growth_rate_users_desc'] = 'Monthly growth rate of the number of users in percentage';
$string['days_to_threshold_disk'] = 'Days until disk threshold';
$string['days_to_threshold_disk_desc'] = 'Projected days until reaching the disk warning threshold';
$string['days_to_threshold_users'] = 'Days until user threshold';
$string['days_to_threshold_users_desc'] = 'Projected days until reaching the user warning threshold';

// Email templates
$string['messagehtml_userlimit'] = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Limit Alert - {$a->sitename}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%); padding: 30px 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <span style="font-size: 40px;">&#9888;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #ffffff; font-size: 24px; font-weight: bold; padding-top: 10px;">
                                        Daily User Limit Alert
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 15px;">
                                        <span style="display: inline-block; background-color: #ffffff; color: #c0392b; padding: 8px 20px; border-radius: 25px; font-weight: bold; font-size: 18px;">
                                            {$a->percentaje}% of limit reached
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Urgency Banner -->
                    <tr>
                        <td style="background-color: #fff3cd; padding: 15px 20px; border-left: 4px solid #ffc107;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #856404;">
                                        <strong>&#9889; ATTENTION REQUIRED:</strong> The platform <strong>{$a->sitename}</strong> has exceeded the daily user threshold. Immediate review is recommended.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 25px 30px;">
                            <!-- Alert Summary -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #3498db;">
                                        &#128202; Alert Summary
                                    </td>
                                </tr>
                            </table>

                            <!-- Progress Bar -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #ecf0f1; border-radius: 10px; padding: 3px;">
                                        <table role="presentation" width="{$a->percentaje}%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="background: linear-gradient(90deg, #e74c3c, #c0392b); color: #ffffff; text-align: center; padding: 10px 0; border-radius: 8px; font-weight: bold; font-size: 14px;">
                                                    {$a->percentaje}%
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Key Metrics Table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Report Date:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->lastday}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Active Users Today:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #e74c3c; font-size: 16px; border-bottom: 1px solid #e0e0e0;">{$a->numberofusers} users</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Configured Limit:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->threshold} users</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Users Over Limit:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #c0392b;">{$a->excess_users} users</td>
                                </tr>
                            </table>

                            <!-- Projection Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; color: #ffffff;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="font-size: 16px; font-weight: bold; padding-bottom: 10px;">
                                                    &#128200; Growth Projection
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; line-height: 1.5;">
                                                    Based on current trends, it is estimated that in <strong>{$a->days_to_critical} days</strong> the platform will reach <strong>{$a->critical_threshold}%</strong> of the configured limit. Consider increasing your user quota.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Platform Information -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #3498db;">
                                        &#128421; Platform Information
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Moodle Version:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->moodle_release}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Total Courses:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->courses_count}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Backups per Course:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->backup_auto_max_kept}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Disk Space Usage:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50;">{$a->diskusage} / {$a->quotadisk} ({$a->disk_percent}%)</td>
                                </tr>
                            </table>

                            <!-- Historical Data -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #3498db;">
                                        &#128197; Recent User History (Last 7 Days)
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #34495e;">
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Date</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Active Users</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">% of Limit</th>
                                </tr>
                                {$a->historical_data_rows}
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{$a->referer}" style="display: inline-block; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: #ffffff; padding: 14px 35px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 16px;">
                                            &#128202; View Full Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #2c3e50; padding: 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="color: #bdc3c7; font-size: 12px; padding-bottom: 10px;">
                                        This message was automatically generated by <strong style="color: #3498db;">Usage Report</strong> from <a href="https://ingeweb.co/" style="color: #3498db; text-decoration: none;">ingeweb.co</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #95a5a6; font-size: 11px; font-style: italic;">
                                        * Only distinct users who authenticated on the indicated date are counted. Users who authenticate more than once are only counted once.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

$string['messagehtml_diskusage'] = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disk Space Alert - {$a->sitename}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #d35400 0%, #e67e22 100%); padding: 30px 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <span style="font-size: 40px;">&#128190;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #ffffff; font-size: 24px; font-weight: bold; padding-top: 10px;">
                                        Disk Space Alert
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 15px;">
                                        <span style="display: inline-block; background-color: #ffffff; color: #d35400; padding: 8px 20px; border-radius: 25px; font-weight: bold; font-size: 18px;">
                                            {$a->percentage}% of quota used
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Urgency Banner -->
                    <tr>
                        <td style="background-color: #ffeaa7; padding: 15px 20px; border-left: 4px solid #fdcb6e;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #6c5c00;">
                                        <strong>&#128680; DISK SPACE WARNING:</strong> The platform <strong>{$a->sitename}</strong> has exceeded <strong>{$a->percentage}%</strong> of the assigned disk space. Review the recommendations below.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 25px 30px;">
                            <!-- Disk Usage Summary -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128202; Disk Usage Summary
                                    </td>
                                </tr>
                            </table>

                            <!-- Progress Bar -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #ecf0f1; border-radius: 10px; padding: 3px;">
                                        <table role="presentation" width="{$a->percentage}%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="background: linear-gradient(90deg, #e67e22, #d35400); color: #ffffff; text-align: center; padding: 10px 0; border-radius: 8px; font-weight: bold; font-size: 14px;">
                                                    {$a->percentage}%
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Key Metrics Table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Report Date:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->lastday}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Used Space:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #e67e22; font-size: 16px; border-bottom: 1px solid #e0e0e0;">{$a->diskusage}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Assigned Quota:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->quotadisk}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Available Space:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #27ae60;">{$a->available_space} ({$a->available_percent}%)</td>
                                </tr>
                            </table>

                            <!-- Space Distribution -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128193; Space Distribution by Category
                                    </td>
                                </tr>
                            </table>

                            <!-- Distribution Bars -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <!-- Database -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #9b59b6; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Database</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->databasesize}</strong> <span style="color: #777;">({$a->db_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Files -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #27ae60; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Files (filedir)</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->filedir_size}</strong> <span style="color: #777;">({$a->filedir_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Cache -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #e67e22; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Cache</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->cache_size}</strong> <span style="color: #777;">({$a->cache_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Others -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #7f8c8d; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Others</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->other_size}</strong> <span style="color: #777;">({$a->other_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Platform Information -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128421; Platform Information
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Moodle Version:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->moodle_release}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Total Courses:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->coursescount}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Backups per Course:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->backupcount}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Active Users:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50;">{$a->numberofusers} / {$a->threshold} ({$a->user_percent}%)</td>
                                </tr>
                            </table>

                            <!-- Largest Courses -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128218; Largest Courses
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #34495e;">
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Course</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Size</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">% of Total</th>
                                </tr>
                                {$a->top_courses_rows}
                            </table>

                            <!-- Recommendations Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; background-color: #e8f6f3; border-radius: 8px; border-left: 4px solid #1abc9c;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="font-size: 16px; font-weight: bold; color: #16a085; padding-bottom: 12px;">
                                                    &#128161; Recommendations to Free Up Space
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; color: #2c3e50; line-height: 1.8;">
                                                    &#8226; <strong>Reduce backups:</strong> Lower the number of automatic backups per course (currently: {$a->backupcount})<br>
                                                    &#8226; <strong>Clean files:</strong> Remove old unused files using the file cleanup tool<br>
                                                    &#8226; <strong>Review courses:</strong> Archive or clean up the largest courses listed above<br>
                                                    &#8226; <strong>Purge cache:</strong> Clear the system cache to free up temporary space
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{$a->referer}" style="display: inline-block; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: #ffffff; padding: 14px 35px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 16px;">
                                            &#128202; View Full Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #2c3e50; padding: 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="color: #bdc3c7; font-size: 12px; padding-bottom: 10px;">
                                        This message was automatically generated by <strong style="color: #3498db;">Usage Report</strong> from <a href="https://ingeweb.co/" style="color: #3498db; text-decoration: none;">ingeweb.co</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #95a5a6; font-size: 11px; font-style: italic;">
                                        If you need technical assistance, please do not reply to this email and contact your hosting administrator.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';