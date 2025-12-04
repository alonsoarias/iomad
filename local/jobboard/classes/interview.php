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
 * Interview scheduling and management class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class interview {

    /** @var string Interview status - scheduled. */
    const STATUS_SCHEDULED = 'scheduled';

    /** @var string Interview status - confirmed by applicant. */
    const STATUS_CONFIRMED = 'confirmed';

    /** @var string Interview status - completed. */
    const STATUS_COMPLETED = 'completed';

    /** @var string Interview status - cancelled. */
    const STATUS_CANCELLED = 'cancelled';

    /** @var string Interview status - no show. */
    const STATUS_NOSHOW = 'noshow';

    /** @var string Interview status - rescheduled. */
    const STATUS_RESCHEDULED = 'rescheduled';

    /** @var string Interview type - in person. */
    const TYPE_INPERSON = 'inperson';

    /** @var string Interview type - video call. */
    const TYPE_VIDEO = 'video';

    /** @var string Interview type - phone. */
    const TYPE_PHONE = 'phone';

    /**
     * Schedule a new interview.
     *
     * @param int $applicationid Application ID.
     * @param int $scheduledtime Scheduled datetime (timestamp).
     * @param int $duration Duration in minutes.
     * @param string $type Interview type (inperson, video, phone).
     * @param string $location Location or meeting URL.
     * @param array $interviewers Array of interviewer user IDs.
     * @param string $notes Additional notes.
     * @return int|bool Interview ID on success, false on failure.
     */
    public static function schedule(
        int $applicationid,
        int $scheduledtime,
        int $duration,
        string $type,
        string $location,
        array $interviewers,
        string $notes = ''
    ) {
        global $DB, $USER;

        // Validate application exists and is in correct status.
        $application = $DB->get_record('local_jobboard_application', ['id' => $applicationid]);
        if (!$application) {
            return false;
        }

        // Application should be at docs_validated or later (but not rejected/withdrawn).
        $validstatuses = ['docs_validated', 'interview'];
        if (!in_array($application->status, $validstatuses)) {
            return false;
        }

        // Validate type.
        if (!in_array($type, [self::TYPE_INPERSON, self::TYPE_VIDEO, self::TYPE_PHONE])) {
            return false;
        }

        // Check for scheduling conflicts.
        foreach ($interviewers as $interviewerid) {
            if (self::has_conflict($interviewerid, $scheduledtime, $duration)) {
                return false;
            }
        }

        // Create interview record.
        $interview = new \stdClass();
        $interview->applicationid = $applicationid;
        $interview->scheduledtime = $scheduledtime;
        $interview->duration = $duration;
        $interview->interviewtype = $type;
        $interview->location = $location;
        $interview->status = self::STATUS_SCHEDULED;
        $interview->notes = $notes;
        $interview->createdby = $USER->id;
        $interview->timecreated = time();

        $interview->id = $DB->insert_record('local_jobboard_interview', $interview);

        // Add interviewers.
        foreach ($interviewers as $interviewerid) {
            $interviewer = new \stdClass();
            $interviewer->interviewid = $interview->id;
            $interviewer->userid = $interviewerid;
            $interviewer->timecreated = time();
            $DB->insert_record('local_jobboard_interviewer', $interviewer);
        }

        // Update application status.
        application::update_status($applicationid, 'interview',
            get_string('interviewscheduled', 'local_jobboard'));

        // Trigger event.
        \local_jobboard\event\interview_scheduled::create([
            'context' => \context_system::instance(),
            'objectid' => $interview->id,
            'relateduserid' => $application->userid,
            'other' => [
                'applicationid' => $applicationid,
                'scheduledtime' => $scheduledtime,
            ],
        ])->trigger();

        // Queue notification.
        self::queue_notification($interview->id, 'interview_scheduled');

        return $interview->id;
    }

    /**
     * Check if an interviewer has a scheduling conflict.
     *
     * @param int $userid Interviewer user ID.
     * @param int $scheduledtime Proposed start time.
     * @param int $duration Duration in minutes.
     * @param int $excludeinterviewid Exclude this interview from check (for rescheduling).
     * @return bool True if conflict exists.
     */
    public static function has_conflict(
        int $userid,
        int $scheduledtime,
        int $duration,
        int $excludeinterviewid = 0
    ): bool {
        global $DB;

        $endtime = $scheduledtime + ($duration * 60);

        // Find overlapping interviews.
        $sql = "SELECT i.id
                  FROM {local_jobboard_interview} i
                  JOIN {local_jobboard_interviewer} iv ON iv.interviewid = i.id
                 WHERE iv.userid = :userid
                   AND i.status NOT IN ('cancelled', 'rescheduled')
                   AND i.id != :excludeid
                   AND (
                       (i.scheduledtime <= :start1 AND (i.scheduledtime + i.duration * 60) > :start2)
                       OR (i.scheduledtime < :end1 AND (i.scheduledtime + i.duration * 60) >= :end2)
                       OR (i.scheduledtime >= :start3 AND (i.scheduledtime + i.duration * 60) <= :end3)
                   )";

        $params = [
            'userid' => $userid,
            'excludeid' => $excludeinterviewid,
            'start1' => $scheduledtime,
            'start2' => $scheduledtime,
            'end1' => $endtime,
            'end2' => $endtime,
            'start3' => $scheduledtime,
            'end3' => $endtime,
        ];

        return $DB->record_exists_sql($sql, $params);
    }

    /**
     * Reschedule an interview.
     *
     * @param int $interviewid Interview ID.
     * @param int $newtime New scheduled time.
     * @param int $newduration New duration (optional, 0 to keep current).
     * @param string $reason Reason for rescheduling.
     * @return int|bool New interview ID on success, false on failure.
     */
    public static function reschedule(
        int $interviewid,
        int $newtime,
        int $newduration = 0,
        string $reason = ''
    ) {
        global $DB, $USER;

        $interview = $DB->get_record('local_jobboard_interview', ['id' => $interviewid]);
        if (!$interview) {
            return false;
        }

        // Cannot reschedule completed or already rescheduled interviews.
        if (in_array($interview->status, [self::STATUS_COMPLETED, self::STATUS_RESCHEDULED])) {
            return false;
        }

        // Get interviewers.
        $interviewers = $DB->get_records('local_jobboard_interviewer', ['interviewid' => $interviewid]);
        $interviewerids = array_column($interviewers, 'userid');

        $duration = $newduration ?: $interview->duration;

        // Check for conflicts.
        foreach ($interviewerids as $interviewerid) {
            if (self::has_conflict($interviewerid, $newtime, $duration, $interviewid)) {
                return false;
            }
        }

        // Mark old interview as rescheduled.
        $interview->status = self::STATUS_RESCHEDULED;
        $interview->notes = $interview->notes . "\n\n" .
            get_string('rescheduledby', 'local_jobboard', [
                'user' => fullname($DB->get_record('user', ['id' => $USER->id])),
                'time' => userdate(time()),
                'reason' => $reason,
            ]);
        $interview->timemodified = time();
        $DB->update_record('local_jobboard_interview', $interview);

        // Create new interview.
        $newinterviewid = self::schedule(
            $interview->applicationid,
            $newtime,
            $duration,
            $interview->interviewtype,
            $interview->location,
            $interviewerids,
            get_string('reschedulednote', 'local_jobboard', $interviewid)
        );

        if ($newinterviewid) {
            // Queue notification.
            self::queue_notification($newinterviewid, 'interview_rescheduled');
        }

        return $newinterviewid;
    }

    /**
     * Cancel an interview.
     *
     * @param int $interviewid Interview ID.
     * @param string $reason Cancellation reason.
     * @return bool Success.
     */
    public static function cancel(int $interviewid, string $reason = ''): bool {
        global $DB, $USER;

        $interview = $DB->get_record('local_jobboard_interview', ['id' => $interviewid]);
        if (!$interview) {
            return false;
        }

        // Cannot cancel completed interviews.
        if ($interview->status === self::STATUS_COMPLETED) {
            return false;
        }

        $interview->status = self::STATUS_CANCELLED;
        $interview->notes = $interview->notes . "\n\n" .
            get_string('cancelledby', 'local_jobboard', [
                'user' => fullname($DB->get_record('user', ['id' => $USER->id])),
                'time' => userdate(time()),
                'reason' => $reason,
            ]);
        $interview->timemodified = time();

        $DB->update_record('local_jobboard_interview', $interview);

        // Trigger event.
        \local_jobboard\event\interview_cancelled::create([
            'context' => \context_system::instance(),
            'objectid' => $interviewid,
            'other' => [
                'applicationid' => $interview->applicationid,
                'reason' => $reason,
            ],
        ])->trigger();

        // Queue notification.
        self::queue_notification($interviewid, 'interview_cancelled');

        return true;
    }

    /**
     * Confirm interview attendance (by applicant).
     *
     * @param int $interviewid Interview ID.
     * @return bool Success.
     */
    public static function confirm(int $interviewid): bool {
        global $DB, $USER;

        $interview = $DB->get_record('local_jobboard_interview', ['id' => $interviewid]);
        if (!$interview) {
            return false;
        }

        // Check user is the applicant.
        $application = $DB->get_record('local_jobboard_application', ['id' => $interview->applicationid]);
        if (!$application || $application->userid != $USER->id) {
            return false;
        }

        if ($interview->status !== self::STATUS_SCHEDULED) {
            return false;
        }

        $interview->status = self::STATUS_CONFIRMED;
        $interview->timemodified = time();
        $DB->update_record('local_jobboard_interview', $interview);

        return true;
    }

    /**
     * Complete an interview with results.
     *
     * @param int $interviewid Interview ID.
     * @param int $rating Overall rating (1-5).
     * @param string $feedback Interview feedback/notes.
     * @param string $recommendation Recommendation (hire, reject, further_review).
     * @return bool Success.
     */
    public static function complete(
        int $interviewid,
        int $rating,
        string $feedback,
        string $recommendation
    ): bool {
        global $DB, $USER;

        $interview = $DB->get_record('local_jobboard_interview', ['id' => $interviewid]);
        if (!$interview) {
            return false;
        }

        if (!in_array($interview->status, [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED])) {
            return false;
        }

        // Validate rating.
        if ($rating < 1 || $rating > 5) {
            return false;
        }

        // Validate recommendation.
        if (!in_array($recommendation, ['hire', 'reject', 'further_review'])) {
            return false;
        }

        $interview->status = self::STATUS_COMPLETED;
        $interview->rating = $rating;
        $interview->feedback = $feedback;
        $interview->recommendation = $recommendation;
        $interview->completedby = $USER->id;
        $interview->timecompleted = time();
        $interview->timemodified = time();

        $DB->update_record('local_jobboard_interview', $interview);

        // Trigger event.
        \local_jobboard\event\interview_completed::create([
            'context' => \context_system::instance(),
            'objectid' => $interviewid,
            'other' => [
                'applicationid' => $interview->applicationid,
                'rating' => $rating,
                'recommendation' => $recommendation,
            ],
        ])->trigger();

        return true;
    }

    /**
     * Mark interview as no-show.
     *
     * @param int $interviewid Interview ID.
     * @param string $notes Additional notes.
     * @return bool Success.
     */
    public static function mark_noshow(int $interviewid, string $notes = ''): bool {
        global $DB, $USER;

        $interview = $DB->get_record('local_jobboard_interview', ['id' => $interviewid]);
        if (!$interview) {
            return false;
        }

        $interview->status = self::STATUS_NOSHOW;
        $interview->notes = $interview->notes . "\n\n" .
            get_string('markednoshow', 'local_jobboard', [
                'user' => fullname($DB->get_record('user', ['id' => $USER->id])),
                'time' => userdate(time()),
                'notes' => $notes,
            ]);
        $interview->timemodified = time();

        $DB->update_record('local_jobboard_interview', $interview);

        return true;
    }

    /**
     * Get interviews for an application.
     *
     * @param int $applicationid Application ID.
     * @return array Array of interview records.
     */
    public static function get_for_application(int $applicationid): array {
        global $DB;

        return $DB->get_records('local_jobboard_interview',
            ['applicationid' => $applicationid],
            'scheduledtime DESC'
        );
    }

    /**
     * Get upcoming interviews for an interviewer.
     *
     * @param int $userid Interviewer user ID.
     * @param int $daysahead Days to look ahead (default 7).
     * @return array Array of interview records with application/user info.
     */
    public static function get_upcoming_for_interviewer(int $userid, int $daysahead = 7): array {
        global $DB;

        $now = time();
        $until = $now + ($daysahead * 86400);

        $sql = "SELECT i.*, a.userid as applicantuserid, v.title as vacancytitle, v.code as vacancycode,
                       u.firstname, u.lastname, u.email
                  FROM {local_jobboard_interview} i
                  JOIN {local_jobboard_interviewer} iv ON iv.interviewid = i.id
                  JOIN {local_jobboard_application} a ON a.id = i.applicationid
                  JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                  JOIN {user} u ON u.id = a.userid
                 WHERE iv.userid = :userid
                   AND i.status IN ('scheduled', 'confirmed')
                   AND i.scheduledtime >= :now
                   AND i.scheduledtime <= :until
                 ORDER BY i.scheduledtime ASC";

        return $DB->get_records_sql($sql, [
            'userid' => $userid,
            'now' => $now,
            'until' => $until,
        ]);
    }

    /**
     * Get upcoming interviews for an applicant.
     *
     * @param int $userid Applicant user ID.
     * @return array Array of interview records.
     */
    public static function get_upcoming_for_applicant(int $userid): array {
        global $DB;

        $sql = "SELECT i.*, v.title as vacancytitle, v.code as vacancycode
                  FROM {local_jobboard_interview} i
                  JOIN {local_jobboard_application} a ON a.id = i.applicationid
                  JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                 WHERE a.userid = :userid
                   AND i.status IN ('scheduled', 'confirmed')
                   AND i.scheduledtime >= :now
                 ORDER BY i.scheduledtime ASC";

        return $DB->get_records_sql($sql, [
            'userid' => $userid,
            'now' => time(),
        ]);
    }

    /**
     * Get interview details with interviewers.
     *
     * @param int $interviewid Interview ID.
     * @return object|bool Interview object with interviewers, or false.
     */
    public static function get_details(int $interviewid) {
        global $DB;

        $interview = $DB->get_record('local_jobboard_interview', ['id' => $interviewid]);
        if (!$interview) {
            return false;
        }

        // Get interviewers.
        $sql = "SELECT iv.id, iv.userid, u.firstname, u.lastname, u.email
                  FROM {local_jobboard_interviewer} iv
                  JOIN {user} u ON u.id = iv.userid
                 WHERE iv.interviewid = :interviewid";
        $interview->interviewers = $DB->get_records_sql($sql, ['interviewid' => $interviewid]);

        // Get application and vacancy info.
        $application = $DB->get_record('local_jobboard_application', ['id' => $interview->applicationid]);
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        $applicant = $DB->get_record('user', ['id' => $application->userid]);

        $interview->application = $application;
        $interview->vacancy = $vacancy;
        $interview->applicant = $applicant;

        return $interview;
    }

    /**
     * Get interview schedule for a vacancy.
     *
     * @param int $vacancyid Vacancy ID.
     * @param int $fromtime Start of date range (optional).
     * @param int $totime End of date range (optional).
     * @return array Array of interview records.
     */
    public static function get_vacancy_schedule(int $vacancyid, int $fromtime = 0, int $totime = 0): array {
        global $DB;

        $params = ['vacancyid' => $vacancyid];
        $whereclauses = ['v.id = :vacancyid'];

        if ($fromtime) {
            $whereclauses[] = 'i.scheduledtime >= :fromtime';
            $params['fromtime'] = $fromtime;
        }

        if ($totime) {
            $whereclauses[] = 'i.scheduledtime <= :totime';
            $params['totime'] = $totime;
        }

        $whereclause = implode(' AND ', $whereclauses);

        $sql = "SELECT i.*, a.userid as applicantuserid, u.firstname, u.lastname
                  FROM {local_jobboard_interview} i
                  JOIN {local_jobboard_application} a ON a.id = i.applicationid
                  JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                  JOIN {user} u ON u.id = a.userid
                 WHERE $whereclause
                 ORDER BY i.scheduledtime ASC";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Queue notification for interview.
     *
     * @param int $interviewid Interview ID.
     * @param string $type Notification type.
     */
    protected static function queue_notification(int $interviewid, string $type): void {
        global $DB;

        $interview = self::get_details($interviewid);
        if (!$interview) {
            return;
        }

        // Build notification data.
        $data = [
            'interview_id' => $interviewid,
            'vacancy_title' => $interview->vacancy->title,
            'vacancy_code' => $interview->vacancy->code,
            'interview_date' => userdate($interview->scheduledtime, get_string('strftimedatetime', 'langconfig')),
            'interview_type' => get_string('interviewtype_' . $interview->interviewtype, 'local_jobboard'),
            'location' => $interview->location,
            'duration' => $interview->duration,
        ];

        // Queue notification for applicant.
        notification::queue(
            $interview->applicant->id,
            $type,
            $data,
            $interview->applicationid
        );
    }

    /**
     * Get interview statistics for a vacancy.
     *
     * @param int $vacancyid Vacancy ID.
     * @return array Statistics array.
     */
    public static function get_vacancy_stats(int $vacancyid): array {
        global $DB;

        $stats = [
            'total' => 0,
            'scheduled' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'noshow' => 0,
            'avg_rating' => 0,
            'hire_recommendations' => 0,
        ];

        $sql = "SELECT i.status, COUNT(*) as cnt
                  FROM {local_jobboard_interview} i
                  JOIN {local_jobboard_application} a ON a.id = i.applicationid
                 WHERE a.vacancyid = :vacancyid
                 GROUP BY i.status";

        $statuscount = $DB->get_records_sql($sql, ['vacancyid' => $vacancyid]);

        foreach ($statuscount as $sc) {
            $stats['total'] += $sc->cnt;
            if (isset($stats[$sc->status])) {
                $stats[$sc->status] = (int)$sc->cnt;
            }
        }

        // Average rating from completed interviews.
        $avgrating = $DB->get_field_sql("
            SELECT AVG(i.rating)
              FROM {local_jobboard_interview} i
              JOIN {local_jobboard_application} a ON a.id = i.applicationid
             WHERE a.vacancyid = :vacancyid
               AND i.status = 'completed'
               AND i.rating IS NOT NULL
        ", ['vacancyid' => $vacancyid]);

        $stats['avg_rating'] = $avgrating ? round($avgrating, 1) : 0;

        // Hire recommendations.
        $stats['hire_recommendations'] = $DB->count_records_sql("
            SELECT COUNT(*)
              FROM {local_jobboard_interview} i
              JOIN {local_jobboard_application} a ON a.id = i.applicationid
             WHERE a.vacancyid = :vacancyid
               AND i.status = 'completed'
               AND i.recommendation = 'hire'
        ", ['vacancyid' => $vacancyid]);

        return $stats;
    }
}
