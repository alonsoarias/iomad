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
 * Report exporter class.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_platform_usage;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/csvlib.class.php');

/**
 * Class to handle report exports.
 */
class exporter {

    /** @var report Report instance */
    protected $report;

    /** @var string Export type (logins, courses, users, daily) */
    protected $type;

    /**
     * Constructor.
     *
     * @param report $report Report instance
     * @param string $type Export type
     */
    public function __construct(report $report, string $type = 'logins') {
        $this->report = $report;
        $this->type = $type;
    }

    /**
     * Export data to CSV/Excel.
     *
     * @param string $format Format (csv or excel)
     */
    public function export(string $format = 'excel'): void {
        $contextname = $this->report->is_course_context() ? 'course_' : 'platform_';
        $filename = $contextname . 'usage_' . $this->type . '_' . date('Ymd_His');

        $excel = ($format === 'excel') ? 2 : 0;
        $export = new \csv_export_writer('comma', '"', 'application/download', $excel);
        $export->set_filename($filename);

        // Get headers and data based on type.
        switch ($this->type) {
            case 'logins':
                $this->export_logins($export);
                break;
            case 'courses':
                $this->export_courses($export);
                break;
            case 'users':
                $this->export_users($export);
                break;
            case 'daily':
                $this->export_daily($export);
                break;
            case 'summary':
                $this->export_summary($export);
                break;
            case 'dedication':
                $this->export_dedication($export);
                break;
            case 'activities':
                $this->export_activities($export);
                break;
            case 'dailyusers':
                $this->export_dailyusers($export);
                break;
            default:
                $this->export_logins($export);
        }

        $export->download_file();
    }

    /**
     * Add report header with context information.
     *
     * @param \csv_export_writer $export Export writer
     * @param string $title Section title
     */
    protected function add_report_header(\csv_export_writer $export, string $title): void {
        // Report title.
        $export->add_data([$title]);
        $export->add_data([get_string('generateddate', 'report_platform_usage') . ': ' . userdate(time(), '%d/%m/%Y %H:%M')]);

        // Context info.
        if ($this->report->is_course_context()) {
            $export->add_data([get_string('coursereport', 'report_platform_usage')]);
        } else {
            $export->add_data([get_string('filter_company', 'report_platform_usage') . ': ' . $this->report->get_company_name()]);
        }
        $export->add_data([get_string('filter_daterange', 'report_platform_usage') . ': ' . $this->report->get_date_range()]);
        $export->add_data([]); // Empty row.
    }

