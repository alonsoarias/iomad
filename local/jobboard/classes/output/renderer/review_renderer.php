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
 * Review renderer trait for Job Board plugin.
 *
 * Contains all review and validation rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Trait for review rendering functionality.
 */
trait review_renderer {

    /**
     * Render review page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_review_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/review/index', $data);
    }

    /**
     * Render review results partial (for AJAX filtering).
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_review_results(array $data): string {
        return $this->render_from_template('local_jobboard/pages/review/results', $data);
    }

    /**
     * Render my reviews page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_myreviews_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/review/my_reviews', $data);
    }

    /**
     * Render bulk validate page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_bulk_validate_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/documents/bulk_validate', $data);
    }

    /**
     * Render validate document page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_validate_document_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/documents/validate', $data);
    }

    /**
     * Render reupload document page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_reupload_document_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/documents/reupload', $data);
    }

    /**
     * Prepare my reviews page data for template.
     *
     * @param int $userid Current user ID.
     * @param array $applications Array of application records.
     * @param int $total Total count.
     * @param array $stats Reviewer statistics.
     * @param array $filtervalues Current filter values.
     * @param array $vacancies Available vacancies for filter.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @return array Complete template data.
     */
    public function prepare_myreviews_page_data(
        int $userid,
        array $applications,
        int $total,
        array $stats,
        array $filtervalues,
        array $vacancies,
        int $page,
        int $perpage
    ): array {
        global $OUTPUT;

        // Prepare assignments data.
        $assignments = [];
        foreach ($applications as $app) {
            $totaldocs = (int)($app->total_docs ?? 0);
            $validatedocs = (int)($app->validated_docs ?? 0);
            $rejecteddocs = (int)($app->rejected_docs ?? 0);
            $pendingdocs = (int)($app->pending_docs ?? 0);

            $daysUntilClose = (int)ceil(($app->closedate - time()) / 86400);
            $isUrgent = $daysUntilClose <= 3 && $daysUntilClose > 0;
            $isClosed = $daysUntilClose <= 0;

            // Status color.
            $statusColor = 'secondary';
            if (in_array($app->status, ['docs_validated', 'selected'])) {
                $statusColor = 'success';
            } else if (in_array($app->status, ['docs_rejected', 'rejected'])) {
                $statusColor = 'danger';
            } else if ($app->status === 'under_review') {
                $statusColor = 'warning';
            } else if ($app->status === 'submitted') {
                $statusColor = 'info';
            }

            // Calculate percentages.
            $approvedpercent = $totaldocs > 0 ? round(($validatedocs / $totaldocs) * 100) : 0;
            $rejectedpercent = $totaldocs > 0 ? round(($rejecteddocs / $totaldocs) * 100) : 0;

            $assignments[] = [
                'id' => $app->id,
                'applicantname' => format_string($app->firstname . ' ' . $app->lastname),
                'email' => $app->email,
                'vacancycode' => format_string($app->vacancy_code),
                'vacancytitle' => format_string($app->vacancy_title),
                'status' => $app->status,
                'statuslabel' => get_string('status_' . $app->status, 'local_jobboard'),
                'statuscolor' => $statusColor,
                'totaldocs' => $totaldocs,
                'approvedcount' => $validatedocs,
                'rejectedcount' => $rejecteddocs,
                'pendingcount' => $pendingdocs,
                'approvedpercent' => $approvedpercent,
                'rejectedpercent' => $rejectedpercent,
                'closedate' => userdate($app->closedate, get_string('strftimedate', 'langconfig')),
                'daysuntilclose' => max(0, $daysUntilClose),
                'isurgent' => $isUrgent,
                'isclosed' => $isClosed,
                'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'applicationid' => $app->id]))->out(false),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id]))->out(false),
                'downloadzipurl' => (new moodle_url('/local/jobboard/download_documents.php', ['applicationid' => $app->id, 'sesskey' => sesskey()]))->out(false),
            ];
        }

        // Stats cards.
        $statscards = [
            [
                'value' => (string)($stats['pending'] ?? 0),
                'label' => get_string('pendingassignments', 'local_jobboard'),
                'icon' => 'tasks',
                'color' => 'primary',
            ],
            [
                'value' => (string)($stats['validated'] ?? 0),
                'label' => get_string('documentsvalidated', 'local_jobboard'),
                'icon' => 'check-circle',
                'color' => 'success',
            ],
            [
                'value' => (string)($stats['rejected'] ?? 0),
                'label' => get_string('documentsrejected', 'local_jobboard'),
                'icon' => 'times-circle',
                'color' => 'danger',
            ],
            [
                'value' => round($stats['avg_time_hours'] ?? 0, 1) . 'h',
                'label' => get_string('avgvalidationtime', 'local_jobboard'),
                'icon' => 'clock',
                'color' => 'info',
            ],
        ];

        // Filter form.
        $statusOptions = [['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($filtervalues['status'])]];
        foreach (['submitted', 'under_review', 'docs_validated', 'docs_rejected'] as $s) {
            $statusOptions[] = [
                'value' => $s,
                'label' => get_string('status_' . $s, 'local_jobboard'),
                'selected' => ($filtervalues['status'] ?? '') === $s,
            ];
        }

        $vacancyOptions = [['value' => '0', 'label' => get_string('allvacancies', 'local_jobboard'), 'selected' => empty($filtervalues['vacancy'])]];
        foreach ($vacancies as $v) {
            $vacancyOptions[] = [
                'value' => (string)$v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($filtervalues['vacancy'] ?? 0) == $v->id,
            ];
        }

        $priorityOptions = [
            ['value' => '', 'label' => get_string('datesubmitted', 'local_jobboard'), 'selected' => empty($filtervalues['priority'])],
            ['value' => 'closing', 'label' => get_string('closingdate', 'local_jobboard'), 'selected' => ($filtervalues['priority'] ?? '') === 'closing'],
            ['value' => 'pending', 'label' => get_string('pendingdocuments', 'local_jobboard'), 'selected' => ($filtervalues['priority'] ?? '') === 'pending'],
        ];

        // Per-page options.
        $perpageoptions = [];
        foreach ([10, 20, 50, 100] as $opt) {
            $perpageoptions[] = [
                'value' => $opt,
                'label' => $opt,
                'selected' => $perpage == $opt,
            ];
        }

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [['name' => 'view', 'value' => 'myreviews']],
            'fields' => [
                [
                    'name' => 'status',
                    'label' => get_string('status', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $statusOptions,
                    'col' => 'jb-col-md-2',
                ],
                [
                    'name' => 'vacancy',
                    'label' => get_string('vacancy', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $vacancyOptions,
                    'col' => 'jb-col-md-3',
                ],
                [
                    'name' => 'priority',
                    'label' => get_string('sortby', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $priorityOptions,
                    'col' => 'jb-col-md-3',
                ],
                [
                    'name' => 'perpage',
                    'label' => get_string('perpage', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $perpageoptions,
                    'col' => 'jb-col-md-2',
                ],
            ],
        ];

        // Showing info.
        $showinginfo = '';
        if ($total > 0) {
            $from = ($page * $perpage) + 1;
            $to = min(($page + 1) * $perpage, $total);
            $showinginfo = get_string('showingxtoy', 'local_jobboard', (object)['from' => $from, 'to' => $to, 'total' => $total]);
        }

        // Pagination.
        $pagination = '';
        if ($total > $perpage) {
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'myreviews',
                'status' => $filtervalues['status'] ?? '',
                'vacancy' => $filtervalues['vacancy'] ?? 0,
                'priority' => $filtervalues['priority'] ?? '',
                'perpage' => $perpage,
            ]);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'reviewqueueurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            'stats' => $statscards,
            'filterform' => $filterform,
            'showinginfo' => $showinginfo,
            'hasassignments' => !empty($assignments),
            'assignments' => $assignments,
            'pagination' => $pagination,
        ];
    }

    /**
     * Prepare data for review page template.
     *
     * Handles both list mode (all applications pending review) and single application review mode.
     *
     * @param array $params View parameters including vacancyid, applicationid, page, perpage.
     * @param \context $context Current context.
     * @param int $total Total count (for list mode pagination).
     * @param array $applications Applications data (for list mode).
     * @param array $queuestats Queue statistics (for list mode).
     * @param ?object $selectedapp Selected application object (for single app mode).
     * @param ?object $vacancy Vacancy object (for single app mode).
     * @param ?object $applicant User object (for single app mode).
     * @param array $documents Documents array (for single app mode).
     * @param array $navdata Navigation data: previd, nextid, navposition, navtotal.
     * @return array Template data.
     */
    public function prepare_review_page_data(
        array $params,
        \context $context,
        int $total = 0,
        array $applications = [],
        array $queuestats = [],
        ?object $selectedapp = null,
        ?object $vacancy = null,
        ?object $applicant = null,
        array $documents = [],
        array $navdata = []
    ): array {
        global $OUTPUT;

        $vacancyid = $params['vacancyid'] ?? 0;
        $applicationid = $params['applicationid'] ?? 0;
        $page = $params['page'] ?? 0;
        $perpage = $params['perpage'] ?? 20;

        $dashboardurl = (new moodle_url('/local/jobboard/index.php'))->out(false);
        $myreviewsurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']))->out(false);

        // Common breadcrumbs.
        $breadcrumbs = [
            [
                'label' => get_string('dashboard', 'local_jobboard'),
                'url' => $dashboardurl,
            ],
            [
                'label' => get_string('reviewdocuments', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            ],
        ];

        // Base data structure.
        $data = [
            'pagetitle' => get_string('reviewdocuments', 'local_jobboard'),
            'breadcrumbs' => $breadcrumbs,
            'dashboardurl' => $dashboardurl,
            'myreviewsurl' => $myreviewsurl,
            'selectedapplication' => null,
            'hasapplications' => false,
            'applications' => [],
            'stats' => [],
            'filterform' => null,
            'pagination' => null,
            'headeractions' => [
                [
                    'url' => $myreviewsurl,
                    'label' => get_string('myreviews', 'local_jobboard'),
                    'icon' => 'clipboard-list',
                    'class' => 'outline-primary',
                ],
            ],
        ];

        // Single application review mode.
        if ($selectedapp && $vacancy && $applicant) {
            return $this->prepare_review_single_application_data(
                $data,
                $params,
                $selectedapp,
                $vacancy,
                $applicant,
                $documents,
                $navdata
            );
        }

        // List mode - applications pending review.
        return $this->prepare_review_list_data(
            $data,
            $params,
            $total,
            $applications,
            $queuestats,
            $context
        );
    }

    /**
     * Prepare bulk validation page data for template.
     *
     * @param int $vacancyid Vacancy filter ID (0 for all).
     * @param string $documenttype Document type filter.
     * @param \context $context Page context for capability checks.
     * @return array Template data.
     */
    public function prepare_bulk_validate_page_data(int $vacancyid, string $documenttype, \context $context): array {
        global $DB;

        $pageurl = new moodle_url('/local/jobboard/admin/bulk_validate.php');

        // Get vacancy list for filter.
        $vacancies = $DB->get_records_select('local_jobboard_vacancy',
            "status IN ('published', 'closed')", null, 'code ASC', 'id, code, title');

        $vacanciesdata = [];
        foreach ($vacancies as $v) {
            $vacanciesdata[] = [
                'id' => $v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($v->id == $vacancyid),
            ];
        }

        // Get pending documents by type.
        $pendingbytype = \local_jobboard\bulk_validator::get_pending_by_type($vacancyid ?: null);

        // Calculate stats.
        $totalPending = 0;
        $typeCount = count($pendingbytype);
        foreach ($pendingbytype as $dt) {
            $totalPending += $dt->count;
        }

        // Prepare pending by type data.
        $pendingtypedata = [];
        $documenttypesdata = [];
        foreach ($pendingbytype as $dt) {
            $typename = get_string('doctype_' . $dt->documenttype, 'local_jobboard');
            $isSelected = ($documenttype === $dt->documenttype);

            $pendingtypedata[] = [
                'documenttype' => $dt->documenttype,
                'typename' => $typename,
                'count' => (int) $dt->count,
                'isselected' => $isSelected,
                'selecturl' => (new moodle_url($pageurl, [
                    'vacancyid' => $vacancyid,
                    'documenttype' => $dt->documenttype,
                ]))->out(false),
            ];

            $documenttypesdata[] = [
                'code' => $dt->documenttype,
                'label' => $typename . ' (' . $dt->count . ')',
                'selected' => $isSelected,
            ];
        }

        // Rejection reasons.
        $reasons = ['illegible', 'expired', 'incomplete', 'wrongtype', 'mismatch'];
        $rejectreasons = [];
        foreach ($reasons as $reason) {
            $rejectreasons[] = [
                'code' => $reason,
                'label' => get_string('rejectreason_' . $reason, 'local_jobboard'),
            ];
        }

        // Base data.
        $data = [
            'pagetitle' => get_string('bulkvalidation', 'local_jobboard'),
            'stats' => [
                'totalpending' => $totalPending,
                'typecount' => $typeCount,
            ],
            'vacancies' => $vacanciesdata,
            'selectedvacancyid' => $vacancyid,
            'documenttypes' => $documenttypesdata,
            'selecteddocumenttype' => $documenttype,
            'pendingbytype' => $pendingtypedata,
            'haspendingbytype' => !empty($pendingtypedata),
            'rejectreasons' => $rejectreasons,
            'filterformurl' => $pageurl->out(false),
            'actionformurl' => $pageurl->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            'assignreviewerurl' => (new moodle_url('/local/jobboard/admin/assign_reviewer.php'))->out(false),
            'reportsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'reports']))->out(false),
            'canviewreports' => has_capability('local/jobboard:viewreports', $context),
            'sesskey' => sesskey(),
            'hastypeselected' => !empty($documenttype),
        ];

        // If document type selected, get the documents.
        if (!empty($documenttype)) {
            $documents = \local_jobboard\bulk_validator::get_pending_documents_by_type($documenttype, $vacancyid ?: null);
            $typename = get_string('doctype_' . $documenttype, 'local_jobboard');

            $documentsdata = [];
            foreach ($documents as $doc) {
                $docobj = \local_jobboard\document::get((int) $doc->id);
                $downloadurl = $docobj ? $docobj->get_download_url() : null;

                $documentsdata[] = [
                    'id' => $doc->id,
                    'applicantname' => format_string($doc->firstname . ' ' . $doc->lastname),
                    'applicantemail' => $doc->email,
                    'vacancycode' => format_string($doc->vacancy_code),
                    'filename' => format_string($doc->filename),
                    'uploadeddate' => userdate($doc->timecreated, '%Y-%m-%d %H:%M'),
                    'downloadurl' => $downloadurl ? $downloadurl->out(false) : null,
                    'validateurl' => (new moodle_url('/local/jobboard/admin/validate_document.php', ['id' => $doc->id]))->out(false),
                ];
            }

            $data['typename'] = $typename;
            $data['documents'] = $documentsdata;
            $data['hasdocuments'] = !empty($documentsdata);
            $data['documentcount'] = count($documentsdata);
        }

        return $data;
    }

    /**
     * Prepare document validation page data for template.
     *
     * @param \local_jobboard\document $document The document to validate.
     * @param \local_jobboard\application $application The related application.
     * @return array Template data.
     */
    public function prepare_validate_document_page_data(
        \local_jobboard\document $document,
        \local_jobboard\application $application
    ): array {
        global $DB;

        // Get applicant user.
        $applicant = $DB->get_record('user', ['id' => $application->userid]);
        if (!$applicant) {
            throw new \moodle_exception('error:usernotfound', 'local_jobboard');
        }

        // Get document type name.
        $typename = get_string('doctype_' . $document->documenttype, 'local_jobboard');

        // Prepare document data.
        $documentdata = [
            'id' => $document->id,
            'type' => $document->documenttype,
            'typename' => $typename,
            'filename' => format_string($document->filename),
            'uploadeddate' => userdate($document->timecreated, get_string('strftimedatetime', 'langconfig')),
            'issuedate' => !empty($document->issuedate) ?
                userdate($document->issuedate, get_string('strftimedate', 'langconfig')) : null,
        ];

        // Get preview info.
        $downloadurl = $document->get_download_url();
        $previewinfo = \local_jobboard\document_services::get_preview_info($document);

        $previewurl = $previewinfo['url'] ?: ($downloadurl ? $downloadurl->out(false) : null);
        $previewmime = $previewinfo['mimetype'];

        // Determine preview capabilities.
        $canpreview = ($previewinfo['status'] === 'ready' && $previewinfo['url']);
        $directpreview = in_array($document->mimetype, \local_jobboard\document_services::DIRECT_PREVIEW_MIMETYPES);
        $showpreview = $canpreview || $directpreview;

        $ispdf = ($previewmime === 'application/pdf' && $previewinfo['url']);
        $isimage = (strpos($document->mimetype, 'image/') === 0);

        // Get status color.
        $statuscolor = 'secondary';
        if ($previewinfo['status'] === 'ready') {
            $statuscolor = 'success';
        } else if ($previewinfo['status'] === 'converting') {
            $statuscolor = 'info';
        } else if ($previewinfo['status'] === 'failed') {
            $statuscolor = 'danger';
        }

        $previewdata = [
            'downloadurl' => $downloadurl ? $downloadurl->out(false) : null,
            'previewurl' => $previewurl,
            'mimetype' => $previewmime,
            'canpreview' => $showpreview,
            'isconverting' => ($previewinfo['status'] === 'converting'),
            'isfailed' => ($previewinfo['can_convert'] && $previewinfo['status'] === 'failed'),
            'ispdf' => $ispdf,
            'isimage' => $isimage,
            'showstatus' => $previewinfo['can_convert'],
            'statuscolor' => $statuscolor,
            'statusmessage' => \local_jobboard\document_services::get_status_message($previewinfo['status']),
        ];

        // Prepare applicant data.
        $applicantdata = [
            'fullname' => fullname($applicant),
            'email' => $applicant->email,
            'idnumber' => !empty($applicant->idnumber) ? format_string($applicant->idnumber) : null,
        ];

        // Get validation checklist.
        $checklistdata = [];
        $checklistitems = $this->get_validation_checklist($document->documenttype);
        $idx = 0;
        foreach ($checklistitems as $item) {
            $checklistdata[] = [
                'id' => $idx++,
                'text' => $item,
            ];
        }

        // Rejection reasons.
        $reasons = [
            'illegible' => get_string('rejectreason_illegible', 'local_jobboard'),
            'expired' => get_string('rejectreason_expired', 'local_jobboard'),
            'incomplete' => get_string('rejectreason_incomplete', 'local_jobboard'),
            'wrongtype' => get_string('rejectreason_wrongtype', 'local_jobboard'),
            'mismatch' => get_string('rejectreason_mismatch', 'local_jobboard'),
            'other' => get_string('other'),
        ];
        $rejectreasons = [];
        foreach ($reasons as $code => $label) {
            $rejectreasons[] = [
                'code' => $code,
                'label' => $label,
            ];
        }

        // Build URLs.
        $baseurl = new moodle_url('/local/jobboard/admin/validate_document.php', ['id' => $document->id]);
        $backurl = new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]);

        return [
            'pagetitle' => get_string('validatedocument', 'local_jobboard'),
            'document' => $documentdata,
            'applicant' => $applicantdata,
            'application' => ['id' => $application->id],
            'preview' => $previewdata,
            'checklist' => $checklistdata,
            'haschecklist' => !empty($checklistdata),
            'rejectreasons' => $rejectreasons,
            'approveformurl' => (new moodle_url($baseurl, ['action' => 'validate']))->out(false),
            'rejectformurl' => (new moodle_url($baseurl, ['action' => 'reject']))->out(false),
            'backurl' => $backurl->out(false),
            'sesskey' => sesskey(),
        ];
    }

    /**
     * Prepare reupload document page data.
     *
     * @param string $formhtml Rendered form HTML.
     * @param string|null $documentname Original document name.
     * @param string|null $documenttype Document type label.
     * @param string|null $rejectreason Reason for rejection.
     * @param string|null $backurl Back URL.
     * @return array Template data.
     */
    public function prepare_reupload_document_data(
        string $formhtml,
        ?string $documentname = null,
        ?string $documenttype = null,
        ?string $rejectreason = null,
        ?string $backurl = null
    ): array {
        return [
            'pagetitle' => get_string('reuploaddocument', 'local_jobboard'),
            'helptext' => get_string('reuploadhelp', 'local_jobboard'),
            'formhtml' => $formhtml,
            'documentname' => $documentname,
            'documenttype' => $documenttype,
            'rejectreason' => $rejectreason,
            'backurl' => $backurl,
        ];
    }

    /**
     * Prepare review list page data.
     *
     * @param array $data Base template data.
     * @param array $params View parameters.
     * @param int $total Total applications count.
     * @param array $applications Applications array.
     * @param array $queuestats Queue statistics.
     * @param \context $context Current context.
     * @return array Template data.
     */
    protected function prepare_review_list_data(
        array $data,
        array $params,
        int $total,
        array $applications,
        array $queuestats,
        \context $context
    ): array {
        global $DB, $OUTPUT;

        $vacancyid = $params['vacancyid'] ?? 0;
        $page = $params['page'] ?? 0;
        $perpage = $params['perpage'] ?? 20;
        $code = $params['code'] ?? '';
        $contracttype = $params['contracttype'] ?? '';
        $department = $params['department'] ?? '';
        $location = $params['location'] ?? '';
        $search = $params['search'] ?? '';
        $datefrom = $params['datefrom'] ?? '';
        $dateto = $params['dateto'] ?? '';

        // Stats cards.
        $data['stats'] = [
            [
                'value' => (string) ($queuestats['total'] ?? 0),
                'label' => get_string('pendingreview', 'local_jobboard'),
                'color' => 'primary',
                'icon' => 'file-alt',
            ],
            [
                'value' => (string) ($queuestats['pending'] ?? 0),
                'label' => get_string('pendingdocuments', 'local_jobboard'),
                'color' => 'warning',
                'icon' => 'clock',
            ],
            [
                'value' => (string) ($queuestats['urgent'] ?? 0),
                'label' => get_string('urgent', 'local_jobboard'),
                'color' => 'danger',
                'icon' => 'exclamation-triangle',
            ],
        ];

        // Filter form - matching public page style.
        $statusfilter = $params['statusfilter'] ?? '';

        // Status filter options - include ALL statuses.
        $statusoptions = [
            ['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($statusfilter)],
            ['value' => 'draft', 'label' => get_string('status_draft', 'local_jobboard'), 'selected' => ($statusfilter === 'draft')],
            ['value' => 'submitted', 'label' => get_string('status_submitted', 'local_jobboard'), 'selected' => ($statusfilter === 'submitted')],
            ['value' => 'under_review', 'label' => get_string('status_under_review', 'local_jobboard'), 'selected' => ($statusfilter === 'under_review')],
            ['value' => 'docs_rejected', 'label' => get_string('status_docs_rejected', 'local_jobboard'), 'selected' => ($statusfilter === 'docs_rejected')],
            ['value' => 'docs_validated', 'label' => get_string('status_docs_validated', 'local_jobboard'), 'selected' => ($statusfilter === 'docs_validated')],
            ['value' => 'interview', 'label' => get_string('status_interview', 'local_jobboard'), 'selected' => ($statusfilter === 'interview')],
            ['value' => 'selected', 'label' => get_string('status_selected', 'local_jobboard'), 'selected' => ($statusfilter === 'selected')],
            ['value' => 'rejected', 'label' => get_string('status_rejected', 'local_jobboard'), 'selected' => ($statusfilter === 'rejected')],
            ['value' => 'withdrawn', 'label' => get_string('status_withdrawn', 'local_jobboard'), 'selected' => ($statusfilter === 'withdrawn')],
        ];

        // Build filter options from all vacancies (like public page does) - include ALL statuses.
        $vacancies = $DB->get_records_sql(
            "SELECT DISTINCT v.* FROM {local_jobboard_vacancy} v
             JOIN {local_jobboard_application} a ON a.vacancyid = v.id
             ORDER BY v.code ASC"
        );

        // Contract types.
        $contracttypes = \local_jobboard_get_contract_types();
        $contracttypesList = [];
        $departmentsList = [];
        $locationsList = [];

        foreach ($vacancies as $v) {
            // Contract types (Tipo de Vinculación).
            if (!empty($v->contracttype) && !isset($contracttypesList[$v->contracttype])) {
                $contracttypesList[$v->contracttype] = $contracttypes[$v->contracttype] ?? $v->contracttype;
            }
            // Departments (Programa académico).
            if (!empty($v->department) && !isset($departmentsList[$v->department])) {
                $departmentsList[$v->department] = $v->department;
            }
            // Locations (Ubicación).
            if (!empty($v->location) && !isset($locationsList[$v->location])) {
                $locationsList[$v->location] = $v->location;
            }
        }

        // Sort options alphabetically.
        asort($contracttypesList);
        asort($departmentsList);
        asort($locationsList);

        // Build filter options arrays.
        $contractoptions = [];
        foreach ($contracttypesList as $key => $label) {
            $contractoptions[] = ['value' => $key, 'label' => $label, 'selected' => ($contracttype === $key)];
        }

        $departmentoptions = [];
        foreach ($departmentsList as $key => $label) {
            $departmentoptions[] = ['value' => $key, 'label' => $label, 'selected' => ($department === $key)];
        }

        $locationoptions = [];
        foreach ($locationsList as $key => $label) {
            $locationoptions[] = ['value' => $key, 'label' => $label, 'selected' => ($location === $key)];
        }

        $hasfilters = !empty($statusfilter) || !empty($code) || !empty($contracttype) ||
                      !empty($department) || !empty($location) || !empty($search) ||
                      !empty($datefrom) || !empty($dateto);

        // Filter form - public page style.
        $data['filterform'] = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [
                ['name' => 'view', 'value' => 'review'],
            ],
            // Filter values.
            'codevalue' => $code,
            'searchvalue' => $search,
            'datefromvalue' => $datefrom,
            'datetovalue' => $dateto,
            // Status filter (specific to review page).
            'statusoptions' => $statusoptions,
            // Filter options (matching public page).
            'showcontractfilter' => !empty($contractoptions),
            'contractoptions' => $contractoptions,
            'showdepartmentfilter' => !empty($departmentoptions),
            'departmentoptions' => $departmentoptions,
            'showlocationfilter' => !empty($locationoptions),
            'locationoptions' => $locationoptions,
            // Filter state.
            'hasfilters' => $hasfilters,
            'clearfiltersurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
        ];

        // Per-page options - add to filterform for template access.
        $perpageoptions = [];
        foreach ([10, 20, 50, 100] as $opt) {
            $perpageoptions[] = [
                'value' => $opt,
                'label' => $opt,
                'selected' => $perpage == $opt,
            ];
        }
        $data['filterform']['perpageoptions'] = $perpageoptions;
        $data['perpageoptions'] = $perpageoptions;

        // Applications list.
        $appsdata = [];
        foreach ($applications as $app) {
            $isurgent = !empty($app->closedate) && ($app->closedate - time()) <= 7 * 86400;
            $haspendingdocs = (int) ($app->pendingcount ?? 0) > 0;

            // Status color for visual distinction.
            $statuscolor = 'secondary';
            if (in_array($app->status, ['docs_validated', 'selected'])) {
                $statuscolor = 'success';
            } else if (in_array($app->status, ['docs_rejected', 'rejected'])) {
                $statuscolor = 'danger';
            } else if (in_array($app->status, ['under_review', 'interview'])) {
                $statuscolor = 'warning';
            } else if ($app->status === 'submitted') {
                $statuscolor = 'info';
            } else if ($app->status === 'withdrawn') {
                $statuscolor = 'dark';
            } else if ($app->status === 'draft') {
                $statuscolor = 'secondary';
            }

            $appsdata[] = [
                'id' => $app->id,
                'userid' => $app->userid,
                'applicantname' => fullname($app),
                'profileurl' => (new moodle_url('/user/profile.php', ['id' => $app->userid]))->out(false),
                'email' => $app->email,
                'idnumber' => format_string($app->idnumber ?? ''),
                'vacancycode' => format_string($app->vacancy_code ?? ''),
                'vacancytitle' => format_string($app->vacancy_title ?? ''),
                'vacancydepartment' => format_string($app->vacancy_department ?? ''),
                'status' => $app->status,
                'statuslabel' => get_string('status_' . $app->status, 'local_jobboard'),
                'statuscolor' => $statuscolor,
                'doccount' => (int) ($app->doccount ?? 0),
                'pendingcount' => (int) ($app->pendingcount ?? 0),
                'datesubmitted' => userdate($app->timecreated, get_string('strftimedatetime', 'langconfig')),
                'datesubmittedshort' => userdate($app->timecreated, '%Y-%m-%d'),
                'isurgent' => $isurgent,
                'haspendingdocs' => $haspendingdocs,
                'isactionable' => in_array($app->status, ['submitted', 'under_review', 'docs_rejected']),
                'reviewurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $app->id,
                ]))->out(false),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'application',
                    'id' => $app->id,
                ]))->out(false),
                'downloadzipurl' => (new moodle_url('/local/jobboard/download_documents.php', [
                    'applicationid' => $app->id,
                    'sesskey' => sesskey(),
                ]))->out(false),
            ];
        }

        $data['hasapplications'] = !empty($appsdata);
        $data['applications'] = $appsdata;
        $data['applicationcount'] = $total;

        // Pagination with all filter parameters.
        $paginationparams = array_filter([
            'view' => 'review',
            'status' => $statusfilter,
            'code' => $code,
            'contracttype' => $contracttype,
            'department' => $department,
            'location' => $location,
            'search' => $search,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
            'perpage' => $perpage,
        ]);
        if ($total > $perpage) {
            $baseurl = new moodle_url('/local/jobboard/index.php', $paginationparams);
            $data['pagination'] = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        // Bulk download URL (for selected applications).
        $data['bulkdownloadurl'] = (new moodle_url('/local/jobboard/download_documents.php', [
            'bulk' => 1,
            'sesskey' => sesskey(),
        ]))->out(false);

        // Session key for bulk actions.
        $data['sesskey'] = sesskey();

        // Current filter values for AJAX.
        $data['currentfilters'] = [
            'status' => $statusfilter,
            'code' => $code,
            'contracttype' => $contracttype,
            'department' => $department,
            'location' => $location,
            'search' => $search,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
            'page' => $page,
            'perpage' => $perpage,
        ];

        return $data;
    }

    /**
     * Prepare single application review data.
     *
     * @param array $data Base template data.
     * @param array $params View parameters.
     * @param object $application Application object.
     * @param object $vacancy Vacancy object.
     * @param object $applicant User object.
     * @param array $documents Documents array.
     * @param array $navdata Navigation data.
     * @return array Template data.
     */
    protected function prepare_review_single_application_data(
        array $data,
        array $params,
        object $application,
        object $vacancy,
        object $applicant,
        array $documents,
        array $navdata
    ): array {
        global $DB, $PAGE;

        $vacancyid = $params['vacancyid'] ?? 0;
        $applicationid = $application->id;

        // Initialize TinyMCE editor for document observations.
        // Get the preferred editor (TinyMCE in Moodle 4.x).
        $editor = editors_get_preferred_editor(FORMAT_HTML);

        // Update page title.
        $data['pagetitle'] = get_string('reviewapplication', 'local_jobboard');

        // Update breadcrumbs.
        $data['breadcrumbs'][] = [
            'label' => fullname($applicant),
            'url' => null,
            'active' => true,
        ];

        // Header actions for single view.
        $data['headeractions'] = [
            [
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancyid]))->out(false),
                'label' => get_string('back'),
                'icon' => 'arrow-left',
                'class' => 'outline-secondary',
            ],
        ];

        // Count document statuses.
        $docstats = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($documents as $doc) {
            $status = $doc->status ?? 'pending';
            if (isset($docstats[$status])) {
                $docstats[$status]++;
            }
        }
        $totaldocs = count($documents);

        // Navigation.
        $previd = $navdata['previd'] ?? null;
        $nextid = $navdata['nextid'] ?? null;
        $navposition = $navdata['navposition'] ?? 0;
        $navtotal = $navdata['navtotal'] ?? 0;

        $shownav = $navtotal > 1;
        $prevurl = $previd ? (new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $previd,
            'vacancyid' => $vacancyid,
        ]))->out(false) : null;

        $nexturl = $nextid ? (new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $nextid,
            'vacancyid' => $vacancyid,
        ]))->out(false) : null;

        // Get text documents from applicationdata (e.g., carta_intencion).
        $textdocuments = [];
        if (!empty($application->applicationdata)) {
            $appdata = json_decode($application->applicationdata, true);
            if (!empty($appdata['text_documents'])) {
                $textdocuments = $appdata['text_documents'];
            }
        }

        // Fallback: if carta_intencion not in text_documents, check coverletter field.
        if (empty($textdocuments['carta_intencion']) && !empty($application->coverletter)) {
            $textdocuments['carta_intencion'] = [
                'type' => 'textarea',
                'value' => $application->coverletter,
            ];
        }

        // Ensure text documents have document records for proper validation workflow.
        // Create records for any text documents that don't have them.
        $docrecordsneeded = false;
        foreach ($textdocuments as $doccode => $textdoc) {
            $hasrecord = false;
            foreach ($documents as $doc) {
                if ($doc->documenttype === $doccode && $doc->mimetype === 'text/plain') {
                    $hasrecord = true;
                    break;
                }
            }
            if (!$hasrecord && !empty($textdoc['value'])) {
                // Create document record for this text document.
                try {
                    \local_jobboard\document::create_text_document(
                        $applicationid,
                        $doccode,
                        $textdoc['value']
                    );
                    $docrecordsneeded = true;
                } catch (\Exception $e) {
                    debugging('Failed to create text document record for ' . $doccode . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            }
        }

        // Reload documents if new records were created.
        if ($docrecordsneeded) {
            $documents = \local_jobboard\document::get_by_application($applicationid);
        }

        // Check user roles and document validation permissions.
        $bypasssequentialreview = $params['bypass_sequential_review'] ?? false;
        $isadmin = $params['is_admin'] ?? false;
        $isdean = $params['is_dean'] ?? false;
        $ishr = $params['is_hr'] ?? false;
        // Only HR and Admin can validate/reject individual documents.
        // Dean can only approve/reject the full application.
        $canvalidatedocuments = $params['can_validate_documents'] ?? false;

        // Prepare documents with preview info.
        // Sequential review: find the first pending document (current) and mark others as locked.
        // Admin and Dean bypass sequential review. HR must review sequentially.
        $docsdata = [];
        $currentdocid = null;
        $currentdocindex = null;
        $docindex = 0;

        // First pass: find the current document (first pending one).
        // For admins, we don't enforce sequential order.
        foreach ($documents as $doc) {
            $status = $doc->status ?? 'pending';
            if ($status === 'pending' && $currentdocid === null) {
                $currentdocid = $doc->id;
                $currentdocindex = $docindex;
            }
            $docindex++;
        }

        // Second pass: build document data with sequential review flags.
        $docindex = 0;
        foreach ($documents as $doc) {
            $status = $doc->status ?? 'pending';
            $statusconfig = [
                'pending' => ['icon' => 'clock', 'color' => 'warning'],
                'approved' => ['icon' => 'check-circle', 'color' => 'success'],
                'rejected' => ['icon' => 'times-circle', 'color' => 'danger'],
            ];
            $config = $statusconfig[$status] ?? $statusconfig['pending'];

            $downloadurl = null;
            $previewurlobj = null;
            if (method_exists($doc, 'get_download_url')) {
                $downloadurl = $doc->get_download_url(true);  // Force download.
                $previewurlobj = $doc->get_download_url(false);  // Inline view for preview.
            }

            $typename = method_exists($doc, 'get_doctype_name') ? $doc->get_doctype_name() : ($doc->typename ?? 'Document');

            $reviewedby = null;
            $reviewedat = null;
            if (!empty($doc->reviewerid) && $status !== 'pending') {
                $reviewer = $DB->get_record('user', ['id' => $doc->reviewerid]);
                if ($reviewer) {
                    $reviewedby = fullname($reviewer);
                    $reviewedat = !empty($doc->reviewedat) ? userdate($doc->reviewedat, get_string('strftimedatetime', 'langconfig')) : '';
                }
            }

            // Get preview info for document viewer (use inline URL, not download).
            $previewurl = $previewurlobj ? $previewurlobj->out(false) : null;
            $mimetype = $doc->mimetype ?? 'application/octet-stream';
            $ispdf = ($mimetype === 'application/pdf');
            $isimage = (strpos($mimetype, 'image/') === 0);

            // Check if this is a text document (stored with mimetype 'text/plain' or 'text/html').
            $istext = in_array($mimetype, ['text/plain', 'text/html']);
            $textcontent = null;
            if ($istext && !empty($textdocuments[$doc->documenttype])) {
                $textcontent = $textdocuments[$doc->documenttype]['value'] ?? null;
                // Remove from textdocuments so we don't add it twice.
                unset($textdocuments[$doc->documenttype]);
            }
            $canpreview = ($ispdf || $isimage || $istext);

            // Get existing observation for this document.
            $observation = $doc->observation ?? '';

            // Sequential review flags.
            // Admins bypass sequential review - all pending docs are accessible.
            if ($bypasssequentialreview) {
                // For admins: all pending documents are "current" (can be reviewed).
                $iscurrent = ($status === 'pending');
                $islocked = false;  // Never locked for admins.
            } else {
                // For regular users: only the first pending document is current.
                $iscurrent = ($doc->id == $currentdocid);
                $islocked = ($status === 'pending' && !$iscurrent);
            }
            $isreviewed = ($status !== 'pending');

            // Initialize editor for documents that can be reviewed.
            if ($iscurrent) {
                $editorid = 'doc_observation_' . $doc->id;
                $editoroptions = [
                    'context' => $PAGE->context,
                    'maxfiles' => 0,
                    'maxbytes' => 0,
                ];
                $editor->use_editor($editorid, $editoroptions);
            }

            // For text content, base64 encode for safe storage in data attribute.
            $textcontentencoded = !empty($textcontent) ? base64_encode($textcontent) : null;

            $docsdata[] = [
                'id' => $doc->id,
                'applicationid' => $applicationid,
                'typename' => format_string($typename),
                'filename' => format_string($doc->filename ?? ''),
                'status' => $status,
                'statuslabel' => get_string('docstatus:' . $status, 'local_jobboard'),
                'statusicon' => $config['icon'],
                'statuscolor' => $config['color'],
                'ispending' => ($status === 'pending'),
                'downloadurl' => $downloadurl ? $downloadurl->out(false) : null,
                'previewurl' => $previewurl,
                'haspreviewurl' => !empty($previewurl),
                'mimetype' => $mimetype,
                'ispdf' => $ispdf,
                'isimage' => $isimage,
                'istext' => $istext,
                'textcontent' => $textcontent,
                'textcontentencoded' => $textcontentencoded,
                'hastextcontent' => !empty($textcontent),
                'canpreview' => $canpreview,
                'validateurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $applicationid,
                    'documentid' => $doc->id,
                    'action' => 'validate',
                    'sesskey' => sesskey(),
                ]))->out(false),
                // Reject URL with all params - reason will be added via form POST.
                'rejecturl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $applicationid,
                    'documentid' => $doc->id,
                    'action' => 'reject',
                    'sesskey' => sesskey(),
                ]))->out(false),
                'rejectreason' => $doc->validation_reason ?? null,
                'reviewedby' => $reviewedby,
                'reviewedat' => $reviewedat,
                'sesskey' => sesskey(),
                'observation' => $observation,
                // Sequential review fields.
                'docindex' => $docindex + 1,
                'iscurrent' => $iscurrent,
                'islocked' => $islocked,
                'isreviewed' => $isreviewed,
                // Document validation permission - only HR and Admin can validate/reject docs.
                // Dean can view all docs but cannot validate/reject individual documents.
                'canvalidatedocuments' => $canvalidatedocuments,
                // Show action buttons only if current AND user can validate documents.
                'showactions' => $iscurrent && $canvalidatedocuments,
            ];

            $docindex++;
        }

        // Note: Text documents now have proper document records created earlier,
        // so they're included in the main documents loop above with proper validation workflow.

        // Get current document for initial preview (the one being reviewed).
        // If no pending document, show the first document in the list.
        $initialpreview = null;
        if (!empty($docsdata)) {
            if ($currentdocid !== null) {
                // Find the current pending document.
                foreach ($docsdata as $docdata) {
                    if ($docdata['id'] == $currentdocid) {
                        $initialpreview = $docdata;
                        break;
                    }
                }
            }
            // If no current document found (all reviewed), show the first document.
            if ($initialpreview === null) {
                $initialpreview = reset($docsdata);
            }
        }

        // Calculate current document position for progress display.
        $currentdocposition = $currentdocindex !== null ? $currentdocindex + 1 : count($docsdata) + 1;

        // Application status class.
        $appstatusclass = 'secondary';
        $appstatus = $application->status ?? 'draft';
        if (in_array($appstatus, ['docs_validated', 'selected'])) {
            $appstatusclass = 'success';
        } else if (in_array($appstatus, ['docs_rejected', 'rejected'])) {
            $appstatusclass = 'danger';
        } else if ($appstatus === 'under_review') {
            $appstatusclass = 'warning';
        } else if ($appstatus === 'submitted') {
            $appstatusclass = 'info';
        }

        // Progress percentages.
        $approvedpercent = $totaldocs > 0 ? round(($docstats['approved'] / $totaldocs) * 100) : 0;
        $rejectedpercent = $totaldocs > 0 ? round(($docstats['rejected'] / $totaldocs) * 100) : 0;

        // All reviewed?
        $allreviewed = ($docstats['pending'] === 0 && $totaldocs > 0);
        $haspending = ($docstats['pending'] > 0);

        // Complete button color.
        $completebtncolor = ($docstats['rejected'] > 0) ? 'warning' : 'success';

        // Stats for single application.
        $data['stats'] = [
            [
                'value' => (string) $totaldocs,
                'label' => get_string('documents', 'local_jobboard'),
                'color' => 'primary',
                'icon' => 'file-alt',
            ],
            [
                'value' => (string) $docstats['approved'],
                'label' => get_string('docstatus:approved', 'local_jobboard'),
                'color' => 'success',
                'icon' => 'check-circle',
            ],
            [
                'value' => (string) $docstats['rejected'],
                'label' => get_string('docstatus:rejected', 'local_jobboard'),
                'color' => 'danger',
                'icon' => 'times-circle',
            ],
            [
                'value' => (string) $docstats['pending'],
                'label' => get_string('docstatus:pending', 'local_jobboard'),
                'color' => 'warning',
                'icon' => 'clock',
            ],
        ];

        $data['selectedapplication'] = [
            'id' => $applicationid,
            'applicantname' => fullname($applicant),
            'profileurl' => (new moodle_url('/user/profile.php', ['id' => $applicant->id]))->out(false),
            'email' => $applicant->email,
            'dateapplied' => userdate($application->timecreated, get_string('strftimedate', 'langconfig')),
            'vacancycode' => format_string($vacancy->code ?? ''),
            'vacancytitle' => format_string($vacancy->title ?? ''),
            'status' => $appstatus,
            'statuslabel' => get_string('status_' . $appstatus, 'local_jobboard'),
            'statuscolor' => $appstatusclass,
        ];

        $data['hasdocuments'] = !empty($docsdata);
        $data['documents'] = $docsdata;
        $data['initialpreview'] = $initialpreview;
        // hasinitialpreview is true if we have a previewurl OR if it's a text document with content.
        $data['hasinitialpreview'] = ($initialpreview !== null &&
            (!empty($initialpreview['previewurl']) || !empty($initialpreview['hastextcontent'])));

        $data['shownav'] = $shownav;
        $data['prevurl'] = $prevurl;
        $data['nexturl'] = $nexturl;
        $data['navposition'] = $navposition;
        $data['navtotal'] = $navtotal;

        $data['approvedcount'] = $docstats['approved'];
        $data['rejectedcount'] = $docstats['rejected'];
        $data['pendingcount'] = $docstats['pending'];
        $data['approvedpercent'] = $approvedpercent;
        $data['rejectedpercent'] = $rejectedpercent;

        $data['allreviewed'] = $allreviewed;
        $data['haspending'] = $haspending;
        $data['completebtncolor'] = $completebtncolor;

        // Sequential review progress.
        $data['currentdocposition'] = $currentdocposition;
        $data['totaldocs'] = $totaldocs;
        $data['hascurrentdoc'] = ($currentdocid !== null);

        // Role flags for template.
        $data['is_admin'] = $isadmin;
        $data['is_dean'] = $isdean;
        $data['is_hr'] = $ishr;
        $data['bypass_sequential_review'] = $bypasssequentialreview;
        // Only HR and Admin can validate/reject individual documents.
        // Dean can see all docs but cannot validate/reject them.
        $data['can_validate_documents'] = $canvalidatedocuments;

        // Only HR and Admin can use "validate all" (not Dean).
        $data['canvalidateall'] = $canvalidatedocuments && $bypasssequentialreview;
        $data['validateallurl'] = ($canvalidatedocuments && $bypasssequentialreview)
            ? (new moodle_url('/local/jobboard/index.php', [
                'view' => 'review',
                'applicationid' => $applicationid,
                'action' => 'validateall',
                'sesskey' => sesskey(),
            ]))->out(false)
            : '';

        $data['submitreviewurl'] = (new moodle_url('/local/jobboard/index.php'))->out(false);
        $data['sesskey'] = sesskey();

        $data['backurl'] = (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancyid]))->out(false);
        $data['downloadzipurl'] = (new moodle_url('/local/jobboard/download_documents.php', ['applicationid' => $applicationid, 'sesskey' => sesskey()]))->out(false);

        return $data;
    }

    /**
     * Get validation checklist items for a document type.
     *
     * @param string $doctype Document type code.
     * @return array Checklist items.
     */
    protected function get_validation_checklist(string $doctype): array {
        $common = [
            get_string('checklist_legible', 'local_jobboard'),
            get_string('checklist_complete', 'local_jobboard'),
            get_string('checklist_namematch', 'local_jobboard'),
        ];

        $specific = [];
        switch ($doctype) {
            case 'cedula':
                $specific = [
                    get_string('checklist_cedula_number', 'local_jobboard'),
                    get_string('checklist_cedula_photo', 'local_jobboard'),
                ];
                break;
            case 'antecedentes_procuraduria':
            case 'antecedentes_contraloria':
            case 'antecedentes_policia':
            case 'rnmc':
            case 'sijin':
                $specific = [
                    get_string('checklist_background_date', 'local_jobboard'),
                    get_string('checklist_background_status', 'local_jobboard'),
                ];
                break;
            case 'titulo_pregrado':
            case 'titulo_postgrado':
            case 'titulo_especializacion':
            case 'titulo_maestria':
            case 'titulo_doctorado':
                $specific = [
                    get_string('checklist_title_institution', 'local_jobboard'),
                    get_string('checklist_title_date', 'local_jobboard'),
                    get_string('checklist_title_program', 'local_jobboard'),
                ];
                break;
            case 'acta_grado':
                $specific = [
                    get_string('checklist_acta_number', 'local_jobboard'),
                    get_string('checklist_acta_date', 'local_jobboard'),
                ];
                break;
            case 'tarjeta_profesional':
                $specific = [
                    get_string('checklist_tarjeta_number', 'local_jobboard'),
                    get_string('checklist_tarjeta_profession', 'local_jobboard'),
                ];
                break;
            case 'rut':
                $specific = [
                    get_string('checklist_rut_nit', 'local_jobboard'),
                    get_string('checklist_rut_updated', 'local_jobboard'),
                ];
                break;
            case 'eps':
                $specific = [
                    get_string('checklist_eps_active', 'local_jobboard'),
                    get_string('checklist_eps_entity', 'local_jobboard'),
                ];
                break;
            case 'pension':
                $specific = [
                    get_string('checklist_pension_fund', 'local_jobboard'),
                    get_string('checklist_pension_active', 'local_jobboard'),
                ];
                break;
            case 'certificado_medico':
                $specific = [
                    get_string('checklist_medical_date', 'local_jobboard'),
                    get_string('checklist_medical_aptitude', 'local_jobboard'),
                ];
                break;
            case 'libreta_militar':
                $specific = [
                    get_string('checklist_military_class', 'local_jobboard'),
                    get_string('checklist_military_number', 'local_jobboard'),
                ];
                break;
        }

        return array_merge($common, $specific);
    }

    /**
     * Prepare data for assign reviewer page.
     *
     * @param int $vacancyid Optional vacancy ID to filter by.
     * @return array Template data.
     */
    public function prepare_assign_reviewer_page_data(int $vacancyid = 0): array {
        global $DB;

        // Get statistics.
        $unassignedcount = \local_jobboard\reviewer::count_unassigned($vacancyid ?: null);
        $reviewers = \local_jobboard\reviewer::get_available_reviewers();
        $reviewercount = count($reviewers);
        $assignedcount = \local_jobboard\reviewer::count_assigned($vacancyid ?: null);
        $avgworkload = $reviewercount > 0 ? round($assignedcount / $reviewercount, 1) : 0;

        // Get vacancies for filter dropdown.
        $vacancies = [];
        $allvacancies = $DB->get_records_sql(
            "SELECT DISTINCT v.id, v.code, v.title
               FROM {local_jobboard_vacancy} v
               JOIN {local_jobboard_application} a ON a.vacancyid = v.id
              WHERE a.status = 'submitted'
              ORDER BY v.code"
        );
        foreach ($allvacancies as $v) {
            $vacancies[] = [
                'id' => $v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($v->id == $vacancyid),
            ];
        }

        // Prepare reviewers data with workload.
        $reviewersdata = [];
        foreach ($reviewers as $r) {
            $workload = \local_jobboard\reviewer::get_workload($r->id);
            $workloadcolor = 'success';
            if ($workload >= 20) {
                $workloadcolor = 'danger';
            } else if ($workload >= 10) {
                $workloadcolor = 'warning';
            }

            $reviewersdata[] = [
                'id' => $r->id,
                'fullname' => fullname($r),
                'workload' => $workload,
                'workloadcolor' => $workloadcolor,
                'workloadindicator' => $workload >= 15 ? ' ⚠️' : '',
                'hasstats' => false, // Could add more detailed stats later.
            ];
        }

        // Get unassigned applications.
        $unassigned = \local_jobboard\reviewer::get_unassigned_applications($vacancyid ?: null);
        $unassigneddata = [];
        foreach ($unassigned as $app) {
            $statuscolor = 'warning';
            if ($app->status === 'submitted') {
                $statuscolor = 'primary';
            }

            $unassigneddata[] = [
                'id' => $app->id,
                'applicantname' => format_string($app->firstname . ' ' . $app->lastname),
                'vacancycode' => format_string($app->vacancy_code),
                'statustext' => get_string('status_' . $app->status, 'local_jobboard'),
                'statuscolor' => $statuscolor,
                'dateapplied' => userdate($app->timecreated, '%Y-%m-%d'),
            ];
        }

        $pageurl = new \moodle_url('/local/jobboard/admin/assign_reviewer.php');

        return [
            'pagetitle' => get_string('assignreviewer', 'local_jobboard'),
            'stats' => [
                'unassigned' => $unassignedcount,
                'reviewers' => $reviewercount,
                'assigned' => $assignedcount,
                'avgworkload' => $avgworkload,
            ],
            'vacancies' => $vacancies,
            'selectedvacancyid' => $vacancyid,
            'reviewers' => $reviewersdata,
            'hasreviewers' => !empty($reviewersdata),
            'unassigned' => $unassigneddata,
            'hasunassigned' => !empty($unassigneddata),
            'filterformurl' => $pageurl->out(false),
            'actionformurl' => $pageurl->out(false),
            'dashboardurl' => (new \moodle_url('/local/jobboard/'))->out(false),
            'reviewurl' => (new \moodle_url('/local/jobboard/views/myreviews.php'))->out(false),
            'sesskey' => sesskey(),
        ];
    }

    /**
     * Render the assign reviewer page.
     *
     * @param array $data Template data from prepare_assign_reviewer_page_data.
     * @return string Rendered HTML.
     */
    public function render_assign_reviewer_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/review/assign_reviewer', $data);
    }

    /**
     * Prepare data for faculty assignment page (mode=faculties).
     *
     * @return array Template data.
     */
    public function prepare_faculty_assignment_page_data(): array {
        global $DB;

        $pageurl = new \moodle_url('/local/jobboard/admin/assign_reviewer.php', ['mode' => 'faculties']);

        // Get all faculties.
        $faculties = $DB->get_records('local_jobboard_faculty', ['enabled' => 1], 'name ASC');
        $facultiesdata = [];
        $totalreviewers = 0;

        foreach ($faculties as $faculty) {
            // Get reviewers assigned to this faculty.
            $assignedsql = "SELECT fr.*, u.firstname, u.lastname, u.email
                            FROM {local_jobboard_faculty_reviewer} fr
                            JOIN {user} u ON u.id = fr.userid
                            WHERE fr.facultyid = :facultyid AND fr.status = 'active'
                            ORDER BY fr.role ASC, u.lastname ASC";
            $assignedreviewers = $DB->get_records_sql($assignedsql, ['facultyid' => $faculty->id]);

            $reviewersdata = [];
            foreach ($assignedreviewers as $reviewer) {
                $reviewersdata[] = [
                    'id' => $reviewer->id,
                    'userid' => $reviewer->userid,
                    'fullname' => fullname($reviewer),
                    'email' => $reviewer->email,
                    'role' => $reviewer->role,
                    'roledisplay' => get_string('faculty_reviewer_role_' . $reviewer->role, 'local_jobboard'),
                    'isdean' => $reviewer->role === 'dean',
                    'unassignurl' => (new \moodle_url('/local/jobboard/admin/assign_reviewer.php', [
                        'mode' => 'faculties',
                        'action' => 'unassignfaculty',
                        'id' => $reviewer->id,
                        'sesskey' => sesskey(),
                    ]))->out(false),
                ];
                $totalreviewers++;
            }

            // Count programs in this faculty.
            $programcount = $DB->count_records('local_jobboard_program', ['facultyid' => $faculty->id, 'enabled' => 1]);

            // Count vacancies linked to programs in this faculty.
            $vacancycount = 0;
            if ($programcount > 0) {
                $vacancycount = $DB->count_records_sql(
                    "SELECT COUNT(DISTINCT v.id)
                       FROM {local_jobboard_vacancy} v
                       JOIN {local_jobboard_program} p ON v.programid = p.id
                      WHERE p.facultyid = :facultyid AND v.status = 'published'",
                    ['facultyid' => $faculty->id]
                );
            }

            $facultiesdata[] = [
                'id' => $faculty->id,
                'code' => $faculty->code,
                'name' => format_string($faculty->name),
                'shortname' => $faculty->shortname ?? $faculty->code,
                'reviewers' => $reviewersdata,
                'hasreviewers' => !empty($reviewersdata),
                'reviewercount' => count($reviewersdata),
                'programcount' => $programcount,
                'vacancycount' => $vacancycount,
            ];
        }

        // Get users with dean role for assignment dropdown.
        $deanrole = $DB->get_record('role', ['shortname' => 'jobboard_dean']);
        $availabledeans = [];
        if ($deanrole) {
            $systemcontext = \context_system::instance();
            $deanusers = get_role_users($deanrole->id, $systemcontext, false, 'u.id, u.firstname, u.lastname, u.email');
            foreach ($deanusers as $user) {
                $availabledeans[] = [
                    'id' => $user->id,
                    'fullname' => fullname($user),
                    'email' => $user->email,
                ];
            }
        }

        // If no deans available, get users who could be deans (with approve capability).
        if (empty($availabledeans)) {
            $systemcontext = \context_system::instance();
            $potentialusers = get_users_by_capability($systemcontext, 'local/jobboard:approveprofile', 'u.id, u.firstname, u.lastname, u.email');
            foreach ($potentialusers as $user) {
                $availabledeans[] = [
                    'id' => $user->id,
                    'fullname' => fullname($user),
                    'email' => $user->email,
                ];
            }
        }

        // Faculty reviewer roles for dropdown.
        $facultyroles = [
            ['value' => 'dean', 'label' => get_string('faculty_reviewer_role_dean', 'local_jobboard')],
            ['value' => 'lead_reviewer', 'label' => get_string('faculty_reviewer_role_lead_reviewer', 'local_jobboard')],
            ['value' => 'reviewer', 'label' => get_string('faculty_reviewer_role_reviewer', 'local_jobboard')],
        ];

        return [
            'pagetitle' => get_string('managefacultyreviewers', 'local_jobboard'),
            'pagedesc' => get_string('managefacultyreviewers_desc', 'local_jobboard'),
            'faculties' => $facultiesdata,
            'hasfaculties' => !empty($facultiesdata),
            'facultycount' => count($facultiesdata),
            'totalreviewers' => $totalreviewers,
            'availabledeans' => $availabledeans,
            'hasavailabledeans' => !empty($availabledeans),
            'facultyroles' => $facultyroles,
            'actionformurl' => $pageurl->out(false),
            'dashboardurl' => (new \moodle_url('/local/jobboard/'))->out(false),
            'rolesurl' => (new \moodle_url('/local/jobboard/admin/roles.php'))->out(false),
            'assignurl' => (new \moodle_url('/local/jobboard/admin/assign_reviewer.php'))->out(false),
            'sesskey' => sesskey(),
        ];
    }

    /**
     * Render the faculty assignment page.
     *
     * @param array $data Template data from prepare_faculty_assignment_page_data.
     * @return string Rendered HTML.
     */
    public function render_faculty_assignment_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/review/faculty_assignment', $data);
    }
}
