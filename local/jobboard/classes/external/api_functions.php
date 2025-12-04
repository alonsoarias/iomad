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
 * External API functions for Job Board plugin.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use context_system;

/**
 * External API functions class.
 */
class api_functions extends external_api {

    /**
     * Returns description of revoke_token parameters.
     */
    public static function revoke_token_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tokenid' => new external_value(PARAM_INT, 'Token ID'),
        ]);
    }

    /**
     * Revoke an API token.
     */
    public static function revoke_token(int $tokenid): array {
        $params = self::validate_parameters(self::revoke_token_parameters(), ['tokenid' => $tokenid]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/jobboard:manageapitokens', $context);

        $token = \local_jobboard\api_token::get($params['tokenid']);
        if (!$token) {
            throw new \moodle_exception('api:token:notfound', 'local_jobboard');
        }

        $token->revoke();

        return [
            'success' => true,
            'message' => get_string('api:token:revoked', 'local_jobboard'),
            'statuslabel' => get_string('api:token:status:disabled', 'local_jobboard'),
            'enablelabel' => get_string('api:token:enable', 'local_jobboard'),
        ];
    }

    /**
     * Returns description of revoke_token return value.
     */
    public static function revoke_token_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
            'statuslabel' => new external_value(PARAM_TEXT, 'New status label'),
            'enablelabel' => new external_value(PARAM_TEXT, 'Enable button label'),
        ]);
    }

    /**
     * Returns description of enable_token parameters.
     */
    public static function enable_token_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tokenid' => new external_value(PARAM_INT, 'Token ID'),
        ]);
    }

    /**
     * Enable an API token.
     */
    public static function enable_token(int $tokenid): array {
        $params = self::validate_parameters(self::enable_token_parameters(), ['tokenid' => $tokenid]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/jobboard:manageapitokens', $context);

        $token = \local_jobboard\api_token::get($params['tokenid']);
        if (!$token) {
            throw new \moodle_exception('api:token:notfound', 'local_jobboard');
        }

        $token->update((object)['enabled' => true]);

        return [
            'success' => true,
            'message' => get_string('enabled', 'local_jobboard'),
            'statuslabel' => get_string('api:token:status:active', 'local_jobboard'),
            'revokelabel' => get_string('api:token:revoke', 'local_jobboard'),
        ];
    }

    /**
     * Returns description of enable_token return value.
     */
    public static function enable_token_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
            'statuslabel' => new external_value(PARAM_TEXT, 'New status label'),
            'revokelabel' => new external_value(PARAM_TEXT, 'Revoke button label'),
        ]);
    }

    /**
     * Returns description of delete_token parameters.
     */
    public static function delete_token_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tokenid' => new external_value(PARAM_INT, 'Token ID'),
        ]);
    }

    /**
     * Delete an API token.
     */
    public static function delete_token(int $tokenid): array {
        $params = self::validate_parameters(self::delete_token_parameters(), ['tokenid' => $tokenid]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/jobboard:manageapitokens', $context);

        $token = \local_jobboard\api_token::get($params['tokenid']);
        if (!$token) {
            throw new \moodle_exception('api:token:notfound', 'local_jobboard');
        }

        $token->delete();

        return [
            'success' => true,
            'message' => get_string('api:token:deleted', 'local_jobboard'),
        ];
    }

    /**
     * Returns description of delete_token return value.
     */
    public static function delete_token_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
        ]);
    }

    /**
     * Returns description of filter_vacancies parameters.
     */
    public static function filter_vacancies_parameters(): external_function_parameters {
        return new external_function_parameters([
            'status' => new external_value(PARAM_ALPHA, 'Status filter', VALUE_DEFAULT, ''),
            'companyid' => new external_value(PARAM_INT, 'Company ID filter', VALUE_DEFAULT, 0),
            'search' => new external_value(PARAM_TEXT, 'Search term', VALUE_DEFAULT, ''),
            'page' => new external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 1),
            'perpage' => new external_value(PARAM_INT, 'Items per page', VALUE_DEFAULT, 12),
        ]);
    }

    /**
     * Filter vacancies.
     */
    public static function filter_vacancies(
        string $status = '',
        int $companyid = 0,
        string $search = '',
        int $page = 1,
        int $perpage = 12
    ): array {
        global $DB, $PAGE;

        $params = self::validate_parameters(self::filter_vacancies_parameters(), [
            'status' => $status, 'companyid' => $companyid, 'search' => $search,
            'page' => $page, 'perpage' => $perpage,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        $PAGE->set_context($context);

        $conditions = [];
        $sqlparams = [];

        if (!has_capability('local/jobboard:viewallvacancies', $context)) {
            $conditions[] = "status = :status";
            $sqlparams['status'] = 'published';
        } elseif (!empty($params['status'])) {
            $conditions[] = "status = :status";
            $sqlparams['status'] = $params['status'];
        }

        if (!empty($params['companyid'])) {
            $conditions[] = "companyid = :companyid";
            $sqlparams['companyid'] = $params['companyid'];
        }

        if (!empty($params['search'])) {
            $conditions[] = "(" . $DB->sql_like('title', ':search1', false) .
                           " OR " . $DB->sql_like('code', ':search2', false) . ")";
            $sqlparams['search1'] = '%' . $DB->sql_like_escape($params['search']) . '%';
            $sqlparams['search2'] = '%' . $DB->sql_like_escape($params['search']) . '%';
        }

        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $total = $DB->count_records_sql("SELECT COUNT(*) FROM {local_jobboard_vacancy} $where", $sqlparams);
        $offset = ($params['page'] - 1) * $params['perpage'];
        $records = $DB->get_records_sql(
            "SELECT * FROM {local_jobboard_vacancy} $where ORDER BY timecreated DESC",
            $sqlparams, $offset, $params['perpage']
        );

        $vacancies = [];
        foreach ($records as $record) {
            $daysremaining = max(0, floor(($record->closedate - time()) / 86400));
            $vacancies[] = [
                'id' => (int)$record->id,
                'code' => $record->code,
                'title' => $record->title,
                'description' => shorten_text(strip_tags($record->description), 150),
                'contracttype' => get_string('contract:' . $record->contracttype, 'local_jobboard'),
                'location' => $record->location ?? '',
                'department' => $record->department ?? '',
                'positions' => (int)$record->positions,
                'closedate' => userdate($record->closedate, get_string('strftimedate')),
                'daysremaining' => $daysremaining,
                'urgent' => $daysremaining <= 7,
                'status' => $record->status,
                'statusclass' => self::get_status_class($record->status),
                'viewurl' => (new \moodle_url('/local/jobboard/vacancy.php', ['id' => $record->id]))->out(false),
                'applyurl' => (new \moodle_url('/local/jobboard/apply.php', ['id' => $record->id]))->out(false),
                'canapply' => has_capability('local/jobboard:apply', $context) &&
                              $record->status === 'published' && $record->closedate > time(),
            ];
        }

        return [
            'vacancies' => $vacancies,
            'total' => $total,
            'page' => $params['page'],
            'perpage' => $params['perpage'],
            'pages' => (int)ceil($total / $params['perpage']),
        ];
    }

    /**
     * Returns description of filter_vacancies return value.
     */
    public static function filter_vacancies_returns(): external_single_structure {
        return new external_single_structure([
            'vacancies' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Vacancy ID'),
                    'code' => new external_value(PARAM_TEXT, 'Vacancy code'),
                    'title' => new external_value(PARAM_TEXT, 'Vacancy title'),
                    'description' => new external_value(PARAM_RAW, 'Short description'),
                    'contracttype' => new external_value(PARAM_TEXT, 'Contract type'),
                    'location' => new external_value(PARAM_TEXT, 'Location'),
                    'department' => new external_value(PARAM_TEXT, 'Department'),
                    'positions' => new external_value(PARAM_INT, 'Number of positions'),
                    'closedate' => new external_value(PARAM_TEXT, 'Close date'),
                    'daysremaining' => new external_value(PARAM_INT, 'Days remaining'),
                    'urgent' => new external_value(PARAM_BOOL, 'Is urgent'),
                    'status' => new external_value(PARAM_ALPHA, 'Status'),
                    'statusclass' => new external_value(PARAM_TEXT, 'Status CSS class'),
                    'viewurl' => new external_value(PARAM_URL, 'View URL'),
                    'applyurl' => new external_value(PARAM_URL, 'Apply URL'),
                    'canapply' => new external_value(PARAM_BOOL, 'Can apply'),
                ])
            ),
            'total' => new external_value(PARAM_INT, 'Total count'),
            'page' => new external_value(PARAM_INT, 'Current page'),
            'perpage' => new external_value(PARAM_INT, 'Items per page'),
            'pages' => new external_value(PARAM_INT, 'Total pages'),
        ]);
    }

    protected static function get_status_class(string $status): string {
        return ['draft' => 'secondary', 'published' => 'success', 'closed' => 'danger', 'assigned' => 'primary'][$status] ?? 'secondary';
    }
}
