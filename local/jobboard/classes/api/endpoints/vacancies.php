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
 * Vacancies API endpoint for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\api\endpoints;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\vacancy;
use local_jobboard\api\response;

/**
 * API endpoint for vacancy operations.
 */
class vacancies extends base {

    /**
     * GET /vacancies - List published vacancies.
     */
    public function list(): void {
        $pagination = $this->get_pagination();

        // Build filters.
        $filters = [
            'status' => $this->query('status', 'published'),
            'userid' => $this->userid,
            'respect_tenant' => true,
        ];

        // Optional company filter.
        if ($this->query('companyid')) {
            $filters['companyid'] = $this->query_int('companyid');
        }

        // Optional search.
        if ($this->query('search')) {
            $filters['search'] = clean_param($this->query('search'), PARAM_TEXT);
        }

        // Date filters.
        if ($this->query('open_after')) {
            $filters['datefrom'] = strtotime($this->query('open_after'));
        }
        if ($this->query('close_before')) {
            $filters['dateto'] = strtotime($this->query('close_before'));
        }

        // Sort.
        $sort = $this->query('sort', 'closedate');
        $order = $this->query('order', 'ASC');

        // Get vacancies.
        $result = vacancy::get_list(
            $filters,
            $sort,
            $order,
            $pagination['page'] - 1,
            $pagination['perpage']
        );

        // Format response.
        $vacancies = array_map(function (vacancy $v) {
            return $this->format_vacancy($v, false);
        }, $result['vacancies']);

        $this->success(
            $vacancies,
            response::HTTP_OK,
            $this->build_pagination($result['total'], $pagination['page'], $pagination['perpage'])
        );
    }

    /**
     * GET /vacancies/{id} - Get vacancy details.
     */
    public function get(): void {
        $id = $this->param_int('id');

        if (!$id) {
            response::bad_request('Invalid vacancy ID');
        }

        $vacancy = vacancy::get($id);

        if (!$vacancy) {
            response::not_found('Vacancy not found');
        }

        // Check visibility.
        if (!local_jobboard_can_view_vacancy($vacancy->to_record(), $this->userid)) {
            response::forbidden('You do not have access to this vacancy');
        }

        $this->success($this->format_vacancy($vacancy, true));
    }

    /**
     * Format a vacancy for API response.
     *
     * @param vacancy $vacancy The vacancy object.
     * @param bool $detailed Include full details.
     * @return array Formatted vacancy data.
     */
    private function format_vacancy(vacancy $vacancy, bool $detailed = false): array {
        $data = [
            'id' => $vacancy->id,
            'code' => $vacancy->code,
            'title' => $vacancy->title,
            'status' => $vacancy->status,
            'contract_type' => $vacancy->contracttype,
            'location' => $vacancy->location,
            'department' => $vacancy->department,
            'positions' => $vacancy->positions,
            'open_date' => date('Y-m-d', $vacancy->opendate),
            'close_date' => date('Y-m-d', $vacancy->closedate),
            'is_open' => $vacancy->is_open(),
            'company_id' => $vacancy->companyid,
            'company_name' => $vacancy->get_company_name(),
            'application_count' => $vacancy->get_application_count(),
        ];

        if ($detailed) {
            $data['description'] = $vacancy->description;
            $data['duration'] = $vacancy->duration;
            $data['salary'] = $vacancy->salary;
            $data['requirements'] = $vacancy->requirements;
            $data['desirable'] = $vacancy->desirable;
            $data['course_id'] = $vacancy->courseid;
            $data['category_id'] = $vacancy->categoryid;
            $data['created_at'] = date('Y-m-d\TH:i:sP', $vacancy->timecreated);
            $data['updated_at'] = $vacancy->timemodified ? date('Y-m-d\TH:i:sP', $vacancy->timemodified) : null;

            // Include document requirements.
            $data['document_requirements'] = $this->format_document_requirements($vacancy);

            // Include custom fields.
            $data['custom_fields'] = $this->format_custom_fields($vacancy);
        }

        return $data;
    }

    /**
     * Format document requirements for a vacancy.
     *
     * @param vacancy $vacancy The vacancy.
     * @return array Document requirements.
     */
    private function format_document_requirements(vacancy $vacancy): array {
        global $DB;

        $requirements = $vacancy->get_document_requirements();
        $formatted = [];

        foreach ($requirements as $req) {
            // Get document type info.
            $doctype = $DB->get_record('local_jobboard_doctype', ['code' => $req->documenttype]);

            $formatted[] = [
                'type' => $req->documenttype,
                'name' => $doctype ? $doctype->name : $req->documenttype,
                'required' => (bool) $req->required,
                'accepted_formats' => explode(',', $req->acceptedformats ?? 'pdf,jpg,png'),
                'max_size_bytes' => $req->maxsize ?: local_jobboard_get_max_filesize(),
                'requires_issue_date' => (bool) $req->requiresissuedate,
                'max_age_days' => $req->maxagedays,
                'instructions' => $req->instructions,
                'external_url' => $doctype ? $doctype->externalurl : null,
            ];
        }

        return $formatted;
    }

    /**
     * Format custom fields for a vacancy.
     *
     * @param vacancy $vacancy The vacancy.
     * @return array Custom fields.
     */
    private function format_custom_fields(vacancy $vacancy): array {
        $fields = $vacancy->get_custom_fields();
        $formatted = [];

        foreach ($fields as $field) {
            $formatted[] = [
                'name' => $field->fieldname,
                'type' => $field->fieldtype,
                'value' => $field->fieldvalue,
                'required' => (bool) $field->required,
            ];
        }

        return $formatted;
    }
}
