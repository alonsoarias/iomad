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
 * Access records generator class.
 *
 * @package   local_platform_access
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_platform_access;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to generate access records for users.
 */
class generator {

    /** @var int Date from timestamp */
    protected $datefrom;

    /** @var int Date to timestamp */
    protected $dateto;

    /** @var int Number of logins per user */
    protected $loginsperuser;

    /** @var int Number of course accesses per user */
    protected $courseaccessperuser;

    /** @var int Number of activity accesses per course */
    protected $activityaccesspercourse;

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

    /** @var array Statistics */
    protected $stats = [
        'users_processed' => 0,
        'logins_generated' => 0,
        'course_access_generated' => 0,
        'activity_access_generated' => 0,
        'lastaccess_updated' => 0,
    ];

    /**
     * Constructor.
     *
     * @param array $options Options for generation
     */
    public function __construct(array $options = []) {
        $this->datefrom = $options['datefrom'] ?? strtotime('-30 days');
        $this->dateto = $options['dateto'] ?? time();
        $this->loginsperuser = $options['loginsperuser'] ?? 1;
        $this->courseaccessperuser = $options['courseaccessperuser'] ?? 1;
        $this->activityaccesspercourse = $options['activityaccesspercourse'] ?? 1;
        $this->randomize = $options['randomize'] ?? true;
        $this->includeadmins = $options['includeadmins'] ?? false;
        $this->onlyactive = $options['onlyactive'] ?? true;
        $this->accesstype = $options['accesstype'] ?? 'all';
        $this->companyid = $options['companyid'] ?? 0;
    }

    /**
     * Generate a random timestamp between datefrom and dateto.
     *
     * @return int Timestamp
     */
    protected function get_random_timestamp(): int {
        if ($this->randomize) {
            return rand($this->datefrom, $this->dateto);
        }
        return $this->dateto;
    }

    /**
     * Generate a random IP address.
     *
     * @return string IP address
     */
    protected function get_random_ip(): string {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }

    /**
     * Get all companies.
     *
     * @return array Array of company objects
     */
    public static function get_companies(): array {
        global $DB;

        return $DB->get_records('company', ['suspended' => 0], 'name ASC', 'id, name, shortname');
    }

