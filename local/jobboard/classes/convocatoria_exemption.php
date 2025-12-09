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
 * Convocatoria document exemptions management class.
 *
 * Handles global document exemptions at the convocatoria level.
 * When a document type is exempted for a convocatoria, ALL applicants
 * within that convocatoria are exempt from that document requirement.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing convocatoria-level document exemptions.
 */
class convocatoria_exemption {

    /** @var string Table name for convocatoria exemptions. */
    protected const TABLE = 'local_jobboard_conv_docexempt';

    /** @var int Exemption record ID. */
    public int $id;

    /** @var int Convocatoria ID. */
    public int $convocatoriaid;

    /** @var int Document type ID. */
    public int $doctypeid;

    /** @var string|null Exemption reason. */
    public ?string $exemptionreason;

    /** @var int Created by user ID. */
    public int $createdby;

    /** @var int Time created. */
    public int $timecreated;

    /** @var int|null Time modified. */
    public ?int $timemodified;

    /** @var object|null Document type object (lazy loaded). */
    protected ?object $doctype = null;

    /**
     * Constructor.
     *
     * @param object $record Database record.
     */
    public function __construct(object $record) {
        $this->id = (int) $record->id;
        $this->convocatoriaid = (int) $record->convocatoriaid;
        $this->doctypeid = (int) $record->doctypeid;
        $this->exemptionreason = $record->exemptionreason ?? null;
        $this->createdby = (int) $record->createdby;
        $this->timecreated = (int) $record->timecreated;
        $this->timemodified = isset($record->timemodified) ? (int) $record->timemodified : null;
    }

