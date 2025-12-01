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
 * English language strings for the usage monitor plugin.
 *
 * @package     report_usage_monitor
 * @category    string
 * @author      Alonso Arias <soporte@ingeweb.co>
 * @copyright   2025 Alonso Arias <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin general.
$string['pluginname'] = 'Usage monitor';
$string['reportinfotext'] = 'This plugin has been created for another success story of <strong>IngeWeb</strong>. Visit us at <a target="_blank" href="http://ingeweb.co/">IngeWeb - Solutions to succeed on the Internet</a>.';
$string['exclusivedisclaimer'] = 'This plugin is part of, and is to be exclusively used with the Moodle hosting service provided by <a target="_blank" href="http://ingeweb.co/">IngeWeb</a>.';

// Dashboard.
$string['dashboard'] = 'Dashboard';
$string['dashboard_title'] = 'Usage monitor';
$string['diskusage'] = 'Disk usage';
$string['users_today_card'] = 'Daily users';
$string['max_userdaily_for_90_days'] = 'Peak daily users (90 days)';
$string['notcalculatedyet'] = 'Not calculated yet';
$string['lastexecution'] = 'Last calculation: {$a}';
$string['lastexecutioncalculate'] = 'Last calculation: {$a}';
$string['users_today'] = 'Daily users today: {$a}';
$string['date'] = 'Date';
$string['last_calculation'] = 'Last calculation';
$string['usersquantity'] = 'Number of users';
$string['disk_usage_distribution'] = 'Disk usage distribution';
$string['disk_usage_history'] = 'Disk usage history (last 30 days)';
$string['percentage_used'] = 'Percentage used';

// Dashboard sections.
$string['disk_usage_by_directory'] = 'Disk usage by directory';
$string['largest_courses'] = 'Largest courses';
$string['database'] = 'Database';
$string['files_dir'] = 'Files (filedir)';
$string['cache'] = 'Cache';
$string['others'] = 'Others';
$string['directory'] = 'Directory';
$string['size'] = 'Size';
$string['percentage'] = 'Percentage';
$string['course'] = 'Course';
$string['backup_count'] = 'Backups';
$string['topuser'] = 'Top 10 daily users';
$string['lastusers'] = 'Daily users (last 10 days)';
$string['usertable'] = 'User table';
$string['userchart'] = 'User chart';
$string['system_info'] = 'System information';
$string['moodle_version'] = 'Moodle version';
$string['total_courses'] = 'Total courses';
$string['backup_per_course'] = 'Backups per course';
$string['registered_users'] = 'Registered users';
$string['active_users'] = 'Active users';
$string['suspended_users'] = 'Suspended users';
$string['recommendations'] = 'Recommendations';

// Warning levels.
$string['warning70'] = 'Warning (70%)';
$string['critical90'] = 'Critical (90%)';
$string['limit100'] = 'Limit (100%)';
$string['percent_of_threshold'] = '% of limit';
$string['warning_threshold'] = 'Warning threshold';

// Notifications.
$string['notification_status'] = 'Notification status';
$string['last_disk_notification'] = 'Last disk notification';
$string['last_user_notification'] = 'Last user notification';
$string['next_possible_notification'] = 'Next possible in';
$string['no_notification_sent'] = 'No notification sent yet';
$string['notifications_disabled'] = 'Notifications disabled';
$string['email_not_configured'] = 'Email not configured';

// Tasks.
$string['task_status'] = 'Task status';
$string['task_name'] = 'Task';
$string['last_run'] = 'Last run';
$string['next_run'] = 'Next run';
$string['task_never_run'] = 'Never run';
$string['task_disabled'] = 'Disabled';

// Alerts.
$string['active_alerts'] = 'Active alerts';
$string['alert_disk_critical'] = 'Disk space critical ({$a}%)';
$string['alert_disk_warning'] = 'Disk space warning ({$a}%)';
$string['alert_users_critical'] = 'Daily users critical ({$a}%)';
$string['alert_users_warning'] = 'Daily users warning ({$a}%)';
$string['no_active_alerts'] = 'No active alerts - all systems normal';

// Quick actions.
$string['quick_actions'] = 'Quick actions';
$string['go_to_settings'] = 'Plugin settings';
$string['purge_cache'] = 'Purge cache';
$string['run_tasks'] = 'Scheduled tasks';
$string['file_cleanup'] = 'File cleanup';

// Course size.
$string['show_more_courses'] = 'Show more courses';
$string['install_coursesize'] = 'Install report_coursesize plugin for detailed course size analysis';
$string['coursesize_plugin_url'] = 'https://moodle.org/plugins/report_coursesize';

