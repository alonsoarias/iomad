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
 * Faculty Reviewer class for local_jobboard.
 *
 * Manages the assignment of reviewers (deans) to faculties for DOCENTE
 * type convocatorias where review is performed per faculty.
 *
 * @package   local_jobboard
 * @copyright 2024-2026 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing a faculty reviewer assignment.
 */
class faculty_reviewer {

    /** @var int The assignment ID. */
    public $id = 0;

    /** @var int The faculty ID. */
    public $facultyid = 0;

    /** @var int The reviewer user ID. */
    public $userid = 0;

    /** @var string The role (dean, lead_reviewer, reviewer). */
    public $role = 'reviewer';

    /** @var string The status (active, inactive). */
    public $status = 'active';

    /** @var int User who added this assignment. */
    public $addedby = 0;

    /** @var int Creation timestamp. */
    public $timecreated = 0;

    /** @var int|null Last modification timestamp. */
    public $timemodified = null;

    /** @var \stdClass|null Cached faculty object. */
    protected $faculty = null;

    /** @var \stdClass|null Cached user object. */
    protected $user = null;

    /** @var string Role constant: Dean. */
    public const ROLE_DEAN = 'dean';

    /** @var string Role constant: Lead Reviewer. */
    public const ROLE_LEAD = 'lead_reviewer';

    /** @var string Role constant: Reviewer. */
    public const ROLE_REVIEWER = 'reviewer';

    /** @var string Status constant: Active. */
    public const STATUS_ACTIVE = 'active';

    /** @var string Status constant: Inactive. */
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Constructor.
     *
     * @param int|\stdClass|null $idorrecord Assignment ID, database record, or null.
     */
    public function __construct($idorrecord = null) {
        if ($idorrecord === null) {
            return;
        }

        if (is_object($idorrecord)) {
            $this->load_from_record($idorrecord);
        } else {
            $this->load((int) $idorrecord);
        }
    }

    /**
     * Load assignment from ID.
     *
     * @param int $id The assignment ID.
     * @throws \dml_exception If not found.
     */
    public function load(int $id): void {
        global $DB;

        $record = $DB->get_record('local_jobboard_faculty_reviewer', ['id' => $id], '*', MUST_EXIST);
        $this->load_from_record($record);
    }

    /**
     * Load assignment from database record.
     *
     * @param \stdClass $record The database record.
     */
    public function load_from_record(\stdClass $record): void {
        $this->id = (int) $record->id;
        $this->facultyid = (int) $record->facultyid;
        $this->userid = (int) $record->userid;
        $this->role = $record->role ?? 'reviewer';
        $this->status = $record->status ?? 'active';
        $this->addedby = (int) ($record->addedby ?? 0);
        $this->timecreated = (int) ($record->timecreated ?? 0);
        $this->timemodified = $record->timemodified ? (int) $record->timemodified : null;
    }

    /**
     * Get an assignment by ID.
     *
     * @param int $id The assignment ID.
     * @return self|null The assignment or null if not found.
     */
    public static function get(int $id): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_faculty_reviewer', ['id' => $id]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Get reviewer assignment by faculty and user.
     *
     * @param int $facultyid The faculty ID.
     * @param int $userid The user ID.
     * @return self|null The assignment or null.
     */
    public static function get_by_faculty_user(int $facultyid, int $userid): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_faculty_reviewer', [
            'facultyid' => $facultyid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Get all active reviewers for a faculty.
     *
     * @param int $facultyid The faculty ID.
     * @return array Array of faculty_reviewer objects.
     */
    public static function get_by_faculty(int $facultyid): array {
        global $DB;

        $records = $DB->get_records('local_jobboard_faculty_reviewer', [
            'facultyid' => $facultyid,
            'status' => self::STATUS_ACTIVE,
        ], 'role ASC, timecreated ASC');

        $reviewers = [];
        foreach ($records as $record) {
            $reviewers[] = new self($record);
        }

        return $reviewers;
    }

    /**
     * Get all faculties a user is assigned to review.
     *
     * @param int $userid The user ID.
     * @return array Array of faculty_reviewer objects.
     */
    public static function get_by_user(int $userid): array {
        global $DB;

        $records = $DB->get_records('local_jobboard_faculty_reviewer', [
            'userid' => $userid,
            'status' => self::STATUS_ACTIVE,
        ], 'timecreated ASC');

        $assignments = [];
        foreach ($records as $record) {
            $assignments[] = new self($record);
        }

        return $assignments;
    }

    /**
     * Get the faculty IDs that a user can review.
     *
     * @param int $userid The user ID.
     * @return array Array of faculty IDs.
     */
    public static function get_faculty_ids_for_user(int $userid): array {
        global $DB;

        return $DB->get_fieldset_select(
            'local_jobboard_faculty_reviewer',
            'facultyid',
            'userid = :userid AND status = :status',
            ['userid' => $userid, 'status' => self::STATUS_ACTIVE]
        );
    }

