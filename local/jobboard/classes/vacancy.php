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
 * Vacancy class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing a job vacancy.
 */
class vacancy {

    /** @var int The vacancy ID. */
    public $id = 0;

    /** @var string The unique vacancy code. */
    public $code = '';

    /** @var string The vacancy title. */
    public $title = '';

    /** @var string The vacancy description. */
    public $description = '';

    /** @var string The contract type. */
    public $contracttype = '';

    /** @var string The contract duration. */
    public $duration = '';

    /** @var string The salary information. */
    public $salary = '';

    /** @var string The work location. */
    public $location = '';

    /** @var string The department. */
    public $department = '';

    /** @var int|null The associated course ID. */
    public $courseid = null;

    /** @var int|null The associated category ID. */
    public $categoryid = null;

    /** @var int|null The Iomad company ID. */
    public $companyid = null;

    /** @var int The opening date timestamp. */
    public $opendate = 0;

    /** @var int The closing date timestamp. */
    public $closedate = 0;

    /** @var int The number of available positions. */
    public $positions = 1;

    /** @var string The minimum requirements. */
    public $requirements = '';

    /** @var string The desirable requirements. */
    public $desirable = '';

    /** @var string The vacancy status. */
    public $status = 'draft';

    /** @var string The publication type (public or internal). */
    public $publicationtype = 'internal';

    /** @var int The user who created the vacancy. */
    public $createdby = 0;

    /** @var int|null The user who last modified the vacancy. */
    public $modifiedby = null;

    /** @var int The creation timestamp. */
    public $timecreated = 0;

    /** @var int|null The last modification timestamp. */
    public $timemodified = null;

    /** @var \stdClass|null The raw database record. */
    protected $record = null;

    /** @var array Allowed status values. */
    public const STATUSES = ['draft', 'published', 'closed', 'assigned'];

    /** @var array Allowed publication types. */
    public const PUBLICATION_TYPES = ['public', 'internal'];

    /**
     * Constructor.
     *
     * @param int|stdClass|null $idorrecord Vacancy ID, database record, or null.
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
     * Load vacancy from ID.
     *
     * @param int $id The vacancy ID.
     * @throws \dml_exception If not found.
     */
    public function load(int $id): void {
        global $DB;

        $record = $DB->get_record('local_jobboard_vacancy', ['id' => $id], '*', MUST_EXIST);
        $this->load_from_record($record);
    }

    /**
     * Load vacancy from database record.
     *
     * @param \stdClass $record The database record.
     */
    public function load_from_record(\stdClass $record): void {
        $this->record = $record;
        $this->id = (int) $record->id;
        $this->code = $record->code;
        $this->title = $record->title;
        $this->description = $record->description ?? '';
        $this->contracttype = $record->contracttype ?? '';
        $this->duration = $record->duration ?? '';
        $this->salary = $record->salary ?? '';
        $this->location = $record->location ?? '';
        $this->department = $record->department ?? '';
        $this->courseid = $record->courseid ? (int) $record->courseid : null;
        $this->categoryid = $record->categoryid ? (int) $record->categoryid : null;
        $this->companyid = $record->companyid ? (int) $record->companyid : null;
        $this->opendate = (int) $record->opendate;
        $this->closedate = (int) $record->closedate;
        $this->positions = (int) $record->positions;
        $this->requirements = $record->requirements ?? '';
        $this->desirable = $record->desirable ?? '';
        $this->status = $record->status;
        $this->publicationtype = $record->publicationtype ?? 'internal';
        $this->createdby = (int) $record->createdby;
        $this->modifiedby = $record->modifiedby ? (int) $record->modifiedby : null;
        $this->timecreated = (int) $record->timecreated;
        $this->timemodified = $record->timemodified ? (int) $record->timemodified : null;
    }

