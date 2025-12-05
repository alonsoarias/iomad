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
 * External API functions for local_jobboard.
 *
 * This file contains all external web service functions following Moodle 4.1+ standards.
 * Each function follows the three-method pattern:
 * - functionname_parameters(): Define input parameters
 * - functionname(): Execute the function
 * - functionname_returns(): Define return structure
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External API class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api extends \external_api {

    // =========================================================================
    // VACANCY FUNCTIONS
    // =========================================================================

    /**
     * Returns description of get_vacancies parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_vacancies_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'status' => new \external_value(PARAM_ALPHA, 'Filter by status (draft, published, closed, assigned)', VALUE_DEFAULT, ''),
            'publicationtype' => new \external_value(PARAM_ALPHA, 'Filter by publication type (public, internal)', VALUE_DEFAULT, ''),
            'companyid' => new \external_value(PARAM_INT, 'Filter by company ID (Iomad)', VALUE_DEFAULT, 0),
            'search' => new \external_value(PARAM_TEXT, 'Search term for title, code, description', VALUE_DEFAULT, ''),
            'page' => new \external_value(PARAM_INT, 'Page number (0-based)', VALUE_DEFAULT, 0),
            'perpage' => new \external_value(PARAM_INT, 'Number of items per page', VALUE_DEFAULT, 20),
        ]);
    }

    /**
     * Get list of vacancies.
     *
     * @param string $status Status filter.
     * @param string $publicationtype Publication type filter.
     * @param int $companyid Company ID filter.
     * @param string $search Search term.
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array List of vacancies with pagination info.
     */
    public static function get_vacancies(
        string $status = '',
        string $publicationtype = '',
        int $companyid = 0,
        string $search = '',
        int $page = 0,
        int $perpage = 20
    ): array {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::get_vacancies_parameters(), [
            'status' => $status,
            'publicationtype' => $publicationtype,
            'companyid' => $companyid,
            'search' => $search,
            'page' => $page,
            'perpage' => $perpage,
        ]);

        // Get and validate context.
        $context = \context_system::instance();
        self::validate_context($context);

        // Build query conditions.
        $conditions = [];
        $sqlparams = [];

        // Check user capabilities for visibility.
        $canviewall = has_capability('local/jobboard:viewallvacancies', $context);
        $canviewinternal = has_capability('local/jobboard:viewinternalvacancies', $context);
        $canviewpublic = has_capability('local/jobboard:viewpublicvacancies', $context);

        if (!$canviewall) {
            // Non-admin users can only see published vacancies.
            $conditions[] = "status = :pubstatus";
            $sqlparams['pubstatus'] = 'published';

            // Filter by publication type based on capabilities.
            if (!$canviewinternal && $canviewpublic) {
                $conditions[] = "publicationtype = :pubtype";
                $sqlparams['pubtype'] = 'public';
            } elseif ($canviewinternal && !empty($params['publicationtype'])) {
                $conditions[] = "publicationtype = :pubtype";
                $sqlparams['pubtype'] = $params['publicationtype'];
            }

            // Only show open vacancies (not expired).
            $conditions[] = "closedate >= :now";
            $sqlparams['now'] = time();
        } else {
            if (!empty($params['status'])) {
                $conditions[] = "status = :status";
                $sqlparams['status'] = $params['status'];
            }

            if (!empty($params['publicationtype'])) {
                $conditions[] = "publicationtype = :pubtype";
                $sqlparams['pubtype'] = $params['publicationtype'];
            }
        }

        // Filter by company (multi-tenant).
        if (!empty($params['companyid'])) {
            $conditions[] = "companyid = :companyid";
            $sqlparams['companyid'] = $params['companyid'];
        } elseif (!$canviewall && function_exists('local_jobboard_get_user_companyid')) {
            $usercompanyid = local_jobboard_get_user_companyid($USER->id);
            if ($usercompanyid) {
                $conditions[] = "(companyid IS NULL OR companyid = :usercompanyid)";
                $sqlparams['usercompanyid'] = $usercompanyid;
            }
        }

        // Search filter.
        if (!empty($params['search'])) {
            $searchterm = '%' . $DB->sql_like_escape($params['search']) . '%';
            $conditions[] = "(" . $DB->sql_like('title', ':search1', false) .
                           " OR " . $DB->sql_like('code', ':search2', false) .
                           " OR " . $DB->sql_like('description', ':search3', false) . ")";
            $sqlparams['search1'] = $searchterm;
            $sqlparams['search2'] = $searchterm;
            $sqlparams['search3'] = $searchterm;
        }

        // Build WHERE clause.
        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get total count.
        $total = $DB->count_records_sql("SELECT COUNT(*) FROM {local_jobboard_vacancy} $where", $sqlparams);

        // Get records with pagination.
        $sql = "SELECT * FROM {local_jobboard_vacancy} $where ORDER BY timecreated DESC";
        $records = $DB->get_records_sql($sql, $sqlparams, $params['page'] * $params['perpage'], $params['perpage']);

        // Format response.
        $vacancies = [];
        foreach ($records as $record) {
            $vacancies[] = self::format_vacancy_for_response($record);
        }

        return [
            'vacancies' => $vacancies,
            'total' => $total,
            'page' => $params['page'],
            'perpage' => $params['perpage'],
            'pages' => (int) ceil($total / max($params['perpage'], 1)),
            'warnings' => [],
        ];
    }

    /**
     * Returns description of get_vacancies return value.
     *
     * @return \external_single_structure
     */
    public static function get_vacancies_returns(): \external_single_structure {
        return new \external_single_structure([
            'vacancies' => new \external_multiple_structure(
                self::vacancy_structure()
            ),
            'total' => new \external_value(PARAM_INT, 'Total number of vacancies'),
            'page' => new \external_value(PARAM_INT, 'Current page number'),
            'perpage' => new \external_value(PARAM_INT, 'Items per page'),
            'pages' => new \external_value(PARAM_INT, 'Total number of pages'),
            'warnings' => new \external_warnings(),
        ]);
    }

    /**
     * Returns description of get_vacancy parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_vacancy_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'id' => new \external_value(PARAM_INT, 'Vacancy ID'),
        ]);
    }

    /**
     * Get a single vacancy by ID.
     *
     * @param int $id Vacancy ID.
     * @return array Vacancy details.
     */
    public static function get_vacancy(int $id): array {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::get_vacancy_parameters(), ['id' => $id]);

        // Get and validate context.
        $context = \context_system::instance();
        self::validate_context($context);

        // Get vacancy.
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $params['id']]);

        if (!$vacancy) {
            throw new \moodle_exception('error:vacancynotfound', 'local_jobboard');
        }

        // Check permissions.
        $canviewall = has_capability('local/jobboard:viewallvacancies', $context);
        $canviewinternal = has_capability('local/jobboard:viewinternalvacancies', $context);
        $canviewpublic = has_capability('local/jobboard:viewpublicvacancies', $context);

        if (!$canviewall) {
            if ($vacancy->status !== 'published') {
                throw new \moodle_exception('error:vacancynotfound', 'local_jobboard');
            }

            if ($vacancy->publicationtype === 'internal' && !$canviewinternal) {
                throw new \moodle_exception('error:nopermission', 'local_jobboard');
            }

            if ($vacancy->publicationtype === 'public' && !$canviewpublic) {
                throw new \moodle_exception('error:nopermission', 'local_jobboard');
            }
        }

        // Get document requirements.
        $requirements = $DB->get_records('local_jobboard_doc_requirement', ['vacancyid' => $vacancy->id], 'sortorder ASC');

        $docreqs = [];
        foreach ($requirements as $req) {
            $docreqs[] = [
                'id' => (int) $req->id,
                'documenttype' => $req->documenttype,
                'required' => (bool) $req->required,
                'acceptedformats' => $req->acceptedformats ?? 'pdf,jpg,png',
                'maxsize' => (int) ($req->maxsize ?? 10485760),
                'maxagedays' => (int) ($req->maxagedays ?? 0),
                'instructions' => $req->instructions ?? '',
            ];
        }

        $result = self::format_vacancy_for_response($vacancy);
        $result['documentrequirements'] = $docreqs;
        $result['warnings'] = [];

        return $result;
    }

    /**
     * Returns description of get_vacancy return value.
     *
     * @return \external_single_structure
     */
    public static function get_vacancy_returns(): \external_single_structure {
        $structure = self::vacancy_structure();
        $structure['documentrequirements'] = new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_INT, 'Requirement ID'),
                'documenttype' => new \external_value(PARAM_TEXT, 'Document type code'),
                'required' => new \external_value(PARAM_BOOL, 'Is required'),
                'acceptedformats' => new \external_value(PARAM_TEXT, 'Accepted file formats'),
                'maxsize' => new \external_value(PARAM_INT, 'Maximum file size in bytes'),
                'maxagedays' => new \external_value(PARAM_INT, 'Maximum document age in days'),
                'instructions' => new \external_value(PARAM_RAW, 'Instructions for applicant'),
            ])
        );
        $structure['warnings'] = new \external_warnings();

        return new \external_single_structure($structure);
    }

    /**
     * Returns description of filter_vacancies parameters.
     *
     * @return \external_function_parameters
     */
    public static function filter_vacancies_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'status' => new \external_value(PARAM_ALPHA, 'Status filter', VALUE_DEFAULT, ''),
            'companyid' => new \external_value(PARAM_INT, 'Company ID filter', VALUE_DEFAULT, 0),
            'search' => new \external_value(PARAM_TEXT, 'Search term', VALUE_DEFAULT, ''),
            'page' => new \external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 1),
            'perpage' => new \external_value(PARAM_INT, 'Items per page', VALUE_DEFAULT, 12),
        ]);
    }

    /**
     * Filter vacancies (AJAX function).
     *
     * @param string $status Status filter.
     * @param int $companyid Company ID filter.
     * @param string $search Search term.
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array Filtered vacancies.
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
            'status' => $status,
            'companyid' => $companyid,
            'search' => $search,
            'page' => $page,
            'perpage' => $perpage,
        ]);

        $context = \context_system::instance();
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
            $sqlparams,
            $offset,
            $params['perpage']
        );

        $vacancies = [];
        foreach ($records as $record) {
            $daysremaining = max(0, (int) floor(($record->closedate - time()) / 86400));
            $vacancies[] = [
                'id' => (int) $record->id,
                'code' => $record->code,
                'title' => $record->title,
                'description' => shorten_text(strip_tags($record->description), 150),
                'contracttype' => get_string('contract:' . $record->contracttype, 'local_jobboard'),
                'location' => $record->location ?? '',
                'department' => $record->department ?? '',
                'positions' => (int) $record->positions,
                'closedate' => userdate($record->closedate, get_string('strftimedate')),
                'daysremaining' => $daysremaining,
                'urgent' => $daysremaining <= 7,
                'status' => $record->status,
                'statusclass' => self::get_status_class($record->status),
                'viewurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $record->id]))->out(false),
                'applyurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'id' => $record->id]))->out(false),
                'canapply' => has_capability('local/jobboard:apply', $context) &&
                              $record->status === 'published' && $record->closedate > time(),
            ];
        }

        return [
            'vacancies' => $vacancies,
            'total' => $total,
            'page' => $params['page'],
            'perpage' => $params['perpage'],
            'pages' => (int) ceil($total / max($params['perpage'], 1)),
        ];
    }

    /**
     * Returns description of filter_vacancies return value.
     *
     * @return \external_single_structure
     */
    public static function filter_vacancies_returns(): \external_single_structure {
        return new \external_single_structure([
            'vacancies' => new \external_multiple_structure(
                new \external_single_structure([
                    'id' => new \external_value(PARAM_INT, 'Vacancy ID'),
                    'code' => new \external_value(PARAM_TEXT, 'Vacancy code'),
                    'title' => new \external_value(PARAM_TEXT, 'Vacancy title'),
                    'description' => new \external_value(PARAM_RAW, 'Short description'),
                    'contracttype' => new \external_value(PARAM_TEXT, 'Contract type'),
                    'location' => new \external_value(PARAM_TEXT, 'Location'),
                    'department' => new \external_value(PARAM_TEXT, 'Department'),
                    'positions' => new \external_value(PARAM_INT, 'Number of positions'),
                    'closedate' => new \external_value(PARAM_TEXT, 'Close date'),
                    'daysremaining' => new \external_value(PARAM_INT, 'Days remaining'),
                    'urgent' => new \external_value(PARAM_BOOL, 'Is urgent'),
                    'status' => new \external_value(PARAM_ALPHA, 'Status'),
                    'statusclass' => new \external_value(PARAM_TEXT, 'Status CSS class'),
                    'viewurl' => new \external_value(PARAM_URL, 'View URL'),
                    'applyurl' => new \external_value(PARAM_URL, 'Apply URL'),
                    'canapply' => new \external_value(PARAM_BOOL, 'Can apply'),
                ])
            ),
            'total' => new \external_value(PARAM_INT, 'Total count'),
            'page' => new \external_value(PARAM_INT, 'Current page'),
            'perpage' => new \external_value(PARAM_INT, 'Items per page'),
            'pages' => new \external_value(PARAM_INT, 'Total pages'),
        ]);
    }

    // =========================================================================
    // APPLICATION FUNCTIONS
    // =========================================================================

    /**
     * Returns description of get_applications parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_applications_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'vacancyid' => new \external_value(PARAM_INT, 'Filter by vacancy ID', VALUE_DEFAULT, 0),
            'status' => new \external_value(PARAM_ALPHA, 'Filter by status', VALUE_DEFAULT, ''),
            'page' => new \external_value(PARAM_INT, 'Page number (0-based)', VALUE_DEFAULT, 0),
            'perpage' => new \external_value(PARAM_INT, 'Number of items per page', VALUE_DEFAULT, 20),
        ]);
    }

    /**
     * Get list of applications.
     *
     * @param int $vacancyid Vacancy ID filter.
     * @param string $status Status filter.
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array List of applications.
     */
    public static function get_applications(
        int $vacancyid = 0,
        string $status = '',
        int $page = 0,
        int $perpage = 20
    ): array {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::get_applications_parameters(), [
            'vacancyid' => $vacancyid,
            'status' => $status,
            'page' => $page,
            'perpage' => $perpage,
        ]);

        // Get and validate context.
        $context = \context_system::instance();
        self::validate_context($context);

        // Check capabilities.
        $canviewall = has_capability('local/jobboard:viewallapplications', $context);

        // Build query conditions.
        $conditions = [];
        $sqlparams = [];

        if (!$canviewall) {
            require_capability('local/jobboard:viewownapplications', $context);
            $conditions[] = "a.userid = :userid";
            $sqlparams['userid'] = $USER->id;
        }

        if (!empty($params['vacancyid'])) {
            $conditions[] = "a.vacancyid = :vacancyid";
            $sqlparams['vacancyid'] = $params['vacancyid'];
        }

        if (!empty($params['status'])) {
            $conditions[] = "a.status = :status";
            $sqlparams['status'] = $params['status'];
        }

        // Build WHERE clause.
        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get total count.
        $total = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a $where",
            $sqlparams
        );

        // Get records.
        $sql = "SELECT a.*, v.code as vacancycode, v.title as vacancytitle
                FROM {local_jobboard_application} a
                JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                $where
                ORDER BY a.timecreated DESC";
        $records = $DB->get_records_sql($sql, $sqlparams, $params['page'] * $params['perpage'], $params['perpage']);

        // Format response.
        $applications = [];
        foreach ($records as $record) {
            $applications[] = self::format_application_for_response($record);
        }

        return [
            'applications' => $applications,
            'total' => $total,
            'page' => $params['page'],
            'perpage' => $params['perpage'],
            'pages' => (int) ceil($total / max($params['perpage'], 1)),
            'warnings' => [],
        ];
    }

    /**
     * Returns description of get_applications return value.
     *
     * @return \external_single_structure
     */
    public static function get_applications_returns(): \external_single_structure {
        return new \external_single_structure([
            'applications' => new \external_multiple_structure(
                self::application_structure()
            ),
            'total' => new \external_value(PARAM_INT, 'Total number of applications'),
            'page' => new \external_value(PARAM_INT, 'Current page number'),
            'perpage' => new \external_value(PARAM_INT, 'Items per page'),
            'pages' => new \external_value(PARAM_INT, 'Total number of pages'),
            'warnings' => new \external_warnings(),
        ]);
    }

    /**
     * Returns description of get_application parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_application_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'id' => new \external_value(PARAM_INT, 'Application ID'),
        ]);
    }

    /**
     * Get a single application by ID.
     *
     * @param int $id Application ID.
     * @return array Application details.
     */
    public static function get_application(int $id): array {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::get_application_parameters(), ['id' => $id]);

        // Get and validate context.
        $context = \context_system::instance();
        self::validate_context($context);

        // Get application with vacancy info.
        $sql = "SELECT a.*, v.code as vacancycode, v.title as vacancytitle
                FROM {local_jobboard_application} a
                JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                WHERE a.id = :id";
        $application = $DB->get_record_sql($sql, ['id' => $params['id']]);

        if (!$application) {
            throw new \moodle_exception('error:applicationnotfound', 'local_jobboard');
        }

        // Check permissions.
        $canviewall = has_capability('local/jobboard:viewallapplications', $context);
        if (!$canviewall && $application->userid != $USER->id) {
            throw new \moodle_exception('error:nopermission', 'local_jobboard');
        }

        // Get documents.
        $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $application->id], 'timecreated ASC');

        $docs = [];
        foreach ($documents as $doc) {
            $validation = $DB->get_record('local_jobboard_doc_validation', ['documentid' => $doc->id]);
            $docs[] = [
                'id' => (int) $doc->id,
                'documenttype' => $doc->documenttype,
                'filename' => $doc->filename,
                'filesize' => (int) $doc->filesize,
                'mimetype' => $doc->mimetype ?? '',
                'issuedate' => (int) ($doc->issuedate ?? 0),
                'timecreated' => (int) $doc->timecreated,
                'validationstatus' => $validation ? ($validation->isvalid ? 'approved' : 'rejected') : 'pending',
                'validationnotes' => $validation ? ($validation->notes ?? '') : '',
                'rejectreason' => $validation ? ($validation->rejectreason ?? '') : '',
            ];
        }

        // Get workflow history.
        $history = $DB->get_records('local_jobboard_workflow_log', ['applicationid' => $application->id], 'timecreated DESC');

        $historyitems = [];
        foreach ($history as $item) {
            $historyitems[] = [
                'id' => (int) $item->id,
                'previousstatus' => $item->previousstatus ?? '',
                'newstatus' => $item->newstatus,
                'comments' => $item->comments ?? '',
                'timecreated' => (int) $item->timecreated,
            ];
        }

        $result = self::format_application_for_response($application);
        $result['documents'] = $docs;
        $result['history'] = $historyitems;
        $result['warnings'] = [];

        return $result;
    }

    /**
     * Returns description of get_application return value.
     *
     * @return \external_single_structure
     */
    public static function get_application_returns(): \external_single_structure {
        $structure = self::application_structure();
        $structure['documents'] = new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_INT, 'Document ID'),
                'documenttype' => new \external_value(PARAM_TEXT, 'Document type'),
                'filename' => new \external_value(PARAM_TEXT, 'Original filename'),
                'filesize' => new \external_value(PARAM_INT, 'File size in bytes'),
                'mimetype' => new \external_value(PARAM_TEXT, 'MIME type'),
                'issuedate' => new \external_value(PARAM_INT, 'Issue date timestamp'),
                'timecreated' => new \external_value(PARAM_INT, 'Upload timestamp'),
                'validationstatus' => new \external_value(PARAM_ALPHA, 'Validation status'),
                'validationnotes' => new \external_value(PARAM_RAW, 'Validation notes'),
                'rejectreason' => new \external_value(PARAM_TEXT, 'Rejection reason'),
            ])
        );
        $structure['history'] = new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_INT, 'History entry ID'),
                'previousstatus' => new \external_value(PARAM_TEXT, 'Previous status'),
                'newstatus' => new \external_value(PARAM_TEXT, 'New status'),
                'comments' => new \external_value(PARAM_RAW, 'Comments'),
                'timecreated' => new \external_value(PARAM_INT, 'Timestamp'),
            ])
        );
        $structure['warnings'] = new \external_warnings();

        return new \external_single_structure($structure);
    }

    /**
     * Returns description of check_application_limit parameters.
     *
     * @return \external_function_parameters
     */
    public static function check_application_limit_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'vacancyid' => new \external_value(PARAM_INT, 'Vacancy ID to check', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Check if user can apply (based on application limits).
     *
     * @param int $vacancyid Vacancy ID.
     * @return array Result with can_apply flag and active applications count.
     */
    public static function check_application_limit(int $vacancyid = 0): array {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::check_application_limit_parameters(), [
            'vacancyid' => $vacancyid,
        ]);

        // Get and validate context.
        $context = \context_system::instance();
        self::validate_context($context);

        // Must be logged in.
        if (!isloggedin() || isguestuser()) {
            return [
                'canapply' => false,
                'reason' => 'notloggedin',
                'message' => get_string('loginrequiredtoapply', 'local_jobboard'),
                'activecount' => 0,
                'maxallowed' => 0,
                'activeapplications' => [],
                'warnings' => [],
            ];
        }

        // Check if user has unlimited applications capability.
        if (has_capability('local/jobboard:unlimitedapplications', $context)) {
            return [
                'canapply' => true,
                'reason' => 'unlimited',
                'message' => '',
                'activecount' => 0,
                'maxallowed' => 0,
                'activeapplications' => [],
                'warnings' => [],
            ];
        }

        // Get configuration.
        $allowmultiple = get_config('local_jobboard', 'allow_multiple_applications');
        $maxactive = (int) get_config('local_jobboard', 'max_active_applications');

        // If unlimited applications allowed.
        if ($allowmultiple && $maxactive == 0) {
            return [
                'canapply' => true,
                'reason' => 'unlimited',
                'message' => '',
                'activecount' => 0,
                'maxallowed' => 0,
                'activeapplications' => [],
                'warnings' => [],
            ];
        }

        // Count active applications.
        $activestates = ['submitted', 'under_review', 'docs_validated', 'docs_rejected', 'interview'];
        list($insql, $inparams) = $DB->get_in_or_equal($activestates, SQL_PARAMS_NAMED);
        $inparams['userid'] = $USER->id;

        $sql = "SELECT a.*, v.code as vacancycode, v.title as vacancytitle
                FROM {local_jobboard_application} a
                JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                WHERE a.userid = :userid AND a.status $insql
                ORDER BY a.timecreated DESC";
        $activeapps = $DB->get_records_sql($sql, $inparams);
        $activecount = count($activeapps);

        // Format active applications for response.
        $activeapplist = [];
        foreach ($activeapps as $app) {
            $activeapplist[] = [
                'id' => (int) $app->id,
                'vacancyid' => (int) $app->vacancyid,
                'vacancycode' => $app->vacancycode,
                'vacancytitle' => $app->vacancytitle,
                'status' => $app->status,
                'timecreated' => (int) $app->timecreated,
            ];
        }

        // Check if already applied to this vacancy.
        if (!empty($params['vacancyid'])) {
            $existing = $DB->get_record('local_jobboard_application', [
                'vacancyid' => $params['vacancyid'],
                'userid' => $USER->id,
            ]);

            if ($existing) {
                return [
                    'canapply' => false,
                    'reason' => 'alreadyapplied',
                    'message' => get_string('error:alreadyapplied', 'local_jobboard'),
                    'activecount' => $activecount,
                    'maxallowed' => $maxactive ?: 1,
                    'activeapplications' => $activeapplist,
                    'warnings' => [],
                ];
            }
        }

        // Determine limit.
        $limit = $allowmultiple ? $maxactive : 1;

        if ($limit > 0 && $activecount >= $limit) {
            return [
                'canapply' => false,
                'reason' => 'limitreached',
                'message' => get_string('error:applicationlimitreached', 'local_jobboard', $limit),
                'activecount' => $activecount,
                'maxallowed' => $limit,
                'activeapplications' => $activeapplist,
                'warnings' => [],
            ];
        }

        return [
            'canapply' => true,
            'reason' => 'allowed',
            'message' => '',
            'activecount' => $activecount,
            'maxallowed' => $limit,
            'activeapplications' => $activeapplist,
            'warnings' => [],
        ];
    }

    /**
     * Returns description of check_application_limit return value.
     *
     * @return \external_single_structure
     */
    public static function check_application_limit_returns(): \external_single_structure {
        return new \external_single_structure([
            'canapply' => new \external_value(PARAM_BOOL, 'Whether user can apply'),
            'reason' => new \external_value(PARAM_ALPHA, 'Reason code'),
            'message' => new \external_value(PARAM_RAW, 'Human-readable message'),
            'activecount' => new \external_value(PARAM_INT, 'Number of active applications'),
            'maxallowed' => new \external_value(PARAM_INT, 'Maximum allowed applications'),
            'activeapplications' => new \external_multiple_structure(
                new \external_single_structure([
                    'id' => new \external_value(PARAM_INT, 'Application ID'),
                    'vacancyid' => new \external_value(PARAM_INT, 'Vacancy ID'),
                    'vacancycode' => new \external_value(PARAM_TEXT, 'Vacancy code'),
                    'vacancytitle' => new \external_value(PARAM_TEXT, 'Vacancy title'),
                    'status' => new \external_value(PARAM_ALPHA, 'Application status'),
                    'timecreated' => new \external_value(PARAM_INT, 'Creation timestamp'),
                ])
            ),
            'warnings' => new \external_warnings(),
        ]);
    }

    // =========================================================================
    // TOKEN MANAGEMENT FUNCTIONS
    // =========================================================================

    /**
     * Returns description of revoke_token parameters.
     *
     * @return \external_function_parameters
     */
    public static function revoke_token_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'tokenid' => new \external_value(PARAM_INT, 'Token ID'),
        ]);
    }

    /**
     * Revoke an API token.
     *
     * @param int $tokenid Token ID.
     * @return array Result with success status.
     */
    public static function revoke_token(int $tokenid): array {
        $params = self::validate_parameters(self::revoke_token_parameters(), ['tokenid' => $tokenid]);
        $context = \context_system::instance();
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
     *
     * @return \external_single_structure
     */
    public static function revoke_token_returns(): \external_single_structure {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Success status'),
            'message' => new \external_value(PARAM_TEXT, 'Status message'),
            'statuslabel' => new \external_value(PARAM_TEXT, 'New status label'),
            'enablelabel' => new \external_value(PARAM_TEXT, 'Enable button label'),
        ]);
    }

    /**
     * Returns description of enable_token parameters.
     *
     * @return \external_function_parameters
     */
    public static function enable_token_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'tokenid' => new \external_value(PARAM_INT, 'Token ID'),
        ]);
    }

    /**
     * Enable an API token.
     *
     * @param int $tokenid Token ID.
     * @return array Result with success status.
     */
    public static function enable_token(int $tokenid): array {
        $params = self::validate_parameters(self::enable_token_parameters(), ['tokenid' => $tokenid]);
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/jobboard:manageapitokens', $context);

        $token = \local_jobboard\api_token::get($params['tokenid']);
        if (!$token) {
            throw new \moodle_exception('api:token:notfound', 'local_jobboard');
        }

        $token->update((object) ['enabled' => true]);

        return [
            'success' => true,
            'message' => get_string('enabled', 'local_jobboard'),
            'statuslabel' => get_string('api:token:status:active', 'local_jobboard'),
            'revokelabel' => get_string('api:token:revoke', 'local_jobboard'),
        ];
    }

    /**
     * Returns description of enable_token return value.
     *
     * @return \external_single_structure
     */
    public static function enable_token_returns(): \external_single_structure {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Success status'),
            'message' => new \external_value(PARAM_TEXT, 'Status message'),
            'statuslabel' => new \external_value(PARAM_TEXT, 'New status label'),
            'revokelabel' => new \external_value(PARAM_TEXT, 'Revoke button label'),
        ]);
    }

    /**
     * Returns description of delete_token parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_token_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'tokenid' => new \external_value(PARAM_INT, 'Token ID'),
        ]);
    }

    /**
     * Delete an API token.
     *
     * @param int $tokenid Token ID.
     * @return array Result with success status.
     */
    public static function delete_token(int $tokenid): array {
        $params = self::validate_parameters(self::delete_token_parameters(), ['tokenid' => $tokenid]);
        $context = \context_system::instance();
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
     *
     * @return \external_single_structure
     */
    public static function delete_token_returns(): \external_single_structure {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Success status'),
            'message' => new \external_value(PARAM_TEXT, 'Status message'),
        ]);
    }

    // =========================================================================
    // IOMAD FUNCTIONS
    // =========================================================================

    /**
     * Returns description of get_departments parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_departments_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'companyid' => new \external_value(PARAM_INT, 'Company ID'),
        ]);
    }

    /**
     * Get list of departments for a company.
     *
     * @param int $companyid Company ID.
     * @return array List of departments.
     */
    public static function get_departments(int $companyid): array {
        // Validate parameters.
        $params = self::validate_parameters(self::get_departments_parameters(), [
            'companyid' => $companyid,
        ]);

        // Get and validate context.
        $context = \context_system::instance();
        self::validate_context($context);

        // Check if user has capability.
        require_capability('local/jobboard:createvacancy', $context);

        // Check if IOMAD is installed.
        if (!\local_jobboard_is_iomad_installed()) {
            return [];
        }

        // Get departments.
        $departments = \local_jobboard_get_departments($params['companyid']);

        $result = [];
        foreach ($departments as $id => $name) {
            $result[] = [
                'id' => (int) $id,
                'name' => $name,
            ];
        }

        return $result;
    }

    /**
     * Returns description of get_departments return value.
     *
     * @return \external_multiple_structure
     */
    public static function get_departments_returns(): \external_multiple_structure {
        return new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_INT, 'Department ID'),
                'name' => new \external_value(PARAM_TEXT, 'Department name'),
            ])
        );
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Get vacancy structure for external functions.
     *
     * @return array Structure definition.
     */
    protected static function vacancy_structure(): array {
        return [
            'id' => new \external_value(PARAM_INT, 'Vacancy ID'),
            'code' => new \external_value(PARAM_TEXT, 'Vacancy code'),
            'title' => new \external_value(PARAM_TEXT, 'Vacancy title'),
            'description' => new \external_value(PARAM_RAW, 'Description'),
            'contracttype' => new \external_value(PARAM_TEXT, 'Contract type'),
            'duration' => new \external_value(PARAM_TEXT, 'Duration'),
            'salary' => new \external_value(PARAM_TEXT, 'Salary information'),
            'location' => new \external_value(PARAM_TEXT, 'Location'),
            'department' => new \external_value(PARAM_TEXT, 'Department'),
            'companyid' => new \external_value(PARAM_INT, 'Company ID'),
            'companyname' => new \external_value(PARAM_TEXT, 'Company name'),
            'opendate' => new \external_value(PARAM_INT, 'Opening date timestamp'),
            'closedate' => new \external_value(PARAM_INT, 'Closing date timestamp'),
            'positions' => new \external_value(PARAM_INT, 'Number of positions'),
            'requirements' => new \external_value(PARAM_RAW, 'Requirements'),
            'desirable' => new \external_value(PARAM_RAW, 'Desirable qualifications'),
            'status' => new \external_value(PARAM_ALPHA, 'Status'),
            'publicationtype' => new \external_value(PARAM_ALPHA, 'Publication type'),
            'timecreated' => new \external_value(PARAM_INT, 'Creation timestamp'),
            'isopen' => new \external_value(PARAM_BOOL, 'Is accepting applications'),
            'daysremaining' => new \external_value(PARAM_INT, 'Days until closing'),
            'applicationcount' => new \external_value(PARAM_INT, 'Number of applications'),
        ];
    }

    /**
     * Get application structure for external functions.
     *
     * @return array Structure definition.
     */
    protected static function application_structure(): array {
        return [
            'id' => new \external_value(PARAM_INT, 'Application ID'),
            'vacancyid' => new \external_value(PARAM_INT, 'Vacancy ID'),
            'vacancycode' => new \external_value(PARAM_TEXT, 'Vacancy code'),
            'vacancytitle' => new \external_value(PARAM_TEXT, 'Vacancy title'),
            'userid' => new \external_value(PARAM_INT, 'Applicant user ID'),
            'status' => new \external_value(PARAM_ALPHA, 'Status'),
            'statusdisplay' => new \external_value(PARAM_TEXT, 'Status display name'),
            'isexemption' => new \external_value(PARAM_BOOL, 'Is ISER exemption'),
            'exemptionreason' => new \external_value(PARAM_RAW, 'Exemption reason'),
            'timecreated' => new \external_value(PARAM_INT, 'Creation timestamp'),
            'timemodified' => new \external_value(PARAM_INT, 'Last modification timestamp'),
        ];
    }

    /**
     * Format vacancy record for API response.
     *
     * @param \stdClass $record Database record.
     * @return array Formatted vacancy.
     */
    protected static function format_vacancy_for_response(\stdClass $record): array {
        global $DB;

        $now = time();
        $isopen = $record->status === 'published' &&
                  $record->opendate <= $now &&
                  $record->closedate >= $now;

        $daysremaining = max(0, (int) floor(($record->closedate - $now) / 86400));

        // Get company name if applicable.
        $companyname = '';
        if (!empty($record->companyid)) {
            $company = $DB->get_record('company', ['id' => $record->companyid], 'name');
            $companyname = $company ? $company->name : '';
        }

        // Count applications.
        $appcount = $DB->count_records('local_jobboard_application', ['vacancyid' => $record->id]);

        return [
            'id' => (int) $record->id,
            'code' => $record->code,
            'title' => $record->title,
            'description' => $record->description ?? '',
            'contracttype' => $record->contracttype ?? '',
            'duration' => $record->duration ?? '',
            'salary' => $record->salary ?? '',
            'location' => $record->location ?? '',
            'department' => $record->department ?? '',
            'companyid' => (int) ($record->companyid ?? 0),
            'companyname' => $companyname,
            'opendate' => (int) $record->opendate,
            'closedate' => (int) $record->closedate,
            'positions' => (int) $record->positions,
            'requirements' => $record->requirements ?? '',
            'desirable' => $record->desirable ?? '',
            'status' => $record->status,
            'publicationtype' => $record->publicationtype ?? 'internal',
            'timecreated' => (int) $record->timecreated,
            'isopen' => $isopen,
            'daysremaining' => $daysremaining,
            'applicationcount' => $appcount,
        ];
    }

    /**
     * Format application record for API response.
     *
     * @param \stdClass $record Database record.
     * @return array Formatted application.
     */
    protected static function format_application_for_response(\stdClass $record): array {
        $statuses = local_jobboard_get_application_statuses();

        return [
            'id' => (int) $record->id,
            'vacancyid' => (int) $record->vacancyid,
            'vacancycode' => $record->vacancycode ?? '',
            'vacancytitle' => $record->vacancytitle ?? '',
            'userid' => (int) $record->userid,
            'status' => $record->status,
            'statusdisplay' => $statuses[$record->status] ?? $record->status,
            'isexemption' => (bool) ($record->isexemption ?? false),
            'exemptionreason' => $record->exemptionreason ?? '',
            'timecreated' => (int) $record->timecreated,
            'timemodified' => (int) ($record->timemodified ?? $record->timecreated),
        ];
    }

    /**
     * Get CSS class for vacancy status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    protected static function get_status_class(string $status): string {
        $classes = [
            'draft' => 'secondary',
            'published' => 'success',
            'closed' => 'danger',
            'assigned' => 'primary',
        ];
        return $classes[$status] ?? 'secondary';
    }
}
