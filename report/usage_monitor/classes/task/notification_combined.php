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

namespace report_usage_monitor\task;

defined('MOODLE_INTERNAL') || die();

use report_usage_monitor\notification_helper;

/**
 * Unified scheduled task for usage monitor notifications.
 *
 * This task handles both disk usage and user limit notifications in a single
 * unified notification. When either or both thresholds are exceeded, a single
 * email is sent containing all relevant alerts.
 *
 * @package     report_usage_monitor
 * @copyright   2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_combined extends \core\task\scheduled_task {

    /**
     * Returns task name.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('processcombinednotificationtask', 'report_usage_monitor');
    }

    /**
     * Execute the scheduled task.
     *
     * @return bool
     */
    public function execute(): bool {
        global $CFG;
        require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

        mtrace("Starting combined usage monitor notification task...");

        $result = $this->process_combined_notification();

        mtrace("Combined notification task completed.");

        return $result;
    }

    /**
     * Process combined notification for both disk and user limits.
     *
     * @return bool
     */
    private function process_combined_notification(): bool {
        $config = get_config('report_usage_monitor');

        // Check if email is configured.
        $to_email = $config->email ?? '';
        if (empty($to_email) || !filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
            mtrace("  Email not configured or invalid. Skipping notification.");
            return true;
        }

        // Check disk usage status.
        $disk_alert = $this->check_disk_threshold($config);

        // Check user limit status.
        $user_alert = $this->check_user_threshold($config);

        // Determine notification type.
        $send_disk = $disk_alert['should_notify'];
        $send_users = $user_alert['should_notify'];

        if (!$send_disk && !$send_users) {
            mtrace("  No thresholds exceeded or too soon for notification. Nothing to send.");
            return true;
        }

        // Determine notification type.
        if ($send_disk && $send_users) {
            $type = 'both';
            mtrace("  Both thresholds exceeded. Sending combined notification.");
        } else if ($send_disk) {
            $type = 'disk';
            mtrace("  Disk threshold exceeded. Sending disk notification.");
        } else {
            $type = 'users';
            mtrace("  User threshold exceeded. Sending user notification.");
        }

        // Build data for notification.
        $disk_data = $send_disk ? $disk_alert['data'] : null;
        $user_data = $send_users ? $user_alert['data'] : null;

        // Send unified notification.
        mtrace("  Sending unified notification (type: {$type})...");

        $result = $this->send_unified_notification($type, $disk_data, $user_data);

        if ($result) {
            // Update last notification timestamps.
            $current_time = time();
            if ($send_disk) {
                set_config('last_notificationdisk_time', $current_time, 'report_usage_monitor');
                mtrace("  Disk notification timestamp updated.");
            }
            if ($send_users) {
                set_config('last_notificationusers_time', $current_time, 'report_usage_monitor');
                mtrace("  User notification timestamp updated.");
            }
            mtrace("  Notification sent successfully.");
        } else {
            mtrace("  ERROR: Failed to send notification.");
        }

        return $result;
    }

    /**
     * Check disk usage threshold and determine if notification should be sent.
     *
     * @param object $config Plugin configuration
     * @return array Array with 'should_notify' boolean and 'data' object
     */
    private function check_disk_threshold($config): array {
        $result = ['should_notify' => false, 'data' => null];

        // Calculate disk metrics.
        $quota_bytes = ((int)($config->disk_quota ?? 10)) * 1024 * 1024 * 1024;
        $usage_bytes = ((int)($config->totalusagereadable ?? 0)) + ((int)($config->totalusagereadabledb ?? 0));

        // Validate quota.
        if ($quota_bytes <= 0) {
            mtrace("  [Disk] Quota not configured. Skipping disk check.");
            return $result;
        }

        $percentage = ($usage_bytes / $quota_bytes) * 100;
        $warning_level = (int)($config->disk_warning_level ?? 90);

        mtrace("  [Disk] Usage: " . display_size($usage_bytes) . " / " . display_size($quota_bytes) .
               " (" . round($percentage, 1) . "%) - Warning level: {$warning_level}%");

        // Check if below warning level.
        if ($percentage < $warning_level) {
            mtrace("  [Disk] Below warning level. No notification needed.");
            return $result;
        }

        // Check notification interval.
        $interval = notification_helper::calculate_notification_interval($percentage, 'disk');
        $last_notification = (int)get_config('report_usage_monitor', 'last_notificationdisk_time');
        $current_time = time();
        $time_since_last = $current_time - $last_notification;

        mtrace("  [Disk] Interval: " . format_time($interval) . " - Time since last: " . format_time($time_since_last));

        if ($time_since_last < $interval) {
            $next_possible = ($last_notification + $interval) - $current_time;
            mtrace("  [Disk] Too soon. Next possible in: " . format_time($next_possible));
            return $result;
        }

        // Notification should be sent.
        $result['should_notify'] = true;
        $result['data'] = notification_helper::build_disk_email_data($quota_bytes, $usage_bytes, $percentage);

        return $result;
    }

    /**
     * Check user limit threshold and determine if notification should be sent.
     *
     * User notifications are only sent at 8 AM to maintain the original schedule.
     *
     * @param object $config Plugin configuration
     * @return array Array with 'should_notify' boolean and 'data' object
     */
    private function check_user_threshold($config): array {
        global $DB;

        $result = ['should_notify' => false, 'data' => null];

        // User notifications are only checked at 8 AM (between 8:00 and 8:59).
        $current_hour = (int)date('G');
        if ($current_hour !== 8) {
            mtrace("  [Users] Current hour is {$current_hour}. User notifications only at 8 AM. Skipping.");
            return $result;
        }

        $user_threshold = (int)($config->max_daily_users_threshold ?? 100);
        $warning_level = (int)($config->users_warning_level ?? 90);

        // Validate threshold.
        if ($user_threshold <= 0) {
            mtrace("  [Users] Threshold not configured. Skipping user check.");
            return $result;
        }

        // Get recent user activity.
        $yesterday_start = strtotime('yesterday midnight');
        $sql = "SELECT COUNT(DISTINCT userid) AS user_count,
                       (timecreated - (timecreated % 86400)) AS timestamp_date
                FROM {logstore_standard_log}
                WHERE action = 'loggedin'
                  AND timecreated >= :start_time
                GROUP BY (timecreated - (timecreated % 86400))
                ORDER BY timestamp_date DESC";

        $records = $DB->get_records_sql($sql, ['start_time' => $yesterday_start], 0, 1);
        $record = reset($records);

        if (!$record) {
            mtrace("  [Users] No user activity data found.");
            return $result;
        }

        $user_count = (int)$record->user_count;
        $timestamp = (int)$record->timestamp_date;
        $percentage = ($user_count / $user_threshold) * 100;

        mtrace("  [Users] Count: {$user_count} / {$user_threshold} (" . round($percentage, 1) .
               "%) - Warning level: {$warning_level}%");

        // Check if below warning level.
        if ($percentage < $warning_level) {
            mtrace("  [Users] Below warning level. No notification needed.");
            return $result;
        }

        // Check notification interval.
        $interval = notification_helper::calculate_notification_interval($percentage, 'users');
        $last_notification = (int)get_config('report_usage_monitor', 'last_notificationusers_time');
        $current_time = time();
        $time_since_last = $current_time - $last_notification;

        mtrace("  [Users] Interval: " . format_time($interval) . " - Time since last: " . format_time($time_since_last));

        if ($time_since_last < $interval) {
            $next_possible = ($last_notification + $interval) - $current_time;
            mtrace("  [Users] Too soon. Next possible in: " . format_time($next_possible));
            return $result;
        }

        // Notification should be sent.
        $result['should_notify'] = true;
        $result['data'] = notification_helper::build_user_email_data($user_count, $timestamp, $percentage);

        return $result;
    }

    /**
     * Send unified notification email.
     *
     * @param string $type Notification type: 'disk', 'users', or 'both'
     * @param \stdClass|null $disk_data Disk notification data
     * @param \stdClass|null $user_data User notification data
     * @return bool Success status
     */
    private function send_unified_notification(string $type, ?\stdClass $disk_data, ?\stdClass $user_data): bool {
        global $CFG, $SITE, $PAGE, $OUTPUT, $DB;

        $config = get_config('report_usage_monitor');
        $to_email = $config->email ?? '';

        // Ensure PAGE context is set for CLI.
        if (!isset($PAGE->context) || $PAGE->context === null) {
            $PAGE->set_context(\context_system::instance());
        }

        // Build unified email data.
        $unified_data = notification_helper::build_unified_email_data($type, $disk_data, $user_data);

        // Subject line.
        $subject = get_string('email_notification_subject', 'report_usage_monitor') . ' ' . $unified_data->sitename;

        // Render unified Mustache template.
        $message_html = notification_helper::render_email_template('report_usage_monitor/email_notification', $unified_data);

        // Get admin user as base for recipient.
        $admin = get_admin();
        if (!$admin) {
            mtrace("  ERROR: No admin user found.");
            return false;
        }

        // Clone admin and set recipient email.
        $user = clone $admin;
        $user->email = $to_email;

        // Get noreply user as sender.
        $from = \core_user::get_noreply_user();
        $from->firstname = format_string($SITE->shortname);
        $from->lastname = 'Usage Monitor';

        // Plain text version.
        $message_text = html_to_text($message_html, 0, false);

        // Send email.
        $result = email_to_user($user, $from, $subject, $message_text, $message_html, '', '', true);

        // Log notification to history.
        if ($result) {
            $this->log_notification($type, $disk_data, $user_data);
        }

        return $result;
    }

    /**
     * Log notification to history table.
     *
     * @param string $type Notification type
     * @param \stdClass|null $disk_data Disk data
     * @param \stdClass|null $user_data User data
     */
    private function log_notification(string $type, ?\stdClass $disk_data, ?\stdClass $user_data): void {
        global $DB;

        if (!$DB->get_manager()->table_exists('report_usage_monitor_history')) {
            return;
        }

        $config = get_config('report_usage_monitor');
        $current_time = time();

        // Log disk notification if applicable.
        if (($type === 'disk' || $type === 'both') && $disk_data) {
            $record = new \stdClass();
            $record->type = 'disk';
            $record->percentage = $disk_data->percentage;
            $record->value = ((int)($config->totalusagereadable ?? 0)) + ((int)($config->totalusagereadabledb ?? 0));
            $record->threshold = ((int)($config->disk_quota ?? 10)) * 1024 * 1024 * 1024;
            $record->timecreated = $current_time;

            try {
                $DB->insert_record('report_usage_monitor_history', $record);
            } catch (\Exception $e) {
                debugging('notification_combined::log_notification - Disk: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        // Log user notification if applicable.
        if (($type === 'users' || $type === 'both') && $user_data) {
            $record = new \stdClass();
            $record->type = 'users';
            $record->percentage = $user_data->percentaje;
            $record->value = $user_data->numberofusers;
            $record->threshold = $user_data->threshold;
            $record->timecreated = $current_time;

            try {
                $DB->insert_record('report_usage_monitor_history', $record);
            } catch (\Exception $e) {
                debugging('notification_combined::log_notification - Users: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
    }
}