    /**
     * Get a vacancy by ID.
     *
     * @param int $id The vacancy ID.
     * @return self|null The vacancy or null if not found.
     */
    public static function get(int $id): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_vacancy', ['id' => $id]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Get a vacancy by code.
     *
     * @param string $code The vacancy code.
     * @return self|null The vacancy or null if not found.
     */
    public static function get_by_code(string $code): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_vacancy', ['code' => $code]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Check if a vacancy code already exists.
     *
     * @param string $code The code to check.
     * @param int $excludeid Vacancy ID to exclude (for updates).
     * @return bool True if code exists.
     */
    public static function code_exists(string $code, int $excludeid = 0): bool {
        global $DB;

        $params = ['code' => $code];
        $sql = "SELECT id FROM {local_jobboard_vacancy} WHERE code = :code";

        if ($excludeid) {
            $sql .= " AND id != :excludeid";
            $params['excludeid'] = $excludeid;
        }

        return $DB->record_exists_sql($sql, $params);
    }

    /**
     * Create a new vacancy.
     *
     * @param \stdClass $data The vacancy data.
     * @return self The created vacancy.
     * @throws \moodle_exception If validation fails.
     */
    public static function create(\stdClass $data): self {
        global $DB, $USER;

        $vacancy = new self();
        $vacancy->set_from_data($data);

        // Set defaults.
        $vacancy->createdby = $USER->id;
        $vacancy->timecreated = time();
        $vacancy->status = 'draft';

        // Validate.
        $errors = $vacancy->validate();
        if (!empty($errors)) {
            throw new \moodle_exception('error:validation', 'local_jobboard', '', implode(', ', $errors));
        }

        // Insert.
        $record = $vacancy->to_record();
        unset($record->id);
        $vacancy->id = $DB->insert_record('local_jobboard_vacancy', $record);

        // Log audit.
        audit::log('vacancy_created', 'vacancy', $vacancy->id);

        // Trigger event.
        $event = \local_jobboard\event\vacancy_created::create([
            'objectid' => $vacancy->id,
            'context' => \context_system::instance(),
            'other' => ['code' => $vacancy->code, 'title' => $vacancy->title],
        ]);
        $event->trigger();

        return $vacancy;
    }

