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
 * Renderer for Job Board plugin.
 *
 * This is the main renderer class that uses traits for organization.
 * Renderer traits are located in classes/output/renderer/ directory.
 *
 * Structure:
 * - dashboard_renderer: Dashboard page and widgets
 * - public_renderer: Public-facing pages (browse, vacancy, convocatoria)
 * - vacancy_renderer: Vacancy management pages
 * - convocatoria_renderer: Convocatoria management pages
 * - application_renderer: Application pages
 * - review_renderer: Review and validation pages
 * - admin_renderer: Admin settings and tools pages
 * - exemption_renderer: Exemption management pages
 * - committee_renderer: Committee and reviewer assignment pages
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

// Load renderer traits.
require_once(__DIR__ . '/renderer/dashboard_renderer.php');
require_once(__DIR__ . '/renderer/public_renderer.php');
require_once(__DIR__ . '/renderer/vacancy_renderer.php');
require_once(__DIR__ . '/renderer/convocatoria_renderer.php');
require_once(__DIR__ . '/renderer/application_renderer.php');
require_once(__DIR__ . '/renderer/review_renderer.php');
require_once(__DIR__ . '/renderer/admin_renderer.php');
require_once(__DIR__ . '/renderer/exemption_renderer.php');
require_once(__DIR__ . '/renderer/committee_renderer.php');

/**
 * Renderer class for the Job Board plugin.
 *
 * Uses traits for render_* methods organization.
 * The prepare_* methods remain in this file for now.
 *
 * @todo In future versions, move prepare_* methods to corresponding traits.
 */
class renderer extends renderer_base
{

    // Traits define render_*_page methods for template rendering.
    // The prepare_* methods remain in this file.
    use renderer\dashboard_renderer;
    use renderer\public_renderer;
    use renderer\vacancy_renderer;
    use renderer\convocatoria_renderer;
    use renderer\application_renderer;
    use renderer\review_renderer;
    use renderer\admin_renderer;
    use renderer\exemption_renderer;
    use renderer\committee_renderer;

    // =========================================================================
    // PREPARE METHODS - Vacancies, Convocatorias, Applications, etc.
    // Dashboard prepare methods are now in dashboard_renderer trait.
    // =========================================================================






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
     * Prepare overview report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_overview_report_data(int $vacancyid, int $datefrom, int $dateto): array
    {
        global $DB;

        $params = ['from' => $datefrom, 'to' => $dateto];
        $vacancywhere = '';
        if ($vacancyid) {
            $vacancywhere = ' AND a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        // Summary stats.
        $totalapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}",
            $params
        );

