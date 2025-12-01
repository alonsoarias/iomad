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
 * Library functions for report_platform_usage.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend navigation to add report to course navigation.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course object
 * @param context $context The course context
 */
function report_platform_usage_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/platform_usage:view', $context)) {
        $url = new moodle_url('/report/platform_usage/index.php');
        $navigation->add(
            get_string('pluginname', 'report_platform_usage'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'report_platform_usage',
            new pix_icon('i/report', '')
        );
    }
}

/**
 * Add report to the course reports section.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $user The user object
 * @param context $context The context
 * @param stdClass $course The course object
 * @param stdClass $coursenode The course node
 */
function report_platform_usage_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    // This report is not user-specific, so we don't add it to the profile.
}

/**
 * This function extends the settings navigation block for the site.
 *
 * @param settings_navigation $settingsnav The settings navigation object
 * @param context $context The context
 */
function report_platform_usage_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    if ($context->contextlevel == CONTEXT_COURSE) {
        $course = get_course($context->instanceid);
        if ($course->id != SITEID && has_capability('report/platform_usage:view', $context)) {
            $node = $settingsnav->get('coursereports');
            if ($node) {
                $url = new moodle_url('/report/platform_usage/index.php');
                $node->add(
                    get_string('pluginname', 'report_platform_usage'),
                    $url,
                    navigation_node::TYPE_SETTING,
                    null,
                    'report_platform_usage',
                    new pix_icon('i/report', '')
                );
            }
        }
    }
}