    /**
     * Update the vacancy.
     *
     * @param \stdClass $data The updated data.
     * @throws \moodle_exception If validation fails or update not allowed.
     */
    public function update(\stdClass $data): void {
        global $DB, $USER;

        if (!$this->can_edit()) {
            throw new \moodle_exception('error:cannotedit', 'local_jobboard');
        }

        $this->set_from_data($data);
        $this->modifiedby = $USER->id;
        $this->timemodified = time();

        // Validate.
        $errors = $this->validate();
        if (!empty($errors)) {
            throw new \moodle_exception('error:validation', 'local_jobboard', '', implode(', ', $errors));
        }

        $record = $this->to_record();
        $DB->update_record('local_jobboard_vacancy', $record);

        // Log audit.
        audit::log('vacancy_updated', 'vacancy', $this->id);

        // Trigger event.
        $event = \local_jobboard\event\vacancy_updated::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'other' => ['code' => $this->code, 'title' => $this->title],
        ]);
        $event->trigger();
    }

    /**
     * Delete the vacancy.
     *
     * @throws \moodle_exception If delete not allowed.
     */
    public function delete(): void {
        global $DB;

        if (!$this->can_delete()) {
            throw new \moodle_exception('error:cannotdelete', 'local_jobboard');
        }

        // Delete related records.
        $DB->delete_records('local_jobboard_vacancy_field', ['vacancyid' => $this->id]);
        $DB->delete_records('local_jobboard_doc_requirement', ['vacancyid' => $this->id]);
        $DB->delete_records('local_jobboard_vacancy', ['id' => $this->id]);

        // Log audit.
        audit::log('vacancy_deleted', 'vacancy', $this->id, [
            'code' => $this->code,
            'title' => $this->title,
        ]);

        // Trigger event.
        $event = \local_jobboard\event\vacancy_deleted::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'other' => ['code' => $this->code, 'title' => $this->title],
        ]);
        $event->trigger();
    }

    /**
     * Set properties from form data.
     *
     * @param \stdClass $data The form data.
     */
    protected function set_from_data(\stdClass $data): void {
        $fields = [
            'code', 'title', 'description', 'contracttype', 'duration',
            'salary', 'location', 'department', 'courseid', 'categoryid',
            'companyid', 'opendate', 'closedate', 'positions', 'requirements',
            'desirable', 'status', 'publicationtype',
        ];

        foreach ($fields as $field) {
            if (isset($data->$field)) {
                $this->$field = $data->$field;
            }
        }

        // Handle description as array (from editor).
        if (isset($data->description) && is_array($data->description)) {
            $this->description = $data->description['text'] ?? '';
        }

        // Handle requirements as array (from editor).
        if (isset($data->requirements) && is_array($data->requirements)) {
            $this->requirements = $data->requirements['text'] ?? '';
        }

        // Handle desirable as array (from editor).
        if (isset($data->desirable) && is_array($data->desirable)) {
            $this->desirable = $data->desirable['text'] ?? '';
        }
    }

    /**
     * Validate the vacancy data.
     *
     * @return array Array of error messages.
     */
    public function validate(): array {
        $errors = [];

        // Required fields.
        if (empty($this->code)) {
            $errors[] = get_string('error:requiredfield', 'local_jobboard') . ': ' .
                get_string('vacancycode', 'local_jobboard');
        }

        if (empty($this->title)) {
            $errors[] = get_string('error:requiredfield', 'local_jobboard') . ': ' .
                get_string('vacancytitle', 'local_jobboard');
        }

        if (empty($this->opendate)) {
            $errors[] = get_string('error:requiredfield', 'local_jobboard') . ': ' .
                get_string('opendate', 'local_jobboard');
        }

        if (empty($this->closedate)) {
            $errors[] = get_string('error:requiredfield', 'local_jobboard') . ': ' .
                get_string('closedate', 'local_jobboard');
        }

        // Validate dates.
        if ($this->opendate && $this->closedate) {
            if ($this->closedate <= $this->opendate) {
                $errors[] = get_string('error:invaliddates', 'local_jobboard');
            }
        }

        // Validate unique code.
        if (!empty($this->code) && self::code_exists($this->code, $this->id)) {
            $errors[] = get_string('error:codeexists', 'local_jobboard');
        }

        // Validate status.
        if (!in_array($this->status, self::STATUSES)) {
            $errors[] = get_string('error:invalidstatus', 'local_jobboard');
        }

        // Validate publication type.
        if (!in_array($this->publicationtype, self::PUBLICATION_TYPES)) {
            $errors[] = get_string('error:invalidpublicationtype', 'local_jobboard');
        }

        // For Iomad: require companyid if enabled.
        if (\local_jobboard_is_iomad_installed()) {
            // Company ID is recommended but not strictly required.
        }

        return $errors;
    }

    /**
     * Check if the vacancy is public.
     *
     * @return bool True if public.
     */
    public function is_public(): bool {
        return $this->publicationtype === 'public';
    }

    /**
     * Check if the vacancy is internal.
     *
     * @return bool True if internal.
     */
    public function is_internal(): bool {
        return $this->publicationtype === 'internal';
    }

    /**
     * Convert to database record.
     *
     * @return \stdClass The database record.
     */
    public function to_record(): \stdClass {
        return (object) [
            'id' => $this->id ?: null,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'contracttype' => $this->contracttype,
            'duration' => $this->duration,
            'salary' => $this->salary,
            'location' => $this->location,
            'department' => $this->department,
            'courseid' => $this->courseid,
            'categoryid' => $this->categoryid,
            'companyid' => $this->companyid,
            'opendate' => $this->opendate,
            'closedate' => $this->closedate,
            'positions' => $this->positions,
            'requirements' => $this->requirements,
            'desirable' => $this->desirable,
            'status' => $this->status,
            'publicationtype' => $this->publicationtype,
            'createdby' => $this->createdby,
            'modifiedby' => $this->modifiedby,
            'timecreated' => $this->timecreated,
            'timemodified' => $this->timemodified,
        ];
    }

    /**
     * Check if the vacancy can be edited.
     *
     * @return bool True if editable.
     */
    public function can_edit(): bool {
        // Can edit in draft or published states.
        return in_array($this->status, ['draft', 'published']);
    }

    /**
     * Check if the vacancy can be deleted.
     *
     * @return bool True if deletable.
     */
    public function can_delete(): bool {
        global $DB;

        // Can only delete drafts with no applications.
        if ($this->status !== 'draft') {
            return false;
        }

        return !$DB->record_exists('local_jobboard_application', ['vacancyid' => $this->id]);
    }

    /**
     * Check if the vacancy can be published.
     *
     * @return bool True if can be published.
     */
    public function can_publish(): bool {
        // Must be in draft status.
        if ($this->status !== 'draft') {
            return false;
        }

        // Must have required fields.
        if (empty($this->code) || empty($this->title) || empty($this->opendate) || empty($this->closedate)) {
            return false;
        }

        // Close date must be in the future.
        if ($this->closedate <= time()) {
            return false;
        }

        return true;
    }

    /**
     * Publish the vacancy.
     *
     * @throws \moodle_exception If publish not allowed.
     */
    public function publish(): void {
        if (!$this->can_publish()) {
            throw new \moodle_exception('error:cannotpublish', 'local_jobboard');
        }

        $this->change_status('published');

        // Trigger event.
        $event = \local_jobboard\event\vacancy_published::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'other' => ['code' => $this->code, 'title' => $this->title],
        ]);
        $event->trigger();
    }

    /**
     * Close the vacancy.
     */
    public function close(): void {
        if ($this->status !== 'published') {
            return;
        }

        $this->change_status('closed');
    }

    /**
     * Change the vacancy status.
     *
     * @param string $newstatus The new status.
     */
    protected function change_status(string $newstatus): void {
        global $DB, $USER;

        $this->status = $newstatus;
        $this->modifiedby = $USER->id;
        $this->timemodified = time();

        $DB->update_record('local_jobboard_vacancy', (object) [
            'id' => $this->id,
            'status' => $this->status,
            'modifiedby' => $this->modifiedby,
            'timemodified' => $this->timemodified,
        ]);

        // Log audit.
        audit::log('vacancy_status_changed', 'vacancy', $this->id, ['new_status' => $newstatus]);
    }

    /**
     * Check if the vacancy is accepting applications.
     *
     * @return bool True if accepting applications.
     */
    public function is_open(): bool {
        $now = time();
        return $this->status === 'published' &&
               $this->opendate <= $now &&
               $this->closedate >= $now;
    }

    /**
     * Get the count of applications for this vacancy.
     *
     * @param string|null $status Filter by status.
     * @return int The application count.
     */
    public function get_application_count(?string $status = null): int {
        global $DB;

        $params = ['vacancyid' => $this->id];

        if ($status) {
            $params['status'] = $status;
        }

        return $DB->count_records('local_jobboard_application', $params);
    }

    /**
     * Get the status display name.
     *
     * @return string The localized status name.
     */
    public function get_status_display(): string {
        return get_string('status:' . $this->status, 'local_jobboard');
    }

    /**
     * Get the company name.
     *
     * @return string The company name.
     */
    public function get_company_name(): string {
        if (!$this->companyid) {
            return '';
        }

        return local_jobboard_get_company_name($this->companyid);
    }

    /**
     * Get document requirements for this vacancy.
     *
     * @return array Array of document requirement records.
     */
    public function get_document_requirements(): array {
        global $DB;

        return $DB->get_records('local_jobboard_doc_requirement', ['vacancyid' => $this->id], 'sortorder ASC');
    }

    /**
     * Get custom fields for this vacancy.
     *
     * @return array Array of custom field records.
     */
    public function get_custom_fields(): array {
        global $DB;

        return $DB->get_records('local_jobboard_vacancy_field', ['vacancyid' => $this->id], 'sortorder ASC');
    }

    /**
     * Get list of vacancies with filtering and pagination.
     *
     * @param array $filters Filter options.
     * @param string $sort Sort field.
     * @param string $order Sort order (ASC/DESC).
     * @param int $page Page number.
     * @param int $perpage Items per page.
     * @return array ['vacancies' => array, 'total' => int]
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

        // Filter by status.
        if (!empty($filters['status'])) {
            $where[] = 'status = :status';
            $params['status'] = $filters['status'];
        }

        // Filter by company.
        if (!empty($filters['companyid'])) {
            $where[] = 'companyid = :companyid';
            $params['companyid'] = $filters['companyid'];
        }

        // Filter by date range.
        if (!empty($filters['datefrom'])) {
            $where[] = 'opendate >= :datefrom';
            $params['datefrom'] = $filters['datefrom'];
        }

        if (!empty($filters['dateto'])) {
            $where[] = 'closedate <= :dateto';
            $params['dateto'] = $filters['dateto'];
        }

        // Search in title, code, description.
        if (!empty($filters['search'])) {
            $searchterm = '%' . $DB->sql_like_escape($filters['search']) . '%';
            $where[] = '(' . $DB->sql_like('title', ':search1', false) . ' OR ' .
                       $DB->sql_like('code', ':search2', false) . ' OR ' .
                       $DB->sql_like('description', ':search3', false) . ')';
            $params['search1'] = $searchterm;
            $params['search2'] = $searchterm;
            $params['search3'] = $searchterm;
        }

        // Filter for user visibility (multi-tenant).
        if (!empty($filters['userid']) && !empty($filters['respect_tenant'])) {
            $context = \context_system::instance();
            if (!has_capability('local/jobboard:viewallvacancies', $context, $filters['userid'])) {
                $usercompanyid = \local_jobboard_get_user_companyid($filters['userid']);
                if ($usercompanyid) {
                    $where[] = '(companyid IS NULL OR companyid = :usercompanyid)';
                    $params['usercompanyid'] = $usercompanyid;
                }
                // Only show published vacancies to regular users.
                $where[] = "status = 'published'";
            }
        }

        $wheresql = implode(' AND ', $where);

        // Validate sort field.
        $allowedsorts = ['id', 'code', 'title', 'opendate', 'closedate', 'status', 'timecreated'];
        if (!in_array($sort, $allowedsorts)) {
            $sort = 'timecreated';
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        // Get total count.
        $total = $DB->count_records_sql("SELECT COUNT(*) FROM {local_jobboard_vacancy} WHERE $wheresql", $params);

        // Get records.
        $sql = "SELECT * FROM {local_jobboard_vacancy} WHERE $wheresql ORDER BY $sort $order";
        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        $vacancies = [];
        foreach ($records as $record) {
            $vacancies[] = new self($record);
        }

        return [
            'vacancies' => $vacancies,
            'total' => $total,
        ];
    }

    /**
     * Get published vacancies available for applications.
     *
     * @param int|null $userid User ID for tenant filtering.
     * @param int $limit Maximum number of vacancies.
     * @return array Array of vacancy objects.
     */
    public static function get_available(int $userid = null, int $limit = 50): array {
        $now = time();

        $filters = [
            'status' => 'published',
            'datefrom' => 0,
            'dateto' => $now + (365 * 24 * 60 * 60), // Next year.
        ];

        if ($userid) {
            $filters['userid'] = $userid;
            $filters['respect_tenant'] = true;
        }

        $result = self::get_list($filters, 'closedate', 'ASC', 0, $limit);

        // Additional filter: only open vacancies.
        return array_filter($result['vacancies'], function ($vacancy) use ($now) {
            return $vacancy->opendate <= $now && $vacancy->closedate >= $now;
        });
    }
}
