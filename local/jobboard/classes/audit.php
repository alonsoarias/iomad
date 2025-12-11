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
 * Enhanced Audit log class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\trait\request_helper;

/**
 * Class for handling audit logging with enhanced tracking capabilities.
 */
class audit {

    use request_helper;

    // Action constants.
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW = 'view';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_VALIDATE = 'validate';
    const ACTION_REJECT = 'reject';
    const ACTION_SUBMIT = 'submit';
    const ACTION_TRANSITION = 'transition';
    const ACTION_EMAIL_SENT = 'email_sent';
    const ACTION_LOGIN = 'login';
    const ACTION_EXPORT = 'export';
    const ACTION_UPLOAD = 'upload';
    const ACTION_APPROVE = 'approve';
    const ACTION_REVOKE = 'revoke';
    const ACTION_ASSIGN = 'assign';
    const ACTION_REOPEN = 'reopen';
    const ACTION_PUBLISH = 'publish';
    const ACTION_CLOSE = 'close';
    const ACTION_REUPLOAD = 'reupload';
    const ACTION_IMPORT = 'import';

    // Entity constants.
    const ENTITY_VACANCY = 'vacancy';
    const ENTITY_APPLICATION = 'application';
    const ENTITY_DOCUMENT = 'document';
    const ENTITY_EXEMPTION = 'exemption';
    const ENTITY_CONVOCATORIA = 'convocatoria';
    const ENTITY_CONFIG = 'config';
    const ENTITY_USER = 'user';
    const ENTITY_EMAIL_TEMPLATE = 'email_template';
    const ENTITY_INTERVIEW = 'interview';
    const ENTITY_COMMITTEE = 'committee';
    const ENTITY_EVALUATION = 'evaluation';
    const ENTITY_DECISION = 'decision';
    const ENTITY_CONSENT = 'consent';
    const ENTITY_PROFILE = 'applicant_profile';
    const ENTITY_DOCTYPE = 'doctype';
    const ENTITY_NOTIFICATION = 'notification';
    const ENTITY_DOC_REQUIREMENT = 'doc_requirement';
    const ENTITY_WORKFLOW_LOG = 'workflow_log';

    /**
     * Log an action to the audit table with enhanced tracking.
     *
     * @param string $action The action performed (use class constants).
     * @param string $entitytype The type of entity (use class constants).
     * @param int|null $entityid The entity ID.
     * @param mixed $extradata Additional data to log (will be JSON encoded).
     * @param mixed $previousvalue Previous value before change (will be JSON encoded).
     * @param mixed $newvalue New value after change (will be JSON encoded).
     * @param int|null $userid The user ID (or null for current user).
     * @return int|bool Record ID or false on failure.
     */
    public static function log(
        string $action,
        string $entitytype,
        ?int $entityid = null,
        $extradata = null,
        $previousvalue = null,
        $newvalue = null,
        ?int $userid = null
    ) {
        global $DB, $USER;

        $record = new \stdClass();
        $record->userid = $userid ?? ($USER->id ?? 0);
        $record->action = $action;
        $record->entitytype = $entitytype;
        $record->entityid = $entityid;
        $record->ipaddress = self::get_user_ip();
        $record->useragent = substr(self::get_user_agent(), 0, 512);
        $record->previousvalue = $previousvalue !== null ? json_encode($previousvalue) : null;
        $record->newvalue = $newvalue !== null ? json_encode($newvalue) : null;
        $record->extradata = $extradata !== null ? json_encode($extradata) : null;
        $record->timecreated = time();

        try {
            return $DB->insert_record('local_jobboard_audit', $record);
        } catch (\Exception $e) {
            // Silently fail - don't break main operation if audit fails.
            debugging('Failed to log audit record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Log a state transition with before/after values.
     *
     * @param string $entitytype The entity type.
     * @param int $entityid The entity ID.
     * @param string $field The field that changed.
     * @param mixed $oldvalue Previous value.
     * @param mixed $newvalue New value.
     * @param array $extradata Additional context.
     * @return int|bool Record ID or false on failure.
     */
    public static function log_transition(
        string $entitytype,
        int $entityid,
        string $field,
        $oldvalue,
        $newvalue,
        array $extradata = []
    ) {
        $extradata['field'] = $field;
        $extradata['transition'] = $oldvalue . ' -> ' . $newvalue;

        return self::log(
            self::ACTION_TRANSITION,
            $entitytype,
            $entityid,
            $extradata,
            [$field => $oldvalue],
            [$field => $newvalue]
        );
    }

    /**
     * Log an email sent event.
     *
     * @param string $templatekey The email template key.
     * @param int $recipientid The recipient user ID.
     * @param int|null $entityid Related entity ID (e.g., application ID).
     * @param string $entitytype Related entity type.
     * @param array $extradata Additional context.
     * @return int|bool Record ID or false on failure.
     */
    public static function log_email(
        string $templatekey,
        int $recipientid,
        ?int $entityid = null,
        string $entitytype = self::ENTITY_APPLICATION,
        array $extradata = []
    ) {
        global $DB;

        $recipient = $DB->get_record('user', ['id' => $recipientid], 'email');
        $extradata['template'] = $templatekey;
        $extradata['recipient_email'] = $recipient ? $recipient->email : 'unknown';
        $extradata['sent_at'] = time();

        return self::log(
            self::ACTION_EMAIL_SENT,
            $entitytype,
            $entityid,
            $extradata
        );
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
            $where[] = 'a.userid = :userid';
            $params['userid'] = $filters['userid'];
        }

        if (!empty($filters['action'])) {
            $where[] = 'a.action = :action';
            $params['action'] = $filters['action'];
        }

        if (!empty($filters['entitytype'])) {
            $where[] = 'a.entitytype = :entitytype';
            $params['entitytype'] = $filters['entitytype'];
        }

        if (!empty($filters['entityid'])) {
            $where[] = 'a.entityid = :entityid';
            $params['entityid'] = $filters['entityid'];
        }

        if (!empty($filters['datefrom'])) {
            $where[] = 'a.timecreated >= :datefrom';
            $params['datefrom'] = $filters['datefrom'];
        }

        if (!empty($filters['dateto'])) {
            $where[] = 'a.timecreated <= :dateto';
            $params['dateto'] = $filters['dateto'];
        }

        $wheresql = implode(' AND ', $where);

        $total = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_audit} a WHERE $wheresql",
            $params
        );

        $sql = "SELECT a.*, u.firstname, u.lastname, u.email
                FROM {local_jobboard_audit} a
                LEFT JOIN {user} u ON u.id = a.userid
                WHERE $wheresql
                ORDER BY a.timecreated DESC";

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        // Decode JSON fields.
        foreach ($records as $record) {
            $record->extradata_decoded = $record->extradata ? json_decode($record->extradata, true) : null;
            $record->previousvalue_decoded = $record->previousvalue ? json_decode($record->previousvalue, true) : null;
            $record->newvalue_decoded = $record->newvalue ? json_decode($record->newvalue, true) : null;
        }

        return [
            'records' => $records,
            'total' => $total,
        ];
    }

