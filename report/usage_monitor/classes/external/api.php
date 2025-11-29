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
 * External API for Usage Monitor - Refactored with modern Moodle patterns.
 *
 * @package    report_usage_monitor
 * @copyright  2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage_monitor\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use context_system;
use stdClass;

/**
 * External API class for report_usage_monitor plugin.
 *
 * Provides REST API endpoints for monitoring platform usage including
 * disk space, user counts, and system health metrics.
 */
class api extends external_api {

    // =========================================================================
    // GET SYSTEM HEALTH - New comprehensive endpoint
    // =========================================================================

    /**
     * Parameters for get_system_health.
     *
     * @return external_function_parameters
     */
    public static function get_system_health_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Get comprehensive system health status.
     *
     * Returns a complete overview of system health including disk usage,
     * user metrics, alerts, and recommended actions.
     *
     * @return array System health data
     */
    public static function get_system_health(): array {
        global $DB, $CFG, $SITE;

        // Validate context and capability.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('report/usage_monitor:view', $context);

        $config = get_config('report_usage_monitor');

        // Calculate disk metrics.
        $disk_usage = ((int)($config->totalusagereadable ?? 0)) + ((int)($config->totalusagereadabledb ?? 0));
        $disk_quota = ((int)($config->disk_quota ?? 10)) * 1024 * 1024 * 1024;
        $disk_percent = $disk_quota > 0 ? ($disk_usage / $disk_quota) * 100 : 0;

        // Calculate user metrics.
        $users_today = (int)($config->totalusersdaily ?? 0);
        $user_threshold = (int)($config->max_daily_users_threshold ?? 100);
        $users_percent = $user_threshold > 0 ? ($users_today / $user_threshold) * 100 : 0;

        // Determine health status.
        $disk_status = self::get_status_level($disk_percent);
        $users_status = self::get_status_level($users_percent);
        $overall_status = self::get_overall_status($disk_status, $users_status);

        // Get active alerts.
        $alerts = self::get_active_alerts($disk_percent, $users_percent, $config);

        // Get recommended actions.
        $actions = self::get_recommended_actions($disk_percent, $users_percent, $config);

        return [
            'status' => $overall_status,
            'timestamp' => time(),
            'site' => [
                'name' => format_string($SITE->fullname),
                'url' => $CFG->wwwroot,
                'moodle_version' => $CFG->release,
            ],
            'disk' => [
                'status' => $disk_status,
                'used_bytes' => $disk_usage,
                'used_readable' => display_size($disk_usage),
                'quota_bytes' => $disk_quota,
                'quota_readable' => display_size($disk_quota),
                'available_bytes' => max(0, $disk_quota - $disk_usage),
                'available_readable' => display_size(max(0, $disk_quota - $disk_usage)),
                'percentage' => round($disk_percent, 2),
                'growth_rate_monthly' => calculate_growth_rate('disk'),
                'days_to_threshold' => self::safe_project_limit_date($disk_usage, $disk_quota * 0.9, calculate_growth_rate('disk')),
            ],
            'users' => [
                'status' => $users_status,
                'current' => $users_today,
                'threshold' => $user_threshold,
                'percentage' => round($users_percent, 2),
                'max_90_days' => (int)($config->max_userdaily_for_90_days_users ?? 0),
                'max_90_days_date' => self::format_timestamp($config->max_userdaily_for_90_days_date ?? 0),
                'growth_rate_monthly' => calculate_growth_rate('users'),
                'days_to_threshold' => self::safe_project_limit_date($users_today, $user_threshold * 0.9, calculate_growth_rate('users')),
            ],
            'alerts' => $alerts,
            'actions' => $actions,
            'last_calculations' => [
                'disk' => self::format_timestamp($config->lastexecutioncalculate ?? 0),
                'users' => self::format_timestamp($config->lastexecution ?? 0),
            ],
        ];
    }

