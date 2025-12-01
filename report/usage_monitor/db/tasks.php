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
 * Scheduled tasks definition for the usage monitor plugin.
 *
 * This file defines all scheduled tasks for calculating disk usage,
 * user statistics, and sending notifications.
 *
 * @package     report_usage_monitor
 * @category    task
 * @author      Alonso Arias <soporte@ingeweb.co>
 * @copyright   2025 Alonso Arias <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

// Check if 'du' command is available for faster disk calculations.
$ducommandavailable = !empty($CFG->pathtodu) && is_executable(trim($CFG->pathtodu));

$tasks = [
    // Task to calculate disk usage.
    [
        'classname' => 'report_usage_monitor\task\disk_usage',
        'blocking' => 0,
        'minute' => '0',
        'hour' => $ducommandavailable ? '*/6' : '12',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    // Task to calculate recently connected users ranking.
    [
        'classname' => 'report_usage_monitor\task\last_users',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*/2',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    // Unified task for processing notifications (disk and user limits).
    // Sends a single notification when either or both thresholds are exceeded.
    // Runs at same frequency as disk calculation to maintain original notification timing.
    // User notifications are only sent at 8 AM; disk notifications follow interval logic.
    [
        'classname' => 'report_usage_monitor\task\notification_combined',
        'blocking' => 0,
        'minute' => '0',
        'hour' => $ducommandavailable ? '*/6' : '*/12',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    // Task to calculate peak users in the last 90 days.
    [
        'classname' => 'report_usage_monitor\task\users_daily_90_days',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    // Task to calculate daily users.
    [
        'classname' => 'report_usage_monitor\task\users_daily',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];
