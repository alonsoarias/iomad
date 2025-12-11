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
 * Email template management class - Refactored v3.0.
 *
 * Provides complete email template management with multi-tenant support
 * for IOMAD companies, template categories, and standardized placeholders.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_template {

    // =========================================================================
    // TEMPLATE CATEGORIES
    // =========================================================================

    /** @var string Category: Application lifecycle templates. */
    public const CATEGORY_APPLICATION = 'application';

    /** @var string Category: Document review templates. */
    public const CATEGORY_DOCUMENTS = 'documents';

    /** @var string Category: Interview templates. */
    public const CATEGORY_INTERVIEW = 'interview';

    /** @var string Category: Selection process templates. */
    public const CATEGORY_SELECTION = 'selection';

    /** @var string Category: System notifications. */
    public const CATEGORY_SYSTEM = 'system';

    // =========================================================================
    // TEMPLATE CODES
    // =========================================================================

    /** @var string Template: Application received confirmation. */
    public const CODE_APPLICATION_RECEIVED = 'application_received';

    /** @var string Template: Application under review. */
    public const CODE_UNDER_REVIEW = 'under_review';

    /** @var string Template: Documents validated. */
    public const CODE_DOCS_VALIDATED = 'docs_validated';

    /** @var string Template: Documents rejected. */
    public const CODE_DOCS_REJECTED = 'docs_rejected';

    /** @var string Template: Document review complete. */
    public const CODE_REVIEW_COMPLETE = 'review_complete';

    /** @var string Template: Interview scheduled. */
    public const CODE_INTERVIEW_SCHEDULED = 'interview_scheduled';

    /** @var string Template: Interview reminder. */
    public const CODE_INTERVIEW_REMINDER = 'interview_reminder';

    /** @var string Template: Interview completed. */
    public const CODE_INTERVIEW_COMPLETED = 'interview_completed';

    /** @var string Template: Applicant selected. */
    public const CODE_SELECTED = 'selected';

    /** @var string Template: Applicant rejected. */
    public const CODE_REJECTED = 'rejected';

    /** @var string Template: Applicant on waitlist. */
    public const CODE_WAITLIST = 'waitlist';

    /** @var string Template: Vacancy closing soon. */
    public const CODE_VACANCY_CLOSING = 'vacancy_closing';

    /** @var string Template: New vacancy notification. */
    public const CODE_NEW_VACANCY = 'new_vacancy';

    /** @var string Template: Reviewer assignment. */
    public const CODE_REVIEWER_ASSIGNED = 'reviewer_assigned';

    // =========================================================================
    // PROPERTIES
    // =========================================================================

    /** @var int Template ID. */
    public int $id = 0;

    /** @var string Template code. */
    public string $code = '';

    /** @var int Company ID (0 for global). */
    public int $companyid = 0;

    /** @var string Category. */
    public string $category = self::CATEGORY_APPLICATION;

    /** @var string Template name. */
    public string $name = '';

    /** @var string Template description. */
    public string $description = '';

    /** @var string Email subject. */
    public string $subject = '';

    /** @var string Email body. */
    public string $body = '';

    /** @var int Body format (FORMAT_HTML = 1, FORMAT_PLAIN = 0, etc.). */
    public int $bodyformat = 1; // FORMAT_HTML value, using literal for PHP autoload compatibility.

    /** @var bool Whether template is enabled. */
    public bool $enabled = true;

    /** @var bool Whether this is a system default. */
    public bool $is_default = false;

    /** @var int Display priority. */
    public int $priority = 0;

    // =========================================================================
    // PLACEHOLDERS CONFIGURATION
    // =========================================================================

    /**
     * Get all available placeholders organized by template code.
     *
     * Standard placeholders use format: {placeholder_name}
     * All placeholders are lowercase with underscores.
     *
     * @return array Map of template code => placeholders with descriptions.
     */
    public static function get_placeholders_config(): array {
        return [
            // Common placeholders available to all templates.
            '_common' => [
                '{user_fullname}' => 'Nombre completo del usuario / User full name',
                '{user_firstname}' => 'Nombre del usuario / User first name',
                '{user_lastname}' => 'Apellido del usuario / User last name',
                '{user_email}' => 'Email del usuario / User email',
                '{site_name}' => 'Nombre del sitio / Site name',
                '{site_url}' => 'URL del sitio / Site URL',
                '{current_date}' => 'Fecha actual / Current date',
                '{company_name}' => 'Nombre del centro tutorial / Tutorial center name',
            ],

            // Application templates.
            self::CODE_APPLICATION_RECEIVED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{application_id}' => 'ID de la aplicación / Application ID',
                '{application_url}' => 'URL de la aplicación / Application URL',
                '{submit_date}' => 'Fecha de envío / Submit date',
            ],

            self::CODE_UNDER_REVIEW => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{application_url}' => 'URL de la aplicación / Application URL',
                '{reviewer_name}' => 'Nombre del revisor / Reviewer name',
            ],

            // Document review templates.
            self::CODE_DOCS_VALIDATED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{application_url}' => 'URL de la aplicación / Application URL',
                '{documents_count}' => 'Cantidad de documentos / Documents count',
            ],

            self::CODE_DOCS_REJECTED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{rejected_docs}' => 'Lista de documentos rechazados / Rejected documents list',
                '{observations}' => 'Observaciones del revisor / Reviewer observations',
                '{application_url}' => 'URL de la aplicación / Application URL',
                '{resubmit_deadline}' => 'Fecha límite para reenvío / Resubmit deadline',
            ],

            self::CODE_REVIEW_COMPLETE => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{review_summary}' => 'Resumen de la revisión / Review summary',
                '{approved_count}' => 'Documentos aprobados / Approved documents',
                '{rejected_count}' => 'Documentos rechazados / Rejected documents',
                '{action_required}' => 'Acciones requeridas / Required actions',
                '{application_url}' => 'URL de la aplicación / Application URL',
            ],

            // Interview templates.
            self::CODE_INTERVIEW_SCHEDULED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{interview_date}' => 'Fecha de la entrevista / Interview date',
                '{interview_time}' => 'Hora de la entrevista / Interview time',
                '{interview_location}' => 'Ubicación de la entrevista / Interview location',
                '{interview_type}' => 'Tipo de entrevista / Interview type',
                '{interview_duration}' => 'Duración de la entrevista / Interview duration',
                '{interview_notes}' => 'Notas adicionales / Additional notes',
                '{interviewer_name}' => 'Nombre del entrevistador / Interviewer name',
            ],

            self::CODE_INTERVIEW_REMINDER => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{interview_date}' => 'Fecha de la entrevista / Interview date',
                '{interview_time}' => 'Hora de la entrevista / Interview time',
                '{interview_location}' => 'Ubicación de la entrevista / Interview location',
                '{hours_until}' => 'Horas restantes / Hours until interview',
            ],

            self::CODE_INTERVIEW_COMPLETED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{interview_feedback}' => 'Retroalimentación / Interview feedback',
                '{next_steps}' => 'Próximos pasos / Next steps',
            ],

            // Selection templates.
            self::CODE_SELECTED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{selection_notes}' => 'Notas de selección / Selection notes',
                '{next_steps}' => 'Próximos pasos / Next steps',
                '{contact_info}' => 'Información de contacto / Contact information',
            ],

            self::CODE_REJECTED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{rejection_reason}' => 'Motivo del rechazo / Rejection reason',
                '{feedback}' => 'Retroalimentación / Feedback',
            ],

            self::CODE_WAITLIST => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{waitlist_position}' => 'Posición en lista de espera / Waitlist position',
                '{notification_note}' => 'Nota informativa / Information note',
            ],

            // System templates.
            self::CODE_VACANCY_CLOSING => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{days_remaining}' => 'Días restantes / Days remaining',
                '{close_date}' => 'Fecha de cierre / Close date',
                '{vacancy_url}' => 'URL de la convocatoria / Vacancy URL',
            ],

            self::CODE_NEW_VACANCY => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{vacancy_description}' => 'Descripción de la convocatoria / Vacancy description',
                '{open_date}' => 'Fecha de apertura / Open date',
                '{close_date}' => 'Fecha de cierre / Close date',
                '{vacancy_url}' => 'URL de la convocatoria / Vacancy URL',
                '{faculty_name}' => 'Nombre de la facultad / Faculty name',
            ],

            self::CODE_REVIEWER_ASSIGNED => [
                '{vacancy_code}' => 'Código de la convocatoria / Vacancy code',
                '{vacancy_title}' => 'Título de la convocatoria / Vacancy title',
                '{applicant_name}' => 'Nombre del aspirante / Applicant name',
                '{application_url}' => 'URL de la aplicación / Application URL',
                '{documents_count}' => 'Cantidad de documentos / Documents count',
                '{deadline}' => 'Fecha límite de revisión / Review deadline',
            ],
        ];
    }

    /**
     * Get placeholders for a specific template code.
     *
     * @param string $code Template code.
     * @return array Merged common + specific placeholders.
     */
    public static function get_placeholders(string $code): array {
        $config = self::get_placeholders_config();
        $common = $config['_common'] ?? [];
        $specific = $config[$code] ?? [];

        return array_merge($common, $specific);
    }

    /**
     * Get all template codes organized by category.
     *
     * @return array Category => [codes].
     */
    public static function get_codes_by_category(): array {
        return [
            self::CATEGORY_APPLICATION => [
                self::CODE_APPLICATION_RECEIVED,
                self::CODE_UNDER_REVIEW,
            ],
            self::CATEGORY_DOCUMENTS => [
                self::CODE_DOCS_VALIDATED,
                self::CODE_DOCS_REJECTED,
                self::CODE_REVIEW_COMPLETE,
            ],
            self::CATEGORY_INTERVIEW => [
                self::CODE_INTERVIEW_SCHEDULED,
                self::CODE_INTERVIEW_REMINDER,
                self::CODE_INTERVIEW_COMPLETED,
            ],
            self::CATEGORY_SELECTION => [
                self::CODE_SELECTED,
                self::CODE_REJECTED,
                self::CODE_WAITLIST,
            ],
            self::CATEGORY_SYSTEM => [
                self::CODE_VACANCY_CLOSING,
                self::CODE_NEW_VACANCY,
                self::CODE_REVIEWER_ASSIGNED,
            ],
        ];
    }

    /**
     * Get all valid template codes.
     *
     * @return array List of template codes.
     */
    public static function get_all_codes(): array {
        $codes = [];
        foreach (self::get_codes_by_category() as $category => $categoryCodes) {
            $codes = array_merge($codes, $categoryCodes);
        }
        return $codes;
    }

    /**
     * Get all valid categories.
     *
     * @return array List of category codes.
     */
    public static function get_all_categories(): array {
        return [
            self::CATEGORY_APPLICATION,
            self::CATEGORY_DOCUMENTS,
            self::CATEGORY_INTERVIEW,
            self::CATEGORY_SELECTION,
            self::CATEGORY_SYSTEM,
        ];
    }

    /**
     * Get category for a template code.
     *
     * @param string $code Template code.
     * @return string Category.
     */
    public static function get_category_for_code(string $code): string {
        foreach (self::get_codes_by_category() as $category => $codes) {
            if (in_array($code, $codes)) {
                return $category;
            }
        }
        return self::CATEGORY_APPLICATION;
    }

    // =========================================================================
    // FACTORY METHODS
    // =========================================================================

    /**
     * Create template from database record.
     *
     * @param \stdClass $record Database record.
     * @return self Template instance.
     */
    public static function from_record(\stdClass $record): self {
        $template = new self();
        $template->id = (int) ($record->id ?? 0);
        $template->code = $record->code ?? '';
        $template->companyid = (int) ($record->companyid ?? 0);
        $template->category = $record->category ?? self::CATEGORY_APPLICATION;
        $template->name = $record->name ?? '';
        $template->description = $record->description ?? '';
        $template->subject = $record->subject ?? '';
        $template->body = $record->body ?? '';
        $template->bodyformat = (int) ($record->bodyformat ?? FORMAT_HTML);
        $template->enabled = !empty($record->enabled);
        $template->is_default = !empty($record->is_default);
        $template->priority = (int) ($record->priority ?? 0);

        return $template;
    }

    /**
     * Get a template by code, with company fallback to global.
     *
     * @param string $code Template code.
     * @param int $companyid Company ID (0 for global only).
     * @return self|null Template or null if not found.
     */
    public static function get(string $code, int $companyid = 0): ?self {
        global $DB;

        // First try company-specific template.
        if ($companyid > 0) {
            $record = $DB->get_record('local_jobboard_email_template', [
                'code' => $code,
                'companyid' => $companyid,
                'enabled' => 1,
            ]);
            if ($record) {
                return self::from_record($record);
            }
        }

        // Fall back to global template.
        $record = $DB->get_record('local_jobboard_email_template', [
            'code' => $code,
            'companyid' => 0,
            'enabled' => 1,
        ]);

        if ($record) {
            return self::from_record($record);
        }

        // Fall back to language string default.
        return self::get_default($code);
    }

    /**
     * Get template by ID.
     *
     * @param int $id Template ID.
     * @return self|null Template or null.
     */
    public static function get_by_id(int $id): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_email_template', ['id' => $id]);
        return $record ? self::from_record($record) : null;
    }

    /**
     * Get default template from language strings.
     *
     * @param string $code Template code.
     * @return self|null Default template or null.
     */
    public static function get_default(string $code): ?self {
        $subjectkey = 'email_' . $code . '_subject';
        $bodykey = 'email_' . $code . '_body';

        // Check if language strings exist.
        if (!get_string_manager()->string_exists($subjectkey, 'local_jobboard')) {
            return null;
        }

        $template = new self();
        $template->code = $code;
        $template->companyid = 0;
        $template->category = self::get_category_for_code($code);
        $template->name = self::get_template_name($code);
        $template->subject = get_string($subjectkey, 'local_jobboard');
        $template->body = get_string($bodykey, 'local_jobboard');
        $template->bodyformat = FORMAT_HTML;
        $template->enabled = true;
        $template->is_default = true;

        return $template;
    }

    /**
     * Get human-readable template name.
     *
     * @param string $code Template code.
     * @return string Template name.
     */
    public static function get_template_name(string $code): string {
        $key = 'template_' . $code;
        if (get_string_manager()->string_exists($key, 'local_jobboard')) {
            return get_string($key, 'local_jobboard');
        }
        // Fallback: convert code to readable name.
        return ucwords(str_replace('_', ' ', $code));
    }

    /**
     * Get category display name.
     *
     * @param string $category Category code.
     * @return string Category name.
     */
    public static function get_category_name(string $category): string {
        $key = 'template_category_' . $category;
        if (get_string_manager()->string_exists($key, 'local_jobboard')) {
            return get_string($key, 'local_jobboard');
        }
        return ucfirst($category);
    }

    // =========================================================================
    // CRUD OPERATIONS
    // =========================================================================

    /**
     * Save template to database.
     *
     * @return int Template ID.
     */
    public function save(): int {
        global $DB, $USER;

        $record = new \stdClass();
        $record->code = $this->code;
        $record->companyid = $this->companyid;
        $record->category = $this->category;
        $record->name = $this->name;
        $record->description = $this->description;
        $record->subject = $this->subject;
        $record->body = $this->body;
        $record->bodyformat = $this->bodyformat;
        $record->enabled = $this->enabled ? 1 : 0;
        $record->is_default = $this->is_default ? 1 : 0;
        $record->priority = $this->priority;
        $record->timemodified = time();
        $record->modifiedby = $USER->id ?? 0;

        if ($this->id > 0) {
            // Update existing.
            $record->id = $this->id;
            $DB->update_record('local_jobboard_email_template', $record);

            audit::log('email_template_updated', 'email_template', $this->id, [
                'code' => $this->code,
                'companyid' => $this->companyid,
            ]);
        } else {
            // Insert new.
            $record->timecreated = time();
            $record->createdby = $USER->id ?? 0;
            $this->id = $DB->insert_record('local_jobboard_email_template', $record);

            audit::log('email_template_created', 'email_template', $this->id, [
                'code' => $this->code,
                'companyid' => $this->companyid,
            ]);
        }

        return $this->id;
    }

    /**
     * Static save method for quick saves.
     *
     * @param string $code Template code.
     * @param string $subject Email subject.
     * @param string $body Email body.
     * @param int $companyid Company ID.
     * @return int Template ID.
     */
    public static function save_template(string $code, string $subject, string $body, int $companyid = 0): int {
        global $DB;

        // Check for existing template.
        $existing = $DB->get_record('local_jobboard_email_template', [
            'code' => $code,
            'companyid' => $companyid,
        ]);

        $template = new self();
        if ($existing) {
            $template = self::from_record($existing);
        } else {
            $template->code = $code;
            $template->companyid = $companyid;
            $template->category = self::get_category_for_code($code);
            $template->name = self::get_template_name($code);
        }

        $template->subject = $subject;
        $template->body = $body;

        return $template->save();
    }

    /**
     * Delete template.
     *
     * @return bool Success.
     */
    public function delete(): bool {
        global $DB;

        if ($this->id <= 0) {
            return false;
        }

        // Don't delete system defaults.
        if ($this->is_default && $this->companyid === 0) {
            return false;
        }

        $result = $DB->delete_records('local_jobboard_email_template', ['id' => $this->id]);

        if ($result) {
            audit::log('email_template_deleted', 'email_template', $this->id, [
                'code' => $this->code,
                'companyid' => $this->companyid,
            ]);
        }

        return $result;
    }

    /**
     * Reset template to default (delete custom version).
     *
     * @param string $code Template code.
     * @param int $companyid Company ID.
     * @return bool Success.
     */
    public static function reset_to_default(string $code, int $companyid = 0): bool {
        global $DB;

        return $DB->delete_records('local_jobboard_email_template', [
            'code' => $code,
            'companyid' => $companyid,
            'is_default' => 0,
        ]);
    }

    /**
     * Toggle template enabled status.
     *
     * @param int $id Template ID.
     * @return bool New enabled status.
     */
    public static function toggle_enabled(int $id): bool {
        global $DB;

        $record = $DB->get_record('local_jobboard_email_template', ['id' => $id]);
        if (!$record) {
            return false;
        }

        $newstatus = empty($record->enabled) ? 1 : 0;
        $DB->set_field('local_jobboard_email_template', 'enabled', $newstatus, ['id' => $id]);

        return (bool) $newstatus;
    }

    // =========================================================================
    // TEMPLATE RENDERING
    // =========================================================================

    /**
     * Render template with placeholder replacement.
     *
     * @param array $placeholders Associative array of placeholder => value.
     * @return array ['subject' => string, 'body' => string, 'bodyhtml' => string]
     */
    public function render(array $placeholders): array {
        global $SITE, $CFG;

        // Add common placeholders.
        $defaults = [
            '{site_name}' => format_string($SITE->fullname ?? 'Moodle'),
            '{site_url}' => $CFG->wwwroot,
            '{current_date}' => userdate(time(), get_string('strftimedatetime', 'langconfig')),
        ];

        $placeholders = array_merge($defaults, $placeholders);

        // Normalize keys (ensure braces).
        $normalized = [];
        foreach ($placeholders as $key => $value) {
            if (strpos($key, '{') !== 0) {
                $key = '{' . $key . '}';
            }
            $normalized[$key] = (string) $value;
        }

        // Replace placeholders.
        $subject = str_replace(array_keys($normalized), array_values($normalized), $this->subject);
        $body = str_replace(array_keys($normalized), array_values($normalized), $this->body);

        // Generate HTML version.
        $bodyhtml = $this->bodyformat === FORMAT_PLAIN
            ? nl2br(s($body))
            : $body;

        return [
            'subject' => $subject,
            'body' => strip_tags($body),
            'bodyhtml' => $bodyhtml,
        ];
    }

    /**
     * Preview template with sample data.
     *
     * @return array Rendered template with sample values.
     */
    public function preview(): array {
        $sampledata = $this->get_sample_placeholders();
        return $this->render($sampledata);
    }

    /**
     * Get sample placeholder values for preview.
     *
     * @return array Sample placeholder values.
     */
    protected function get_sample_placeholders(): array {
        global $USER;

        $samples = [
            // Common.
            '{user_fullname}' => fullname($USER),
            '{user_firstname}' => $USER->firstname,
            '{user_lastname}' => $USER->lastname,
            '{user_email}' => $USER->email,
            '{company_name}' => 'Centro Tutorial Ejemplo',

            // Application.
            '{vacancy_code}' => 'CONV-2024-001',
            '{vacancy_title}' => 'Docente de Programación',
            '{application_id}' => '12345',
            '{application_url}' => new \moodle_url('/local/jobboard/index.php', ['view' => 'myapplications']),
            '{submit_date}' => userdate(time(), get_string('strftimedatetime', 'langconfig')),

            // Documents.
            '{documents_count}' => '5',
            '{rejected_docs}' => "- Hoja de vida: Formato incorrecto\n- Cédula: Imagen ilegible",
            '{observations}' => 'Por favor revise y corrija los documentos señalados.',
            '{review_summary}' => "Documentos revisados: 5\nAprobados: 3\nRechazados: 2",
            '{approved_count}' => '3',
            '{rejected_count}' => '2',
            '{action_required}' => 'Por favor reenvíe los documentos rechazados.',
            '{resubmit_deadline}' => userdate(time() + 7 * 86400, get_string('strftimedatetime', 'langconfig')),

            // Interview.
            '{interview_date}' => userdate(time() + 3 * 86400, get_string('strftimedateshort', 'langconfig')),
            '{interview_time}' => '10:00 AM',
            '{interview_location}' => 'Sala de Reuniones A, Edificio Principal',
            '{interview_type}' => 'Presencial',
            '{interview_duration}' => '45 minutos',
            '{interview_notes}' => 'Traer documentos originales para verificación.',
            '{interviewer_name}' => 'Dr. Juan Pérez',
            '{hours_until}' => '48',
            '{interview_feedback}' => 'Excelente desempeño en la entrevista.',

            // Selection.
            '{selection_notes}' => 'Ha sido seleccionado por su experiencia y perfil académico.',
            '{next_steps}' => 'El departamento de Recursos Humanos lo contactará para el proceso de contratación.',
            '{contact_info}' => 'rrhh@ejemplo.edu.co - Tel: +57 123 456 7890',
            '{rejection_reason}' => 'Se ha seleccionado a otro candidato con mayor experiencia en el área.',
            '{feedback}' => 'Le invitamos a participar en futuras convocatorias.',
            '{waitlist_position}' => '3',
            '{notification_note}' => 'Le notificaremos si hay cambios en el proceso.',

            // System.
            '{days_remaining}' => '5',
            '{close_date}' => userdate(time() + 5 * 86400, get_string('strftimedatetime', 'langconfig')),
            '{open_date}' => userdate(time() - 10 * 86400, get_string('strftimedatetime', 'langconfig')),
            '{vacancy_url}' => new \moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
            '{vacancy_description}' => 'Se requiere docente para el área de programación...',
            '{faculty_name}' => 'Facultad de Ingenierías e Informática',

            // Reviewer.
            '{reviewer_name}' => 'María García',
            '{applicant_name}' => 'Pedro Sánchez',
            '{deadline}' => userdate(time() + 5 * 86400, get_string('strftimedatetime', 'langconfig')),
        ];

        return $samples;
    }

    // =========================================================================
    // QUERY METHODS
    // =========================================================================

    /**
     * Get all templates for a company (including global).
     *
     * @param int $companyid Company ID.
     * @param bool $includeGlobal Include global templates.
     * @return array Templates.
     */
    public static function get_all_for_company(int $companyid, bool $includeGlobal = true): array {
        global $DB;

        $templates = [];

        // Get company-specific templates.
        if ($companyid > 0) {
            $records = $DB->get_records('local_jobboard_email_template', [
                'companyid' => $companyid,
            ], 'category, priority, code');

            foreach ($records as $record) {
                $templates[$record->code] = self::from_record($record);
            }
        }

        // Get global templates.
        if ($includeGlobal) {
            $globals = $DB->get_records('local_jobboard_email_template', [
                'companyid' => 0,
            ], 'category, priority, code');

            foreach ($globals as $record) {
                // Only add if not overridden by company template.
                if (!isset($templates[$record->code])) {
                    $templates[$record->code] = self::from_record($record);
                }
            }
        }

        // Add defaults for any missing templates.
        foreach (self::get_all_codes() as $code) {
            if (!isset($templates[$code])) {
                $default = self::get_default($code);
                if ($default) {
                    $templates[$code] = $default;
                }
            }
        }

        return $templates;
    }

    /**
     * Get templates by category.
     *
     * @param string $category Category code.
     * @param int $companyid Company ID (0 for global).
     * @return array Templates in category.
     */
    public static function get_by_category(string $category, int $companyid = 0): array {
        $codes = self::get_codes_by_category()[$category] ?? [];
        $templates = [];

        foreach ($codes as $code) {
            $template = self::get($code, $companyid);
            if ($template) {
                $templates[$code] = $template;
            }
        }

        return $templates;
    }

    /**
     * Get statistics.
     *
     * @return array Statistics.
     */
    public static function get_statistics(): array {
        global $DB;

        $stats = [
            'total' => 0,
            'enabled' => 0,
            'disabled' => 0,
            'global' => 0,
            'company_specific' => 0,
            'by_category' => [],
        ];

        $stats['total'] = $DB->count_records('local_jobboard_email_template');
        $stats['enabled'] = $DB->count_records('local_jobboard_email_template', ['enabled' => 1]);
        $stats['disabled'] = $stats['total'] - $stats['enabled'];
        $stats['global'] = $DB->count_records('local_jobboard_email_template', ['companyid' => 0]);
        $stats['company_specific'] = $stats['total'] - $stats['global'];

        // Count by category.
        $sql = "SELECT category, COUNT(*) as cnt FROM {local_jobboard_email_template} GROUP BY category";
        $categories = $DB->get_records_sql($sql);
        foreach ($categories as $cat) {
            $stats['by_category'][$cat->category] = (int) $cat->cnt;
        }

        return $stats;
    }

    // =========================================================================
    // INSTALLATION & SEEDING
    // =========================================================================

    /**
     * Install default templates.
     *
     * Creates default templates for all codes if they don't exist.
     *
     * @return int Number of templates created.
     */
    public static function install_defaults(): int {
        global $DB;

        $created = 0;
        $defaults = self::get_default_templates();

        foreach ($defaults as $code => $data) {
            // Check if already exists.
            $exists = $DB->record_exists('local_jobboard_email_template', [
                'code' => $code,
                'companyid' => 0,
            ]);

            if (!$exists) {
                $template = new self();
                $template->code = $code;
                $template->companyid = 0;
                $template->category = $data['category'];
                $template->name = $data['name'];
                $template->description = $data['description'] ?? '';
                $template->subject = $data['subject'];
                $template->body = $data['body'];
                $template->bodyformat = FORMAT_HTML;
                $template->enabled = true;
                $template->is_default = true;
                $template->priority = $data['priority'] ?? 0;
                $template->save();
                $created++;
            }
        }

        return $created;
    }

    /**
     * Get default template definitions.
     *
     * @return array Default templates.
     */
    public static function get_default_templates(): array {
        return [
            self::CODE_APPLICATION_RECEIVED => [
                'category' => self::CATEGORY_APPLICATION,
                'name' => 'Aplicación Recibida',
                'description' => 'Se envía cuando un aspirante completa su aplicación.',
                'priority' => 10,
                'subject' => 'Aplicación Recibida - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Hemos recibido su aplicación para la convocatoria <strong>{vacancy_title}</strong> (Código: {vacancy_code}).</p>
<p>Puede consultar el estado de su aplicación en cualquier momento desde: <a href="{application_url}">{application_url}</a></p>
<p>Le notificaremos cuando haya actualizaciones en su proceso.</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_UNDER_REVIEW => [
                'category' => self::CATEGORY_APPLICATION,
                'name' => 'En Revisión',
                'description' => 'Se envía cuando la aplicación pasa a estado de revisión.',
                'priority' => 20,
                'subject' => 'Su aplicación está en revisión - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Su aplicación para la convocatoria <strong>{vacancy_title}</strong> está siendo revisada por nuestro equipo evaluador.</p>
<p>Este proceso puede tomar algunos días. Le notificaremos cuando haya novedades.</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_DOCS_VALIDATED => [
                'category' => self::CATEGORY_DOCUMENTS,
                'name' => 'Documentos Validados',
                'description' => 'Se envía cuando todos los documentos son aprobados.',
                'priority' => 10,
                'subject' => 'Documentos Validados - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Sus documentos para la convocatoria <strong>{vacancy_title}</strong> han sido validados exitosamente.</p>
<p>Total de documentos revisados: {documents_count}</p>
<p>Su aplicación continuará al siguiente paso del proceso de selección.</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_DOCS_REJECTED => [
                'category' => self::CATEGORY_DOCUMENTS,
                'name' => 'Documentos Rechazados',
                'description' => 'Se envía cuando uno o más documentos requieren corrección.',
                'priority' => 20,
                'subject' => 'Documentos Requieren Corrección - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Algunos documentos de su aplicación para la convocatoria <strong>{vacancy_title}</strong> requieren corrección:</p>
<p><strong>Documentos rechazados:</strong></p>
<pre>{rejected_docs}</pre>
<p><strong>Observaciones del revisor:</strong></p>
<p>{observations}</p>
<p>Por favor ingrese a la plataforma para corregir y reenviar los documentos antes de: {resubmit_deadline}</p>
<p><a href="{application_url}">Ir a mi aplicación</a></p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_REVIEW_COMPLETE => [
                'category' => self::CATEGORY_DOCUMENTS,
                'name' => 'Revisión Completada',
                'description' => 'Se envía cuando se completa la revisión documental.',
                'priority' => 30,
                'subject' => 'Revisión de Documentos Completada - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>La revisión de sus documentos para la convocatoria <strong>{vacancy_title}</strong> ha sido completada.</p>
<p><strong>Resumen:</strong></p>
<pre>{review_summary}</pre>
<p>{action_required}</p>
<p>Para más detalles, visite: <a href="{application_url}">{application_url}</a></p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_INTERVIEW_SCHEDULED => [
                'category' => self::CATEGORY_INTERVIEW,
                'name' => 'Entrevista Programada',
                'description' => 'Se envía cuando se programa una entrevista.',
                'priority' => 10,
                'subject' => 'Entrevista Programada - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Ha sido programado/a para una entrevista para la convocatoria <strong>{vacancy_title}</strong>.</p>
<p><strong>Detalles de la entrevista:</strong></p>
<ul>
<li><strong>Fecha:</strong> {interview_date}</li>
<li><strong>Hora:</strong> {interview_time}</li>
<li><strong>Duración aproximada:</strong> {interview_duration}</li>
<li><strong>Modalidad:</strong> {interview_type}</li>
<li><strong>Lugar/Enlace:</strong> {interview_location}</li>
</ul>
<p><strong>Notas adicionales:</strong></p>
<p>{interview_notes}</p>
<p>Por favor confirme su asistencia respondiendo a este correo.</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_INTERVIEW_REMINDER => [
                'category' => self::CATEGORY_INTERVIEW,
                'name' => 'Recordatorio de Entrevista',
                'description' => 'Se envía como recordatorio antes de la entrevista.',
                'priority' => 20,
                'subject' => 'Recordatorio: Entrevista en {hours_until} horas - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Le recordamos que su entrevista para la convocatoria <strong>{vacancy_title}</strong> está próxima.</p>
<p><strong>Fecha:</strong> {interview_date} a las {interview_time}</p>
<p><strong>Lugar:</strong> {interview_location}</p>
<p>Le esperamos.</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_INTERVIEW_COMPLETED => [
                'category' => self::CATEGORY_INTERVIEW,
                'name' => 'Entrevista Completada',
                'description' => 'Se envía después de completar la entrevista.',
                'priority' => 30,
                'subject' => 'Entrevista Completada - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Gracias por participar en la entrevista para la convocatoria <strong>{vacancy_title}</strong>.</p>
<p>{interview_feedback}</p>
<p><strong>Próximos pasos:</strong></p>
<p>{next_steps}</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_SELECTED => [
                'category' => self::CATEGORY_SELECTION,
                'name' => 'Seleccionado',
                'description' => 'Se envía cuando el aspirante es seleccionado.',
                'priority' => 10,
                'subject' => '¡Felicitaciones! Ha sido seleccionado/a - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Nos complace informarle que ha sido <strong>seleccionado/a</strong> para la convocatoria <strong>{vacancy_title}</strong>.</p>
<p>{selection_notes}</p>
<p><strong>Próximos pasos:</strong></p>
<p>{next_steps}</p>
<p><strong>Información de contacto:</strong></p>
<p>{contact_info}</p>
<p>¡Bienvenido/a a nuestro equipo!</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_REJECTED => [
                'category' => self::CATEGORY_SELECTION,
                'name' => 'No Seleccionado',
                'description' => 'Se envía cuando el aspirante no es seleccionado.',
                'priority' => 20,
                'subject' => 'Resultado del Proceso de Selección - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Agradecemos su participación en el proceso de selección para la convocatoria <strong>{vacancy_title}</strong>.</p>
<p>Después de una cuidadosa evaluación, lamentamos informarle que en esta ocasión no ha sido seleccionado/a para continuar en el proceso.</p>
<p>{feedback}</p>
<p>Le invitamos a participar en futuras convocatorias que se ajusten a su perfil profesional.</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_WAITLIST => [
                'category' => self::CATEGORY_SELECTION,
                'name' => 'Lista de Espera',
                'description' => 'Se envía cuando el aspirante queda en lista de espera.',
                'priority' => 30,
                'subject' => 'Lista de Espera - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Le informamos que ha quedado en <strong>lista de espera</strong> para la convocatoria <strong>{vacancy_title}</strong>.</p>
<p>Su posición actual es: <strong>#{waitlist_position}</strong></p>
<p>{notification_note}</p>
<p>Le contactaremos en caso de que se presente alguna vacante.</p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_VACANCY_CLOSING => [
                'category' => self::CATEGORY_SYSTEM,
                'name' => 'Convocatoria por Cerrar',
                'description' => 'Se envía cuando una convocatoria está por cerrar.',
                'priority' => 10,
                'subject' => 'Convocatoria por cerrar - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Le informamos que la convocatoria <strong>{vacancy_title}</strong> cerrará en <strong>{days_remaining} día(s)</strong>.</p>
<p><strong>Fecha de cierre:</strong> {close_date}</p>
<p>Si está interesado/a, puede aplicar antes de la fecha límite en:</p>
<p><a href="{vacancy_url}">{vacancy_url}</a></p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_NEW_VACANCY => [
                'category' => self::CATEGORY_SYSTEM,
                'name' => 'Nueva Convocatoria',
                'description' => 'Se envía para notificar sobre nuevas convocatorias.',
                'priority' => 20,
                'subject' => 'Nueva Convocatoria Disponible - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Le informamos sobre una nueva convocatoria disponible:</p>
<p><strong>{vacancy_title}</strong> (Código: {vacancy_code})</p>
<p>{vacancy_description}</p>
<p><strong>Facultad:</strong> {faculty_name}</p>
<p><strong>Fecha de apertura:</strong> {open_date}</p>
<p><strong>Fecha de cierre:</strong> {close_date}</p>
<p>Para más información y aplicar, visite:</p>
<p><a href="{vacancy_url}">{vacancy_url}</a></p>
<p>Atentamente,<br>{site_name}</p>',
            ],

            self::CODE_REVIEWER_ASSIGNED => [
                'category' => self::CATEGORY_SYSTEM,
                'name' => 'Revisor Asignado',
                'description' => 'Se envía al revisor cuando se le asigna una aplicación.',
                'priority' => 30,
                'subject' => 'Nueva aplicación para revisar - {vacancy_code}',
                'body' => '<p>Estimado/a {user_fullname},</p>
<p>Se le ha asignado una nueva aplicación para revisar:</p>
<p><strong>Convocatoria:</strong> {vacancy_title} ({vacancy_code})</p>
<p><strong>Aspirante:</strong> {applicant_name}</p>
<p><strong>Documentos a revisar:</strong> {documents_count}</p>
<p><strong>Fecha límite de revisión:</strong> {deadline}</p>
<p>Puede acceder a la aplicación desde:</p>
<p><a href="{application_url}">{application_url}</a></p>
<p>Atentamente,<br>{site_name}</p>',
            ],
        ];
    }
}
