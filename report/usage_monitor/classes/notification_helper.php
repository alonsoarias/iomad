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
 * Notification helper class for Usage Monitor.
 *
 * Centralizes notification logic and email generation.
 *
 * @package    report_usage_monitor
 * @copyright  2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for generating and sending notifications.
 */
class notification_helper {

    /** @var int Critical threshold level */
    const LEVEL_CRITICAL = 90;

    /** @var int Warning threshold level */
    const LEVEL_WARNING = 70;

    /** @var int Low threshold level */
    const LEVEL_LOW = 50;

    /**
     * Get severity level based on percentage.
     *
     * @param float $percentage Current usage percentage
     * @return string Severity level: critical, warning, normal
     */
    public static function get_severity_level(float $percentage): string {
        if ($percentage >= self::LEVEL_CRITICAL) {
            return 'critical';
        } else if ($percentage >= self::LEVEL_WARNING) {
            return 'warning';
        }
        return 'normal';
    }

    /**
     * Get CSS class for severity level.
     *
     * @param string $level Severity level
     * @return string CSS class name
     */
    public static function get_severity_class(string $level): string {
        $classes = [
            'critical' => 'warning-level-high',
            'warning' => 'warning-level-medium',
            'normal' => 'warning-level-low',
        ];
        return $classes[$level] ?? 'warning-level-low';
    }

    /**
     * Get color for severity level.
     *
     * @param string $level Severity level
     * @return string Hex color code
     */
    public static function get_severity_color(string $level): string {
        $colors = [
            'critical' => '#e74c3c',
            'warning' => '#f39c12',
            'normal' => '#27ae60',
        ];
        return $colors[$level] ?? '#3498db';
    }

    /**
     * Calculate notification interval based on severity.
     *
     * @param float $percentage Current usage percentage
     * @param string $type Type of notification (disk, users)
     * @return int Interval in seconds
     */
    public static function calculate_notification_interval(float $percentage, string $type = 'disk'): int {
        if (!is_numeric($percentage)) {
            return PHP_INT_MAX;
        }

        if ($type === 'disk') {
            // Use string keys to avoid float to int implicit conversion
            $thresholds = [
                '99.9' => 12 * 3600,     // 12 hours for critical (>99.9%)
                '98.5' => 24 * 3600,     // 1 day for very high (>98.5%)
                '95' => 2 * 86400,       // 2 days for high (>95%)
                '90' => 5 * 86400,       // 5 days for warning (>90%)
            ];
        } else {
            $thresholds = [
                '100' => 24 * 3600,      // 1 day when exceeded
                '90' => 3 * 86400,       // 3 days when >90%
                '80' => 7 * 86400,       // 1 week when >80%
            ];
        }

        foreach ($thresholds as $threshold => $interval) {
            // Cast string key back to float for comparison
            if ($percentage >= (float)$threshold) {
                return $interval;
            }
        }

        return PHP_INT_MAX;
    }

    /**
     * Get user history as structured data array.
     *
     * @param int $days Number of days to include
     * @return array Array of user history data
     */
    public static function get_user_history_data(int $days = 7): array {
        global $CFG;

        require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

        $config = get_config('report_usage_monitor');
        $threshold = (int)($config->max_daily_users_threshold ?? 100);

        return get_historical_user_data($days, $threshold);
    }

    /**
     * Generate user history data rows for email.
     *
     * @deprecated Use get_user_history_data() and Mustache templates instead
     * @param int $days Number of days to include
     * @return string HTML table rows
     */
    public static function get_user_history_rows(int $days = 7): string {
        $data = self::get_user_history_data($days);

        if (empty($data)) {
            return '<tr><td colspan="3" style="text-align: center;">No data available</td></tr>';
        }

        $rows = '';
        foreach ($data as $row) {
            $color = $row['percent'] >= 90 ? '#e74c3c' : ($row['percent'] >= 70 ? '#f39c12' : '#27ae60');
            $rows .= "<tr>
                <td>{$row['fecha']}</td>
                <td>{$row['usuarios']}</td>
                <td style=\"color: {$color}; font-weight: bold;\">{$row['percent']}%</td>
            </tr>";
        }

        return $rows;
    }

