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
 * Web service definitions for theme_inteb
 *
 * @package     theme_inteb
 * @copyright   2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @author      Alonso Arias <soporteplataformas@iser.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'theme_inteb_get_section_progress' => [
        'classname'   => 'theme_inteb\external\get_section_progress',
        'methodname'  => 'execute',
        'description' => 'Get progress information for a course section',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
    ],
    'theme_inteb_get_course_progress' => [
        'classname'   => 'theme_inteb\external\get_course_progress',
        'methodname'  => 'execute',
        'description' => 'Get overall progress information for a course',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
    ],
    'theme_inteb_get_activities_completion' => [
        'classname'   => 'theme_inteb\external\get_activities_completion',
        'methodname'  => 'execute',
        'description' => 'Get completion states for all activities in a course',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
    ],
];