    /**
     * Export login summary data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_logins(\csv_export_writer $export): void {
        $this->add_report_header($export, get_string('loginsummary', 'report_platform_usage'));

        // Description.
        $export->add_data([get_string('export_logins_desc', 'report_platform_usage')]);
        $export->add_data([]); // Empty row.

        // Headers.
        $headers = [
            get_string('period', 'report_platform_usage'),
            get_string('logins', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get summary data.
        $summary = $this->report->get_login_summary();

        $rows = [
            [get_string('today', 'report_platform_usage'), $summary['logins_today'], $summary['unique_users_today']],
            [get_string('lastweek', 'report_platform_usage'), $summary['logins_week'], $summary['unique_users_week']],
            [get_string('lastmonth', 'report_platform_usage'), $summary['logins_month'], $summary['unique_users_month']],
        ];

        foreach ($rows as $row) {
            $export->add_data($row);
        }
    }

    /**
     * Export course access data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_courses(\csv_export_writer $export): void {
        $this->add_report_header($export, get_string('courseaccessdetails', 'report_platform_usage'));

        // Description.
        $export->add_data([get_string('export_courses_desc', 'report_platform_usage')]);
        $export->add_data([]); // Empty row.

        // Headers.
        $headers = [
            get_string('coursename', 'report_platform_usage'),
            get_string('shortname', 'report_platform_usage'),
            get_string('courseaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
            get_string('lastaccess', 'report_platform_usage'),
            get_string('avgaccessperuser', 'report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get course data.
        $courses = $this->report->get_course_access_details();
        $totalAccesses = 0;
        $totalUsers = 0;

        foreach ($courses as $course) {
            $avgAccess = $course->unique_users > 0 ? round($course->access_count / $course->unique_users, 2) : 0;
            $row = [
                $course->fullname,
                $course->shortname,
                $course->access_count,
                $course->unique_users,
                userdate($course->last_access, '%d/%m/%Y %H:%M'),
                $avgAccess,
            ];
            $export->add_data($row);
            $totalAccesses += $course->access_count;
            $totalUsers += $course->unique_users;
        }

        // Totals row.
        $export->add_data([]);
        $export->add_data([get_string('total', 'report_platform_usage'), '', $totalAccesses, $totalUsers, '', '']);
    }

    /**
     * Export user data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_users(\csv_export_writer $export): void {
        $this->add_report_header($export, get_string('userdetails', 'report_platform_usage'));

        // Description.
        $export->add_data([get_string('export_users_desc', 'report_platform_usage')]);
        $export->add_data([]); // Empty row.

        // Headers.
        $headers = [
            get_string('username', 'report_platform_usage'),
            get_string('fullname', 'report_platform_usage'),
            get_string('email', 'report_platform_usage'),
            get_string('logincount', 'report_platform_usage'),
            get_string('lastaccess', 'report_platform_usage'),
            get_string('created', 'report_platform_usage'),
            get_string('status', 'report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get user data.
        $users = $this->report->get_user_login_details();
        $activeThreshold = strtotime('-30 days');
        $activeCount = 0;
        $inactiveCount = 0;

        foreach ($users as $user) {
            $isActive = $user->lastaccess >= $activeThreshold;
            $status = $isActive ? get_string('active', 'report_platform_usage') : get_string('inactive', 'report_platform_usage');

            if ($isActive) {
                $activeCount++;
            } else {
                $inactiveCount++;
            }

            $row = [
                $user->username,
                fullname($user),
                $user->email,
                $user->login_count,
                $user->lastaccess ? userdate($user->lastaccess, '%d/%m/%Y %H:%M') : '-',
                userdate($user->timecreated, '%d/%m/%Y'),
                $status,
            ];
            $export->add_data($row);
        }

        // Summary.
        $export->add_data([]);
        $export->add_data([get_string('usersummary', 'report_platform_usage')]);
        $export->add_data([get_string('totalusers', 'report_platform_usage'), count($users)]);
        $export->add_data([get_string('activeusers', 'report_platform_usage'), $activeCount]);
        $export->add_data([get_string('inactiveusers', 'report_platform_usage'), $inactiveCount]);
    }

    /**
     * Export daily login data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_daily(\csv_export_writer $export): void {
        $this->add_report_header($export, get_string('dailylogins', 'report_platform_usage'));

        // Description.
        $export->add_data([get_string('export_daily_desc', 'report_platform_usage')]);
        $export->add_data([]); // Empty row.

        // Headers.
        $headers = [
            get_string('date', 'report_platform_usage'),
            get_string('logins', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get daily data.
        $daily = $this->report->get_daily_data_for_export();
        $totalLogins = 0;
        $maxLogins = 0;
        $maxDate = '';

        foreach ($daily as $day) {
            $row = [
                $day->login_date,
                $day->login_count,
                $day->unique_users,
            ];
            $export->add_data($row);

            $totalLogins += $day->login_count;
            if ($day->login_count > $maxLogins) {
                $maxLogins = $day->login_count;
                $maxDate = $day->login_date;
            }
        }

        // Summary.
        $export->add_data([]);
        $export->add_data([get_string('total', 'report_platform_usage') . ' ' . get_string('logins', 'report_platform_usage'), $totalLogins]);
        $export->add_data([get_string('maximum', 'report_platform_usage'), $maxLogins, $maxDate]);
        if (count($daily) > 0) {
            $export->add_data([get_string('average', 'report_platform_usage'), round($totalLogins / count($daily), 2)]);
        }
    }

    /**
     * Export activities data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_activities(\csv_export_writer $export): void {
        $this->add_report_header($export, get_string('activityaccessdetails', 'report_platform_usage'));

        // Description.
        $export->add_data([get_string('export_activities_desc', 'report_platform_usage')]);
        $export->add_data([]); // Empty row.

        // Headers.
        $headers = [
            get_string('activityname', 'report_platform_usage'),
            get_string('activitytype', 'report_platform_usage'),
            get_string('coursename', 'report_platform_usage'),
            get_string('activityaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
            get_string('avgaccessperuser', 'report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get activity data.
        $activities = $this->report->get_top_activities(100);

        foreach ($activities as $activity) {
            $avgAccess = $activity->unique_users > 0 ? round($activity->access_count / $activity->unique_users, 2) : 0;
            $row = [
                $activity->name,
                $activity->type_name,
                $activity->course_name,
                $activity->access_count,
                $activity->unique_users,
                $avgAccess,
            ];
            $export->add_data($row);
        }
    }

    /**
     * Export daily users data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_dailyusers(\csv_export_writer $export): void {
        $this->add_report_header($export, get_string('dailyusers', 'report_platform_usage'));

        // Description.
        $export->add_data([get_string('export_dailyusers_desc', 'report_platform_usage')]);
        $export->add_data([]); // Empty row.

        // Headers.
        $headers = [
            get_string('date', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get daily users data.
        $dailyUsers = $this->report->get_daily_users(30);

        for ($i = 0; $i < count($dailyUsers['labels']); $i++) {
            $row = [
                $dailyUsers['labels'][$i],
                $dailyUsers['data'][$i],
            ];
            $export->add_data($row);
        }

        // Statistics.
        $totalUsers = array_sum($dailyUsers['data']);
        $avgUsers = count($dailyUsers['data']) > 0 ? round($totalUsers / count($dailyUsers['data']), 1) : 0;
        $maxUsers = !empty($dailyUsers['data']) ? max($dailyUsers['data']) : 0;

        $export->add_data([]);
        $export->add_data([get_string('average', 'report_platform_usage'), $avgUsers]);
        $export->add_data([get_string('maximum', 'report_platform_usage'), $maxUsers]);
    }

    /**
     * Export dedication data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_dedication(\csv_export_writer $export): void {
        $this->add_report_header($export, get_string('topdedication', 'report_platform_usage'));

        // Description.
        $export->add_data([get_string('export_dedication_desc', 'report_platform_usage')]);
        $export->add_data([]); // Empty row.

        // Headers.
        $headers = [
            '#',
            get_string('coursename', 'report_platform_usage'),
            get_string('shortname', 'report_platform_usage'),
            get_string('totaldedication', 'report_platform_usage'),
            get_string('enrolledusers', 'report_platform_usage'),
            get_string('dedicationpercent', 'report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get dedication data.
        $dedication = $this->report->get_top_courses_dedication(100);

        foreach ($dedication as $course) {
            $row = [
                $course['rank'],
                $course['fullname'],
                $course['shortname'],
                $course['total_dedication_formatted'],
                $course['enrolled_students'],
                $course['dedication_percent'] . '%',
            ];
            $export->add_data($row);
        }
    }

    /**
     * Export complete summary.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_summary(\csv_export_writer $export): void {
        // Report header.
        $this->add_report_header($export, get_string('reporttitle', 'report_platform_usage'));

        // Report description.
        $export->add_data([get_string('export_report_desc', 'report_platform_usage')]);
        $export->add_data([]);

        // ========================================
        // SECTION 1: Login summary.
        // ========================================
        $export->add_data(['=== ' . get_string('platformaccess', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_logins_desc', 'report_platform_usage')]);
        $export->add_data([
            get_string('period', 'report_platform_usage'),
            get_string('logins', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ]);

        $summary = $this->report->get_login_summary();
        $export->add_data([get_string('today', 'report_platform_usage'), $summary['logins_today'], $summary['unique_users_today']]);
        $export->add_data([get_string('lastweek', 'report_platform_usage'), $summary['logins_week'], $summary['unique_users_week']]);
        $export->add_data([get_string('lastmonth', 'report_platform_usage'), $summary['logins_month'], $summary['unique_users_month']]);
        $export->add_data([]);

        // ========================================
        // SECTION 2: User summary.
        // ========================================
        $export->add_data(['=== ' . get_string('usersummary', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_users_desc', 'report_platform_usage')]);
        $userSummary = $this->report->get_user_activity_summary();
        $export->add_data([get_string('totalusers', 'report_platform_usage'), $userSummary['total']]);
        $export->add_data([get_string('activeusers', 'report_platform_usage'), $userSummary['active']]);
        $export->add_data([get_string('inactiveusers', 'report_platform_usage'), $userSummary['inactive']]);
        $export->add_data([]);

        // ========================================
        // SECTION 3: Completions summary.
        // ========================================
        $export->add_data(['=== ' . get_string('completions', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_completions_desc', 'report_platform_usage')]);
        $completions = $this->report->get_course_completions_summary();
        $export->add_data([get_string('completionstoday', 'report_platform_usage'), $completions['completions_today']]);
        $export->add_data([get_string('completionsweek', 'report_platform_usage'), $completions['completions_week']]);
        $export->add_data([get_string('completionsmonth', 'report_platform_usage'), $completions['completions_month']]);
        $export->add_data([get_string('totalcompletions', 'report_platform_usage'), $completions['total_completions']]);
        $export->add_data([]);

        // ========================================
        // SECTION 4: Top courses.
        // ========================================
        $export->add_data(['=== ' . get_string('topcourses', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_courses_desc', 'report_platform_usage')]);
        $export->add_data([
            get_string('coursename', 'report_platform_usage'),
            get_string('shortname', 'report_platform_usage'),
            get_string('courseaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ]);

        $courses = $this->report->get_top_courses(20);
        foreach ($courses as $course) {
            $export->add_data([$course->fullname, $course->shortname, $course->access_count, $course->unique_users]);
        }
        $export->add_data([]);

        // ========================================
        // SECTION 5: Top activities.
        // ========================================
        $export->add_data(['=== ' . get_string('topactivities', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_activities_desc', 'report_platform_usage')]);
        $export->add_data([
            get_string('activityname', 'report_platform_usage'),
            get_string('activitytype', 'report_platform_usage'),
            get_string('coursename', 'report_platform_usage'),
            get_string('activityaccesses', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ]);

        $activities = $this->report->get_top_activities(20);
        foreach ($activities as $activity) {
            $export->add_data([$activity->name, $activity->type_name, $activity->course_name, $activity->access_count, $activity->unique_users]);
        }
        $export->add_data([]);

        // ========================================
        // SECTION 6: Course dedication.
        // ========================================
        $export->add_data(['=== ' . get_string('topdedication', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_dedication_desc', 'report_platform_usage')]);
        $export->add_data([
            '#',
            get_string('coursename', 'report_platform_usage'),
            get_string('shortname', 'report_platform_usage'),
            get_string('totaldedication', 'report_platform_usage'),
            get_string('enrolledusers', 'report_platform_usage'),
            get_string('dedicationpercent', 'report_platform_usage'),
        ]);

        $dedication = $this->report->get_top_courses_dedication(20);
        foreach ($dedication as $course) {
            $export->add_data([
                $course['rank'],
                $course['fullname'],
                $course['shortname'],
                $course['total_dedication_formatted'],
                $course['enrolled_students'],
                $course['dedication_percent'] . '%',
            ]);
        }
        $export->add_data([]);

        // ========================================
        // SECTION 7: Daily logins.
        // ========================================
        $export->add_data(['=== ' . get_string('dailylogins', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_daily_desc', 'report_platform_usage')]);
        $export->add_data([
            get_string('date', 'report_platform_usage'),
            get_string('logins', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ]);

        $daily = $this->report->get_daily_data_for_export();
        foreach ($daily as $day) {
            $export->add_data([$day->login_date, $day->login_count, $day->unique_users]);
        }
        $export->add_data([]);

        // ========================================
        // SECTION 8: Daily unique users.
        // ========================================
        $export->add_data(['=== ' . get_string('dailyusers', 'report_platform_usage') . ' ===']);
        $export->add_data([get_string('export_dailyusers_desc', 'report_platform_usage')]);
        $export->add_data([
            get_string('date', 'report_platform_usage'),
            get_string('uniqueusers', 'report_platform_usage'),
        ]);

        $dailyUsers = $this->report->get_daily_users(30);
        for ($i = 0; $i < count($dailyUsers['labels']); $i++) {
            $export->add_data([$dailyUsers['labels'][$i], $dailyUsers['data'][$i]]);
        }

        // Statistics.
        $avgUsers = count($dailyUsers['data']) > 0 ? round(array_sum($dailyUsers['data']) / count($dailyUsers['data']), 1) : 0;
        $maxUsers = !empty($dailyUsers['data']) ? max($dailyUsers['data']) : 0;
        $export->add_data([]);
        $export->add_data([get_string('average', 'report_platform_usage'), $avgUsers]);
        $export->add_data([get_string('maximum', 'report_platform_usage'), $maxUsers]);
    }
}