    /**
     * Get faculty codes for a user (used for vacancy code pattern matching).
     *
     * @param int $userid The user ID.
     * @return array Array of faculty codes (e.g., ['FCAS', 'FII']).
     */
    public static function get_faculty_codes_for_user(int $userid): array {
        global $DB;

        return $DB->get_fieldset_sql(
            "SELECT f.code
               FROM {local_jobboard_faculty_reviewer} fr
               JOIN {local_jobboard_faculty} f ON f.id = fr.facultyid
              WHERE fr.userid = :userid AND fr.status = :status",
            ['userid' => $userid, 'status' => self::STATUS_ACTIVE]
        );
    }

    /**
     * Check if a vacancy code matches any of the user's assigned faculty codes.
     *
     * @param string $vacancycode The vacancy code (e.g., 'FCAS-001', 'FII-DOC-002').
     * @param int $userid The user ID.
     * @return bool True if the vacancy belongs to one of the user's faculties.
     */
    public static function vacancy_belongs_to_user_faculty(string $vacancycode, int $userid): bool {
        $facultycodes = self::get_faculty_codes_for_user($userid);

        foreach ($facultycodes as $code) {
            if (strpos($vacancycode, $code) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is a reviewer for a specific faculty.
     *
     * @param int $facultyid The faculty ID.
     * @param int $userid The user ID.
     * @return bool True if user is an active reviewer for the faculty.
     */
    public static function is_reviewer(int $facultyid, int $userid): bool {
        global $DB;

        return $DB->record_exists('local_jobboard_faculty_reviewer', [
            'facultyid' => $facultyid,
            'userid' => $userid,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Check if user is a dean for a specific faculty.
     *
     * @param int $facultyid The faculty ID.
     * @param int $userid The user ID.
     * @return bool True if user is the dean for the faculty.
     */
    public static function is_dean(int $facultyid, int $userid): bool {
        global $DB;

        return $DB->record_exists('local_jobboard_faculty_reviewer', [
            'facultyid' => $facultyid,
            'userid' => $userid,
            'role' => self::ROLE_DEAN,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Assign a reviewer to a faculty.
     *
     * @param int $facultyid The faculty ID.
     * @param int $userid The user ID to assign.
     * @param string $role The role (dean, lead_reviewer, reviewer).
     * @return self The created assignment.
     * @throws \moodle_exception If assignment already exists.
     */
    public static function assign(int $facultyid, int $userid, string $role = 'reviewer'): self {
        global $DB, $USER;

        // Check if already assigned.
        $existing = self::get_by_faculty_user($facultyid, $userid);
        if ($existing) {
            if ($existing->status === self::STATUS_ACTIVE) {
                throw new \moodle_exception('error:alreadyassigned', 'local_jobboard');
            }
            // Reactivate inactive assignment.
            $existing->reactivate($role);
            return $existing;
        }

        // Validate role.
        if (!in_array($role, [self::ROLE_DEAN, self::ROLE_LEAD, self::ROLE_REVIEWER])) {
            $role = self::ROLE_REVIEWER;
        }

        $assignment = new self();
        $assignment->facultyid = $facultyid;
        $assignment->userid = $userid;
        $assignment->role = $role;
        $assignment->status = self::STATUS_ACTIVE;
        $assignment->addedby = $USER->id;
        $assignment->timecreated = time();

        $record = $assignment->to_record();
        unset($record->id);
        $assignment->id = $DB->insert_record('local_jobboard_faculty_reviewer', $record);

        // Log audit.
        audit::log(
            audit::ACTION_CREATE,
            'faculty_reviewer',
            $assignment->id,
            [
                'facultyid' => $facultyid,
                'userid' => $userid,
                'role' => $role,
            ]
        );

        return $assignment;
    }

    /**
     * Reactivate an inactive assignment.
     *
     * @param string|null $newrole Optional new role to set.
     */
    public function reactivate(?string $newrole = null): void {
        global $DB, $USER;

        if ($this->status === self::STATUS_ACTIVE) {
            return;
        }

        $oldstatus = $this->status;
        $this->status = self::STATUS_ACTIVE;
        $this->timemodified = time();

        if ($newrole && in_array($newrole, [self::ROLE_DEAN, self::ROLE_LEAD, self::ROLE_REVIEWER])) {
            $this->role = $newrole;
        }

        $DB->update_record('local_jobboard_faculty_reviewer', $this->to_record());

        // Log audit.
        audit::log(
            audit::ACTION_UPDATE,
            'faculty_reviewer',
            $this->id,
            [
                'facultyid' => $this->facultyid,
                'userid' => $this->userid,
                'action' => 'reactivated',
                'previous_status' => $oldstatus,
            ]
        );
    }

    /**
     * Deactivate this assignment.
     */
    public function deactivate(): void {
        global $DB;

        if ($this->status === self::STATUS_INACTIVE) {
            return;
        }

        $this->status = self::STATUS_INACTIVE;
        $this->timemodified = time();

        $DB->update_record('local_jobboard_faculty_reviewer', $this->to_record());

        // Log audit.
        audit::log(
            audit::ACTION_UPDATE,
            'faculty_reviewer',
            $this->id,
            [
                'facultyid' => $this->facultyid,
                'userid' => $this->userid,
                'action' => 'deactivated',
            ]
        );
    }

    /**
     * Delete this assignment permanently.
     */
    public function delete(): void {
        global $DB;

        // Log audit before deletion.
        audit::log(
            audit::ACTION_DELETE,
            'faculty_reviewer',
            $this->id,
            [
                'facultyid' => $this->facultyid,
                'userid' => $this->userid,
                'role' => $this->role,
            ]
        );

        $DB->delete_records('local_jobboard_faculty_reviewer', ['id' => $this->id]);
    }

    /**
     * Update the role for this assignment.
     *
     * @param string $newrole The new role.
     */
    public function update_role(string $newrole): void {
        global $DB;

        if (!in_array($newrole, [self::ROLE_DEAN, self::ROLE_LEAD, self::ROLE_REVIEWER])) {
            return;
        }

        $oldrole = $this->role;
        $this->role = $newrole;
        $this->timemodified = time();

        $DB->update_record('local_jobboard_faculty_reviewer', $this->to_record());

        // Log audit.
        audit::log(
            audit::ACTION_UPDATE,
            'faculty_reviewer',
            $this->id,
            [
                'facultyid' => $this->facultyid,
                'userid' => $this->userid,
                'action' => 'role_changed',
                'previous_role' => $oldrole,
                'new_role' => $newrole,
            ]
        );
    }

    /**
     * Get the faculty object.
     *
     * @return \stdClass|null The faculty record.
     */
    public function get_faculty(): ?\stdClass {
        global $DB;

        if ($this->faculty === null && $this->facultyid) {
            $this->faculty = $DB->get_record('local_jobboard_faculty', ['id' => $this->facultyid]);
        }
        return $this->faculty;
    }

    /**
     * Get the user object.
     *
     * @return \stdClass|false The user record.
     */
    public function get_user() {
        if ($this->user === null && $this->userid) {
            $this->user = \core_user::get_user($this->userid);
        }
        return $this->user;
    }

    /**
     * Get role display name.
     *
     * @return string The localized role name.
     */
    public function get_role_display(): string {
        return get_string('faculty_reviewer_role_' . $this->role, 'local_jobboard');
    }

    /**
     * Get status display name.
     *
     * @return string The localized status name.
     */
    public function get_status_display(): string {
        return get_string('faculty_reviewer_status_' . $this->status, 'local_jobboard');
    }

    /**
     * Convert to database record.
     *
     * @return \stdClass The database record.
     */
    public function to_record(): \stdClass {
        return (object) [
            'id' => $this->id ?: null,
            'facultyid' => $this->facultyid,
            'userid' => $this->userid,
            'role' => $this->role,
            'status' => $this->status,
            'addedby' => $this->addedby,
            'timecreated' => $this->timecreated,
            'timemodified' => $this->timemodified,
        ];
    }

    /**
     * Get all faculty reviewers with user and faculty details.
     *
     * @param array $filters Optional filters (facultyid, userid, role, status).
     * @return array Array of records with joined data.
     */
    public static function get_list(array $filters = []): array {
        global $DB;

        $params = [];
        $where = ['1=1'];

        if (!empty($filters['facultyid'])) {
            $where[] = 'fr.facultyid = :facultyid';
            $params['facultyid'] = $filters['facultyid'];
        }

        if (!empty($filters['userid'])) {
            $where[] = 'fr.userid = :userid';
            $params['userid'] = $filters['userid'];
        }

        if (!empty($filters['role'])) {
            $where[] = 'fr.role = :role';
            $params['role'] = $filters['role'];
        }

        if (!empty($filters['status'])) {
            $where[] = 'fr.status = :status';
            $params['status'] = $filters['status'];
        } else {
            // Default to active only.
            $where[] = 'fr.status = :status';
            $params['status'] = self::STATUS_ACTIVE;
        }

        if (!empty($filters['companyid'])) {
            $where[] = 'f.companyid = :companyid';
            $params['companyid'] = $filters['companyid'];
        }

        $wheresql = implode(' AND ', $where);

        $sql = "SELECT fr.*,
                       f.code as faculty_code, f.name as faculty_name, f.companyid,
                       u.firstname, u.lastname, u.email
                FROM {local_jobboard_faculty_reviewer} fr
                JOIN {local_jobboard_faculty} f ON f.id = fr.facultyid
                JOIN {user} u ON u.id = fr.userid
                WHERE $wheresql
                ORDER BY f.name ASC, fr.role ASC, u.lastname ASC";

        return $DB->get_records_sql($sql, $params);
    }
}
