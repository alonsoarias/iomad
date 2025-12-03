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
 * Plugin settings.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Add link in the reports section.
$ADMIN->add('reports', new admin_externalpage(
    'report_platform_usage',
    get_string('pluginname', 'report_platform_usage'),
    new moodle_url('/report/platform_usage/index.php'),
    'report/platform_usage:view'
));

// Add settings page.
if ($hassiteconfig) {
    $settings = new admin_settingpage('report_platform_usage_settings',
        get_string('settings', 'report_platform_usage'));

    // Session limit for dedication calculation.
    $settings->add(new admin_setting_configduration(
        'report_platform_usage/session_limit',
        get_string('setting_session_limit', 'report_platform_usage'),
        get_string('setting_session_limit_desc', 'report_platform_usage'),
        HOURSECS, // Default: 1 hour.
        MINSECS  // Min unit: minutes.
    ));

    // Default time period for reports.
    $settings->add(new admin_setting_configselect(
        'report_platform_usage/default_period',
        get_string('setting_default_period', 'report_platform_usage'),
        get_string('setting_default_period_desc', 'report_platform_usage'),
        30, // Default: 30 days.
        [
            7 => get_string('lastweek', 'report_platform_usage'),
            30 => get_string('lastmonth', 'report_platform_usage'),
            90 => get_string('lastquarter', 'report_platform_usage'),
            365 => get_string('lastyear', 'report_platform_usage'),
        ]
    ));

    // Number of items to show in top lists.
    $settings->add(new admin_setting_configtext(
        'report_platform_usage/top_items_limit',
        get_string('setting_top_items_limit', 'report_platform_usage'),
        get_string('setting_top_items_limit_desc', 'report_platform_usage'),
        10,
        PARAM_INT
    ));

    // Enable/disable cache.
    $settings->add(new admin_setting_configcheckbox(
        'report_platform_usage/enable_cache',
        get_string('setting_enable_cache', 'report_platform_usage'),
        get_string('setting_enable_cache_desc', 'report_platform_usage'),
        1
    ));

    $ADMIN->add('reports', $settings);
}
