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
 * Scheduled task for disk usage notification.
 *
 * @package     report_usage_monitor
 * @copyright   2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_disk extends \core\task\scheduled_task {

    /**
     * Returns task name.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('processdisknotificationtask', 'report_usage_monitor');
    }

    /**
     * Execute the scheduled task.
     *
     * @return bool
     */
    public function execute(): bool {
        global $CFG;
        require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

        mtrace("Starting disk usage notification task...");

        $result = $this->process_disk_notification();

        mtrace("Disk usage notification task completed.");

        return $result;
    }

    /**
     * Process disk usage notification.
     *
     * @return bool
     */
    private function process_disk_notification(): bool {
        $config = get_config('report_usage_monitor');

        // Calculate disk metrics.
        $quota_bytes = ((int)($config->disk_quota ?? 10)) * 1024 * 1024 * 1024;
        $usage_bytes = ((int)($config->totalusagereadable ?? 0)) + ((int)($config->totalusagereadabledb ?? 0));

        // Validate quota.
        if ($quota_bytes <= 0) {
            mtrace("  Disk quota not configured. Skipping notification.");
            return true;
        }

        $percentage = ($usage_bytes / $quota_bytes) * 100;
        $warning_level = (int)($config->disk_warning_level ?? 90);

        mtrace("  Disk usage: " . display_size($usage_bytes) . " / " . display_size($quota_bytes) . " ({$percentage}%)");
        mtrace("  Warning level: {$warning_level}%");

        // Check if below warning level.
        if ($percentage < $warning_level) {
            mtrace("  Usage ({$percentage}%) is below warning level ({$warning_level}%). No notification needed.");
            return true;
        }

        // Check notification interval.
        $interval = notification_helper::calculate_notification_interval($percentage, 'disk');
        $last_notification = (int)get_config('report_usage_monitor', 'last_notificationdisk_time');
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
        mtrace("  Sending disk usage notification...");

        $email_data = notification_helper::build_disk_email_data($quota_bytes, $usage_bytes, $percentage);
        $result = notification_helper::send_notification('disk', $email_data);

        if ($result) {
            set_config('last_notificationdisk_time', $current_time, 'report_usage_monitor');
            mtrace("  Notification sent successfully.");
        } else {
            mtrace("  ERROR: Failed to send notification.");
        }

        return $result;
    }
}
