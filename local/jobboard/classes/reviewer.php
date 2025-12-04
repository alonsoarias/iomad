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
 * Reviewer management class.
 *
 * Handles assignment of reviewers to applications and workload balancing.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing reviewer assignments.
 */
class reviewer {

    /**
     * Assign a reviewer to an application.
     *
     * @param int $applicationid Application ID.
     * @param int $reviewerid Reviewer user ID.
     * @return bool Success.
     */
    public static function assign(int $applicationid, int $reviewerid): bool {
        global $DB, $USER;

        // Verify application exists.
        $application = application::get($applicationid);
        if (!$application) {
            return false;
        }

        // Verify reviewer has capability.
        $context = \context_system::instance();
        if (!has_capability('local/jobboard:reviewdocuments', $context, $reviewerid)) {
            return false;
        }

        // Update application.
        $record = new \stdClass();
        $record->id = $applicationid;
        $record->reviewerid = $reviewerid;
        $record->timemodified = time();

        $result = $DB->update_record('local_jobboard_application', $record);

        if ($result) {
            // Log assignment.
            audit::log('reviewer_assigned', 'local_jobboard_application', $applicationid, [
                'reviewerid' => $reviewerid,
                'assignedby' => $USER->id,
            ]);

            // Notify reviewer.
            self::notify_assignment($application, $reviewerid);
        }

        return $result;
    }

    /**
     * Unassign reviewer from application.
     *
     * @param int $applicationid Application ID.
     * @return bool Success.
     */
    public static function unassign(int $applicationid): bool {
        global $DB, $USER;

        $application = application::get($applicationid);
        if (!$application || !$application->reviewerid) {
            return false;
        }

        $previousreviewer = $application->reviewerid;

        $record = new \stdClass();
        $record->id = $applicationid;
        $record->reviewerid = null;
        $record->timemodified = time();

        $result = $DB->update_record('local_jobboard_application', $record);

        if ($result) {
            audit::log('reviewer_unassigned', 'local_jobboard_application', $applicationid, [
                'previousreviewer' => $previousreviewer,
                'unassignedby' => $USER->id,
            ]);
        }

        return $result;
    }

