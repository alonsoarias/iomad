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
 * Reports renderer for Job Board plugin.
 *
 * Handles rendering of report pages, charts, and data visualizations.
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
 * Reports renderer class.
 *
 * Responsible for rendering report-related UI components including
 * data tables, charts, and export interfaces.
 */
class reports_renderer extends renderer_base {

    /**
     * Render reports dashboard/index page.
     *
     * @param array $availablereports Available report types.
     * @param array $recentsnapshots Recent report snapshots.
     * @return string HTML output.
     */
    public function render_reports_index(
        array $availablereports,
        array $recentsnapshots = []
    ): string {
        $reportdata = $this->prepare_report_cards($availablereports);
        $snapshotdata = $this->prepare_snapshot_items($recentsnapshots);

        $data = [
            'reports' => $reportdata,
            'hasreports' => !empty($reportdata),
            'recentsnapshots' => $snapshotdata,
            'hassnapshots' => !empty($snapshotdata),
        ];

        return $this->render_from_template('local_jobboard/reports_index', $data);
    }

    /**
     * Prepare report card data.
     *
     * @param array $reports Available reports.
     * @return array Report cards data.
     */
    protected function prepare_report_cards(array $reports): array {
        $cards = [];
        foreach ($reports as $report) {
            $cards[] = [
                'id' => $report['id'] ?? '',
                'name' => $report['name'] ?? '',
                'description' => $report['description'] ?? '',
                'icon' => $report['icon'] ?? 'bar-chart-2',
                'url' => $this->get_url('reports', ['report' => $report['id'] ?? '']),
                'category' => $report['category'] ?? 'general',
            ];
        }
        return $cards;
    }

    /**
     * Prepare snapshot items for display.
     *
     * @param array $snapshots Recent snapshots.
     * @return array Snapshot items data.
     */
    protected function prepare_snapshot_items(array $snapshots): array {
        $items = [];
        foreach ($snapshots as $snapshot) {
            $items[] = [
                'id' => $snapshot->id ?? 0,
                'reportname' => $snapshot->reportname ?? '',
                'timecreated' => isset($snapshot->timecreated) ? $this->format_datetime($snapshot->timecreated) : '',
                'createdby' => isset($snapshot->userid) ? $this->get_user_fullname($snapshot->userid) : '',
                'downloadurl' => $this->get_url('reports', [
                    'action' => 'download',
                    'snapshotid' => $snapshot->id ?? 0,
                ]),
            ];
        }
        return $items;
    }

    /**
     * Render applications report.
     *
     * @param array $data Report data.
     * @param array $filters Current filters.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_applications_report(
        array $data,
        array $filters = [],
        string $pagination = ''
    ): string {
        $rows = $this->prepare_application_report_rows($data['rows'] ?? []);
        $summary = $this->prepare_report_summary($data['summary'] ?? []);

        $templatedata = [
            'title' => get_string('report:applications', 'local_jobboard'),
            'rows' => $rows,
            'hasrows' => !empty($rows),
            'summary' => $summary,
            'filters' => $this->prepare_report_filters($filters),
            'pagination' => $pagination,
            'exporturls' => $this->get_export_urls('applications', $filters),
            'chartdata' => $this->prepare_chart_data($data['chart'] ?? []),
            'haschart' => !empty($data['chart']),
        ];

        return $this->render_from_template('local_jobboard/report_applications', $templatedata);
    }

    /**
     * Prepare application report rows.
     *
     * @param array $rows Raw row data.
     * @return array Formatted rows.
     */
    protected function prepare_application_report_rows(array $rows): array {
        $formatted = [];
        foreach ($rows as $row) {
            $formatted[] = [
                'applicationid' => $row->id ?? 0,
                'applicantname' => isset($row->userid) ? $this->get_user_fullname($row->userid) : '',
                'vacancycode' => $row->vacancycode ?? '',
                'vacancytitle' => $row->vacancytitle ?? '',
                'status' => $row->status ?? '',
                'statusclass' => $this->get_application_status_class($row->status ?? ''),
                'statuslabel' => get_string('appstatus:' . ($row->status ?? 'draft'), 'local_jobboard'),
                'timecreated' => isset($row->timecreated) ? $this->format_datetime($row->timecreated) : '',
                'documentcount' => $row->documentcount ?? 0,
                'viewurl' => $this->get_url('application', ['id' => $row->id ?? 0]),
            ];
        }
        return $formatted;
    }

