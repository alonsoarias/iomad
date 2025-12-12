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
 * Review renderer for Job Board plugin.
 *
 * Handles rendering of document review, validation, and reviewer interfaces.
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
 * Review renderer class.
 *
 * Responsible for rendering review-related UI components including
 * document validation, reviewer dashboards, and bulk validation interfaces.
 */
class review_renderer extends renderer_base {

    /**
     * Render document row for review.
     *
     * @param object $document The document object.
     * @param bool $canvalidate Whether user can validate.
     * @return string HTML output.
     */
    public function render_document_row($document, bool $canvalidate = false): string {
        $data = $this->prepare_document_row_data($document, $canvalidate);
        return $this->render_from_template('local_jobboard/document_row', $data);
    }

    /**
     * Prepare document row template data.
     *
     * @param object $document The document object.
     * @param bool $canvalidate Whether user can validate.
     * @return array Template data.
     */
    public function prepare_document_row_data($document, bool $canvalidate = false): array {
        global $DB;

        $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $document->doctypeid]);

        return [
            'id' => $document->id,
            'doctypename' => $doctype ? $doctype->name : get_string('unknowndoctype', 'local_jobboard'),
            'doctypecode' => $doctype ? $doctype->code : '',
            'filename' => $document->filename,
            'filesize' => display_size($document->filesize ?? 0),
            'mimetype' => $document->mimetype ?? '',
            'timecreated' => $this->format_datetime($document->timecreated),
            'validationstatus' => $document->validationstatus,
            'statusclass' => $this->get_document_status_class($document->validationstatus),
            'statuslabel' => get_string('docstatus:' . $document->validationstatus, 'local_jobboard'),
            'ispending' => $document->validationstatus === 'pending',
            'isapproved' => $document->validationstatus === 'approved',
            'isrejected' => $document->validationstatus === 'rejected',
            'downloadurl' => $this->get_url('download', ['documentid' => $document->id]),
            'previewurl' => $this->get_url('preview', ['documentid' => $document->id]),
            'canvalidate' => $canvalidate && $document->validationstatus === 'pending',
            'validateurl' => $this->get_url('validate', ['documentid' => $document->id]),
            'rejectionnotes' => $document->rejectionnotes ?? '',
        ];
    }

    /**
     * Render review dashboard.
     *
     * @param array $pendingapplications Applications pending review.
     * @param array $pendingdocuments Documents pending validation.
     * @param array $stats Review statistics.
     * @return string HTML output.
     */
    public function render_review_dashboard(
        array $pendingapplications,
        array $pendingdocuments,
        array $stats = []
    ): string {
        $applicationrenderer = new application_renderer($this->page, $this->target);

        $applications = array_map(function($app) use ($applicationrenderer) {
            return $applicationrenderer->prepare_application_row_data($app, true, true);
        }, $pendingapplications);

        $documents = array_map(function($doc) {
            return $this->prepare_document_row_data($doc, true);
        }, $pendingdocuments);

        $data = [
            'hasapplications' => !empty($applications),
            'applications' => $applications,
            'applicationcount' => count($applications),
            'hasdocuments' => !empty($documents),
            'documents' => $documents,
            'documentcount' => count($documents),
            'stats' => $stats,
            'bulkvalidationurl' => $this->get_url('bulkvalidation'),
        ];

        return $this->render_from_template('local_jobboard/review_dashboard', $data);
    }

    /**
     * Render document validation page.
     *
     * @param object $document The document to validate.
     * @param object $application The associated application.
     * @param array $checklist Validation checklist items.
     * @param \moodleform $form The validation form.
     * @return string HTML output.
     */
    public function render_document_validation(
        $document,
        $application,
        array $checklist,
        $form
    ): string {
        global $DB;

        $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $document->doctypeid]);
        $applicationrenderer = new application_renderer($this->page, $this->target);

        $data = [
            'document' => $this->prepare_document_row_data($document, false),
            'application' => $applicationrenderer->prepare_application_row_data($application, false, true),
            'doctype' => [
                'name' => $doctype->name,
                'description' => $doctype->description ?? '',
                'requirements' => format_text($doctype->requirements ?? '', FORMAT_HTML),
            ],
            'checklist' => $checklist,
            'haschecklist' => !empty($checklist),
            'formhtml' => $form->render(),
            'previewurl' => $this->get_url('preview', ['documentid' => $document->id]),
            'downloadurl' => $this->get_url('download', ['documentid' => $document->id]),
            'backurl' => $this->get_url('review', ['applicationid' => $application->id]),
        ];

        return $this->render_from_template('local_jobboard/document_validation', $data);
    }

    /**
     * Render bulk validation page.
     *
     * @param array $documents Documents to validate.
     * @param string $filterform Filter form HTML.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_bulk_validation(
        array $documents,
        string $filterform = '',
        string $pagination = ''
    ): string {
        $rows = [];
        foreach ($documents as $document) {
            $row = $this->prepare_document_row_data($document, true);
            // Add application info.
            global $DB;
            $application = $DB->get_record('local_jobboard_application', ['id' => $document->applicationid]);
            if ($application) {
                $row['applicantname'] = $this->get_user_fullname($application->userid);
                $row['applicationid'] = $application->id;
            }
            $rows[] = $row;
        }

        $data = [
            'hasdocuments' => !empty($rows),
            'documents' => $rows,
            'count' => count($rows),
            'filterform' => $filterform,
            'pagination' => $pagination,
            'bulkapproveurl' => $this->get_url('bulkvalidation', ['action' => 'approve']),
            'bulkrejecturl' => $this->get_url('bulkvalidation', ['action' => 'reject']),
        ];

        return $this->render_from_template('local_jobboard/bulk_validation', $data);
    }

    /**
     * Render application review page.
     *
     * @param object $application The application to review.
     * @param array $documents Application documents.
     * @param \moodleform $statusform Status change form.
     * @return string HTML output.
     */
    public function render_application_review(
        $application,
        array $documents,
        $statusform
    ): string {
        $applicationrenderer = new application_renderer($this->page, $this->target);

        $documentdata = array_map(function($doc) {
            return $this->prepare_document_row_data($doc, true);
        }, $documents);

        $approvedcount = count(array_filter($documents, fn($d) => $d->validationstatus === 'approved'));
        $rejectedcount = count(array_filter($documents, fn($d) => $d->validationstatus === 'rejected'));
        $pendingcount = count(array_filter($documents, fn($d) => $d->validationstatus === 'pending'));

        $data = [
            'application' => $applicationrenderer->prepare_application_row_data($application, false, true),
            'documents' => $documentdata,
            'hasdocuments' => !empty($documentdata),
            'documentstats' => [
                'total' => count($documents),
                'approved' => $approvedcount,
                'rejected' => $rejectedcount,
                'pending' => $pendingcount,
                'approvedpercent' => $this->calculate_percentage($approvedcount, count($documents)),
            ],
            'alldocsapproved' => $pendingcount === 0 && $rejectedcount === 0,
            'hasrejected' => $rejectedcount > 0,
            'statusformhtml' => $statusform->render(),
            'backurl' => $this->get_url('review'),
        ];

        return $this->render_from_template('local_jobboard/application_review', $data);
    }

    /**
     * Render reviewer assignment page.
     *
     * @param object $application The application.
     * @param array $assignedreviewers Currently assigned reviewers.
     * @param array $availablereviewers Available reviewers to assign.
     * @param \moodleform $form Assignment form.
     * @return string HTML output.
     */
    public function render_reviewer_assignment(
        $application,
        array $assignedreviewers,
        array $availablereviewers,
        $form
    ): string {
        $applicationrenderer = new application_renderer($this->page, $this->target);

        $assigned = array_map(function($reviewer) {
            return [
                'id' => $reviewer->id,
                'userid' => $reviewer->userid,
                'name' => $this->get_user_fullname($reviewer->userid),
                'assigneddate' => $this->format_datetime($reviewer->timecreated),
                'removeurl' => $this->get_url('reviewers', [
                    'applicationid' => $reviewer->applicationid,
                    'action' => 'remove',
                    'reviewerid' => $reviewer->id,
                ]),
            ];
        }, $assignedreviewers);

        $data = [
            'application' => $applicationrenderer->prepare_application_row_data($application, false, true),
            'assignedreviewers' => $assigned,
            'hasassigned' => !empty($assigned),
            'formhtml' => $form->render(),
            'backurl' => $this->get_url('review', ['applicationid' => $application->id]),
        ];

        return $this->render_from_template('local_jobboard/reviewer_assignment', $data);
    }

    /**
     * Render validation checklist.
     *
     * @param array $items Checklist items.
     * @param string $name Form field name prefix.
     * @return string HTML output.
     */
    public function render_validation_checklist(array $items, string $name = 'checklist'): string {
        $checklistdata = [];
        foreach ($items as $index => $item) {
            $checklistdata[] = [
                'index' => $index,
                'name' => $name . '[' . $index . ']',
                'label' => $item['label'],
                'required' => $item['required'] ?? false,
                'checked' => $item['checked'] ?? false,
            ];
        }

        return $this->render_from_template('local_jobboard/validation_checklist', [
            'items' => $checklistdata,
            'hasitems' => !empty($checklistdata),
        ]);
    }
}
