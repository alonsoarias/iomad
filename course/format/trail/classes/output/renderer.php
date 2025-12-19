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
 * Trail Format - Modern renderer class for Moodle 4.5 compatibility.
 *
 * @package    format_trail
 * @copyright  2024 onwards
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_trail\output;

use core_courseformat\output\section_renderer;
use moodle_page;

/**
 * Modern renderer for Trail format compatible with Moodle 4.5.
 *
 * This renderer extends the core section_renderer and provides
 * additional methods specific to the Trail format.
 *
 * @package    format_trail
 * @copyright  2024 onwards
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends section_renderer {

    /**
     * Constructor.
     *
     * @param moodle_page $page The moodle page object.
     * @param string $target The rendering target.
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
    }

    /**
     * Generate the section title for Trail format.
     *
     * @param \section_info $section The section info.
     * @param \stdClass $course The course object.
     * @return string The section title HTML.
     */
    public function section_title($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title without link.
     *
     * @param \section_info $section The section info.
     * @param \stdClass $course The course object.
     * @return string The section title HTML without link.
     */
    public function section_title_without_link($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section, false));
    }
}