    /**
     * Render vacancies report.
     *
     * @param array $data Report data.
     * @param array $filters Current filters.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_vacancies_report(
        array $data,
        array $filters = [],
        string $pagination = ''
    ): string {
        $rows = $this->prepare_vacancy_report_rows($data['rows'] ?? []);
        $summary = $this->prepare_report_summary($data['summary'] ?? []);

        $templatedata = [
            'title' => get_string('report:vacancies', 'local_jobboard'),
            'rows' => $rows,
            'hasrows' => !empty($rows),
            'summary' => $summary,
            'filters' => $this->prepare_report_filters($filters),
            'pagination' => $pagination,
            'exporturls' => $this->get_export_urls('vacancies', $filters),
            'chartdata' => $this->prepare_chart_data($data['chart'] ?? []),
            'haschart' => !empty($data['chart']),
        ];

        return $this->render_from_template('local_jobboard/report_vacancies', $templatedata);
    }

    /**
     * Prepare vacancy report rows.
     *
     * @param array $rows Raw row data.
     * @return array Formatted rows.
     */
    protected function prepare_vacancy_report_rows(array $rows): array {
        $formatted = [];
        foreach ($rows as $row) {
            $formatted[] = [
                'vacancyid' => $row->id ?? 0,
                'code' => $row->code ?? '',
                'title' => $row->title ?? '',
                'status' => $row->status ?? '',
                'statusclass' => $this->get_status_class($row->status ?? ''),
                'statuslabel' => get_string('vacancystatus:' . ($row->status ?? 'draft'), 'local_jobboard'),
                'positions' => $row->positions ?? 0,
                'applicationcount' => $row->applicationcount ?? 0,
                'closedate' => isset($row->closedate) ? $this->format_date($row->closedate) : '',
                'conversionrate' => $this->calculate_conversion_rate($row),
                'viewurl' => $this->get_url('vacancy', ['id' => $row->id ?? 0]),
            ];
        }
        return $formatted;
    }

    /**
     * Calculate conversion rate for a vacancy.
     *
     * @param object $row Vacancy row data.
     * @return string Formatted conversion rate.
     */
    protected function calculate_conversion_rate($row): string {
        $applications = $row->applicationcount ?? 0;
        $hired = $row->hiredcount ?? 0;

        if ($applications <= 0) {
            return '0%';
        }

        return round(($hired / $applications) * 100, 1) . '%';
    }

    /**
     * Render document validation report.
     *
     * @param array $data Report data.
     * @param array $filters Current filters.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_document_report(
        array $data,
        array $filters = [],
        string $pagination = ''
    ): string {
        $rows = $this->prepare_document_report_rows($data['rows'] ?? []);
        $summary = $this->prepare_report_summary($data['summary'] ?? []);

        $templatedata = [
            'title' => get_string('report:documents', 'local_jobboard'),
            'rows' => $rows,
            'hasrows' => !empty($rows),
            'summary' => $summary,
            'filters' => $this->prepare_report_filters($filters),
            'pagination' => $pagination,
            'exporturls' => $this->get_export_urls('documents', $filters),
            'chartdata' => $this->prepare_chart_data($data['chart'] ?? []),
            'haschart' => !empty($data['chart']),
        ];

        return $this->render_from_template('local_jobboard/report_documents', $templatedata);
    }

    /**
     * Prepare document report rows.
     *
     * @param array $rows Raw row data.
     * @return array Formatted rows.
     */
    protected function prepare_document_report_rows(array $rows): array {
        $formatted = [];
        foreach ($rows as $row) {
            $formatted[] = [
                'documentid' => $row->id ?? 0,
                'doctypename' => $row->doctypename ?? '',
                'applicantname' => isset($row->userid) ? $this->get_user_fullname($row->userid) : '',
                'validationstatus' => $row->validationstatus ?? '',
                'statusclass' => $this->get_document_status_class($row->validationstatus ?? ''),
                'statuslabel' => get_string('docstatus:' . ($row->validationstatus ?? 'pending'), 'local_jobboard'),
                'timecreated' => isset($row->timecreated) ? $this->format_datetime($row->timecreated) : '',
                'validateddate' => isset($row->timevalidated) ? $this->format_datetime($row->timevalidated) : '',
                'validatedby' => isset($row->validatedby) ? $this->get_user_fullname($row->validatedby) : '',
            ];
        }
        return $formatted;
    }

