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
 * Report data handler class.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_platform_usage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to handle report data retrieval.
 */
class report {

    /** @var int Company ID filter */
    protected $companyid;

    /** @var int Date from timestamp */
    protected $datefrom;

    /** @var int Date to timestamp */
    protected $dateto;

    /** @var array Cached user IDs for the company */
    protected $userids = null;

    /**
     * Constructor.
     *
     * @param int $companyid Company ID (0 for all companies)
     * @param int $datefrom Start date timestamp
     * @param int $dateto End date timestamp
     */
    public function __construct(int $companyid = 0, int $datefrom = 0, int $dateto = 0) {
        $this->companyid = $companyid;
        $this->datefrom = $datefrom ?: strtotime('-30 days midnight');
        $this->dateto = $dateto ?: time();
    }

    /**
     * Get company user IDs.
     *
     * @return array User IDs
     */
    protected function get_company_userids(): array {
        global $DB;

        if ($this->userids !== null) {
            return $this->userids;
        }

        if ($this->companyid > 0) {
            // Check if company_users table exists.
            $dbman = $DB->get_manager();
            if ($dbman->table_exists('company_users')) {
                $this->userids = $DB->get_fieldset_select(
                    'company_users',
                    'userid',
                    'companyid = ?',
                    [$this->companyid]
                );
            } else {
                $this->userids = [];
            }
        } else {
            // All users.
            $this->userids = $DB->get_fieldset_select(
                'user',
                'id',
                'deleted = 0 AND id > 1'
            );
        }

        return $this->userids;
    }

