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
 * Trail Format - Content output class for Moodle 4.5 compatibility.
 *
 * @package    format_trail
 * @copyright  2024 onwards
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_trail\output\courseformat;

use core_courseformat\output\local\content as content_base;
use core\output\renderer_base;
use stdClass;

/**
 * Base class to render course content for Trail format.
 *
 * @package    format_trail
 * @copyright  2024 onwards
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends content_base {

    /**
     * @var bool Trail format does not add section after each topic by default.
     */
    protected $hasaddsection = false;

    /**
     * Get the template name for this templatable.
     *
     * @param renderer_base $renderer The renderer requesting the template name.
     * @return string The template name.
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'format_trail/local/content';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this method.
     * @return stdClass data context for a Mustache template.
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $PAGE;

        $format = $this->format;
        $course = $format->get_course();
        $editing = $PAGE->user_is_editing();

        $data = (object)[
            'title' => $format->page_title(),
            'format' => $format->get_format(),
            'sectionreturn' => null,
            'editing' => $editing,
            'courseid' => $course->id,
        ];

        // Get sections data from parent.
        $singlesectionid = $this->format->get_sectionid();
        $sections = $this->export_sections($output);

        if (!empty($sections)) {
            // Is first entry section 0?
            if ($sections[0]->num === 0) {
                $initialsection = array_shift($sections);
                $data->initialsection = $initialsection;
            }
            $data->sections = $sections;
        }

        // Single section format has extra navigation.
        if ($singlesectionid) {
            $singlesectionno = $this->format->get_sectionnum();
            $sectionnavigation = new $this->sectionnavigationclass($format, $singlesectionno);
            $data->sectionnavigation = $sectionnavigation->export_for_template($output);

            $sectionselector = new $this->sectionselectorclass($format, $sectionnavigation);
            $data->sectionselector = $sectionselector->export_for_template($output);
            $data->hasnavigation = true;
            $data->singlesection = array_shift($data->sections);
            $data->sectionreturn = $singlesectionno;
        }

        // Add section button if in edit mode.
        if ($this->hasaddsection) {
            $addsection = new $this->addsectionclass($format);
            $data->numsections = $addsection->export_for_template($output);
        }

        // Bulk edit tools for Moodle 4.2+.
        if ($format->show_editor()) {
            $bulkedittools = new $this->bulkedittoolsclass($format);
            $data->bulkedittools = $bulkedittools->export_for_template($output);
        }

        return $data;
    }
}