// Course access and completion.
$string['course_activity'] = 'Course activity';
$string['course_access_trends'] = 'Course access trends';
$string['most_accessed_courses'] = 'Most accessed courses';
$string['course_completion_trends'] = 'Course completion trends';
$string['total_accesses'] = 'Total accesses';
$string['unique_users'] = 'Unique users';
$string['unique_courses'] = 'Courses accessed';
$string['completions'] = 'Completions';
$string['avg_per_day'] = 'Avg. per day';
$string['last_30_days'] = 'Last 30 days';
$string['no_data_available'] = 'No data available';
$string['access_summary'] = 'Access summary';
$string['completion_summary'] = 'Completion summary';
$string['users_completed'] = 'Users with completions';
$string['courses_with_completions'] = 'Courses with completions';
$string['total_completions'] = 'Total completions';
$string['access_chart'] = 'Access chart';
$string['completion_chart'] = 'Completion chart';
$string['access_table'] = 'Access table';
$string['completion_table'] = 'Completion table';

// Tooltips.
$string['tooltip_total_accesses'] = 'Total number of course views in the last 30 days. Includes multiple visits by the same user.';
$string['tooltip_unique_users'] = 'Number of different users who accessed at least one course in the last 30 days.';
$string['tooltip_unique_courses'] = 'Number of different courses accessed at least once in the last 30 days. This is NOT the total number of courses on the platform.';
$string['tooltip_total_completions'] = 'Total number of course completions registered in the last 30 days.';
$string['tooltip_users_completed'] = 'Number of different users who completed at least one course in the last 30 days.';
$string['tooltip_courses_with_completions'] = 'Number of different courses that had at least one completion in the last 30 days.';
$string['tooltip_avg_per_day'] = 'Average number of accesses or completions per day during the last 30 days.';
$string['tooltip_disk_usage'] = 'Current disk space used compared to your allocated quota.';
$string['tooltip_users_today'] = 'Number of unique users who logged in today compared to your daily user limit.';
$string['tooltip_max_90_days'] = 'Highest number of daily unique logins recorded in the last 90 days.';

// Recommendations.
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

// Scheduled tasks.
$string['calculatediskusagetask'] = 'Calculate disk usage';
$string['getlastusers'] = 'Calculate unique access ranking';
$string['getlastusers90days'] = 'Get top users in the last 90 days';
$string['getlastusersconnected'] = 'Calculate daily users';
$string['processusersdailytask'] = 'Process daily users statistics';
$string['processcombinednotificationtask'] = 'Process usage monitor notifications';

// Settings.
$string['mainsettings'] = 'Main settings';
$string['email'] = 'Notification email';
$string['configemail'] = 'Email address where you want to send the notifications.';
$string['max_daily_users_threshold'] = 'User limit';
$string['configmax_daily_users_threshold'] = 'Maximum number of daily users allowed.';
$string['disk_quota'] = 'Disk quota';
$string['configdisk_quota'] = 'Disk quota in gigabytes.';
$string['notificationsettings'] = 'Notification settings';
$string['notificationsettingsinfo'] = 'Configure when and how notifications are sent.';
$string['disk_warning_level'] = 'Disk warning level';
$string['configdisk_warning_level'] = 'Percentage of disk usage that triggers warnings.';
$string['users_warning_level'] = 'Users warning level';
$string['configusers_warning_level'] = 'Percentage of user limit that triggers warnings.';
$string['pathtodu'] = 'Path to du command';
$string['configpathtodu'] = 'Configure the path to the du command (disk usage). This is necessary for calculating disk usage. <strong>This setting is reflected in Moodle system paths</strong>.';
$string['pathtodurecommendation'] = 'We recommend that you review and configure the path to \'du\' in the Moodle system paths. You can find this setting under Site administration > Server > System paths. <a target="_blank" href="settings.php?section=systempaths#id_s__pathtodu">Click here to go to system paths</a>.';
$string['pathtodunote'] = 'Note: The path to \'du\' will be automatically detected only if this plugin is on a Linux system and if the location of \'du\' can be successfully detected.';
$string['activateshellexec'] = 'The shell_exec function is not active on this server. To use the auto-detection of the path to du, you need to enable shell_exec in your server configuration.';
$string['enable_api'] = 'Enable API';
$string['configenable_api'] = 'Enable API access for external systems to retrieve usage information.';

// Email notifications.
$string['subjectemail1'] = 'Daily user limit exceeded on platform:';
$string['subjectemail2'] = 'Disk space alert on platform:';

// API.
$string['get_usage_data'] = 'Get usage data';
$string['get_usage_data_desc'] = 'Retrieves precalculated usage data for disk and users with minimal overhead.';
$string['set_usage_thresholds'] = 'Set usage thresholds';
$string['set_usage_thresholds_desc'] = 'Updates the configured thresholds for users and disk space.';
$string['user_threshold_updated'] = 'User threshold updated successfully.';
$string['disk_threshold_updated'] = 'Disk threshold updated successfully.';
$string['error_user_threshold_negative'] = 'User threshold must be greater than 0.';
$string['error_disk_threshold_negative'] = 'Disk threshold must be greater than 0.';
$string['error_no_thresholds_provided'] = 'No thresholds provided to update.';

