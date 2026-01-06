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
 * PDF exporter using TCPDF for Platform Usage Report.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_platform_usage;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/pdflib.php');

/**
 * Class for exporting reports to PDF format.
 */
class pdf_exporter {

    /** @var report Report instance */
    protected $report;

    /** @var \pdf PDF instance */
    protected $pdf;

    /** @var array Color definitions */
    protected $colors;

    /**
     * Constructor.
     *
     * @param report $report Report instance
     */
    public function __construct(report $report) {
        $this->report = $report;
        $this->initColors();
    }

    /**
     * Initialize color palette.
     */
    protected function initColors(): void {
        $this->colors = [
            'primary' => [99, 102, 241],      // #6366f1
            'success' => [16, 185, 129],      // #10b981
            'warning' => [245, 158, 11],      // #f59e0b
            'info' => [6, 182, 212],          // #06b6d4
            'error' => [244, 63, 94],         // #f43f5e
            'gray_dark' => [51, 65, 85],      // #334155
            'gray_light' => [241, 245, 249],  // #f1f5f9
            'white' => [255, 255, 255],
            'black' => [15, 23, 42],          // #0f172a
        ];
    }

    /**
     * Export the report to PDF.
     */
    public function export(): void {
        global $CFG, $USER;

        $this->pdf = new \pdf('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information.
        $this->pdf->SetCreator('IOMAD Platform');
        $this->pdf->SetAuthor(fullname($USER));
        $this->pdf->SetTitle(get_string('reporttitle', 'report_platform_usage'));
        $this->pdf->SetSubject(get_string('export_report_desc', 'report_platform_usage'));

        // Remove default header/footer.
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(true);

        // Set margins.
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 20);

        // Set default font.
        $this->pdf->SetFont('helvetica', '', 10);

        // Add content based on context.
        if ($this->report->is_course_context()) {
            $this->addCourseReportContent();
        } else {
            $this->addPlatformReportContent();
        }

        // Generate filename.
        $contextname = $this->report->is_course_context() ? 'course_' : 'platform_';
        $filename = $contextname . 'usage_report_' . date('Ymd_His') . '.pdf';

        // Output PDF.
        $this->pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Add platform report content.
     */
    protected function addPlatformReportContent(): void {
        // Page 1: Summary and Login Stats.
        $this->pdf->AddPage();
        $this->addReportHeader();
        $this->addLoginSummarySection();
        $this->addUserSummarySection();
        $this->addCompletionsSummarySection();

        // Page 2: Top Courses.
        $this->pdf->AddPage();
        $this->addSectionTitle(get_string('topcourses', 'report_platform_usage'));
        $this->addTopCoursesTable();

        // Page 3: Top Activities.
        $this->pdf->AddPage();
        $this->addSectionTitle(get_string('topactivities', 'report_platform_usage'));
        $this->addTopActivitiesTable();

        // Page 4: Dedication.
        $this->pdf->AddPage();
        $this->addSectionTitle(get_string('topdedication', 'report_platform_usage'));
        $this->addDedicationTable();

        // Page 5: Daily Logins.
        $this->pdf->AddPage();
        $this->addSectionTitle(get_string('dailylogins', 'report_platform_usage'));
        $this->addDailyLoginsTable();
    }

    /**
     * Add course report content.
     */
    protected function addCourseReportContent(): void {
        $courseStats = $this->report->get_course_statistics();

        // Page 1: Course Summary.
        $this->pdf->AddPage();
        $this->addCourseReportHeader();
        $this->addCourseSummarySection($courseStats);

        // Page 2: Enrolled Users.
        $this->pdf->AddPage();
        $this->addSectionTitle(get_string('courseenrolledusers', 'report_platform_usage'));
        $this->addEnrolledUsersTable();

        // Page 3: Course Activities.
        $this->pdf->AddPage();
        $this->addSectionTitle(get_string('topactivities', 'report_platform_usage'));
        $this->addCourseActivitiesTable();
    }

    /**
     * Add report header.
     */
    protected function addReportHeader(): void {
        // Title.
        $this->pdf->SetFont('helvetica', 'B', 20);
        $this->pdf->SetTextColor(...$this->colors['primary']);
        $this->pdf->Cell(0, 15, get_string('reporttitle', 'report_platform_usage'), 0, 1, 'C');

        // Subtitle with date range.
        $this->pdf->SetFont('helvetica', '', 11);
        $this->pdf->SetTextColor(...$this->colors['gray_dark']);
        $this->pdf->Cell(0, 8, $this->report->get_date_range(), 0, 1, 'C');

        // Company filter if applicable.
        $companyName = $this->report->get_company_name();
        if ($companyName !== get_string('allcompanies', 'report_platform_usage')) {
            $this->pdf->SetFont('helvetica', 'I', 10);
            $this->pdf->Cell(0, 6, get_string('filter_company', 'report_platform_usage') . ': ' . $companyName, 0, 1, 'C');
        }

        // Generated date.
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(128, 128, 128);
        $this->pdf->Cell(0, 6, get_string('generateddate', 'report_platform_usage') . ': ' . userdate(time(), '%d/%m/%Y %H:%M'), 0, 1, 'C');

        $this->pdf->Ln(10);
    }

    /**
     * Add course report header.
     */
    protected function addCourseReportHeader(): void {
        $courseStats = $this->report->get_course_statistics();

        // Title.
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->SetTextColor(...$this->colors['primary']);
        $this->pdf->Cell(0, 12, get_string('coursereport', 'report_platform_usage'), 0, 1, 'C');

        // Course name.
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->SetTextColor(...$this->colors['black']);
        $this->pdf->Cell(0, 10, $courseStats['fullname'] ?? '', 0, 1, 'C');

        // Date range.
        $this->pdf->SetFont('helvetica', '', 11);
        $this->pdf->SetTextColor(...$this->colors['gray_dark']);
        $this->pdf->Cell(0, 8, $this->report->get_date_range(), 0, 1, 'C');

        // Generated date.
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(128, 128, 128);
        $this->pdf->Cell(0, 6, get_string('generateddate', 'report_platform_usage') . ': ' . userdate(time(), '%d/%m/%Y %H:%M'), 0, 1, 'C');

        $this->pdf->Ln(10);
    }

    /**
     * Add section title.
     *
     * @param string $title Section title
     */
    protected function addSectionTitle(string $title): void {
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->SetTextColor(...$this->colors['primary']);
        $this->pdf->Cell(0, 10, $title, 0, 1, 'L');
        $this->pdf->SetTextColor(...$this->colors['black']);
        $this->pdf->Ln(2);
    }

    /**
     * Add login summary section.
     */
    protected function addLoginSummarySection(): void {
        $summary = $this->report->get_login_summary();

        $this->addSectionTitle(get_string('platformaccess', 'report_platform_usage'));

        // Create summary box.
        $this->pdf->SetFillColor(...$this->colors['gray_light']);
        $this->pdf->RoundedRect(15, $this->pdf->GetY(), 180, 35, 3, '1111', 'F');

        $startY = $this->pdf->GetY() + 5;

        // Three columns for periods.
        $colWidth = 60;

        // Today.
        $this->pdf->SetXY(15, $startY);
        $this->addMetricBox(
            get_string('today', 'report_platform_usage'),
            number_format($summary['logins_today']),
            get_string('uniqueusers', 'report_platform_usage') . ': ' . number_format($summary['unique_users_today']),
            $colWidth
        );

        // Week.
        $this->pdf->SetXY(75, $startY);
        $this->addMetricBox(
            get_string('lastweek', 'report_platform_usage'),
            number_format($summary['logins_week']),
            get_string('uniqueusers', 'report_platform_usage') . ': ' . number_format($summary['unique_users_week']),
            $colWidth
        );

        // Month.
        $this->pdf->SetXY(135, $startY);
        $this->addMetricBox(
            get_string('lastmonth', 'report_platform_usage'),
            number_format($summary['logins_month']),
            get_string('uniqueusers', 'report_platform_usage') . ': ' . number_format($summary['unique_users_month']),
            $colWidth
        );

        $this->pdf->SetY($startY + 35);
        $this->pdf->Ln(5);
    }

    /**
     * Add user summary section.
     */
    protected function addUserSummarySection(): void {
        $userSummary = $this->report->get_user_activity_summary();

        $this->addSectionTitle(get_string('usersummary', 'report_platform_usage'));

        $this->pdf->SetFillColor(...$this->colors['gray_light']);
        $this->pdf->RoundedRect(15, $this->pdf->GetY(), 180, 30, 3, '1111', 'F');

        $startY = $this->pdf->GetY() + 5;
        $colWidth = 60;

        // Total.
        $this->pdf->SetXY(15, $startY);
        $this->addMetricBox(
            get_string('totalusers', 'report_platform_usage'),
            number_format($userSummary['total']),
            '',
            $colWidth
        );

        // Active.
        $this->pdf->SetXY(75, $startY);
        $this->pdf->SetTextColor(...$this->colors['success']);
        $this->addMetricBox(
            get_string('activeusers', 'report_platform_usage'),
            number_format($userSummary['active']),
            '',
            $colWidth
        );

        // Inactive.
        $this->pdf->SetXY(135, $startY);
        $this->pdf->SetTextColor(...$this->colors['error']);
        $this->addMetricBox(
            get_string('inactiveusers', 'report_platform_usage'),
            number_format($userSummary['inactive']),
            '',
            $colWidth
        );

        $this->pdf->SetTextColor(...$this->colors['black']);
        $this->pdf->SetY($startY + 30);
        $this->pdf->Ln(5);
    }

    /**
     * Add completions summary section.
     */
    protected function addCompletionsSummarySection(): void {
        $completions = $this->report->get_course_completions_summary();

        $this->addSectionTitle(get_string('completions', 'report_platform_usage'));

        $this->pdf->SetFillColor(...$this->colors['gray_light']);
        $this->pdf->RoundedRect(15, $this->pdf->GetY(), 180, 30, 3, '1111', 'F');

        $startY = $this->pdf->GetY() + 5;
        $colWidth = 45;

        // Today.
        $this->pdf->SetXY(15, $startY);
        $this->addMetricBox(get_string('today', 'report_platform_usage'), number_format($completions['completions_today']), '', $colWidth);

        // Week.
        $this->pdf->SetXY(60, $startY);
        $this->addMetricBox(get_string('lastweek', 'report_platform_usage'), number_format($completions['completions_week']), '', $colWidth);

        // Month.
        $this->pdf->SetXY(105, $startY);
        $this->addMetricBox(get_string('lastmonth', 'report_platform_usage'), number_format($completions['completions_month']), '', $colWidth);

        // Total.
        $this->pdf->SetXY(150, $startY);
        $this->pdf->SetTextColor(...$this->colors['info']);
        $this->addMetricBox(get_string('total', 'report_platform_usage'), number_format($completions['total_completions']), '', $colWidth);

        $this->pdf->SetTextColor(...$this->colors['black']);
        $this->pdf->SetY($startY + 30);
        $this->pdf->Ln(5);
    }

    /**
     * Add metric box.
     *
     * @param string $label Label
     * @param string $value Value
     * @param string $subtitle Subtitle
     * @param int $width Box width
     */
    protected function addMetricBox(string $label, string $value, string $subtitle = '', int $width = 60): void {
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();

        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(...$this->colors['gray_dark']);
        $this->pdf->Cell($width, 5, $label, 0, 1, 'C');

        $this->pdf->SetX($x);
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->SetTextColor(...$this->colors['black']);
        $this->pdf->Cell($width, 8, $value, 0, 1, 'C');

        if (!empty($subtitle)) {
            $this->pdf->SetX($x);
            $this->pdf->SetFont('helvetica', '', 8);
            $this->pdf->SetTextColor(128, 128, 128);
            $this->pdf->Cell($width, 5, $subtitle, 0, 1, 'C');
        }
    }

    /**
     * Add course summary section.
     *
     * @param array $courseStats Course statistics
     */
    protected function addCourseSummarySection(array $courseStats): void {
        $this->addSectionTitle(get_string('coursemetrics', 'report_platform_usage'));

        $this->pdf->SetFillColor(...$this->colors['gray_light']);
        $this->pdf->RoundedRect(15, $this->pdf->GetY(), 180, 35, 3, '1111', 'F');

        $startY = $this->pdf->GetY() + 5;
        $colWidth = 45;

        // Enrolled users.
        $this->pdf->SetXY(15, $startY);
        $this->addMetricBox(get_string('courseenrolledusers', 'report_platform_usage'), number_format($courseStats['enrolled'] ?? 0), '', $colWidth);

        // Active users.
        $this->pdf->SetXY(60, $startY);
        $this->pdf->SetTextColor(...$this->colors['success']);
        $this->addMetricBox(get_string('courseactiveusers', 'report_platform_usage'), number_format($courseStats['active_users'] ?? 0), '', $colWidth);

        // Accesses.
        $this->pdf->SetXY(105, $startY);
        $this->pdf->SetTextColor(...$this->colors['info']);
        $this->addMetricBox(get_string('courseaccesses', 'report_platform_usage'), number_format($courseStats['accesses'] ?? 0), '', $colWidth);

        // Completions.
        $this->pdf->SetXY(150, $startY);
        $this->pdf->SetTextColor(...$this->colors['warning']);
        $this->addMetricBox(get_string('coursecompletions', 'report_platform_usage'), number_format($courseStats['completions'] ?? 0), '', $colWidth);

        $this->pdf->SetTextColor(...$this->colors['black']);
        $this->pdf->SetY($startY + 40);
    }

    /**
     * Add top courses table.
     */
    protected function addTopCoursesTable(): void {
        $courses = $this->report->get_top_courses(20);

        if (empty($courses)) {
            $this->pdf->SetFont('helvetica', 'I', 10);
            $this->pdf->Cell(0, 10, get_string('nodata', 'report_platform_usage'), 0, 1, 'C');
            return;
        }

        // Table headers.
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->SetFillColor(...$this->colors['primary']);
        $this->pdf->SetTextColor(...$this->colors['white']);

        $this->pdf->Cell(90, 8, get_string('coursename', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(30, 8, get_string('shortname', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(30, 8, get_string('courseaccesses', 'report_platform_usage'), 1, 0, 'R', true);
        $this->pdf->Cell(30, 8, get_string('uniqueusers', 'report_platform_usage'), 1, 1, 'R', true);

        // Table rows.
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(...$this->colors['black']);
        $fill = false;

        foreach ($courses as $course) {
            if ($fill) {
                $this->pdf->SetFillColor(...$this->colors['gray_light']);
            } else {
                $this->pdf->SetFillColor(...$this->colors['white']);
            }

            $fullname = mb_strlen($course->fullname) > 50 ? mb_substr($course->fullname, 0, 47) . '...' : $course->fullname;
            $shortname = mb_strlen($course->shortname) > 15 ? mb_substr($course->shortname, 0, 12) . '...' : $course->shortname;

            $this->pdf->Cell(90, 7, $fullname, 1, 0, 'L', true);
            $this->pdf->Cell(30, 7, $shortname, 1, 0, 'L', true);
            $this->pdf->Cell(30, 7, number_format($course->access_count), 1, 0, 'R', true);
            $this->pdf->Cell(30, 7, number_format($course->unique_users), 1, 1, 'R', true);

            $fill = !$fill;
        }
    }

    /**
     * Add top activities table.
     */
    protected function addTopActivitiesTable(): void {
        $activities = $this->report->get_top_activities(20);

        if (empty($activities)) {
            $this->pdf->SetFont('helvetica', 'I', 10);
            $this->pdf->Cell(0, 10, get_string('nodata', 'report_platform_usage'), 0, 1, 'C');
            return;
        }

        // Table headers.
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->SetFillColor(...$this->colors['primary']);
        $this->pdf->SetTextColor(...$this->colors['white']);

        $this->pdf->Cell(70, 8, get_string('activityname', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(25, 8, get_string('activitytype', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(55, 8, get_string('coursename', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(15, 8, get_string('activityaccesses', 'report_platform_usage'), 1, 0, 'R', true);
        $this->pdf->Cell(15, 8, get_string('uniqueusers', 'report_platform_usage'), 1, 1, 'R', true);

        // Table rows.
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->SetTextColor(...$this->colors['black']);
        $fill = false;

        foreach ($activities as $activity) {
            if ($fill) {
                $this->pdf->SetFillColor(...$this->colors['gray_light']);
            } else {
                $this->pdf->SetFillColor(...$this->colors['white']);
            }

            $name = mb_strlen($activity->name) > 40 ? mb_substr($activity->name, 0, 37) . '...' : $activity->name;
            $courseName = mb_strlen($activity->course_name) > 30 ? mb_substr($activity->course_name, 0, 27) . '...' : $activity->course_name;

            $this->pdf->Cell(70, 6, $name, 1, 0, 'L', true);
            $this->pdf->Cell(25, 6, $activity->type_name ?? $activity->type, 1, 0, 'L', true);
            $this->pdf->Cell(55, 6, $courseName, 1, 0, 'L', true);
            $this->pdf->Cell(15, 6, number_format($activity->access_count), 1, 0, 'R', true);
            $this->pdf->Cell(15, 6, number_format($activity->unique_users), 1, 1, 'R', true);

            $fill = !$fill;
        }
    }

    /**
     * Add dedication table.
     */
    protected function addDedicationTable(): void {
        $dedication = $this->report->get_top_courses_dedication(20);

        if (empty($dedication)) {
            $this->pdf->SetFont('helvetica', 'I', 10);
            $this->pdf->Cell(0, 10, get_string('nodata', 'report_platform_usage'), 0, 1, 'C');
            return;
        }

        // Description.
        $this->pdf->SetFont('helvetica', 'I', 9);
        $this->pdf->SetTextColor(...$this->colors['gray_dark']);
        $this->pdf->MultiCell(0, 5, get_string('dedication_desc', 'report_platform_usage'), 0, 'L');
        $this->pdf->Ln(3);

        // Table headers.
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->SetFillColor(...$this->colors['primary']);
        $this->pdf->SetTextColor(...$this->colors['white']);

        $this->pdf->Cell(10, 8, '#', 1, 0, 'C', true);
        $this->pdf->Cell(80, 8, get_string('coursename', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(35, 8, get_string('totaldedication', 'report_platform_usage'), 1, 0, 'R', true);
        $this->pdf->Cell(25, 8, get_string('enrolledusers', 'report_platform_usage'), 1, 0, 'R', true);
        $this->pdf->Cell(30, 8, get_string('dedicationpercent', 'report_platform_usage'), 1, 1, 'R', true);

        // Table rows.
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(...$this->colors['black']);
        $fill = false;

        foreach ($dedication as $course) {
            if ($fill) {
                $this->pdf->SetFillColor(...$this->colors['gray_light']);
            } else {
                $this->pdf->SetFillColor(...$this->colors['white']);
            }

            $fullname = mb_strlen($course['fullname']) > 45 ? mb_substr($course['fullname'], 0, 42) . '...' : $course['fullname'];

            $this->pdf->Cell(10, 7, $course['rank'], 1, 0, 'C', true);
            $this->pdf->Cell(80, 7, $fullname, 1, 0, 'L', true);
            $this->pdf->Cell(35, 7, $course['total_dedication_formatted'], 1, 0, 'R', true);
            $this->pdf->Cell(25, 7, number_format($course['enrolled_students']), 1, 0, 'R', true);
            $this->pdf->Cell(30, 7, $course['dedication_percent'] . '%', 1, 1, 'R', true);

            $fill = !$fill;
        }
    }

    /**
     * Add daily logins table.
     */
    protected function addDailyLoginsTable(): void {
        $daily = $this->report->get_daily_data_for_export();

        if (empty($daily)) {
            $this->pdf->SetFont('helvetica', 'I', 10);
            $this->pdf->Cell(0, 10, get_string('nodata', 'report_platform_usage'), 0, 1, 'C');
            return;
        }

        // Table headers.
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->SetFillColor(...$this->colors['primary']);
        $this->pdf->SetTextColor(...$this->colors['white']);

        $this->pdf->Cell(60, 8, get_string('date', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(60, 8, get_string('logins', 'report_platform_usage'), 1, 0, 'R', true);
        $this->pdf->Cell(60, 8, get_string('uniqueusers', 'report_platform_usage'), 1, 1, 'R', true);

        // Table rows.
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(...$this->colors['black']);
        $fill = false;
        $totalLogins = 0;
        $totalUsers = 0;

        foreach ($daily as $day) {
            if ($fill) {
                $this->pdf->SetFillColor(...$this->colors['gray_light']);
            } else {
                $this->pdf->SetFillColor(...$this->colors['white']);
            }

            $this->pdf->Cell(60, 6, $day->login_date, 1, 0, 'L', true);
            $this->pdf->Cell(60, 6, number_format($day->login_count), 1, 0, 'R', true);
            $this->pdf->Cell(60, 6, number_format($day->unique_users), 1, 1, 'R', true);

            $totalLogins += $day->login_count;
            $totalUsers += $day->unique_users;
            $fill = !$fill;
        }

        // Totals row.
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->SetFillColor(...$this->colors['gray_dark']);
        $this->pdf->SetTextColor(...$this->colors['white']);
        $this->pdf->Cell(60, 7, get_string('total', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(60, 7, number_format($totalLogins), 1, 0, 'R', true);
        $this->pdf->Cell(60, 7, number_format($totalUsers), 1, 1, 'R', true);
    }

    /**
     * Add enrolled users table for course context.
     */
    protected function addEnrolledUsersTable(): void {
        $users = $this->report->get_course_users_details();

        if (empty($users)) {
            $this->pdf->SetFont('helvetica', 'I', 10);
            $this->pdf->Cell(0, 10, get_string('nodata', 'report_platform_usage'), 0, 1, 'C');
            return;
        }

        // Table headers.
        $this->pdf->SetFont('helvetica', 'B', 8);
        $this->pdf->SetFillColor(...$this->colors['primary']);
        $this->pdf->SetTextColor(...$this->colors['white']);

        $this->pdf->Cell(50, 7, get_string('fullname', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(50, 7, get_string('email', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(30, 7, get_string('lastaccess', 'report_platform_usage'), 1, 0, 'C', true);
        $this->pdf->Cell(25, 7, get_string('userdedication', 'report_platform_usage'), 1, 0, 'R', true);
        $this->pdf->Cell(25, 7, get_string('status', 'report_platform_usage'), 1, 1, 'C', true);

        // Table rows.
        $this->pdf->SetFont('helvetica', '', 8);
        $fill = false;

        foreach ($users as $user) {
            if ($fill) {
                $this->pdf->SetFillColor(...$this->colors['gray_light']);
            } else {
                $this->pdf->SetFillColor(...$this->colors['white']);
            }

            $this->pdf->SetTextColor(...$this->colors['black']);

            $fullname = mb_strlen($user->fullname) > 28 ? mb_substr($user->fullname, 0, 25) . '...' : $user->fullname;
            $email = mb_strlen($user->email) > 28 ? mb_substr($user->email, 0, 25) . '...' : $user->email;
            $lastAccess = $user->lastcourseaccess ? userdate($user->lastcourseaccess, '%d/%m/%Y') : '-';

            $this->pdf->Cell(50, 6, $fullname, 1, 0, 'L', true);
            $this->pdf->Cell(50, 6, $email, 1, 0, 'L', true);
            $this->pdf->Cell(30, 6, $lastAccess, 1, 0, 'C', true);
            $this->pdf->Cell(25, 6, $user->dedication_formatted ?? '-', 1, 0, 'R', true);

            // Status with color.
            $status = $user->completed ? get_string('completed', 'report_platform_usage') : get_string('notcompleted', 'report_platform_usage');
            if ($user->completed) {
                $this->pdf->SetTextColor(...$this->colors['success']);
            } else {
                $this->pdf->SetTextColor(...$this->colors['error']);
            }
            $this->pdf->Cell(25, 6, $status, 1, 1, 'C', true);

            $fill = !$fill;
        }
    }

    /**
     * Add course activities table.
     */
    protected function addCourseActivitiesTable(): void {
        $activities = $this->report->get_course_top_activities(20);

        if (empty($activities)) {
            $this->pdf->SetFont('helvetica', 'I', 10);
            $this->pdf->Cell(0, 10, get_string('nodata', 'report_platform_usage'), 0, 1, 'C');
            return;
        }

        // Table headers.
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->SetFillColor(...$this->colors['primary']);
        $this->pdf->SetTextColor(...$this->colors['white']);

        $this->pdf->Cell(90, 8, get_string('activityname', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(30, 8, get_string('activitytype', 'report_platform_usage'), 1, 0, 'L', true);
        $this->pdf->Cell(30, 8, get_string('activityaccesses', 'report_platform_usage'), 1, 0, 'R', true);
        $this->pdf->Cell(30, 8, get_string('uniqueusers', 'report_platform_usage'), 1, 1, 'R', true);

        // Table rows.
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(...$this->colors['black']);
        $fill = false;

        foreach ($activities as $activity) {
            if ($fill) {
                $this->pdf->SetFillColor(...$this->colors['gray_light']);
            } else {
                $this->pdf->SetFillColor(...$this->colors['white']);
            }

            $name = mb_strlen($activity->name) > 50 ? mb_substr($activity->name, 0, 47) . '...' : $activity->name;

            $this->pdf->Cell(90, 7, $name, 1, 0, 'L', true);
            $this->pdf->Cell(30, 7, $activity->type_name ?? $activity->type, 1, 0, 'L', true);
            $this->pdf->Cell(30, 7, number_format($activity->access_count), 1, 0, 'R', true);
            $this->pdf->Cell(30, 7, number_format($activity->unique_users), 1, 1, 'R', true);

            $fill = !$fill;
        }
    }
}