    /**
     * Get audit trail for a specific entity.
     *
     * @param string $entitytype The entity type.
     * @param int $entityid The entity ID.
     * @param int $limit Maximum records to return.
     * @return array Array of audit records.
     */
    public static function get_trail(string $entitytype, int $entityid, int $limit = 50): array {
        global $DB;

        $records = $DB->get_records_sql(
            "SELECT a.*, u.firstname, u.lastname
             FROM {local_jobboard_audit} a
             LEFT JOIN {user} u ON u.id = a.userid
             WHERE a.entitytype = :entitytype AND a.entityid = :entityid
             ORDER BY a.timecreated DESC",
            ['entitytype' => $entitytype, 'entityid' => $entityid],
            0,
            $limit
        );

        // Decode JSON fields.
        foreach ($records as $record) {
            $record->extradata_decoded = $record->extradata ? json_decode($record->extradata, true) : null;
            $record->previousvalue_decoded = $record->previousvalue ? json_decode($record->previousvalue, true) : null;
            $record->newvalue_decoded = $record->newvalue ? json_decode($record->newvalue, true) : null;
        }

        return $records;
    }

    /**
     * Get audit records for a specific entity (alias for get_trail).
     *
     * @param string $entitytype The entity type.
     * @param int $entityid The entity ID.
     * @return array Array of audit records.
     */
    public static function get_entity_history(string $entitytype, int $entityid): array {
        return self::get_trail($entitytype, $entityid);
    }

    /**
     * Get user activity log.
     *
     * @param int $userid User ID.
     * @param int $limit Maximum records to return.
     * @return array Array of audit records.
     */
    public static function get_user_activity(int $userid, int $limit = 100): array {
        global $DB;

        return $DB->get_records_sql(
            "SELECT a.*, u.firstname, u.lastname
             FROM {local_jobboard_audit} a
             LEFT JOIN {user} u ON u.id = a.userid
             WHERE a.userid = :userid
             ORDER BY a.timecreated DESC",
            ['userid' => $userid],
            0,
            $limit
        );
    }

    /**
     * Get recent activity for dashboard.
     *
     * @param int $limit Maximum records.
     * @param array $entitytypes Filter by entity types.
     * @return array Array of audit records.
     */
    public static function get_recent_activity(int $limit = 20, array $entitytypes = []): array {
        global $DB;

        $wheresql = '1=1';
        $params = [];

        if (!empty($entitytypes)) {
            list($insql, $inparams) = $DB->get_in_or_equal($entitytypes, SQL_PARAMS_NAMED, 'et');
            $wheresql .= " AND a.entitytype $insql";
            $params = array_merge($params, $inparams);
        }

        return $DB->get_records_sql(
            "SELECT a.*, u.firstname, u.lastname
             FROM {local_jobboard_audit} a
             LEFT JOIN {user} u ON u.id = a.userid
             WHERE $wheresql
             ORDER BY a.timecreated DESC",
            $params,
            0,
            $limit
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

        $count = $DB->count_records_select(
            'local_jobboard_audit',
            'timecreated < :cutoff',
            ['cutoff' => $cutoff]
        );

        if ($count > 0) {
            $DB->delete_records_select(
                'local_jobboard_audit',
                'timecreated < :cutoff',
                ['cutoff' => $cutoff]
            );
        }

        return $count;
    }

    /**
     * Format action for display.
     *
     * @param string $action The action code.
     * @return string Localized action name.
     */
    public static function format_action(string $action): string {
        $stringkey = 'audit_action_' . $action;
        $sm = get_string_manager();

        if ($sm->string_exists($stringkey, 'local_jobboard')) {
            return get_string($stringkey, 'local_jobboard');
        }

        return ucfirst(str_replace('_', ' ', $action));
    }

    /**
     * Format entity type for display.
     *
     * @param string $entitytype The entity type code.
     * @return string Localized entity type name.
     */
    public static function format_entitytype(string $entitytype): string {
        $stringkey = 'audit_entity_' . $entitytype;
        $sm = get_string_manager();

        if ($sm->string_exists($stringkey, 'local_jobboard')) {
            return get_string($stringkey, 'local_jobboard');
        }

        return ucfirst(str_replace('_', ' ', $entitytype));
    }
}