// API response fields.
$string['site_name'] = 'Site name';
$string['site_shortname'] = 'Site short name';
$string['moodle_release'] = 'Moodle version';
$string['course_count'] = 'Number of courses';
$string['user_count'] = 'Number of users';
$string['backup_auto_max_kept'] = 'Automatic backups kept';
$string['total_bytes'] = 'Total disk usage in bytes';
$string['total_readable'] = 'Disk usage (readable)';
$string['quota_bytes'] = 'Disk quota in bytes';
$string['quota_readable'] = 'Disk quota (readable)';
$string['disk_percentage'] = 'Disk usage percentage';
$string['database_bytes'] = 'Database size in bytes';
$string['database_readable'] = 'Database size (readable)';
$string['database_percentage'] = 'Database size percentage';
$string['filedir_bytes'] = 'File directory size in bytes';
$string['filedir_readable'] = 'File directory size (readable)';
$string['filedir_percentage'] = 'File directory size percentage';
$string['cache_bytes'] = 'Cache size in bytes';
$string['cache_readable'] = 'Cache size (readable)';
$string['cache_percentage'] = 'Cache size percentage';
$string['backup_bytes'] = 'Backup size in bytes';
$string['backup_readable'] = 'Backup size (readable)';
$string['backup_percentage'] = 'Backup size percentage';
$string['others_bytes'] = 'Other directories size in bytes';
$string['others_readable'] = 'Other directories size (readable)';
$string['others_percentage'] = 'Other directories percentage';
$string['user_threshold'] = 'User threshold';
$string['user_percentage'] = 'User usage percentage';
$string['course_id'] = 'Course ID';
$string['course_fullname'] = 'Course full name';
$string['course_shortname'] = 'Course short name';
$string['course_size_bytes'] = 'Course size in bytes';
$string['course_size_readable'] = 'Course size (readable)';
$string['course_backup_size_bytes'] = 'Course backup size in bytes';
$string['course_backup_size_readable'] = 'Course backup size (readable)';
$string['course_percentage'] = 'Course size percentage';
$string['course_backup_count'] = 'Course backup count';
$string['disk_calculation_timestamp'] = 'Disk calculation timestamp';
$string['users_calculation_timestamp'] = 'Users calculation timestamp';

// Notification history API.
$string['notification_type'] = 'Notification type (disk, users, or all)';
$string['notification_limit'] = 'Maximum number of records to return';
$string['notification_offset'] = 'Offset for pagination';
$string['notification_total'] = 'Total number of records available';
$string['notification_limit_value'] = 'Requested maximum number of records';
$string['notification_offset_value'] = 'Requested offset';
$string['notification_id'] = 'Notification ID';
$string['notification_type_value'] = 'Notification type (disk or users)';
$string['notification_percentage'] = 'Usage percentage';
$string['notification_value'] = 'Value (readable)';
$string['notification_value_raw'] = 'Value in bytes or user count';
$string['notification_threshold'] = 'Threshold (readable)';
$string['notification_threshold_raw'] = 'Threshold in bytes or user count';
$string['notification_timecreated'] = 'Creation timestamp';
$string['notification_timereadable'] = 'Date and time (readable)';

// Projections.
$string['api_projections_title'] = 'Growth projections';
$string['api_projections_desc'] = 'Growth projection data and estimated days to reach thresholds';
$string['api_monthly_growth_rate'] = 'Monthly growth rate';
$string['api_projection_days'] = 'Days to reach threshold';
$string['growth_rate_disk'] = 'Disk growth rate';
$string['growth_rate_disk_desc'] = 'Monthly disk usage growth rate in percentage';
$string['growth_rate_users'] = 'User growth rate';
$string['growth_rate_users_desc'] = 'Monthly user count growth rate in percentage';
$string['days_to_threshold_disk'] = 'Days until disk threshold';
$string['days_to_threshold_disk_desc'] = 'Projected days until reaching the disk warning threshold';
$string['days_to_threshold_users'] = 'Days until user threshold';
$string['days_to_threshold_users_desc'] = 'Projected days until reaching the user warning threshold';

// Unified email template strings.
$string['email_notification_title'] = 'Usage Monitor Alert';
$string['email_notification_subject'] = 'Usage alert on platform';
$string['email_users_alert'] = 'Users';
$string['email_disk_alert'] = 'Disk';
$string['email_thresholds_exceeded'] = 'One or more usage thresholds have been exceeded on your platform. Please review the details below.';
$string['email_userlimit_section'] = 'Daily user limit exceeded';
$string['email_diskusage_section'] = 'Disk space usage alert';
$string['email_rec_users'] = 'Review users';
$string['email_rec_users_desc'] = 'Consider cleaning up inactive user accounts that have not logged in for a long time';
$string['email_rec_quota'] = 'Increase quota';
$string['email_rec_quota_desc'] = 'If the number of users is consistently approaching the limit, consider increasing your quota';
$string['email_general_disclaimer'] = 'This is an automated notification. For technical assistance, please contact your hosting administrator.';

