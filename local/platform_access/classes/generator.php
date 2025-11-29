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
 * Access records generator class - Optimized with bulk inserts.
 *
 * @package   local_platform_access
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_platform_access;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to generate access records for users with bulk insert optimization.
 */
class generator {

    /** @var int Batch size for bulk inserts */
    const BATCH_SIZE = 500;

    /** @var int Date from timestamp */
    protected $datefrom;

    /** @var int Date to timestamp */
    protected $dateto;

    /** @var int Minimum logins per user */
    protected $loginsmin;

    /** @var int Maximum logins per user */
    protected $loginsmax;

    /** @var int Minimum course accesses per user */
    protected $courseaccessmin;

    /** @var int Maximum course accesses per user */
    protected $courseaccessmax;

    /** @var int Minimum activity accesses per course */
    protected $activityaccessmin;

    /** @var int Maximum activity accesses per course */
    protected $activityaccessmax;

    /** @var bool Whether to randomize timestamps */
    protected $randomize;

    /** @var bool Include admin users */
    protected $includeadmins;

    /** @var bool Only active users */
    protected $onlyactive;

    /** @var string Access type: 'login', 'course', 'activity', 'all' */
    protected $accesstype;

    /** @var int Company ID (0 = all companies) */
    protected $companyid;

    /** @var bool Update user creation date */
    protected $updateusercreated;

    /** @var int User creation date timestamp */
    protected $usercreateddate;

    /** @var bool Clean existing records before generating */
    protected $cleanbeforegenerate;

    /** @var bool Generate dashboard access records */
    protected $generatedashboard;

    /** @var bool Generate logout records */
    protected $generatelogouts;

    /** @var bool Generate course completion records */
    protected $generatecompletions;

    /** @var int Minimum completion percentage */
    protected $completionpercentmin;

    /** @var int Maximum completion percentage */
    protected $completionpercentmax;

    /** @var array Statistics */
    protected $stats = [
        'users_processed' => 0,
        'users_updated' => 0,
        'users_without_enrollments' => 0,
        'logins_generated' => 0,
        'course_access_generated' => 0,
        'activity_access_generated' => 0,
        'dashboard_access_generated' => 0,
        'logouts_generated' => 0,
        'completions_generated' => 0,
        'lastaccess_updated' => 0,
        'records_deleted' => 0,
    ];

    /** @var array Buffer for log records */
    protected $logbuffer = [];

    /** @var array Buffer for user_lastaccess records */
    protected $lastaccessbuffer = [];

    /** @var array Buffer for course_completions records */
    protected $completionbuffer = [];

    /** @var array Buffer for course_modules_completion records */
    protected $cmcompletionbuffer = [];

    /** @var array Buffer for course_modules_viewed records */
    protected $cmviewedbuffer = [];

    /** @var array Cache for user enrollments */
    protected $userenrollmentscache = [];

    /** @var array Cache for course contexts */
    protected $coursecontextscache = [];

    /** @var array Cache for module contexts */
    protected $modulecontextscache = [];

    /** @var array Cache for user contexts */
    protected $usercontextscache = [];

    /** @var bool Logstore exists flag */
    protected $logstoreexists = null;

    /** @var bool course_modules_viewed table exists */
    protected $cmviewedexists = null;

    /** @var bool local_report_user_logins table exists */
    protected $reportloginsexists = null;

