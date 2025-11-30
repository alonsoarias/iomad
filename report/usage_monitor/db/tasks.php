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
 * Scheduled tasks definition for report_usage_monitor.
 *
 * @package     report_usage_monitor
 * @category    admin
 * @copyright   2023 Soporte IngeWeb <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

// Check if 'du' command is available for faster disk calculations.
$du_command_available = !empty($CFG->pathtodu) && is_executable(trim($CFG->pathtodu));

$tasks = [
    // Task to calculate disk usage.
    [
        'classname' => 'report_usage_monitor\task\disk_usage',
        'blocking' => 0,
        'minute' => '0',
        'hour' => $du_command_available ? '*/6' : '12', // Every 6 hours if 'du' is available, otherwise every 12 hours.
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
    // Task to calculate recently connected users.
    [
        'classname' => 'report_usage_monitor\task\last_users',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*/2',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
    // Unified task for processing notifications (disk and user limits).
    // Sends a single notification when either or both thresholds are exceeded.
    [
        'classname' => 'report_usage_monitor\task\notification_combined',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '8', // Once daily at 8 AM.
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
    // Task to calculate peak users in the last 90 days.
    [
        'classname' => 'report_usage_monitor\task\users_daily_90_days',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
    // Task to calculate daily users.
    [
        'classname' => 'report_usage_monitor\task\users_daily',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ]
];
