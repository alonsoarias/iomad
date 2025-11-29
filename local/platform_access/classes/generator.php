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

    /** @var bool Generate failed login attempts */
    protected $generatefailedlogins;

    /** @var int Minimum failed logins per day */
    protected $failedloginsmin;

    /** @var int Maximum failed logins per day */
    protected $failedloginsmax;

    /** @var bool Calculate and track session duration */
    protected $calculatesessionduration;

    /** @var int Minimum session duration in minutes */
    protected $sessiondurationmin;

    /** @var int Maximum session duration in minutes */
    protected $sessiondurationmax;

    /** @var array Session tracking for duration calculation */
    protected $sessiondurations = [];

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
        'failed_logins_generated' => 0,
        'sessions_with_duration' => 0,
        'total_session_minutes' => 0,
        'avg_session_minutes' => 0,
    ];

    /**
     * Constructor.
     *
     * @param array $options Options for generation
     */
    public function __construct(array $options = []) {
        $this->datefrom = $options['datefrom'] ?? strtotime('2025-11-15');
        $this->dateto = $options['dateto'] ?? time();
        // Ensure minimum values are at least 1.
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

        // Failed login settings (for security monitoring).
        $this->generatefailedlogins = $options['generatefailedlogins'] ?? true;
        $this->failedloginsmin = max(0, intval($options['failedloginsmin'] ?? 0));
        $this->failedloginsmax = max($this->failedloginsmin, intval($options['failedloginsmax'] ?? 3));

        // Session duration tracking settings.
        $this->calculatesessionduration = $options['calculatesessionduration'] ?? true;
        $this->sessiondurationmin = max(5, intval($options['sessiondurationmin'] ?? 10)); // Minimum 5 minutes.
        $this->sessiondurationmax = max($this->sessiondurationmin, intval($options['sessiondurationmax'] ?? 120)); // Max 2 hours default.
    }

    /**
     * Get a random number between min and max.
     *
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @return int Random number
     */
    protected function get_random_count(int $min, int $max): int {
        if ($min >= $max) {
            return $min;
        }
        return rand($min, $max);
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
     * Clean existing access records before generating new ones.
     *
     * @param array $users Array of user objects to clean records for
     * @return int Number of records deleted
     */
    public function clean_existing_records(array $users): int {
        global $DB;

        if (empty($users)) {
            return 0;
        }

        $deleted = 0;
        $userids = array_keys($users);

        // Get user IDs for SQL.
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

        // 1. Delete login events from logstore_standard_log.
        if ($this->logstore_exists()) {
            $deleted += $DB->count_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\user_loggedin'], $userparams)
            );
            $DB->delete_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\user_loggedin'], $userparams)
            );

            // 2. Delete course_viewed events.
            $deleted += $DB->count_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\course_viewed'], $userparams)
            );
            $DB->delete_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\course_viewed'], $userparams)
            );

            // 3. Delete course_module_viewed events (all module types).
            $deleted += $DB->count_records_select('logstore_standard_log',
                "eventname LIKE :eventname AND userid $usersql",
                array_merge(['eventname' => '%\\event\\course_module_viewed'], $userparams)
            );
            $DB->delete_records_select('logstore_standard_log',
                "eventname LIKE :eventname AND userid $usersql",
                array_merge(['eventname' => '%\\event\\course_module_viewed'], $userparams)
            );

            // 4. Delete dashboard_viewed events.
            $deleted += $DB->count_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\dashboard_viewed'], $userparams)
            );
            $DB->delete_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\dashboard_viewed'], $userparams)
            );

            // 5. Delete user_loggedout events.
            $deleted += $DB->count_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\user_loggedout'], $userparams)
            );
            $DB->delete_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\user_loggedout'], $userparams)
            );

            // 6. Delete course_completed events.
            $deleted += $DB->count_records_select('logstore_standard_log',
                "eventname = :eventname AND relateduserid $usersql",
                array_merge(['eventname' => '\\core\\event\\course_completed'], $userparams)
            );
            $DB->delete_records_select('logstore_standard_log',
                "eventname = :eventname AND relateduserid $usersql",
                array_merge(['eventname' => '\\core\\event\\course_completed'], $userparams)
            );

            // 7. Delete user_login_failed events.
            $deleted += $DB->count_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\user_login_failed'], $userparams)
            );
            $DB->delete_records_select('logstore_standard_log',
                "eventname = :eventname AND userid $usersql",
                array_merge(['eventname' => '\\core\\event\\user_login_failed'], $userparams)
            );

            // Also delete failed logins where userid is 0 but username matches our users.
            $usernames = [];
            foreach ($users as $user) {
                $usernames[] = $user->username;
            }
            if (!empty($usernames)) {
                foreach ($usernames as $username) {
                    $deleted += $DB->count_records_select('logstore_standard_log',
                        "eventname = :eventname AND other LIKE :username",
                        ['eventname' => '\\core\\event\\user_login_failed', 'username' => '%' . $username . '%']
                    );
                    $DB->delete_records_select('logstore_standard_log',
                        "eventname = :eventname AND other LIKE :username",
                        ['eventname' => '\\core\\event\\user_login_failed', 'username' => '%' . $username . '%']
                    );
                }
            }
        }

        // 4. Delete/truncate user_lastaccess records for these users.
        $deleted += $DB->count_records_select('user_lastaccess', "userid $usersql", $userparams);
        $DB->delete_records_select('user_lastaccess', "userid $usersql", $userparams);

        // 5. Delete local_report_user_logins records for these users.
        if ($DB->get_manager()->table_exists('local_report_user_logins')) {
            $deleted += $DB->count_records_select('local_report_user_logins', "userid $usersql", $userparams);
            $DB->delete_records_select('local_report_user_logins', "userid $usersql", $userparams);
        }

        // 9. Delete course_modules_completion records for these users.
        $deleted += $DB->count_records_select('course_modules_completion', "userid $usersql", $userparams);
        $DB->delete_records_select('course_modules_completion', "userid $usersql", $userparams);

        // 10. Delete course_completions records for these users.
        $deleted += $DB->count_records_select('course_completions', "userid $usersql", $userparams);
        $DB->delete_records_select('course_completions', "userid $usersql", $userparams);

        // 7. Delete course_modules_viewed records for these users.
        if ($DB->get_manager()->table_exists('course_modules_viewed')) {
            $deleted += $DB->count_records_select('course_modules_viewed', "userid $usersql", $userparams);
            $DB->delete_records_select('course_modules_viewed', "userid $usersql", $userparams);
        }

        // 8. Reset user access fields.
        foreach ($users as $user) {
            $update = new \stdClass();
            $update->id = $user->id;
            $update->firstaccess = 0;
            $update->lastaccess = 0;
            $update->lastlogin = 0;
            $update->currentlogin = 0;
            $update->lastip = '';
            $DB->update_record('user', $update);
        }

        $this->stats['records_deleted'] = $deleted;
        return $deleted;
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

        // Check if company_users table exists and has records.
        $companyusersexists = $DB->get_manager()->table_exists('company_users');
        $hascompanyusers = $companyusersexists && $DB->count_records('company_users') > 0;

        // Base query for users.
        if ($this->companyid > 0 && $hascompanyusers) {
            // Specific company selected.
            $sql = "SELECT DISTINCT u.*
                    FROM {user} u
                    JOIN {company_users} cu ON cu.userid = u.id
                    WHERE cu.companyid = :companyid";
            $params['companyid'] = $this->companyid;

            // Only active users in company.
            if ($this->onlyactive) {
                $sql .= " AND cu.suspended = 0";
            }
        } else if ($hascompanyusers) {
            // All users from all companies.
            $sql = "SELECT DISTINCT u.*
                    FROM {user} u
                    JOIN {company_users} cu ON cu.userid = u.id
                    WHERE 1=1";

            if ($this->onlyactive) {
                $sql .= " AND cu.suspended = 0";
            }
        } else {
            // Fallback: get all users if no company_users records exist.
            $sql = "SELECT u.*
                    FROM {user} u
                    WHERE 1=1";
        }

        // Only active users (not deleted, not suspended in Moodle).
        if ($this->onlyactive) {
            $sql .= " AND u.deleted = 0 AND u.suspended = 0";
        }

        // Exclude guest user and admin user.
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
     * Update user creation date.
     *
     * @param object $user User object
     * @return bool Success
     */
    public function update_user_created_date($user): bool {
        global $DB;

        if (!$this->updateusercreated) {
            return false;
        }

        try {
            // Update user table.
            $update = new \stdClass();
            $update->id = $user->id;
            $update->timecreated = $this->usercreateddate;
            $DB->update_record('user', $update);

            // Update local_report_user_logins if exists.
            if ($DB->get_manager()->table_exists('local_report_user_logins')) {
                if ($record = $DB->get_record('local_report_user_logins', ['userid' => $user->id])) {
                    $DB->set_field('local_report_user_logins', 'created', $this->usercreateddate, ['id' => $record->id]);
                }
            }

            $this->stats['users_updated']++;
            return true;
        } catch (\Exception $e) {
            debugging('Error updating user created date: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Get courses for a company.
     *
     * @return array Array of course objects
     */
    public function get_company_courses(): array {
        global $DB;

        // Check if company_course table exists and has records.
        $companycourseexists = $DB->get_manager()->table_exists('company_course');
        $hascompanycourses = $companycourseexists && $DB->count_records('company_course') > 0;

        if ($this->companyid > 0 && $hascompanycourses) {
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
            // Get all visible courses (fallback).
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
     * Get courses where a user is enrolled AND belong to the company.
     *
     * @param int $userid User ID
     * @return array Array of course objects
     */
    public function get_user_courses(int $userid): array {
        global $DB;

        // Check if company_course table exists.
        $companycourseexists = $DB->get_manager()->table_exists('company_course');
        $hascompanycourses = $companycourseexists && $DB->count_records('company_course') > 0;

        if ($this->companyid > 0 && $hascompanycourses) {
            // Get courses where user is enrolled AND course belongs to the company.
            $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname
                    FROM {course} c
                    JOIN {enrol} e ON e.courseid = c.id
                    JOIN {user_enrolments} ue ON ue.enrolid = e.id
                    JOIN {company_course} cc ON cc.courseid = c.id
                    WHERE ue.userid = :userid
                    AND cc.companyid = :companyid
                    AND c.id > 1
                    AND c.visible = 1
                    AND ue.status = 0
                    ORDER BY c.id ASC";

            return $DB->get_records_sql($sql, ['userid' => $userid, 'companyid' => $this->companyid]);
        } else {
            // Without company filter, get all enrolled courses.
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
     * Check if logstore_standard_log table exists.
     *
     * @return bool
     */
    protected function logstore_exists(): bool {
        global $DB;
        return $DB->get_manager()->table_exists('logstore_standard_log');
    }

    /**
     * Convert event data to log entry format (same as Moodle's buffered_writer).
     *
     * @param \core\event\base $event The event object
     * @param int $timestamp Custom timestamp
     * @param string $ip IP address
     * @return array Log entry data
     */
    protected function event_to_log_entry(\core\event\base $event, int $timestamp, string $ip): array {
        // Get event data the same way Moodle does.
        $entry = $event->get_data();

        // Serialize the 'other' field as Moodle does.
        $entry['other'] = serialize($entry['other']);

        // Override timestamp with our custom one.
        $entry['timecreated'] = $timestamp;

        // Add request info.
        $entry['origin'] = 'web';
        $entry['ip'] = $ip;
        $entry['realuserid'] = null;

        return $entry;
    }

    /**
     * Generate login record for a user using native Moodle event.
     *
     * @param object $user User object
     * @param int $timestamp Timestamp for the login
     * @return bool Success
     */
    public function generate_login_record($user, int $timestamp): bool {
        global $DB;

        // Verify logstore table exists.
        if (!$this->logstore_exists()) {
            return false;
        }

        $ip = $this->get_random_ip();

        try {
            // Create the event using Moodle's native event class.
            $event = \core\event\user_loggedin::create([
                'userid' => $user->id,
                'objectid' => $user->id,
                'other' => [
                    'username' => $user->username,
                ],
            ]);

            // Convert to log entry with custom timestamp.
            $entry = $this->event_to_log_entry($event, $timestamp, $ip);

            // Insert into logstore.
            $DB->insert_record('logstore_standard_log', (object)$entry);
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

        // Update firstaccess if not set or if timestamp is earlier.
        if (empty($user->firstaccess) || $timestamp < $user->firstaccess) {
            $update->firstaccess = $timestamp;
        }

        // Update lastaccess if this timestamp is newer.
        if ($timestamp > $user->lastaccess) {
            $update->lastaccess = $timestamp;
            $this->stats['lastaccess_updated']++;
        }

        // Update login times.
        if (empty($user->currentlogin) || $timestamp > $user->currentlogin) {
            $update->lastlogin = $user->currentlogin ?: $timestamp;
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

            if (empty($current->lastlogin) || $timestamp > $current->lastlogin) {
                $update->lastlogin = $timestamp;
            }

            $DB->update_record('local_report_user_logins', $update);
        } else {
            // Create new record.
            $record = new \stdClass();
            $record->userid = $user->id;
            $record->created = $this->updateusercreated ? $this->usercreateddate : $user->timecreated;
            $record->firstlogin = $timestamp;
            $record->lastlogin = $timestamp;
            $record->logincount = 1;
            $record->modifiedtime = time();

            $DB->insert_record('local_report_user_logins', $record);
        }
    }

    /**
     * Generate course access record for a user using native Moodle event.
     *
     * @param object $user User object
     * @param object $course Course object
     * @param int $timestamp Timestamp for the access
     * @return bool Success
     */
    public function generate_course_access_record($user, $course, int $timestamp): bool {
        global $DB;

        // Verify logstore table exists.
        if (!$this->logstore_exists()) {
            return false;
        }

        $ip = $this->get_random_ip();

        try {
            $coursecontext = \context_course::instance($course->id, IGNORE_MISSING);
            if (!$coursecontext) {
                return false;
            }

            // Create the event using Moodle's native event class.
            $event = \core\event\course_viewed::create([
                'userid' => $user->id,
                'context' => $coursecontext,
            ]);

            // Convert to log entry with custom timestamp.
            $entry = $this->event_to_log_entry($event, $timestamp, $ip);

            // Insert into logstore.
            $DB->insert_record('logstore_standard_log', (object)$entry);
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
     * Generate activity access record for a user using native Moodle event when available.
     *
     * @param object $user User object
     * @param object $course Course object
     * @param object $cm Course module object
     * @param int $timestamp Timestamp for the access
     * @return bool Success
     */
    public function generate_activity_access_record($user, $course, $cm, int $timestamp): bool {
        global $DB;

        // Verify logstore table exists.
        if (!$this->logstore_exists()) {
            return false;
        }

        $ip = $this->get_random_ip();

        try {
            $cmcontext = \context_module::instance($cm->id, IGNORE_MISSING);
            if (!$cmcontext) {
                return false;
            }

            // Build event class name based on module type.
            $eventclass = '\\mod_' . $cm->modname . '\\event\\course_module_viewed';

            // Check if the event class exists for this module.
            if (class_exists($eventclass)) {
                // Create the event using the module's native event class.
                $event = $eventclass::create([
                    'userid' => $user->id,
                    'context' => $cmcontext,
                    'objectid' => $cm->instance,
                ]);

                // Convert to log entry with custom timestamp.
                $entry = $this->event_to_log_entry($event, $timestamp, $ip);
            } else {
                // Fallback: create log entry manually for modules without the event class.
                $entry = [
                    'eventname' => $eventclass,
                    'component' => 'mod_' . $cm->modname,
                    'action' => 'viewed',
                    'target' => 'course_module',
                    'objecttable' => $cm->modname,
                    'objectid' => $cm->instance,
                    'crud' => 'r',
                    'edulevel' => \core\event\base::LEVEL_PARTICIPATING,
                    'contextid' => $cmcontext->id,
                    'contextlevel' => CONTEXT_MODULE,
                    'contextinstanceid' => $cm->id,
                    'userid' => $user->id,
                    'courseid' => $course->id,
                    'relateduserid' => null,
                    'anonymous' => 0,
                    'other' => 'N;',
                    'timecreated' => $timestamp,
                    'origin' => 'web',
                    'ip' => $ip,
                    'realuserid' => null,
                ];
            }

            // Insert into logstore.
            $DB->insert_record('logstore_standard_log', (object)$entry);
            $this->stats['activity_access_generated']++;

            // Update completion tracking tables (as Moodle does when viewing an activity).
            $this->update_activity_completion($user->id, $cm, $timestamp);

            return true;
        } catch (\Exception $e) {
            debugging('Error generating activity access record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Update activity completion tables when an activity is viewed.
     * This mimics what Moodle's completion_info::set_module_viewed() does.
     *
     * @param int $userid User ID
     * @param object $cm Course module object
     * @param int $timestamp Timestamp
     */
    protected function update_activity_completion(int $userid, $cm, int $timestamp): void {
        global $DB;

        // Get or create course_modules_completion record.
        $existing = $DB->get_record('course_modules_completion', [
            'coursemoduleid' => $cm->id,
            'userid' => $userid,
        ]);

        if ($existing) {
            // Update existing record - mark as viewed.
            if ($existing->viewed != 1) {
                $update = new \stdClass();
                $update->id = $existing->id;
                $update->viewed = 1;
                $update->timemodified = $timestamp;
                $DB->update_record('course_modules_completion', $update);
            }
        } else {
            // Create new completion record.
            $record = new \stdClass();
            $record->coursemoduleid = $cm->id;
            $record->userid = $userid;
            $record->completionstate = 0; // Not completed, just viewed.
            $record->viewed = 1;
            $record->overrideby = null;
            $record->timemodified = $timestamp;

            $DB->insert_record('course_modules_completion', $record);
        }

        // Update course_modules_viewed table (separate tracking table).
        $existingViewed = $DB->get_record('course_modules_viewed', [
            'coursemoduleid' => $cm->id,
            'userid' => $userid,
        ]);

        if (!$existingViewed) {
            $viewedRecord = new \stdClass();
            $viewedRecord->coursemoduleid = $cm->id;
            $viewedRecord->userid = $userid;
            $viewedRecord->timecreated = $timestamp;

            $DB->insert_record('course_modules_viewed', $viewedRecord);
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
     * Generate dashboard access record for a user.
     *
     * @param object $user User object
     * @param int $timestamp Timestamp for the access
     * @return bool Success
     */
    public function generate_dashboard_access_record($user, int $timestamp): bool {
        global $DB;

        if (!$this->logstore_exists()) {
            return false;
        }

        $ip = $this->get_random_ip();

        try {
            $usercontext = \context_user::instance($user->id, IGNORE_MISSING);
            if (!$usercontext) {
                return false;
            }

            // Create the event using Moodle's native event class.
            $event = \core\event\dashboard_viewed::create([
                'userid' => $user->id,
                'context' => $usercontext,
            ]);

            // Convert to log entry with custom timestamp.
            $entry = $this->event_to_log_entry($event, $timestamp, $ip);

            // Insert into logstore.
            $DB->insert_record('logstore_standard_log', (object)$entry);
            $this->stats['dashboard_access_generated']++;

            return true;
        } catch (\Exception $e) {
            debugging('Error generating dashboard access record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Generate logout record for a user.
     *
     * @param object $user User object
     * @param int $timestamp Timestamp for the logout
     * @return bool Success
     */
    public function generate_logout_record($user, int $timestamp): bool {
        global $DB;

        if (!$this->logstore_exists()) {
            return false;
        }

        $ip = $this->get_random_ip();

        try {
            // Create the event using Moodle's native event class.
            $event = \core\event\user_loggedout::create([
                'userid' => $user->id,
                'objectid' => $user->id,
                'other' => [
                    'sessionid' => md5(uniqid(rand(), true)),
                ],
            ]);

            // Convert to log entry with custom timestamp.
            $entry = $this->event_to_log_entry($event, $timestamp, $ip);

            // Insert into logstore.
            $DB->insert_record('logstore_standard_log', (object)$entry);
            $this->stats['logouts_generated']++;

            return true;
        } catch (\Exception $e) {
            debugging('Error generating logout record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Generate failed login record for a user (for security monitoring).
     *
     * @param object $user User object
     * @param int $timestamp Timestamp for the failed login
     * @param int $reason Failure reason (1=user not exist, 2=suspended, 3=wrong password, 4=locked out, 5=not authorized)
     * @return bool Success
     */
    public function generate_failed_login_record($user, int $timestamp, int $reason = 3): bool {
        global $DB;

        if (!$this->logstore_exists()) {
            return false;
        }

        $ip = $this->get_random_ip();

        try {
            // Create the event using Moodle's native event class.
            $event = \core\event\user_login_failed::create([
                'userid' => $user->id, // The actual user ID if known.
                'other' => [
                    'username' => $user->username,
                    'reason' => $reason,
                ],
            ]);

            // Convert to log entry with custom timestamp.
            $entry = $this->event_to_log_entry($event, $timestamp, $ip);

            // Insert into logstore.
            $DB->insert_record('logstore_standard_log', (object)$entry);
            $this->stats['failed_logins_generated']++;

            return true;
        } catch (\Exception $e) {
            debugging('Error generating failed login record: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Generate a session with proper duration tracking.
     * Creates login, activities during session, and logout with realistic timing.
     *
     * @param object $user User object
     * @param int $logintime Login timestamp
     * @param int $sessionduration Session duration in seconds
     * @return array Session info with events generated
     */
    public function generate_session_with_duration($user, int $logintime, int $sessionduration): array {
        $sessioninfo = [
            'login_time' => $logintime,
            'logout_time' => $logintime + $sessionduration,
            'duration_minutes' => round($sessionduration / 60, 1),
            'events' => [],
        ];

        // Generate login.
        if ($this->generate_login_record($user, $logintime)) {
            $sessioninfo['events'][] = 'login';
        }

        // Generate dashboard access shortly after login (5-60 seconds).
        if ($this->generatedashboard && rand(1, 100) <= 80) {
            $dashboardtime = $logintime + rand(5, 60);
            if ($dashboardtime < $sessioninfo['logout_time']) {
                $this->generate_dashboard_access_record($user, $dashboardtime);
                $sessioninfo['events'][] = 'dashboard';
            }
        }

        // Generate logout at the end of session.
        if ($this->generate_logout_record($user, $sessioninfo['logout_time'])) {
            $sessioninfo['events'][] = 'logout';
        }

        // Track session duration for statistics.
        $this->sessiondurations[] = $sessioninfo['duration_minutes'];
        $this->stats['sessions_with_duration']++;
        $this->stats['total_session_minutes'] += $sessioninfo['duration_minutes'];

        return $sessioninfo;
    }

    /**
     * Calculate and update average session duration statistics.
     */
    protected function calculate_session_stats(): void {
        if ($this->stats['sessions_with_duration'] > 0) {
            $this->stats['avg_session_minutes'] = round(
                $this->stats['total_session_minutes'] / $this->stats['sessions_with_duration'],
                1
            );
        }
    }

    /**
     * Generate course completion record for a user.
     *
     * @param object $user User object
     * @param object $course Course object
     * @param int $timestamp Timestamp for the completion
     * @return bool Success
     */
    public function generate_course_completion($user, $course, int $timestamp): bool {
        global $DB;

        if (!$this->logstore_exists()) {
            return false;
        }

        $ip = $this->get_random_ip();

        try {
            // Create or update course_completions record.
            $completion = new \stdClass();
            $completion->userid = $user->id;
            $completion->course = $course->id;
            $completion->timeenrolled = $timestamp - 86400 * 7; // 7 days before completion.
            $completion->timestarted = $timestamp - 86400; // 1 day before completion.
            $completion->timecompleted = $timestamp;
            $completion->reaggregate = 0;

            $existing = $DB->get_record('course_completions', [
                'userid' => $user->id,
                'course' => $course->id,
            ]);

            if ($existing) {
                $completion->id = $existing->id;
                $DB->update_record('course_completions', $completion);
            } else {
                $completion->id = $DB->insert_record('course_completions', $completion);
            }

            // Create the course_completed event.
            $coursecontext = \context_course::instance($course->id, IGNORE_MISSING);
            if (!$coursecontext) {
                return false;
            }

            $event = \core\event\course_completed::create([
                'objectid' => $completion->id,
                'relateduserid' => $user->id,
                'context' => $coursecontext,
                'courseid' => $course->id,
                'other' => [
                    'relateduserid' => $user->id,
                ],
            ]);

            // Convert to log entry with custom timestamp.
            $entry = $this->event_to_log_entry($event, $timestamp, $ip);

            // Insert into logstore.
            $DB->insert_record('logstore_standard_log', (object)$entry);
            $this->stats['completions_generated']++;

            return true;
        } catch (\Exception $e) {
            debugging('Error generating course completion: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
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

        // Clean existing records before generating new ones.
        if ($this->cleanbeforegenerate) {
            $this->clean_existing_records($users);
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

            // Update user creation date first.
            if ($this->updateusercreated) {
                $this->update_user_created_date($user);
            }

            // Generate random number of login records.
            if ($this->accesstype === 'login' || $this->accesstype === 'all') {
                $numlogins = $this->get_random_count($this->loginsmin, $this->loginsmax);
                for ($i = 0; $i < $numlogins; $i++) {
                    $timestamp = $this->get_random_timestamp();

                    // Use session duration tracking if enabled.
                    if ($this->calculatesessionduration) {
                        // Calculate session duration in seconds.
                        $sessiondurationminutes = $this->get_random_count($this->sessiondurationmin, $this->sessiondurationmax);
                        $sessiondurationseconds = $sessiondurationminutes * 60;

                        // Generate complete session with duration tracking.
                        $this->generate_session_with_duration($user, $timestamp, $sessiondurationseconds);
                    } else {
                        // Original behavior without session duration tracking.
                        $this->generate_login_record($user, $timestamp);

                        // Generate dashboard access after login (70% probability).
                        if ($this->generatedashboard && rand(1, 100) <= 70) {
                            $dashboardtime = $timestamp + rand(5, 60); // 5-60 seconds after login.
                            $this->generate_dashboard_access_record($user, $dashboardtime);
                        }

                        // Generate logout record if enabled (50% probability).
                        if ($this->generatelogouts && rand(1, 100) <= 50) {
                            $logouttime = $timestamp + rand(300, 7200); // 5 min - 2 hours after login.
                            $this->generate_logout_record($user, $logouttime);
                        }
                    }
                }

                // Generate failed login attempts if enabled (for security monitoring).
                if ($this->generatefailedlogins && $this->failedloginsmax > 0) {
                    $numfailedlogins = $this->get_random_count($this->failedloginsmin, $this->failedloginsmax);
                    for ($i = 0; $i < $numfailedlogins; $i++) {
                        $timestamp = $this->get_random_timestamp();
                        // Random failure reason: 1=user not exist, 2=suspended, 3=wrong password (most common), 4=locked, 5=not authorized.
                        $reason = (rand(1, 100) <= 80) ? 3 : rand(1, 5); // 80% wrong password, 20% other reasons.
                        $this->generate_failed_login_record($user, $timestamp, $reason);
                    }
                }
            }

            // Get courses where user is enrolled (and belong to company if companyid is set).
            // Only generate access records for courses where user is actually enrolled.
            $usercourses = $this->get_user_courses($user->id);

            // Skip course/activity access if user is not enrolled in any courses.
            if (!empty($usercourses)) {
                // Generate random number of course access records.
                if ($this->accesstype === 'course' || $this->accesstype === 'all') {
                    foreach ($usercourses as $course) {
                        $numaccess = $this->get_random_count($this->courseaccessmin, $this->courseaccessmax);
                        for ($i = 0; $i < $numaccess; $i++) {
                            $timestamp = $this->get_random_timestamp();
                            $this->generate_course_access_record($user, $course, $timestamp);
                        }
                    }
                }

                // Generate random number of activity access records.
                if ($this->accesstype === 'activity' || $this->accesstype === 'all') {
                    foreach ($usercourses as $course) {
                        if (!isset($coursemodules[$course->id])) {
                            $coursemodules[$course->id] = $this->get_course_modules($course->id);
                        }

                        $modules = $coursemodules[$course->id];
                        foreach ($modules as $cm) {
                            $numaccess = $this->get_random_count($this->activityaccessmin, $this->activityaccessmax);
                            for ($i = 0; $i < $numaccess; $i++) {
                                $timestamp = $this->get_random_timestamp();
                                $this->generate_activity_access_record($user, $course, $cm, $timestamp);
                            }
                        }
                    }
                }

                // Generate course completions if enabled.
                if ($this->generatecompletions) {
                    $completionpercent = $this->get_random_count($this->completionpercentmin, $this->completionpercentmax);
                    foreach ($usercourses as $course) {
                        // Each user completes a random percentage of their courses.
                        if (rand(1, 100) <= $completionpercent) {
                            $timestamp = $this->get_random_timestamp();
                            $this->generate_course_completion($user, $course, $timestamp);
                        }
                    }
                }
            } else {
                // User has no course enrollments in company courses.
                $this->stats['users_without_enrollments']++;
            }
        }

        // Calculate final session duration statistics.
        $this->calculate_session_stats();

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

    /**
     * Get session duration statistics.
     *
     * @return array Session duration stats [min, max, avg, median, total_sessions]
     */
    public function get_session_duration_stats(): array {
        if (empty($this->sessiondurations)) {
            return [
                'min' => 0,
                'max' => 0,
                'avg' => 0,
                'median' => 0,
                'total_sessions' => 0,
            ];
        }

        $sorted = $this->sessiondurations;
        sort($sorted);
        $count = count($sorted);
        $middle = floor($count / 2);

        return [
            'min' => min($sorted),
            'max' => max($sorted),
            'avg' => round(array_sum($sorted) / $count, 1),
            'median' => ($count % 2) ? $sorted[$middle] : ($sorted[$middle - 1] + $sorted[$middle]) / 2,
            'total_sessions' => $count,
        ];
    }
}
