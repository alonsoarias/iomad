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
 * Applications API endpoint for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\api\endpoints;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\application;
use local_jobboard\vacancy;
use local_jobboard\document;
use local_jobboard\api\response;

/**
 * API endpoint for application operations.
 */
class applications extends base {

    /**
     * GET /applications - List applications for the authenticated user.
     */
    public function list(): void {
        global $DB;

        $pagination = $this->get_pagination();

        // Build query.
        $params = ['userid' => $this->userid];
        $where = ['userid = :userid'];

        // Optional status filter.
        if ($this->query('status')) {
            $where[] = 'status = :status';
            $params['status'] = clean_param($this->query('status'), PARAM_ALPHANUMEXT);
        }

        // Optional vacancy filter.
        if ($this->query('vacancy_id')) {
            $where[] = 'vacancyid = :vacancyid';
            $params['vacancyid'] = $this->query_int('vacancy_id');
        }

        // Count total.
        $wheresql = implode(' AND ', $where);
        $total = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} WHERE $wheresql",
            $params
        );

        // Get applications.
        $records = $DB->get_records_sql(
            "SELECT * FROM {local_jobboard_application}
             WHERE $wheresql
             ORDER BY timecreated DESC",
            $params,
            $pagination['offset'],
            $pagination['perpage']
        );

        // Format response.
        $applications = array_map(function ($record) {
            $app = new application($record);
            return $this->format_application($app, false);
        }, $records);

        $this->success(
            array_values($applications),
            response::HTTP_OK,
            $this->build_pagination($total, $pagination['page'], $pagination['perpage'])
        );
    }

    /**
     * GET /applications/{id} - Get application details.
     */
    public function get(): void {
        $id = $this->param_int('id');

        if (!$id) {
            response::bad_request('Invalid application ID');
        }

        $application = application::get($id);

        if (!$application) {
            response::not_found('Application not found');
        }

        // Check access - user can only view their own applications.
        if ($application->userid != $this->userid) {
            response::forbidden('You do not have access to this application');
        }

        $this->success($this->format_application($application, true));
    }

    /**
     * POST /applications - Create a new application.
     */
    public function create(): void {
        global $USER;

        // Validate required fields.
        $errors = $this->validate_required(['vacancy_id', 'consent_given', 'digital_signature']);

        if (!empty($errors)) {
            response::validation_error($errors);
        }

        // Validate consent.
        if (!$this->input('consent_given')) {
            response::validation_error(['consent_given' => 'Consent must be given to submit an application']);
        }

        $vacancyid = (int) $this->input('vacancy_id');

        // Check vacancy exists and is open.
        $vacancy = vacancy::get($vacancyid);
        if (!$vacancy) {
            response::not_found('Vacancy not found');
        }

        if (!$vacancy->is_open()) {
            response::bad_request('This vacancy is not accepting applications');
        }

        // Check user can view vacancy (multi-tenant).
        if (!local_jobboard_can_view_vacancy($vacancy->to_record(), $this->userid)) {
            response::forbidden('You do not have access to this vacancy');
        }

        // Check for existing application.
        if (application::exists($vacancyid, $this->userid)) {
            response::error(
                'You have already applied to this vacancy',
                'duplicate_application',
                response::HTTP_CONFLICT
            );
        }

        // Build application data.
        $data = (object) [
            'vacancyid' => $vacancyid,
            'userid' => $this->userid,
            'digitalsignature' => clean_param($this->input('digital_signature'), PARAM_TEXT),
            'coverletter' => clean_param($this->input('cover_letter', ''), PARAM_TEXT),
            'applicationdata' => $this->input('custom_data') ? json_encode($this->input('custom_data')) : null,
        ];

        // Create application.
        try {
            $application = application::create($data);
        } catch (\moodle_exception $e) {
            response::bad_request($e->getMessage());
        }

        $this->created([
            'id' => $application->id,
            'status' => $application->status,
            'message' => 'Application submitted successfully',
            'created_at' => date('Y-m-d\TH:i:sP', $application->timecreated),
        ]);
    }

    /**
     * Format an application for API response.
     *
     * @param application $application The application object.
     * @param bool $detailed Include full details.
     * @return array Formatted application data.
     */
    private function format_application(application $application, bool $detailed = false): array {
        $vacancy = vacancy::get($application->vacancyid);

        $data = [
            'id' => $application->id,
            'status' => $application->status,
            'status_display' => $application->get_status_display(),
            'vacancy' => [
                'id' => $vacancy ? $vacancy->id : null,
                'code' => $vacancy ? $vacancy->code : null,
                'title' => $vacancy ? $vacancy->title : null,
            ],
            'is_iser_exemption' => (bool) $application->isexemption,
            'created_at' => date('Y-m-d\TH:i:sP', $application->timecreated),
            'updated_at' => $application->timemodified ? date('Y-m-d\TH:i:sP', $application->timemodified) : null,
        ];

        if ($detailed) {
            $data['cover_letter'] = $application->coverletter;
            $data['digital_signature'] = $application->digitalsignature;
            $data['consent_given'] = (bool) $application->consentgiven;
            $data['consent_timestamp'] = $application->consenttimestamp
                ? date('Y-m-d\TH:i:sP', $application->consenttimestamp) : null;
            $data['status_notes'] = $application->statusnotes;
            $data['exemption_reason'] = $application->exemptionreason;

            // Include custom data.
            if ($application->applicationdata) {
                $data['custom_data'] = json_decode($application->applicationdata, true);
            }

            // Include documents.
            $data['documents'] = $this->format_documents($application);

            // Include workflow history.
            $data['history'] = $this->format_workflow_history($application);
        }

        return $data;
    }

    /**
     * Format documents for an application.
     *
     * @param application $application The application.
     * @return array Document list.
     */
    private function format_documents(application $application): array {
        global $DB;

        $documents = $DB->get_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'issuperseded' => 0,
        ], 'timecreated ASC');

        $formatted = [];

        foreach ($documents as $record) {
            $doc = new document($record);
            $validation = $doc->get_validation();

            $formatted[] = [
                'id' => $doc->id,
                'type' => $doc->documenttype,
                'type_display' => $doc->get_type_display(),
                'filename' => $doc->filename,
                'filesize' => $doc->filesize,
                'filesize_display' => $doc->get_filesize_display(),
                'mimetype' => $doc->mimetype,
                'issue_date' => $doc->issuedate ? date('Y-m-d', $doc->issuedate) : null,
                'days_since_issue' => $doc->get_days_since_issue(),
                'validation_status' => $doc->get_validation_status(),
                'validation_status_display' => $doc->get_validation_status_display(),
                'reject_reason' => $validation && !$validation->isvalid ? $validation->rejectreason : null,
                'validator_notes' => $validation ? $validation->notes : null,
                'uploaded_at' => date('Y-m-d\TH:i:sP', $doc->timecreated),
            ];
        }

        return $formatted;
    }

    /**
     * Format workflow history for an application.
     *
     * @param application $application The application.
     * @return array Workflow history.
     */
    private function format_workflow_history(application $application): array {
        global $DB;

        $logs = $DB->get_records('local_jobboard_workflow_log', [
            'applicationid' => $application->id,
        ], 'timecreated ASC');

        $formatted = [];

        foreach ($logs as $log) {
            $formatted[] = [
                'previous_status' => $log->previousstatus,
                'new_status' => $log->newstatus,
                'comments' => $log->comments,
                'timestamp' => date('Y-m-d\TH:i:sP', $log->timecreated),
            ];
        }

        return $formatted;
    }
}
