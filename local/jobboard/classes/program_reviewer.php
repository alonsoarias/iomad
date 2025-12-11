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

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Program reviewer management class.
 *
 * Manages document reviewers assigned to academic programs (course categories).
 * A reviewer can be assigned to multiple programs and each program can have
 * multiple reviewers including lead reviewers.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class program_reviewer {

    /** @var string Role: Lead reviewer - can assign work to other reviewers. */
    public const ROLE_LEAD = 'lead_reviewer';

    /** @var string Role: Regular reviewer. */
    public const ROLE_REVIEWER = 'reviewer';

    /** @var string Status: Active reviewer. */
    public const STATUS_ACTIVE = 'active';

    /** @var string Status: Inactive reviewer. */
    public const STATUS_INACTIVE = 'inactive';

    /** @var string Table name. */
    private const TABLE = 'local_jobboard_program_reviewer';

    /**
     * Add a reviewer to a program.
     *
     * @param int $categoryid Course category ID (program).
     * @param int $userid User ID.
     * @param string $role Reviewer role.
     * @return int|false Record ID or false on failure.
     */
    public static function add(int $categoryid, int $userid, string $role = self::ROLE_REVIEWER) {
        global $DB, $USER;

        // Check if already exists.
        if ($DB->record_exists(self::TABLE, ['categoryid' => $categoryid, 'userid' => $userid])) {
            return false;
        }

        // Verify user has reviewer capability.
        $context = \context_system::instance();
        if (!has_capability('local/jobboard:review', $context, $userid)) {
            return false;
        }

        // Verify category exists.
        if (!$DB->record_exists('course_categories', ['id' => $categoryid])) {
            return false;
        }

        $record = new \stdClass();
        $record->categoryid = $categoryid;
        $record->userid = $userid;
        $record->role = $role;
        $record->status = self::STATUS_ACTIVE;
        $record->addedby = $USER->id;
        $record->timecreated = time();

        $id = $DB->insert_record(self::TABLE, $record);

        if ($id) {
            audit::log('program_reviewer_added', 'program_reviewer', $id, [
                'categoryid' => $categoryid,
                'userid' => $userid,
                'role' => $role,
            ]);
        }

        return $id;
    }

    /**
     * Remove a reviewer from a program.
     *
     * @param int $categoryid Course category ID.
     * @param int $userid User ID.
     * @return bool Success.
     */
    public static function remove(int $categoryid, int $userid): bool {
        global $DB;

        $record = $DB->get_record(self::TABLE, [
            'categoryid' => $categoryid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return false;
        }

        // Don't allow removing the last active lead reviewer.
        if ($record->role === self::ROLE_LEAD && $record->status === self::STATUS_ACTIVE) {
            $otherleads = $DB->count_records(self::TABLE, [
                'categoryid' => $categoryid,
                'role' => self::ROLE_LEAD,
                'status' => self::STATUS_ACTIVE,
            ]);
            if ($otherleads <= 1) {
                return false;
            }
        }

        $result = $DB->delete_records(self::TABLE, ['id' => $record->id]);

        if ($result) {
            audit::log('program_reviewer_removed', 'program_reviewer', $record->id, [
                'categoryid' => $categoryid,
                'userid' => $userid,
            ]);
        }

        return $result;
    }

    /**
     * Update reviewer role.
     *
     * @param int $categoryid Course category ID.
     * @param int $userid User ID.
     * @param string $newrole New role.
     * @return bool Success.
     */
    public static function update_role(int $categoryid, int $userid, string $newrole): bool {
        global $DB;

        $record = $DB->get_record(self::TABLE, [
            'categoryid' => $categoryid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return false;
        }

        $oldrole = $record->role;
        $record->role = $newrole;
        $record->timemodified = time();

        $result = $DB->update_record(self::TABLE, $record);

        if ($result) {
            audit::log('program_reviewer_role_changed', 'program_reviewer', $record->id, [
                'categoryid' => $categoryid,
                'userid' => $userid,
                'old_role' => $oldrole,
                'new_role' => $newrole,
            ]);
        }

        return $result;
    }

    /**
     * Toggle reviewer status (active/inactive).
     *
     * @param int $categoryid Course category ID.
     * @param int $userid User ID.
     * @return bool Success.
     */
    public static function toggle_status(int $categoryid, int $userid): bool {
        global $DB;

        $record = $DB->get_record(self::TABLE, [
            'categoryid' => $categoryid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return false;
        }

        $newstatus = ($record->status === self::STATUS_ACTIVE) ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;

        // Don't allow deactivating the last active lead reviewer.
        if ($newstatus === self::STATUS_INACTIVE && $record->role === self::ROLE_LEAD) {
            $otherleads = $DB->count_records(self::TABLE, [
                'categoryid' => $categoryid,
                'role' => self::ROLE_LEAD,
                'status' => self::STATUS_ACTIVE,
            ]);
            if ($otherleads <= 1) {
                return false;
            }
        }

        $record->status = $newstatus;
        $record->timemodified = time();

        return $DB->update_record(self::TABLE, $record);
    }

    /**
     * Get all reviewers for a program.
     *
     * @param int $categoryid Course category ID.
     * @param bool $activeonly Only return active reviewers.
     * @return array Array of reviewer records with user data.
     */
    public static function get_for_program(int $categoryid, bool $activeonly = true): array {
        global $DB;

        $params = ['categoryid' => $categoryid];
        $where = 'pr.categoryid = :categoryid';

        if ($activeonly) {
            $where .= ' AND pr.status = :status';
            $params['status'] = self::STATUS_ACTIVE;
        }

        $sql = "SELECT pr.*, u.firstname, u.lastname, u.email, u.picture, u.imagealt
                  FROM {" . self::TABLE . "} pr
                  JOIN {user} u ON u.id = pr.userid
                 WHERE $where
              ORDER BY pr.role DESC, u.lastname, u.firstname";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get all programs a user reviews.
     *
     * @param int $userid User ID.
     * @param bool $activeonly Only return active assignments.
     * @return array Array of category records with reviewer info.
     */
    public static function get_user_programs(int $userid, bool $activeonly = true): array {
        global $DB;

        $params = ['userid' => $userid];
        $where = 'pr.userid = :userid';

        if ($activeonly) {
            $where .= ' AND pr.status = :status';
            $params['status'] = self::STATUS_ACTIVE;
        }

        $sql = "SELECT cc.id, cc.name, cc.parent, cc.path, pr.role, pr.status, pr.timecreated
                  FROM {" . self::TABLE . "} pr
                  JOIN {course_categories} cc ON cc.id = pr.categoryid
                 WHERE $where
              ORDER BY cc.name";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get all active reviewers across all programs.
     *
     * @return array Array of reviewer records.
     */
    public static function get_all_active(): array {
        global $DB;

        $sql = "SELECT pr.*, u.firstname, u.lastname, u.email, cc.name as programname
                  FROM {" . self::TABLE . "} pr
                  JOIN {user} u ON u.id = pr.userid
                  JOIN {course_categories} cc ON cc.id = pr.categoryid
                 WHERE pr.status = :status
              ORDER BY cc.name, pr.role DESC, u.lastname";

        return $DB->get_records_sql($sql, ['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get statistics about program reviewers.
     *
     * @return array Statistics array.
     */
    public static function get_statistics(): array {
        global $DB;

        $stats = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'lead_reviewers' => 0,
            'programs_with_reviewers' => 0,
            'users_as_reviewers' => 0,
        ];

        $stats['total'] = $DB->count_records(self::TABLE);
        $stats['active'] = $DB->count_records(self::TABLE, ['status' => self::STATUS_ACTIVE]);
        $stats['inactive'] = $stats['total'] - $stats['active'];
        $stats['lead_reviewers'] = $DB->count_records(self::TABLE, [
            'role' => self::ROLE_LEAD,
            'status' => self::STATUS_ACTIVE,
        ]);

        // Count distinct programs with reviewers.
        $sql = "SELECT COUNT(DISTINCT categoryid) FROM {" . self::TABLE . "} WHERE status = :status";
        $stats['programs_with_reviewers'] = $DB->count_records_sql($sql, ['status' => self::STATUS_ACTIVE]);

        // Count distinct users who are reviewers.
        $sql = "SELECT COUNT(DISTINCT userid) FROM {" . self::TABLE . "} WHERE status = :status";
        $stats['users_as_reviewers'] = $DB->count_records_sql($sql, ['status' => self::STATUS_ACTIVE]);

        return $stats;
    }

    /**
     * Get programs with their reviewer counts.
     *
     * @return array Programs with reviewer counts.
     */
    public static function get_programs_with_reviewers(): array {
        global $DB;

        $sql = "SELECT cc.id, cc.name, cc.parent, cc.path,
                       COUNT(pr.id) as reviewer_count,
                       SUM(CASE WHEN pr.role = :leadrole THEN 1 ELSE 0 END) as lead_count
                  FROM {course_categories} cc
                  JOIN {" . self::TABLE . "} pr ON pr.categoryid = cc.id AND pr.status = :status
              GROUP BY cc.id, cc.name, cc.parent, cc.path
              ORDER BY cc.name";

        return $DB->get_records_sql($sql, [
            'status' => self::STATUS_ACTIVE,
            'leadrole' => self::ROLE_LEAD,
        ]);
    }

    /**
     * Get programs without reviewers.
     *
     * @param int $parentid Optional parent category ID to filter.
     * @return array Programs without reviewers.
     */
    public static function get_programs_without_reviewers(int $parentid = 0): array {
        global $DB;

        $params = [];
        $where = '';

        if ($parentid > 0) {
            $where = "WHERE cc.parent = :parentid";
            $params['parentid'] = $parentid;
        }

        $sql = "SELECT cc.id, cc.name, cc.parent, cc.path
                  FROM {course_categories} cc
                 WHERE NOT EXISTS (
                     SELECT 1 FROM {" . self::TABLE . "} pr
                      WHERE pr.categoryid = cc.id AND pr.status = :status
                 )
                 $where
              ORDER BY cc.name";

        $params['status'] = self::STATUS_ACTIVE;

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Check if a user is a reviewer for any program.
     *
     * @param int $userid User ID.
     * @param int $categoryid Optional specific program category ID.
     * @return bool True if user is a reviewer.
     */
    public static function is_reviewer(int $userid, int $categoryid = 0): bool {
        global $DB;

        $conditions = [
            'userid' => $userid,
            'status' => self::STATUS_ACTIVE,
        ];

        if ($categoryid > 0) {
            $conditions['categoryid'] = $categoryid;
        }

        return $DB->record_exists(self::TABLE, $conditions);
    }

    /**
     * Check if a user is a lead reviewer for any program.
     *
     * @param int $userid User ID.
     * @param int $categoryid Optional specific program category ID.
     * @return bool True if user is a lead reviewer.
     */
    public static function is_lead_reviewer(int $userid, int $categoryid = 0): bool {
        global $DB;

        $conditions = [
            'userid' => $userid,
            'role' => self::ROLE_LEAD,
            'status' => self::STATUS_ACTIVE,
        ];

        if ($categoryid > 0) {
            $conditions['categoryid'] = $categoryid;
        }

        return $DB->record_exists(self::TABLE, $conditions);
    }

    /**
     * Get reviewers for a vacancy (based on vacancy's program/category).
     *
     * @param int $vacancyid Vacancy ID.
     * @return array Array of reviewers.
     */
    public static function get_for_vacancy(int $vacancyid): array {
        global $DB;

        // Get vacancy to find its program (through convocatoria or direct assignment).
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
        if (!$vacancy) {
            return [];
        }

        // Get program from vacancy location/department or convocatoria.
        // This depends on how programs are linked to vacancies in your system.
        // For now, return all active reviewers.
        return self::get_all_active();
    }

    /**
     * Get workload statistics for reviewers in a program.
     *
     * @param int $categoryid Course category ID.
     * @return array Workload data per reviewer.
     */
    public static function get_workload_for_program(int $categoryid): array {
        global $DB;

        $reviewers = self::get_for_program($categoryid, true);

        foreach ($reviewers as &$reviewer) {
            // Count assigned applications.
            $reviewer->assigned_count = $DB->count_records('local_jobboard_application', [
                'reviewerid' => $reviewer->userid,
                'status' => 'under_review',
            ]);

            // Count completed reviews.
            $reviewer->completed_count = $DB->count_records_sql(
                "SELECT COUNT(DISTINCT a.id)
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_doc_validation} dv ON dv.validatedby = :userid
                   JOIN {local_jobboard_document} d ON d.id = dv.documentid AND d.applicationid = a.id
                  WHERE a.status IN ('docs_validated', 'docs_rejected')",
                ['userid' => $reviewer->userid]
            );
        }

        return $reviewers;
    }

    /**
     * Get hierarchy of categories for program selection.
     *
     * Returns faculty > program structure based on course categories.
     *
     * @return array Hierarchical category structure.
     */
    public static function get_category_hierarchy(): array {
        global $DB;

        // Get all course categories.
        $categories = $DB->get_records('course_categories', [], 'sortorder', 'id, name, parent, path, depth');

        // Build hierarchy.
        $hierarchy = [];
        foreach ($categories as $cat) {
            if ($cat->parent == 0) {
                // Top level - could be faculty.
                $hierarchy[$cat->id] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'programs' => [],
                ];
            }
        }

        // Add programs under faculties.
        foreach ($categories as $cat) {
            if ($cat->parent > 0 && isset($hierarchy[$cat->parent])) {
                $hierarchy[$cat->parent]['programs'][$cat->id] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                ];
            }
        }

        return $hierarchy;
    }
}