    /**
     * Get top courses as structured data array.
     *
     * @param int $limit Number of courses to include
     * @return array Array of course data
     */
    public static function get_top_courses_data(int $limit = 5): array {
        global $CFG;

        require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

        $courses = get_largest_courses($limit);

        return get_top_courses_data($courses);
    }

    /**
     * Generate top courses rows for email.
     *
     * @deprecated Use get_top_courses_data() and Mustache templates instead
     * @param int $limit Number of courses to include
     * @return string HTML table rows
     */
    public static function get_top_courses_rows(int $limit = 5): string {
        $data = self::get_top_courses_data($limit);

        if (empty($data)) {
            return '<tr><td colspan="3" style="text-align: center;">No data available</td></tr>';
        }

        $rows = '';
        foreach ($data as $row) {
            $name = $row['fullname'];
            if (strlen($name) > 40) {
                $name = substr($name, 0, 37) . '...';
            }

            $rows .= "<tr>
                <td>{$name}</td>
                <td>{$row['totalsize']}</td>
                <td>{$row['percentage']}%</td>
            </tr>";
        }

        return $rows;
    }

    /**
     * Build email data object for disk notification.
     *
     * @param int $quota_bytes Disk quota in bytes
     * @param int $usage_bytes Current usage in bytes
     * @param float $percentage Current percentage
     * @return \stdClass Email data object
     */
    public static function build_disk_email_data(int $quota_bytes, int $usage_bytes, float $percentage): \stdClass {
        global $CFG, $SITE, $DB;

        $config = get_config('report_usage_monitor');
        $dir_analysis = json_decode($config->dir_analysis ?? '{}', true) ?: [];

        $user_threshold = (int)($config->max_daily_users_threshold ?? 100);
        $users_today = (int)($config->totalusersdaily ?? 0);
        $user_percent = $user_threshold > 0 ? round(($users_today / $user_threshold) * 100, 1) : 0;

        $available_bytes = max(0, $quota_bytes - $usage_bytes);
        $available_percent = $quota_bytes > 0 ? round(($available_bytes / $quota_bytes) * 100, 1) : 0;

        $total_usage = $usage_bytes > 0 ? $usage_bytes : 1;

        $severity = self::get_severity_level($percentage);

        $data = new \stdClass();
        $data->sitename = format_string($SITE->fullname);
        $data->siteurl = $CFG->wwwroot;
        $data->referer = $CFG->wwwroot . '/report/usage_monitor/index.php';
        $data->lastday = date('d/m/Y H:i');
        $data->percentage = round($percentage, 1);
        $data->warning_level_class = self::get_severity_class($severity);

        // Disk info.
        $data->diskusage = display_size($usage_bytes);
        $data->quotadisk = display_size($quota_bytes);
        $data->available_space = display_size($available_bytes);
        $data->available_percent = $available_percent;

        // Directory breakdown.
        $data->databasesize = display_size($dir_analysis['database'] ?? 0);
        $data->db_percent = round((($dir_analysis['database'] ?? 0) / $total_usage) * 100, 1);
        $data->filedir_size = display_size($dir_analysis['filedir'] ?? 0);
        $data->filedir_percent = round((($dir_analysis['filedir'] ?? 0) / $total_usage) * 100, 1);
        $data->cache_size = display_size($dir_analysis['cache'] ?? 0);
        $data->cache_percent = round((($dir_analysis['cache'] ?? 0) / $total_usage) * 100, 1);
        $data->other_size = display_size($dir_analysis['others'] ?? 0);
        $data->other_percent = round((($dir_analysis['others'] ?? 0) / $total_usage) * 100, 1);

        // System info.
        $data->moodle_release = $CFG->release;
        $data->moodle_version = $CFG->version;
        $data->coursescount = $DB->count_records('course');
        $data->backupcount = get_config('backup', 'backup_auto_max_kept') ?? 0;

        // User info.
        $data->numberofusers = $users_today;
        $data->threshold = $user_threshold;
        $data->user_percent = $user_percent;

        // Top courses - structured data for Mustache.
        $data->top_courses = self::get_top_courses_data(5);
        $data->has_top_courses = !empty($data->top_courses);
        // Keep legacy HTML rows for backwards compatibility.
        $data->top_courses_rows = self::get_top_courses_rows(5);

        return $data;
    }

