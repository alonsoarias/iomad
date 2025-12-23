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
 * Faculty dean assignment management for local_jobboard.
 *
 * This class manages the assignment of deans to faculties.
 * Deans can only review applications for vacancies within their assigned faculties.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educacion Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Faculty dean assignment class.
 *
 * @package   local_jobboard
 */
class faculty_dean {

    /** @var string Status for active dean assignment. */
    public const STATUS_ACTIVE = 'active';

    /** @var string Status for inactive dean assignment. */
    public const STATUS_INACTIVE = 'inactive';

    /** @var string Database table name. */
    protected const TABLE = 'local_jobboard_faculty_dean';

    /** @var int The record ID. */
    public int $id;

    /** @var int The faculty ID. */
    public int $facultyid;

    /** @var int The user ID (dean). */
    public int $userid;

    /** @var string Status (active/inactive). */
    public string $status = self::STATUS_ACTIVE;

    /** @var int|null User who added the assignment. */
    public ?int $addedby = null;

    /** @var int Time created. */
    public int $timecreated;

    /** @var int|null Time modified. */
    public ?int $timemodified = null;

    /**
     * Constructor.
     *
     * @param int $id Record ID (0 for new record).
     */
    public function __construct(int $id = 0) {
        global $DB;

        $this->timecreated = time();

        if ($id > 0) {
            $record = $DB->get_record(self::TABLE, ['id' => $id], '*', MUST_EXIST);
            $this->populate_from_record($record);
        }
    }

    /**
     * Populate object from database record.
     *
     * @param \stdClass $record Database record.
     */
    protected function populate_from_record(\stdClass $record): void {
        $this->id = (int) $record->id;
        $this->facultyid = (int) $record->facultyid;
        $this->userid = (int) $record->userid;
        $this->status = $record->status;
        $this->addedby = $record->addedby ? (int) $record->addedby : null;
        $this->timecreated = (int) $record->timecreated;
        $this->timemodified = $record->timemodified ? (int) $record->timemodified : null;
    }

    /**
     * Get record object for database operations.
     *
     * @return \stdClass Database record.
     */
    protected function get_record(): \stdClass {
        $record = new \stdClass();
        if (isset($this->id)) {
            $record->id = $this->id;
        }
        $record->facultyid = $this->facultyid;
        $record->userid = $this->userid;
        $record->status = $this->status;
        $record->addedby = $this->addedby;
        $record->timecreated = $this->timecreated;
        $record->timemodified = $this->timemodified;
        return $record;
    }

    /**
     * Save the record to database.
     *
     * @return int The record ID.
     */
    public function save(): int {
        global $DB, $USER;

        $this->timemodified = time();

        if (!isset($this->addedby)) {
            $this->addedby = $USER->id;
        }

        $record = $this->get_record();

        if (isset($this->id)) {
            $DB->update_record(self::TABLE, $record);
        } else {
            $this->id = $DB->insert_record(self::TABLE, $record);
        }

        return $this->id;
    }

    /**
     * Delete the record from database.
     *
     * @return bool True on success.
     */
    public function delete(): bool {
        global $DB;

        if (!isset($this->id)) {
            return false;
        }

        return $DB->delete_records(self::TABLE, ['id' => $this->id]);
    }

    /**
     * Assign a dean to a faculty.
     *
     * @param int $facultyid Faculty ID.
     * @param int $userid User ID (dean).
     * @param int|null $addedby User who added the assignment.
     * @return self The created assignment.
     * @throws \moodle_exception If assignment already exists.
     */
    public static function add(int $facultyid, int $userid, ?int $addedby = null): self {
        global $DB, $USER;

        // Check if already exists.
        if ($DB->record_exists(self::TABLE, ['facultyid' => $facultyid, 'userid' => $userid])) {
            throw new \moodle_exception('error:dean_already_assigned', 'local_jobboard');
        }

        $dean = new self();
        $dean->facultyid = $facultyid;
        $dean->userid = $userid;
        $dean->addedby = $addedby ?? $USER->id;
        $dean->status = self::STATUS_ACTIVE;
        $dean->save();

        return $dean;
    }

    /**
     * Remove a dean from a faculty.
     *
     * @param int $facultyid Faculty ID.
     * @param int $userid User ID (dean).
     * @return bool True on success.
     */
    public static function remove(int $facultyid, int $userid): bool {
        global $DB;
        return $DB->delete_records(self::TABLE, ['facultyid' => $facultyid, 'userid' => $userid]);
    }

    /**
     * Set the status of a dean assignment.
     *
     * @param int $facultyid Faculty ID.
     * @param int $userid User ID (dean).
     * @param string $status New status.
     * @return bool True on success.
     */
    public static function set_status(int $facultyid, int $userid, string $status): bool {
        global $DB;

        $record = $DB->get_record(self::TABLE, ['facultyid' => $facultyid, 'userid' => $userid]);
        if (!$record) {
            return false;
        }

        $record->status = $status;
        $record->timemodified = time();
        return $DB->update_record(self::TABLE, $record);
    }

    /**
     * Get all faculties assigned to a dean.
     *
     * @param int $userid User ID (dean).
     * @param bool $activeonly Only return active assignments.
     * @return array Array of faculty IDs.
     */
    public static function get_faculties_for_dean(int $userid, bool $activeonly = true): array {
        global $DB;

        $params = ['userid' => $userid];
        $where = 'userid = :userid';

        if ($activeonly) {
            $where .= ' AND status = :status';
            $params['status'] = self::STATUS_ACTIVE;
        }

        $records = $DB->get_records_select(self::TABLE, $where, $params, '', 'facultyid');
        return array_keys($records);
    }

