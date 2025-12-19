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

namespace local_jobboard\external;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_jobboard\document;

/**
 * External API for rejecting a document.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reject_document extends external_api {

    /**
     * Define parameters for execute.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'documentid' => new external_value(PARAM_INT, 'The document ID to reject'),
            'applicationid' => new external_value(PARAM_INT, 'The application ID'),
            'reason' => new external_value(PARAM_TEXT, 'The rejection reason'),
        ]);
    }

    /**
     * Reject a document with reason.
     *
     * @param int $documentid The document ID.
     * @param int $applicationid The application ID.
     * @param string $reason The rejection reason.
     * @return array Result with success status and next document info.
     */
    public static function execute(int $documentid, int $applicationid, string $reason): array {
        global $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
            'documentid' => $documentid,
            'applicationid' => $applicationid,
            'reason' => $reason,
        ]);

        // Context validation.
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/jobboard:reviewdocuments', $context);

        // Reason is required for rejection.
        $reason = trim($params['reason']);
        if (empty($reason)) {
            return [
                'success' => false,
                'message' => get_string('observation_required_for_rejection', 'local_jobboard'),
                'documentid' => $params['documentid'],
                'newstatus' => '',
                'nextdocumentid' => 0,
                'stats' => [
                    'approved' => 0,
                    'rejected' => 0,
                    'pending' => 0,
                    'total' => 0,
                ],
                'allreviewed' => false,
            ];
        }

        try {
            // Get and validate the document.
            $doc = new document($params['documentid']);
            if (!$doc->id || $doc->applicationid != $params['applicationid']) {
                throw new \moodle_exception('error:invaliddocument', 'local_jobboard');
            }

            // Reject the document.
            $doc->reject($USER->id, $reason);

            // Get stats for progress update.
            $stats = document::get_stats($params['applicationid']);

            // Find next pending document.
            $nextdoc = self::get_next_pending_document($params['applicationid']);

            return [
                'success' => true,
                'message' => get_string('documentrejected', 'local_jobboard'),
                'documentid' => $params['documentid'],
                'newstatus' => 'rejected',
                'nextdocumentid' => $nextdoc ? $nextdoc->id : 0,
                'stats' => [
                    'approved' => $stats['approved'],
                    'rejected' => $stats['rejected'],
                    'pending' => $stats['pending'],
                    'total' => $stats['total'],
                ],
                'allreviewed' => ($stats['pending'] == 0),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'documentid' => $params['documentid'],
                'newstatus' => '',
                'nextdocumentid' => 0,
                'stats' => [
                    'approved' => 0,
                    'rejected' => 0,
                    'pending' => 0,
                    'total' => 0,
                ],
                'allreviewed' => false,
            ];
        }
    }

    /**
     * Get the next pending document for an application.
     *
     * @param int $applicationid The application ID.
     * @return document|null The next pending document or null.
     */
    private static function get_next_pending_document(int $applicationid): ?document {
        $documents = document::get_by_application($applicationid);
        foreach ($documents as $doc) {
            if ($doc->status === 'pending') {
                return $doc;
            }
        }
        return null;
    }

    /**
     * Define return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the operation was successful'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
            'documentid' => new external_value(PARAM_INT, 'The document ID that was rejected'),
            'newstatus' => new external_value(PARAM_TEXT, 'The new status of the document'),
            'nextdocumentid' => new external_value(PARAM_INT, 'The next pending document ID, or 0 if none'),
            'stats' => new external_single_structure([
                'approved' => new external_value(PARAM_INT, 'Number of approved documents'),
                'rejected' => new external_value(PARAM_INT, 'Number of rejected documents'),
                'pending' => new external_value(PARAM_INT, 'Number of pending documents'),
                'total' => new external_value(PARAM_INT, 'Total number of documents'),
            ]),
            'allreviewed' => new external_value(PARAM_BOOL, 'Whether all documents have been reviewed'),
        ]);
    }
}