    /**
     * Build email data object for user limit notification.
     *
     * @param int $user_count Current user count
     * @param int $timestamp Date timestamp
     * @param float $percentage Current percentage
     * @return \stdClass Email data object
     */
    public static function build_user_email_data(int $user_count, int $timestamp, float $percentage): \stdClass {
        global $CFG, $SITE, $DB;

        $config = get_config('report_usage_monitor');
        $user_threshold = (int)($config->max_daily_users_threshold ?? 100);

        // Disk info.
        $disk_usage = ((int)($config->totalusagereadable ?? 0)) + ((int)($config->totalusagereadabledb ?? 0));
        $disk_quota = ((int)($config->disk_quota ?? 10)) * 1024 * 1024 * 1024;
        $disk_percent = $disk_quota > 0 ? round(($disk_usage / $disk_quota) * 100, 1) : 0;

        $excess = max(0, $user_count - $user_threshold);
        $severity = self::get_severity_level($percentage);

        // Estimate days to critical.
        $growth_rate = self::estimate_user_growth_rate();
        $days_to_critical = 30; // Default.
        if ($growth_rate > 0 && $percentage < 100) {
            $remaining = 100 - $percentage;
            $days_to_critical = max(1, round($remaining / ($growth_rate / 30)));
        }

        $data = new \stdClass();
        $data->sitename = format_string($SITE->fullname);
        $data->siteurl = $CFG->wwwroot;
        $data->referer = $CFG->wwwroot . '/report/usage_monitor/index.php';
        $data->lastday = is_numeric($timestamp) && $timestamp > 0 ? date('d/m/Y', $timestamp) : date('d/m/Y');
        $data->numberofusers = $user_count;
        $data->threshold = $user_threshold;
        $data->percentaje = round($percentage, 1);
        $data->excess_users = $excess;

        // Projection.
        $data->days_to_critical = $days_to_critical;
        $data->critical_threshold = 100;

        // System info.
        $data->moodle_release = $CFG->release;
        $data->moodle_version = $CFG->version;
        $data->courses_count = $DB->count_records('course');
        $data->backup_auto_max_kept = get_config('backup', 'backup_auto_max_kept') ?? 0;
        $data->diskusage = display_size($disk_usage);
        $data->quotadisk = display_size($disk_quota);
        $data->disk_percent = $disk_percent;

        // Historical data - structured for Mustache.
        $data->user_history = self::get_user_history_data(7);
        $data->has_user_history = !empty($data->user_history);
        // Keep legacy HTML rows for backwards compatibility.
        $data->historical_data_rows = self::get_user_history_rows(7);

        return $data;
    }

    /**
     * Estimate user growth rate from historical data.
     *
     * @return float Monthly growth rate percentage
     */
    private static function estimate_user_growth_rate(): float {
        global $DB;

        $month_ago = time() - (30 * 86400);
        $records = $DB->get_records_sql(
            "SELECT value FROM {report_usage_monitor_history}
             WHERE type = 'users' AND timecreated > ?
             ORDER BY timecreated ASC",
            [$month_ago]
        );

        if (count($records) < 2) {
            return 0;
        }

        $values = array_column((array)$records, 'value');
        $first = reset($values);
        $last = end($values);

        if ($first <= 0) {
            return 0;
        }

        return (($last - $first) / $first) * 100;
    }

