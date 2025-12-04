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
 * Data generator for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Data generator class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_jobboard_generator extends component_generator_base {

    /** @var int Counter for vacancy codes. */
    protected $vacancycount = 0;

    /** @var int Counter for application IDs. */
    protected $applicationcount = 0;

    /**
     * Reset the generator state.
     */
    public function reset() {
        $this->vacancycount = 0;
        $this->applicationcount = 0;
        parent::reset();
    }

    /**
     * Create a vacancy.
     *
     * @param array|stdClass $record The vacancy data.
     * @return stdClass The created vacancy record.
     */
    public function create_vacancy($record = null): stdClass {
        global $DB, $USER;

        $this->vacancycount++;

        $record = (object) (array) $record;

        // Set defaults.
        if (!isset($record->code)) {
            $record->code = 'GENVAC' . $this->vacancycount;
        }
        if (!isset($record->title)) {
            $record->title = 'Generated Vacancy ' . $this->vacancycount;
        }
        if (!isset($record->status)) {
            $record->status = 'draft';
        }
        if (!isset($record->opendate)) {
            $record->opendate = time();
        }
        if (!isset($record->closedate)) {
            $record->closedate = time() + (30 * 24 * 60 * 60);
        }
        if (!isset($record->positions)) {
            $record->positions = 1;
        }
        if (!isset($record->createdby)) {
            $record->createdby = $USER->id ?: 2; // Admin user.
        }
        if (!isset($record->timecreated)) {
            $record->timecreated = time();
        }

        $id = $DB->insert_record('local_jobboard_vacancy', $record);

        return $DB->get_record('local_jobboard_vacancy', ['id' => $id]);
    }

    /**
     * Create an application.
     *
     * @param array|stdClass $record The application data.
     * @return stdClass The created application record.
     */
    public function create_application($record = null): stdClass {
        global $DB;

        $this->applicationcount++;

        $record = (object) (array) $record;

        // Resolve vacancy by code if needed.
        if (isset($record->vacancy) && !isset($record->vacancyid)) {
            $vacancy = $DB->get_record('local_jobboard_vacancy', ['code' => $record->vacancy]);
            if ($vacancy) {
                $record->vacancyid = $vacancy->id;
            }
            unset($record->vacancy);
        }

        // Resolve user by username if needed.
        if (isset($record->user) && !isset($record->userid)) {
            $user = $DB->get_record('user', ['username' => $record->user]);
            if ($user) {
                $record->userid = $user->id;
            }
            unset($record->user);
        }

        // Set defaults.
        if (!isset($record->status)) {
            $record->status = 'submitted';
        }
        if (!isset($record->isexemption)) {
            $record->isexemption = 0;
        }
        if (!isset($record->timecreated)) {
            $record->timecreated = time();
        }

        $id = $DB->insert_record('local_jobboard_application', $record);

        return $DB->get_record('local_jobboard_application', ['id' => $id]);
    }

    /**
     * Create a document.
     *
     * @param array|stdClass $record The document data.
     * @return stdClass The created document record.
     */
    public function create_document($record = null): stdClass {
        global $DB, $USER;

        $record = (object) (array) $record;

        // Set defaults.
        if (!isset($record->documenttype)) {
            $record->documenttype = 'cedula';
        }
        if (!isset($record->filename)) {
            $record->filename = 'document.pdf';
        }
        if (!isset($record->uploadedby)) {
            $record->uploadedby = $USER->id ?: 2;
        }
        if (!isset($record->timecreated)) {
            $record->timecreated = time();
        }
        if (!isset($record->issuperseded)) {
            $record->issuperseded = 0;
        }
        if (!isset($record->isencrypted)) {
            $record->isencrypted = 0;
        }

        $id = $DB->insert_record('local_jobboard_document', $record);

        return $DB->get_record('local_jobboard_document', ['id' => $id]);
    }

    /**
     * Create an exemption.
     *
     * @param array|stdClass $record The exemption data.
     * @return stdClass The created exemption record.
     */
    public function create_exemption($record = null): stdClass {
        global $DB, $USER;

        $record = (object) (array) $record;

        // Resolve user by username if needed.
        if (isset($record->user) && !isset($record->userid)) {
            $user = $DB->get_record('user', ['username' => $record->user]);
            if ($user) {
                $record->userid = $user->id;
            }
            unset($record->user);
        }

        // Set defaults.
        if (!isset($record->exemptiontype)) {
            $record->exemptiontype = 'historico_iser';
        }
        if (!isset($record->validfrom)) {
            $record->validfrom = time() - (365 * 24 * 60 * 60); // 1 year ago.
        }
        if (!isset($record->createdby)) {
            $record->createdby = $USER->id ?: 2;
        }
        if (!isset($record->timecreated)) {
            $record->timecreated = time();
        }

        $id = $DB->insert_record('local_jobboard_exemption', $record);

        return $DB->get_record('local_jobboard_exemption', ['id' => $id]);
    }
}
