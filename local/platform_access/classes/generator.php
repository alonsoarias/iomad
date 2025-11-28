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

    /** @var bool Whether to randomize timestamps */
    protected $randomize;

    /** @var bool Include admin users */
    protected $includeadmins;

    /** @var bool Only active users */
    protected $onlyactive;

    /** @var string Access type: 'login', 'course', 'both' */
    protected $accesstype;

    /** @var array Statistics */
    protected $stats = [
        'users_processed' => 0,
        'logins_generated' => 0,
        'course_access_generated' => 0,
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
        $this->randomize = $options['randomize'] ?? true;
        $this->includeadmins = $options['includeadmins'] ?? false;
        $this->onlyactive = $options['onlyactive'] ?? true;
        $this->accesstype = $options['accesstype'] ?? 'both';
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
     * Get users to process.
     *
     * @return array Array of user objects
     */
    public function get_users(): array {
        global $DB;

        $conditions = [];
        $params = [];

        // Only active users (not deleted, not suspended).
        if ($this->onlyactive) {
            $conditions[] = 'deleted = 0';
            $conditions[] = 'suspended = 0';
        }

        // Exclude guest user.
        $conditions[] = 'id <> :guestid';
        $params['guestid'] = 1; // Guest user is usually ID 1.

        // Exclude admin users if not included.
        if (!$this->includeadmins) {
            $adminids = get_admins();
            if (!empty($adminids)) {
                $adminidlist = implode(',', array_keys($adminids));
                $conditions[] = "id NOT IN ($adminidlist)";
            }
        }

        // Exclude users with username 'guest'.
        $conditions[] = "username <> 'guest'";

        $where = implode(' AND ', $conditions);

        return $DB->get_records_select('user', $where, $params, 'id ASC');
    }

    /**
     * Get courses to process.
     *
     * @return array Array of course objects
     */
    public function get_courses(): array {
        global $DB;

        // Get all visible courses except site course (id=1).
        return $DB->get_records_select(
            'course',
            'id > 1 AND visible = 1',
            [],
            'id ASC',
            'id, fullname, shortname, category'
        );
    }

    /**
     * Get courses where a user is enrolled.
     *
     * @param int $userid User ID
     * @return array Array of course IDs
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
        $coursecontext = \context_course::instance($course->id);

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

        // Get users.
        $users = $this->get_users();
        if (empty($users)) {
            return $this->stats;
        }

        // Get all courses (for users without specific enrollments).
        $allcourses = $this->get_courses();

        foreach ($users as $user) {
            $this->stats['users_processed']++;

            if ($progresscallback) {
                $progresscallback($user, $this->stats);
            }

            // Generate login records.
            if ($this->accesstype === 'login' || $this->accesstype === 'both') {
                for ($i = 0; $i < $this->loginsperuser; $i++) {
                    $timestamp = $this->get_random_timestamp();
                    $this->generate_login_record($user, $timestamp);
                }
            }

            // Generate course access records.
            if ($this->accesstype === 'course' || $this->accesstype === 'both') {
                // Get courses where user is enrolled.
                $usercourses = $this->get_user_courses($user->id);

                // If user has no enrollments, use all courses.
                $courses = !empty($usercourses) ? $usercourses : $allcourses;

                foreach ($courses as $course) {
                    for ($i = 0; $i < $this->courseaccessperuser; $i++) {
                        $timestamp = $this->get_random_timestamp();
                        $this->generate_course_access_record($user, $course, $timestamp);
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
