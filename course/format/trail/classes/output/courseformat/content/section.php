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
 * Trail Format - Section output class for Moodle 4.5 compatibility.
 *
 * @package    format_trail
 * @copyright  2024 onwards
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_trail\output\courseformat\content;

use core_courseformat\output\local\content\section as section_base;
use core\output\renderer_base;
use stdClass;

/**
 * Class to render a section for Trail format.
 *
 * @package    format_trail
 * @copyright  2024 onwards
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section extends section_base {

    /**
     * Get the template name for this templatable.
     *
     * @param renderer_base $renderer The renderer requesting the template name.
     * @return string The template name.
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'format_trail/local/content/section';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this method.
     * @return stdClass data context for a Mustache template.
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = parent::export_for_template($output);

        // Add trail-specific section data here if needed.
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();

        // Get trail format settings.
        $settings = $format->get_settings();

        // Add section image data if available.
        $sectionimage = $format->get_image($course->id, $section->id);
        if ($sectionimage) {
            $data->hasimage = true;
            $data->sectionimage = $sectionimage;
        }

        return $data;
    }
}
