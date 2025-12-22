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

/**
 * Application class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\trait\request_helper;
use local_jobboard\helper\date_helper;

/**
 * Class representing a job application.
 */
class application {

    use request_helper;

    /** @var int The application ID. */
    public $id = 0;

    /** @var int The vacancy ID. */
    public $vacancyid = 0;

    /** @var int The applicant user ID. */
    public $userid = 0;

    /** @var string The application status. */
    public $status = 'submitted';

    /** @var int ISER exemption marker. */
    public $isexemption = 0;

    /** @var string Exemption reason. */
    public $exemptionreason = '';

    /** @var string Status notes. */
    public $statusnotes = '';

    /** @var int Consent given flag. */
    public $consentgiven = 0;

    /** @var int Consent timestamp. */
    public $consenttimestamp = 0;

    /** @var string IP at consent time. */
    public $consentip = '';

    /** @var string User agent at consent time. */
    public $consentuseragent = '';

    /** @var string Digital signature (full name). */
    public $digitalsignature = '';

    /** @var string Cover letter / motivation. */
    public $coverletter = '';

    /** @var string Additional application data (JSON). */
    public $applicationdata = '';

    /** @var int Assigned reviewer user ID. */
    public $reviewerid = null;

    /** @var int Creation timestamp. */
    public $timecreated = 0;

    /** @var int Last modification timestamp. */
    public $timemodified = null;

    /** @var \stdClass|null The raw database record. */
    protected $record = null;

    /** @var vacancy|null Cached vacancy object. */
    protected $vacancy = null;

    /** @var array Allowed status values. */
    public const STATUSES = [
        'draft',
        'submitted',
        'under_review',
        'preselected',           // Internal status - applicant sees 'under_review'.
        'pending_validation',    // Ready for Talento Humano to validate documents.
        'docs_validated',
        'docs_rejected',
        'interview',
        'selected',
        'rejected',
        'withdrawn',
    ];

    /** @var array Internal statuses not visible to applicants. */
    public const INTERNAL_STATUSES = [
        'preselected',
        'pending_validation',
    ];

    /** @var array Status mapping for applicant view (internal -> public). */
    public const STATUS_PUBLIC_MAP = [
        'preselected' => 'under_review',
        'pending_validation' => 'under_review',
    ];