    /**
     * Get users to process (filtered by company if set).
     *
     * @return array Array of user objects
     */
    public function get_users(): array {
        global $DB;

        $params = [];

        // Base query for users in a company.
        if ($this->companyid > 0) {
            $sql = "SELECT DISTINCT u.*
                    FROM {user} u
                    JOIN {company_users} cu ON cu.userid = u.id
                    WHERE cu.companyid = :companyid";
            $params['companyid'] = $this->companyid;

            // Only active users in company.
            if ($this->onlyactive) {
                $sql .= " AND cu.suspended = 0";
            }
        } else {
            // All users from all companies.
            $sql = "SELECT DISTINCT u.*
                    FROM {user} u
                    JOIN {company_users} cu ON cu.userid = u.id
                    WHERE 1=1";

            if ($this->onlyactive) {
                $sql .= " AND cu.suspended = 0";
            }
        }

        // Only active users (not deleted, not suspended in Moodle).
        if ($this->onlyactive) {
            $sql .= " AND u.deleted = 0 AND u.suspended = 0";
        }

        // Exclude guest user.
        $sql .= " AND u.id > 1 AND u.username <> 'guest'";

        // Exclude admin users if not included.
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
     * Get courses for a company.
     *
     * @return array Array of course objects
     */
    public function get_company_courses(): array {
        global $DB;

        if ($this->companyid > 0) {
            // Get courses associated with the company.
            $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname, c.category
                    FROM {course} c
                    JOIN {company_course} cc ON cc.courseid = c.id
                    WHERE cc.companyid = :companyid
                    AND c.id > 1
                    AND c.visible = 1
                    ORDER BY c.id ASC";

            return $DB->get_records_sql($sql, ['companyid' => $this->companyid]);
        } else {
            // Get all visible courses.
            return $DB->get_records_select(
                'course',
                'id > 1 AND visible = 1',
                [],
                'id ASC',
                'id, fullname, shortname, category'
            );
        }
    }

    /**
     * Get courses where a user is enrolled.
     *
     * @param int $userid User ID
     * @return array Array of course objects
     */
    public function get_user_courses(int $userid): array {
        global $DB;

        $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname
                FROM {course} c
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE ue.userid = :userid
                AND c.id > 1
                AND c.visible = 1
                AND ue.status = 0
                ORDER BY c.id ASC";

        return $DB->get_records_sql($sql, ['userid' => $userid]);
    }

    /**
     * Get course modules (activities) for a course.
     *
     * @param int $courseid Course ID
     * @return array Array of course module objects
     */
    public function get_course_modules(int $courseid): array {
        global $DB;

        $sql = "SELECT cm.id, cm.course, cm.module, cm.instance, m.name as modname
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                WHERE cm.course = :courseid
                AND cm.visible = 1
                AND cm.deletioninprogress = 0
                ORDER BY cm.id ASC";

        return $DB->get_records_sql($sql, ['courseid' => $courseid]);
    }

    /**
     * Get activity instance name.
     *
     * @param object $cm Course module object
     * @return string Activity name
     */
    protected function get_activity_name($cm): string {
        global $DB;

        $table = $cm->modname;
        if ($instance = $DB->get_record($table, ['id' => $cm->instance], 'id, name')) {
            return $instance->name;
        }
        return 'Activity ' . $cm->id;
    }

    /**
     * Generate login record for a user.
     *
     * @param object $user User object
     * @param int $timestamp Timestamp for the login
     * @return bool Success
     */
    public function generate_login_record($user, int $timestamp): bool {
        global $DB;

        $ip = $this->get_random_ip();
        $systemcontext = \context_system::instance();

        // Insert into logstore_standard_log.
        $logrecord = new \stdClass();
        $logrecord->eventname = '\\core\\event\\user_loggedin';
        $logrecord->component = 'core';
        $logrecord->action = 'loggedin';
        $logrecord->target = 'user';
        $logrecord->objecttable = 'user';
        $logrecord->objectid = $user->id;
        $logrecord->crud = 'r';
        $logrecord->edulevel = 0; // LEVEL_OTHER
        $logrecord->contextid = $systemcontext->id;
        $logrecord->contextlevel = CONTEXT_SYSTEM;
        $logrecord->contextinstanceid = 0;
        $logrecord->userid = $user->id;
        $logrecord->courseid = 0;
        $logrecord->relateduserid = null;
        $logrecord->anonymous = 0;
        $logrecord->other = serialize(['username' => $user->username]);
        $logrecord->timecreated = $timestamp;
        $logrecord->origin = 'web';
        $logrecord->ip = $ip;
        $logrecord->realuserid = null;

        try {
            $DB->insert_record('logstore_standard_log', $logrecord);
            $this->stats['logins_generated']++;

            // Update user table.
            $this->update_user_login_info($user->id, $timestamp, $ip);

            // Update local_report_user_logins if it exists.
            $this->update_report_user_logins($user, $timestamp);

            return true;
        } catch (\Exception $e) {
            debugging('Error generating login record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Update user login information.
     *
     * @param int $userid User ID
     * @param int $timestamp Timestamp
     * @param string $ip IP address
     */
    protected function update_user_login_info(int $userid, int $timestamp, string $ip): void {
        global $DB;

        $user = $DB->get_record('user', ['id' => $userid]);
        if (!$user) {
            return;
        }

        $update = new \stdClass();
        $update->id = $userid;

        // Update firstaccess if not set.
        if (empty($user->firstaccess)) {
            $update->firstaccess = $timestamp;
        }

        // Update lastaccess if this timestamp is newer.
        if ($timestamp > $user->lastaccess) {
            $update->lastaccess = $timestamp;
            $this->stats['lastaccess_updated']++;
        }

        // Update login times.
        if ($timestamp > $user->currentlogin) {
            $update->lastlogin = $user->currentlogin;
            $update->currentlogin = $timestamp;
            $update->lastip = $ip;
        }

        $DB->update_record('user', $update);
    }

    /**
     * Update local_report_user_logins table.
     *
     * @param object $user User object
     * @param int $timestamp Timestamp
     */
    protected function update_report_user_logins($user, int $timestamp): void {
        global $DB;

        // Check if table exists.
        if (!$DB->get_manager()->table_exists('local_report_user_logins')) {
            return;
        }

        if ($current = $DB->get_record('local_report_user_logins', ['userid' => $user->id])) {
            // Update existing record.
            $update = new \stdClass();
            $update->id = $current->id;
            $update->logincount = $current->logincount + 1;
            $update->modifiedtime = time();

            if (empty($current->firstlogin) || $timestamp < $current->firstlogin) {
                $update->firstlogin = $timestamp;
            }

            if ($timestamp > $current->lastlogin) {
                $update->lastlogin = $timestamp;
            }

            $DB->update_record('local_report_user_logins', $update);
        } else {
            // Create new record.
            $record = new \stdClass();
            $record->userid = $user->id;
            $record->created = $user->timecreated;
            $record->firstlogin = $timestamp;
            $record->lastlogin = $timestamp;
            $record->logincount = 1;
            $record->modifiedtime = time();

            $DB->insert_record('local_report_user_logins', $record);
        }
    }

    /**
     * Generate course access record for a user.
     *
     * @param object $user User object
     * @param object $course Course object
     * @param int $timestamp Timestamp for the access
     * @return bool Success
     */
    public function generate_course_access_record($user, $course, int $timestamp): bool {
        global $DB;

        $ip = $this->get_random_ip();

        try {
            $coursecontext = \context_course::instance($course->id, IGNORE_MISSING);
            if (!$coursecontext) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        // Insert into logstore_standard_log.
        $logrecord = new \stdClass();
        $logrecord->eventname = '\\core\\event\\course_viewed';
        $logrecord->component = 'core';
        $logrecord->action = 'viewed';
        $logrecord->target = 'course';
        $logrecord->objecttable = null;
        $logrecord->objectid = null;
        $logrecord->crud = 'r';
        $logrecord->edulevel = 2; // LEVEL_PARTICIPATING
        $logrecord->contextid = $coursecontext->id;
        $logrecord->contextlevel = CONTEXT_COURSE;
        $logrecord->contextinstanceid = $course->id;
        $logrecord->userid = $user->id;
        $logrecord->courseid = $course->id;
        $logrecord->relateduserid = null;
        $logrecord->anonymous = 0;
        $logrecord->other = 'N;';
        $logrecord->timecreated = $timestamp;
        $logrecord->origin = 'web';
        $logrecord->ip = $ip;
        $logrecord->realuserid = null;

        try {
            $DB->insert_record('logstore_standard_log', $logrecord);
            $this->stats['course_access_generated']++;

            // Update user_lastaccess table.
            $this->update_user_lastaccess($user->id, $course->id, $timestamp);

            return true;
        } catch (\Exception $e) {
            debugging('Error generating course access record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Generate activity access record for a user.
     *
     * @param object $user User object
     * @param object $course Course object
     * @param object $cm Course module object
     * @param int $timestamp Timestamp for the access
     * @return bool Success
     */
    public function generate_activity_access_record($user, $course, $cm, int $timestamp): bool {
        global $DB;

        $ip = $this->get_random_ip();

        try {
            $cmcontext = \context_module::instance($cm->id, IGNORE_MISSING);
            if (!$cmcontext) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        // Build event name based on module type.
        $eventname = '\\mod_' . $cm->modname . '\\event\\course_module_viewed';

        // Insert into logstore_standard_log.
        $logrecord = new \stdClass();
        $logrecord->eventname = $eventname;
        $logrecord->component = 'mod_' . $cm->modname;
        $logrecord->action = 'viewed';
        $logrecord->target = 'course_module';
        $logrecord->objecttable = $cm->modname;
        $logrecord->objectid = $cm->instance;
        $logrecord->crud = 'r';
        $logrecord->edulevel = 2; // LEVEL_PARTICIPATING
        $logrecord->contextid = $cmcontext->id;
        $logrecord->contextlevel = CONTEXT_MODULE;
        $logrecord->contextinstanceid = $cm->id;
        $logrecord->userid = $user->id;
        $logrecord->courseid = $course->id;
        $logrecord->relateduserid = null;
        $logrecord->anonymous = 0;
        $logrecord->other = 'N;';
        $logrecord->timecreated = $timestamp;
        $logrecord->origin = 'web';
        $logrecord->ip = $ip;
        $logrecord->realuserid = null;

        try {
            $DB->insert_record('logstore_standard_log', $logrecord);
            $this->stats['activity_access_generated']++;

            return true;
        } catch (\Exception $e) {
            debugging('Error generating activity access record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Update user_lastaccess table.
     *
     * @param int $userid User ID
     * @param int $courseid Course ID
     * @param int $timestamp Timestamp
     */
    protected function update_user_lastaccess(int $userid, int $courseid, int $timestamp): void {
        global $DB;

        $existing = $DB->get_record('user_lastaccess', [
            'userid' => $userid,
            'courseid' => $courseid,
        ]);

        if ($existing) {
            // Update only if new timestamp is newer.
            if ($timestamp > $existing->timeaccess) {
                $DB->set_field('user_lastaccess', 'timeaccess', $timestamp, ['id' => $existing->id]);
            }
        } else {
            // Insert new record.
            $record = new \stdClass();
            $record->userid = $userid;
            $record->courseid = $courseid;
            $record->timeaccess = $timestamp;

            $DB->insert_record('user_lastaccess', $record);
        }
    }

    /**
     * Run the generation process.
     *
     * @param callable|null $progresscallback Callback for progress updates
     * @return array Statistics
     */
    public function run(?callable $progresscallback = null): array {
        $starttime = time();

        // Get users from the company.
        $users = $this->get_users();
        if (empty($users)) {
            return $this->stats;
        }

        // Get courses for the company.
        $companycourses = $this->get_company_courses();

        // Pre-fetch course modules for activity access.
        $coursemodules = [];
        if ($this->accesstype === 'activity' || $this->accesstype === 'all') {
            foreach ($companycourses as $course) {
                $coursemodules[$course->id] = $this->get_course_modules($course->id);
            }
        }

        foreach ($users as $user) {
            $this->stats['users_processed']++;

            if ($progresscallback) {
                $progresscallback($user, $this->stats);
            }

            // Generate login records.
            if ($this->accesstype === 'login' || $this->accesstype === 'all') {
                for ($i = 0; $i < $this->loginsperuser; $i++) {
                    $timestamp = $this->get_random_timestamp();
                    $this->generate_login_record($user, $timestamp);
                }
            }

            // Get courses where user is enrolled (or use company courses).
            $usercourses = $this->get_user_courses($user->id);
            if (empty($usercourses)) {
                $usercourses = $companycourses;
            }

            // Generate course access records.
            if ($this->accesstype === 'course' || $this->accesstype === 'all') {
                foreach ($usercourses as $course) {
                    for ($i = 0; $i < $this->courseaccessperuser; $i++) {
                        $timestamp = $this->get_random_timestamp();
                        $this->generate_course_access_record($user, $course, $timestamp);
                    }
                }
            }

            // Generate activity access records.
            if ($this->accesstype === 'activity' || $this->accesstype === 'all') {
                foreach ($usercourses as $course) {
                    if (!isset($coursemodules[$course->id])) {
                        $coursemodules[$course->id] = $this->get_course_modules($course->id);
                    }

                    $modules = $coursemodules[$course->id];
                    foreach ($modules as $cm) {
                        for ($i = 0; $i < $this->activityaccesspercourse; $i++) {
                            $timestamp = $this->get_random_timestamp();
                            $this->generate_activity_access_record($user, $course, $cm, $timestamp);
                        }
                    }
                }
            }
        }

        $this->stats['time_elapsed'] = time() - $starttime;

        return $this->stats;
    }

    /**
     * Get statistics.
     *
     * @return array Statistics
     */
    public function get_stats(): array {
        return $this->stats;
    }
}
