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
 * ISER Exemption management class.
 *
 * Handles exemptions for historic ISER personnel who have reduced
 * documentation requirements due to prior employment history.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing ISER exemptions.
 */
class exemption {

    /** @var string Exemption type: Historic ISER personnel. */
    public const TYPE_HISTORICO_ISER = 'historico_iser';

    /** @var string Exemption type: Recently approved documents. */
    public const TYPE_DOCUMENTOS_RECIENTES = 'documentos_recientes';

    /** @var string Exemption type: Internal transfer. */
    public const TYPE_TRASLADO_INTERNO = 'traslado_interno';

    /** @var string Exemption type: Priority rehire. */
    public const TYPE_RECONTRATACION = 'recontratacion';

    /** @var array List of all exemption types. */
    public const TYPES = [
        self::TYPE_HISTORICO_ISER,
        self::TYPE_DOCUMENTOS_RECIENTES,
        self::TYPE_TRASLADO_INTERNO,
        self::TYPE_RECONTRATACION,
    ];

    /** @var int Exemption record ID. */
    public int $id;

    /** @var int User ID. */
    public int $userid;

    /** @var string Exemption type. */
    public string $exemptiontype;

    /** @var string|null Document reference. */
    public ?string $documentref;

    /** @var int Valid from timestamp. */
    public int $validfrom;

    /** @var int|null Valid until timestamp (null = permanent). */
    public ?int $validuntil;

    /** @var string|null Notes. */
    public ?string $notes;

    /** @var int Created by user ID. */
    public int $createdby;

    /** @var int Time created. */
    public int $timecreated;

    /** @var int|null Time revoked. */
    public ?int $timerevoked;

    /** @var int|null Revoked by user ID. */
    public ?int $revokedby;

    /** @var string|null Revocation reason. */
    public ?string $revokereason;

    /**
     * Constructor.
     *
     * @param object $record Database record.
     */
    public function __construct(object $record) {
        $this->id = (int) $record->id;
        $this->userid = (int) $record->userid;
        $this->exemptiontype = $record->exemptiontype;
        $this->documentref = $record->documentref ?? null;
        $this->validfrom = (int) $record->validfrom;
        $this->validuntil = isset($record->validuntil) ? (int) $record->validuntil : null;
        $this->notes = $record->notes ?? null;
        $this->createdby = (int) $record->createdby;
        $this->timecreated = (int) $record->timecreated;
        $this->timerevoked = isset($record->timerevoked) ? (int) $record->timerevoked : null;
        $this->revokedby = isset($record->revokedby) ? (int) $record->revokedby : null;
        $this->revokereason = $record->revokereason ?? null;
    }