    /**
     * Bulk assign applications to a reviewer.
     *
     * @param array $applicationids Array of application IDs.
     * @param int $reviewerid Reviewer user ID.
     * @return array Results with 'success' and 'failed' counts.
     */
    public static function bulk_assign(array $applicationids, int $reviewerid): array {
        $results = ['success' => 0, 'failed' => 0];

        foreach ($applicationids as $appid) {
            if (self::assign($appid, $reviewerid)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Auto-assign applications based on workload.
     *
     * @param int $vacancyid Vacancy ID (optional, for specific vacancy).
     * @param int $maxperreviewer Maximum applications per reviewer.
     * @return int Number of applications assigned.
     */
    public static function auto_assign(?int $vacancyid = null, int $maxperreviewer = 20): int {
        global $DB;

        // Get available reviewers.
        $reviewers = self::get_available_reviewers($vacancyid);
        if (empty($reviewers)) {
            return 0;
        }

        // Get unassigned applications.
        $params = ['reviewerid' => null];
        $where = 'reviewerid IS NULL AND status IN (:s1, :s2)';
        $params['s1'] = 'submitted';
        $params['s2'] = 'under_review';

        if ($vacancyid) {
            $where .= ' AND vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        $applications = $DB->get_records_select('local_jobboard_application', $where, $params, 'timecreated ASC');

        $assigned = 0;
        $reviewerindex = 0;
        $reviewerids = array_keys($reviewers);

        foreach ($applications as $app) {
            // Find reviewer with lowest workload.
            $selectedreviewer = null;
            $lowestworkload = PHP_INT_MAX;

            foreach ($reviewerids as $rid) {
                $workload = self::get_reviewer_workload($rid);
                if ($workload < $maxperreviewer && $workload < $lowestworkload) {
                    $lowestworkload = $workload;
                    $selectedreviewer = $rid;
                }
            }

            if ($selectedreviewer && self::assign($app->id, $selectedreviewer)) {
                $assigned++;
            }
        }

        return $assigned;
    }

    /**
     * Get available reviewers.
     *
     * @param int|null $vacancyid Vacancy ID for company filtering.
     * @return array Reviewers keyed by user ID.
     */
    public static function get_available_reviewers(?int $vacancyid = null): array {
        global $DB;

        $context = \context_system::instance();

        // Get users with review capability.
        $users = get_users_by_capability($context, 'local/jobboard:reviewdocuments', 'u.id, u.firstname, u.lastname, u.email');

        // Filter by company if vacancy has one.
        if ($vacancyid) {
            $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
            if ($vacancy && $vacancy->companyid) {
                $companyusers = $DB->get_records('company_users', ['companyid' => $vacancy->companyid], '', 'userid');
                $users = array_filter($users, fn($u) => isset($companyusers[$u->id]));
            }
        }

        return $users;
    }

    /**
     * Get reviewer's current workload.
     *
     * @param int $reviewerid Reviewer user ID.
     * @return int Number of active assigned applications.
     */
    public static function get_reviewer_workload(int $reviewerid): int {
        global $DB;

        return $DB->count_records_select('local_jobboard_application',
            'reviewerid = :reviewerid AND status IN (:s1, :s2, :s3)',
            [
                'reviewerid' => $reviewerid,
                's1' => 'submitted',
                's2' => 'under_review',
                's3' => 'docs_validated',
            ]);
    }

    /**
     * Get reviewer statistics.
     *
     * @param int $reviewerid Reviewer user ID.
     * @param int $since Timestamp to calculate stats from.
     * @return array Statistics.
     */
    public static function get_reviewer_stats(int $reviewerid, int $since = 0): array {
        global $DB;

        if ($since == 0) {
            $since = strtotime('-30 days');
        }

        $stats = [
            'assigned' => 0,
            'reviewed' => 0,
            'validated' => 0,
            'rejected' => 0,
            'pending' => 0,
            'avg_review_time' => 0,
        ];

        // Current assignments.
        $stats['pending'] = self::get_reviewer_workload($reviewerid);

        // Completed reviews.
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN a.status IN ('docs_validated', 'interview', 'selected') THEN 1 ELSE 0 END) as validated,
                       SUM(CASE WHEN a.status IN ('docs_rejected', 'rejected') THEN 1 ELSE 0 END) as rejected
                  FROM {local_jobboard_application} a
                 WHERE a.reviewerid = :reviewerid
                   AND a.timemodified > :since
                   AND a.status NOT IN ('submitted', 'under_review')";

        $result = $DB->get_record_sql($sql, ['reviewerid' => $reviewerid, 'since' => $since]);
        if ($result) {
            $stats['reviewed'] = (int) $result->total;
            $stats['validated'] = (int) $result->validated;
            $stats['rejected'] = (int) $result->rejected;
        }

        // Calculate average review time from workflow logs.
        $sql = "SELECT AVG(w2.timecreated - w1.timecreated) as avgtime
                  FROM {local_jobboard_workflow_log} w1
                  JOIN {local_jobboard_workflow_log} w2 ON w1.applicationid = w2.applicationid
                  JOIN {local_jobboard_application} a ON a.id = w1.applicationid
                 WHERE a.reviewerid = :reviewerid
                   AND w1.newstatus = 'under_review'
                   AND w2.newstatus IN ('docs_validated', 'docs_rejected')
                   AND w2.timecreated > :since";

        $avgresult = $DB->get_record_sql($sql, ['reviewerid' => $reviewerid, 'since' => $since]);
        if ($avgresult && $avgresult->avgtime) {
            $stats['avg_review_time'] = round($avgresult->avgtime / 3600, 1); // Hours.
        }

        return $stats;
    }

    /**
     * Get all reviewers with their workloads.
     *
     * @return array Reviewers with workload info.
     */
    public static function get_all_with_workload(): array {
        $reviewers = self::get_available_reviewers();
        $result = [];

        foreach ($reviewers as $reviewer) {
            $reviewer->workload = self::get_reviewer_workload($reviewer->id);
            $reviewer->stats = self::get_reviewer_stats($reviewer->id);
            $result[] = $reviewer;
        }

        // Sort by workload ascending.
        usort($result, fn($a, $b) => $a->workload <=> $b->workload);

        return $result;
    }

    /**
     * Get applications assigned to a reviewer.
     *
     * @param int $reviewerid Reviewer user ID.
     * @param array $filters Additional filters.
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array Applications and total.
     */
    public static function get_assigned_applications(int $reviewerid, array $filters = [],
        int $page = 0, int $perpage = 20): array {

        $filters['reviewerid'] = $reviewerid;
        return application::get_list($filters, 'timecreated', 'ASC', $page, $perpage);
    }

    /**
     * Notify reviewer of new assignment.
     *
     * @param application $application Application object.
     * @param int $reviewerid Reviewer user ID.
     */
    protected static function notify_assignment(application $application, int $reviewerid): void {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        $applicant = $DB->get_record('user', ['id' => $application->userid]);

        $data = [
            'vacancy_code' => $vacancy->code ?? '',
            'vacancy_title' => $vacancy->title ?? '',
            'applicant_name' => fullname($applicant),
            'application_id' => $application->id,
            'application_url' => (new \moodle_url('/local/jobboard/application.php',
                ['id' => $application->id]))->out(false),
        ];

        notification::queue($reviewerid, 'reviewer_assigned', $data);
    }

    /**
     * Reassign applications from one reviewer to another.
     *
     * @param int $fromreviewerid Source reviewer ID.
     * @param int $toreviewerid Target reviewer ID.
     * @param array $applicationids Specific applications (empty = all).
     * @return int Number reassigned.
     */
    public static function reassign(int $fromreviewerid, int $toreviewerid, array $applicationids = []): int {
        global $DB;

        if (empty($applicationids)) {
            // Get all active assignments.
            $applications = $DB->get_records_select('local_jobboard_application',
                'reviewerid = :reviewerid AND status IN (:s1, :s2)',
                [
                    'reviewerid' => $fromreviewerid,
                    's1' => 'submitted',
                    's2' => 'under_review',
                ]);
            $applicationids = array_keys($applications);
        }

        $reassigned = 0;
        foreach ($applicationids as $appid) {
            if (self::assign($appid, $toreviewerid)) {
                $reassigned++;
            }
        }

        return $reassigned;
    }
}
