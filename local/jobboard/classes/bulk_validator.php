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
 * Bulk document validation class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for bulk document validation operations.
 */
class bulk_validator {

    /**
     * Bulk validate documents.
     *
     * @param array $documentids Document IDs to validate.
     * @param bool $isvalid Validation result.
     * @param string|null $rejectreason Rejection reason if not valid.
     * @param string|null $notes Additional notes.
     * @return array Results with 'success', 'failed', 'errors'.
     */
    public static function validate_documents(array $documentids, bool $isvalid,
        ?string $rejectreason = null, ?string $notes = null): array {

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($documentids as $docid) {
            try {
                $document = document::get($docid);
                if (!$document) {
                    $results['failed']++;
                    $results['errors'][] = get_string('documentnotfound', 'local_jobboard') . ": {$docid}";
                    continue;
                }

                // Check if already validated.
                if ($document->get_validation()) {
                    $results['failed']++;
                    $results['errors'][] = get_string('alreadyvalidated', 'local_jobboard') . ": {$docid}";
                    continue;
                }

                $document->validate($isvalid, $rejectreason, $notes);
                $results['success']++;

            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "{$docid}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Bulk validate all documents for an application.
     *
     * @param int $applicationid Application ID.
     * @param bool $isvalid Validation result.
     * @param string|null $notes Notes.
     * @return array Results.
     */
    public static function validate_application_documents(int $applicationid, bool $isvalid,
        ?string $notes = null): array {

        $documents = document::get_for_application($applicationid, false);
        $documentids = array_map(fn($d) => $d->id, $documents);

        return self::validate_documents($documentids, $isvalid, null, $notes);
    }

    /**
     * Get documents pending validation grouped by type.
     *
     * @param int|null $vacancyid Filter by vacancy.
     * @param int|null $reviewerid Filter by assigned reviewer.
     * @return array Documents grouped by type.
     */
    public static function get_pending_by_type(?int $vacancyid = null, ?int $reviewerid = null): array {
        global $DB;

        $params = [];
        $where = ['d.issuperseded = 0'];

        // Filter out validated documents.
        $where[] = 'NOT EXISTS (SELECT 1 FROM {local_jobboard_doc_validation} v WHERE v.documentid = d.id)';

        if ($vacancyid) {
            $where[] = 'a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        if ($reviewerid) {
            $where[] = 'a.reviewerid = :reviewerid';
            $params['reviewerid'] = $reviewerid;
        }

        $whereclause = implode(' AND ', $where);

        $sql = "SELECT d.documenttype, COUNT(*) as count
                  FROM {local_jobboard_document} d
                  JOIN {local_jobboard_application} a ON a.id = d.applicationid
                 WHERE {$whereclause}
              GROUP BY d.documenttype
              ORDER BY count DESC";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get pending documents of a specific type.
     *
     * @param string $documenttype Document type code.
     * @param int|null $vacancyid Filter by vacancy.
     * @param int|null $reviewerid Filter by assigned reviewer.
     * @param int $limit Maximum documents to return.
     * @return array Document records with application info.
     */
    public static function get_pending_documents_by_type(string $documenttype, ?int $vacancyid = null,
        ?int $reviewerid = null, int $limit = 50): array {
        global $DB;

        $params = ['doctype' => $documenttype];
        $where = ['d.documenttype = :doctype', 'd.issuperseded = 0'];

        // Filter out validated documents.
        $where[] = 'NOT EXISTS (SELECT 1 FROM {local_jobboard_doc_validation} v WHERE v.documentid = d.id)';

        if ($vacancyid) {
            $where[] = 'a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        if ($reviewerid) {
            $where[] = 'a.reviewerid = :reviewerid';
            $params['reviewerid'] = $reviewerid;
        }

        $whereclause = implode(' AND ', $where);

        $sql = "SELECT d.*, a.vacancyid, a.userid as applicantuserid,
                       u.firstname, u.lastname, u.email,
                       v.code as vacancy_code, v.title as vacancy_title
                  FROM {local_jobboard_document} d
                  JOIN {local_jobboard_application} a ON a.id = d.applicationid
                  JOIN {user} u ON u.id = a.userid
                  JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                 WHERE {$whereclause}
              ORDER BY d.timecreated ASC";

        return $DB->get_records_sql($sql, $params, 0, $limit);
    }

    /**
     * Auto-validate documents based on rules.
     *
     * @param string $documenttype Document type to process.
     * @param array $rules Validation rules.
     * @return array Results.
     */
    public static function auto_validate(string $documenttype, array $rules = []): array {
        global $DB;

        $results = [
            'processed' => 0,
            'validated' => 0,
            'flagged' => 0,
        ];

        // Get documents to process.
        $documents = self::get_pending_documents_by_type($documenttype, null, null, 100);

        foreach ($documents as $doc) {
            $results['processed']++;

            $issues = [];

            // Check issue date if applicable.
            if (!empty($doc->issuedate) && isset($rules['max_age_days'])) {
                $maxage = $rules['max_age_days'] * 24 * 60 * 60;
                if ($doc->issuedate < (time() - $maxage)) {
                    $issues[] = 'expired';
                }
            }

            // Check file size.
            if (isset($rules['min_size']) && $doc->filesize < $rules['min_size']) {
                $issues[] = 'file_too_small';
            }

            // Check mime type.
            if (isset($rules['allowed_mimes']) && !in_array($doc->mimetype, $rules['allowed_mimes'])) {
                $issues[] = 'invalid_format';
            }

            // If no issues, auto-validate.
            if (empty($issues)) {
                $document = document::get($doc->id);
                $document->validate(true, null, get_string('autovalidated', 'local_jobboard'));
                $results['validated']++;
            } else {
                // Flag for manual review.
                $DB->set_field('local_jobboard_document', 'applicationdata',
                    json_encode(['auto_issues' => $issues]), ['id' => $doc->id]);
                $results['flagged']++;
            }
        }

        return $results;
    }

    /**
     * Get validation statistics.
     *
     * @param int|null $vacancyid Filter by vacancy.
     * @param int $since Time period start.
     * @return array Statistics.
     */
    public static function get_validation_stats(?int $vacancyid = null, int $since = 0): array {
        global $DB;

        if ($since == 0) {
            $since = strtotime('-30 days');
        }

        $params = ['since' => $since];
        $vacancyjoin = '';
        $vacancywhere = '';

        if ($vacancyid) {
            $vacancyjoin = 'JOIN {local_jobboard_application} a ON a.id = d.applicationid';
            $vacancywhere = ' AND a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        // Total documents.
        $sql = "SELECT COUNT(*) FROM {local_jobboard_document} d {$vacancyjoin}
                 WHERE d.timecreated > :since {$vacancywhere}";
        $totaldocs = $DB->count_records_sql($sql, $params);

        // Validated.
        $sql = "SELECT COUNT(*) FROM {local_jobboard_document} d
                  JOIN {local_jobboard_doc_validation} v ON v.documentid = d.id
                  {$vacancyjoin}
                 WHERE d.timecreated > :since AND v.isvalid = 1 {$vacancywhere}";
        $validated = $DB->count_records_sql($sql, $params);

        // Rejected.
        $sql = "SELECT COUNT(*) FROM {local_jobboard_document} d
                  JOIN {local_jobboard_doc_validation} v ON v.documentid = d.id
                  {$vacancyjoin}
                 WHERE d.timecreated > :since AND v.isvalid = 0 {$vacancywhere}";
        $rejected = $DB->count_records_sql($sql, $params);

        // Pending.
        $sql = "SELECT COUNT(*) FROM {local_jobboard_document} d
                  {$vacancyjoin}
                 WHERE d.timecreated > :since
                   AND d.issuperseded = 0
                   AND NOT EXISTS (SELECT 1 FROM {local_jobboard_doc_validation} v WHERE v.documentid = d.id)
                   {$vacancywhere}";
        $pending = $DB->count_records_sql($sql, $params);

        // By document type.
        $sql = "SELECT d.documenttype,
                       COUNT(*) as total,
                       SUM(CASE WHEN v.isvalid = 1 THEN 1 ELSE 0 END) as validated,
                       SUM(CASE WHEN v.isvalid = 0 THEN 1 ELSE 0 END) as rejected,
                       SUM(CASE WHEN v.id IS NULL AND d.issuperseded = 0 THEN 1 ELSE 0 END) as pending
                  FROM {local_jobboard_document} d
                  LEFT JOIN {local_jobboard_doc_validation} v ON v.documentid = d.id
                  {$vacancyjoin}
                 WHERE d.timecreated > :since {$vacancywhere}
              GROUP BY d.documenttype
              ORDER BY total DESC";
        $bytype = $DB->get_records_sql($sql, $params);

        // Average validation time.
        $sql = "SELECT AVG(v.timecreated - d.timecreated) as avgtime
                  FROM {local_jobboard_document} d
                  JOIN {local_jobboard_doc_validation} v ON v.documentid = d.id
                  {$vacancyjoin}
                 WHERE d.timecreated > :since {$vacancywhere}";
        $avgresult = $DB->get_record_sql($sql, $params);
        $avgtime = $avgresult ? round($avgresult->avgtime / 3600, 1) : 0;

        return [
            'total' => $totaldocs,
            'validated' => $validated,
            'rejected' => $rejected,
            'pending' => $pending,
            'validation_rate' => $totaldocs > 0 ? round(($validated / $totaldocs) * 100, 1) : 0,
            'rejection_rate' => $totaldocs > 0 ? round(($rejected / $totaldocs) * 100, 1) : 0,
            'avg_validation_time_hours' => $avgtime,
            'by_type' => $bytype,
        ];
    }

    /**
     * Get rejection reasons statistics.
     *
     * @param int|null $vacancyid Filter by vacancy.
     * @param int $since Time period start.
     * @return array Rejection reasons with counts.
     */
    public static function get_rejection_reasons_stats(?int $vacancyid = null, int $since = 0): array {
        global $DB;

        if ($since == 0) {
            $since = strtotime('-30 days');
        }

        $params = ['since' => $since];
        $vacancyjoin = '';
        $vacancywhere = '';

        if ($vacancyid) {
            $vacancyjoin = 'JOIN {local_jobboard_application} a ON a.id = d.applicationid';
            $vacancywhere = ' AND a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        $sql = "SELECT v.rejectreason, COUNT(*) as count
                  FROM {local_jobboard_doc_validation} v
                  JOIN {local_jobboard_document} d ON d.id = v.documentid
                  {$vacancyjoin}
                 WHERE v.isvalid = 0
                   AND v.timecreated > :since
                   {$vacancywhere}
              GROUP BY v.rejectreason
              ORDER BY count DESC";

        return $DB->get_records_sql($sql, $params);
    }
}
