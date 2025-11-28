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
 * @package   local_report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_report_platform_usage;

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
        $filename = 'platform_usage_' . $this->type . '_' . date('Ymd_His');

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
            default:
                $this->export_logins($export);
        }

        $export->download_file();
    }

    /**
     * Export login summary data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_logins(\csv_export_writer $export): void {
        // Headers.
        $headers = [
            get_string('period', 'local_report_platform_usage'),
            get_string('logins', 'local_report_platform_usage'),
            get_string('uniqueusers', 'local_report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get summary data.
        $summary = $this->report->get_login_summary();

        $rows = [
            [get_string('today', 'local_report_platform_usage'), $summary['logins_today'], $summary['unique_users_today']],
            [get_string('lastweek', 'local_report_platform_usage'), $summary['logins_week'], $summary['unique_users_week']],
            [get_string('lastmonth', 'local_report_platform_usage'), $summary['logins_month'], $summary['unique_users_month']],
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
        // Headers.
        $headers = [
            get_string('coursename', 'local_report_platform_usage'),
            'Shortname',
            get_string('courseaccesses', 'local_report_platform_usage'),
            get_string('uniqueusers', 'local_report_platform_usage'),
            get_string('lastaccess', 'local_report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get course data.
        $courses = $this->report->get_course_access_details();

        foreach ($courses as $course) {
            $row = [
                $course->fullname,
                $course->shortname,
                $course->access_count,
                $course->unique_users,
                userdate($course->last_access),
            ];
            $export->add_data($row);
        }
    }

    /**
     * Export user data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_users(\csv_export_writer $export): void {
        // Headers.
        $headers = [
            get_string('username', 'local_report_platform_usage'),
            get_string('fullname', 'local_report_platform_usage'),
            get_string('email', 'local_report_platform_usage'),
            get_string('logincount', 'local_report_platform_usage'),
            get_string('lastaccess', 'local_report_platform_usage'),
            'Created',
        ];
        $export->add_data($headers);

        // Get user data.
        $users = $this->report->get_user_login_details();

        foreach ($users as $user) {
            $row = [
                $user->username,
                fullname($user),
                $user->email,
                $user->login_count,
                $user->lastaccess ? userdate($user->lastaccess) : '-',
                userdate($user->timecreated),
            ];
            $export->add_data($row);
        }
    }

    /**
     * Export daily login data.
     *
     * @param \csv_export_writer $export Export writer
     */
    protected function export_daily(\csv_export_writer $export): void {
        // Headers.
        $headers = [
            get_string('date', 'local_report_platform_usage'),
            get_string('logins', 'local_report_platform_usage'),
            get_string('uniqueusers', 'local_report_platform_usage'),
        ];
        $export->add_data($headers);

        // Get daily data.
        $daily = $this->report->get_daily_data_for_export();

        foreach ($daily as $day) {
            $row = [
                $day->login_date,
                $day->login_count,
                $day->unique_users,
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
        // Login summary section.
        $export->add_data([get_string('platformaccess', 'local_report_platform_usage')]);
        $export->add_data([
            get_string('period', 'local_report_platform_usage'),
            get_string('logins', 'local_report_platform_usage'),
            get_string('uniqueusers', 'local_report_platform_usage'),
        ]);

        $summary = $this->report->get_login_summary();
        $export->add_data([get_string('today', 'local_report_platform_usage'), $summary['logins_today'], $summary['unique_users_today']]);
        $export->add_data([get_string('lastweek', 'local_report_platform_usage'), $summary['logins_week'], $summary['unique_users_week']]);
        $export->add_data([get_string('lastmonth', 'local_report_platform_usage'), $summary['logins_month'], $summary['unique_users_month']]);

        $export->add_data([]); // Empty row.

        // User summary.
        $export->add_data([get_string('usersummary', 'local_report_platform_usage')]);
        $userSummary = $this->report->get_user_activity_summary();
        $export->add_data([get_string('totalusers', 'local_report_platform_usage'), $userSummary['total']]);
        $export->add_data([get_string('activeusers', 'local_report_platform_usage'), $userSummary['active']]);
        $export->add_data([get_string('inactiveusers', 'local_report_platform_usage'), $userSummary['inactive']]);

        $export->add_data([]); // Empty row.

        // Top courses.
        $export->add_data([get_string('topcourses', 'local_report_platform_usage')]);
        $export->add_data([
            get_string('coursename', 'local_report_platform_usage'),
            get_string('courseaccesses', 'local_report_platform_usage'),
            get_string('uniqueusers', 'local_report_platform_usage'),
        ]);

        $courses = $this->report->get_top_courses(10);
        foreach ($courses as $course) {
            $export->add_data([$course->fullname, $course->access_count, $course->unique_users]);
        }

        $export->add_data([]); // Empty row.

        // Daily logins.
        $export->add_data([get_string('dailylogins', 'local_report_platform_usage')]);
        $export->add_data([
            get_string('date', 'local_report_platform_usage'),
            get_string('logins', 'local_report_platform_usage'),
            get_string('uniqueusers', 'local_report_platform_usage'),
        ]);

        $daily = $this->report->get_daily_data_for_export();
        foreach ($daily as $day) {
            $export->add_data([$day->login_date, $day->login_count, $day->unique_users]);
        }
    }
}