    /**
     * Get exemption by ID.
     *
     * @param int $id Exemption ID.
     * @return self|null Exemption object or null.
     */
    public static function get(int $id): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_exemption', ['id' => $id]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Get active exemption for a user.
     *
     * @param int $userid User ID.
     * @return self|null Active exemption or null.
     */
    public static function get_active_for_user(int $userid): ?self {
        global $DB;

        $now = time();

        $sql = "SELECT *
                  FROM {local_jobboard_exemption}
                 WHERE userid = :userid
                   AND validfrom <= :now1
                   AND (validuntil IS NULL OR validuntil >= :now2)
                   AND timerevoked IS NULL
              ORDER BY timecreated DESC";

        $record = $DB->get_record_sql($sql, [
            'userid' => $userid,
            'now1' => $now,
            'now2' => $now,
        ]);

        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Get all exemptions for a user.
     *
     * @param int $userid User ID.
     * @param bool $activeonly Only return active exemptions.
     * @return array Array of exemption objects.
     */
    public static function get_all_for_user(int $userid, bool $activeonly = false): array {
        global $DB;

        $params = ['userid' => $userid];
        $where = 'userid = :userid';

        if ($activeonly) {
            $now = time();
            $where .= ' AND validfrom <= :now1 AND (validuntil IS NULL OR validuntil >= :now2) AND timerevoked IS NULL';
            $params['now1'] = $now;
            $params['now2'] = $now;
        }

        $records = $DB->get_records_select('local_jobboard_exemption', $where, $params, 'timecreated DESC');

        return array_map(fn($r) => new self($r), $records);
    }

    /**
     * Check if a user has an active exemption.
     *
     * @param int $userid User ID.
     * @return bool True if user has active exemption.
     */
    public static function user_has_active_exemption(int $userid): bool {
        return self::get_active_for_user($userid) !== null;
    }

    /**
     * Create a new exemption.
     *
     * @param int $userid User ID.
     * @param string $type Exemption type.
     * @param array $data Additional data.
     * @return self Created exemption.
     * @throws \moodle_exception If invalid data.
     */
    public static function create(int $userid, string $type, array $data = []): self {
        global $DB, $USER;

        // Validate type.
        if (!in_array($type, self::TYPES)) {
            throw new \moodle_exception('invalidexemptiontype', 'local_jobboard');
        }

        // Check user exists.
        if (!$DB->record_exists('user', ['id' => $userid, 'deleted' => 0])) {
            throw new \moodle_exception('usernotfound', 'local_jobboard');
        }

        $record = new \stdClass();
        $record->userid = $userid;
        $record->exemptiontype = $type;
        $record->documentref = $data['documentref'] ?? null;
        $record->validfrom = $data['validfrom'] ?? time();
        $record->validuntil = $data['validuntil'] ?? null;
        $record->notes = $data['notes'] ?? null;
        $record->createdby = $USER->id;
        $record->timecreated = time();

        $id = $DB->insert_record('local_jobboard_exemption', $record);

        $exemption = self::get($id);

        // Log audit event.
        audit::log('exemption_created', 'local_jobboard_exemption', $id, [
            'userid' => $userid,
            'exemptiontype' => $type,
        ]);

        return $exemption;
    }

    /**
     * Revoke this exemption.
     *
     * @param string $reason Revocation reason.
     * @return bool Success.
     */
    public function revoke(string $reason): bool {
        global $DB, $USER;

        if ($this->timerevoked !== null) {
            return false; // Already revoked.
        }

        $this->timerevoked = time();
        $this->revokedby = $USER->id;
        $this->revokereason = $reason;

        $record = new \stdClass();
        $record->id = $this->id;
        $record->timerevoked = $this->timerevoked;
        $record->revokedby = $this->revokedby;
        $record->revokereason = $this->revokereason;

        $result = $DB->update_record('local_jobboard_exemption', $record);

        if ($result) {
            audit::log('exemption_revoked', 'local_jobboard_exemption', $this->id, [
                'userid' => $this->userid,
                'reason' => $reason,
            ]);
        }

        return $result;
    }

    /**
     * Check if this exemption is currently active.
     *
     * @return bool True if active.
     */
    public function is_active(): bool {
        $now = time();

        return $this->validfrom <= $now
            && ($this->validuntil === null || $this->validuntil >= $now)
            && $this->timerevoked === null;
    }

    /**
     * Get the reduced document requirements for this exemption type.
     *
     * @return array Array of document type codes that are still required.
     */
    public function get_required_document_codes(): array {
        switch ($this->exemptiontype) {
            case self::TYPE_HISTORICO_ISER:
                // Historic ISER: Only need updated background checks and medical.
                return [
                    'antecedentes_procuraduria',
                    'antecedentes_contraloria',
                    'antecedentes_policia',
                    'rnmc',
                    'certificado_medico',
                ];

            case self::TYPE_DOCUMENTOS_RECIENTES:
                // Recent documents: Only update expired ones (handled dynamically).
                return [];

            case self::TYPE_TRASLADO_INTERNO:
                // Internal transfer: Minimal documentation.
                return [
                    'antecedentes_procuraduria',
                    'antecedentes_contraloria',
                ];

            case self::TYPE_RECONTRATACION:
                // Rehire within same year: Only background checks.
                return [
                    'antecedentes_procuraduria',
                    'antecedentes_contraloria',
                    'antecedentes_policia',
                    'rnmc',
                ];

            default:
                return [];
        }
    }

    /**
     * Get document types required for an exemption or standard application.
     *
     * @param int|null $userid User ID to check for exemption.
     * @param bool $onlyrequired Only return required documents.
     * @return array Array of document type records.
     */
    public static function get_required_doctypes(?int $userid = null, bool $onlyrequired = true): array {
        global $DB;

        $exemption = null;
        if ($userid) {
            $exemption = self::get_active_for_user($userid);
        }

        // Get all document types.
        $conditions = [];
        if ($onlyrequired) {
            $conditions['isrequired'] = 1;
        }
        $doctypes = $DB->get_records('local_jobboard_doctype', $conditions, 'sortorder ASC');

        if (!$exemption) {
            return $doctypes;
        }

        // Filter based on exemption requirements.
        $requiredcodes = $exemption->get_required_document_codes();

        if (empty($requiredcodes)) {
            // For DOCUMENTOS_RECIENTES, return empty - handled by caller.
            return [];
        }

        return array_filter($doctypes, fn($dt) => in_array($dt->code, $requiredcodes));
    }

    /**
     * Get exemption type display name.
     *
     * @return string Localized type name.
     */
    public function get_type_name(): string {
        return get_string('exemptiontype_' . $this->exemptiontype, 'local_jobboard');
    }

    /**
     * Get all exemption types with names.
     *
     * @return array Type code => name.
     */
    public static function get_all_types(): array {
        $types = [];
        foreach (self::TYPES as $type) {
            $types[$type] = get_string('exemptiontype_' . $type, 'local_jobboard');
        }
        return $types;
    }

    /**
     * Get exemptions list for admin.
     *
     * @param array $filters Filter criteria.
     * @param string $sort Sort field.
     * @param string $order Sort order.
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array Array with 'exemptions' and 'total'.
     */
    public static function get_list(array $filters = [], string $sort = 'timecreated',
        string $order = 'DESC', int $page = 0, int $perpage = 20): array {
        global $DB;

        $params = [];
        $where = ['1=1'];

        if (!empty($filters['userid'])) {
            $where[] = 'e.userid = :userid';
            $params['userid'] = $filters['userid'];
        }

        if (!empty($filters['exemptiontype'])) {
            $where[] = 'e.exemptiontype = :exemptiontype';
            $params['exemptiontype'] = $filters['exemptiontype'];
        }

        if (!empty($filters['active'])) {
            $now = time();
            $where[] = 'e.validfrom <= :now1';
            $where[] = '(e.validuntil IS NULL OR e.validuntil >= :now2)';
            $where[] = 'e.timerevoked IS NULL';
            $params['now1'] = $now;
            $params['now2'] = $now;
        }

        if (!empty($filters['search'])) {
            $search = '%' . $DB->sql_like_escape($filters['search']) . '%';
            $where[] = '(' . $DB->sql_like('u.firstname', ':search1', false) .
                ' OR ' . $DB->sql_like('u.lastname', ':search2', false) .
                ' OR ' . $DB->sql_like('u.email', ':search3', false) .
                ' OR ' . $DB->sql_like('e.documentref', ':search4', false) . ')';
            $params['search1'] = $search;
            $params['search2'] = $search;
            $params['search3'] = $search;
            $params['search4'] = $search;
        }

        $whereclause = implode(' AND ', $where);

        // Validate sort field.
        $allowedsorts = ['timecreated', 'validfrom', 'validuntil', 'exemptiontype', 'userid'];
        if (!in_array($sort, $allowedsorts)) {
            $sort = 'timecreated';
        }
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT e.*, u.firstname, u.lastname, u.email
                  FROM {local_jobboard_exemption} e
                  JOIN {user} u ON u.id = e.userid
                 WHERE {$whereclause}
              ORDER BY e.{$sort} {$order}";

        $countsql = "SELECT COUNT(e.id)
                       FROM {local_jobboard_exemption} e
                       JOIN {user} u ON u.id = e.userid
                      WHERE {$whereclause}";

        $total = $DB->count_records_sql($countsql, $params);
        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        $exemptions = [];
        foreach ($records as $record) {
            $exemption = new self($record);
            $exemption->userfirstname = $record->firstname;
            $exemption->userlastname = $record->lastname;
            $exemption->useremail = $record->email;
            $exemptions[] = $exemption;
        }

        return ['exemptions' => $exemptions, 'total' => $total];
    }

    /**
     * Import exemptions from CSV.
     *
     * @param string $content CSV content.
     * @return array Result with 'imported', 'errors', 'skipped'.
     */
    public static function import_from_csv(string $content): array {
        global $DB;

        $result = [
            'imported' => 0,
            'errors' => [],
            'skipped' => 0,
        ];

        $lines = explode("\n", $content);
        $header = null;

        foreach ($lines as $linenum => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $fields = str_getcsv($line);

            if ($header === null) {
                // First line is header.
                $header = array_map('strtolower', array_map('trim', $fields));
                continue;
            }

            $row = array_combine($header, $fields);
            if (!$row) {
                $result['errors'][] = get_string('csvlineerror', 'local_jobboard', $linenum + 1);
                continue;
            }

            // Find user.
            $user = null;
            if (!empty($row['email'])) {
                $user = $DB->get_record('user', ['email' => $row['email'], 'deleted' => 0]);
            } else if (!empty($row['username'])) {
                $user = $DB->get_record('user', ['username' => $row['username'], 'deleted' => 0]);
            } else if (!empty($row['idnumber'])) {
                $user = $DB->get_record('user', ['idnumber' => $row['idnumber'], 'deleted' => 0]);
            }

            if (!$user) {
                $result['errors'][] = get_string('csvusernotfound', 'local_jobboard', $linenum + 1);
                continue;
            }

            // Check exemption type.
            $type = $row['exemptiontype'] ?? self::TYPE_HISTORICO_ISER;
            if (!in_array($type, self::TYPES)) {
                $result['errors'][] = get_string('csvinvalidtype', 'local_jobboard', $linenum + 1);
                continue;
            }

            // Check if already has active exemption.
            if (self::user_has_active_exemption($user->id)) {
                $result['skipped']++;
                continue;
            }

            try {
                self::create($user->id, $type, [
                    'documentref' => $row['documentref'] ?? null,
                    'notes' => $row['notes'] ?? null,
                    'validfrom' => !empty($row['validfrom']) ? strtotime($row['validfrom']) : time(),
                    'validuntil' => !empty($row['validuntil']) ? strtotime($row['validuntil']) : null,
                ]);
                $result['imported']++;
            } catch (\Exception $e) {
                $result['errors'][] = get_string('csvimporterror', 'local_jobboard', [
                    'line' => $linenum + 1,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }
}