// Email template strings for Mustache templates.
// User limit email strings.
$string['email_userlimit_title'] = 'Daily user limit alert';
$string['email_of_limit'] = 'of limit reached';
$string['email_attention_required'] = 'Attention required';
$string['email_userlimit_urgency'] = 'The platform has exceeded the daily user threshold. Immediate review is recommended for';
$string['email_alert_summary'] = 'Alert summary';
$string['email_report_date'] = 'Report date';
$string['email_active_users_today'] = 'Active users today';
$string['email_configured_limit'] = 'Configured limit';
$string['email_users_over_limit'] = 'Users over limit';
$string['email_users'] = 'users';
$string['email_growth_projection'] = 'Growth projection';
$string['email_projection_text'] = 'Based on current trends, it is estimated that in';
$string['email_days'] = 'days';
$string['email_projection_will_reach'] = 'the platform will reach';
$string['email_platform_info'] = 'Platform information';
$string['email_recent_history'] = 'Recent user history (last 7 days)';
$string['email_active_users'] = 'Active users';
$string['email_percent_of_limit'] = '% of limit';
$string['email_view_dashboard'] = 'View full dashboard';
$string['email_auto_generated'] = 'This message was automatically generated by';
$string['email_from'] = 'from';
$string['email_users_disclaimer'] = 'Only distinct users who authenticated on the indicated date are counted. Users who authenticate more than once are only counted once.';

// Disk usage email strings.
$string['email_diskusage_title'] = 'Disk space alert';
$string['email_of_quota_used'] = 'of quota used';
$string['email_disk_warning'] = 'Disk space warning';
$string['email_diskusage_urgency'] = 'has exceeded {$a}% of the assigned disk space. Review the recommendations below for platform';
$string['email_disk_summary'] = 'Disk usage summary';
$string['email_used_space'] = 'Used space';
$string['email_assigned_quota'] = 'Assigned quota';
$string['email_available_space'] = 'Available space';
$string['email_space_distribution'] = 'Space distribution by category';
$string['email_percent_of_total'] = '% of total';
$string['email_recommendations_title'] = 'Recommendations to free up space';
$string['email_rec_backups'] = 'Reduce backups';
$string['email_rec_backups_desc'] = 'Lower the number of automatic backups per course (currently: {$a})';
$string['email_rec_files'] = 'Clean files';
$string['email_rec_files_desc'] = 'Remove old unused files using the file cleanup tool';
$string['email_rec_courses'] = 'Review courses';
$string['email_rec_courses_desc'] = 'Archive or clean up the largest courses listed above';
$string['email_rec_cache'] = 'Purge cache';
$string['email_rec_cache_desc'] = 'Clear the system cache to free up temporary space';
$string['email_disk_disclaimer'] = 'If you need technical assistance, please do not reply to this email and contact your hosting administrator.';

// Course dedication section.
$string['top_courses_dedication'] = 'Top courses by student dedication';
$string['top_courses_dedication_desc'] = 'Courses with the highest percentage of student dedication time';
$string['dedication_details'] = 'Dedication details';
$string['dedication_time'] = 'Dedication time';
$string['avg_dedication'] = 'Average dedication';
$string['total_dedication'] = 'Total dedication';
$string['enrolled_students'] = 'Enrolled students';
$string['active_students'] = 'Active students';
$string['dedication_percent'] = 'Dedication %';
$string['dedication_per_student'] = 'Avg. per student';
$string['tooltip_dedication'] = 'Dedication time is calculated based on user session activity in the course. A session ends when there is no activity for the configured session limit.';
$string['tooltip_dedication_percent'] = 'Percentage of dedication represents how much time students dedicate to this course compared to others.';
$string['no_dedication_data'] = 'No dedication data available. There is no activity recorded in the selected period.';
$string['view_dedication_report'] = 'View full dedication report';
$string['hours'] = 'hours';
$string['minutes'] = 'minutes';
$string['seconds'] = 'seconds';
$string['dedication_session_limit'] = 'Session limit';
$string['configdedication_session_limit'] = 'Maximum time of inactivity before a session is considered closed. Longer limits may overestimate dedication time.';
$string['dedication_last_calculated'] = 'Last calculated';
$string['course_dedication_rank'] = 'Rank';
$string['dedicationsettings'] = 'Dedication settings';
$string['dedicationsettingsinfo'] = 'Configure how student dedication time is calculated.';
