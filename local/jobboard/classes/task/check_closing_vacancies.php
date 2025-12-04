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
 * Scheduled task to check for closing vacancies.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\task;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\vacancy;
use local_jobboard\notification;

/**
 * Scheduled task to notify about vacancies closing soon.
 */
class check_closing_vacancies extends \core\task\scheduled_task {

    /**
     * Get task name.
     *
     * @return string Task name.
     */
    public function get_name(): string {
        return get_string('task:checkclosingvacancies', 'local_jobboard');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $days = get_config('local_jobboard', 'closingnotificationdays');
        if (empty($days)) {
            $days = 3; // Default 3 days.
        }

        $now = time();
        $threshold = $now + ($days * 24 * 60 * 60);

        // Find published vacancies closing within threshold.
        $sql = "SELECT v.*
                  FROM {local_jobboard_vacancy} v
                 WHERE v.status = 'published'
                   AND v.closedate > :now
                   AND v.closedate <= :threshold";

        $vacancies = $DB->get_records_sql($sql, [
            'now' => $now,
            'threshold' => $threshold,
        ]);

        $notified = 0;

        foreach ($vacancies as $vacancyrecord) {
            $vacancy = new vacancy($vacancyrecord);
            $daysleft = ceil(($vacancy->closedate - $now) / (24 * 60 * 60));

            // Get users who might be interested (those with apply capability).
            $users = $this->get_potential_applicants($vacancy);

            foreach ($users as $user) {
                // Check if not already applied and not already notified.
                if (!$this->has_applied($user->id, $vacancy->id) &&
                    !$this->already_notified($user->id, $vacancy->id, 'closing_soon')) {

                    notification::queue_closing_soon($vacancy, $user->id, $daysleft);
                    $this->mark_notified($user->id, $vacancy->id, 'closing_soon');
                    $notified++;
                }
            }
        }

        // Also auto-close expired vacancies.
        $closed = $DB->execute(
            "UPDATE {local_jobboard_vacancy}
                SET status = 'closed', timemodified = :now
              WHERE status = 'published' AND closedate < :closedate",
            ['now' => $now, 'closedate' => $now]
        );

        mtrace("Closing notifications sent: {$notified}");
        mtrace("Vacancies auto-closed: {$closed}");
    }

    /**
     * Get potential applicants for a vacancy.
     *
     * @param vacancy $vacancy Vacancy object.
     * @return array Users.
     */
    protected function get_potential_applicants(vacancy $vacancy): array {
        global $DB;

        // Get users with apply capability (simplified - in production would use proper capability check).
        $sql = "SELECT DISTINCT u.id, u.email, u.firstname, u.lastname
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.suspended = 0
                   AND u.confirmed = 1";

        // If vacancy has company restriction, filter by company.
        if (!empty($vacancy->companyid)) {
            $sql .= " AND EXISTS (SELECT 1 FROM {company_users} cu
                                   WHERE cu.userid = u.id AND cu.companyid = :companyid)";
            return $DB->get_records_sql($sql, ['companyid' => $vacancy->companyid]);
        }

        // Limit to prevent spam.
        return $DB->get_records_sql($sql, [], 0, 1000);
    }

    /**
     * Check if user has applied to vacancy.
     *
     * @param int $userid User ID.
     * @param int $vacancyid Vacancy ID.
     * @return bool Has applied.
     */
    protected function has_applied(int $userid, int $vacancyid): bool {
        global $DB;
        return $DB->record_exists('local_jobboard_application', [
            'userid' => $userid,
            'vacancyid' => $vacancyid,
        ]);
    }

    /**
     * Check if user was already notified.
     *
     * @param int $userid User ID.
     * @param int $vacancyid Vacancy ID.
     * @param string $type Notification type.
     * @return bool Already notified.
     */
    protected function already_notified(int $userid, int $vacancyid, string $type): bool {
        global $DB;

        // Check in last 24 hours.
        return $DB->record_exists_select('local_jobboard_notification',
            "userid = :userid AND templatecode = :type AND timecreated > :since
             AND " . $DB->sql_like('data', ':vacancy'),
            [
                'userid' => $userid,
                'type' => $type,
                'since' => time() - (24 * 60 * 60),
                'vacancy' => '%"vacancy_code":"' . $DB->sql_like_escape($vacancyid) . '"%',
            ]);
    }

    /**
     * Mark user as notified.
     *
     * @param int $userid User ID.
     * @param int $vacancyid Vacancy ID.
     * @param string $type Notification type.
     */
    protected function mark_notified(int $userid, int $vacancyid, string $type): void {
        // The notification record itself serves as the marker.
    }
}
