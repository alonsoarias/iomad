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
        return $this->render_from_template('local_jobboard/pages/review', $data);
    }

    /**
     * Render my reviews page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_myreviews_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/myreviews', $data);
    }

    /**
     * Render bulk validate page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_bulk_validate_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/bulk_validate', $data);
    }

    /**
     * Render validate document page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_validate_document_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/validate_document', $data);
    }

    /**
     * Render reupload document page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_reupload_document_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/reupload_document', $data);
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

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [['name' => 'view', 'value' => 'myreviews']],
            'fields' => [
                [
                    'name' => 'status',
                    'label' => get_string('status', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $statusOptions,
                    'col' => 'jb-col-md-3',
                ],
                [
                    'name' => 'vacancy',
                    'label' => get_string('vacancy', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $vacancyOptions,
                    'col' => 'jb-col-md-4',
                ],
                [
                    'name' => 'priority',
                    'label' => get_string('sortby', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $priorityOptions,
                    'col' => 'jb-col-md-3',
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
                $docobj = \local_jobboard\document::get($doc->id);
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
     * @return array Template data.
     */
    public function prepare_reupload_document_data(string $formhtml): array {
        return [
            'pagetitle' => get_string('reuploaddocument', 'local_jobboard'),
            'helptext' => get_string('reuploadhelp', 'local_jobboard'),
            'formhtml' => $formhtml,
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

        // Filter form.
        $vacancies = $DB->get_records('local_jobboard_vacancy', ['status' => 'published'], 'code ASC', 'id, code, title');
        $vacancyoptions = [['value' => 0, 'label' => get_string('allvacancies', 'local_jobboard'), 'selected' => ($vacancyid == 0)]];
        foreach ($vacancies as $v) {
            $vacancyoptions[] = [
                'value' => $v->id,
                'label' => format_string($v->code) . ' - ' . format_string($v->title),
                'selected' => ($vacancyid == $v->id),
            ];
        }

        $data['filterform'] = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [
                ['name' => 'view', 'value' => 'review'],
            ],
            'fields' => [
                [
                    'name' => 'vacancyid',
                    'options' => $vacancyoptions,
                ],
            ],
        ];

        // Applications list.
        $appsdata = [];
        foreach ($applications as $app) {
            $isurgent = !empty($app->closedate) && ($app->closedate - time()) <= 7 * 86400;
            $haspendingdocs = (int) ($app->pendingcount ?? 0) > 0;

            $appsdata[] = [
                'id' => $app->id,
                'applicantname' => fullname($app),
                'email' => $app->email,
                'vacancycode' => format_string($app->vacancy_code ?? ''),
                'vacancytitle' => format_string($app->vacancy_title ?? ''),
                'statuslabel' => get_string('status_' . $app->status, 'local_jobboard'),
                'doccount' => (int) ($app->doccount ?? 0),
                'pendingcount' => (int) ($app->pendingcount ?? 0),
                'datesubmitted' => userdate($app->timecreated, get_string('strftimedate', 'langconfig')),
                'isurgent' => $isurgent,
                'haspendingdocs' => $haspendingdocs,
                'reviewurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $app->id,
                ]))->out(false),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'application',
                    'id' => $app->id,
                ]))->out(false),
            ];
        }

        $data['hasapplications'] = !empty($appsdata);
        $data['applications'] = $appsdata;

        // Pagination.
        if ($total > $perpage) {
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'review',
                'vacancyid' => $vacancyid,
            ]);
            $data['pagination'] = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

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
        global $DB;

        $vacancyid = $params['vacancyid'] ?? 0;
        $applicationid = $application->id;

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

        // Prepare documents.
        $docsdata = [];
        foreach ($documents as $doc) {
            $status = $doc->status ?? 'pending';
            $statusconfig = [
                'pending' => ['icon' => 'clock', 'color' => 'warning'],
                'approved' => ['icon' => 'check-circle', 'color' => 'success'],
                'rejected' => ['icon' => 'times-circle', 'color' => 'danger'],
            ];
            $config = $statusconfig[$status] ?? $statusconfig['pending'];

            $downloadurl = null;
            if (method_exists($doc, 'get_download_url')) {
                $downloadurl = $doc->get_download_url();
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

            $docsdata[] = [
                'id' => $doc->id,
                'typename' => format_string($typename),
                'filename' => format_string($doc->filename ?? ''),
                'status' => $status,
                'statuslabel' => get_string('docstatus:' . $status, 'local_jobboard'),
                'statusicon' => $config['icon'],
                'statuscolor' => $config['color'],
                'ispending' => ($status === 'pending'),
                'downloadurl' => $downloadurl,
                'validateurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $applicationid,
                    'documentid' => $doc->id,
                    'action' => 'validate',
                    'sesskey' => sesskey(),
                ]))->out(false),
                'rejecturl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
                'rejectreason' => $doc->rejectreason ?? null,
                'reviewedby' => $reviewedby,
                'reviewedat' => $reviewedat,
                'sesskey' => sesskey(),
            ];
        }

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

        $data['canvalidateall'] = ($docstats['pending'] > 0);
        $data['validateallurl'] = (new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $applicationid,
            'action' => 'validateall',
            'sesskey' => sesskey(),
        ]))->out(false);

        $data['submitreviewurl'] = (new moodle_url('/local/jobboard/index.php'))->out(false);
        $data['sesskey'] = sesskey();

        $data['backurl'] = (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancyid]))->out(false);

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
}
