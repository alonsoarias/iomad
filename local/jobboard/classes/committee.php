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

declare(strict_types=1);

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Selection committee management class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class committee {

    /** @var string Member role - chair. */
    const ROLE_CHAIR = 'chair';

    /** @var string Member role - evaluator. */
    const ROLE_EVALUATOR = 'evaluator';

    /** @var string Member role - observer. */
    const ROLE_OBSERVER = 'observer';

    /** @var string Member role - secretary. */
    const ROLE_SECRETARY = 'secretary';

    /** @var string Vote - approve. */
    const VOTE_APPROVE = 'approve';

    /** @var string Vote - reject. */
    const VOTE_REJECT = 'reject';

    /** @var string Vote - abstain. */
    const VOTE_ABSTAIN = 'abstain';

    /**
     * Create a selection committee for a faculty/company.
     *
     * This is the primary method - committees are now per faculty (company in IOMAD).
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @param string $name Committee name.
     * @param array $members Array of member definitions [userid, role].
     * @return int|bool Committee ID on success, false on failure.
     */
    public static function create_for_company(int $companyid, string $name, array $members = []) {
        global $DB, $USER;

        // Check if committee already exists for this company.
        if ($DB->record_exists('local_jobboard_committee', ['companyid' => $companyid])) {
            return false;
        }

        $committee = new \stdClass();
        $committee->companyid = $companyid;
        $committee->vacancyid = null; // Faculty-wide committee.
        $committee->name = $name;
        $committee->status = 'active';
        $committee->createdby = $USER->id;
        $committee->timecreated = time();

        $committee->id = $DB->insert_record('local_jobboard_committee', $committee);

        // Add members.
        foreach ($members as $member) {
            if (!isset($member['userid']) || !isset($member['role'])) {
                continue;
            }
            self::add_member($committee->id, $member['userid'], $member['role']);
        }

        return $committee->id;
    }

    /**
     * Create a selection committee for a vacancy (legacy support).
     *
     * @deprecated Use create_for_company() instead. Committees should be per faculty.
     * @param int $vacancyid Vacancy ID.
     * @param string $name Committee name.
     * @param array $members Array of member definitions [userid, role].
     * @return int|bool Committee ID on success, false on failure.
     */
    public static function create(int $vacancyid, string $name, array $members = []) {
        global $DB, $USER;

        // Validate vacancy exists.
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
        if (!$vacancy) {
            return false;
        }

        // If vacancy has a companyid, check if a committee already exists for that company.
        if (!empty($vacancy->companyid)) {
            $existingcommittee = $DB->get_record('local_jobboard_committee', ['companyid' => $vacancy->companyid]);
            if ($existingcommittee) {
                // Return the existing company committee.
                return $existingcommittee->id;
            }
        }

        // Check if committee already exists for this vacancy.
        if ($DB->record_exists('local_jobboard_committee', ['vacancyid' => $vacancyid])) {
            return false;
        }

        $committee = new \stdClass();
        $committee->companyid = $vacancy->companyid ?? null;
        $committee->vacancyid = $vacancyid;
        $committee->name = $name;
        $committee->status = 'active';
        $committee->createdby = $USER->id;
        $committee->timecreated = time();

        $committee->id = $DB->insert_record('local_jobboard_committee', $committee);

        // Add members.
        foreach ($members as $member) {
            if (!isset($member['userid']) || !isset($member['role'])) {
                continue;
            }
            self::add_member($committee->id, $member['userid'], $member['role']);
        }

        return $committee->id;
    }

    /**
     * Get committee for a company/faculty.
     *
     * @param int $companyid IOMAD Company/Faculty ID.
     * @return object|bool Committee record with members, or false.
     */
    public static function get_for_company(int $companyid) {
        global $DB;

        $committee = $DB->get_record('local_jobboard_committee', ['companyid' => $companyid]);
        if (!$committee) {
            return false;
        }

        // Get members.
        $sql = "SELECT cm.*, u.firstname, u.lastname, u.email, u.username
                  FROM {local_jobboard_committee_member} cm
                  JOIN {user} u ON u.id = cm.userid
                 WHERE cm.committeeid = :committeeid
                 ORDER BY
                    CASE cm.role
                        WHEN 'chair' THEN 1
                        WHEN 'secretary' THEN 2
                        WHEN 'evaluator' THEN 3
                        WHEN 'observer' THEN 4
                    END, u.lastname";

        $committee->members = $DB->get_records_sql($sql, ['committeeid' => $committee->id]);

        return $committee;
    }

    /**
     * Add a member to the committee.
     *
     * @param int $committeeid Committee ID.
     * @param int $userid User ID.
     * @param string $role Member role.
     * @return int|bool Member record ID on success, false on failure.
     */
    public static function add_member(int $committeeid, int $userid, string $role) {
        global $DB, $USER;

        // Validate role.
        if (!in_array($role, [self::ROLE_CHAIR, self::ROLE_EVALUATOR, self::ROLE_OBSERVER, self::ROLE_SECRETARY])) {
            return false;
        }

        // Check if user is already a member.
        if ($DB->record_exists('local_jobboard_committee_member', [
            'committeeid' => $committeeid,
            'userid' => $userid,
        ])) {
            return false;
        }

        // If adding a chair, check there isn't already one.
        if ($role === self::ROLE_CHAIR) {
            if ($DB->record_exists('local_jobboard_committee_member', [
                'committeeid' => $committeeid,
                'role' => self::ROLE_CHAIR,
            ])) {
                // Remove existing chair role.
                $existing = $DB->get_record('local_jobboard_committee_member', [
                    'committeeid' => $committeeid,
                    'role' => self::ROLE_CHAIR,
                ]);
                $existing->role = self::ROLE_EVALUATOR;
                $DB->update_record('local_jobboard_committee_member', $existing);
            }
        }

        $member = new \stdClass();
        $member->committeeid = $committeeid;
        $member->userid = $userid;
        $member->role = $role;
        $member->addedby = $USER->id;
        $member->timecreated = time();

        return $DB->insert_record('local_jobboard_committee_member', $member);
    }

    /**
     * Remove a member from the committee.
     *
     * @param int $committeeid Committee ID.
     * @param int $userid User ID.
     * @return bool Success.
     */
    public static function remove_member(int $committeeid, int $userid): bool {
        global $DB;

        // Cannot remove the only chair.
        $member = $DB->get_record('local_jobboard_committee_member', [
            'committeeid' => $committeeid,
            'userid' => $userid,
        ]);

        if (!$member) {
            return false;
        }

        if ($member->role === self::ROLE_CHAIR) {
            // Check if there are other evaluators who can become chair.
            $othermembers = $DB->count_records_select('local_jobboard_committee_member',
                'committeeid = :committeeid AND userid != :userid AND role IN (:r1, :r2)',
                [
                    'committeeid' => $committeeid,
                    'userid' => $userid,
                    'r1' => self::ROLE_EVALUATOR,
                    'r2' => self::ROLE_CHAIR,
                ]
            );
            if ($othermembers == 0) {
                return false; // Cannot leave committee without evaluating members.
            }
        }

        return $DB->delete_records('local_jobboard_committee_member', [
            'committeeid' => $committeeid,
            'userid' => $userid,
        ]);
    }

    /**
     * Update member role.
     *
     * @param int $committeeid Committee ID.
     * @param int $userid User ID.
     * @param string $newrole New role.
     * @return bool Success.
     */
    public static function update_member_role(int $committeeid, int $userid, string $newrole): bool {
        global $DB;

        if (!in_array($newrole, [self::ROLE_CHAIR, self::ROLE_EVALUATOR, self::ROLE_OBSERVER, self::ROLE_SECRETARY])) {
            return false;
        }

        $member = $DB->get_record('local_jobboard_committee_member', [
            'committeeid' => $committeeid,
            'userid' => $userid,
        ]);

        if (!$member) {
            return false;
        }

        // If making someone chair, demote current chair.
        if ($newrole === self::ROLE_CHAIR && $member->role !== self::ROLE_CHAIR) {
            $DB->set_field('local_jobboard_committee_member', 'role', self::ROLE_EVALUATOR,
                ['committeeid' => $committeeid, 'role' => self::ROLE_CHAIR]);
        }

        $member->role = $newrole;
        $member->timemodified = time();

        return $DB->update_record('local_jobboard_committee_member', $member);
    }

    /**
     * Get committee for a vacancy.
     *
     * First checks for a faculty/company-wide committee, then falls back
     * to vacancy-specific committee for legacy data.
     *
     * @param int $vacancyid Vacancy ID.
     * @return object|bool Committee record with members, or false.
     */
    public static function get_for_vacancy(int $vacancyid) {
        global $DB;

        // Get the vacancy to find its company.
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
        if (!$vacancy) {
            return false;
        }

        // First, try to get the faculty/company committee.
        if (!empty($vacancy->companyid)) {
            $committee = $DB->get_record('local_jobboard_committee', ['companyid' => $vacancy->companyid]);
        }

        // Fall back to vacancy-specific committee (legacy).
        if (empty($committee)) {
            $committee = $DB->get_record('local_jobboard_committee', ['vacancyid' => $vacancyid]);
        }

        if (!$committee) {
            return false;
        }

        // Get members.
        $sql = "SELECT cm.*, u.firstname, u.lastname, u.email, u.username
                  FROM {local_jobboard_committee_member} cm
                  JOIN {user} u ON u.id = cm.userid
                 WHERE cm.committeeid = :committeeid
                 ORDER BY
                    CASE cm.role
                        WHEN 'chair' THEN 1
                        WHEN 'secretary' THEN 2
                        WHEN 'evaluator' THEN 3
                        WHEN 'observer' THEN 4
                    END, u.lastname";

        $committee->members = $DB->get_records_sql($sql, ['committeeid' => $committee->id]);

        return $committee;
    }

    /**
     * Get all committees.
     *
     * @return array Array of committee records with company/vacancy info.
     */
    public static function get_all(): array {
        global $DB;

        $sql = "SELECT c.*,
                       comp.name as company_name, comp.shortname as company_shortname,
                       v.code as vacancy_code, v.title as vacancy_title,
                       (SELECT COUNT(*) FROM {local_jobboard_committee_member} WHERE committeeid = c.id) as membercount
                  FROM {local_jobboard_committee} c
             LEFT JOIN {company} comp ON comp.id = c.companyid
             LEFT JOIN {local_jobboard_vacancy} v ON v.id = c.vacancyid
                 ORDER BY comp.name, c.timecreated DESC";

        return $DB->get_records_sql($sql);
    }

    /**
     * Record an evaluation/vote for an application.
     *
     * @param int $committeeid Committee ID.
     * @param int $applicationid Application ID.
     * @param int $userid Evaluator user ID.
     * @param int $score Numeric score (1-100).
     * @param string $vote Vote (approve, reject, abstain).
     * @param string $comments Evaluation comments.
     * @param array $criteria Criteria scores [criterionid => score].
     * @return int|bool Evaluation ID on success, false on failure.
     */
    public static function evaluate(
        int $committeeid,
        int $applicationid,
        int $userid,
        int $score,
        string $vote,
        string $comments = '',
        array $criteria = []
    ) {
        global $DB;

        // Validate user is a voting member.
        $member = $DB->get_record('local_jobboard_committee_member', [
            'committeeid' => $committeeid,
            'userid' => $userid,
        ]);

        if (!$member || $member->role === self::ROLE_OBSERVER) {
            return false; // Observers cannot vote.
        }

        // Validate vote.
        if (!in_array($vote, [self::VOTE_APPROVE, self::VOTE_REJECT, self::VOTE_ABSTAIN])) {
            return false;
        }

        // Validate score.
        if ($score < 0 || $score > 100) {
            return false;
        }

        // Check if already evaluated.
        $existing = $DB->get_record('local_jobboard_evaluation', [
            'committeeid' => $committeeid,
            'applicationid' => $applicationid,
            'userid' => $userid,
        ]);

        if ($existing) {
            // Update existing evaluation.
            $existing->score = $score;
            $existing->vote = $vote;
            $existing->comments = $comments;
            $existing->criteriaratings = json_encode($criteria);
            $existing->timemodified = time();

            $DB->update_record('local_jobboard_evaluation', $existing);
            return $existing->id;
        }

        // Create new evaluation.
        $evaluation = new \stdClass();
        $evaluation->committeeid = $committeeid;
        $evaluation->applicationid = $applicationid;
        $evaluation->userid = $userid;
        $evaluation->score = $score;
        $evaluation->vote = $vote;
        $evaluation->comments = $comments;
        $evaluation->criteriaratings = json_encode($criteria);
        $evaluation->timecreated = time();

        return $DB->insert_record('local_jobboard_evaluation', $evaluation);
    }

    /**
     * Get evaluations for an application.
     *
     * @param int $applicationid Application ID.
     * @param int $committeeid Committee ID (optional, if multiple committees).
     * @return array Array of evaluation records.
     */
    public static function get_evaluations(int $applicationid, int $committeeid = 0): array {
        global $DB;

        $params = ['applicationid' => $applicationid];
        $where = 'e.applicationid = :applicationid';

        if ($committeeid) {
            $where .= ' AND e.committeeid = :committeeid';
            $params['committeeid'] = $committeeid;
        }

        $sql = "SELECT e.*, u.firstname, u.lastname, cm.role
                  FROM {local_jobboard_evaluation} e
                  JOIN {user} u ON u.id = e.userid
                  JOIN {local_jobboard_committee_member} cm ON cm.committeeid = e.committeeid AND cm.userid = e.userid
                 WHERE $where
                 ORDER BY e.timecreated";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Calculate aggregate evaluation results for an application.
     *
     * @param int $applicationid Application ID.
     * @param int $committeeid Committee ID.
     * @return array Aggregated results.
     */
    public static function get_aggregate_results(int $applicationid, int $committeeid): array {
        global $DB;

        $evaluations = self::get_evaluations($applicationid, $committeeid);

        $results = [
            'total_evaluators' => 0,
            'votes_approve' => 0,
            'votes_reject' => 0,
            'votes_abstain' => 0,
            'avg_score' => 0,
            'min_score' => 0,
            'max_score' => 0,
            'recommendation' => 'pending',
            'evaluations' => $evaluations,
        ];

        if (empty($evaluations)) {
            return $results;
        }

        $scores = [];
        foreach ($evaluations as $eval) {
            $results['total_evaluators']++;

            switch ($eval->vote) {
                case self::VOTE_APPROVE:
                    $results['votes_approve']++;
                    break;
                case self::VOTE_REJECT:
                    $results['votes_reject']++;
                    break;
                case self::VOTE_ABSTAIN:
                    $results['votes_abstain']++;
                    break;
            }

            $scores[] = $eval->score;
        }

        if (!empty($scores)) {
            $results['avg_score'] = round(array_sum($scores) / count($scores), 1);
            $results['min_score'] = min($scores);
            $results['max_score'] = max($scores);
        }

        // Calculate recommendation based on votes.
        $votingmembers = $results['votes_approve'] + $results['votes_reject'];
        if ($votingmembers > 0) {
            $approvepct = ($results['votes_approve'] / $votingmembers) * 100;
            if ($approvepct >= 66) {
                $results['recommendation'] = 'strong_approve';
            } else if ($approvepct >= 50) {
                $results['recommendation'] = 'approve';
            } else if ($approvepct >= 33) {
                $results['recommendation'] = 'marginal';
            } else {
                $results['recommendation'] = 'reject';
            }
        }

        return $results;
    }

    /**
     * Make final selection decision for an application.
     *
     * @param int $applicationid Application ID.
     * @param string $decision Decision (selected, rejected).
     * @param string $reason Decision reason/notes.
     * @return bool Success.
     */
    public static function make_decision(int $applicationid, string $decision, string $reason = ''): bool {
        global $DB, $USER;

        if (!in_array($decision, ['selected', 'rejected'])) {
            return false;
        }

        $application = $DB->get_record('local_jobboard_application', ['id' => $applicationid]);
        if (!$application) {
            return false;
        }

        // Get committee.
        $committee = self::get_for_vacancy($application->vacancyid);
        if (!$committee) {
            return false;
        }

        // Check user is chair or has manage capability.
        $ischair = false;
        foreach ($committee->members as $member) {
            if ($member->userid == $USER->id && $member->role === self::ROLE_CHAIR) {
                $ischair = true;
                break;
            }
        }

        if (!$ischair && !has_capability('local/jobboard:manageworkflow', \context_system::instance())) {
            return false;
        }

        // Record decision.
        $record = new \stdClass();
        $record->committeeid = $committee->id;
        $record->applicationid = $applicationid;
        $record->decision = $decision;
        $record->reason = $reason;
        $record->decidedby = $USER->id;
        $record->timecreated = time();

        $DB->insert_record('local_jobboard_decision', $record);

        // Update application status.
        application::update_status($applicationid, $decision, $reason);

        // Trigger event.
        $eventclass = $decision === 'selected' ?
            '\local_jobboard\event\applicant_selected' :
            '\local_jobboard\event\applicant_rejected';

        $eventclass::create([
            'context' => \context_system::instance(),
            'objectid' => $applicationid,
            'relateduserid' => $application->userid,
            'other' => [
                'committeeid' => $committee->id,
                'reason' => $reason,
            ],
        ])->trigger();

        return true;
    }

    /**
     * Get ranking of applicants for a vacancy.
     *
     * @param int $vacancyid Vacancy ID.
     * @return array Ranked array of applications with scores.
     */
    public static function get_ranking(int $vacancyid): array {
        global $DB;

        $committee = self::get_for_vacancy($vacancyid);
        if (!$committee) {
            return [];
        }

        // Get all applications at interview stage or later.
        $sql = "SELECT a.id, a.userid, a.status, u.firstname, u.lastname, u.email,
                       COALESCE(AVG(e.score), 0) as avg_score,
                       COUNT(CASE WHEN e.vote = 'approve' THEN 1 END) as approve_votes,
                       COUNT(CASE WHEN e.vote = 'reject' THEN 1 END) as reject_votes,
                       COUNT(e.id) as total_evaluations
                  FROM {local_jobboard_application} a
                  JOIN {user} u ON u.id = a.userid
             LEFT JOIN {local_jobboard_evaluation} e ON e.applicationid = a.id AND e.committeeid = :committeeid
                 WHERE a.vacancyid = :vacancyid
                   AND a.status IN ('interview', 'docs_validated', 'selected', 'rejected')
                 GROUP BY a.id, a.userid, a.status, u.firstname, u.lastname, u.email
                 ORDER BY avg_score DESC, approve_votes DESC";

        $ranking = $DB->get_records_sql($sql, [
            'vacancyid' => $vacancyid,
            'committeeid' => $committee->id,
        ]);

        // Add rank numbers.
        $rank = 0;
        $lastscorekey = '';
        foreach ($ranking as &$app) {
            $scorekey = $app->avg_score . '-' . $app->approve_votes;
            if ($scorekey !== $lastscorekey) {
                $rank++;
                $lastscorekey = $scorekey;
            }
            $app->rank = $rank;
        }

        return $ranking;
    }

    /**
     * Check if user is a committee member.
     *
     * @param int $committeeid Committee ID.
     * @param int $userid User ID.
     * @return string|bool Role if member, false otherwise.
     */
    public static function get_member_role(int $committeeid, int $userid) {
        global $DB;

        $member = $DB->get_record('local_jobboard_committee_member', [
            'committeeid' => $committeeid,
            'userid' => $userid,
        ]);

        return $member ? $member->role : false;
    }

    /**
     * Get evaluation criteria for a vacancy.
     *
     * @param int $vacancyid Vacancy ID.
     * @return array Array of criteria.
     */
    public static function get_criteria(int $vacancyid): array {
        global $DB;

        return $DB->get_records('local_jobboard_criteria',
            ['vacancyid' => $vacancyid],
            'sortorder ASC'
        );
    }

    /**
     * Set evaluation criteria for a vacancy.
     *
     * @param int $vacancyid Vacancy ID.
     * @param array $criteria Array of criteria definitions.
     * @return bool Success.
     */
    public static function set_criteria(int $vacancyid, array $criteria): bool {
        global $DB;

        // Delete existing criteria.
        $DB->delete_records('local_jobboard_criteria', ['vacancyid' => $vacancyid]);

        // Insert new criteria.
        $sortorder = 0;
        foreach ($criteria as $criterion) {
            $record = new \stdClass();
            $record->vacancyid = $vacancyid;
            $record->name = $criterion['name'];
            $record->description = $criterion['description'] ?? '';
            $record->weight = $criterion['weight'] ?? 1;
            $record->maxscore = $criterion['maxscore'] ?? 10;
            $record->sortorder = $sortorder++;
            $record->timecreated = time();

            $DB->insert_record('local_jobboard_criteria', $record);
        }

        return true;
    }
}
