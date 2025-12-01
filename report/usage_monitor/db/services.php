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
 * External services definition for the usage monitor plugin.
 *
 * This file defines web service functions and the external API service
 * for integration with external systems.
 *
 * @package     report_usage_monitor
 * @category    webservice
 * @author      Alonso Arias <soporte@ingeweb.co>
 * @copyright   2025 Alonso Arias <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'report_usage_monitor_get_monitor_stats' => [
        'classname' => 'report_usage_monitor_external',
        'methodname' => 'get_monitor_stats',
        'classpath' => 'report/usage_monitor/classes/external.php',
        'description' => 'Gets current system usage statistics including disk and user metrics.',
        'type' => 'read',
        'capabilities' => 'report/usage_monitor:view',
        'ajax' => true,
    ],
    'report_usage_monitor_get_notification_history' => [
        'classname' => 'report_usage_monitor_external',
        'methodname' => 'get_notification_history',
        'classpath' => 'report/usage_monitor/classes/external.php',
        'description' => 'Gets the history of sent notifications.',
        'type' => 'read',
        'capabilities' => 'report/usage_monitor:view',
        'ajax' => true,
    ],
    'report_usage_monitor_get_usage_data' => [
        'classname' => 'report_usage_monitor_external',
        'methodname' => 'get_usage_data',
        'classpath' => 'report/usage_monitor/classes/external.php',
        'description' => 'Gets precalculated usage data for lightweight API consumption.',
        'type' => 'read',
        'capabilities' => 'report/usage_monitor:view',
        'ajax' => true,
    ],
    'report_usage_monitor_set_usage_thresholds' => [
        'classname' => 'report_usage_monitor_external',
        'methodname' => 'set_usage_thresholds',
        'classpath' => 'report/usage_monitor/classes/external.php',
        'description' => 'Sets the usage thresholds for users and disk space.',
        'type' => 'write',
        'capabilities' => 'report/usage_monitor:manage',
        'ajax' => true,
    ],
];

// Define external services.
$services = [
    'Usage Monitor API' => [
        'functions' => [
            'report_usage_monitor_get_monitor_stats',
            'report_usage_monitor_get_notification_history',
            'report_usage_monitor_get_usage_data',
            'report_usage_monitor_set_usage_thresholds',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'usage_monitor_api',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];
