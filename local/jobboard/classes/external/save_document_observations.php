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

/**
 * External API for saving document observations.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class save_document_observations extends external_api {

    /**
     * Define parameters for execute.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'applicationid' => new external_value(PARAM_INT, 'The application ID'),
            'observations' => new external_value(PARAM_RAW, 'JSON encoded observations array'),
        ]);
    }

    /**
     * Save document observations.
     *
     * @param int $applicationid The application ID.
     * @param string $observations JSON encoded observations.
     * @return array Result.
     */
    public static function execute(int $applicationid, string $observations): array {
        global $DB, $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
            'applicationid' => $applicationid,
            'observations' => $observations,
        ]);

        // Context validation.
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/jobboard:reviewdocuments', $context);

        try {
            $obsdata = json_decode($params['observations'], true);
            if (!is_array($obsdata)) {
                throw new \moodle_exception('error:invaliddata', 'local_jobboard');
            }

            $saved = 0;
            foreach ($obsdata as $obs) {
                if (empty($obs['documentid'])) {
                    continue;
                }

                $documentid = (int) $obs['documentid'];
                $text = $obs['observation'] ?? '';

                // Check if observation record exists.
                $existing = $DB->get_record('local_jobboard_doc_observation', [
                    'documentid' => $documentid,
                ]);

                if ($existing) {
                    $existing->observation = $text;
                    $existing->userid = $USER->id;
                    $existing->timemodified = time();
                    $DB->update_record('local_jobboard_doc_observation', $existing);
                } else {
                    $record = new \stdClass();
                    $record->documentid = $documentid;
                    $record->observation = $text;
                    $record->userid = $USER->id;
                    $record->timecreated = time();
                    $record->timemodified = time();
                    $DB->insert_record('local_jobboard_doc_observation', $record);
                }
                $saved++;
            }

            return [
                'success' => true,
                'saved' => $saved,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'saved' => 0,
            ];
        }
    }

    /**
     * Define return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the operation was successful'),
            'saved' => new external_value(PARAM_INT, 'Number of observations saved'),
        ]);
    }
}