    /**
     * Constructor.
     *
     * @param array $options Options for generation
     */
    public function __construct(array $options = []) {
        $this->datefrom = $options['datefrom'] ?? strtotime('2025-11-15');
        $this->dateto = $options['dateto'] ?? time();
        $this->loginsmin = max(1, intval($options['loginsmin'] ?? 1));
        $this->loginsmax = max($this->loginsmin, intval($options['loginsmax'] ?? 5));
        $this->courseaccessmin = max(1, intval($options['courseaccessmin'] ?? 1));
        $this->courseaccessmax = max($this->courseaccessmin, intval($options['courseaccessmax'] ?? 3));
        $this->activityaccessmin = max(1, intval($options['activityaccessmin'] ?? 1));
        $this->activityaccessmax = max($this->activityaccessmin, intval($options['activityaccessmax'] ?? 2));
        $this->randomize = $options['randomize'] ?? true;
        $this->includeadmins = $options['includeadmins'] ?? false;
        $this->onlyactive = $options['onlyactive'] ?? true;
        $this->accesstype = $options['accesstype'] ?? 'all';
        $this->companyid = intval($options['companyid'] ?? 0);
        $this->updateusercreated = $options['updateusercreated'] ?? true;
        $this->usercreateddate = $options['usercreateddate'] ?? strtotime('2025-11-15');
        $this->cleanbeforegenerate = $options['cleanbeforegenerate'] ?? true;
        $this->generatedashboard = $options['generatedashboard'] ?? true;
        $this->generatelogouts = $options['generatelogouts'] ?? false;
        $this->generatecompletions = $options['generatecompletions'] ?? false;
        $this->completionpercentmin = max(0, min(100, intval($options['completionpercentmin'] ?? 50)));
        $this->completionpercentmax = max($this->completionpercentmin, min(100, intval($options['completionpercentmax'] ?? 100)));
    }

    /**
     * Get a random number between min and max.
     */
    protected function get_random_count(int $min, int $max): int {
        return ($min >= $max) ? $min : rand($min, $max);
    }

    /**
     * Generate a random timestamp.
     */
    protected function get_random_timestamp(): int {
        return $this->randomize ? rand($this->datefrom, $this->dateto) : $this->dateto;
    }

    /**
     * Generate a random IP address.
     */
    protected function get_random_ip(): string {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }

    /**
     * Check if logstore table exists (cached).
     */
    protected function logstore_exists(): bool {
        global $DB;
        if ($this->logstoreexists === null) {
            $this->logstoreexists = $DB->get_manager()->table_exists('logstore_standard_log');
        }
        return $this->logstoreexists;
    }

    /**
     * Check if course_modules_viewed table exists (cached).
     */
    protected function cmviewed_exists(): bool {
        global $DB;
        if ($this->cmviewedexists === null) {
            $this->cmviewedexists = $DB->get_manager()->table_exists('course_modules_viewed');
        }
        return $this->cmviewedexists;
    }

    /**
     * Check if local_report_user_logins table exists (cached).
     */
    protected function report_logins_exists(): bool {
        global $DB;
        if ($this->reportloginsexists === null) {
            $this->reportloginsexists = $DB->get_manager()->table_exists('local_report_user_logins');
        }
        return $this->reportloginsexists;
    }

    /**
     * Flush log buffer to database.
     */
    protected function flush_log_buffer(): void {
        global $DB;
        if (!empty($this->logbuffer) && $this->logstore_exists()) {
            $DB->insert_records('logstore_standard_log', $this->logbuffer);
            $this->logbuffer = [];
        }
    }

    /**
     * Add record to log buffer and flush if full.
     */
    protected function add_to_log_buffer(array $record): void {
        $this->logbuffer[] = (object)$record;
        if (count($this->logbuffer) >= self::BATCH_SIZE) {
            $this->flush_log_buffer();
        }
    }

    /**
     * Get course context (cached).
     */
    protected function get_course_context(int $courseid): ?\context_course {
        if (!isset($this->coursecontextscache[$courseid])) {
            $this->coursecontextscache[$courseid] = \context_course::instance($courseid, IGNORE_MISSING);
        }
        return $this->coursecontextscache[$courseid];
    }

    /**
     * Get module context (cached).
     */
    protected function get_module_context(int $cmid): ?\context_module {
        if (!isset($this->modulecontextscache[$cmid])) {
            $this->modulecontextscache[$cmid] = \context_module::instance($cmid, IGNORE_MISSING);
        }
        return $this->modulecontextscache[$cmid];
    }

