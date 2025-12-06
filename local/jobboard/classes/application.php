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

    /** @var string Digital consent text. */
    public $consenttext = '';

    /** @var string IP at consent time. */
    public $consentip = '';

    /** @var string User agent at consent time. */
    public $consentuseragent = '';

    /** @var int Consent timestamp. */
    public $consenttime = 0;

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
        'submitted',
        'under_review',
        'docs_validated',
        'docs_rejected',
        'interview',
        'selected',
        'rejected',
        'withdrawn',
    ];

    /** @var array Allowed status transitions. */
    public const TRANSITIONS = [
        'submitted' => ['under_review', 'rejected'],
        'under_review' => ['docs_validated', 'docs_rejected'],
        'docs_rejected' => ['under_review'],
        'docs_validated' => ['interview', 'rejected'],
        'interview' => ['selected', 'rejected'],
    ];

    /**
     * Constructor.
     *
     * @param int|stdClass|null $idorrecord Application ID, database record, or null.
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
        $this->isexemption = (int) $record->isexemption;
        $this->exemptionreason = $record->exemptionreason ?? '';
        $this->consenttext = $record->consenttext ?? '';
        $this->consentip = $record->consentip ?? '';
        $this->consentuseragent = $record->consentuseragent ?? '';
        $this->consenttime = (int) ($record->consenttime ?? 0);
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
     * @return bool True if already applied.
     */
    public static function user_has_applied(int $vacancyid, int $userid): bool {
        global $DB;

        return $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancyid,
            'userid' => $userid,
        ]);
    }

    /**
     * Create a new application.
     *
     * @param \stdClass $data The application data.
     * @return self The created application.
     * @throws \moodle_exception If validation fails.
     */
    public static function create(\stdClass $data): self {
        global $DB, $USER;

        $application = new self();
        $application->vacancyid = (int) $data->vacancyid;
        $application->userid = (int) ($data->userid ?? $USER->id);
        $application->status = 'submitted';
        $application->timecreated = time();

        // Set consent data.
        if (!empty($data->consenttext)) {
            $application->consenttext = $data->consenttext;
            $application->consentip = self::get_user_ip();
            $application->consentuseragent = self::get_user_agent();
            $application->consenttime = time();
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

        // Validate.
        $errors = $application->validate();
        if (!empty($errors)) {
            throw new \moodle_exception('error:validation', 'local_jobboard', '', implode(', ', $errors));
        }

        // Insert.
        $record = $application->to_record();
        unset($record->id);
        $application->id = $DB->insert_record('local_jobboard_application', $record);

        // Log workflow.
        $application->log_workflow_change(null, 'submitted', get_string('applicationsubmitted', 'local_jobboard'));

        // Log audit.
        audit::log('application_created', 'application', $application->id);

        // Trigger event.
        $event = \local_jobboard\event\application_created::create([
            'objectid' => $application->id,
            'context' => \context_system::instance(),
            'relateduserid' => $application->userid,
            'other' => ['vacancyid' => $application->vacancyid],
        ]);
        $event->trigger();

        return $application;
    }

    /**
     * Validate the application data.
     *
     * @return array Array of error messages.
     */
    public function validate(): array {
        global $DB;

        $errors = [];

        // Check vacancy exists and is open.
        $vacancy = $this->get_vacancy();
        if (!$vacancy) {
            $errors[] = get_string('error:vacancynotfound', 'local_jobboard');
        } elseif (!$vacancy->is_open() && !$this->id) {
            $errors[] = get_string('error:vacancyclosed', 'local_jobboard');
        }

        // Check user hasn't already applied (for new applications).
        if (!$this->id && self::user_has_applied($this->vacancyid, $this->userid)) {
            $errors[] = get_string('error:alreadyapplied', 'local_jobboard');
        }

        // Check consent is provided (for new applications).
        if (!$this->id && empty($this->consenttext)) {
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

        // Log audit.
        audit::log('application_status_changed', 'application', $this->id, [
            'old_status' => $oldstatus,
            'new_status' => $newstatus,
        ]);

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

        // Log audit.
        audit::log('application_withdrawn', 'application', $this->id);
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
            '{APPLICATION_DATE}' => local_jobboard_format_date($this->timecreated),
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
     * @return string The localized status name.
     */
    public function get_status_display(): string {
        return get_string('appstatus:' . $this->status, 'local_jobboard');
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
            'isexemption' => $this->isexemption,
            'exemptionreason' => $this->exemptionreason,
            'consenttext' => $this->consenttext,
            'consentip' => $this->consentip,
            'consentuseragent' => $this->consentuseragent,
            'consenttime' => $this->consenttime,
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
}
