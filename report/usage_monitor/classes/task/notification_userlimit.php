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

namespace report_usage_monitor\task;

defined('MOODLE_INTERNAL') || die();

use report_usage_monitor\notification_helper;

/**
 * Scheduled task for user limit notification.
 *
 * @package     report_usage_monitor
 * @copyright   2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_userlimit extends \core\task\scheduled_task {

    /**
     * Returns task name.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('processuserlimitnotificationtask', 'report_usage_monitor');
    }

    /**
     * Execute the scheduled task.
     *
     * @return bool
     */
    public function execute(): bool {
        global $CFG;
        require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

        mtrace("Starting user limit notification task...");

        $result = $this->process_user_notification();

        mtrace("User limit notification task completed.");

        return $result;
    }

    /**
     * Process user limit notification.
     *
     * @return bool
     */
    private function process_user_notification(): bool {
        global $DB;

        $config = get_config('report_usage_monitor');
        $user_threshold = (int)($config->max_daily_users_threshold ?? 100);
        $warning_level = (int)($config->users_warning_level ?? 90);

        // Validate threshold.
        if ($user_threshold <= 0) {
            mtrace("  User threshold not configured. Skipping notification.");
            return true;
        }

        mtrace("  User threshold: {$user_threshold}");
        mtrace("  Warning level: {$warning_level}%");

        // Get recent user activity using portable timestamp arithmetic.
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
            mtrace("  No user activity data found for the last day.");
            return true;
        }

        $user_count = (int)$record->user_count;
        $timestamp = (int)$record->timestamp_date;
        $percentage = ($user_count / $user_threshold) * 100;

        mtrace("  Users today: {$user_count} / {$user_threshold} ({$percentage}%)");

        // Check if below warning level.
        if ($percentage < $warning_level) {
            mtrace("  Usage ({$percentage}%) is below warning level ({$warning_level}%). No notification needed.");
            return true;
        }

        // Check notification interval.
        $interval = notification_helper::calculate_notification_interval($percentage, 'users');
        $last_notification = (int)get_config('report_usage_monitor', 'last_notificationusers_time');
        $current_time = time();
        $time_since_last = $current_time - $last_notification;

        mtrace("  Notification interval: " . format_time($interval));
        mtrace("  Time since last notification: " . format_time($time_since_last));

        if ($time_since_last < $interval) {
            $next_possible = ($last_notification + $interval) - $current_time;
            mtrace("  Too soon for next notification. Next possible in: " . format_time($next_possible));
            return true;
        }

        // Send notification.
        mtrace("  Sending user limit notification...");

        $email_data = notification_helper::build_user_email_data($user_count, $timestamp, $percentage);
        $result = notification_helper::send_notification('users', $email_data);

        if ($result) {
            set_config('last_notificationusers_time', $current_time, 'report_usage_monitor');
            mtrace("  Notification sent successfully.");
        } else {
            mtrace("  ERROR: Failed to send notification.");
        }

        return $result;
    }
}
