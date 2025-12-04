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
 * Enhanced Excel exporter with charts using PHPSpreadsheet.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_platform_usage;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/phpspreadsheet/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

/**
 * Class for exporting reports to Excel with embedded charts.
 */
class excel_exporter {

    /** @var report Report instance */
    protected $report;

    /** @var Spreadsheet Spreadsheet instance */
    protected $spreadsheet;

    /** @var array Style definitions */
    protected $styles;

    /** @var array Sheet names for internal use */
    protected $sheetNames;

    /**
     * Constructor.
     *
     * @param report $report Report instance
     */
    public function __construct(report $report) {
        $this->report = $report;
        $this->spreadsheet = new Spreadsheet();
        $this->initStyles();
        $this->initSheetNames();
    }

    /**
     * Initialize sheet names using language strings for proper localization.
     * Names are sanitized to comply with Excel's 31-character limit.
     */
    protected function initSheetNames(): void {
        $this->sheetNames = [
            // Platform context sheets.
            'summary' => $this->sanitizeSheetName(get_string('sheet_summary', 'report_platform_usage')),
            'daily' => $this->sanitizeSheetName(get_string('sheet_daily_logins', 'report_platform_usage')),
            'dailyusers' => $this->sanitizeSheetName(get_string('sheet_daily_users', 'report_platform_usage')),
            'completions' => $this->sanitizeSheetName(get_string('sheet_completions', 'report_platform_usage')),
            'courses' => $this->sanitizeSheetName(get_string('sheet_courses', 'report_platform_usage')),
            'activities' => $this->sanitizeSheetName(get_string('sheet_activities', 'report_platform_usage')),
            'users' => $this->sanitizeSheetName(get_string('sheet_users', 'report_platform_usage')),
            'dedication' => $this->sanitizeSheetName(get_string('sheet_dedication', 'report_platform_usage')),
            // Course context sheets.
            'course_summary' => $this->sanitizeSheetName(get_string('sheet_course_summary', 'report_platform_usage')),
            'enrolled_users' => $this->sanitizeSheetName(get_string('sheet_enrolled_users', 'report_platform_usage')),
            'access_history' => $this->sanitizeSheetName(get_string('sheet_access_history', 'report_platform_usage')),
            'course_activities' => $this->sanitizeSheetName(get_string('sheet_course_activities', 'report_platform_usage')),
            'course_completions' => $this->sanitizeSheetName(get_string('sheet_course_completions', 'report_platform_usage')),
            'course_dedication' => $this->sanitizeSheetName(get_string('sheet_course_dedication', 'report_platform_usage')),
        ];
    }

    /**
     * Sanitize sheet name for Excel compatibility.
     * Excel sheet names have a 31 character limit and cannot contain certain characters.
     *
     * @param string $name The sheet name to sanitize
     * @return string The sanitized sheet name
     */
    protected function sanitizeSheetName(string $name): string {
        // Remove invalid characters: \ / ? * [ ] :
        $name = preg_replace('/[\\\\\\/\\?\\*\\[\\]\\:]/', '', $name);
        // Trim and limit to 31 characters.
        $name = mb_substr(trim($name), 0, 31);
        return $name;
    }

    /**
     * Format time in seconds to a human-readable string.
     *
     * @param int $seconds Time in seconds
     * @return string Formatted time string (e.g., "1 hour", "30 mins")
     */
    protected function formatTimeForDisplay(int $seconds): string {
        if ($seconds >= HOURSECS) {
            $hours = floor($seconds / HOURSECS);
            return $hours . ' ' . ($hours == 1 ? get_string('hour') : get_string('hours'));
        } else if ($seconds >= MINSECS) {
            $mins = floor($seconds / MINSECS);
            return $mins . ' ' . ($mins == 1 ? get_string('min') : get_string('mins'));
        } else {
            return $seconds . ' ' . ($seconds == 1 ? get_string('sec') : get_string('secs'));
        }
    }

