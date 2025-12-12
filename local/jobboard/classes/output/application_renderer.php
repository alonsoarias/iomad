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
 * Application renderer for Job Board plugin.
 *
 * Handles rendering of application forms, lists, and detail views.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Application renderer class.
 *
 * Responsible for rendering application-related UI components including
 * forms, lists, detail views, and status tracking.
 */
class application_renderer extends renderer_base {

    /**
     * Render application row for list display.
     *
     * @param object $application The application object.
     * @param bool $canreview Whether user can review.
     * @param bool $showapplicant Whether to show applicant name.
     * @return string HTML output.
     */
    public function render_application_row(
        $application,
        bool $canreview = false,
        bool $showapplicant = false
    ): string {
        $data = $this->prepare_application_row_data($application, $canreview, $showapplicant);
        return $this->render_from_template('local_jobboard/application_row', $data);
    }

    /**
     * Prepare application row template data.
     *
     * @param object $application The application object.
     * @param bool $canreview Whether user can review.
     * @param bool $showapplicant Whether to show applicant name.
     * @return array Template data.
     */
    public function prepare_application_row_data(
        $application,
        bool $canreview,
        bool $showapplicant
    ): array {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        $documentcount = $DB->count_records('local_jobboard_document', ['applicationid' => $application->id]);
        $documentsvalidated = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'approved',
        ]);

        $data = [
            'id' => $application->id,
            'vacancyid' => $application->vacancyid,
            'vacancycode' => $vacancy ? $vacancy->code : '',
            'vacancytitle' => $vacancy ? $vacancy->title : '',
            'status' => $application->status,
            'statusclass' => $this->get_application_status_class($application->status),
            'statuslabel' => get_string('appstatus:' . $application->status, 'local_jobboard'),
            'timecreated' => $this->format_datetime($application->timecreated),
            'documentcount' => $documentcount,
            'documentsvalidated' => $documentsvalidated,
            'documentpercent' => $this->calculate_percentage($documentsvalidated, $documentcount),
            'viewurl' => $this->get_url('application', ['id' => $application->id]),
            'canreview' => $canreview,
            'reviewurl' => $this->get_url('review', ['applicationid' => $application->id]),
        ];

        if ($showapplicant) {
            $data['applicantname'] = $this->get_user_fullname($application->userid);
            $data['applicantid'] = $application->userid;
        }

        return $data;
    }

    /**
     * Render application list.
     *
     * @param array $applications Array of application objects.
     * @param bool $canreview Whether user can review.
     * @param bool $showapplicant Whether to show applicant names.
     * @param string $filterform Filter form HTML.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_application_list(
        array $applications,
        bool $canreview = false,
        bool $showapplicant = false,
        string $filterform = '',
        string $pagination = ''
    ): string {
        $rows = [];
        foreach ($applications as $application) {
            $rows[] = $this->prepare_application_row_data($application, $canreview, $showapplicant);
        }

        $data = [
            'hasapplications' => !empty($rows),
            'applications' => $rows,
            'count' => count($rows),
            'showapplicant' => $showapplicant,
            'filterform' => $filterform,
            'pagination' => $pagination,
        ];

        return $this->render_from_template('local_jobboard/application_list', $data);
    }

    /**
     * Render application detail page.
     *
     * @param object $application The application object.
     * @param array $documents Application documents.
     * @param bool $canreview Whether user can review.
     * @param bool $canedit Whether user can edit.
     * @return string HTML output.
     */
    public function render_application_detail(
        $application,
        array $documents = [],
        bool $canreview = false,
        bool $canedit = false
    ): string {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        $data = $this->prepare_application_row_data($application, $canreview, true);

        // Vacancy info.
        if ($vacancy) {
            $vacancyrenderer = new vacancy_renderer($this->page, $this->target);
            $data['vacancy'] = $vacancyrenderer->prepare_vacancy_card_data($vacancy, false);
        }

        // Documents.
        $reviewrenderer = new review_renderer($this->page, $this->target);
        $data['documents'] = array_map(function($doc) use ($reviewrenderer) {
            return $reviewrenderer->prepare_document_row_data($doc);
        }, $documents);
        $data['hasdocuments'] = !empty($documents);

        // Timeline/history.
        $data['timeline'] = $this->prepare_application_timeline($application);
        $data['hastimeline'] = !empty($data['timeline']);

        // Actions.
        $data['canedit'] = $canedit && $application->status === 'draft';
        $data['canwithdraw'] = $canedit && in_array($application->status, ['draft', 'submitted']);
        if ($data['canedit']) {
            $data['editurl'] = $this->get_url('apply', ['id' => $application->vacancyid, 'applicationid' => $application->id]);
        }
        if ($data['canwithdraw']) {
            $data['withdrawurl'] = $this->get_url('application', ['id' => $application->id, 'action' => 'withdraw']);
        }

        return $this->render_from_template('local_jobboard/application_detail', $data);
    }

    /**
     * Prepare application timeline data.
     *
     * @param object $application The application object.
     * @return array Timeline entries.
     */
    protected function prepare_application_timeline($application): array {
        global $DB;

        $logs = $DB->get_records('local_jobboard_workflow_log', [
            'applicationid' => $application->id,
        ], 'timecreated ASC');

        $timeline = [];
        foreach ($logs as $log) {
            $timeline[] = [
                'action' => $log->action,
                'actionlabel' => get_string('action:' . $log->action, 'local_jobboard'),
                'oldstatus' => $log->oldstatus,
                'newstatus' => $log->newstatus,
                'newstatusclass' => $this->get_application_status_class($log->newstatus),
                'newstatuslabel' => get_string('appstatus:' . $log->newstatus, 'local_jobboard'),
                'username' => $this->get_user_fullname($log->userid),
                'timecreated' => $this->format_datetime($log->timecreated),
                'notes' => $log->notes ?? '',
            ];
        }

        return $timeline;
    }

    /**
     * Render my applications page.
     *
     * @param array $applications Array of user's applications.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_my_applications(array $applications, string $pagination = ''): string {
        $cards = [];
        foreach ($applications as $application) {
            $cards[] = $this->prepare_my_application_card($application);
        }

        $data = [
            'hasapplications' => !empty($cards),
            'applications' => $cards,
            'count' => count($cards),
            'pagination' => $pagination,
            'browsevacanciesurl' => $this->get_url('vacancies'),
        ];

        return $this->render_from_template('local_jobboard/my_applications', $data);
    }

    /**
     * Prepare my application card data.
     *
     * @param object $application The application object.
     * @return array Card data.
     */
    protected function prepare_my_application_card($application): array {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        $documentcount = $DB->count_records('local_jobboard_document', ['applicationid' => $application->id]);
        $documentsvalidated = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'approved',
        ]);
        $documentspending = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'pending',
        ]);

        return [
            'id' => $application->id,
            'vacancycode' => $vacancy ? $vacancy->code : '',
            'vacancytitle' => $vacancy ? $vacancy->title : '',
            'vacancyurl' => $vacancy ? $this->get_url('vacancy', ['id' => $vacancy->id]) : '',
            'status' => $application->status,
            'statusclass' => $this->get_application_status_class($application->status),
            'statuslabel' => get_string('appstatus:' . $application->status, 'local_jobboard'),
            'isdraft' => $application->status === 'draft',
            'issubmitted' => $application->status === 'submitted',
            'isapproved' => $application->status === 'approved',
            'isrejected' => $application->status === 'rejected',
            'timecreated' => $this->format_datetime($application->timecreated),
            'documentcount' => $documentcount,
            'documentsvalidated' => $documentsvalidated,
            'documentspending' => $documentspending,
            'documentpercent' => $this->calculate_percentage($documentsvalidated, $documentcount),
            'haspendingdocs' => $documentspending > 0,
            'viewurl' => $this->get_url('application', ['id' => $application->id]),
            'editurl' => $this->get_url('apply', ['id' => $application->vacancyid, 'applicationid' => $application->id]),
            'canedit' => $application->status === 'draft',
        ];
    }

    /**
     * Render application form page.
     *
     * @param \moodleform $form The application form.
     * @param object $vacancy The vacancy being applied to.
     * @param object|null $application Existing application if editing.
     * @return string HTML output.
     */
    public function render_application_form($form, $vacancy, $application = null): string {
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);

        $data = [
            'formhtml' => $form->render(),
            'vacancy' => $vacancyrenderer->prepare_vacancy_card_data($vacancy, false),
            'isedit' => $application !== null,
            'applicationid' => $application ? $application->id : null,
            'backurl' => $this->get_url('vacancy', ['id' => $vacancy->id]),
        ];

        return $this->render_from_template('local_jobboard/application_form', $data);
    }

    /**
     * Render application confirmation page.
     *
     * @param object $application The submitted application.
     * @param object $vacancy The vacancy applied to.
     * @return string HTML output.
     */
    public function render_application_confirmation($application, $vacancy): string {
        $data = [
            'applicationid' => $application->id,
            'vacancycode' => $vacancy->code,
            'vacancytitle' => $vacancy->title,
            'submitteddate' => $this->format_datetime($application->timecreated),
            'viewapplicationurl' => $this->get_url('application', ['id' => $application->id]),
            'myapplicationsurl' => $this->get_url('myapplications'),
            'browsevacanciesurl' => $this->get_url('vacancies'),
        ];

        return $this->render_from_template('local_jobboard/application_confirmation', $data);
    }
}
