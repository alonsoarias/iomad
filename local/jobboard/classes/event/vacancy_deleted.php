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
 * Vacancy deleted event.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered when a vacancy is deleted.
 */
class vacancy_deleted extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'local_jobboard_vacancy';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:vacancydeleted', 'local_jobboard');
    }

    /**
     * Get event description.
     *
     * @return string
     */
    public function get_description() {
        $code = $this->other['code'] ?? 'unknown';
        $title = $this->other['title'] ?? 'unknown';
        return "The user with id '{$this->userid}' deleted vacancy '{$code}' - '{$title}' with id '{$this->objectid}'.";
    }
}
