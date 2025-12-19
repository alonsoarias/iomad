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
use local_jobboard\application;
use local_jobboard\review_notifier;

/**
 * External API for sending observations email.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_observations_email extends external_api {

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
     * Send observations email to applicant.
     *
     * @param int $applicationid The application ID.
     * @param string $observations JSON encoded observations.
     * @return array Result.
     */
    public static function execute(int $applicationid, string $observations): array {
        global $DB;

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
            // Get application.
            $application = new application($params['applicationid']);
            if (!$application->id) {
                throw new \moodle_exception('error:invalidapplication', 'local_jobboard');
            }

            // Decode observations.
            $obsdata = json_decode($params['observations'], true);
            $obstext = '';
            if (is_array($obsdata)) {
                foreach ($obsdata as $obs) {
                    if (!empty($obs['observation'])) {
                        $obstext .= $obs['observation'] . "\n";
                    }
                }
            }

            // Send notification.
            review_notifier::notify($params['applicationid'], $obstext);

            return [
                'success' => true,
                'message' => get_string('emailsent', 'local_jobboard'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
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
            'success' => new external_value(PARAM_BOOL, 'Whether the email was sent successfully'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
        ]);
    }
}