    /**
     * Render convocatoria report.
     *
     * @param array $data Report data.
     * @param array $filters Current filters.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_convocatoria_report(
        array $data,
        array $filters = [],
        string $pagination = ''
    ): string {
        $rows = $this->prepare_convocatoria_report_rows($data['rows'] ?? []);
        $summary = $this->prepare_report_summary($data['summary'] ?? []);

        $templatedata = [
            'title' => get_string('report:convocatorias', 'local_jobboard'),
            'rows' => $rows,
            'hasrows' => !empty($rows),
            'summary' => $summary,
            'filters' => $this->prepare_report_filters($filters),
            'pagination' => $pagination,
            'exporturls' => $this->get_export_urls('convocatorias', $filters),
            'chartdata' => $this->prepare_chart_data($data['chart'] ?? []),
            'haschart' => !empty($data['chart']),
        ];

        return $this->render_from_template('local_jobboard/report_convocatorias', $templatedata);
    }

    /**
     * Prepare convocatoria report rows.
     *
     * @param array $rows Raw row data.
     * @return array Formatted rows.
     */
    protected function prepare_convocatoria_report_rows(array $rows): array {
        $formatted = [];
        foreach ($rows as $row) {
            $formatted[] = [
                'convocatoriaid' => $row->id ?? 0,
                'code' => $row->code ?? '',
                'name' => $row->name ?? '',
                'status' => $row->status ?? '',
                'statusclass' => $this->get_convocatoria_status_class($row->status ?? ''),
                'statuslabel' => get_string('convocatoriastatus:' . ($row->status ?? 'draft'), 'local_jobboard'),
                'startdate' => isset($row->startdate) ? $this->format_date($row->startdate) : '',
                'enddate' => isset($row->enddate) ? $this->format_date($row->enddate) : '',
                'vacancycount' => $row->vacancycount ?? 0,
                'applicationcount' => $row->applicationcount ?? 0,
                'viewurl' => $this->get_url('convocatoria', ['id' => $row->id ?? 0]),
            ];
        }
        return $formatted;
    }

    /**
     * Prepare report summary data.
     *
     * @param array $summary Raw summary data.
     * @return array Formatted summary items.
     */
    protected function prepare_report_summary(array $summary): array {
        $items = [];
        foreach ($summary as $key => $value) {
            $items[] = [
                'key' => $key,
                'label' => get_string('summary:' . $key, 'local_jobboard'),
                'value' => is_numeric($value) ? number_format($value) : $value,
            ];
        }
        return $items;
    }

    /**
     * Prepare report filters for display.
     *
     * @param array $filters Current filter values.
     * @return array Filter configuration.
     */
    protected function prepare_report_filters(array $filters): array {
        return [
            'daterangefrom' => $filters['datefrom'] ?? '',
            'daterangeto' => $filters['dateto'] ?? '',
            'status' => $filters['status'] ?? '',
            'convocatoriaid' => $filters['convocatoriaid'] ?? '',
            'companyid' => $filters['companyid'] ?? '',
        ];
    }

    /**
     * Get export URLs for a report.
     *
     * @param string $reporttype Report type.
     * @param array $filters Current filters.
     * @return array Export URLs.
     */
    protected function get_export_urls(string $reporttype, array $filters): array {
        $baseparams = array_merge(['report' => $reporttype, 'action' => 'export'], $filters);

        return [
            'csv' => $this->get_url('reports', array_merge($baseparams, ['format' => 'csv'])),
            'excel' => $this->get_url('reports', array_merge($baseparams, ['format' => 'excel'])),
            'pdf' => $this->get_url('reports', array_merge($baseparams, ['format' => 'pdf'])),
        ];
    }

    /**
     * Prepare chart data for JavaScript.
     *
     * @param array $chartconfig Chart configuration.
     * @return array|string Chart data for template.
     */
    protected function prepare_chart_data(array $chartconfig): array {
        if (empty($chartconfig)) {
            return [];
        }

        return [
            'type' => $chartconfig['type'] ?? 'bar',
            'labels' => json_encode($chartconfig['labels'] ?? []),
            'datasets' => json_encode($chartconfig['datasets'] ?? []),
            'options' => json_encode($chartconfig['options'] ?? []),
        ];
    }

    /**
     * Render report filter form.
     *
     * @param string $reporttype Report type.
     * @param array $filters Current filter values.
     * @return string HTML output.
     */
    public function render_report_filter_form(string $reporttype, array $filters = []): string {
        global $DB;

        // Get convocatoria options.
        $convocatorias = $DB->get_records('local_jobboard_convocatoria', null, 'name ASC');
        $convocatoriaoptions = [];
        foreach ($convocatorias as $conv) {
            $convocatoriaoptions[] = [
                'value' => $conv->id,
                'label' => $conv->code . ' - ' . $conv->name,
                'selected' => ($filters['convocatoriaid'] ?? '') == $conv->id,
            ];
        }

        $data = [
            'actionurl' => $this->get_url('reports', ['report' => $reporttype]),
            'reporttype' => $reporttype,
            'datefrom' => $filters['datefrom'] ?? '',
            'dateto' => $filters['dateto'] ?? '',
            'convocatorias' => $convocatoriaoptions,
            'hasconvocatorias' => !empty($convocatoriaoptions),
            'status' => $filters['status'] ?? '',
            'statusoptions' => $this->get_status_options_for_report($reporttype, $filters['status'] ?? ''),
        ];

        return $this->render_from_template('local_jobboard/report_filter_form', $data);
    }