    /**
     * Return structure for get_system_health.
     *
     * @return external_single_structure
     */
    public static function get_system_health_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'Overall system status: healthy, warning, critical'),
            'timestamp' => new external_value(PARAM_INT, 'Response timestamp'),
            'site' => new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'Site name'),
                'url' => new external_value(PARAM_URL, 'Site URL'),
                'moodle_version' => new external_value(PARAM_TEXT, 'Moodle version'),
            ]),
            'disk' => new external_single_structure([
                'status' => new external_value(PARAM_ALPHA, 'Disk status: healthy, warning, critical'),
                'used_bytes' => new external_value(PARAM_INT, 'Used disk space in bytes'),
                'used_readable' => new external_value(PARAM_TEXT, 'Human-readable used space'),
                'quota_bytes' => new external_value(PARAM_INT, 'Disk quota in bytes'),
                'quota_readable' => new external_value(PARAM_TEXT, 'Human-readable quota'),
                'available_bytes' => new external_value(PARAM_INT, 'Available space in bytes'),
                'available_readable' => new external_value(PARAM_TEXT, 'Human-readable available space'),
                'percentage' => new external_value(PARAM_FLOAT, 'Usage percentage'),
                'growth_rate_monthly' => new external_value(PARAM_FLOAT, 'Monthly growth rate percentage'),
                'days_to_threshold' => new external_value(PARAM_INT, 'Estimated days to reach warning threshold'),
            ]),
            'users' => new external_single_structure([
                'status' => new external_value(PARAM_ALPHA, 'Users status: healthy, warning, critical'),
                'current' => new external_value(PARAM_INT, 'Current daily users'),
                'threshold' => new external_value(PARAM_INT, 'User threshold'),
                'percentage' => new external_value(PARAM_FLOAT, 'Usage percentage'),
                'max_90_days' => new external_value(PARAM_INT, 'Maximum users in last 90 days'),
                'max_90_days_date' => new external_value(PARAM_TEXT, 'Date of maximum users'),
                'growth_rate_monthly' => new external_value(PARAM_FLOAT, 'Monthly growth rate percentage'),
                'days_to_threshold' => new external_value(PARAM_INT, 'Estimated days to reach warning threshold'),
            ]),
            'alerts' => new external_multiple_structure(
                new external_single_structure([
                    'type' => new external_value(PARAM_ALPHA, 'Alert type: disk, users'),
                    'level' => new external_value(PARAM_ALPHA, 'Alert level: warning, critical'),
                    'message' => new external_value(PARAM_TEXT, 'Alert message'),
                    'percentage' => new external_value(PARAM_FLOAT, 'Current percentage'),
                ])
            ),
            'actions' => new external_multiple_structure(
                new external_single_structure([
                    'priority' => new external_value(PARAM_ALPHA, 'Action priority: high, medium, low'),
                    'category' => new external_value(PARAM_ALPHA, 'Action category'),
                    'description' => new external_value(PARAM_TEXT, 'Action description'),
                ])
            ),
            'last_calculations' => new external_single_structure([
                'disk' => new external_value(PARAM_TEXT, 'Last disk calculation time'),
                'users' => new external_value(PARAM_TEXT, 'Last users calculation time'),
            ]),
        ]);
    }

    // =========================================================================
    // GET TREND ANALYSIS - New analytics endpoint
    // =========================================================================

    /**
     * Parameters for get_trend_analysis.
     *
     * @return external_function_parameters
     */
    public static function get_trend_analysis_parameters(): external_function_parameters {
        return new external_function_parameters([
            'days' => new external_value(PARAM_INT, 'Number of days to analyze (7, 30, 90)', VALUE_DEFAULT, 30),
            'type' => new external_value(PARAM_ALPHA, 'Analysis type: disk, users, both', VALUE_DEFAULT, 'both'),
        ]);
    }

    /**
     * Get trend analysis for disk or user metrics.
     *
     * @param int $days Number of days to analyze
     * @param string $type Type of analysis
     * @return array Trend analysis data
     */
    public static function get_trend_analysis(int $days = 30, string $type = 'both'): array {
        global $DB;

        // Validate context and capability.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('report/usage_monitor:view', $context);

        // Validate parameters.
        $params = self::validate_parameters(self::get_trend_analysis_parameters(), [
            'days' => $days,
            'type' => $type,
        ]);

        $days = min(max($params['days'], 7), 90);
        $type = $params['type'];
        $time_threshold = time() - ($days * 86400);

        $result = [
            'period_days' => $days,
            'period_start' => date('Y-m-d', $time_threshold),
            'period_end' => date('Y-m-d'),
        ];

        // Disk trends.
        if ($type === 'disk' || $type === 'both') {
            $disk_history = $DB->get_records_sql(
                "SELECT id, timecreated, value, percentage
                 FROM {report_usage_monitor_history}
                 WHERE type = 'disk' AND timecreated > ?
                 ORDER BY timecreated ASC",
                [$time_threshold]
            );

            $disk_data = [];
            foreach ($disk_history as $record) {
                $disk_data[] = [
                    'date' => date('Y-m-d', $record->timecreated),
                    'value_bytes' => (int)$record->value,
                    'value_readable' => display_size($record->value),
                    'percentage' => round($record->percentage, 2),
                ];
            }

            $result['disk'] = [
                'data_points' => count($disk_data),
                'history' => $disk_data,
                'summary' => self::calculate_trend_summary($disk_data, 'percentage'),
            ];
        }

        // User trends.
        if ($type === 'users' || $type === 'both') {
            $user_history = $DB->get_records_sql(
                "SELECT id, timecreated, value, percentage
                 FROM {report_usage_monitor_history}
                 WHERE type = 'users' AND timecreated > ?
                 ORDER BY timecreated ASC",
                [$time_threshold]
            );

            $user_data = [];
            foreach ($user_history as $record) {
                $user_data[] = [
                    'date' => date('Y-m-d', $record->timecreated),
                    'value' => (int)$record->value,
                    'percentage' => round($record->percentage, 2),
                ];
            }

            $result['users'] = [
                'data_points' => count($user_data),
                'history' => $user_data,
                'summary' => self::calculate_trend_summary($user_data, 'percentage'),
            ];
        }

        return $result;
    }

    /**
     * Return structure for get_trend_analysis.
     *
     * @return external_single_structure
     */
    public static function get_trend_analysis_returns(): external_single_structure {
        $history_structure = new external_single_structure([
            'date' => new external_value(PARAM_TEXT, 'Date'),
            'value_bytes' => new external_value(PARAM_INT, 'Value in bytes', VALUE_OPTIONAL),
            'value_readable' => new external_value(PARAM_TEXT, 'Human-readable value', VALUE_OPTIONAL),
            'value' => new external_value(PARAM_INT, 'Value', VALUE_OPTIONAL),
            'percentage' => new external_value(PARAM_FLOAT, 'Percentage'),
        ]);

        $summary_structure = new external_single_structure([
            'min' => new external_value(PARAM_FLOAT, 'Minimum value'),
            'max' => new external_value(PARAM_FLOAT, 'Maximum value'),
            'avg' => new external_value(PARAM_FLOAT, 'Average value'),
            'trend' => new external_value(PARAM_ALPHA, 'Trend direction: up, down, stable'),
            'change_percent' => new external_value(PARAM_FLOAT, 'Percentage change over period'),
        ]);

        $type_structure = new external_single_structure([
            'data_points' => new external_value(PARAM_INT, 'Number of data points'),
            'history' => new external_multiple_structure($history_structure),
            'summary' => $summary_structure,
        ]);

        return new external_single_structure([
            'period_days' => new external_value(PARAM_INT, 'Analysis period in days'),
            'period_start' => new external_value(PARAM_TEXT, 'Period start date'),
            'period_end' => new external_value(PARAM_TEXT, 'Period end date'),
            'disk' => $type_structure,
            'users' => $type_structure,
        ], 'Trend analysis results', VALUE_OPTIONAL);
    }

    // =========================================================================
    // UPDATE CONFIGURATION - Enhanced configuration endpoint
    // =========================================================================

    /**
     * Parameters for update_configuration.
     *
     * @return external_function_parameters
     */
    public static function update_configuration_parameters(): external_function_parameters {
        return new external_function_parameters([
            'settings' => new external_single_structure([
                'user_threshold' => new external_value(PARAM_INT, 'Daily user threshold', VALUE_OPTIONAL),
                'disk_quota_gb' => new external_value(PARAM_INT, 'Disk quota in GB', VALUE_OPTIONAL),
                'disk_warning_level' => new external_value(PARAM_INT, 'Disk warning level percentage (1-100)', VALUE_OPTIONAL),
                'users_warning_level' => new external_value(PARAM_INT, 'Users warning level percentage (1-100)', VALUE_OPTIONAL),
                'notification_email' => new external_value(PARAM_EMAIL, 'Notification email address', VALUE_OPTIONAL),
            ]),
        ]);
    }

    /**
     * Update plugin configuration.
     *
     * @param array $settings Configuration settings to update
     * @return array Update result
     */
    public static function update_configuration(array $settings): array {
        global $DB;

        // Validate context and capability.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('report/usage_monitor:manage', $context);

        // Validate parameters.
        $params = self::validate_parameters(self::update_configuration_parameters(), ['settings' => $settings]);
        $settings = $params['settings'];

        $result = [
            'success' => true,
            'updated' => [],
            'errors' => [],
        ];

        $transaction = $DB->start_delegated_transaction();

        try {
            // Update user threshold.
            if (isset($settings['user_threshold'])) {
                if ($settings['user_threshold'] > 0) {
                    set_config('max_daily_users_threshold', $settings['user_threshold'], 'report_usage_monitor');
                    $result['updated'][] = 'user_threshold';
                } else {
                    $result['errors'][] = get_string('error_user_threshold_negative', 'report_usage_monitor');
                }
            }

            // Update disk quota.
            if (isset($settings['disk_quota_gb'])) {
                if ($settings['disk_quota_gb'] > 0) {
                    set_config('disk_quota', $settings['disk_quota_gb'], 'report_usage_monitor');
                    $result['updated'][] = 'disk_quota_gb';
                } else {
                    $result['errors'][] = get_string('error_disk_threshold_negative', 'report_usage_monitor');
                }
            }

            // Update disk warning level.
            if (isset($settings['disk_warning_level'])) {
                $level = max(1, min(100, $settings['disk_warning_level']));
                set_config('disk_warning_level', $level, 'report_usage_monitor');
                $result['updated'][] = 'disk_warning_level';
            }

            // Update users warning level.
            if (isset($settings['users_warning_level'])) {
                $level = max(1, min(100, $settings['users_warning_level']));
                set_config('users_warning_level', $level, 'report_usage_monitor');
                $result['updated'][] = 'users_warning_level';
            }

            // Update notification email.
            if (isset($settings['notification_email']) && !empty($settings['notification_email'])) {
                set_config('email', $settings['notification_email'], 'report_usage_monitor');
                $result['updated'][] = 'notification_email';
            }

            if (!empty($result['errors'])) {
                $result['success'] = false;
                $transaction->rollback(new \moodle_exception('configuration_update_failed', 'report_usage_monitor'));
            } else {
                $transaction->allow_commit();
            }
        } catch (\Exception $e) {
            $transaction->rollback($e);
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Return structure for update_configuration.
     *
     * @return external_single_structure
     */
    public static function update_configuration_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the update was successful'),
            'updated' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'Name of updated setting')
            ),
            'errors' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'Error message')
            ),
        ]);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Get status level based on percentage.
     *
     * @param float $percentage
     * @return string Status level: healthy, warning, critical
     */
    private static function get_status_level(float $percentage): string {
        if ($percentage >= 90) {
            return 'critical';
        } else if ($percentage >= 70) {
            return 'warning';
        }
        return 'healthy';
    }

    /**
     * Get overall system status.
     *
     * @param string $disk_status
     * @param string $users_status
     * @return string Overall status
     */
    private static function get_overall_status(string $disk_status, string $users_status): string {
        if ($disk_status === 'critical' || $users_status === 'critical') {
            return 'critical';
        } else if ($disk_status === 'warning' || $users_status === 'warning') {
            return 'warning';
        }
        return 'healthy';
    }

    /**
     * Get active alerts based on current metrics.
     *
     * @param float $disk_percent
     * @param float $users_percent
     * @param object $config
     * @return array Active alerts
     */
    private static function get_active_alerts(float $disk_percent, float $users_percent, $config): array {
        $alerts = [];

        $disk_warning = (int)($config->disk_warning_level ?? 90);
        $users_warning = (int)($config->users_warning_level ?? 90);

        if ($disk_percent >= 90) {
            $alerts[] = [
                'type' => 'disk',
                'level' => 'critical',
                'message' => "Disk usage is critically high at " . round($disk_percent, 1) . "%. Immediate action required.",
                'percentage' => round($disk_percent, 2),
            ];
        } else if ($disk_percent >= $disk_warning) {
            $alerts[] = [
                'type' => 'disk',
                'level' => 'warning',
                'message' => "Disk usage is above warning threshold at " . round($disk_percent, 1) . "%.",
                'percentage' => round($disk_percent, 2),
            ];
        }

        if ($users_percent >= 100) {
            $alerts[] = [
                'type' => 'users',
                'level' => 'critical',
                'message' => "User limit exceeded at " . round($users_percent, 1) . "%. Consider upgrading your plan.",
                'percentage' => round($users_percent, 2),
            ];
        } else if ($users_percent >= $users_warning) {
            $alerts[] = [
                'type' => 'users',
                'level' => 'warning',
                'message' => "User count is approaching limit at " . round($users_percent, 1) . "%.",
                'percentage' => round($users_percent, 2),
            ];
        }

        return $alerts;
    }

    /**
     * Get recommended actions based on current state.
     *
     * @param float $disk_percent
     * @param float $users_percent
     * @param object $config
     * @return array Recommended actions
     */
    private static function get_recommended_actions(float $disk_percent, float $users_percent, $config): array {
        $actions = [];
        $backup_count = get_config('backup', 'backup_auto_max_kept') ?? 0;

        if ($disk_percent >= 90) {
            $actions[] = [
                'priority' => 'high',
                'category' => 'disk',
                'description' => 'Urgently reduce disk usage by removing old backups and unused files.',
            ];
            if ($backup_count > 1) {
                $actions[] = [
                    'priority' => 'high',
                    'category' => 'backup',
                    'description' => "Reduce automatic backups from $backup_count to 1 per course.",
                ];
            }
        } else if ($disk_percent >= 70) {
            $actions[] = [
                'priority' => 'medium',
                'category' => 'disk',
                'description' => 'Consider cleaning up old course files and reducing backup retention.',
            ];
        }

        if ($users_percent >= 90) {
            $actions[] = [
                'priority' => 'high',
                'category' => 'users',
                'description' => 'User limit is almost reached. Contact your provider to increase quota.',
            ];
            $actions[] = [
                'priority' => 'medium',
                'category' => 'users',
                'description' => 'Review and remove inactive user accounts to free up slots.',
            ];
        } else if ($users_percent >= 70) {
            $actions[] = [
                'priority' => 'low',
                'category' => 'users',
                'description' => 'Monitor user growth and plan for capacity increase if needed.',
            ];
        }

        return $actions;
    }

    /**
     * Calculate trend summary from data points.
     *
     * @param array $data
     * @param string $field
     * @return array Summary statistics
     */
    private static function calculate_trend_summary(array $data, string $field): array {
        if (empty($data)) {
            return [
                'min' => 0,
                'max' => 0,
                'avg' => 0,
                'trend' => 'stable',
                'change_percent' => 0,
            ];
        }

        $values = array_column($data, $field);
        $min = min($values);
        $max = max($values);
        $avg = array_sum($values) / count($values);

        $first = reset($values);
        $last = end($values);
        $change = $first > 0 ? (($last - $first) / $first) * 100 : 0;

        $trend = 'stable';
        if ($change > 5) {
            $trend = 'up';
        } else if ($change < -5) {
            $trend = 'down';
        }

        return [
            'min' => round($min, 2),
            'max' => round($max, 2),
            'avg' => round($avg, 2),
            'trend' => $trend,
            'change_percent' => round($change, 2),
        ];
    }

    /**
     * Safely project limit date.
     *
     * @param int $current
     * @param int $threshold
     * @param float $growth_rate
     * @return int Days to threshold
     */
    private static function safe_project_limit_date($current, $threshold, $growth_rate): int {
        $result = project_limit_date($current, $threshold, $growth_rate);
        if ($result === null || $result === PHP_INT_MAX || $result < 0) {
            return 999; // Large number indicating "not projected"
        }
        return min((int)$result, 999);
    }

    /**
     * Format timestamp safely.
     *
     * @param mixed $timestamp
     * @return string Formatted date or placeholder
     */
    private static function format_timestamp($timestamp): string {
        if (!is_numeric($timestamp) || $timestamp <= 0) {
            return 'N/A';
        }
        return date('Y-m-d H:i:s', (int)$timestamp);
    }
}