    /**
     * Initialize style definitions.
     */
    protected function initStyles(): void {
        $this->styles = [
            'header' => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            'title' => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => '2F5496'],
                ],
            ],
            'subtitle' => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => '4472C4'],
                ],
            ],
            'data' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'highlight' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA'],
                ],
            ],
            'number' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            'description' => [
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '666666'],
                    'size' => 10,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA'],
                ],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
            'section_header' => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => '495057'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF'],
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '4472C4'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Export complete report to Excel.
     */
    public function export(): void {
        // Remove default sheet.
        $this->spreadsheet->removeSheetByIndex(0);

        // Create sheets based on context.
        if ($this->report->is_course_context()) {
            // Course-specific export with detailed user data.
            $this->createCourseSummarySheet();
            $this->createCourseUsersDetailSheet();
            $this->createCourseAccessHistorySheet();
            $this->createCourseActivitiesSheet();
            $this->createCourseCompletionsSheet();
            $this->createCourseDedicationSheet();
        } else {
            // Platform-wide export.
            $this->createSummarySheet();
            $this->createDailyLoginsSheet();
            $this->createDailyUsersSheet();
            $this->createCompletionsSheet();
            $this->createCoursesSheet();
            $this->createActivitiesSheet();
            $this->createDedicationSheet();
            $this->createUsersSheet();
        }

        // Set first sheet as active.
        $this->spreadsheet->setActiveSheetIndex(0);

        // Set document properties.
        $title = $this->report->is_course_context()
            ? get_string('coursereport', 'report_platform_usage')
            : get_string('reporttitle', 'report_platform_usage');

        $this->spreadsheet->getProperties()
            ->setCreator('IOMAD Platform')
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription('Platform usage report generated by IOMAD');

        // Output file.
        $prefix = $this->report->is_course_context() ? 'course_usage_report_' : 'platform_usage_report_';
        $filename = $prefix . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save('php://output');
        exit;
    }

    /**
     * Create summary sheet with overview and charts.
     */
    protected function createSummarySheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['summary']);

        // Report header - different title for course context.
        if ($this->report->is_course_context()) {
            $sheet->setCellValue('A1', get_string('coursereport', 'report_platform_usage'));
        } else {
            $sheet->setCellValue('A1', get_string('reporttitle', 'report_platform_usage'));
        }
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:F1');

        // Report description.
        $sheet->setCellValue('A2', get_string('export_report_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:F2');
        $sheet->getRowDimension(2)->setRowHeight(30);

        // Context info - company or course.
        $infoRow = 3;
        if ($this->report->is_course_context()) {
            $course = get_course($this->report->get_course_id());
            $sheet->setCellValue("A{$infoRow}", get_string('coursename', 'report_platform_usage') . ': ' . format_string($course->fullname));
            $infoRow++;
        } else if (report::is_iomad_installed()) {
            // Only show company filter if IOMAD is installed.
            $sheet->setCellValue("A{$infoRow}", get_string('filter_company', 'report_platform_usage') . ': ' . $this->report->get_company_name());
            $infoRow++;
        }
        $sheet->setCellValue("A{$infoRow}", get_string('filter_daterange', 'report_platform_usage') . ': ' . $this->report->get_date_range());
        $infoRow++;
        $sheet->setCellValue("A{$infoRow}", get_string('generateddate', 'report_platform_usage') . ': ' . userdate(time(), '%d/%m/%Y %H:%M'));

        // Login summary section.
        $row = 7;
        $sheet->setCellValue("A{$row}", get_string('loginsummary', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells("A{$row}:C{$row}");

        // Login section description.
        $row++;
        $sheet->setCellValue("A{$row}", get_string('export_logins_desc', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['description']);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getRowDimension($row)->setRowHeight(40);

        $row++;
        $headers = [
            get_string('period', 'report_platform_usage'),
            get_string('logins', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->styles['header']);

        $summary = $this->report->get_login_summary();
        $dataRows = [
            [get_string('totallogins', 'report_platform_usage'), $summary['total_logins'], ''],
            [get_string('uniqueusers', 'report_platform_usage'), $summary['unique_users'], ''],
            [get_string('avgperday', 'report_platform_usage'), $summary['avg_logins_per_day'], ''],
            [get_string('avgperuser', 'report_platform_usage'), $summary['avg_logins_per_user'], ''],
        ];

        $row++;
        $loginDataStart = $row;
        foreach ($dataRows as $dataRow) {
            $sheet->fromArray($dataRow, null, "A{$row}");
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("B{$row}:C{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }
        $loginDataEnd = $row - 1;

        // User summary section.
        $row += 2;
        $sheet->setCellValue("A{$row}", get_string('usersummary', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells("A{$row}:C{$row}");

        // User section description.
        $row++;
        $sheet->setCellValue("A{$row}", get_string('export_users_desc', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['description']);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getRowDimension($row)->setRowHeight(40);

        $userSummary = $this->report->get_user_activity_summary();
        $row++;
        $sheet->setCellValue("A{$row}", get_string('totalusers', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $userSummary['total']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('activeusers', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $userSummary['active']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['highlight']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('inactiveusers', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $userSummary['inactive']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);

        // Create login summary bar chart.
        $this->createLoginSummaryChart($sheet, $loginDataStart, $loginDataEnd);

        // Create user activity pie chart.
        $this->createUserActivityChart($sheet, $userSummary);

        // Completions section.
        $row += 3;
        $sheet->setCellValue("A{$row}", get_string('completions', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells("A{$row}:C{$row}");

        $row++;
        $sheet->setCellValue("A{$row}", get_string('export_completions_desc', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['description']);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getRowDimension($row)->setRowHeight(40);

        $completions = $this->report->get_course_completions_summary();
        $row++;
        $sheet->setCellValue("A{$row}", get_string('totalcompletions', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $completions['total_completions']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('uniquecourses', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $completions['unique_courses']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('avgperday', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $completions['avg_per_day']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);

        // Top 5 courses section.
        $row += 3;
        $sheet->setCellValue("A{$row}", get_string('topcourses', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells("A{$row}:D{$row}");

        $row++;
        $headers = [
            get_string('coursename', 'report_platform_usage'),
            get_string('shortname', 'report_platform_usage'),
            get_string('courseaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($this->styles['header']);

        $courses = $this->report->get_top_courses(5);
        $row++;
        foreach ($courses as $course) {
            $sheet->fromArray([
                $course->fullname,
                $course->shortname,
                $course->access_count,
                $course->unique_users,
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("C{$row}:D{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }

        // Top 5 activities section.
        $row += 2;
        $sheet->setCellValue("A{$row}", get_string('topactivities', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells("A{$row}:E{$row}");

        $row++;
        $headers = [
            get_string('activityname', 'report_platform_usage'),
            get_string('activitytype', 'report_platform_usage'),
            get_string('coursename', 'report_platform_usage'),
            get_string('courseaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray($this->styles['header']);

        $activities = $this->report->get_top_activities(5);
        $row++;
        foreach ($activities as $activity) {
            $sheet->fromArray([
                $activity->name,
                $activity->type_name,
                $activity->course_name,
                $activity->access_count,
                $activity->unique_users,
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("D{$row}:E{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }

        // Data interpretation notes section.
        $row += 3;
        $sheet->setCellValue("A{$row}", get_string('export_data_notes', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells("A{$row}:F{$row}");

        $row++;
        $notes = [
            get_string('export_login_note', 'report_platform_usage'),
            get_string('export_active_note', 'report_platform_usage'),
            get_string('export_completion_note', 'report_platform_usage'),
        ];
        foreach ($notes as $note) {
            $sheet->setCellValue("A{$row}", '• ' . $note);
            $sheet->getStyle("A{$row}")->applyFromArray($this->styles['description']);
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        // Auto-size columns.
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create login summary bar chart.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet
     * @param int $dataStart Start row
     * @param int $dataEnd End row
     */
    protected function createLoginSummaryChart($sheet, int $dataStart, int $dataEnd): void {
        $sheetName = $this->sheetNames['summary'];
        // Header row is 2 rows before data start (row 9 when data starts at row 10).
        $headerRow = $dataStart - 1;
        // Data series for logins.
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$B\${$headerRow}", null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$C\${$headerRow}", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, 3),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, 3),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$C\${$dataStart}:\$C\${$dataEnd}", null, 3),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title(get_string('loginsummary', 'report_platform_usage'));

        $chart = new Chart(
            'loginSummaryChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('E6');
        $chart->setBottomRightPosition('L18');

        $sheet->addChart($chart);
    }

    /**
     * Create user activity pie chart.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet
     * @param array $userSummary User summary data
     */
    protected function createUserActivityChart($sheet, array $userSummary): void {
        $sheetName = $this->sheetNames['summary'];
        // Create temporary data for pie chart.
        $sheet->setCellValue('N6', get_string('activeusers', 'report_platform_usage'));
        $sheet->setCellValue('O6', $userSummary['active']);
        $sheet->setCellValue('N7', get_string('inactiveusers', 'report_platform_usage'));
        $sheet->setCellValue('O7', $userSummary['inactive']);
        $sheet->getColumnDimension('N')->setVisible(false);
        $sheet->getColumnDimension('O')->setVisible(false);

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$N\$6:\$N\$7", null, 2),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$N\$6:\$N\$7", null, 2),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$O\$6:\$O\$7", null, 2),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title(get_string('usersummary', 'report_platform_usage'));

        $chart = new Chart(
            'userActivityChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('E19');
        $chart->setBottomRightPosition('L31');

        $sheet->addChart($chart);
    }

    /**
     * Create daily logins sheet with chart.
     */
    protected function createDailyLoginsSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['daily']);

        // Header.
        $sheet->setCellValue('A1', get_string('dailylogins', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:C1');

        // Description.
        $sheet->setCellValue('A2', get_string('export_daily_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:C2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Column headers.
        $headers = [
            get_string('date', 'report_platform_usage'),
            get_string('logins', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:C4')->applyFromArray($this->styles['header']);

        // Get daily data.
        $dailyData = $this->report->get_daily_logins(30);
        $row = 5;
        $dataStart = $row;

        for ($i = 0; $i < count($dailyData['labels']); $i++) {
            $sheet->fromArray([
                $dailyData['labels'][$i],
                $dailyData['logins'][$i],
                $dailyData['unique_users'][$i],
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("B{$row}:C{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }
        $dataEnd = $row - 1;

        // Create line chart.
        $this->createDailyLoginsChart($sheet, $dataStart, $dataEnd);

        // Auto-size columns.
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create daily logins line chart.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet
     * @param int $dataStart Start row
     * @param int $dataEnd End row
     */
    protected function createDailyLoginsChart($sheet, int $dataStart, int $dataEnd): void {
        $sheetName = $this->sheetNames['daily'];

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$B\$4", null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$C\$4", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $dataEnd - $dataStart + 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$C\${$dataStart}:\$C\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title(get_string('dailylogins', 'report_platform_usage'));

        $chart = new Chart(
            'dailyLoginsChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('E3');
        $chart->setBottomRightPosition('O20');

        $sheet->addChart($chart);
    }

    /**
     * Create daily users sheet with chart.
     */
    protected function createDailyUsersSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['dailyusers']);

        // Header.
        $sheet->setCellValue('A1', get_string('dailyusers', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:B1');

        // Description.
        $sheet->setCellValue('A2', get_string('export_dailyusers_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:B2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Column headers.
        $headers = [
            get_string('date', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:B4')->applyFromArray($this->styles['header']);

        // Get daily users data.
        $dailyUsers = $this->report->get_daily_users(30);
        $row = 5;
        $dataStart = $row;

        for ($i = 0; $i < count($dailyUsers['labels']); $i++) {
            $sheet->fromArray([
                $dailyUsers['labels'][$i],
                $dailyUsers['data'][$i],
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("B{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }
        $dataEnd = $row - 1;

        // Add statistics.
        $totalUsers = array_sum($dailyUsers['data']);
        $avgUsers = count($dailyUsers['data']) > 0 ? round($totalUsers / count($dailyUsers['data']), 1) : 0;
        $maxUsers = !empty($dailyUsers['data']) ? max($dailyUsers['data']) : 0;

        $row += 2;
        $sheet->setCellValue("A{$row}", get_string('average', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $avgUsers);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('maximum', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $maxUsers);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['highlight']);

        // Create line chart.
        if ($dataEnd >= $dataStart) {
            $this->createDailyUsersChart($sheet, $dataStart, $dataEnd);
        }

        // Auto-size columns.
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create daily users line chart.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet
     * @param int $dataStart Start row
     * @param int $dataEnd End row
     */
    protected function createDailyUsersChart($sheet, int $dataStart, int $dataEnd): void {
        $sheetName = $this->sheetNames['dailyusers'];

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$B\$4", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title(get_string('dailyusers', 'report_platform_usage'));

        $chart = new Chart(
            'dailyUsersChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('D3');
        $chart->setBottomRightPosition('N18');

        $sheet->addChart($chart);
    }

    /**
     * Create completions sheet with chart.
     */
    protected function createCompletionsSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['completions']);

        // Header.
        $sheet->setCellValue('A1', get_string('completiontrends', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:B1');

        // Description.
        $sheet->setCellValue('A2', get_string('export_completions_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:B2');
        $sheet->getRowDimension(2)->setRowHeight(50);

        // Summary section.
        $sheet->setCellValue('A4', get_string('summary', 'report_platform_usage'));
        $sheet->getStyle('A4')->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells('A4:B4');

        $completions = $this->report->get_course_completions_summary();
        $row = 5;
        $sheet->setCellValue("A{$row}", get_string('totalcompletions', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $completions['total_completions']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('uniquecourses', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $completions['unique_courses']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('avgperday', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $completions['avg_per_day']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['highlight']);

        // Daily trends section.
        $row += 3;
        $sheet->setCellValue("A{$row}", get_string('completiontrends_desc', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['description']);
        $sheet->mergeCells("A{$row}:B{$row}");
        $row++;

        // Column headers.
        $headers = [
            get_string('date', 'report_platform_usage'),
            get_string('completions', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['header']);
        $row++;

        // Get completion trends data.
        $trends = $this->report->get_completion_trends(30);
        $dataStart = $row;

        for ($i = 0; $i < count($trends['labels']); $i++) {
            $sheet->fromArray([
                $trends['labels'][$i],
                $trends['data'][$i],
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("B{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }
        $dataEnd = $row - 1;

        // Create line chart.
        if ($dataEnd >= $dataStart) {
            $this->createCompletionsChart($sheet, $dataStart, $dataEnd);
        }

        // Auto-size columns.
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create completions trend line chart.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet
     * @param int $dataStart Start row
     * @param int $dataEnd End row
     */
    protected function createCompletionsChart($sheet, int $dataStart, int $dataEnd): void {
        $sheetName = $this->sheetNames['completions'];
        // Header row is 1 row before data start.
        $headerRow = $dataStart - 1;

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$B\${$headerRow}", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title(get_string('completiontrends', 'report_platform_usage'));

        $chart = new Chart(
            'completionsChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('D3');
        $chart->setBottomRightPosition('N18');

        $sheet->addChart($chart);
    }

    /**
     * Create courses sheet with details.
     */
    protected function createCoursesSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['courses']);

        // Header.
        $sheet->setCellValue('A1', get_string('courseaccessdetails', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:F1');

        // Description.
        $sheet->setCellValue('A2', get_string('export_courses_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:F2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Column headers.
        $headers = [
            get_string('coursename', 'report_platform_usage'),
            get_string('shortname', 'report_platform_usage'),
            get_string('courseaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
            get_string('lastaccess', 'report_platform_usage'),
            get_string('avgaccessperuser', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:F4')->applyFromArray($this->styles['header']);

        // Get course data.
        $courses = $this->report->get_course_access_details();
        $row = 5;
        $dataStart = $row;

        foreach ($courses as $course) {
            $avgAccess = $course->unique_users > 0 ? round($course->access_count / $course->unique_users, 2) : 0;
            $sheet->fromArray([
                $course->fullname,
                $course->shortname,
                $course->access_count,
                $course->unique_users,
                userdate($course->last_access, '%d/%m/%Y %H:%M'),
                $avgAccess,
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("C{$row}:D{$row}")->applyFromArray($this->styles['number']);
            $sheet->getStyle("F{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }
        $dataEnd = $row - 1;

        // Add totals row.
        if ($dataEnd >= $dataStart) {
            $sheet->setCellValue("A{$row}", get_string('total', 'report_platform_usage'));
            $sheet->setCellValue("C{$row}", "=SUM(C{$dataStart}:C{$dataEnd})");
            $sheet->setCellValue("D{$row}", "=SUM(D{$dataStart}:D{$dataEnd})");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($this->styles['header']);
        }

        // Create course access bar chart.
        if ($dataEnd >= $dataStart && $dataEnd - $dataStart < 20) {
            $this->createCoursesChart($sheet, $dataStart, min($dataEnd, $dataStart + 9));
        }

        // Auto-size columns.
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create courses access chart.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet
     * @param int $dataStart Start row
     * @param int $dataEnd End row
     */
    protected function createCoursesChart($sheet, int $dataStart, int $dataEnd): void {
        $sheetName = $this->sheetNames['courses'];

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$C\$4", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$C\${$dataStart}:\$C\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title(get_string('topcourses', 'report_platform_usage'));

        $chart = new Chart(
            'coursesChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('H3');
        $chart->setBottomRightPosition('O18');

        $sheet->addChart($chart);
    }

    /**
     * Create activities sheet with details.
     */
    protected function createActivitiesSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['activities']);

        // Header.
        $sheet->setCellValue('A1', get_string('activityaccessdetails', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:F1');

        // Description.
        $sheet->setCellValue('A2', get_string('export_activities_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:F2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Column headers.
        $headers = [
            get_string('activityname', 'report_platform_usage'),
            get_string('activitytype', 'report_platform_usage'),
            get_string('coursename', 'report_platform_usage'),
            get_string('courseaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
            get_string('avgaccessperuser', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:F4')->applyFromArray($this->styles['header']);

        // Get activity data.
        $activities = $this->report->get_top_activities(50);
        $row = 5;

        foreach ($activities as $activity) {
            $avgAccess = $activity->unique_users > 0 ? round($activity->access_count / $activity->unique_users, 2) : 0;
            $sheet->fromArray([
                $activity->name,
                $activity->type_name,
                $activity->course_name,
                $activity->access_count,
                $activity->unique_users,
                $avgAccess,
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("D{$row}:E{$row}")->applyFromArray($this->styles['number']);
            $sheet->getStyle("F{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }

        // Auto-size columns.
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create users sheet with details.
     */
    protected function createUsersSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['users']);

        // Header.
        $sheet->setCellValue('A1', get_string('userdetails', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:G1');

        // Description.
        $sheet->setCellValue('A2', get_string('export_users_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:G2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Column headers.
        $headers = [
            get_string('username', 'report_platform_usage'),
            get_string('fullname', 'report_platform_usage'),
            get_string('email', 'report_platform_usage'),
            get_string('logincount', 'report_platform_usage'),
            get_string('lastaccess', 'report_platform_usage'),
            get_string('created', 'report_platform_usage'),
            get_string('status', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:G4')->applyFromArray($this->styles['header']);

        // Get user data.
        $users = $this->report->get_user_login_details();
        $row = 5;
        $activeThreshold = strtotime('-30 days');

        foreach ($users as $user) {
            $isActive = $user->lastaccess >= $activeThreshold;
            $status = $isActive ? get_string('active', 'report_platform_usage') : get_string('inactive', 'report_platform_usage');

            $sheet->fromArray([
                $user->username,
                fullname($user),
                $user->email,
                $user->login_count,
                $user->lastaccess ? userdate($user->lastaccess, '%d/%m/%Y %H:%M') : '-',
                userdate($user->timecreated, '%d/%m/%Y'),
                $status,
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("D{$row}")->applyFromArray($this->styles['number']);

            if ($isActive) {
                $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB('006600');
            } else {
                $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB('CC0000');
            }

            $row++;
        }

        // Auto-size columns.
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create dedication sheet with course dedication details.
     */
    protected function createDedicationSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['dedication']);

        // Header.
        $sheet->setCellValue('A1', get_string('topdedication', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:F1');

        // Description with session timeout info.
        $sessionlimit = get_config('report_platform_usage', 'session_limit');
        $sessionlimit = !empty($sessionlimit) ? (int)$sessionlimit : HOURSECS;
        $sessionlimitFormatted = $this->formatTimeForDisplay($sessionlimit);
        $description = get_string('export_dedication_note', 'report_platform_usage', $sessionlimitFormatted);
        $sheet->setCellValue('A2', $description);
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:F2');
        $sheet->getRowDimension(2)->setRowHeight(50);

        // Column headers.
        $headers = [
            '#',
            get_string('coursename', 'report_platform_usage'),
            get_string('shortname', 'report_platform_usage'),
            get_string('totaldedication', 'report_platform_usage'),
            get_string('enrolledusers', 'report_platform_usage'),
            get_string('dedicationpercent', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:F4')->applyFromArray($this->styles['header']);

        // Get dedication data.
        $dedication = $this->report->get_top_courses_dedication(50);
        $row = 5;
        $dataStart = $row;

        foreach ($dedication as $course) {
            $sheet->fromArray([
                $course['rank'],
                $course['fullname'],
                $course['shortname'],
                $course['total_dedication_formatted'],
                $course['enrolled_students'],
                $course['dedication_percent'] . '%',
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("A{$row}")->applyFromArray($this->styles['number']);
            $sheet->getStyle("E{$row}:F{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }
        $dataEnd = $row - 1;

        // Create dedication bar chart if we have data.
        if ($dataEnd >= $dataStart && $dataEnd - $dataStart < 15) {
            $this->createDedicationChart($sheet, $dataStart, min($dataEnd, $dataStart + 9));
        }

        // Auto-size columns.
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create dedication bar chart.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet
     * @param int $dataStart Start row
     * @param int $dataEnd End row
     */
    protected function createDedicationChart($sheet, int $dataStart, int $dataEnd): void {
        $sheetName = $this->sheetNames['dedication'];

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$F\$4", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$C\${$dataStart}:\$C\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$F\${$dataStart}:\$F\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title(get_string('topdedication', 'report_platform_usage'));

        $chart = new Chart(
            'dedicationChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('H4');
        $chart->setBottomRightPosition('O18');

        $sheet->addChart($chart);
    }

    /**
     * Create course summary sheet for course context.
     */
    protected function createCourseSummarySheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['course_summary']);

        $courseStats = $this->report->get_course_statistics();
        $course = get_course($this->report->get_course_id());

        // Report header.
        $sheet->setCellValue('A1', get_string('coursereport', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:D1');

        // Course info.
        $sheet->setCellValue('A3', get_string('coursename', 'report_platform_usage') . ':');
        $sheet->setCellValue('B3', format_string($course->fullname));
        $sheet->setCellValue('A4', get_string('shortname', 'report_platform_usage') . ':');
        $sheet->setCellValue('B4', $course->shortname);
        $sheet->setCellValue('A5', get_string('filter_daterange', 'report_platform_usage') . ':');
        $sheet->setCellValue('B5', $this->report->get_date_range());
        $sheet->setCellValue('A6', get_string('generateddate', 'report_platform_usage') . ':');
        $sheet->setCellValue('B6', userdate(time(), '%d/%m/%Y %H:%M'));

        // Course metrics section.
        $row = 8;
        $sheet->setCellValue("A{$row}", get_string('coursemetrics', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $sheet->mergeCells("A{$row}:D{$row}");

        $row++;
        $headers = [
            get_string('metric', 'report_platform_usage'),
            get_string('value', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['header']);

        // Metrics data.
        $metrics = [
            [get_string('enrolledusers', 'report_platform_usage'), $courseStats['enrolled_users']],
            [get_string('activeusers', 'report_platform_usage'), $courseStats['active_users']],
            [get_string('inactiveusers', 'report_platform_usage'), $courseStats['inactive_users']],
            [get_string('courseaccesses', 'report_platform_usage'), $courseStats['accesses']],
            [get_string('totalcompletions', 'report_platform_usage'), $courseStats['completions']],
            [get_string('totaldedication', 'report_platform_usage'), $courseStats['total_dedication_formatted']],
            [get_string('avgaccessperuser', 'report_platform_usage'), $courseStats['avg_dedication_formatted']],
        ];

        $row++;
        $dataStart = $row;
        foreach ($metrics as $metric) {
            $sheet->fromArray($metric, null, "A{$row}");
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
            $row++;
        }

        // Create user activity pie chart.
        $this->createCourseUserChart($sheet, $courseStats);

        // Auto-size columns.
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create course user pie chart.
     */
    protected function createCourseUserChart($sheet, array $courseStats): void {
        // Get the actual sheet name for chart references.
        $sheetName = $this->sheetNames['course_summary'];

        // Create temporary data for pie chart.
        $sheet->setCellValue('G8', get_string('activeusers', 'report_platform_usage'));
        $sheet->setCellValue('H8', $courseStats['active_users']);
        $sheet->setCellValue('G9', get_string('inactiveusers', 'report_platform_usage'));
        $sheet->setCellValue('H9', $courseStats['inactive_users']);
        $sheet->getColumnDimension('G')->setVisible(false);
        $sheet->getColumnDimension('H')->setVisible(false);

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$G\$8:\$G\$9", null, 2),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$G\$8:\$G\$9", null, 2),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$H\$8:\$H\$9", null, 2),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title(get_string('courseenrolledusers', 'report_platform_usage'));

        $chart = new Chart(
            'courseUserChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('D8');
        $chart->setBottomRightPosition('F18');

        $sheet->addChart($chart);
    }

    /**
     * Create detailed users sheet for course context.
     */
    protected function createCourseUsersDetailSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['enrolled_users']);

        // Header.
        $sheet->setCellValue('A1', get_string('courseusersdetails', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:H1');

        // Description.
        $sheet->setCellValue('A2', get_string('courseusersdetails_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:H2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Column headers.
        $headers = [
            get_string('fullname', 'report_platform_usage'),
            get_string('username', 'report_platform_usage'),
            get_string('email', 'report_platform_usage'),
            get_string('status', 'report_platform_usage'),
            get_string('completionstatus', 'report_platform_usage'),
            get_string('lastcourseaccess', 'report_platform_usage'),
            get_string('userdedication', 'report_platform_usage'),
            get_string('lastaccess', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:H4')->applyFromArray($this->styles['header']);

        // Get user data.
        $users = $this->report->get_course_users_details();
        $row = 5;
        $completedCount = 0;
        $activeCount = 0;

        foreach ($users as $user) {
            $status = $user['is_active'] ? get_string('active', 'report_platform_usage') : get_string('inactive', 'report_platform_usage');
            $completionStatus = $user['is_completed']
                ? get_string('completed', 'report_platform_usage') . ' (' . userdate($user['completion_date'], '%d/%m/%Y') . ')'
                : get_string('notcompleted', 'report_platform_usage');
            $lastCourseAccess = $user['last_course_access'] ? userdate($user['last_course_access'], '%d/%m/%Y %H:%M') : get_string('never', 'report_platform_usage');
            $lastPlatformAccess = $user['last_platform_access'] ? userdate($user['last_platform_access'], '%d/%m/%Y %H:%M') : get_string('never', 'report_platform_usage');

            $sheet->fromArray([
                $user['fullname'],
                $user['username'],
                $user['email'],
                $status,
                $completionStatus,
                $lastCourseAccess,
                $user['dedication_formatted'],
                $lastPlatformAccess,
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($this->styles['data']);

            // Color code status.
            if ($user['is_active']) {
                $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB('006600');
                $activeCount++;
            } else {
                $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB('CC0000');
            }

            // Color code completion.
            if ($user['is_completed']) {
                $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB('006600');
                $completedCount++;
            }

            $row++;
        }

        // Add summary row.
        $row++;
        $sheet->setCellValue("A{$row}", get_string('summary', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('totalusers', 'report_platform_usage') . ': ' . count($users));
        $row++;
        $sheet->setCellValue("A{$row}", get_string('activeusers', 'report_platform_usage') . ': ' . $activeCount);
        $row++;
        $sheet->setCellValue("A{$row}", get_string('totalcompletions', 'report_platform_usage') . ': ' . $completedCount);

        // Auto-size columns.
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create course access history sheet.
     */
    protected function createCourseAccessHistorySheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['access_history']);

        // Header.
        $sheet->setCellValue('A1', get_string('courseaccesshistory', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:C1');

        // Description.
        $sheet->setCellValue('A2', get_string('courseaccesshistory_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:C2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Get access history data.
        $accessHistory = $this->report->get_course_access_history(30);

        // Check if there's any access data.
        $hasAccessData = false;
        if (!empty($accessHistory['accesses'])) {
            foreach ($accessHistory['accesses'] as $value) {
                if ($value > 0) {
                    $hasAccessData = true;
                    break;
                }
            }
        }

        if ($hasAccessData && !empty($accessHistory['labels'])) {
            // Column headers.
            $headers = [
                get_string('date', 'report_platform_usage'),
                get_string('courseaccesses', 'report_platform_usage'),
                get_string('uniqueusers', 'report_platform_usage'),
            ];
            $sheet->fromArray($headers, null, 'A4');
            $sheet->getStyle('A4:C4')->applyFromArray($this->styles['header']);

            $row = 5;
            $dataStart = $row;

            for ($i = 0; $i < count($accessHistory['labels']); $i++) {
                $sheet->fromArray([
                    $accessHistory['labels'][$i],
                    $accessHistory['accesses'][$i],
                    $accessHistory['unique_users'][$i],
                ], null, "A{$row}");
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->styles['data']);
                $sheet->getStyle("B{$row}:C{$row}")->applyFromArray($this->styles['number']);
                $row++;
            }
            $dataEnd = $row - 1;

            // Add summary.
            $row++;
            $totalAccesses = array_sum($accessHistory['accesses']);
            $avgAccesses = count($accessHistory['accesses']) > 0 ? round($totalAccesses / count($accessHistory['accesses']), 1) : 0;
            $maxAccesses = !empty($accessHistory['accesses']) ? max($accessHistory['accesses']) : 0;

            $sheet->setCellValue("A{$row}", get_string('total', 'report_platform_usage'));
            $sheet->setCellValue("B{$row}", $totalAccesses);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['highlight']);
            $row++;
            $sheet->setCellValue("A{$row}", get_string('average', 'report_platform_usage'));
            $sheet->setCellValue("B{$row}", $avgAccesses);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
            $row++;
            $sheet->setCellValue("A{$row}", get_string('maximum', 'report_platform_usage'));
            $sheet->setCellValue("B{$row}", $maxAccesses);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
            // Create chart if we have data.
            if ($dataEnd >= $dataStart) {
                $this->createCourseAccessHistoryChart($sheet, $dataStart, $dataEnd);
            }
        } else {
            // No access data - show informative message.
            $sheet->setCellValue('A4', get_string('nodata', 'report_platform_usage'));
            $sheet->getStyle('A4')->applyFromArray($this->styles['description']);
            $sheet->mergeCells('A4:C4');
        }

        // Auto-size columns.
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create course access history line chart.
     */
    protected function createCourseAccessHistoryChart($sheet, int $dataStart, int $dataEnd): void {
        // Get the actual sheet name for chart references.
        $sheetName = $this->sheetNames['access_history'];

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$B\$4", null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$C\$4", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $dataEnd - $dataStart + 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$C\${$dataStart}:\$C\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title(get_string('courseaccesstrends', 'report_platform_usage'));

        $chart = new Chart(
            'accessHistoryChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('E3');
        $chart->setBottomRightPosition('N18');

        $sheet->addChart($chart);
    }

    /**
     * Create course activities sheet for course context.
     */
    protected function createCourseActivitiesSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['course_activities']);

        // Header.
        $sheet->setCellValue('A1', get_string('topactivities', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:E1');

        // Description.
        $sheet->setCellValue('A2', get_string('topactivities_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:E2');
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Column headers.
        $headers = [
            get_string('activityname', 'report_platform_usage'),
            get_string('activitytype', 'report_platform_usage'),
            get_string('activityaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
            get_string('avgaccessperuser', 'report_platform_usage'),
        ];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:E4')->applyFromArray($this->styles['header']);

        // Get activity data.
        $activities = $this->report->get_top_activities(50);
        $row = 5;
        $dataStart = $row;

        foreach ($activities as $activity) {
            $avgAccess = $activity->unique_users > 0 ? round($activity->access_count / $activity->unique_users, 2) : 0;
            $sheet->fromArray([
                $activity->name,
                $activity->type_name,
                $activity->access_count,
                $activity->unique_users,
                $avgAccess,
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray($this->styles['data']);
            $sheet->getStyle("C{$row}:E{$row}")->applyFromArray($this->styles['number']);
            $row++;
        }
        $dataEnd = $row - 1;

        // Add activities bar chart if there's data (top 10 for chart clarity).
        if (count($activities) > 0) {
            $chartEnd = min($dataStart + 9, $dataEnd); // Top 10 activities for chart.
            $this->createCourseActivitiesChart($sheet, $dataStart, $chartEnd);
        }

        // Auto-size columns.
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create course activities horizontal bar chart.
     */
    protected function createCourseActivitiesChart($sheet, int $dataStart, int $dataEnd): void {
        // Get the actual sheet name for chart references.
        $sheetName = $this->sheetNames['course_activities'];

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$C\$4", null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$C\${$dataStart}:\$C\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title(get_string('topactivities', 'report_platform_usage'));

        $chart = new Chart(
            'activitiesBarChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('G3');
        $chart->setBottomRightPosition('P18');

        $sheet->addChart($chart);
    }

    /**
     * Create course completions sheet for course context.
     */
    protected function createCourseCompletionsSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['course_completions']);

        $courseStats = $this->report->get_course_statistics();

        // Header.
        $sheet->setCellValue('A1', get_string('coursecompletions', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:C1');

        // Summary.
        $sheet->setCellValue('A3', get_string('summary', 'report_platform_usage'));
        $sheet->getStyle('A3')->applyFromArray($this->styles['subtitle']);

        $row = 4;
        $sheet->setCellValue("A{$row}", get_string('enrolledusers', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $courseStats['enrolled_users']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);

        $row++;
        $sheet->setCellValue("A{$row}", get_string('totalcompletions', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $courseStats['completions']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['highlight']);

        $row++;
        $completionRate = $courseStats['enrolled_users'] > 0
            ? round(($courseStats['completions'] / $courseStats['enrolled_users']) * 100, 1)
            : 0;
        $sheet->setCellValue("A{$row}", get_string('completionrate', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $completionRate . '%');
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);

        // Add completion pie chart only if there are enrolled users.
        if ($courseStats['enrolled_users'] > 0) {
            $this->createCourseCompletionChart($sheet, $courseStats);
        }

        // Get completion trends data.
        $trends = $this->report->get_completion_trends(30);

        // Check if there's any actual completion data in trends.
        $hasCompletionData = false;
        if (!empty($trends['data'])) {
            foreach ($trends['data'] as $value) {
                if ($value > 0) {
                    $hasCompletionData = true;
                    break;
                }
            }
        }

        // Completion trends section - only show if there's data.
        $row += 3;
        $sheet->setCellValue("A{$row}", get_string('completiontrends', 'report_platform_usage'));
        $sheet->getStyle("A{$row}")->applyFromArray($this->styles['subtitle']);

        if ($hasCompletionData && !empty($trends['labels'])) {
            $row++;
            $headers = [
                get_string('date', 'report_platform_usage'),
                get_string('completions', 'report_platform_usage'),
            ];
            $sheet->fromArray($headers, null, "A{$row}");
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['header']);

            $row++;
            $dataStart = $row;
            foreach ($trends['labels'] as $i => $label) {
                $sheet->fromArray([
                    $label,
                    $trends['data'][$i],
                ], null, "A{$row}");
                $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
                $sheet->getStyle("B{$row}")->applyFromArray($this->styles['number']);
                $row++;
            }
            $dataEnd = $row - 1;

            // Add completion trends line chart.
            if ($dataEnd >= $dataStart) {
                $this->createCourseCompletionTrendsChart($sheet, $dataStart, $dataEnd);
            }
        } else {
            // No completion data available - show informative message.
            $row++;
            $sheet->setCellValue("A{$row}", get_string('nodata', 'report_platform_usage'));
            $sheet->getStyle("A{$row}")->applyFromArray($this->styles['description']);
            $sheet->mergeCells("A{$row}:C{$row}");
        }

        // Auto-size columns.
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create course completion pie chart.
     */
    protected function createCourseCompletionChart($sheet, array $courseStats): void {
        // Get the actual sheet name for chart references.
        $sheetName = $this->sheetNames['course_completions'];

        // Prepare data for pie chart.
        $completed = $courseStats['completions'];
        $notCompleted = max(0, $courseStats['enrolled_users'] - $courseStats['completions']);

        // Create temporary data for pie chart.
        $sheet->setCellValue('D4', get_string('completed', 'report_platform_usage'));
        $sheet->setCellValue('E4', $completed);
        $sheet->setCellValue('D5', get_string('notcompleted', 'report_platform_usage'));
        $sheet->setCellValue('E5', $notCompleted);
        $sheet->getColumnDimension('D')->setVisible(false);
        $sheet->getColumnDimension('E')->setVisible(false);

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$D\$4:\$D\$5", null, 2),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$D\$4:\$D\$5", null, 2),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$E\$4:\$E\$5", null, 2),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title(get_string('completionrate', 'report_platform_usage'));

        $chart = new Chart(
            'completionPieChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('F3');
        $chart->setBottomRightPosition('L10');

        $sheet->addChart($chart);
    }

    /**
     * Create course completion trends line chart.
     */
    protected function createCourseCompletionTrendsChart($sheet, int $dataStart, int $dataEnd): void {
        // Get the actual sheet name for chart references.
        $sheetName = $this->sheetNames['course_completions'];

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$B\$" . ($dataStart - 1), null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $dataEnd - $dataStart + 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title(get_string('completiontrends', 'report_platform_usage'));

        $chart = new Chart(
            'completionTrendsChart',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('D' . ($dataStart - 1));
        $chart->setBottomRightPosition('L' . ($dataStart + 14));

        $sheet->addChart($chart);
    }

    /**
     * Create course dedication sheet for course context.
     */
    protected function createCourseDedicationSheet(): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($this->sheetNames['course_dedication']);

        $courseStats = $this->report->get_course_statistics();

        // Header.
        $sheet->setCellValue('A1', get_string('topdedication', 'report_platform_usage'));
        $sheet->getStyle('A1')->applyFromArray($this->styles['title']);
        $sheet->mergeCells('A1:C1');

        // Description.
        $sheet->setCellValue('A2', get_string('export_dedication_desc', 'report_platform_usage'));
        $sheet->getStyle('A2')->applyFromArray($this->styles['description']);
        $sheet->mergeCells('A2:C2');
        $sheet->getRowDimension(2)->setRowHeight(50);

        // Summary.
        $sheet->setCellValue('A4', get_string('summary', 'report_platform_usage'));
        $sheet->getStyle('A4')->applyFromArray($this->styles['subtitle']);

        $row = 5;
        $sheet->setCellValue("A{$row}", get_string('totaldedication', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $courseStats['total_dedication_formatted']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['highlight']);

        $row++;
        $sheet->setCellValue("A{$row}", get_string('avgaccessperuser', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $courseStats['avg_dedication_formatted']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);

        $row++;
        $sheet->setCellValue("A{$row}", get_string('enrolledusers', 'report_platform_usage'));
        $sheet->setCellValue("B{$row}", $courseStats['enrolled_users']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->styles['data']);

        // Auto-size columns.
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
