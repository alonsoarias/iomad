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
 * Faculty reviewer management class.
 *
 * Handles document reviewer assignments per faculty/company.
 * Similar to committee but simpler - just reviewers assigned to faculties.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faculty_reviewer {

    /** @var string Role - lead reviewer (can assign other reviewers). */
    const ROLE_LEAD = 'lead_reviewer';

    /** @var string Role - regular reviewer. */
    const ROLE_REVIEWER = 'reviewer';

    /** @var string Status - active. */
    const STATUS_ACTIVE = 'active';

    /** @var string Status - inactive. */
    const STATUS_INACTIVE = 'inactive';

    /**
     * Add a reviewer to a faculty/company.
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @param int $userid User ID of the reviewer.
     * @param string $role Role (lead_reviewer or reviewer).
     * @return int|bool Record ID on success, false on failure.
     */
    public static function add(int $companyid, int $userid, string $role = self::ROLE_REVIEWER) {
        global $DB, $USER;

        // Validate role.
        if (!in_array($role, [self::ROLE_LEAD, self::ROLE_REVIEWER])) {
            $role = self::ROLE_REVIEWER;
        }

        // Check if already assigned.
        if ($DB->record_exists('local_jobboard_faculty_reviewer', [
            'companyid' => $companyid,
            'userid' => $userid,
        ])) {
            return false;
        }

        // Verify user has reviewer capability.
        $context = \context_system::instance();
        if (!has_capability('local/jobboard:reviewdocuments', $context, $userid)) {
            return false;
        }

        $record = new \stdClass();
        $record->companyid = $companyid;
        $record->userid = $userid;
        $record->role = $role;
        $record->status = self::STATUS_ACTIVE;
        $record->addedby = $USER->id;
        $record->timecreated = time();

        $id = $DB->insert_record('local_jobboard_faculty_reviewer', $record);

        if ($id) {
            audit::log('faculty_reviewer_added', 'local_jobboard_faculty_reviewer', $id, [
                'companyid' => $companyid,
                'userid' => $userid,
                'role' => $role,
            ]);
        }

        return $id;
    }

    /**
     * Remove a reviewer from a faculty/company.
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @param int $userid User ID of the reviewer.
     * @return bool Success.
     */
    public static function remove(int $companyid, int $userid): bool {
        global $DB, $USER;

        $record = $DB->get_record('local_jobboard_faculty_reviewer', [
            'companyid' => $companyid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return false;
        }

        // Cannot remove the only lead reviewer.
        if ($record->role === self::ROLE_LEAD) {
            $otherleads = $DB->count_records_select('local_jobboard_faculty_reviewer',
                'companyid = :companyid AND role = :role AND userid != :userid AND status = :status',
                [
                    'companyid' => $companyid,
                    'role' => self::ROLE_LEAD,
                    'userid' => $userid,
                    'status' => self::STATUS_ACTIVE,
                ]
            );
            if ($otherleads == 0) {
                // Check if there are other active reviewers who can become lead.
                $otherreviewers = $DB->count_records_select('local_jobboard_faculty_reviewer',
                    'companyid = :companyid AND userid != :userid AND status = :status',
                    [
                        'companyid' => $companyid,
                        'userid' => $userid,
                        'status' => self::STATUS_ACTIVE,
                    ]
                );
                if ($otherreviewers == 0) {
                    // OK to remove the last reviewer.
                } else {
                    return false; // Promote another reviewer first.
                }
            }
        }

        $result = $DB->delete_records('local_jobboard_faculty_reviewer', [
            'companyid' => $companyid,
            'userid' => $userid,
        ]);

        if ($result) {
            audit::log('faculty_reviewer_removed', 'local_jobboard_faculty_reviewer', $record->id, [
                'companyid' => $companyid,
                'userid' => $userid,
                'removedby' => $USER->id,
            ]);
        }

        return $result;
    }

    /**
     * Update reviewer role.
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @param int $userid User ID.
     * @param string $newrole New role.
     * @return bool Success.
     */
    public static function update_role(int $companyid, int $userid, string $newrole): bool {
        global $DB;

        if (!in_array($newrole, [self::ROLE_LEAD, self::ROLE_REVIEWER])) {
            return false;
        }

        $record = $DB->get_record('local_jobboard_faculty_reviewer', [
            'companyid' => $companyid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return false;
        }

        $record->role = $newrole;
        $record->timemodified = time();

        return $DB->update_record('local_jobboard_faculty_reviewer', $record);
    }

    /**
     * Toggle reviewer status (active/inactive).
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @param int $userid User ID.
     * @return bool Success.
     */
    public static function toggle_status(int $companyid, int $userid): bool {
        global $DB;

        $record = $DB->get_record('local_jobboard_faculty_reviewer', [
            'companyid' => $companyid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return false;
        }

        $record->status = ($record->status === self::STATUS_ACTIVE) ?
            self::STATUS_INACTIVE : self::STATUS_ACTIVE;
        $record->timemodified = time();

        return $DB->update_record('local_jobboard_faculty_reviewer', $record);
    }

    /**
     * Get all reviewers for a faculty/company.
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @param bool $activeonly Only return active reviewers.
     * @return array Array of reviewer records with user info.
     */
    public static function get_for_company(int $companyid, bool $activeonly = true): array {
        global $DB;

        $params = ['companyid' => $companyid];
        $statuswhere = '';

        if ($activeonly) {
            $statuswhere = 'AND fr.status = :status';
            $params['status'] = self::STATUS_ACTIVE;
        }

        $sql = "SELECT fr.*, u.firstname, u.lastname, u.email, u.username
                  FROM {local_jobboard_faculty_reviewer} fr
                  JOIN {user} u ON u.id = fr.userid
                 WHERE fr.companyid = :companyid
                       $statuswhere
                 ORDER BY
                    CASE fr.role
                        WHEN 'lead_reviewer' THEN 1
                        WHEN 'reviewer' THEN 2
                    END, u.lastname, u.firstname";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get reviewers for a vacancy based on its faculty.
     *
     * @param int $vacancyid Vacancy ID.
     * @return array Array of reviewer records.
     */
    public static function get_for_vacancy(int $vacancyid): array {
        global $DB;

        // Get the vacancy's company.
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
        if (!$vacancy || empty($vacancy->companyid)) {
            // Fall back to global reviewers.
            return self::get_all_active();
        }

        return self::get_for_company($vacancy->companyid);
    }

    /**
     * Get all active reviewers across all faculties.
     *
     * @return array Array of reviewer records grouped by company.
     */
    public static function get_all_active(): array {
        global $DB;

        $sql = "SELECT fr.*, u.firstname, u.lastname, u.email, u.username,
                       c.name as company_name, c.shortname as company_shortname
                  FROM {local_jobboard_faculty_reviewer} fr
                  JOIN {user} u ON u.id = fr.userid
             LEFT JOIN {company} c ON c.id = fr.companyid
                 WHERE fr.status = :status
                 ORDER BY c.name, fr.role, u.lastname";

        return $DB->get_records_sql($sql, ['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get reviewer statistics.
     *
     * @return array Statistics array.
     */
    public static function get_statistics(): array {
        global $DB;

        $stats = [
            'total_reviewers' => 0,
            'active_reviewers' => 0,
            'lead_reviewers' => 0,
            'faculties_with_reviewers' => 0,
        ];

        $stats['total_reviewers'] = $DB->count_records('local_jobboard_faculty_reviewer');
        $stats['active_reviewers'] = $DB->count_records('local_jobboard_faculty_reviewer',
            ['status' => self::STATUS_ACTIVE]);
        $stats['lead_reviewers'] = $DB->count_records('local_jobboard_faculty_reviewer',
            ['role' => self::ROLE_LEAD, 'status' => self::STATUS_ACTIVE]);
        $stats['faculties_with_reviewers'] = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT companyid) FROM {local_jobboard_faculty_reviewer} WHERE status = :status",
            ['status' => self::STATUS_ACTIVE]
        );

        return $stats;
    }

    /**
     * Get all companies/faculties with their reviewer count.
     *
     * @return array Array of companies with reviewer counts.
     */
    public static function get_companies_with_reviewers(): array {
        global $DB;

        $sql = "SELECT c.id, c.name, c.shortname,
                       COALESCE(rv.reviewer_count, 0) as reviewer_count,
                       COALESCE(rv.lead_count, 0) as lead_count
                  FROM {company} c
             LEFT JOIN (
                 SELECT companyid,
                        COUNT(*) as reviewer_count,
                        SUM(CASE WHEN role = 'lead_reviewer' THEN 1 ELSE 0 END) as lead_count
                   FROM {local_jobboard_faculty_reviewer}
                  WHERE status = 'active'
                  GROUP BY companyid
             ) rv ON rv.companyid = c.id
                 WHERE c.id IN (
                     SELECT DISTINCT companyid FROM {local_jobboard_vacancy} WHERE companyid IS NOT NULL
                     UNION
                     SELECT DISTINCT companyid FROM {local_jobboard_faculty_reviewer}
                 )
                 ORDER BY c.name";

        return $DB->get_records_sql($sql);
    }

    /**
     * Check if a user is a faculty reviewer.
     *
     * @param int $userid User ID.
     * @param int $companyid Optional company ID to check specific faculty.
     * @return bool True if user is a faculty reviewer.
     */
    public static function is_reviewer(int $userid, int $companyid = 0): bool {
        global $DB;

        $params = ['userid' => $userid, 'status' => self::STATUS_ACTIVE];
        $where = 'userid = :userid AND status = :status';

        if ($companyid) {
            $where .= ' AND companyid = :companyid';
            $params['companyid'] = $companyid;
        }

        return $DB->record_exists_select('local_jobboard_faculty_reviewer', $where, $params);
    }

    /**
     * Check if a user is a lead reviewer for any faculty.
     *
     * @param int $userid User ID.
     * @param int $companyid Optional company ID to check specific faculty.
     * @return bool True if user is a lead reviewer.
     */
    public static function is_lead_reviewer(int $userid, int $companyid = 0): bool {
        global $DB;

        $params = [
            'userid' => $userid,
            'role' => self::ROLE_LEAD,
            'status' => self::STATUS_ACTIVE,
        ];
        $where = 'userid = :userid AND role = :role AND status = :status';

        if ($companyid) {
            $where .= ' AND companyid = :companyid';
            $params['companyid'] = $companyid;
        }

        return $DB->record_exists_select('local_jobboard_faculty_reviewer', $where, $params);
    }

    /**
     * Get faculties where user is assigned as reviewer.
     *
     * @param int $userid User ID.
     * @return array Array of company records.
     */
    public static function get_user_faculties(int $userid): array {
        global $DB;

        $sql = "SELECT c.*, fr.role, fr.status
                  FROM {local_jobboard_faculty_reviewer} fr
                  JOIN {company} c ON c.id = fr.companyid
                 WHERE fr.userid = :userid
                   AND fr.status = :status
                 ORDER BY c.name";

        return $DB->get_records_sql($sql, [
            'userid' => $userid,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Get reviewer workload for a faculty.
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @return array Array of reviewers with their current workload.
     */
    public static function get_workload_for_company(int $companyid): array {
        global $DB;

        $reviewers = self::get_for_company($companyid);

        foreach ($reviewers as &$rev) {
            // Get pending review count.
            $rev->pending_reviews = $DB->count_records_sql(
                "SELECT COUNT(*)
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                  WHERE a.reviewerid = :reviewerid
                    AND v.companyid = :companyid
                    AND a.status IN ('submitted', 'under_review')",
                ['reviewerid' => $rev->userid, 'companyid' => $companyid]
            );

            // Get completed reviews count.
            $rev->completed_reviews = $DB->count_records_sql(
                "SELECT COUNT(*)
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                  WHERE a.reviewerid = :reviewerid
                    AND v.companyid = :companyid
                    AND a.status NOT IN ('submitted', 'under_review', 'withdrawn')",
                ['reviewerid' => $rev->userid, 'companyid' => $companyid]
            );
        }

        return $reviewers;
    }
}
