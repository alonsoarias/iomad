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

declare(strict_types=1);

/**
 * Application status changed event.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered when an application status changes.
 */
class application_status_changed extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'local_jobboard_application';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:statuschanged', 'local_jobboard');
    }

    /**
     * Get event description.
     *
     * @return string
     */
    public function get_description() {
        $oldstatus = $this->other['oldstatus'] ?? 'unknown';
        $newstatus = $this->other['newstatus'] ?? 'unknown';
        return "The application with id '{$this->objectid}' changed status from '{$oldstatus}' to '{$newstatus}'.";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $this->objectid]);
    }
}
