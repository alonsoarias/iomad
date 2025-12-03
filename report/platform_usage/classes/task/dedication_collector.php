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
 * Task that generates session duration data for reports.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_platform_usage\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Dedication data generator task.
 *
 * This task calculates session duration data for all courses and stores it
 * in the report_platform_usage_ded table for faster report generation.
 */
class dedication_collector extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_collect_dedication', 'report_platform_usage');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $lastruntime = get_config('report_platform_usage', 'dedication_lastcalculated');
        if (empty($lastruntime)) {
            mtrace("This is the first time this task has been run, calculating data for the last 12 weeks");
            // First time this task has been run - pull in the last 12 weeks of time calculations.
            $lastruntime = time() - WEEKSECS * 12;
        } else if ($lastruntime > time() - (2 * HOURSECS)) {
            mtrace("This task can only be triggered every 2 hours");
            return;
        }

        $this->generate_stats($lastruntime, time());
    }

    /**
     * Generate stats for a time period.
     *
     * @param int $timestart
     * @param int $timeend
     */
    protected function generate_stats($timestart, $timeend) {
        if ($timeend - $timestart > WEEKSECS) {
            // Break it down into bite-sized weeks.
            while ($timeend - $timestart > WEEKSECS) {
                $timechunkend = $timestart + WEEKSECS;
                $this->generate_stats($timestart, $timechunkend);
                $timestart = $timechunkend;
            }
        } else {
            $this->calculate($timestart, $timeend);
        }
    }

    /**
     * Calculate stats for a time period.
     *
     * @param int $timestart
     * @param int $timeend
     */
    protected function calculate($timestart, $timeend) {
        global $DB;

        mtrace("Calculating dedication stats from: " . userdate($timestart) . " to: " . userdate($timeend));

        // Get session limit and ignore session limit from settings.
        $sessionlimit = get_config('report_platform_usage', 'session_limit');
        $sessionlimit = !empty($sessionlimit) ? (int)$sessionlimit : HOURSECS;

        $ignoresessionslimit = get_config('report_platform_usage', 'ignore_sessions_limit');
        $ignoresessionslimit = !empty($ignoresessionslimit) ? (int)$ignoresessionslimit : MINSECS;

        // Get list of courses and users we want to calculate for.
        $sql = "SELECT DISTINCT " . $DB->sql_concat_join("':'", ['courseid', 'userid']) . " as tmpid, courseid, userid
                FROM {logstore_standard_log}
                WHERE timecreated >= :timestart AND timecreated < :timeend AND userid > 0 AND courseid > 0";
        $records = $DB->get_recordset_sql($sql, ['timestart' => $timestart, 'timeend' => $timeend]);

        $courses = [];
        foreach ($records as $record) {
            if (!isset($courses[$record->courseid])) {
                $courses[$record->courseid] = [];
            }
            $courses[$record->courseid][] = $record->userid;
        }
        $records->close();

        mtrace("Found " . count($courses) . " courses with activity");

        $insertrecords = [];
        foreach ($courses as $courseid => $users) {
            $course = $DB->get_record('course', ['id' => $courseid]);
            if (empty($course)) {
                mtrace("Course $courseid not found, it may have been deleted.");
                continue;
            }

            foreach ($users as $userid) {
                $events = $this->get_user_events($courseid, $userid, $timestart, $timeend);
                $sessions = $this->calculate_user_sessions($events, $sessionlimit, $ignoresessionslimit);

                foreach ($sessions as $session) {
                    if ($session['duration'] > 0) {
                        $data = new \stdClass();
                        $data->userid = $userid;
                        $data->courseid = $courseid;
                        $data->timespent = $session['duration'];
                        $data->timestart = $session['start'];
                        $insertrecords[] = $data;
                    }
                }
            }
        }

        if (!empty($insertrecords)) {
            mtrace("Inserting " . count($insertrecords) . " dedication records");
            $DB->insert_records('report_platform_usage_ded', $insertrecords);
        }

        // Update lastcalculated entry to prevent re-processing of older timeframes.
        if (get_config('report_platform_usage', 'dedication_lastcalculated') < $timeend) {
            set_config('dedication_lastcalculated', $timeend, 'report_platform_usage');
        }
    }

    /**
     * Get user events for a course in a time range.
     *
     * @param int $courseid
     * @param int $userid
     * @param int $timestart
     * @param int $timeend
     * @return array
     */
    protected function get_user_events($courseid, $userid, $timestart, $timeend) {
        global $DB;

        $sql = "SELECT timecreated
                FROM {logstore_standard_log}
                WHERE courseid = :courseid
                  AND userid = :userid
                  AND timecreated >= :timestart
                  AND timecreated < :timeend
                  AND origin != 'cli'
                ORDER BY timecreated ASC";

        return $DB->get_fieldset_sql($sql, [
            'courseid' => $courseid,
            'userid' => $userid,
            'timestart' => $timestart,
            'timeend' => $timeend
        ]);
    }

    /**
     * Calculate user sessions from events.
     *
     * @param array $events Array of timestamps
     * @param int $sessionlimit Maximum time between events to be considered same session
     * @param int $ignoresessionslimit Minimum session duration to count
     * @return array Array of sessions with start and duration
     */
    protected function calculate_user_sessions($events, $sessionlimit, $ignoresessionslimit) {
        if (empty($events)) {
            return [];
        }

        $sessions = [];
        $sessionstart = $events[0];
        $previoustime = $events[0];

        foreach ($events as $i => $eventtime) {
            if ($i === 0) {
                continue;
            }

            $timediff = $eventtime - $previoustime;
            if ($timediff > $sessionlimit) {
                // Session ended - calculate duration.
                $duration = $previoustime - $sessionstart;
                if ($duration > $ignoresessionslimit) {
                    $sessions[] = [
                        'start' => $sessionstart,
                        'duration' => $duration
                    ];
                }
                $sessionstart = $eventtime;
            }
            $previoustime = $eventtime;
        }

        // Finalize last session.
        $duration = $previoustime - $sessionstart;
        if ($duration > $ignoresessionslimit) {
            $sessions[] = [
                'start' => $sessionstart,
                'duration' => $duration
            ];
        }

        return $sessions;
    }
}