    /**
     * Get exemption by ID.
     *
     * @param int $id Exemption ID.
     * @return self|null Exemption object or null.
     */
    public static function get(int $id): ?self {
        global $DB;

        $record = $DB->get_record(self::TABLE, ['id' => $id]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Get all exemptions for a convocatoria.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @return array Array of exemption objects.
     */
    public static function get_for_convocatoria(int $convocatoriaid): array {
        global $DB;

        $records = $DB->get_records(self::TABLE, ['convocatoriaid' => $convocatoriaid], 'timecreated DESC');

        return array_map(fn($r) => new self($r), $records);
    }

    /**
     * Get exempted document type IDs for a convocatoria.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @return array Array of document type IDs that are exempted.
     */
    public static function get_exempted_doctype_ids(int $convocatoriaid): array {
        global $DB;

        return $DB->get_fieldset_select(self::TABLE, 'doctypeid', 'convocatoriaid = ?', [$convocatoriaid]);
    }

    /**
     * Check if a document type is exempted for a convocatoria.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @param int $doctypeid Document type ID.
     * @return bool True if exempted.
     */
    public static function is_exempted(int $convocatoriaid, int $doctypeid): bool {
        global $DB;

        return $DB->record_exists(self::TABLE, [
            'convocatoriaid' => $convocatoriaid,
            'doctypeid' => $doctypeid,
        ]);
    }

    /**
     * Check if a document type code is exempted for a convocatoria.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @param string $doctypecode Document type code.
     * @return bool True if exempted.
     */
    public static function is_code_exempted(int $convocatoriaid, string $doctypecode): bool {
        global $DB;

        $sql = "SELECT 1 FROM {" . self::TABLE . "} ce
                  JOIN {local_jobboard_doctype} dt ON dt.id = ce.doctypeid
                 WHERE ce.convocatoriaid = :convid AND dt.code = :code";

        return $DB->record_exists_sql($sql, [
            'convid' => $convocatoriaid,
            'code' => $doctypecode,
        ]);
    }

    /**
     * Create a new exemption.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @param int $doctypeid Document type ID.
     * @param string|null $reason Exemption reason.
     * @return self Created exemption.
     * @throws \moodle_exception If invalid data or already exists.
     */
    public static function create(int $convocatoriaid, int $doctypeid, ?string $reason = null): self {
        global $DB, $USER;

        // Validate convocatoria exists.
        if (!$DB->record_exists('local_jobboard_convocatoria', ['id' => $convocatoriaid])) {
            throw new \moodle_exception('error:convocatorianotfound', 'local_jobboard');
        }

        // Validate doctype exists.
        if (!$DB->record_exists('local_jobboard_doctype', ['id' => $doctypeid])) {
            throw new \moodle_exception('error:doctypenotfound', 'local_jobboard');
        }

        // Check if already exempted.
        if (self::is_exempted($convocatoriaid, $doctypeid)) {
            throw new \moodle_exception('error:alreadyexempted', 'local_jobboard');
        }

        $record = new \stdClass();
        $record->convocatoriaid = $convocatoriaid;
        $record->doctypeid = $doctypeid;
        $record->exemptionreason = $reason;
        $record->createdby = $USER->id;
        $record->timecreated = time();

        $id = $DB->insert_record(self::TABLE, $record);

        $exemption = self::get($id);

        // Log audit event.
        audit::log('convocatoria_exemption_created', 'local_jobboard_conv_docexempt', $id, [
            'convocatoriaid' => $convocatoriaid,
            'doctypeid' => $doctypeid,
        ]);

        return $exemption;
    }

    /**
     * Delete this exemption.
     *
     * @return bool Success.
     */
    public function delete(): bool {
        global $DB;

        $result = $DB->delete_records(self::TABLE, ['id' => $this->id]);

        if ($result) {
            audit::log('convocatoria_exemption_deleted', 'local_jobboard_conv_docexempt', $this->id, [
                'convocatoriaid' => $this->convocatoriaid,
                'doctypeid' => $this->doctypeid,
            ]);
        }

        return $result;
    }

    /**
     * Update the exemption reason.
     *
     * @param string|null $reason New reason.
     * @return bool Success.
     */
    public function update_reason(?string $reason): bool {
        global $DB;

        $this->exemptionreason = $reason;
        $this->timemodified = time();

        return $DB->update_record(self::TABLE, (object) [
            'id' => $this->id,
            'exemptionreason' => $reason,
            'timemodified' => $this->timemodified,
        ]);
    }

    /**
     * Get the document type object.
     *
     * @return object|null Document type record.
     */
    public function get_doctype(): ?object {
        global $DB;

        if ($this->doctype === null) {
            $this->doctype = $DB->get_record('local_jobboard_doctype', ['id' => $this->doctypeid]);
        }

        return $this->doctype ?: null;
    }

    /**
     * Set exemptions for a convocatoria (replaces all existing).
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @param array $doctypeids Array of document type IDs to exempt.
     * @param string|null $defaultreason Default reason for all exemptions.
     * @return void
     */
    public static function set_exemptions(int $convocatoriaid, array $doctypeids, ?string $defaultreason = null): void {
        global $DB, $USER;

        // Get current exemptions.
        $currentIds = self::get_exempted_doctype_ids($convocatoriaid);

        // Calculate additions and removals.
        $toAdd = array_diff($doctypeids, $currentIds);
        $toRemove = array_diff($currentIds, $doctypeids);

        // Remove exemptions no longer needed.
        if (!empty($toRemove)) {
            list($insql, $params) = $DB->get_in_or_equal($toRemove, SQL_PARAMS_NAMED);
            $params['convid'] = $convocatoriaid;
            $DB->delete_records_select(
                self::TABLE,
                "convocatoriaid = :convid AND doctypeid $insql",
                $params
            );
        }

        // Add new exemptions.
        foreach ($toAdd as $doctypeid) {
            $record = new \stdClass();
            $record->convocatoriaid = $convocatoriaid;
            $record->doctypeid = $doctypeid;
            $record->exemptionreason = $defaultreason;
            $record->createdby = $USER->id;
            $record->timecreated = time();

            $DB->insert_record(self::TABLE, $record);
        }

        // Log audit.
        audit::log('convocatoria_exemptions_updated', 'convocatoria', $convocatoriaid, [
            'added' => count($toAdd),
            'removed' => count($toRemove),
            'total' => count($doctypeids),
        ]);
    }

    /**
     * Get required document types for a convocatoria after applying exemptions.
     *
     * This filters out documents that are exempted at the convocatoria level.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @param bool $onlyrequired Only return documents marked as required.
     * @param bool $onlyenabled Only return enabled document types.
     * @return array Array of document type records.
     */
    public static function get_required_doctypes_for_convocatoria(
        int $convocatoriaid,
        bool $onlyrequired = true,
        bool $onlyenabled = true
    ): array {
        global $DB;

        // Get exempted doctype IDs.
        $exemptedIds = self::get_exempted_doctype_ids($convocatoriaid);

        // Build query conditions.
        $conditions = [];
        $params = [];

        if ($onlyrequired) {
            $conditions[] = 'isrequired = 1';
        }

        if ($onlyenabled) {
            $conditions[] = 'enabled = 1';
        }

        if (!empty($exemptedIds)) {
            list($insql, $inparams) = $DB->get_in_or_equal($exemptedIds, SQL_PARAMS_NAMED, 'ex', false);
            $conditions[] = "id $insql";
            $params = array_merge($params, $inparams);
        }

        $where = !empty($conditions) ? implode(' AND ', $conditions) : '1=1';

        return $DB->get_records_select('local_jobboard_doctype', $where, $params, 'sortorder ASC');
    }

    /**
     * Get all document types with exemption status for a convocatoria.
     *
     * Returns all enabled document types with an 'is_exempted' flag.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @return array Array of document type records with is_exempted flag.
     */
    public static function get_all_doctypes_with_exemption_status(int $convocatoriaid): array {
        global $DB;

        // Get exempted IDs.
        $exemptedIds = self::get_exempted_doctype_ids($convocatoriaid);

        // Get all enabled document types.
        $doctypes = $DB->get_records('local_jobboard_doctype', ['enabled' => 1], 'sortorder ASC');

        // Add exemption status.
        foreach ($doctypes as &$doctype) {
            $doctype->is_exempted = in_array($doctype->id, $exemptedIds);
        }

        return $doctypes;
    }

    /**
     * Copy exemptions from one convocatoria to another.
     *
     * @param int $sourceConvocatoriaid Source convocatoria ID.
     * @param int $targetConvocatoriaid Target convocatoria ID.
     * @return int Number of exemptions copied.
     */
    public static function copy_exemptions(int $sourceConvocatoriaid, int $targetConvocatoriaid): int {
        global $DB, $USER;

        // Get source exemptions.
        $sourceExemptions = self::get_for_convocatoria($sourceConvocatoriaid);

        $copied = 0;
        foreach ($sourceExemptions as $exemption) {
            // Skip if already exists in target.
            if (self::is_exempted($targetConvocatoriaid, $exemption->doctypeid)) {
                continue;
            }

            $record = new \stdClass();
            $record->convocatoriaid = $targetConvocatoriaid;
            $record->doctypeid = $exemption->doctypeid;
            $record->exemptionreason = $exemption->exemptionreason;
            $record->createdby = $USER->id;
            $record->timecreated = time();

            $DB->insert_record(self::TABLE, $record);
            $copied++;
        }

        if ($copied > 0) {
            audit::log('convocatoria_exemptions_copied', 'convocatoria', $targetConvocatoriaid, [
                'source' => $sourceConvocatoriaid,
                'copied' => $copied,
            ]);
        }

        return $copied;
    }

    /**
     * Get summary of exemptions for display.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @return array Summary with counts and document names.
     */
    public static function get_exemption_summary(int $convocatoriaid): array {
        global $DB;

        $sql = "SELECT dt.id, dt.code, dt.name, ce.exemptionreason
                  FROM {" . self::TABLE . "} ce
                  JOIN {local_jobboard_doctype} dt ON dt.id = ce.doctypeid
                 WHERE ce.convocatoriaid = :convid
              ORDER BY dt.sortorder ASC";

        $exemptions = $DB->get_records_sql($sql, ['convid' => $convocatoriaid]);

        return [
            'count' => count($exemptions),
            'documents' => $exemptions,
            'codes' => array_column($exemptions, 'code'),
        ];
    }
}
