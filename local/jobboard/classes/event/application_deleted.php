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
 * Application deleted event.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered when an application is deleted.
 */
class application_deleted extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'local_jobboard_application';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:applicationdeleted', 'local_jobboard');
    }

    /**
     * Get event description.
     *
     * @return string
     */
    public function get_description() {
        $reason = $this->other['reason'] ?? 'No reason provided';
        return "The user with id '{$this->userid}' deleted the application with id '{$this->objectid}' " .
               "for vacancy id '{$this->other['vacancyid']}' " .
               "(applicant user id: '{$this->other['applicantuserid']}'). " .
               "Reason: {$reason}";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/local/jobboard/index.php', ['view' => 'review']);
    }
}