        $selectedapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'selected' {$vacancywhere}",
            $params
        );

        $rejectedapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'rejected' {$vacancywhere}",
            $params
        );

        $pendingapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to AND a.status IN ('submitted', 'under_review') {$vacancywhere}",
            $params
        );

        $selectionrate = $totalapps > 0 ? round(($selectedapps / $totalapps) * 100, 1) : 0;

        // Stats cards.
        $stats = [
            ['value' => (string) $totalapps, 'label' => get_string('totalapplications', 'local_jobboard'), 'color' => 'primary', 'icon' => 'file-alt'],
            ['value' => (string) $selectedapps, 'label' => get_string('selected', 'local_jobboard'), 'color' => 'success', 'icon' => 'trophy'],
            ['value' => (string) $rejectedapps, 'label' => get_string('rejected', 'local_jobboard'), 'color' => 'danger', 'icon' => 'times-circle'],
            ['value' => $selectionrate . '%', 'label' => get_string('selectionrate', 'local_jobboard'), 'color' => 'info', 'icon' => 'chart-line'],
        ];

        // Applications by status.
        $statusdata = $DB->get_records_sql(
            "SELECT a.status, COUNT(*) as count
               FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
              GROUP BY a.status",
            $params
        );

        $statusrows = [];
        foreach ($statusdata as $row) {
            $pct = $totalapps > 0 ? round(($row->count / $totalapps) * 100) : 0;
            $statusrows[] = [
                'status' => $row->status,
                'statuslabel' => get_string('status_' . $row->status, 'local_jobboard'),
                'count' => (int) $row->count,
                'percentage' => $pct,
                'color' => $this->get_application_status_color($row->status),
            ];
        }

        return [
            'stats' => $stats,
            'statusdata' => $statusrows,
            'hasstatusdata' => !empty($statusrows),
        ];
    }

    /**
     * Get color class for application status.
     *
     * @param string $status Application status.
     * @return string Color class.
     */
    protected function get_application_status_color(string $status): string
    {
        $colors = [
            'submitted' => 'info',
            'under_review' => 'warning',
            'docs_validated' => 'success',
            'docs_rejected' => 'danger',
            'interview' => 'purple',
            'selected' => 'success',
            'rejected' => 'secondary',
            'withdrawn' => 'dark',
        ];
        return $colors[$status] ?? 'secondary';
    }

    /**
     * Prepare applications report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_applications_report_data(int $vacancyid, int $datefrom, int $dateto): array
    {
        global $DB;

        $params = ['from' => $datefrom, 'to' => $dateto];
        $vacancywhere = '';
        if ($vacancyid) {
            $vacancywhere = ' AND v.id = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        // Applications by vacancy.
        $byvacancy = $DB->get_records_sql(
            "SELECT v.id, v.code, v.title, COUNT(a.id) as total,
                    SUM(CASE WHEN a.status = 'selected' THEN 1 ELSE 0 END) as selected,
                    SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) as rejected
               FROM {local_jobboard_vacancy} v
               LEFT JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                    AND a.timecreated BETWEEN :from AND :to
              WHERE 1=1 {$vacancywhere}
              GROUP BY v.id, v.code, v.title
              ORDER BY total DESC",
            $params
        );

        $rows = [];
        foreach ($byvacancy as $row) {
            $pending = (int) $row->total - (int) $row->selected - (int) $row->rejected;
            $rows[] = [
                'id' => $row->id,
                'code' => format_string($row->code),
                'title' => format_string($row->title),
                'total' => (int) $row->total,
                'selected' => (int) $row->selected,
                'rejected' => (int) $row->rejected,
                'pending' => max(0, $pending),
            ];
        }

        return [
            'rows' => $rows,
            'hasdata' => !empty($rows),
        ];
    }

    /**
     * Prepare documents report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_documents_report_data(int $vacancyid, int $datefrom, int $dateto): array
    {
        // Get stats from bulk_validator class.
        $docstats = \local_jobboard\bulk_validator::get_validation_stats($vacancyid ?: null, $datefrom);
        $rejectionreasons = \local_jobboard\bulk_validator::get_rejection_reasons_stats($vacancyid ?: null, $datefrom);

        // Stats cards.
        $stats = [
            ['value' => (string) ($docstats['total'] ?? 0), 'label' => get_string('totaldocuments', 'local_jobboard'), 'color' => 'primary', 'icon' => 'folder'],
            [
                'value' => ($docstats['validated'] ?? 0) . ' (' . ($docstats['validation_rate'] ?? 0) . '%)',
                'label' => get_string('validated', 'local_jobboard'),
                'color' => 'success',
                'icon' => 'check-circle',
            ],
            [
                'value' => ($docstats['rejected'] ?? 0) . ' (' . ($docstats['rejection_rate'] ?? 0) . '%)',
                'label' => get_string('rejected', 'local_jobboard'),
                'color' => 'danger',
                'icon' => 'times-circle',
            ],
            [
                'value' => ($docstats['avg_validation_time_hours'] ?? 0) . 'h',
                'label' => get_string('avgvalidationtime', 'local_jobboard'),
                'color' => 'info',
                'icon' => 'clock',
            ],
        ];

        // Rejection reasons.
        $rejectionrows = [];
        foreach ($rejectionreasons as $reason) {
            $reasontext = get_string('rejectreason_' . $reason->rejectreason, 'local_jobboard');
            $rejectionrows[] = [
                'reason' => $reasontext,
                'count' => (int) $reason->count,
            ];
        }

        // Pending by type.
        $pendingbytype = [];
        foreach (($docstats['by_type'] ?? []) as $row) {
            if (isset($row->pending) && $row->pending > 0) {
                $typename = get_string('doctype_' . $row->documenttype, 'local_jobboard');
                $pendingbytype[] = [
                    'typename' => $typename,
                    'pending' => (int) $row->pending,
                ];
            }
        }

        // By document type table.
        $bytype = [];
        foreach (($docstats['by_type'] ?? []) as $row) {
            $typename = get_string('doctype_' . $row->documenttype, 'local_jobboard');
            $bytype[] = [
                'typename' => $typename,
                'total' => (int) ($row->total ?? 0),
                'validated' => (int) ($row->validated ?? 0),
                'rejected' => (int) ($row->rejected ?? 0),
                'pending' => (int) ($row->pending ?? 0),
            ];
        }

        return [
            'stats' => $stats,
            'rejectionreasons' => $rejectionrows,
            'hasrejections' => !empty($rejectionrows),
            'pendingbytype' => $pendingbytype,
            'haspending' => !empty($pendingbytype),
            'bytype' => $bytype,
            'hasbytypedata' => !empty($bytype),
        ];
    }

    /**
     * Prepare reviewers report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_reviewers_report_data(int $vacancyid, int $datefrom, int $dateto): array
    {
        $reviewers = \local_jobboard\reviewer::get_all_with_workload();

        $rows = [];
        foreach ($reviewers as $rev) {
            $workloadcolor = 'success';
            if ($rev->workload > 15) {
                $workloadcolor = 'danger';
            } else if ($rev->workload > 10) {
                $workloadcolor = 'warning';
            }

            $rows[] = [
                'id' => $rev->id,
                'fullname' => fullname($rev),
                'workload' => (int) $rev->workload,
                'workloadcolor' => $workloadcolor,
                'reviewed' => (int) ($rev->stats['reviewed'] ?? 0),
                'validated' => (int) ($rev->stats['validated'] ?? 0),
                'rejected' => (int) ($rev->stats['rejected'] ?? 0),
                'avgtime' => (int) ($rev->stats['avg_review_time'] ?? 0),
            ];
        }

        return [
            'rows' => $rows,
            'hasdata' => !empty($rows),
        ];
    }

    /**
     * Prepare timeline report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_timeline_report_data(int $vacancyid, int $datefrom, int $dateto): array
    {
        global $DB;

        $params = ['from' => $datefrom, 'to' => $dateto];
        $vacancywhere = '';
        if ($vacancyid) {
            $vacancywhere = ' AND a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        // Daily application counts.
        $sql = "SELECT DATE(FROM_UNIXTIME(a.timecreated)) as day, COUNT(*) as count
                  FROM {local_jobboard_application} a
                 WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
                 GROUP BY DATE(FROM_UNIXTIME(a.timecreated))
                 ORDER BY day ASC";

        $daily = $DB->get_records_sql($sql, $params);

        // Calculate max for visual bar.
        $maxcount = 0;
        foreach ($daily as $row) {
            if ($row->count > $maxcount) {
                $maxcount = (int) $row->count;
            }
        }

        $rows = [];
        foreach ($daily as $row) {
            $pct = $maxcount > 0 ? round(($row->count / $maxcount) * 100) : 0;
            $rows[] = [
                'day' => $row->day,
                'count' => (int) $row->count,
                'percentage' => $pct,
            ];
        }

        return [
            'rows' => $rows,
            'hasdata' => !empty($rows),
        ];
    }




    /**
     * Prepare committee list view data.
     *
     * @param moodle_url $pageurl Base page URL.
     * @param array $companies Available companies.
     * @return array Template data.
     */
    protected function prepare_committee_list_data(moodle_url $pageurl, array $companies): array
    {
        global $DB;

        // Get all committees.
        $sql = "SELECT c.*, comp.name as company_name, comp.shortname as company_shortname,
                       v.code as vacancy_code, v.title as vacancy_title,
                       (SELECT COUNT(*) FROM {local_jobboard_committee_member} WHERE committeeid = c.id) as membercount
                  FROM {local_jobboard_committee} c
             LEFT JOIN {company} comp ON comp.id = c.companyid
             LEFT JOIN {local_jobboard_vacancy} v ON v.id = c.vacancyid
                 ORDER BY comp.name, c.timecreated DESC";
        $committees = $DB->get_records_sql($sql);

        $committeesdata = [];
        foreach ($committees as $comm) {
            if (!empty($comm->companyid)) {
                $linkparams = ['companyid' => $comm->companyid];
                $entityname = format_string($comm->company_name ?: $comm->company_shortname);
            } else {
                $linkparams = ['vacancyid' => $comm->vacancyid];
                $entityname = format_string($comm->vacancy_code . ' - ' . $comm->vacancy_title);
            }

            $committeesdata[] = [
                'id' => $comm->id,
                'name' => format_string($comm->name),
                'entityname' => $entityname,
                'membercount' => (int) $comm->membercount,
                'status' => $comm->status,
                'isactive' => ($comm->status === 'active'),
                'manageurl' => (new moodle_url($pageurl, $linkparams))->out(false),
            ];
        }

        // Companies without committees.
        $companieswithout = [];
        foreach ($companies as $c) {
            if (!$DB->record_exists('local_jobboard_committee', ['companyid' => $c->id])) {
                $companieswithout[] = [
                    'id' => $c->id,
                    'name' => format_string($c->name),
                    'createurl' => (new moodle_url($pageurl, ['companyid' => $c->id]))->out(false),
                ];
            }
        }

        return [
            'committees' => $committeesdata,
            'hascommittees' => !empty($committeesdata),
            'companieswithoutcommittee' => $companieswithout,
            'hascompanieswithoutcommittee' => !empty($companieswithout),
        ];
    }

    /**
     * Prepare committee company view data.
     *
     * @param int $companyid Company ID.
     * @param moodle_url $pageurl Base page URL.
     * @param array $memberroles Role definitions.
     * @param string $usersearch User search string.
     * @return array Template data.
     */
    protected function prepare_committee_company_data(
        int $companyid,
        moodle_url $pageurl,
        array $memberroles,
        string $usersearch
    ): array {
        global $DB;

        $company = $DB->get_record('company', ['id' => $companyid], '*', MUST_EXIST);
        $vacancycount = $DB->count_records('local_jobboard_vacancy', ['companyid' => $companyid]);
        $existingcommittee = \local_jobboard\committee::get_for_company($companyid);

        $data = [
            'company' => [
                'id' => $company->id,
                'name' => format_string($company->name),
                'vacancycount' => $vacancycount,
            ],
            'hasexistingcommittee' => !empty($existingcommittee),
            'createformurl' => $pageurl->out(false),
            'addmemberformurl' => $pageurl->out(false),
            'defaultcommitteename' => get_string('facultycommitteedefaultname', 'local_jobboard', format_string($company->name)),
        ];

        // Get existing member IDs.
        $existingmemberids = [0];
        if ($existingcommittee && !empty($existingcommittee->members)) {
            $existingmemberids = array_column((array) $existingcommittee->members, 'userid');
            if (empty($existingmemberids)) {
                $existingmemberids = [0];
            }
        }

        // Get available users.
        $searchsql = '';
        $searchparams = [];
        if (!empty($usersearch)) {
            $searchsql = " AND (u.firstname LIKE :search1 OR u.lastname LIKE :search2
                           OR u.email LIKE :search3 OR u.username LIKE :search4)";
            $searchparams = [
                'search1' => '%' . $usersearch . '%',
                'search2' => '%' . $usersearch . '%',
                'search3' => '%' . $usersearch . '%',
                'search4' => '%' . $usersearch . '%',
            ];
        }

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.username
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.suspended = 0
                   AND u.id NOT IN (" . implode(',', $existingmemberids) . ")
                   AND u.id > 1
                   $searchsql
                 ORDER BY u.lastname, u.firstname
                 LIMIT 200";
        $availableusers = $DB->get_records_sql($sql, $searchparams);

        $usersdata = [];
        foreach ($availableusers as $user) {
            $usersdata[] = [
                'id' => $user->id,
                'fullname' => fullname($user),
                'username' => $user->username,
                'email' => $user->email,
            ];
        }
        $data['availableusers'] = $usersdata;
        $data['hasavailableusers'] = !empty($usersdata);

        if ($existingcommittee) {
            // Prepare committee data.
            $membersdata = [];
            foreach ($existingcommittee->members as $member) {
                $roledef = $memberroles[$member->role] ?? $memberroles[\local_jobboard\committee::ROLE_EVALUATOR];

                // Available roles for change (exclude current).
                $availableroles = [];
                foreach ($memberroles as $rolecode => $roleinfo) {
                    if ($rolecode !== $member->role) {
                        $availableroles[] = [
                            'code' => $rolecode,
                            'name' => $roleinfo['name'],
                            'changeurl' => (new moodle_url($pageurl, [
                                'action' => 'changerole',
                                'id' => $existingcommittee->id,
                                'userid' => $member->userid,
                                'newrole' => $rolecode,
                                'sesskey' => sesskey(),
                            ]))->out(false),
                        ];
                    }
                }

                $membersdata[] = [
                    'userid' => $member->userid,
                    'fullname' => fullname($member),
                    'username' => $member->username,
                    'email' => $member->email,
                    'role' => $member->role,
                    'rolename' => $roledef['name'],
                    'roleicon' => $roledef['icon'],
                    'rolecolor' => $roledef['color'],
                    'availableroles' => $availableroles,
                    'removeurl' => (new moodle_url($pageurl, [
                        'action' => 'removemember',
                        'id' => $existingcommittee->id,
                        'userid' => $member->userid,
                        'sesskey' => sesskey(),
                    ]))->out(false),
                ];
            }

            $data['existingcommittee'] = [
                'id' => $existingcommittee->id,
                'name' => format_string($existingcommittee->name),
                'membercount' => count($existingcommittee->members),
                'members' => $membersdata,
                'hasmembers' => !empty($membersdata),
            ];

            // Faculty vacancies.
            $facultyvacancies = $DB->get_records(
                'local_jobboard_vacancy',
                ['companyid' => $companyid, 'status' => 'published'],
                'code ASC',
                'id, code, title, status'
            );

            $vacanciesdata = [];
            foreach ($facultyvacancies as $v) {
                $vacanciesdata[] = [
                    'id' => $v->id,
                    'code' => $v->code,
                    'title' => format_string($v->title),
                    'status' => $v->status,
                    'statuslabel' => get_string('status:' . $v->status, 'local_jobboard'),
                    'statuscolor' => $this->get_status_class($v->status),
                ];
            }
            $data['facultyvacancies'] = $vacanciesdata;
            $data['hasfacultyvacancies'] = !empty($vacanciesdata);
        }

        return $data;
    }

    /**
     * Prepare committee vacancy view data (legacy).
     *
     * @param int $vacancyid Vacancy ID.
     * @param moodle_url $pageurl Base page URL.
     * @return array Template data.
     */
    protected function prepare_committee_vacancy_data(int $vacancyid, moodle_url $pageurl): array
    {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid], '*', MUST_EXIST);
        $existingcommittee = \local_jobboard\committee::get_for_vacancy($vacancyid);

        $data = [
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => $vacancy->code,
                'title' => format_string($vacancy->title),
                'status' => $vacancy->status,
                'statuslabel' => get_string('status:' . $vacancy->status, 'local_jobboard'),
                'statuscolor' => $this->get_status_class($vacancy->status),
                'companyid' => $vacancy->companyid,
            ],
            'hasexistingcommittee' => !empty($existingcommittee),
        ];

        if ($existingcommittee) {
            $data['existingcommittee'] = [
                'id' => $existingcommittee->id,
                'name' => format_string($existingcommittee->name),
                'membercount' => count($existingcommittee->members),
            ];
        }

        return $data;
    }



    /**
     * Get validation checklist items for a document type.
     *
     * @param string $doctype Document type code.
     * @return array Checklist items.
     */
    protected function get_validation_checklist(string $doctype): array
    {
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
