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

    /** @var string The work location. */
    public $location = '';

    /** @var string The department. */
    public $department = '';

    /** @var int|null The Iomad company ID. */
    public $companyid = null;

    /** @var int|null The IOMAD department ID. */
    public $departmentid = null;

    /** @var int|null The parent convocatoria ID. */
    public $convocatoriaid = null;

    /** @var \stdClass|null Cached convocatoria record. */
    protected $convocatoria_cache = null;

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
     * Magic getter for backward compatibility with removed fields.
     *
     * Provides access to opendate and closedate properties (now derived from convocatoria).
     *
     * @param string $name The property name.
     * @return mixed The property value or null.
     */
    public function __get(string $name) {
        switch ($name) {
            case 'opendate':
                return $this->get_open_date();
            case 'closedate':
                return $this->get_close_date();
            case 'salary':
                // Salary field has been removed - return empty string for compatibility.
                return '';
            case 'isextemporaneous':
                // Extemporaneous field has been removed - always return 0.
                return 0;
            case 'extemporaneousreason':
                // Extemporaneous reason has been removed - return empty string.
                return '';
            default:
                // Check if property exists in record (for any unmapped fields).
                if ($this->record !== null && property_exists($this->record, $name)) {
                    return $this->record->$name;
                }
                return null;
        }
    }

    /**
     * Magic isset check for backward compatibility with removed fields.
     *
     * @param string $name The property name.
     * @return bool Whether the property is set.
     */
    public function __isset(string $name): bool {
        return in_array($name, ['opendate', 'closedate', 'salary', 'isextemporaneous', 'extemporaneousreason']);
    }

    /**
     * Constructor.
     *
     * @param int|\stdClass|null $idorrecord Vacancy ID, database record, or null.
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
        $this->location = $record->location ?? '';
        $this->department = $record->department ?? '';
        $this->companyid = $record->companyid ? (int) $record->companyid : null;
        $this->departmentid = isset($record->departmentid) && $record->departmentid ? (int) $record->departmentid : null;
        $this->convocatoriaid = isset($record->convocatoriaid) && $record->convocatoriaid ? (int) $record->convocatoriaid : null;
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
            'location', 'department', 'companyid', 'departmentid',
            'convocatoriaid', 'positions',
            'requirements', 'desirable', 'status', 'publicationtype',
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

        // Convocatoria is required (v2.1.0+: dates come from convocatoria).
        if (empty($this->convocatoriaid)) {
            $errors[] = get_string('error:convocatoriarequired', 'local_jobboard');
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
            'location' => $this->location,
            'department' => $this->department,
            'companyid' => $this->companyid,
            'departmentid' => $this->departmentid,
            'convocatoriaid' => $this->convocatoriaid,
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
     * Get the opening date from convocatoria.
     *
     * @return int The opening date timestamp.
     */
    public function get_open_date(): int {
        $convocatoria = $this->get_convocatoria();
        return $convocatoria ? (int) $convocatoria->startdate : 0;
    }

    /**
     * Get the closing date from convocatoria.
     *
     * @return int The closing date timestamp.
     */
    public function get_close_date(): int {
        $convocatoria = $this->get_convocatoria();
        return $convocatoria ? (int) $convocatoria->enddate : 0;
    }

    /**
     * Get the vacancy as a database record object.
     * Alias for to_record() method.
     *
     * @return \stdClass The database record.
     */
    public function get_record(): \stdClass {
        return $this->to_record();
    }

    /**
     * Check if the vacancy can be edited.
     *
     * @return bool True if editable.
     */
    public function can_edit(): bool {
        // Can edit in draft, published, or closed states.
        return in_array($this->status, ['draft', 'published', 'closed']);
    }

    /**
     * Check if the vacancy can be deleted.
     *
     * @param bool $force Whether to allow force delete (ignores application check).
     * @return bool True if deletable.
     */
    public function can_delete(bool $force = false): bool {
        global $DB;

        // Force delete bypasses all checks (admin only).
        if ($force) {
            return true;
        }

        // Cannot delete if there are applications.
        if ($DB->record_exists('local_jobboard_application', ['vacancyid' => $this->id])) {
            return false;
        }

        return true;
    }

    /**
     * Get the reason why the vacancy cannot be deleted.
     *
     * @return string|null The reason or null if can be deleted.
     */
    public function get_delete_restriction_reason(): ?string {
        global $DB;

        $appcount = $DB->count_records('local_jobboard_application', ['vacancyid' => $this->id]);
        if ($appcount > 0) {
            return get_string('error:cannotdelete_hasapplications', 'local_jobboard', $appcount);
        }

        return null;
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

        // Must have required fields and convocatoria.
        if (empty($this->code) || empty($this->title) || empty($this->convocatoriaid)) {
            return false;
        }

        // Close date (from convocatoria) must be in the future.
        $closedate = $this->get_close_date();
        if ($closedate <= time()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the vacancy can be unpublished (reverted to draft).
     *
     * @return bool True if can be unpublished.
     */
    public function can_unpublish(): bool {
        global $DB;

        // Must be in published status.
        if ($this->status !== 'published') {
            return false;
        }

        // Cannot unpublish if there are applications.
        return !$DB->record_exists('local_jobboard_application', ['vacancyid' => $this->id]);
    }

    /**
     * Check if the vacancy can be reopened.
     *
     * @return bool True if can be reopened.
     */
    public function can_reopen(): bool {
        // Must be in closed status.
        return $this->status === 'closed';
    }

    /**
     * Check if the vacancy can be closed.
     *
     * @return bool True if can be closed.
     */
    public function can_close(): bool {
        return $this->status === 'published';
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
     * Unpublish the vacancy (revert to draft).
     *
     * @throws \moodle_exception If unpublish not allowed.
     */
    public function unpublish(): void {
        if (!$this->can_unpublish()) {
            throw new \moodle_exception('error:cannotunpublish', 'local_jobboard');
        }

        $this->change_status('draft');

        // Log audit.
        audit::log('vacancy_unpublished', 'vacancy', $this->id);
    }

    /**
     * Close the vacancy.
     *
     * @throws \moodle_exception If close not allowed.
     */
    public function close(): void {
        if (!$this->can_close()) {
            throw new \moodle_exception('error:cannotclose', 'local_jobboard');
        }

        $this->change_status('closed');

        // Trigger event.
        $event = \local_jobboard\event\vacancy_closed::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'other' => ['code' => $this->code, 'title' => $this->title],
        ]);
        $event->trigger();
    }

    /**
     * Reopen a closed vacancy.
     *
     * Note: As of v2.1.0, vacancy dates come from convocatoria.
     * To extend dates, update the convocatoria instead.
     *
     * @throws \moodle_exception If reopen not allowed.
     */
    public function reopen(): void {
        if (!$this->can_reopen()) {
            throw new \moodle_exception('error:cannotreopen', 'local_jobboard');
        }

        $this->change_status('published');

        // Log audit.
        audit::log('vacancy_reopened', 'vacancy', $this->id);

        // Trigger event.
        $event = \local_jobboard\event\vacancy_published::create([
            'objectid' => $this->id,
            'context' => \context_system::instance(),
            'other' => ['code' => $this->code, 'title' => $this->title, 'action' => 'reopened'],
        ]);
        $event->trigger();
    }

    /**
     * Mark the vacancy as assigned (positions filled).
     */
    public function assign(): void {
        if ($this->status !== 'closed') {
            // Can only assign from closed status.
            return;
        }

        $this->change_status('assigned');

        // Log audit.
        audit::log('vacancy_assigned', 'vacancy', $this->id);
    }

    /**
     * Get available status transitions from current status.
     *
     * @return array Array of available target statuses.
     */
    public function get_available_transitions(): array {
        $transitions = [];

        switch ($this->status) {
            case 'draft':
                if ($this->can_publish()) {
                    $transitions[] = 'published';
                }
                break;
            case 'published':
                $transitions[] = 'closed';
                if ($this->can_unpublish()) {
                    $transitions[] = 'draft';
                }
                break;
            case 'closed':
                $transitions[] = 'published'; // Reopen.
                $transitions[] = 'assigned';
                break;
            case 'assigned':
                // Final state, no transitions.
                break;
        }

        return $transitions;
    }

    /**
     * Change the vacancy status.
     *
     * @param string $newstatus The new status.
     */
    public function change_status(string $newstatus): void {
        global $DB, $USER;

        $oldstatus = $this->status;
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
        audit::log('vacancy_status_changed', 'vacancy', $this->id, [
            'old_status' => $oldstatus,
            'new_status' => $newstatus,
        ]);
    }

    /**
     * Check if the vacancy is accepting applications.
     *
     * @return bool True if accepting applications.
     */
    public function is_open(): bool {
        $now = time();
        $opendate = $this->get_open_date();
        $closedate = $this->get_close_date();
        return $this->status === 'published' &&
               $opendate <= $now &&
               $closedate >= $now;
    }

    /**
     * Check if the vacancy is open for applications.
     * Alias for is_open() method.
     *
     * @return bool True if accepting applications.
     */
    public function is_open_for_applications(): bool {
        return $this->is_open();
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
     * Get the department name.
     *
     * @return string The department name.
     */
    public function get_department_name(): string {
        if (!$this->departmentid) {
            return '';
        }

        return local_jobboard_get_department_name($this->departmentid);
    }

    /**
     * Get the convocatoria record.
     *
     * @return \stdClass|null The convocatoria record or null.
     */
    public function get_convocatoria(): ?\stdClass {
        global $DB;

        if (!$this->convocatoriaid) {
            return null;
        }

        return $DB->get_record('local_jobboard_convocatoria', ['id' => $this->convocatoriaid]);
    }

    /**
     * Get the convocatoria name.
     *
     * @return string The convocatoria name or empty string.
     */
    public function get_convocatoria_name(): string {
        $convocatoria = $this->get_convocatoria();
        return $convocatoria ? $convocatoria->name : '';
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

        // Filter by department.
        if (!empty($filters['departmentid'])) {
            $where[] = 'departmentid = :departmentid';
            $params['departmentid'] = $filters['departmentid'];
        }

        // Filter by convocatoria.
        if (!empty($filters['convocatoriaid'])) {
            $where[] = 'convocatoriaid = :convocatoriaid';
            $params['convocatoriaid'] = (int) $filters['convocatoriaid'];
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
            $userid = (int) $filters['userid'];
            if (!has_capability('local/jobboard:viewallvacancies', $context, $userid)) {
                $usercompanyid = \local_jobboard_get_user_companyid($userid);
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