    /**
     * Build unified email data for notification.
     *
     * @param string $type Notification type (disk, users, or both)
     * @param \stdClass|null $disk_data Disk notification data
     * @param \stdClass|null $user_data User notification data
     * @return \stdClass Unified email data
     */
    public static function build_unified_email_data(
        string $type,
        ?\stdClass $disk_data = null,
        ?\stdClass $user_data = null
    ): \stdClass {
        global $CFG, $SITE, $DB;

        $config = get_config('report_usage_monitor');
        $data = new \stdClass();

        // Common data.
        $data->lang = current_language();
        $data->sitename = format_string($SITE->fullname);
        $data->siteurl = $CFG->wwwroot;
        $data->dashboard_url = $CFG->wwwroot . '/report/usage_monitor/index.php';
        $data->report_date = date('d/m/Y H:i');
        $data->moodle_release = $CFG->release;
        $data->courses_count = $DB->count_records('course');
        $data->backup_count = get_config('backup', 'backup_auto_max_kept') ?? 0;

        // Alert flags.
        $data->alert_users = ($type === 'users' || $type === 'both');
        $data->alert_disk = ($type === 'disk' || $type === 'both');
        $data->has_recommendations = true;

        // Header colors based on alert type.
        if ($data->alert_users && $data->alert_disk) {
            $data->header_color_start = '#8e44ad';
            $data->header_color_end = '#9b59b6';
        } else if ($data->alert_users) {
            $data->header_color_start = '#c0392b';
            $data->header_color_end = '#e74c3c';
        } else {
            $data->header_color_start = '#d35400';
            $data->header_color_end = '#e67e22';
        }

        // User data.
        if ($data->alert_users && $user_data) {
            $data->users_today = $user_data->numberofusers ?? 0;
            $data->user_threshold = $user_data->threshold ?? 100;
            $data->user_percent = round($user_data->percentaje ?? 0, 1);
            $data->user_percent_capped = min(100, $data->user_percent);
            $data->excess_users = max(0, $data->users_today - $data->user_threshold);

            // Structured data for Mustache loops.
            $data->user_history = self::get_user_history_data(7);
            $data->has_user_history = !empty($data->user_history);
        }

        // Disk data.
        if ($data->alert_disk && $disk_data) {
            $data->disk_usage = $disk_data->diskusage ?? '0 B';
            $data->disk_quota = $disk_data->quotadisk ?? '0 B';
            $data->disk_percent = round($disk_data->percentage ?? 0, 1);
            $data->disk_percent_capped = min(100, $data->disk_percent);
            $data->available_space = $disk_data->available_space ?? '0 B';
            $data->available_percent = $disk_data->available_percent ?? 0;
            $data->database_size = $disk_data->databasesize ?? '0 B';
            $data->database_percent = $disk_data->db_percent ?? 0;
            $data->filedir_size = $disk_data->filedir_size ?? '0 B';
            $data->filedir_percent = $disk_data->filedir_percent ?? 0;
            $data->cache_size = $disk_data->cache_size ?? '0 B';
            $data->cache_percent = $disk_data->cache_percent ?? 0;
            $data->other_size = $disk_data->other_size ?? '0 B';
            $data->other_percent = $disk_data->other_percent ?? 0;

            // Structured data for Mustache loops.
            $data->top_courses = self::get_top_courses_data(5);
            $data->has_top_courses = !empty($data->top_courses);
        }

        return $data;
    }