    /**
     * Get status options for a report type.
     *
     * @param string $reporttype Report type.
     * @param string $selected Currently selected value.
     * @return array Status options.
     */
    protected function get_status_options_for_report(string $reporttype, string $selected = ''): array {
        $statuses = [];

        switch ($reporttype) {
            case 'applications':
                $statuses = ['draft', 'submitted', 'reviewing', 'approved', 'rejected', 'withdrawn', 'hired'];
                $prefix = 'appstatus';
                break;
            case 'vacancies':
                $statuses = ['draft', 'published', 'closed', 'archived'];
                $prefix = 'vacancystatus';
                break;
            case 'documents':
                $statuses = ['pending', 'approved', 'rejected', 'expired'];
                $prefix = 'docstatus';
                break;
            case 'convocatorias':
                $statuses = ['draft', 'open', 'closed', 'archived'];
                $prefix = 'convocatoriastatus';
                break;
            default:
                return [];
        }

        $options = [];
        foreach ($statuses as $status) {
            $options[] = [
                'value' => $status,
                'label' => get_string($prefix . ':' . $status, 'local_jobboard'),
                'selected' => $selected === $status,
            ];
        }

        return $options;
    }

    /**
     * Render summary statistics row.
     *
     * @param array $stats Statistics to display.
     * @return string HTML output.
     */
    public function render_summary_stats(array $stats): string {
        $items = [];
        foreach ($stats as $key => $stat) {
            $items[] = [
                'label' => $stat['label'] ?? get_string('stat:' . $key, 'local_jobboard'),
                'value' => $stat['value'] ?? 0,
                'icon' => $stat['icon'] ?? 'activity',
                'type' => $stat['type'] ?? 'info',
                'change' => $stat['change'] ?? null,
                'haschange' => isset($stat['change']),
                'changepositive' => ($stat['change'] ?? 0) > 0,
            ];
        }

        return $this->render_from_template('local_jobboard/summary_stats', [
            'items' => $items,
            'hasitems' => !empty($items),
        ]);
    }

    /**
     * Render data table with sorting and pagination.
     *
     * @param array $columns Table columns.
     * @param array $rows Table rows.
     * @param string $sortby Current sort column.
     * @param string $sortorder Current sort order.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_data_table(
        array $columns,
        array $rows,
        string $sortby = '',
        string $sortorder = 'asc',
        string $pagination = ''
    ): string {
        $columndata = [];
        foreach ($columns as $key => $column) {
            $columndata[] = [
                'key' => $key,
                'label' => $column['label'] ?? $key,
                'sortable' => $column['sortable'] ?? false,
                'issorted' => $sortby === $key,
                'sorturl' => $this->get_sort_url($key, $sortby, $sortorder),
                'sorticon' => $this->get_sort_icon($key, $sortby, $sortorder),
            ];
        }

        return $this->render_from_template('local_jobboard/data_table', [
            'columns' => $columndata,
            'rows' => $rows,
            'hasrows' => !empty($rows),
            'pagination' => $pagination,
        ]);
    }

    /**
     * Get sort URL for a column.
     *
     * @param string $column Column key.
     * @param string $currentsort Current sort column.
     * @param string $currentorder Current sort order.
     * @return string Sort URL.
     */
    protected function get_sort_url(string $column, string $currentsort, string $currentorder): string {
        $neworder = ($column === $currentsort && $currentorder === 'asc') ? 'desc' : 'asc';
        return $this->get_url('reports', ['sortby' => $column, 'sortorder' => $neworder]);
    }

    /**
     * Get sort icon for a column.
     *
     * @param string $column Column key.
     * @param string $currentsort Current sort column.
     * @param string $currentorder Current sort order.
     * @return string Icon name.
     */
    protected function get_sort_icon(string $column, string $currentsort, string $currentorder): string {
        if ($column !== $currentsort) {
            return 'chevrons-up-down';
        }
        return $currentorder === 'asc' ? 'chevron-up' : 'chevron-down';
    }

    /**
     * Render chart container.
     *
     * @param string $chartid Unique chart ID.
     * @param string $type Chart type.
     * @param array $data Chart data.
     * @param array $options Chart options.
     * @param string $title Chart title.
     * @return string HTML output.
     */
    public function render_chart(
        string $chartid,
        string $type,
        array $data,
        array $options = [],
        string $title = ''
    ): string {
        return $this->render_from_template('local_jobboard/chart', [
            'chartid' => $chartid,
            'type' => $type,
            'data' => json_encode($data),
            'options' => json_encode($options),
            'title' => $title,
            'hastitle' => !empty($title),
        ]);
    }
}
