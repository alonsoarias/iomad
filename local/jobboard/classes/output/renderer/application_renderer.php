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
 * Application renderer trait for Job Board plugin.
 *
 * Contains all application-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for application rendering functionality.
 */
trait application_renderer {

    /**
     * Render applications list page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_applications_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/applications', $data);
    }

    /**
     * Render application form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_apply_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/apply', $data);
    }

    /**
     * Render application detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_application_detail_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/application_detail', $data);
    }

    /**
     * Prepare applications page data for template.
     *
     * @param int $userid User ID.
     * @param array $applications Array of application records.
     * @param int $total Total number of applications.
     * @param array $stats Application statistics.
     * @param string $status Current status filter.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @param object|null $exemption User exemption if any.
     * @return array Complete template data.
     */
    public function prepare_applications_page_data(
        int $userid,
        array $applications,
        int $total,
        array $stats,
        string $status,
        int $page,
        int $perpage,
        ?object $exemption = null
    ): array {
        global $OUTPUT;

        // Progress steps for applications.
        $progresssteps = ['submitted', 'under_review', 'docs_validated', 'interview', 'selected'];

        // Prepare application data.
        $applicationdata = [];
        foreach ($applications as $app) {
            // Determine current step index.
            $currentindex = array_search($app->status, $progresssteps);
            if ($currentindex === false) {
                $currentindex = -1;
            }

            // Prepare progress steps.
            $steps = [];
            foreach ($progresssteps as $i => $step) {
                $steps[] = [
                    'step' => $step,
                    'completed' => $i < $currentindex,
                    'current' => $i === $currentindex,
                ];
            }

            // Calculate progress percent.
            $progresspercent = $currentindex >= 0 ? (($currentindex + 1) / count($progresssteps)) * 100 : 0;

            // Document counts.
            $doccount = $app->document_count ?? 0;
            $docsapproved = $app->docs_approved ?? 0;
            $docsrejected = $app->docs_rejected ?? 0;
            $docspending = max(0, $doccount - $docsapproved - $docsrejected);

            // Can withdraw?
            $canwithdraw = in_array($app->status, ['submitted', 'under_review']);

            $applicationdata[] = [
                'id' => $app->id,
                'vacancyid' => $app->vacancyid,
                'vacancycode' => $app->vacancy_code ?? '',
                'vacancytitle' => format_string($app->vacancy_title ?? get_string('unknownvacancy', 'local_jobboard')),
                'vacancyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $app->vacancyid]))->out(false),
                'convocatorianame' => !empty($app->convocatoria_name) ? format_string($app->convocatoria_name) : null,
                'status' => $app->status,
                'statuslabel' => get_string('appstatus:' . $app->status, 'local_jobboard'),
                'statuscolor' => $this->get_application_status_class($app->status),
                'dateapplied' => userdate($app->timecreated, get_string('strftimedate', 'langconfig')),
                'progresssteps' => $steps,
                'progresspercent' => round($progresspercent),
                'documentcount' => $doccount,
                'docsapproved' => $docsapproved,
                'docsrejected' => $docsrejected,
                'docspending' => $docspending,
                'haspendingdocs' => $docspending > 0 && in_array($app->status, ['submitted', 'under_review', 'docs_rejected']),
                'statusnotes' => !empty($app->statusnotes) ? format_string($app->statusnotes) : null,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id]))->out(false),
                'withdrawurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id, 'action' => 'withdraw']))->out(false),
                'canwithdraw' => $canwithdraw,
            ];
        }

        // Prepare filter form.
        $statusoptions = [['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($status)]];
        $statuses = ['submitted', 'under_review', 'docs_validated', 'docs_rejected', 'interview', 'selected', 'rejected', 'withdrawn'];
        foreach ($statuses as $s) {
            $statusoptions[] = [
                'value' => $s,
                'label' => get_string('appstatus:' . $s, 'local_jobboard'),
                'selected' => $status === $s,
            ];
        }

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [
                ['name' => 'view', 'value' => 'applications'],
            ],
            'fields' => [
                [
                    'name' => 'status',
                    'label' => get_string('status', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $statusoptions,
                    'col' => 'jb-col-md-4',
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
                'view' => 'applications',
                'status' => $status,
            ]);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        // Stats cards.
        $statscards = [
            [
                'value' => (string)($stats['total'] ?? 0),
                'label' => get_string('myapplications', 'local_jobboard'),
                'icon' => 'file-alt',
                'color' => 'primary',
            ],
            [
                'value' => (string)(($stats['submitted'] ?? 0) + ($stats['under_review'] ?? 0)),
                'label' => get_string('inprogress', 'local_jobboard'),
                'icon' => 'spinner',
                'color' => 'info',
            ],
            [
                'value' => (string)($stats['docs_validated'] ?? 0),
                'label' => get_string('validationapproved', 'local_jobboard'),
                'icon' => 'check-circle',
                'color' => 'success',
            ],
            [
                'value' => (string)($stats['selected'] ?? 0),
                'label' => get_string('appstatus:selected', 'local_jobboard'),
                'icon' => 'trophy',
                'color' => 'success',
            ],
        ];

        // Exemption data.
        $exemptiondata = null;
        if ($exemption) {
            $exemptiondata = [
                'type' => $exemption->exemptiontype,
                'typeformatted' => get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard'),
                'documentref' => !empty($exemption->documentref) ? format_string($exemption->documentref) : null,
            ];
        }

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'browseurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false),
            'hasexemption' => !empty($exemption),
            'exemption' => $exemptiondata,
            'stats' => $statscards,
            'pendingdocscount' => $stats['pending_docs'] ?? 0,
            'filterform' => $filterform,
            'showinginfo' => $showinginfo,
            'hasapplications' => !empty($applicationdata),
            'applications' => $applicationdata,
            'pagination' => $pagination,
        ];
    }

    /**
     * Prepare application data for templates.
     *
     * @param object $application Application record.
     * @param object|null $vacancy Optional vacancy record.
     * @param object|null $user Optional user record.
     * @return array Template data.
     */
    public function prepare_application_data(object $application, ?object $vacancy = null, ?object $user = null): array {
        global $DB;

        if (!$vacancy) {
            $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        }
        if (!$user) {
            $user = \core_user::get_user($application->userid);
        }

        // Document counts.
        $documentcount = $DB->count_records('local_jobboard_document', ['applicationid' => $application->id]);
        $docsapproved = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'approved',
        ]);
        $docsrejected = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'rejected',
        ]);
        $docspending = $documentcount - $docsapproved - $docsrejected;
        $docsprogress = $documentcount > 0 ? round(($docsapproved / $documentcount) * 100) : 0;

        return [
            'id' => $application->id,
            'userid' => $application->userid,
            'vacancyid' => $application->vacancyid,
            'vacancycode' => $vacancy ? $vacancy->code : '',
            'vacancytitle' => $vacancy ? format_string($vacancy->title) : '',
            'applicantname' => $user ? fullname($user) : '',
            'applicantemail' => $user ? $user->email : '',
            'status' => $application->status,
            'statuslabel' => get_string('appstatus:' . $application->status, 'local_jobboard'),
            'statuscolor' => $this->get_application_status_class($application->status),
            'timecreated' => userdate($application->timecreated, get_string('strftimedatetime', 'langconfig')),
            'timecreatedformatted' => userdate($application->timecreated, get_string('strftimedate', 'langconfig')),
            'documentcount' => $documentcount,
            'docsapproved' => $docsapproved,
            'docsrejected' => $docsrejected,
            'docspending' => $docspending,
            'docsprogress' => $docsprogress,
            'isexemption' => !empty($application->isexemption),
            'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'applicationid' => $application->id]))->out(false),
        ];
    }

    /**
     * Prepare application detail page data.
     *
     * @param \local_jobboard\application $application The application object.
     * @param \local_jobboard\vacancy $vacancy The vacancy object.
     * @param object $applicant The applicant user record.
     * @param array $documents Array of document objects.
     * @param array $history Status history array.
     * @param bool $isowner Whether current user owns the application.
     * @param bool $canreview Whether current user can review.
     * @param bool $canmanage Whether current user can manage workflow.
     * @param array $workflowactions Available workflow transitions.
     * @param object|null $exemption Active exemption record.
     * @return array Template data.
     */
    public function prepare_application_detail_page_data(
        \local_jobboard\application $application,
        \local_jobboard\vacancy $vacancy,
        object $applicant,
        array $documents,
        array $history,
        bool $isowner,
        bool $canreview,
        bool $canmanage,
        array $workflowactions = [],
        ?object $exemption = null
    ): array {
        // Status styling.
        $statuscolor = 'info';
        $statusicon = 'info-circle';

        switch ($application->status) {
            case 'docs_validated':
            case 'selected':
                $statuscolor = 'success';
                $statusicon = 'check-circle';
                break;
            case 'docs_rejected':
            case 'rejected':
                $statuscolor = 'danger';
                $statusicon = 'times-circle';
                break;
            case 'withdrawn':
                $statuscolor = 'secondary';
                $statusicon = 'ban';
                break;
            case 'interview':
                $statuscolor = 'warning';
                $statusicon = 'calendar-check';
                break;
            case 'under_review':
                $statuscolor = 'warning';
                $statusicon = 'clock';
                break;
            case 'submitted':
                $statuscolor = 'info';
                $statusicon = 'paper-plane';
                break;
        }

        // Breadcrumbs.
        $breadcrumbs = [
            ['label' => get_string('dashboard', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php'))->out(false)],
        ];
        if ($isowner) {
            $breadcrumbs[] = [
                'label' => get_string('myapplications', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
            ];
        } else {
            $breadcrumbs[] = [
                'label' => get_string('reviewapplications', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $application->vacancyid]))->out(false),
            ];
        }
        $breadcrumbs[] = [
            'label' => get_string('application', 'local_jobboard') . ' #' . $application->id,
            'url' => null,
            'active' => true,
        ];

        // Prepare documents data.
        $documentsdata = [];
        foreach ($documents as $doc) {
            $docstatus = $doc->status ?? 'pending';
            $docstatuscolor = 'warning';
            $docstatusicon = 'clock';
            $filecolor = 'secondary';

            switch ($docstatus) {
                case 'approved':
                    $docstatuscolor = 'success';
                    $docstatusicon = 'check-circle';
                    $filecolor = 'success';
                    break;
                case 'rejected':
                    $docstatuscolor = 'danger';
                    $docstatusicon = 'times-circle';
                    $filecolor = 'danger';
                    break;
            }

            $documentsdata[] = [
                'id' => $doc->id,
                'typename' => $doc->get_doctype_name(),
                'filename' => format_string($doc->filename),
                'status' => $docstatus,
                'statuslabel' => get_string('docstatus:' . $docstatus, 'local_jobboard'),
                'statuscolor' => $docstatuscolor,
                'statusicon' => $docstatusicon,
                'filecolor' => $filecolor,
                'downloadurl' => $doc->get_download_url() ? $doc->get_download_url()->out(false) : null,
                'rejectreason' => $doc->rejectreason ?? null,
                'canreupload' => $isowner && $docstatus === 'rejected' && in_array($application->status, ['docs_rejected', 'submitted']),
                'reuploadurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id, 'reupload' => $doc->id]))->out(false),
            ];
        }

        // Prepare history data.
        $historydata = [];
        foreach ($history as $entry) {
            $hstatuscolor = $this->get_application_status_class($entry->newstatus);
            $changedbyname = '';
            if (!empty($entry->changedby)) {
                $changedbyuser = \core_user::get_user($entry->changedby);
                $changedbyname = $changedbyuser ? fullname($changedbyuser) : '';
            }

            $historydata[] = [
                'status' => $entry->newstatus,
                'statuslabel' => get_string('status_' . $entry->newstatus, 'local_jobboard'),
                'statuscolor' => $hstatuscolor,
                'timeformatted' => userdate($entry->timecreated, get_string('strftimedatetime', 'langconfig')),
                'notes' => $entry->notes ?? null,
                'changedbyname' => $changedbyname,
            ];
        }

        // Prepare workflow actions.
        $workflowdata = [];
        foreach ($workflowactions as $status) {
            $workflowdata[] = [
                'value' => $status,
                'label' => get_string('status_' . $status, 'local_jobboard'),
            ];
        }

        // Can withdraw?
        $canwithdraw = $isowner && in_array($application->status, ['submitted', 'under_review']);

        // Back navigation.
        $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false);
        $backlabel = get_string('backtoapplications', 'local_jobboard');
        if (!$isowner) {
            $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $application->vacancyid]))->out(false);
            $backlabel = get_string('backtoreviewlist', 'local_jobboard');
        }

        // Applicant info (for reviewers).
        $applicantdata = null;
        if ($canreview || $canmanage) {
            $applicantdata = [
                'fullname' => fullname($applicant),
                'email' => $applicant->email,
                'idnumber' => $applicant->idnumber ?? null,
                'phone' => $applicant->phone1 ?? null,
            ];
        }

        // Exemption info.
        $hasexemption = false;
        $exemptiontype = '';
        $exemptionref = '';
        if ($exemption) {
            $hasexemption = true;
            $exemptiontype = get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard');
            $exemptionref = $exemption->documentref ?? '';
        }

        return [
            'breadcrumbs' => $breadcrumbs,
            'application' => [
                'id' => $application->id,
                'status' => $application->status,
                'statuslabel' => get_string('status_' . $application->status, 'local_jobboard'),
                'statuscolor' => $statuscolor,
                'statusicon' => $statusicon,
                'statusnotes' => $application->statusnotes ?? null,
                'dateapplied' => userdate($application->timecreated, get_string('strftimedate', 'langconfig')),
                'digitalsignature' => $application->digitalsignature ?? null,
                'consenttimestamp' => !empty($application->consenttimestamp) ?
                    userdate($application->consenttimestamp, get_string('strftimedate', 'langconfig')) : null,
                'coverletter' => !empty($application->coverletter) ? nl2br(format_string($application->coverletter)) : null,
            ],
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => $vacancy->code,
                'title' => format_string($vacancy->title),
                'location' => $vacancy->location ?? null,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]))->out(false),
            ],
            'applicant' => $applicantdata,
            'hasexemption' => $hasexemption,
            'exemptiontype' => $exemptiontype,
            'exemptionref' => $exemptionref,
            'documents' => $documentsdata,
            'hasdocuments' => !empty($documentsdata),
            'documentcount' => count($documentsdata),
            'history' => $historydata,
            'hashistory' => !empty($historydata),
            'isowner' => $isowner,
            'canreview' => $canreview && !$isowner,
            'canmanage' => $canmanage,
            'canwithdraw' => $canwithdraw,
            'workflowactions' => $workflowdata,
            'hasworkflowactions' => !empty($workflowdata),
            'sesskey' => sesskey(),
            'workflowurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'withdrawurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id, 'action' => 'withdraw']))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'applicationid' => $application->id]))->out(false),
            'backurl' => $backurl,
            'backlabel' => $backlabel,
        ];
    }

    /**
     * Prepare data for apply page template.
     *
     * @param object $vacancy Vacancy object.
     * @param array $requireddocs Required document types array.
     * @param bool $isexemption Whether user has exemption.
     * @param ?object $exemptioninfo Exemption info object if applicable.
     * @param string $formhtml Rendered form HTML.
     * @param int $daysuntilclose Days until vacancy closes.
     * @return array Template data.
     */
    public function prepare_apply_page_data(
        object $vacancy,
        array $requireddocs,
        bool $isexemption,
        ?object $exemptioninfo,
        string $formhtml,
        int $daysuntilclose
    ): array {
        $vacancyid = $vacancy->id;

        // Progress steps.
        $steps = [
            ['icon' => 'fa-user-check', 'label' => get_string('step_profile', 'local_jobboard'), 'target' => 'id_profilereviewheader'],
            ['icon' => 'fa-file-signature', 'label' => get_string('step_consent', 'local_jobboard'), 'target' => 'id_consentheader'],
            ['icon' => 'fa-upload', 'label' => get_string('step_documents', 'local_jobboard'), 'target' => 'id_documentsheader'],
            ['icon' => 'fa-envelope-open-text', 'label' => get_string('step_coverletter', 'local_jobboard'), 'target' => 'id_additionalheader'],
            ['icon' => 'fa-paper-plane', 'label' => get_string('step_submit', 'local_jobboard'), 'target' => 'id_declarationheader'],
        ];

        $stepsdata = [];
        foreach ($steps as $i => $step) {
            $stepsdata[] = [
                'stepnum' => $i + 1,
                'icon' => $step['icon'],
                'label' => $step['label'],
                'target' => $step['target'],
                'isactive' => ($i === 0),
                'islast' => ($i === count($steps) - 1),
            ];
        }

        // Deadline badge color.
        $deadlinebadgecolor = 'success';
        if ($daysuntilclose <= 3) {
            $deadlinebadgecolor = 'danger';
        } else if ($daysuntilclose <= 7) {
            $deadlinebadgecolor = 'warning';
        }

        // Get vacancy close date.
        $closedate = 0;
        if (method_exists($vacancy, 'get_close_date')) {
            $closedate = $vacancy->get_close_date();
        } else if (!empty($vacancy->closedate)) {
            $closedate = $vacancy->closedate;
        }

        // Determine location (from company or direct field).
        $location = null;
        if (method_exists($vacancy, 'get_company_name')) {
            $location = $vacancy->get_company_name();
        }
        if (empty($location) && !empty($vacancy->location)) {
            $location = $vacancy->location;
        }

        // Determine modality (from department or direct field).
        $modality = null;
        if (method_exists($vacancy, 'get_department_name')) {
            $modality = $vacancy->get_department_name();
        }
        if (empty($modality)) {
            $vacancyrecord = method_exists($vacancy, 'get_record') ? $vacancy->get_record() : $vacancy;
            if (!empty($vacancyrecord->modality)) {
                $modalities = \local_jobboard_get_modalities();
                $modality = $modalities[$vacancyrecord->modality] ?? $vacancyrecord->modality;
            }
        }

        // Guidelines.
        $guidelines = [
            get_string('guideline1', 'local_jobboard'),
            get_string('guideline2', 'local_jobboard'),
            get_string('guideline3', 'local_jobboard'),
            get_string('guideline4', 'local_jobboard'),
        ];

        // Quick tips.
        $quicktips = [
            get_string('tip_saveoften', 'local_jobboard'),
            get_string('tip_checkdocs', 'local_jobboard'),
            get_string('tip_deadline', 'local_jobboard'),
        ];

        // Required docs data.
        $docsdata = [];
        foreach ($requireddocs as $doc) {
            $docsdata[] = [
                'name' => format_string($doc->name),
                'isrequired' => !empty($doc->isrequired),
            ];
        }

        // Exemption info.
        $exemptiondata = null;
        if ($isexemption && $exemptioninfo) {
            $exemptiondata = [
                'exemptiontype' => $exemptioninfo->exemptiontype ?? '',
                'documentref' => $exemptioninfo->documentref ?? '',
            ];
        }

        return [
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => format_string($vacancy->code),
                'title' => format_string($vacancy->title),
                'location' => $location,
                'modality' => $modality,
                'closedateformatted' => $closedate ? userdate($closedate, get_string('strftimedatetime', 'langconfig')) : '',
            ],
            'steps' => $stepsdata,
            'daysuntilclose' => $daysuntilclose,
            'showdeadlinewarning' => ($daysuntilclose <= 3 && $daysuntilclose > 0),
            'deadlinewarningtext' => get_string('deadlinewarning', 'local_jobboard', ceil($daysuntilclose)),
            'showdaysremaining' => ($daysuntilclose > 0),
            'daysremainingtext' => get_string('daysremaining', 'local_jobboard', ceil($daysuntilclose)),
            'deadlinebadgecolor' => $deadlinebadgecolor,
            'guidelines' => $guidelines,
            'isexemption' => $isexemption,
            'exemptioninfo' => $exemptiondata,
            'formhtml' => $formhtml,
            'requireddocs' => $docsdata,
            'hasrequireddocs' => !empty($docsdata),
            'quicktips' => $quicktips,
            'viewvacancyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancyid]))->out(false),
            'backvacancyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]))->out(false),
            'browservacanciesurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false),
        ];
    }
}