    /** @var array Allowed status transitions. */
    public const TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => ['under_review', 'rejected'],
        'under_review' => ['preselected', 'rejected'],              // Decano preselects or rejects.
        'preselected' => ['pending_validation', 'rejected'],        // Internal -> HR validation.
        'pending_validation' => ['docs_validated', 'docs_rejected'], // HR validates documents.
        'docs_rejected' => ['pending_validation'],                  // Re-submit for validation.
        'docs_validated' => ['interview', 'rejected'],
        'interview' => ['selected', 'rejected'],
    ];

    /**
     * Constructor.
     *
     * @param int|\stdClass|null $idorrecord Application ID, database record, or null.
     */
    public function __construct($idorrecord = null) {
        if ($idorrecord === null) {
            return;
        }

        if (is_object($idorrecord)) {
            $this->load_from_record($idorrecord);
        } else {
            $this->load((int) $idorrecord);
        }
    }

    /**
     * Load application from ID.
     *
     * @param int $id The application ID.
     * @throws \dml_exception If not found.
     */
    public function load(int $id): void {
        global $DB;

        $record = $DB->get_record('local_jobboard_application', ['id' => $id], '*', MUST_EXIST);
        $this->load_from_record($record);
    }

    /**
     * Load application from database record.
     *
     * @param \stdClass $record The database record.
     */
    public function load_from_record(\stdClass $record): void {
        $this->record = $record;
        $this->id = (int) $record->id;
        $this->vacancyid = (int) $record->vacancyid;
        $this->userid = (int) $record->userid;
        $this->status = $record->status;
        $this->statusnotes = $record->statusnotes ?? '';
        $this->isexemption = (int) ($record->isexemption ?? 0);
        $this->exemptionreason = $record->exemptionreason ?? '';
        $this->consentgiven = (int) ($record->consentgiven ?? 0);
        $this->consenttimestamp = (int) ($record->consenttimestamp ?? 0);
        $this->consentip = $record->consentip ?? '';
        $this->consentuseragent = $record->consentuseragent ?? '';
        $this->digitalsignature = $record->digitalsignature ?? '';
        $this->coverletter = $record->coverletter ?? '';
        $this->applicationdata = $record->applicationdata ?? '';
        $this->reviewerid = $record->reviewerid ? (int) $record->reviewerid : null;
        $this->timecreated = (int) $record->timecreated;
        $this->timemodified = $record->timemodified ? (int) $record->timemodified : null;
    }

    /**
     * Get an application by ID.
     *
     * @param int $id The application ID.
     * @return self|null The application or null if not found.
     */
    public static function get(int $id): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_application', ['id' => $id]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Get application by vacancy and user.
     *
     * @param int $vacancyid The vacancy ID.
     * @param int $userid The user ID.
     * @return self|null The application or null.
     */
    public static function get_by_vacancy_user(int $vacancyid, int $userid): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_application', [
            'vacancyid' => $vacancyid,
            'userid' => $userid,
        ]);

        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Check if user already applied to vacancy.
     *
     * @param int $vacancyid The vacancy ID.
     * @param int $userid The user ID.
     * @param bool $excludedrafts If true, exclude draft applications from check.
     * @return bool True if already applied.
     */
    public static function user_has_applied(int $vacancyid, int $userid, bool $excludedrafts = false): bool {
        global $DB;

        if ($excludedrafts) {
            return $DB->record_exists_select(
                'local_jobboard_application',
                'vacancyid = :vacancyid AND userid = :userid AND status != :status',
                ['vacancyid' => $vacancyid, 'userid' => $userid, 'status' => 'draft']
            );
        }

        return $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancyid,
            'userid' => $userid,
        ]);
    }

    /**
     * Check if user has a submitted (non-draft) application to vacancy.
     *
     * @param int $vacancyid The vacancy ID.
     * @param int $userid The user ID.
     * @return bool True if has submitted application.
     */
    public static function user_has_submitted_application(int $vacancyid, int $userid): bool {
        return self::user_has_applied($vacancyid, $userid, true);
    }

    /**
     * Get user's draft application for a vacancy.
     *
     * @param int $vacancyid The vacancy ID.
     * @param int $userid The user ID.
     * @return self|null The draft application or null.
     */
    public static function get_draft(int $vacancyid, int $userid): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_application', [
            'vacancyid' => $vacancyid,
            'userid' => $userid,
            'status' => 'draft',
        ]);

        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Check if this application is a draft.
     *
     * @return bool True if draft.
     */
    public function is_draft(): bool {
        return $this->status === 'draft';
    }

    /**
     * Create a new application.
     *
     * @param \stdClass $data The application data.
     * @param bool $isdraft Whether this is a draft (partial save).
     * @return self The created application.
     * @throws \moodle_exception If validation fails.
     */
    public static function create($data, bool $isdraft = false): self {
        global $DB, $USER;

        // Accept both array and stdClass.
        if (is_array($data)) {
            $data = (object) $data;
        }

        $application = new self();
        $application->vacancyid = (int) $data->vacancyid;
        $application->userid = (int) ($data->userid ?? $USER->id);
        $application->status = $isdraft ? 'draft' : 'submitted';
        $application->timecreated = time();

        // Set consent data.
        if (!empty($data->consentgiven)) {
            $application->consentgiven = 1;
            $application->consenttimestamp = $data->consenttimestamp ?? time();
            $application->consentip = $data->consentip ?? self::get_user_ip();
            $application->consentuseragent = self::get_user_agent();
        }

        // Set digital signature.
        if (!empty($data->digitalsignature)) {
            $application->digitalsignature = $data->digitalsignature;
        }

        // Set cover letter.
        if (!empty($data->coverletter)) {
            $application->coverletter = $data->coverletter;
        }

        // Set ISER exemption if applicable.
        if (!empty($data->isexemption)) {
            $application->isexemption = 1;
            $application->exemptionreason = $data->exemptionreason ?? '';
        }

        // Set additional application data.
        if (!empty($data->applicationdata)) {
            if (is_array($data->applicationdata)) {
                $application->applicationdata = json_encode($data->applicationdata);
            } else {
                $application->applicationdata = $data->applicationdata;
            }
        }

        // Validate (skip consent check for drafts).
        $errors = $application->validate($isdraft);
        if (!empty($errors)) {
            throw new \moodle_exception('error:validation', 'local_jobboard', '', implode(', ', $errors));
        }

        // Insert.
        $record = $application->to_record();
        unset($record->id);
        $application->id = $DB->insert_record('local_jobboard_application', $record);

        // Log workflow.
        $statuskey = $isdraft ? 'draftsaved' : 'applicationsubmitted';
        $application->log_workflow_change(null, $application->status, get_string($statuskey, 'local_jobboard'));

        // Log audit with new values (no previous value for creation).
        $newstate = $application->to_record();
        audit::log(
            audit::ACTION_CREATE,
            audit::ENTITY_APPLICATION,
            $application->id,
            ['vacancyid' => $application->vacancyid, 'userid' => $application->userid, 'isdraft' => $isdraft],
            null,
            (array) $newstate
        );

        // Trigger event only for submitted applications.
        if (!$isdraft) {
            $event = \local_jobboard\event\application_created::create([
                'objectid' => $application->id,
                'context' => \context_system::instance(),
                'relateduserid' => $application->userid,
                'other' => ['vacancyid' => $application->vacancyid],
            ]);
            $event->trigger();

            // Queue confirmation notification to applicant.
            notification::queue_application_received($application);
        }

        return $application;
    }

    /**
     * Create or update a draft application.
     *
     * @param \stdClass $data The application data.
     * @return self The created/updated draft application.
     */
    public static function save_draft($data): self {
        global $DB, $USER;

        // Accept both array and stdClass.
        if (is_array($data)) {
            $data = (object) $data;
        }

        $vacancyid = (int) $data->vacancyid;
        $userid = (int) ($data->userid ?? $USER->id);

        // Check for existing draft.
        $existingdraft = self::get_draft($vacancyid, $userid);

        if ($existingdraft) {
            // Update existing draft.
            return $existingdraft->update_draft($data);
        } else {
            // Create new draft.
            return self::create($data, true);
        }
    }

    /**
     * Update an existing draft application.
     *
     * @param \stdClass $data The updated data.
     * @return self This application (updated).
     * @throws \moodle_exception If not a draft.
     */
    public function update_draft($data): self {
        global $DB;

        if (!$this->is_draft()) {
            throw new \moodle_exception('error:notadraft', 'local_jobboard');
        }

        // Accept both array and stdClass.
        if (is_array($data)) {
            $data = (object) $data;
        }

        // Update consent data if provided.
        if (!empty($data->consentgiven)) {
            $this->consentgiven = 1;
            $this->consenttimestamp = $data->consenttimestamp ?? time();
            $this->consentip = $data->consentip ?? self::get_user_ip();
            $this->consentuseragent = self::get_user_agent();
        }

        // Update digital signature if provided.
        if (isset($data->digitalsignature)) {
            $this->digitalsignature = $data->digitalsignature;
        }

        // Update cover letter if provided.
        if (isset($data->coverletter)) {
            $this->coverletter = $data->coverletter;
        }

        // Update ISER exemption if provided.
        if (isset($data->isexemption)) {
            $this->isexemption = $data->isexemption ? 1 : 0;
            $this->exemptionreason = $data->exemptionreason ?? '';
        }

        // Update additional application data.
        if (!empty($data->applicationdata)) {
            if (is_array($data->applicationdata)) {
                $this->applicationdata = json_encode($data->applicationdata);
            } else {
                $this->applicationdata = $data->applicationdata;
            }
        }

        $this->timemodified = time();

        // Update database.
        $DB->update_record('local_jobboard_application', $this->to_record());

        // Log audit.
        audit::log(
            audit::ACTION_UPDATE,
            audit::ENTITY_APPLICATION,
            $this->id,
            ['vacancyid' => $this->vacancyid, 'userid' => $this->userid, 'action' => 'draft_updated']
        );

        return $this;
    }

    /**
     * Submit a draft application.
     *
     * This transitions the application from draft to submitted status.
     *
     * @param \stdClass|null $data Optional final data to set before submitting.
     * @return self This application (submitted).
     * @throws \moodle_exception If not a draft or validation fails.
     */
    public function submit_draft($data = null): self {
        global $DB;

        if (!$this->is_draft()) {
            throw new \moodle_exception('error:notadraft', 'local_jobboard');
        }

        // Update with final data if provided.
        if ($data !== null) {
            if (is_array($data)) {
                $data = (object) $data;
            }

            // Set consent data (required for submission).
            if (!empty($data->consentgiven)) {
                $this->consentgiven = 1;
                $this->consenttimestamp = $data->consenttimestamp ?? time();
                $this->consentip = $data->consentip ?? self::get_user_ip();
                $this->consentuseragent = self::get_user_agent();
            }

            if (!empty($data->digitalsignature)) {
                $this->digitalsignature = $data->digitalsignature;
            }

            if (isset($data->coverletter)) {
                $this->coverletter = $data->coverletter;
            }
        }

        // Validate for submission (not as draft).
        $errors = $this->validate(false);
        if (!empty($errors)) {
            throw new \moodle_exception('error:validation', 'local_jobboard', '', implode(', ', $errors));
        }

        // Change status.
        $oldstatus = $this->status;
        $this->status = 'submitted';
        $this->timemodified = time();

        $DB->update_record('local_jobboard_application', $this->to_record());

        // Log workflow change.
        $this->log_workflow_change($oldstatus, 'submitted', get_string('applicationsubmitted', 'local_jobboard'));

        // Log audit.
        audit::log_transition(
            audit::ENTITY_APPLICATION,
            $this->id,
            'status',
            $oldstatus,
            'submitted',
            ['vacancyid' => $this->vacancyid, 'userid' => $this->userid]
        );

        // Trigger event.
        $event = \local_jobboard\event\application_created::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'relateduserid' => $this->userid,
            'other' => ['vacancyid' => $this->vacancyid],
        ]);
        $event->trigger();

        // Queue confirmation notification to applicant.
        notification::queue_application_received($this);

        return $this;
    }

    /**
     * Validate the application data.
     *
     * @param bool $isdraft If true, skip consent and signature validation (for drafts).
     * @return array Array of error messages.
     */
    public function validate(bool $isdraft = false): array {
        global $DB;

        $errors = [];

        // Check vacancy exists and is open.
        $vacancy = $this->get_vacancy();
        if (!$vacancy) {
            $errors[] = get_string('error:vacancynotfound', 'local_jobboard');
        } elseif (!$vacancy->is_open() && !$this->id) {
            $errors[] = get_string('error:vacancyclosed', 'local_jobboard');
        }

        // Check user hasn't already submitted a non-draft application (for new applications).
        if (!$this->id && self::user_has_submitted_application($this->vacancyid, $this->userid)) {
            $errors[] = get_string('error:alreadyapplied', 'local_jobboard');
        }

        // Check consent is provided (for submissions only, not drafts).
        if (!$isdraft && !$this->id && empty($this->consentgiven)) {
            $errors[] = get_string('error:consentrequired', 'local_jobboard');
        }

        // Validate status.
        if (!in_array($this->status, self::STATUSES)) {
            $errors[] = get_string('error:invalidstatus', 'local_jobboard');
        }

        return $errors;
    }

    /**
     * Change the application status.
     *
     * @param string $newstatus The new status.
     * @param string $comments Optional comments.
     * @param int|null $changedby User making the change (null for current user).
     * @throws \moodle_exception If transition not allowed.
     */
    public function change_status(string $newstatus, string $comments = '', ?int $changedby = null): void {
        global $DB, $USER;

        $changedby = $changedby ?? $USER->id;

        // Validate transition.
        if (!$this->can_transition_to($newstatus)) {
            throw new \moodle_exception('error:invalidtransition', 'local_jobboard');
        }

        $oldstatus = $this->status;
        $this->status = $newstatus;
        $this->timemodified = time();

        $DB->update_record('local_jobboard_application', (object) [
            'id' => $this->id,
            'status' => $this->status,
            'timemodified' => $this->timemodified,
        ]);

        // Log workflow change.
        $this->log_workflow_change($oldstatus, $newstatus, $comments, $changedby);

        // Log audit with proper transition tracking.
        audit::log_transition(
            audit::ENTITY_APPLICATION,
            $this->id,
            'status',
            $oldstatus,
            $newstatus,
            ['vacancyid' => $this->vacancyid, 'userid' => $this->userid]
        );

        // Trigger event.
        $event = \local_jobboard\event\application_status_changed::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'relateduserid' => $this->userid,
            'other' => [
                'vacancyid' => $this->vacancyid,
                'oldstatus' => $oldstatus,
                'newstatus' => $newstatus,
            ],
        ]);
        $event->trigger();

        // Queue notification.
        $this->queue_notification($newstatus);
    }

    /**
     * Check if transition to status is allowed.
     *
     * @param string $newstatus The target status.
     * @return bool True if transition allowed.
     */
    public function can_transition_to(string $newstatus): bool {
        if (!isset(self::TRANSITIONS[$this->status])) {
            return false;
        }

        return in_array($newstatus, self::TRANSITIONS[$this->status]);
    }

    /**
     * Withdraw the application.
     *
     * @param string $reason Optional reason.
     */
    public function withdraw(string $reason = ''): void {
        global $DB, $USER;

        // Can only withdraw if not already final.
        $finalstatuses = ['selected', 'rejected', 'withdrawn'];
        if (in_array($this->status, $finalstatuses)) {
            return;
        }

        $oldstatus = $this->status;
        $this->status = 'withdrawn';
        $this->timemodified = time();

        $DB->update_record('local_jobboard_application', (object) [
            'id' => $this->id,
            'status' => $this->status,
            'timemodified' => $this->timemodified,
        ]);

        // Log workflow change.
        $this->log_workflow_change($oldstatus, 'withdrawn', $reason);

        // Log audit with proper transition tracking.
        audit::log_transition(
            audit::ENTITY_APPLICATION,
            $this->id,
            'status',
            $oldstatus,
            'withdrawn',
            ['vacancyid' => $this->vacancyid, 'userid' => $this->userid, 'reason' => $reason]
        );
    }

    /**
     * Log workflow state change.
     *
     * @param string|null $oldstatus Previous status.
     * @param string $newstatus New status.
     * @param string $comments Comments.
     * @param int|null $changedby User who made the change.
     */
    protected function log_workflow_change(
        ?string $oldstatus,
        string $newstatus,
        string $comments = '',
        ?int $changedby = null
    ): void {
        global $DB, $USER;

        $record = new \stdClass();
        $record->applicationid = $this->id;
        $record->previousstatus = $oldstatus;
        $record->newstatus = $newstatus;
        $record->changedby = $changedby ?? $USER->id;
        $record->comments = $comments;
        $record->notificationsent = 0;
        $record->timecreated = time();

        $DB->insert_record('local_jobboard_workflow_log', $record);
    }

    /**
     * Queue notification for status change.
     *
     * @param string $status The new status.
     */
    protected function queue_notification(string $status): void {
        global $DB;

        // Get email template for this status.
        $template = $DB->get_record('local_jobboard_email_template', [
            'code' => $status,
            'enabled' => 1,
        ]);

        if (!$template) {
            return;
        }

        // Build notification.
        $vacancy = $this->get_vacancy();
        $user = \core_user::get_user($this->userid);

        $subject = $this->replace_placeholders($template->subject, $vacancy, $user);
        $body = $this->replace_placeholders($template->body, $vacancy, $user);

        $notification = new \stdClass();
        $notification->userid = $this->userid;
        $notification->notificationtype = $status;
        $notification->subject = $subject;
        $notification->body = $body;
        $notification->entitytype = 'application';
        $notification->entityid = $this->id;
        $notification->issent = 0;
        $notification->timecreated = time();

        $DB->insert_record('local_jobboard_notification', $notification);
    }

    /**
     * Replace placeholders in template.
     *
     * @param string $text The template text.
     * @param vacancy $vacancy The vacancy.
     * @param \stdClass $user The user.
     * @return string The processed text.
     */
    protected function replace_placeholders(string $text, vacancy $vacancy, \stdClass $user): string {
        global $CFG;

        $replacements = [
            '{USER_FULLNAME}' => fullname($user),
            '{USER_EMAIL}' => $user->email,
            '{VACANCY_CODE}' => $vacancy->code,
            '{VACANCY_TITLE}' => $vacancy->title,
            '{APPLICATION_DATE}' => date_helper::format_date($this->timecreated),
            '{CURRENT_STATUS}' => $this->get_status_display(),
            '{APPLICATION_URL}' => $CFG->wwwroot . '/local/jobboard/index.php?view=application&id=' . $this->id,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Get the vacancy object.
     *
     * @return vacancy|null The vacancy.
     */
    public function get_vacancy(): ?vacancy {
        if ($this->vacancy === null && $this->vacancyid) {
            $this->vacancy = vacancy::get($this->vacancyid);
        }
        return $this->vacancy;
    }

    /**
     * Get the applicant user.
     *
     * @return \stdClass|false The user record.
     */
    public function get_user() {
        return \core_user::get_user($this->userid);
    }

    /**
     * Get status display name.
     *
     * @param bool $forapplicant If true, show public-facing status (hide internal statuses).
     * @return string The localized status name.
     */
    public function get_status_display(bool $forapplicant = false): string {
        $status = $forapplicant ? $this->get_public_status() : $this->status;
        return get_string('appstatus:' . $status, 'local_jobboard');
    }

    /**
     * Check if current status is internal (not visible to applicant).
     *
     * @return bool True if status is internal.
     */
    public function is_internal_status(): bool {
        return in_array($this->status, self::INTERNAL_STATUSES);
    }

    /**
     * Get the public-facing status for the applicant.
     * Internal statuses are mapped to their public equivalents.
     *
     * @return string The public status.
     */
    public function get_public_status(): string {
        if (isset(self::STATUS_PUBLIC_MAP[$this->status])) {
            return self::STATUS_PUBLIC_MAP[$this->status];
        }
        return $this->status;
    }

    /**
     * Check if application is preselected (by Decano).
     *
     * @return bool True if preselected.
     */
    public function is_preselected(): bool {
        return $this->status === 'preselected';
    }

    /**
     * Check if application is pending HR validation.
     *
     * @return bool True if pending validation.
     */
    public function is_pending_validation(): bool {
        return $this->status === 'pending_validation';
    }

    /**
     * Preselect the application (Decano action).
     * Transitions from under_review to preselected.
     *
     * @param string $comments Optional comments.
     * @param int|null $changedby User making the change.
     */
    public function preselect(string $comments = '', ?int $changedby = null): void {
        $this->change_status('preselected', $comments, $changedby);
    }

    /**
     * Send to HR for validation (after preselection).
     * Transitions from preselected to pending_validation.
     *
     * @param string $comments Optional comments.
     * @param int|null $changedby User making the change.
     */
    public function send_to_hr_validation(string $comments = '', ?int $changedby = null): void {
        $this->change_status('pending_validation', $comments, $changedby);
    }

    /**
     * Get documents for this application.
     *
     * @param bool $includesuperseded Include superseded documents.
     * @return array Array of document objects.
     */
    public function get_documents(bool $includesuperseded = false): array {
        global $DB;

        $params = ['applicationid' => $this->id];
        $where = 'applicationid = :applicationid';

        if (!$includesuperseded) {
            $where .= ' AND issuperseded = 0';
        }

        $records = $DB->get_records_select('local_jobboard_document', $where, $params, 'documenttype ASC');

        $documents = [];
        foreach ($records as $record) {
            $documents[] = new document($record);
        }

        return $documents;
    }

    /**
     * Get document by type.
     *
     * @param string $type The document type code.
     * @return document|null The document or null.
     */
    public function get_document_by_type(string $type): ?document {
        global $DB;

        $record = $DB->get_record('local_jobboard_document', [
            'applicationid' => $this->id,
            'documenttype' => $type,
            'issuperseded' => 0,
        ]);

        if (!$record) {
            return null;
        }

        return new document($record);
    }

    /**
     * Get workflow history.
     *
     * @return array Array of workflow log records.
     */
    public function get_workflow_history(): array {
        global $DB;

        return $DB->get_records_sql(
            "SELECT wl.*, u.firstname, u.lastname
             FROM {local_jobboard_workflow_log} wl
             LEFT JOIN {user} u ON u.id = wl.changedby
             WHERE wl.applicationid = :applicationid
             ORDER BY wl.timecreated DESC",
            ['applicationid' => $this->id]
        );
    }

    /**
     * Get available status transitions from current status.
     *
     * @return array Array of available status codes.
     */
    public function get_available_transitions(): array {
        $transitions = helper\status_helper::get_allowed_transitions();
        $currentstatus = $this->status;

        if (!isset($transitions[$currentstatus])) {
            return [];
        }

        return $transitions[$currentstatus];
    }

    /**
     * Get additional application data.
     *
     * @return array Decoded application data.
     */
    public function get_application_data(): array {
        if (empty($this->applicationdata)) {
            return [];
        }

        $data = json_decode($this->applicationdata, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Convert to database record.
     *
     * @return \stdClass The database record.
     */
    public function to_record(): \stdClass {
        return (object) [
            'id' => $this->id ?: null,
            'vacancyid' => $this->vacancyid,
            'userid' => $this->userid,
            'status' => $this->status,
            'statusnotes' => $this->statusnotes,
            'isexemption' => $this->isexemption,
            'exemptionreason' => $this->exemptionreason,
            'consentgiven' => $this->consentgiven,
            'consenttimestamp' => $this->consenttimestamp,
            'consentip' => $this->consentip,
            'consentuseragent' => $this->consentuseragent,
            'digitalsignature' => $this->digitalsignature,
            'coverletter' => $this->coverletter,
            'applicationdata' => $this->applicationdata,
            'reviewerid' => $this->reviewerid,
            'timecreated' => $this->timecreated,
            'timemodified' => $this->timemodified,
        ];
    }

    /**
     * Get list of applications with filtering.
     *
     * @param array $filters Filter options.
     * @param string $sort Sort field.
     * @param string $order Sort order.
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array ['applications' => array, 'total' => int]
     */
    public static function get_list(
        array $filters = [],
        string $sort = 'timecreated',
        string $order = 'DESC',
        int $page = 0,
        int $perpage = 25
    ): array {
        global $DB;

        $params = [];
        $where = ['1=1'];

        if (!empty($filters['vacancyid'])) {
            $where[] = 'a.vacancyid = :vacancyid';
            $params['vacancyid'] = $filters['vacancyid'];
        }

        if (!empty($filters['userid'])) {
            $where[] = 'a.userid = :userid';
            $params['userid'] = $filters['userid'];
        }

        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                list($insql, $inparams) = $DB->get_in_or_equal($filters['status'], SQL_PARAMS_NAMED, 'status');
                $where[] = "a.status $insql";
                $params = array_merge($params, $inparams);
            } else {
                $where[] = 'a.status = :status';
                $params['status'] = $filters['status'];
            }
        }

        if (!empty($filters['reviewerid'])) {
            $where[] = 'a.reviewerid = :reviewerid';
            $params['reviewerid'] = $filters['reviewerid'];
        }

        if (!empty($filters['isexemption'])) {
            $where[] = 'a.isexemption = :isexemption';
            $params['isexemption'] = $filters['isexemption'];
        }

        if (!empty($filters['datefrom'])) {
            $where[] = 'a.timecreated >= :datefrom';
            $params['datefrom'] = $filters['datefrom'];
        }

        if (!empty($filters['dateto'])) {
            $where[] = 'a.timecreated <= :dateto';
            $params['dateto'] = $filters['dateto'];
        }

        // Filter by company (through vacancy).
        if (!empty($filters['companyid'])) {
            $where[] = 'v.companyid = :companyid';
            $params['companyid'] = $filters['companyid'];
        }

        $wheresql = implode(' AND ', $where);

        // Validate sort field.
        $allowedsorts = ['a.id', 'a.timecreated', 'a.status', 'a.userid', 'v.code'];
        if (!in_array('a.' . $sort, $allowedsorts) && !in_array($sort, $allowedsorts)) {
            $sort = 'a.timecreated';
        } else {
            $sort = strpos($sort, '.') !== false ? $sort : 'a.' . $sort;
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        // Get total count.
        $countsql = "SELECT COUNT(*)
                     FROM {local_jobboard_application} a
                     JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                     WHERE $wheresql";
        $total = $DB->count_records_sql($countsql, $params);

        // Get records.
        $sql = "SELECT a.*, v.code as vacancycode, v.title as vacancytitle,
                       u.firstname, u.lastname, u.email
                FROM {local_jobboard_application} a
                JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                JOIN {user} u ON u.id = a.userid
                WHERE $wheresql
                ORDER BY $sort $order";

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        $applications = [];
        foreach ($records as $record) {
            $app = new self();
            $app->load_from_record($record);
            // Attach extra data.
            $app->vacancycode = $record->vacancycode ?? '';
            $app->vacancytitle = $record->vacancytitle ?? '';
            $app->userfirstname = $record->firstname ?? '';
            $app->userlastname = $record->lastname ?? '';
            $app->useremail = $record->email ?? '';
            $applications[] = $app;
        }

        return [
            'applications' => $applications,
            'total' => $total,
        ];
    }

    /**
     * Delete an application and all related data.
     *
     * This performs a full deletion with audit logging.
     *
     * @param string $reason The reason for deletion (required for audit).
     * @return bool True on success.
     */
    public function delete(string $reason): bool {
        global $DB, $USER;

        if (!$this->id) {
            return false;
        }

        // Capture full state before deletion for audit.
        $previousstate = $this->to_record();
        $previousstate->reason = $reason;

        // Get associated documents.
        $documents = document::get_by_application($this->id, true);

        // Delete each document (this also handles files and validation records).
        foreach ($documents as $doc) {
            try {
                $doc->delete();
            } catch (\Exception $e) {
                debugging('Error deleting document ' . $doc->id . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        // Delete workflow history.
        $DB->delete_records('local_jobboard_workflow_history', ['applicationid' => $this->id]);

        // Delete the application record.
        $DB->delete_records('local_jobboard_application', ['id' => $this->id]);

        // Log comprehensive audit.
        audit::log(
            audit::ACTION_DELETE,
            audit::ENTITY_APPLICATION,
            $this->id,
            [
                'vacancyid' => $this->vacancyid,
                'userid' => $this->userid,
                'status' => $this->status,
                'reason' => $reason,
                'deletedby' => $USER->id,
            ],
            (array) $previousstate,
            null
        );

        // Trigger event.
        $event = \local_jobboard\event\application_deleted::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'userid' => $USER->id,
            'other' => [
                'vacancyid' => $this->vacancyid,
                'applicantuserid' => $this->userid,
                'reason' => $reason,
            ],
        ]);
        $event->trigger();

        return true;
    }
}
