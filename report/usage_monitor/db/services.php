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
 * External services definition for report_usage_monitor.
 *
 * @package    report_usage_monitor
 * @copyright  2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    // =========================================================================
    // LEGACY API - Maintained for backwards compatibility
    // =========================================================================
    'report_usage_monitor_get_monitor_stats' => [
        'classname'     => 'report_usage_monitor_external',
        'methodname'    => 'get_monitor_stats',
        'classpath'     => 'report/usage_monitor/classes/external.php',
        'description'   => 'Gets current system usage statistics (legacy).',
        'type'          => 'read',
        'capabilities'  => 'report/usage_monitor:view',
        'ajax'          => true,
    ],
    'report_usage_monitor_get_notification_history' => [
        'classname'     => 'report_usage_monitor_external',
        'methodname'    => 'get_notification_history',
        'classpath'     => 'report/usage_monitor/classes/external.php',
        'description'   => 'Gets notification history (legacy).',
        'type'          => 'read',
        'capabilities'  => 'report/usage_monitor:view',
        'ajax'          => true,
    ],
    'report_usage_monitor_get_usage_data' => [
        'classname'     => 'report_usage_monitor_external',
        'methodname'    => 'get_usage_data',
        'classpath'     => 'report/usage_monitor/classes/external.php',
        'description'   => 'Gets precalculated usage data (legacy).',
        'type'          => 'read',
        'capabilities'  => 'report/usage_monitor:view',
        'ajax'          => true,
    ],
    'report_usage_monitor_set_usage_thresholds' => [
        'classname'     => 'report_usage_monitor_external',
        'methodname'    => 'set_usage_thresholds',
        'classpath'     => 'report/usage_monitor/classes/external.php',
        'description'   => 'Sets usage thresholds (legacy).',
        'type'          => 'write',
        'capabilities'  => 'report/usage_monitor:manage',
        'ajax'          => true,
    ],

    // =========================================================================
    // NEW API v2 - Modern endpoints with namespaced classes
    // =========================================================================
    'report_usage_monitor_get_system_health' => [
        'classname'     => 'report_usage_monitor\\external\\api',
        'methodname'    => 'get_system_health',
        'description'   => 'Get comprehensive system health status including disk, users, alerts and recommended actions.',
        'type'          => 'read',
        'capabilities'  => 'report/usage_monitor:view',
        'ajax'          => true,
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'report_usage_monitor_get_trend_analysis' => [
        'classname'     => 'report_usage_monitor\\external\\api',
        'methodname'    => 'get_trend_analysis',
        'description'   => 'Get historical trend analysis for disk and user metrics.',
        'type'          => 'read',
        'capabilities'  => 'report/usage_monitor:view',
        'ajax'          => true,
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'report_usage_monitor_update_configuration' => [
        'classname'     => 'report_usage_monitor\\external\\api',
        'methodname'    => 'update_configuration',
        'description'   => 'Update plugin configuration settings including thresholds and notifications.',
        'type'          => 'write',
        'capabilities'  => 'report/usage_monitor:manage',
        'ajax'          => true,
    ],
];

// Define external services.
$services = [
    'Usage Monitor API' => [
        'functions' => [
            // Legacy endpoints.
            'report_usage_monitor_get_monitor_stats',
            'report_usage_monitor_get_notification_history',
            'report_usage_monitor_get_usage_data',
            'report_usage_monitor_set_usage_thresholds',
            // New v2 endpoints.
            'report_usage_monitor_get_system_health',
            'report_usage_monitor_get_trend_analysis',
            'report_usage_monitor_update_configuration',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'usage_monitor_api',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];
