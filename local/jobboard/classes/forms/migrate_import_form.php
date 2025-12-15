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
 * Import form for local_jobboard migration.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Import form class for migration.
 */
class migrate_import_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'importhdr', get_string('importdata', 'local_jobboard'));

        $mform->addElement('filepicker', 'importfile', get_string('migrationfile', 'local_jobboard'), null, [
            'accepted_types' => ['.zip', '.json'],
            'maxbytes' => 524288000, // 500MB for full exports with files.
        ]);
        $mform->addRule('importfile', null, 'required');

        $mform->addElement('advcheckbox', 'overwrite', '', get_string('overwriteexisting', 'local_jobboard'));
        $mform->setDefault('overwrite', 0);

        $mform->addElement('advcheckbox', 'dryrun', '', get_string('dryrunmode', 'local_jobboard'));
        $mform->setDefault('dryrun', 1);

        $this->add_action_buttons(true, get_string('importupload', 'local_jobboard'));
    }
}