    /**
     * Render an email template using Mustache.
     *
     * @param string $template_name Full template name (e.g., 'report_usage_monitor/email_notification')
     * @param \stdClass $data Template data
     * @return string Rendered HTML
     */
    public static function render_email_template(string $template_name, \stdClass $data): string {
        global $PAGE, $OUTPUT, $CFG;

        // Ensure PAGE is set up for CLI context.
        if (!isset($PAGE->context) || $PAGE->context === null) {
            $PAGE->set_context(\context_system::instance());
        }

        // Convert data object to array for Mustache.
        $context = (array)$data;

        try {
            // Use the global OUTPUT renderer to render the template.
            $html = $OUTPUT->render_from_template($template_name, $context);
        } catch (\Exception $e) {
            // Fallback to legacy language string templates if Mustache fails.
            debugging('notification_helper::render_email_template - Template error: ' . $e->getMessage(), DEBUG_DEVELOPER);

            // Determine legacy template key from alert type.
            if (!empty($data->alert_disk) && empty($data->alert_users)) {
                $html = get_string('messagehtml_diskusage', 'report_usage_monitor', $data);
            } else {
                $html = get_string('messagehtml_userlimit', 'report_usage_monitor', $data);
            }
        }

        return $html;
    }

    /**
     * Send notification email.
     *
     * @param string $type Notification type (disk, users)
     * @param \stdClass $data Email data
     * @return bool Success status
     */
    public static function send_notification(string $type, \stdClass $data): bool {
        global $DB, $CFG, $SITE, $PAGE, $OUTPUT;

        $config = get_config('report_usage_monitor');
        $to_email = $config->email ?? '';

        if (empty($to_email) || !filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
            debugging('notification_helper::send_notification - Invalid email address', DEBUG_DEVELOPER);
            return false;
        }

        // Build unified email data.
        $unified_data = self::build_unified_email_data($type,
            ($type === 'disk') ? $data : null,
            ($type === 'users') ? $data : null
        );

        // Subject line.
        $subject = get_string('email_notification_subject', 'report_usage_monitor') . ' ' . $unified_data->sitename;

        // Render unified Mustache template.
        $message_html = self::render_email_template('report_usage_monitor/email_notification', $unified_data);

        // Get primary site administrator as base for recipient user object.
        // This ensures all required Moodle user properties are properly set.
        $admin = get_admin();
        if (!$admin) {
            debugging('notification_helper::send_notification - No admin user found', DEBUG_DEVELOPER);
            return false;
        }

        // Clone admin user and override email with configured recipient.
        $user = clone $admin;
        $user->email = $to_email;

        // Get noreply user as sender (more reliable than support user).
        $from = \core_user::get_noreply_user();
        $from->firstname = format_string($SITE->shortname);
        $from->lastname = 'Usage Monitor';

        // Strip HTML for plain text version.
        $message_text = html_to_text($message_html, 0, false);

        // Send email using Moodle's email function.
        $result = email_to_user($user, $from, $subject, $message_text, $message_html, '', '', true);

        if ($result) {
            self::log_notification($type, $data);
        }

        return $result;
    }

    /**
     * Log notification to history table.
     *
     * @param string $type Notification type
     * @param \stdClass $data Notification data
     */
    private static function log_notification(string $type, \stdClass $data): void {
        global $DB;

        if (!$DB->get_manager()->table_exists('report_usage_monitor_history')) {
            return;
        }

        $record = new \stdClass();
        $record->type = $type;

        if ($type === 'disk') {
            $record->percentage = $data->percentage;
            $config = get_config('report_usage_monitor');
            $record->value = ((int)($config->totalusagereadable ?? 0)) + ((int)($config->totalusagereadabledb ?? 0));
            $record->threshold = ((int)($config->disk_quota ?? 10)) * 1024 * 1024 * 1024;
        } else {
            $record->percentage = $data->percentaje;
            $record->value = $data->numberofusers;
            $record->threshold = $data->threshold;
        }

        $record->timecreated = time();

        try {
            $DB->insert_record('report_usage_monitor_history', $record);
        } catch (\Exception $e) {
            debugging('notification_helper::log_notification - ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