    /**
     * Get SQL conditions for user filtering.
     *
     * @param string $userfield The field name for user ID
     * @return array [sql, params]
     */
    protected function get_user_sql(string $userfield = 'userid'): array {
        global $DB;

        $userids = $this->get_company_userids();
        if (empty($userids)) {
            return ['1=0', []];
        }

        list($sql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');
        return ["$userfield $sql", $params];
    }

    /**
     * Get login statistics summary.
     *
     * @return array Statistics
     */
    public function get_login_summary(): array {
        global $DB;

        $now = time();
        $todaystart = strtotime('today midnight');
        $weekstart = strtotime('-7 days midnight');
        $monthstart = strtotime('-30 days midnight');

        list($usersql, $userparams) = $this->get_user_sql('userid');

        // Check if logstore table exists.
        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return [
                'logins_today' => 0,
                'logins_week' => 0,
                'logins_month' => 0,
                'unique_users_today' => 0,
                'unique_users_week' => 0,
                'unique_users_month' => 0,
            ];
        }

        // Logins today.
        $loginstoday = $DB->count_records_select(
            'logstore_standard_log',
            "eventname = :event AND timecreated >= :time AND $usersql",
            array_merge(['event' => '\\core\\event\\user_loggedin', 'time' => $todaystart], $userparams)
        );

        // Unique users today.
        $uniquetoday = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT userid)
             FROM {logstore_standard_log}
             WHERE eventname = :event AND timecreated >= :time AND $usersql",
            array_merge(['event' => '\\core\\event\\user_loggedin', 'time' => $todaystart], $userparams)
        );

        // Logins last 7 days.
        $userparams2 = [];
        list($usersql2, $userparams2) = $this->get_user_sql('userid');
        $loginsweek = $DB->count_records_select(
            'logstore_standard_log',
            "eventname = :event AND timecreated >= :time AND $usersql2",
            array_merge(['event' => '\\core\\event\\user_loggedin', 'time' => $weekstart], $userparams2)
        );

        // Unique users last 7 days.
        $userparams3 = [];
        list($usersql3, $userparams3) = $this->get_user_sql('userid');
        $uniqueweek = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT userid)
             FROM {logstore_standard_log}
             WHERE eventname = :event AND timecreated >= :time AND $usersql3",
            array_merge(['event' => '\\core\\event\\user_loggedin', 'time' => $weekstart], $userparams3)
        );

        // Logins last 30 days.
        $userparams4 = [];
        list($usersql4, $userparams4) = $this->get_user_sql('userid');
        $loginsmonth = $DB->count_records_select(
            'logstore_standard_log',
            "eventname = :event AND timecreated >= :time AND $usersql4",
            array_merge(['event' => '\\core\\event\\user_loggedin', 'time' => $monthstart], $userparams4)
        );

        // Unique users last 30 days.
        $userparams5 = [];
        list($usersql5, $userparams5) = $this->get_user_sql('userid');
        $uniquemonth = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT userid)
             FROM {logstore_standard_log}
             WHERE eventname = :event AND timecreated >= :time AND $usersql5",
            array_merge(['event' => '\\core\\event\\user_loggedin', 'time' => $monthstart], $userparams5)
        );

        return [
            'logins_today' => $loginstoday,
            'logins_week' => $loginsweek,
            'logins_month' => $loginsmonth,
            'unique_users_today' => $uniquetoday,
            'unique_users_week' => $uniqueweek,
            'unique_users_month' => $uniquemonth,
        ];
    }

    /**
     * Get daily login data for chart.
     *
     * @param int $days Number of days to show
     * @return array [labels, logins, unique_users]
     */
    public function get_daily_logins(int $days = 30): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return ['labels' => [], 'logins' => [], 'unique_users' => []];
        }

        $starttime = strtotime("-{$days} days midnight");
        list($usersql, $userparams) = $this->get_user_sql('userid');

        $sql = "SELECT DATE(FROM_UNIXTIME(timecreated)) as login_date,
                       COUNT(*) as login_count,
                       COUNT(DISTINCT userid) as unique_users
                FROM {logstore_standard_log}
                WHERE eventname = :event
                  AND timecreated >= :starttime
                  AND $usersql
                GROUP BY DATE(FROM_UNIXTIME(timecreated))
                ORDER BY login_date ASC";

        $params = array_merge([
            'event' => '\\core\\event\\user_loggedin',
            'starttime' => $starttime,
        ], $userparams);

        $records = $DB->get_records_sql($sql, $params);

        $labels = [];
        $logins = [];
        $uniqueusers = [];

        // Fill in all dates.
        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($date));
            $logins[$date] = 0;
            $uniqueusers[$date] = 0;
        }

        foreach ($records as $record) {
            if (isset($logins[$record->login_date])) {
                $logins[$record->login_date] = (int) $record->login_count;
                $uniqueusers[$record->login_date] = (int) $record->unique_users;
            }
        }

        return [
            'labels' => $labels,
            'logins' => array_values($logins),
            'unique_users' => array_values($uniqueusers),
        ];
    }

    /**
     * Get user activity summary.
     *
     * @return array [total, active, inactive]
     */
    public function get_user_activity_summary(): array {
        global $DB;

        $userids = $this->get_company_userids();
        $total = count($userids);

        if ($total == 0) {
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
            ];
        }

        // Consider active if logged in within last 30 days.
        $activeThreshold = strtotime('-30 days');

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        $active = $DB->count_records_select(
            'user',
            "id $usersql AND lastaccess >= :threshold",
            array_merge($userparams, ['threshold' => $activeThreshold])
        );

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
        ];
    }

    /**
     * Get top courses by access.
     *
     * @param int $limit Number of courses to return
     * @return array Course data
     */
    public function get_top_courses(int $limit = 10): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return [];
        }

        list($usersql, $userparams) = $this->get_user_sql('l.userid');

        $sql = "SELECT c.id, c.shortname, c.fullname,
                       COUNT(*) as access_count,
                       COUNT(DISTINCT l.userid) as unique_users
                FROM {logstore_standard_log} l
                JOIN {course} c ON l.courseid = c.id
                WHERE l.eventname = :event
                  AND l.timecreated BETWEEN :datefrom AND :dateto
                  AND l.courseid IS NOT NULL
                  AND l.courseid > 1
                  AND $usersql
                GROUP BY c.id, c.shortname, c.fullname
                ORDER BY access_count DESC
                LIMIT $limit";

        $params = array_merge([
            'event' => '\\core\\event\\course_viewed',
            'datefrom' => $this->datefrom,
            'dateto' => $this->dateto,
        ], $userparams);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get course access trends.
     *
     * @param int $days Number of days
     * @return array [labels, data]
     */
    public function get_course_access_trends(int $days = 30): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return ['labels' => [], 'data' => []];
        }

        $starttime = strtotime("-{$days} days midnight");
        list($usersql, $userparams) = $this->get_user_sql('userid');

        $sql = "SELECT DATE(FROM_UNIXTIME(timecreated)) as access_date,
                       COUNT(*) as access_count
                FROM {logstore_standard_log}
                WHERE eventname = :event
                  AND timecreated >= :starttime
                  AND courseid IS NOT NULL
                  AND courseid > 1
                  AND $usersql
                GROUP BY DATE(FROM_UNIXTIME(timecreated))
                ORDER BY access_date ASC";

        $params = array_merge([
            'event' => '\\core\\event\\course_viewed',
            'starttime' => $starttime,
        ], $userparams);

        $records = $DB->get_records_sql($sql, $params);

        $labels = [];
        $data = [];

        // Fill in all dates.
        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($date));
            $data[$date] = 0;
        }

        foreach ($records as $record) {
            if (isset($data[$record->access_date])) {
                $data[$record->access_date] = (int) $record->access_count;
            }
        }

        return [
            'labels' => $labels,
            'data' => array_values($data),
        ];
    }

    /**
     * Get top activities by access.
     *
     * @param int $limit Number of activities to return
     * @return array Activity data
     */
    public function get_top_activities(int $limit = 10): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return [];
        }

        list($usersql, $userparams) = $this->get_user_sql('l.userid');

        $sql = "SELECT l.contextinstanceid, l.component, l.courseid,
                       COUNT(*) as access_count,
                       COUNT(DISTINCT l.userid) as unique_users
                FROM {logstore_standard_log} l
                WHERE l.action = 'viewed'
                  AND l.target = 'course_module'
                  AND l.timecreated BETWEEN :datefrom AND :dateto
                  AND $usersql
                GROUP BY l.contextinstanceid, l.component, l.courseid
                ORDER BY access_count DESC
                LIMIT $limit";

        $params = array_merge([
            'datefrom' => $this->datefrom,
            'dateto' => $this->dateto,
        ], $userparams);

        $records = $DB->get_records_sql($sql, $params);

        // Get activity names.
        $result = [];
        foreach ($records as $record) {
            $cm = $DB->get_record('course_modules', ['id' => $record->contextinstanceid]);
            if ($cm) {
                $modname = str_replace('mod_', '', $record->component);
                $modinfo = $DB->get_record($modname, ['id' => $cm->instance]);
                $course = $DB->get_record('course', ['id' => $record->courseid], 'id, fullname, shortname');
                if ($modinfo && isset($modinfo->name)) {
                    $result[] = (object) [
                        'id' => $record->contextinstanceid,
                        'name' => $modinfo->name,
                        'type' => $modname,
                        'type_name' => self::get_module_name($modname),
                        'course_id' => $record->courseid,
                        'course_name' => $course ? $course->fullname : '',
                        'course_shortname' => $course ? $course->shortname : '',
                        'access_count' => $record->access_count,
                        'unique_users' => $record->unique_users,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get human-readable module name.
     *
     * @param string $modname Module name (e.g., 'assign', 'forum')
     * @return string Human-readable name
     */
    public static function get_module_name(string $modname): string {
        // Try to get the plugin name from language strings.
        $pluginname = get_string('pluginname', 'mod_' . $modname);
        if (strpos($pluginname, '[[') === false) {
            return $pluginname;
        }
        // Fallback to capitalized module name.
        return ucfirst($modname);
    }

    /**
     * Get all report data for AJAX.
     *
     * @return array All report data
     */
    public function get_all_data(): array {
        return [
            'login_summary' => $this->get_login_summary(),
            'user_summary' => $this->get_user_activity_summary(),
            'daily_logins' => $this->get_daily_logins(30),
            'course_access_trends' => $this->get_course_access_trends(30),
            'top_courses' => array_values($this->get_top_courses(10)),
            'top_activities' => $this->get_top_activities(10),
        ];
    }

    /**
     * Get detailed user login data for export.
     *
     * @return array User data
     */
    public function get_user_login_details(): array {
        global $DB;

        $userids = $this->get_company_userids();
        if (empty($userids)) {
            return [];
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        // Check if logstore exists.
        $dbman = $DB->get_manager();
        $hasLogstore = $dbman->table_exists('logstore_standard_log');

        if ($hasLogstore) {
            $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email,
                           u.lastaccess, u.timecreated,
                           COALESCE(l.login_count, 0) as login_count
                    FROM {user} u
                    LEFT JOIN (
                        SELECT userid, COUNT(*) as login_count
                        FROM {logstore_standard_log}
                        WHERE eventname = :event
                          AND timecreated BETWEEN :datefrom AND :dateto
                        GROUP BY userid
                    ) l ON u.id = l.userid
                    WHERE u.id $usersql
                    ORDER BY login_count DESC, u.lastname, u.firstname";

            $params = array_merge([
                'event' => '\\core\\event\\user_loggedin',
                'datefrom' => $this->datefrom,
                'dateto' => $this->dateto,
            ], $userparams);
        } else {
            $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email,
                           u.lastaccess, u.timecreated, 0 as login_count
                    FROM {user} u
                    WHERE u.id $usersql
                    ORDER BY u.lastname, u.firstname";
            $params = $userparams;
        }

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get course access data for export.
     *
     * @return array Course data
     */
    public function get_course_access_details(): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return [];
        }

        list($usersql, $userparams) = $this->get_user_sql('l.userid');

        $sql = "SELECT c.id, c.shortname, c.fullname,
                       COUNT(*) as access_count,
                       COUNT(DISTINCT l.userid) as unique_users,
                       MAX(l.timecreated) as last_access
                FROM {logstore_standard_log} l
                JOIN {course} c ON l.courseid = c.id
                WHERE l.eventname = :event
                  AND l.timecreated BETWEEN :datefrom AND :dateto
                  AND l.courseid IS NOT NULL
                  AND l.courseid > 1
                  AND $usersql
                GROUP BY c.id, c.shortname, c.fullname
                ORDER BY access_count DESC";

        $params = array_merge([
            'event' => '\\core\\event\\course_viewed',
            'datefrom' => $this->datefrom,
            'dateto' => $this->dateto,
        ], $userparams);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get daily login data for export.
     *
     * @return array Daily data
     */
    public function get_daily_data_for_export(): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return [];
        }

        list($usersql, $userparams) = $this->get_user_sql('userid');

        $sql = "SELECT DATE(FROM_UNIXTIME(timecreated)) as login_date,
                       COUNT(*) as login_count,
                       COUNT(DISTINCT userid) as unique_users
                FROM {logstore_standard_log}
                WHERE eventname = :event
                  AND timecreated BETWEEN :datefrom AND :dateto
                  AND $usersql
                GROUP BY DATE(FROM_UNIXTIME(timecreated))
                ORDER BY login_date ASC";

        $params = array_merge([
            'event' => '\\core\\event\\user_loggedin',
            'datefrom' => $this->datefrom,
            'dateto' => $this->dateto,
        ], $userparams);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get list of companies.
     *
     * @return array Company list
     */
    public static function get_companies(): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('company')) {
            return [];
        }

        return $DB->get_records_menu('company', null, 'name ASC', 'id, name');
    }
}
