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
 * Reports renderer trait for Job Board plugin.
 *
 * Contains all reporting and analytics rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Trait for reports rendering functionality.
 */
trait reports_renderer {

    /**
     * Render reports page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_reports_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/reports', $data);
    }

    /**
     * Prepare reports page data for template.
     *
     * @param string $reporttype Current report type.
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @param array $vacancies Array of vacancy options for filter.
     * @param \context $context Current context.
     * @return array Template data.
     */
    public function prepare_reports_page_data(
        string $reporttype,
        int $vacancyid,
        int $datefrom,
        int $dateto,
        array $vacancies,
        \context $context
    ): array {
        // Base URLs.
        $baseurl = new \moodle_url('/local/jobboard/index.php', [
            'view' => 'reports',
            'report' => $reporttype,
            'vacancyid' => $vacancyid,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
        ]);

        // Report type tabs.
        $reporttypes = [
            'overview' => ['label' => get_string('reportoverview', 'local_jobboard'), 'icon' => 'chart-pie'],
            'applications' => ['label' => get_string('reportapplications', 'local_jobboard'), 'icon' => 'file-alt'],
            'documents' => ['label' => get_string('reportdocuments', 'local_jobboard'), 'icon' => 'folder-open'],
            'reviewers' => ['label' => get_string('reportreviewers', 'local_jobboard'), 'icon' => 'user-check'],
            'timeline' => ['label' => get_string('reporttimeline', 'local_jobboard'), 'icon' => 'calendar-alt'],
        ];

        $tabs = [];
        foreach ($reporttypes as $type => $info) {
            $tabs[] = [
                'type' => $type,
                'label' => $info['label'],
                'icon' => $info['icon'],
                'url' => (new \moodle_url('/local/jobboard/index.php', [
                    'view' => 'reports',
                    'report' => $type,
                    'vacancyid' => $vacancyid,
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                ]))->out(false),
                'isactive' => ($reporttype === $type),
            ];
        }

        // Export links.
        $exportlinks = [
            ['url' => (new \moodle_url($baseurl, ['format' => 'csv']))->out(false), 'label' => 'CSV', 'icon' => 'file-csv', 'color' => 'secondary'],
            ['url' => (new \moodle_url($baseurl, ['format' => 'excel']))->out(false), 'label' => 'Excel', 'icon' => 'file-excel', 'color' => 'success'],
            ['url' => (new \moodle_url($baseurl, ['format' => 'pdf']))->out(false), 'label' => 'PDF', 'icon' => 'file-pdf', 'color' => 'danger'],
        ];

        // Vacancy filter options.
        $vacancyoptions = [];
        foreach ($vacancies as $v) {
            $vacancyoptions[] = [
                'id' => $v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($v->id == $vacancyid),
            ];
        }

        // Base data.
        $data = [
            'pagetitle' => get_string('reports', 'local_jobboard'),
            'reporttypes' => $tabs,
            'currentreport' => $reporttype,
            'filteraction' => (new \moodle_url('/local/jobboard/index.php'))->out(false),
            'vacancies' => $vacancyoptions,
            'datefrom' => date('Y-m-d', $datefrom),
            'dateto' => date('Y-m-d', $dateto),
            'exportlinks' => $exportlinks,
            'hasdata' => false,
            'dashboardurl' => (new \moodle_url('/local/jobboard/index.php'))->out(false),
            'manageurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'manage']))->out(false),
            'bulkvalidateurl' => (new \moodle_url('/local/jobboard/admin/bulk_validate.php'))->out(false),
            'caps' => [
                'viewallapplications' => has_capability('local/jobboard:viewallapplications', $context),
                'reviewdocuments' => has_capability('local/jobboard:reviewdocuments', $context),
            ],
            // Report type flags.
            'isoverview' => false,
            'isapplications' => false,
            'isdocuments' => false,
            'isreviewers' => false,
            'istimeline' => false,
            // Report data.
            'overview' => [],
            'applications' => [],
            'documents' => [],
            'reviewers' => [],
            'timeline' => [],
        ];

        // Prepare specific report data.
        switch ($reporttype) {
            case 'overview':
                $data['isoverview'] = true;
                $data['overview'] = $this->prepare_overview_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = true;
                break;

            case 'applications':
                $data['isapplications'] = true;
                $data['applications'] = $this->prepare_applications_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = !empty($data['applications']['rows']);
                break;

            case 'documents':
                $data['isdocuments'] = true;
                $data['documents'] = $this->prepare_documents_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = true;
                break;

            case 'reviewers':
                $data['isreviewers'] = true;
                $data['reviewers'] = $this->prepare_reviewers_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = true;
                break;

            case 'timeline':
                $data['istimeline'] = true;
                $data['timeline'] = $this->prepare_timeline_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = !empty($data['timeline']['rows']);
                break;
        }

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
    protected function prepare_overview_report_data(int $vacancyid, int $datefrom, int $dateto): array {
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
    protected function get_application_status_color(string $status): string {
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
    protected function prepare_applications_report_data(int $vacancyid, int $datefrom, int $dateto): array {
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
    protected function prepare_documents_report_data(int $vacancyid, int $datefrom, int $dateto): array {
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
    protected function prepare_reviewers_report_data(int $vacancyid, int $datefrom, int $dateto): array {
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
    protected function prepare_timeline_report_data(int $vacancyid, int $datefrom, int $dateto): array {
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
}
