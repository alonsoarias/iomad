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
 * Maintenance layout for theme_inteb.
 *
 * Provides a minimal layout for the maintenance mode page
 * with custom footer settings support.
 *
 * @package     theme_inteb
 * @copyright   2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @author      Alonso Arias <soporteplataformas@iser.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// global $CFG, $SITE, $OUTPUT;

// Manually include the theme_settings.php for debugging purposes
require_once($CFG->dirroot . '/theme/inteb/classes/util/theme_settings.php');

// Instantiate theme settings and retrieve footer settings
$themesettings = new \theme_inteb\util\settings();
$templatecontext = [
    // We cannot pass the context to format_string, this layout can be used during
    // installation. At that stage database tables do not exist yet.
    'sitename' => format_string($SITE->shortname, true, ["escape" => false]),
    'output' => $OUTPUT
];

$templatecontext = array_merge($templatecontext, $themesettings->footer());

// Render the maintenance page with the combined context
echo $OUTPUT->render_from_template('theme_remui/maintenance', $templatecontext);