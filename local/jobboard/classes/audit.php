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
 * Audit log class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\trait\request_helper;

/**
 * Class for handling audit logging.
 */
class audit {

    use request_helper;

    /**
     * Log an action to the audit table.
     *
     * @param string $action The action performed.
     * @param string $entitytype The type of entity (vacancy, application, document).
     * @param int|null $entityid The entity ID.
     * @param array $extradata Additional data to log.
     * @param int|null $userid The user ID (or null for current user).
     */
    public static function log(
        string $action,
        string $entitytype,
        ?int $entityid = null,
        array $extradata = [],
        ?int $userid = null
    ): void {
        global $DB, $USER;

        $record = new \stdClass();
        $record->userid = $userid ?? ($USER->id ?? 0);
        $record->action = $action;
        $record->entitytype = $entitytype;
        $record->entityid = $entityid;
        $record->ipaddress = self::get_user_ip();
        $record->useragent = self::get_user_agent();
        $record->extradata = !empty($extradata) ? json_encode($extradata) : null;
        $record->timecreated = time();

        try {
            $DB->insert_record('local_jobboard_audit', $record);
        } catch (\Exception $e) {
            // Silently fail - don't break main operation if audit fails.
            debugging('Failed to log audit record: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Get audit records with filtering.
     *
     * @param array $filters Filter options.
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array ['records' => array, 'total' => int]
     */
    public static function get_records(array $filters = [], int $page = 0, int $perpage = 50): array {
        global $DB;

        $params = [];
        $where = ['1=1'];

        if (!empty($filters['userid'])) {
            $where[] = 'userid = :userid';
            $params['userid'] = $filters['userid'];
        }

        if (!empty($filters['action'])) {
            $where[] = 'action = :action';
            $params['action'] = $filters['action'];
        }

        if (!empty($filters['entitytype'])) {
            $where[] = 'entitytype = :entitytype';
            $params['entitytype'] = $filters['entitytype'];
        }

        if (!empty($filters['entityid'])) {
            $where[] = 'entityid = :entityid';
            $params['entityid'] = $filters['entityid'];
        }

        if (!empty($filters['datefrom'])) {
            $where[] = 'timecreated >= :datefrom';
            $params['datefrom'] = $filters['datefrom'];
        }

        if (!empty($filters['dateto'])) {
            $where[] = 'timecreated <= :dateto';
            $params['dateto'] = $filters['dateto'];
        }

        $wheresql = implode(' AND ', $where);

        $total = $DB->count_records_sql("SELECT COUNT(*) FROM {local_jobboard_audit} WHERE $wheresql", $params);

        $sql = "SELECT a.*, u.firstname, u.lastname, u.email
                FROM {local_jobboard_audit} a
                LEFT JOIN {user} u ON u.id = a.userid
                WHERE $wheresql
                ORDER BY a.timecreated DESC";

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        return [
            'records' => $records,
            'total' => $total,
        ];
    }

    /**
     * Get audit records for a specific entity.
     *
     * @param string $entitytype The entity type.
     * @param int $entityid The entity ID.
     * @return array Array of audit records.
     */
    public static function get_entity_history(string $entitytype, int $entityid): array {
        global $DB;

        return $DB->get_records_sql(
            "SELECT a.*, u.firstname, u.lastname
             FROM {local_jobboard_audit} a
             LEFT JOIN {user} u ON u.id = a.userid
             WHERE a.entitytype = :entitytype AND a.entityid = :entityid
             ORDER BY a.timecreated DESC",
            ['entitytype' => $entitytype, 'entityid' => $entityid]
        );
    }

    /**
     * Clean up old audit records.
     *
     * @param int $daystokeep Number of days to keep records.
     * @return int Number of records deleted.
     */
    public static function cleanup(int $daystokeep = 365): int {
        global $DB;

        $cutoff = time() - ($daystokeep * 24 * 60 * 60);

        $count = $DB->count_records_select('local_jobboard_audit', 'timecreated < :cutoff', ['cutoff' => $cutoff]);

        if ($count > 0) {
            $DB->delete_records_select('local_jobboard_audit', 'timecreated < :cutoff', ['cutoff' => $cutoff]);
        }

        return $count;
    }
}