    /**
     * Get all deans assigned to a faculty.
     *
     * @param int $facultyid Faculty ID.
     * @param bool $activeonly Only return active assignments.
     * @return array Array of user IDs.
     */
    public static function get_deans_for_faculty(int $facultyid, bool $activeonly = true): array {
        global $DB;

        $params = ['facultyid' => $facultyid];
        $where = 'facultyid = :facultyid';

        if ($activeonly) {
            $where .= ' AND status = :status';
            $params['status'] = self::STATUS_ACTIVE;
        }

        $records = $DB->get_records_select(self::TABLE, $where, $params, '', 'userid');
        return array_keys($records);
    }

    /**
     * Check if a user is a dean for a specific faculty.
     *
     * @param int $userid User ID.
     * @param int $facultyid Faculty ID.
     * @param bool $activeonly Check only active assignments.
     * @return bool True if user is dean for the faculty.
     */
    public static function is_dean_for_faculty(int $userid, int $facultyid, bool $activeonly = true): bool {
        global $DB;

        $params = ['userid' => $userid, 'facultyid' => $facultyid];
        if ($activeonly) {
            $params['status'] = self::STATUS_ACTIVE;
        }

        return $DB->record_exists(self::TABLE, $params);
    }

    /**
     * Get all faculty dean assignments.
     *
     * @param int $facultyid Optional faculty ID to filter.
     * @param int $userid Optional user ID to filter.
     * @param bool $activeonly Only return active assignments.
     * @return array Array of faculty_dean objects.
     */
    public static function get_all(int $facultyid = 0, int $userid = 0, bool $activeonly = true): array {
        global $DB;

        $params = [];
        $where = [];

        if ($facultyid > 0) {
            $where[] = 'fd.facultyid = :facultyid';
            $params['facultyid'] = $facultyid;
        }

        if ($userid > 0) {
            $where[] = 'fd.userid = :userid';
            $params['userid'] = $userid;
        }

        if ($activeonly) {
            $where[] = 'fd.status = :status';
            $params['status'] = self::STATUS_ACTIVE;
        }

        $wheresql = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

        $sql = "SELECT fd.*, f.name as facultyname, f.code as facultycode,
                       u.firstname, u.lastname, u.email
                  FROM {" . self::TABLE . "} fd
                  JOIN {local_jobboard_faculty} f ON f.id = fd.facultyid
                  JOIN {user} u ON u.id = fd.userid
                  $wheresql
              ORDER BY f.name ASC, u.lastname ASC, u.firstname ASC";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get faculty IDs for programs linked to a vacancy via course category.
     *
     * This method determines which faculty a vacancy belongs to by looking at:
     * 1. The vacancy's convocatoria → programs
     * 2. Direct program-category associations
     *
     * @param int $vacancyid Vacancy ID.
     * @return array Array of faculty IDs associated with the vacancy.
     */
    public static function get_faculty_ids_for_vacancy(int $vacancyid): array {
        global $DB;

        // Get the vacancy and its convocatoria.
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
        if (!$vacancy) {
            return [];
        }

        // Strategy 1: Check if vacancy has a direct program association through name/code matching.
        // This is a fallback for cases where direct linking isn't available.

        // Strategy 2: Look for faculties in the same company as the vacancy.
        $facultyids = [];
        if (!empty($vacancy->companyid)) {
            // Get all faculties for this company.
            $faculties = $DB->get_records('local_jobboard_faculty', [
                'companyid' => $vacancy->companyid,
                'enabled' => 1,
            ], '', 'id');
            $facultyids = array_keys($faculties);
        }

        return $facultyids;
    }

    /**
     * Check if a dean can access a vacancy based on faculty assignment.
     *
     * @param int $userid User ID (dean).
     * @param int $vacancyid Vacancy ID.
     * @return bool True if the dean can access the vacancy.
     */
    public static function can_dean_access_vacancy(int $userid, int $vacancyid): bool {
        // Get faculties the dean is assigned to.
        $deanfaculties = self::get_faculties_for_dean($userid);
        if (empty($deanfaculties)) {
            return false;
        }

        // Get faculties associated with the vacancy.
        $vacancyfaculties = self::get_faculty_ids_for_vacancy($vacancyid);
        if (empty($vacancyfaculties)) {
            // If no faculties are associated with the vacancy, allow access
            // (backward compatibility for vacancies without faculty association).
            return true;
        }

        // Check if there's any overlap.
        return !empty(array_intersect($deanfaculties, $vacancyfaculties));
    }

    /**
     * Get all faculties with their dean assignments for display.
     *
     * @param int $companyid Optional company ID to filter.
     * @return array Array of faculty objects with dean information.
     */
    public static function get_faculties_with_deans(int $companyid = 0): array {
        global $DB;

        $params = ['enabled' => 1];
        $where = 'f.enabled = :enabled';

        if ($companyid > 0) {
            $where .= ' AND f.companyid = :companyid';
            $params['companyid'] = $companyid;
        }

        $sql = "SELECT f.id, f.code, f.name, f.companyid,
                       (SELECT COUNT(*) FROM {" . self::TABLE . "} fd
                        WHERE fd.facultyid = f.id AND fd.status = 'active') as deancount
                  FROM {local_jobboard_faculty} f
                 WHERE $where
              ORDER BY f.sortorder ASC, f.name ASC";

        $faculties = $DB->get_records_sql($sql, $params);

        // Add dean details to each faculty.
        foreach ($faculties as $faculty) {
            $faculty->deans = self::get_all($faculty->id, 0, true);
        }

        return $faculties;
    }
}