    /**
     * Get user context (cached).
     */
    protected function get_user_context(int $userid): ?\context_user {
        if (!isset($this->usercontextscache[$userid])) {
            $this->usercontextscache[$userid] = \context_user::instance($userid, IGNORE_MISSING);
        }
        return $this->usercontextscache[$userid];
    }

    /**
     * Pre-load all user enrollments for the company.
     */
    protected function preload_user_enrollments(array $userids): void {
        global $DB;

        if (empty($userids)) {
            return;
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        $companycourseexists = $DB->get_manager()->table_exists('company_course');
        $hascompanycourses = $companycourseexists && $DB->count_records('company_course') > 0;

        if ($this->companyid > 0 && $hascompanycourses) {
            $sql = "SELECT DISTINCT ue.userid, c.id as courseid, c.fullname, c.shortname
                    FROM {course} c
                    JOIN {enrol} e ON e.courseid = c.id
                    JOIN {user_enrolments} ue ON ue.enrolid = e.id
                    JOIN {company_course} cc ON cc.courseid = c.id
                    WHERE ue.userid $usersql
                    AND cc.companyid = :companyid
                    AND c.id > 1 AND c.visible = 1 AND ue.status = 0
                    ORDER BY ue.userid, c.id";
            $params = array_merge($userparams, ['companyid' => $this->companyid]);
        } else {
            $sql = "SELECT DISTINCT ue.userid, c.id as courseid, c.fullname, c.shortname
                    FROM {course} c
                    JOIN {enrol} e ON e.courseid = c.id
                    JOIN {user_enrolments} ue ON ue.enrolid = e.id
                    WHERE ue.userid $usersql
                    AND c.id > 1 AND c.visible = 1 AND ue.status = 0
                    ORDER BY ue.userid, c.id";
            $params = $userparams;
        }

        $records = $DB->get_recordset_sql($sql, $params);
        foreach ($records as $record) {
            if (!isset($this->userenrollmentscache[$record->userid])) {
                $this->userenrollmentscache[$record->userid] = [];
            }
            $this->userenrollmentscache[$record->userid][$record->courseid] = (object)[
                'id' => $record->courseid,
                'fullname' => $record->fullname,
                'shortname' => $record->shortname,
            ];
        }
        $records->close();

        // Initialize empty arrays for users without enrollments.
        foreach ($userids as $userid) {
            if (!isset($this->userenrollmentscache[$userid])) {
                $this->userenrollmentscache[$userid] = [];
            }
        }
    }

    /**
     * Get user courses from cache.
     */
    protected function get_user_courses_cached(int $userid): array {
        return $this->userenrollmentscache[$userid] ?? [];
    }

    /**
     * Clean existing access records using TRUNCATE for maximum speed.
     */
    public function clean_existing_records(array $users): int {
        global $DB, $CFG;

        if (empty($users)) {
            return 0;
        }

        $deleted = 0;
        $userids = array_keys($users);
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        // Get table prefix.
        $prefix = $CFG->prefix;

        // Truncate logstore_standard_log (fastest method).
        if ($this->logstore_exists()) {
            $count = $DB->count_records('logstore_standard_log');
            if ($count > 0) {
                $deleted += $count;
                $DB->execute("TRUNCATE TABLE {$prefix}logstore_standard_log");
            }
        }

        // Truncate user_lastaccess.
        $count = $DB->count_records('user_lastaccess');
        if ($count > 0) {
            $deleted += $count;
            $DB->execute("TRUNCATE TABLE {$prefix}user_lastaccess");
        }

        // Truncate course_modules_completion.
        $count = $DB->count_records('course_modules_completion');
        if ($count > 0) {
            $deleted += $count;
            $DB->execute("TRUNCATE TABLE {$prefix}course_modules_completion");
        }

        // Truncate course_completions.
        $count = $DB->count_records('course_completions');
        if ($count > 0) {
            $deleted += $count;
            $DB->execute("TRUNCATE TABLE {$prefix}course_completions");
        }

        // Truncate local_report_user_logins if exists.
        if ($this->report_logins_exists()) {
            $count = $DB->count_records('local_report_user_logins');
            if ($count > 0) {
                $deleted += $count;
                $DB->execute("TRUNCATE TABLE {$prefix}local_report_user_logins");
            }
        }

        // Truncate course_modules_viewed if exists.
        if ($this->cmviewed_exists()) {
            $count = $DB->count_records('course_modules_viewed');
            if ($count > 0) {
                $deleted += $count;
                $DB->execute("TRUNCATE TABLE {$prefix}course_modules_viewed");
            }
        }

        // Bulk reset user access fields for selected users.
        $DB->execute("UPDATE {user} SET firstaccess = 0, lastaccess = 0, lastlogin = 0, currentlogin = 0, lastip = '' WHERE id $usersql", $userparams);

        $this->stats['records_deleted'] = $deleted;
        return $deleted;
    }

    /**
     * Get all companies.
     */
    public static function get_companies(): array {
        global $DB;
        return $DB->get_records('company', ['suspended' => 0], 'name ASC', 'id, name, shortname');
    }

    /**
     * Get users to process.
     */
    public function get_users(): array {
        global $DB;

        $params = [];
        $companyusersexists = $DB->get_manager()->table_exists('company_users');
        $hascompanyusers = $companyusersexists && $DB->count_records('company_users') > 0;

        if ($this->companyid > 0 && $hascompanyusers) {
            $sql = "SELECT DISTINCT u.* FROM {user} u
                    JOIN {company_users} cu ON cu.userid = u.id
                    WHERE cu.companyid = :companyid";
            $params['companyid'] = $this->companyid;
            if ($this->onlyactive) {
                $sql .= " AND cu.suspended = 0";
            }
        } else if ($hascompanyusers) {
            $sql = "SELECT DISTINCT u.* FROM {user} u
                    JOIN {company_users} cu ON cu.userid = u.id WHERE 1=1";
            if ($this->onlyactive) {
                $sql .= " AND cu.suspended = 0";
            }
        } else {
            $sql = "SELECT u.* FROM {user} u WHERE 1=1";
        }

        if ($this->onlyactive) {
            $sql .= " AND u.deleted = 0 AND u.suspended = 0";
        }
        $sql .= " AND u.id > 1 AND u.username <> 'guest'";

        if (!$this->includeadmins) {
            $adminids = array_keys(get_admins());
            if (!empty($adminids)) {
                list($insql, $inparams) = $DB->get_in_or_equal($adminids, SQL_PARAMS_NAMED, 'admin', false);
                $sql .= " AND u.id $insql";
                $params = array_merge($params, $inparams);
            }
        }

        $sql .= " ORDER BY u.id ASC";
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get course modules for a course.
     */
    public function get_course_modules(int $courseid): array {
        global $DB;
        return $DB->get_records_sql(
            "SELECT cm.id, cm.course, cm.module, cm.instance, m.name as modname
             FROM {course_modules} cm
             JOIN {modules} m ON m.id = cm.module
             WHERE cm.course = :courseid AND cm.visible = 1 AND cm.deletioninprogress = 0
             ORDER BY cm.id ASC",
            ['courseid' => $courseid]
        );
    }

    /**
     * Build a log entry directly (without creating Moodle event object).
     */
    protected function build_log_entry(string $eventname, string $component, string $action,
        string $target, int $contextid, int $contextlevel, int $contextinstanceid,
        int $userid, int $courseid, int $timestamp, string $ip,
        ?int $objectid = null, ?string $objecttable = null, ?int $relateduserid = null,
        $other = null): array {

        return [
            'eventname' => $eventname,
            'component' => $component,
            'action' => $action,
            'target' => $target,
            'objecttable' => $objecttable,
            'objectid' => $objectid,
            'crud' => 'r',
            'edulevel' => 0,
            'contextid' => $contextid,
            'contextlevel' => $contextlevel,
            'contextinstanceid' => $contextinstanceid,
            'userid' => $userid,
            'courseid' => $courseid,
            'relateduserid' => $relateduserid,
            'anonymous' => 0,
            'other' => $other !== null ? serialize($other) : 'N;',
            'timecreated' => $timestamp,
            'origin' => 'web',
            'ip' => $ip,
            'realuserid' => null,
        ];
    }

    /**
     * Generate login record (buffered).
     */
    protected function buffer_login_record($user, int $timestamp, string $ip): void {
        $context = \context_system::instance();

        $entry = $this->build_log_entry(
            '\\core\\event\\user_loggedin', 'core', 'loggedin', 'user',
            $context->id, CONTEXT_SYSTEM, 0,
            $user->id, 0, $timestamp, $ip,
            $user->id, 'user', null,
            ['username' => $user->username]
        );

        $this->add_to_log_buffer($entry);
        $this->stats['logins_generated']++;
    }

    /**
     * Generate dashboard access record (buffered).
     */
    protected function buffer_dashboard_record($user, int $timestamp, string $ip): void {
        $context = $this->get_user_context($user->id);
        if (!$context) {
            return;
        }

        $entry = $this->build_log_entry(
            '\\core\\event\\dashboard_viewed', 'core', 'viewed', 'dashboard',
            $context->id, CONTEXT_USER, $user->id,
            $user->id, 0, $timestamp, $ip
        );

        $this->add_to_log_buffer($entry);
        $this->stats['dashboard_access_generated']++;
    }

    /**
     * Generate logout record (buffered).
     */
    protected function buffer_logout_record($user, int $timestamp, string $ip): void {
        $context = \context_system::instance();

        $entry = $this->build_log_entry(
            '\\core\\event\\user_loggedout', 'core', 'loggedout', 'user',
            $context->id, CONTEXT_SYSTEM, 0,
            $user->id, 0, $timestamp, $ip,
            $user->id, 'user', null,
            ['sessionid' => md5(uniqid(rand(), true))]
        );

        $this->add_to_log_buffer($entry);
        $this->stats['logouts_generated']++;
    }

    /**
     * Generate course access record (buffered).
     */
    protected function buffer_course_access_record($user, $course, int $timestamp, string $ip): void {
        $context = $this->get_course_context($course->id);
        if (!$context) {
            return;
        }

        $entry = $this->build_log_entry(
            '\\core\\event\\course_viewed', 'core', 'viewed', 'course',
            $context->id, CONTEXT_COURSE, $course->id,
            $user->id, $course->id, $timestamp, $ip
        );

        $this->add_to_log_buffer($entry);
        $this->stats['course_access_generated']++;

        // Buffer lastaccess update.
        $key = $user->id . '_' . $course->id;
        if (!isset($this->lastaccessbuffer[$key]) || $timestamp > $this->lastaccessbuffer[$key]['timestamp']) {
            $this->lastaccessbuffer[$key] = [
                'userid' => $user->id,
                'courseid' => $course->id,
                'timestamp' => $timestamp,
            ];
        }
    }

    /**
     * Generate activity access record (buffered).
     */
    protected function buffer_activity_access_record($user, $course, $cm, int $timestamp, string $ip): void {
        $context = $this->get_module_context($cm->id);
        if (!$context) {
            return;
        }

        $eventclass = '\\mod_' . $cm->modname . '\\event\\course_module_viewed';

        $entry = $this->build_log_entry(
            $eventclass, 'mod_' . $cm->modname, 'viewed', 'course_module',
            $context->id, CONTEXT_MODULE, $cm->id,
            $user->id, $course->id, $timestamp, $ip,
            $cm->instance, $cm->modname
        );

        $this->add_to_log_buffer($entry);
        $this->stats['activity_access_generated']++;

        // Buffer completion record.
        $key = $user->id . '_' . $cm->id;
        if (!isset($this->cmcompletionbuffer[$key])) {
            $this->cmcompletionbuffer[$key] = [
                'coursemoduleid' => $cm->id,
                'userid' => $user->id,
                'completionstate' => 0,
                'viewed' => 1,
                'overrideby' => null,
                'timemodified' => $timestamp,
            ];
        }

        // Buffer viewed record.
        if ($this->cmviewed_exists() && !isset($this->cmviewedbuffer[$key])) {
            $this->cmviewedbuffer[$key] = [
                'coursemoduleid' => $cm->id,
                'userid' => $user->id,
                'timecreated' => $timestamp,
            ];
        }
    }

    /**
     * Generate course completion record (buffered).
     */
    protected function buffer_course_completion($user, $course, int $timestamp, string $ip): void {
        $context = $this->get_course_context($course->id);
        if (!$context) {
            return;
        }

        $key = $user->id . '_' . $course->id;
        $this->completionbuffer[$key] = [
            'userid' => $user->id,
            'course' => $course->id,
            'timeenrolled' => $timestamp - 86400 * 7,
            'timestarted' => $timestamp - 86400,
            'timecompleted' => $timestamp,
            'reaggregate' => 0,
        ];

        // Log entry will be created in flush_completion_buffer.
        $this->stats['completions_generated']++;
    }

    /**
     * Flush user lastaccess buffer.
     */
    protected function flush_lastaccess_buffer(): void {
        global $DB;

        if (empty($this->lastaccessbuffer)) {
            return;
        }

        // Get existing records.
        $keys = [];
        foreach ($this->lastaccessbuffer as $data) {
            $keys[] = ['userid' => $data['userid'], 'courseid' => $data['courseid']];
        }

        // Insert or update.
        foreach ($this->lastaccessbuffer as $key => $data) {
            $existing = $DB->get_record('user_lastaccess', [
                'userid' => $data['userid'],
                'courseid' => $data['courseid'],
            ]);

            if ($existing) {
                if ($data['timestamp'] > $existing->timeaccess) {
                    $DB->set_field('user_lastaccess', 'timeaccess', $data['timestamp'], ['id' => $existing->id]);
                }
            } else {
                $DB->insert_record('user_lastaccess', (object)[
                    'userid' => $data['userid'],
                    'courseid' => $data['courseid'],
                    'timeaccess' => $data['timestamp'],
                ]);
            }
        }

        $this->lastaccessbuffer = [];
    }

    /**
     * Flush completion buffers.
     */
    protected function flush_completion_buffers(): void {
        global $DB;

        // Flush course_modules_completion.
        if (!empty($this->cmcompletionbuffer)) {
            $records = [];
            foreach ($this->cmcompletionbuffer as $data) {
                $records[] = (object)$data;
            }
            $DB->insert_records('course_modules_completion', $records);
            $this->cmcompletionbuffer = [];
        }

        // Flush course_modules_viewed.
        if (!empty($this->cmviewedbuffer) && $this->cmviewed_exists()) {
            $records = [];
            foreach ($this->cmviewedbuffer as $data) {
                $records[] = (object)$data;
            }
            $DB->insert_records('course_modules_viewed', $records);
            $this->cmviewedbuffer = [];
        }

        // Flush course_completions and create log entries.
        if (!empty($this->completionbuffer)) {
            foreach ($this->completionbuffer as $data) {
                $id = $DB->insert_record('course_completions', (object)$data);

                // Create log entry for completion.
                $context = $this->get_course_context($data['course']);
                if ($context) {
                    $ip = $this->get_random_ip();
                    $entry = $this->build_log_entry(
                        '\\core\\event\\course_completed', 'core', 'completed', 'course',
                        $context->id, CONTEXT_COURSE, $data['course'],
                        $data['userid'], $data['course'], $data['timecompleted'], $ip,
                        $id, 'course_completions', $data['userid'],
                        ['relateduserid' => $data['userid']]
                    );
                    $this->add_to_log_buffer($entry);
                }
            }
            $this->completionbuffer = [];
        }
    }

    /**
     * Update user access info in bulk.
     */
    protected function update_users_access_info(array $users, array $logindata): void {
        global $DB;

        foreach ($logindata as $userid => $data) {
            if (!isset($users[$userid])) {
                continue;
            }

            // Skip if no timestamps recorded.
            if (empty($data['timestamps'])) {
                continue;
            }

            $user = $users[$userid];
            $update = new \stdClass();
            $update->id = $userid;

            // Determine first and last timestamps.
            $mintimestamp = min($data['timestamps']);
            $maxtimestamp = max($data['timestamps']);
            $lastip = $data['lastip'] ?: $this->get_random_ip();

            if (empty($user->firstaccess) || $mintimestamp < $user->firstaccess) {
                $update->firstaccess = $mintimestamp;
            }
            if ($maxtimestamp > $user->lastaccess) {
                $update->lastaccess = $maxtimestamp;
                $this->stats['lastaccess_updated']++;
            }

            $update->lastlogin = $user->currentlogin ?: $maxtimestamp;
            $update->currentlogin = $maxtimestamp;
            $update->lastip = $lastip;

            $DB->update_record('user', $update);
        }
    }

    /**
     * Update local_report_user_logins in bulk.
     */
    protected function update_report_user_logins_bulk(array $users, array $logindata): void {
        global $DB;

        if (!$this->report_logins_exists()) {
            return;
        }

        foreach ($logindata as $userid => $data) {
            // Skip if no timestamps recorded.
            if (empty($data['timestamps'])) {
                continue;
            }

            $mintimestamp = min($data['timestamps']);
            $maxtimestamp = max($data['timestamps']);
            $count = count($data['timestamps']);

            $existing = $DB->get_record('local_report_user_logins', ['userid' => $userid]);

            if ($existing) {
                $update = new \stdClass();
                $update->id = $existing->id;
                $update->logincount = $existing->logincount + $count;
                $update->modifiedtime = time();

                if (empty($existing->firstlogin) || $mintimestamp < $existing->firstlogin) {
                    $update->firstlogin = $mintimestamp;
                }
                if (empty($existing->lastlogin) || $maxtimestamp > $existing->lastlogin) {
                    $update->lastlogin = $maxtimestamp;
                }

                $DB->update_record('local_report_user_logins', $update);
            } else {
                $user = $users[$userid] ?? null;
                $created = $this->updateusercreated ? $this->usercreateddate : ($user ? $user->timecreated : time());

                $DB->insert_record('local_report_user_logins', (object)[
                    'userid' => $userid,
                    'created' => $created,
                    'firstlogin' => $mintimestamp,
                    'lastlogin' => $maxtimestamp,
                    'logincount' => $count,
                    'modifiedtime' => time(),
                ]);
            }
        }
    }

    /**
     * Update user creation dates in bulk.
     */
    protected function update_user_created_dates_bulk(array $users): void {
        global $DB;

        if (!$this->updateusercreated) {
            return;
        }

        $userids = array_keys($users);
        if (empty($userids)) {
            return;
        }

        list($usersql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');
        $params['timecreated'] = $this->usercreateddate;

        $DB->execute("UPDATE {user} SET timecreated = :timecreated WHERE id $usersql", $params);
        $this->stats['users_updated'] = count($userids);

        // Update local_report_user_logins if exists.
        if ($this->report_logins_exists()) {
            $DB->execute("UPDATE {local_report_user_logins} SET created = :timecreated WHERE userid $usersql", $params);
        }
    }

    /**
     * Run the generation process with optimizations.
     */
    public function run(?callable $progresscallback = null): array {
        $starttime = time();

        $users = $this->get_users();
        if (empty($users)) {
            return $this->stats;
        }

        $userids = array_keys($users);

        // Clean existing records.
        if ($this->cleanbeforegenerate) {
            $this->clean_existing_records($users);
        }

        // Pre-load user enrollments.
        $this->preload_user_enrollments($userids);

        // Pre-load course modules.
        $coursemodules = [];
        if ($this->accesstype === 'activity' || $this->accesstype === 'all') {
            $allcourseids = [];
            foreach ($this->userenrollmentscache as $courses) {
                foreach ($courses as $course) {
                    $allcourseids[$course->id] = true;
                }
            }
            foreach (array_keys($allcourseids) as $courseid) {
                $coursemodules[$courseid] = $this->get_course_modules($courseid);
            }
        }

        // Update user creation dates in bulk.
        $this->update_user_created_dates_bulk($users);

        // Track login data for bulk user update.
        $logindata = [];

        // Process users.
        foreach ($users as $user) {
            $this->stats['users_processed']++;

            if ($progresscallback) {
                $progresscallback($user, $this->stats);
            }

            // Generate logins.
            if ($this->accesstype === 'login' || $this->accesstype === 'all') {
                $numlogins = $this->get_random_count($this->loginsmin, $this->loginsmax);
                $logindata[$user->id] = ['timestamps' => [], 'lastip' => ''];

                for ($i = 0; $i < $numlogins; $i++) {
                    $timestamp = $this->get_random_timestamp();
                    $ip = $this->get_random_ip();

                    $this->buffer_login_record($user, $timestamp, $ip);
                    $logindata[$user->id]['timestamps'][] = $timestamp;
                    $logindata[$user->id]['lastip'] = $ip;

                    if ($this->generatedashboard && rand(1, 100) <= 70) {
                        $this->buffer_dashboard_record($user, $timestamp + rand(5, 60), $ip);
                    }

                    if ($this->generatelogouts && rand(1, 100) <= 50) {
                        $this->buffer_logout_record($user, $timestamp + rand(300, 7200), $ip);
                    }
                }
            }

            // Get user courses.
            $usercourses = $this->get_user_courses_cached($user->id);

            if (!empty($usercourses)) {
                // Generate course access.
                if ($this->accesstype === 'course' || $this->accesstype === 'all') {
                    foreach ($usercourses as $course) {
                        $numaccess = $this->get_random_count($this->courseaccessmin, $this->courseaccessmax);
                        for ($i = 0; $i < $numaccess; $i++) {
                            $this->buffer_course_access_record($user, $course, $this->get_random_timestamp(), $this->get_random_ip());
                        }
                    }
                }

                // Generate activity access.
                if ($this->accesstype === 'activity' || $this->accesstype === 'all') {
                    foreach ($usercourses as $course) {
                        $modules = $coursemodules[$course->id] ?? [];
                        foreach ($modules as $cm) {
                            $numaccess = $this->get_random_count($this->activityaccessmin, $this->activityaccessmax);
                            for ($i = 0; $i < $numaccess; $i++) {
                                $this->buffer_activity_access_record($user, $course, $cm, $this->get_random_timestamp(), $this->get_random_ip());
                            }
                        }
                    }
                }

                // Generate completions.
                if ($this->generatecompletions) {
                    $completionpercent = $this->get_random_count($this->completionpercentmin, $this->completionpercentmax);
                    foreach ($usercourses as $course) {
                        if (rand(1, 100) <= $completionpercent) {
                            $this->buffer_course_completion($user, $course, $this->get_random_timestamp(), $this->get_random_ip());
                        }
                    }
                }
            } else {
                $this->stats['users_without_enrollments']++;
            }
        }

        // Flush all buffers.
        $this->flush_log_buffer();
        $this->flush_lastaccess_buffer();
        $this->flush_completion_buffers();

        // Update user access info in bulk.
        if (!empty($logindata)) {
            $this->update_users_access_info($users, $logindata);
            $this->update_report_user_logins_bulk($users, $logindata);
        }

        $this->stats['time_elapsed'] = time() - $starttime;

        return $this->stats;
    }

    /**
     * Get statistics.
     */
    public function get_stats(): array {
        return $this->stats;
    }
}
