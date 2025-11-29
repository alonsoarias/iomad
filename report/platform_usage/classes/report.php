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
 * Report data handler class with caching support.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_platform_usage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to handle report data retrieval with MUC caching.
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

    /** @var \cache Cache instance for report data */
    protected $cache;

    /** @var \cache Cache instance for company users */
    protected $usercache;

    /** @var bool Whether to use cache */
    protected $usecache;

    /**
     * Constructor.
     *
     * @param int $companyid Company ID (0 for all companies)
     * @param int $datefrom Start date timestamp
     * @param int $dateto End date timestamp
     * @param bool $usecache Whether to use cache (default true)
     */
    public function __construct(int $companyid = 0, int $datefrom = 0, int $dateto = 0, bool $usecache = true) {
        $this->companyid = $companyid;
        $this->datefrom = $datefrom ?: strtotime('-30 days midnight');
        $this->dateto = $dateto ?: time();
        $this->usecache = $usecache;

        // Initialize caches.
        if ($this->usecache) {
            $this->cache = \cache::make('report_platform_usage', 'reportdata');
            $this->usercache = \cache::make('report_platform_usage', 'companyusers');
        }
    }

    /**
     * Get cache key for current parameters.
     *
     * @param string $type Data type
     * @return string Cache key
     */
    protected function get_cache_key(string $type): string {
        // Round timestamps to reduce cache variations.
        $datefromround = floor($this->datefrom / 3600) * 3600;
        $datetoround = floor($this->dateto / 3600) * 3600;
        return "{$type}_{$this->companyid}_{$datefromround}_{$datetoround}";
    }

    /**
     * Get data from cache or compute.
     *
     * @param string $type Cache key type
     * @param callable $callback Function to compute data if not cached
     * @return mixed Cached or computed data
     */
    protected function get_cached_data(string $type, callable $callback) {
        if (!$this->usecache) {
            return $callback();
        }

        $key = $this->get_cache_key($type);
        $data = $this->cache->get($key);

        if ($data === false) {
            $data = $callback();
            $this->cache->set($key, $data);
        }

        return $data;
    }

    /**
     * Purge all cached data for this report.
     */
    public function purge_cache(): void {
        if ($this->usecache) {
            $this->cache->purge();
            $this->usercache->purge();
        }
    }

    /**
     * Get company user IDs with caching.
     *
     * @return array User IDs
     */
    protected function get_company_userids(): array {
        global $DB;

        if ($this->userids !== null) {
            return $this->userids;
        }

        // Try cache first.
        if ($this->usecache) {
            $cachekey = 'company_' . $this->companyid;
            $cached = $this->usercache->get($cachekey);
            if ($cached !== false) {
                $this->userids = $cached;
                return $this->userids;
            }
        }

        if ($this->companyid > 0) {
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
            $this->userids = $DB->get_fieldset_select(
                'user',
                'id',
                'deleted = 0 AND id > 1'
            );
        }

        // Store in cache.
        if ($this->usecache) {
            $cachekey = 'company_' . $this->companyid;
            $this->usercache->set($cachekey, $this->userids);
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
     * Get login statistics summary (cached).
     *
     * @return array Statistics
     */
    public function get_login_summary(): array {
        return $this->get_cached_data('login_summary', function() {
            return $this->compute_login_summary();
        });
    }

    /**
     * Compute login statistics summary.
     *
     * @return array Statistics
     */
    protected function compute_login_summary(): array {
        global $DB;

        $todaystart = strtotime('today midnight');
        $weekstart = strtotime('-7 days midnight');
        $monthstart = strtotime('-30 days midnight');

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

        $userids = $this->get_company_userids();
        if (empty($userids)) {
            return [
                'logins_today' => 0,
                'logins_week' => 0,
                'logins_month' => 0,
                'unique_users_today' => 0,
                'unique_users_week' => 0,
                'unique_users_month' => 0,
            ];
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        // Single query for all time periods.
        $sql = "SELECT
                    SUM(CASE WHEN timecreated >= :today THEN 1 ELSE 0 END) as logins_today,
                    COUNT(DISTINCT CASE WHEN timecreated >= :today2 THEN userid END) as unique_today,
                    SUM(CASE WHEN timecreated >= :week THEN 1 ELSE 0 END) as logins_week,
                    COUNT(DISTINCT CASE WHEN timecreated >= :week2 THEN userid END) as unique_week,
                    SUM(CASE WHEN timecreated >= :month THEN 1 ELSE 0 END) as logins_month,
                    COUNT(DISTINCT CASE WHEN timecreated >= :month2 THEN userid END) as unique_month
                FROM {logstore_standard_log}
                WHERE eventname = :event
                  AND userid $usersql";

        $params = array_merge([
            'event' => '\\core\\event\\user_loggedin',
            'today' => $todaystart,
            'today2' => $todaystart,
            'week' => $weekstart,
            'week2' => $weekstart,
            'month' => $monthstart,
            'month2' => $monthstart,
        ], $userparams);

        $result = $DB->get_record_sql($sql, $params);

        return [
            'logins_today' => (int) ($result->logins_today ?? 0),
            'logins_week' => (int) ($result->logins_week ?? 0),
            'logins_month' => (int) ($result->logins_month ?? 0),
            'unique_users_today' => (int) ($result->unique_today ?? 0),
            'unique_users_week' => (int) ($result->unique_week ?? 0),
            'unique_users_month' => (int) ($result->unique_month ?? 0),
        ];
    }

    /**
     * Get daily login data for chart (cached).
     *
     * @param int $days Number of days to show
     * @return array [labels, logins, unique_users]
     */
    public function get_daily_logins(int $days = 30): array {
        return $this->get_cached_data("daily_logins_{$days}", function() use ($days) {
            return $this->compute_daily_logins($days);
        });
    }

    /**
     * Compute daily login data.
     *
     * @param int $days Number of days
     * @return array
     */
    protected function compute_daily_logins(int $days): array {
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
     * Get user activity summary (cached).
     *
     * @return array [total, active, inactive]
     */
    public function get_user_activity_summary(): array {
        return $this->get_cached_data('user_activity', function() {
            return $this->compute_user_activity_summary();
        });
    }

    /**
     * Compute user activity summary.
     *
     * @return array
     */
    protected function compute_user_activity_summary(): array {
        global $DB;

        $userids = $this->get_company_userids();
        $total = count($userids);

        if ($total == 0) {
            return ['total' => 0, 'active' => 0, 'inactive' => 0];
        }

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
     * Get top courses by access (cached).
     *
     * @param int $limit Number of courses to return
     * @return array Course data
     */
    public function get_top_courses(int $limit = 10): array {
        return $this->get_cached_data("top_courses_{$limit}", function() use ($limit) {
            return $this->compute_top_courses($limit);
        });
    }

    /**
     * Compute top courses.
     *
     * @param int $limit
     * @return array
     */
    protected function compute_top_courses(int $limit): array {
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
     * Get course access trends (cached).
     *
     * @param int $days Number of days
     * @return array [labels, data]
     */
    public function get_course_access_trends(int $days = 30): array {
        return $this->get_cached_data("course_trends_{$days}", function() use ($days) {
            return $this->compute_course_access_trends($days);
        });
    }

    /**
     * Compute course access trends.
     *
     * @param int $days
     * @return array
     */
    protected function compute_course_access_trends(int $days): array {
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
     * Get top activities by access (cached).
     *
     * @param int $limit Number of activities to return
     * @return array Activity data
     */
    public function get_top_activities(int $limit = 10): array {
        return $this->get_cached_data("top_activities_{$limit}", function() use ($limit) {
            return $this->compute_top_activities($limit);
        });
    }

    /**
     * Compute top activities.
     *
     * @param int $limit
     * @return array
     */
    protected function compute_top_activities(int $limit): array {
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
        $pluginname = get_string('pluginname', 'mod_' . $modname);
        if (strpos($pluginname, '[[') === false) {
            return $pluginname;
        }
        return ucfirst($modname);
    }

    /**
     * Get all report data for AJAX (cached).
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
            'completions_summary' => $this->get_course_completions_summary(),
            'dashboard_access' => $this->get_dashboard_access(),
            'completion_trends' => $this->get_completion_trends(30),
            'logout_summary' => $this->get_logout_summary(),
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
     * Get course completions summary (cached).
     *
     * @return array Completion statistics
     */
    public function get_course_completions_summary(): array {
        return $this->get_cached_data('completions_summary', function() {
            return $this->compute_course_completions_summary();
        });
    }

    /**
     * Compute course completions summary.
     *
     * @return array
     */
    protected function compute_course_completions_summary(): array {
        global $DB;

        $userids = $this->get_company_userids();
        if (empty($userids)) {
            return [
                'completions_today' => 0,
                'completions_week' => 0,
                'completions_month' => 0,
                'total_completions' => 0,
            ];
        }

        $todaystart = strtotime('today midnight');
        $weekstart = strtotime('-7 days midnight');
        $monthstart = strtotime('-30 days midnight');

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        $sql = "SELECT
                    SUM(CASE WHEN timecompleted >= :today THEN 1 ELSE 0 END) as completions_today,
                    SUM(CASE WHEN timecompleted >= :week THEN 1 ELSE 0 END) as completions_week,
                    SUM(CASE WHEN timecompleted >= :month THEN 1 ELSE 0 END) as completions_month,
                    COUNT(*) as total_completions
                FROM {course_completions}
                WHERE userid $usersql
                  AND timecompleted IS NOT NULL";

        $params = array_merge([
            'today' => $todaystart,
            'week' => $weekstart,
            'month' => $monthstart,
        ], $userparams);

        $result = $DB->get_record_sql($sql, $params);

        return [
            'completions_today' => (int) ($result->completions_today ?? 0),
            'completions_week' => (int) ($result->completions_week ?? 0),
            'completions_month' => (int) ($result->completions_month ?? 0),
            'total_completions' => (int) ($result->total_completions ?? 0),
        ];
    }

    /**
     * Get dashboard access statistics (cached).
     *
     * @return array Dashboard access stats
     */
    public function get_dashboard_access(): array {
        return $this->get_cached_data('dashboard_access', function() {
            return $this->compute_dashboard_access();
        });
    }

    /**
     * Compute dashboard access statistics.
     *
     * @return array
     */
    protected function compute_dashboard_access(): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return ['today' => 0, 'week' => 0, 'month' => 0];
        }

        $todaystart = strtotime('today midnight');
        $weekstart = strtotime('-7 days midnight');
        $monthstart = strtotime('-30 days midnight');

        $userids = $this->get_company_userids();
        if (empty($userids)) {
            return ['today' => 0, 'week' => 0, 'month' => 0];
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        $sql = "SELECT
                    COUNT(DISTINCT CASE WHEN timecreated >= :today THEN userid END) as today,
                    COUNT(DISTINCT CASE WHEN timecreated >= :week THEN userid END) as week,
                    COUNT(DISTINCT CASE WHEN timecreated >= :month THEN userid END) as month
                FROM {logstore_standard_log}
                WHERE eventname = :event
                  AND userid $usersql";

        $params = array_merge([
            'event' => '\\core\\event\\dashboard_viewed',
            'today' => $todaystart,
            'week' => $weekstart,
            'month' => $monthstart,
        ], $userparams);

        $result = $DB->get_record_sql($sql, $params);

        return [
            'today' => (int) ($result->today ?? 0),
            'week' => (int) ($result->week ?? 0),
            'month' => (int) ($result->month ?? 0),
        ];
    }

    /**
     * Get completion trends over time (cached).
     *
     * @param int $days Number of days
     * @return array [labels, data]
     */
    public function get_completion_trends(int $days = 30): array {
        return $this->get_cached_data("completion_trends_{$days}", function() use ($days) {
            return $this->compute_completion_trends($days);
        });
    }

    /**
     * Compute completion trends.
     *
     * @param int $days
     * @return array
     */
    protected function compute_completion_trends(int $days): array {
        global $DB;

        $starttime = strtotime("-{$days} days midnight");
        $userids = $this->get_company_userids();

        if (empty($userids)) {
            return ['labels' => [], 'data' => []];
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        $sql = "SELECT DATE(FROM_UNIXTIME(timecompleted)) as completion_date,
                       COUNT(*) as completion_count
                FROM {course_completions}
                WHERE userid $usersql
                  AND timecompleted >= :starttime
                  AND timecompleted IS NOT NULL
                GROUP BY DATE(FROM_UNIXTIME(timecompleted))
                ORDER BY completion_date ASC";

        $params = array_merge(['starttime' => $starttime], $userparams);
        $records = $DB->get_records_sql($sql, $params);

        $labels = [];
        $data = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($date));
            $data[$date] = 0;
        }

        foreach ($records as $record) {
            if (isset($data[$record->completion_date])) {
                $data[$record->completion_date] = (int) $record->completion_count;
            }
        }

        return [
            'labels' => $labels,
            'data' => array_values($data),
        ];
    }

    /**
     * Get logout statistics (cached).
     *
     * @return array Logout statistics
     */
    public function get_logout_summary(): array {
        return $this->get_cached_data('logout_summary', function() {
            return $this->compute_logout_summary();
        });
    }

    /**
     * Compute logout statistics.
     *
     * @return array
     */
    protected function compute_logout_summary(): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            return ['logouts_today' => 0, 'logouts_week' => 0, 'logouts_month' => 0];
        }

        $todaystart = strtotime('today midnight');
        $weekstart = strtotime('-7 days midnight');
        $monthstart = strtotime('-30 days midnight');

        $userids = $this->get_company_userids();
        if (empty($userids)) {
            return ['logouts_today' => 0, 'logouts_week' => 0, 'logouts_month' => 0];
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        $sql = "SELECT
                    SUM(CASE WHEN timecreated >= :today THEN 1 ELSE 0 END) as logouts_today,
                    SUM(CASE WHEN timecreated >= :week THEN 1 ELSE 0 END) as logouts_week,
                    SUM(CASE WHEN timecreated >= :month THEN 1 ELSE 0 END) as logouts_month
                FROM {logstore_standard_log}
                WHERE eventname = :event
                  AND userid $usersql";

        $params = array_merge([
            'event' => '\\core\\event\\user_loggedout',
            'today' => $todaystart,
            'week' => $weekstart,
            'month' => $monthstart,
        ], $userparams);

        $result = $DB->get_record_sql($sql, $params);

        return [
            'logouts_today' => (int) ($result->logouts_today ?? 0),
            'logouts_week' => (int) ($result->logouts_week ?? 0),
            'logouts_month' => (int) ($result->logouts_month ?? 0),
        ];
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

    /**
     * Get company name.
     *
     * @return string Company name or 'All Companies'
     */
    public function get_company_name(): string {
        global $DB;

        if ($this->companyid == 0) {
            return get_string('allcompanies', 'report_platform_usage');
        }

        $company = $DB->get_record('company', ['id' => $this->companyid], 'name');
        return $company ? $company->name : '';
    }

    /**
     * Get date range as formatted string.
     *
     * @return string Date range
     */
    public function get_date_range(): string {
        return userdate($this->datefrom, '%d/%m/%Y') . ' - ' . userdate($this->dateto, '%d/%m/%Y');
    }
}
