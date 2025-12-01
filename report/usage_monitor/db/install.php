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
 * Plugin installation script.
 *
 * This script is executed during plugin installation and displays
 * notifications to the user based on server capabilities.
 *
 * @package     report_usage_monitor
 * @category    upgrade
 * @author      Alonso Arias <soporte@ingeweb.co>
 * @copyright   2025 Alonso Arias <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute plugin installation procedures.
 *
 * Displays notifications about the 'du' command availability
 * and shell_exec function status.
 *
 * @return bool True on success.
 */
function xmldb_report_usage_monitor_install() {
    global $OUTPUT;

    // Check if shell_exec function is available on the server.
    if (function_exists('shell_exec')) {
        echo $OUTPUT->notification(
            get_string('pathtodurecommendation', 'report_usage_monitor'),
            'info'
        );
        echo $OUTPUT->notification(
            get_string('pathtodunote', 'report_usage_monitor'),
            'info'
        );
    } else {
        // If shell_exec is not available, show a warning.
        echo $OUTPUT->notification(
            get_string('activateshellexec', 'report_usage_monitor'),
            'warning'
        );
    }

    return true;
}
