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
 * Email template management class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing email templates with placeholder support.
 */
class email_template {

    /** @var string Template code. */
    public $code;

    /** @var string Email subject. */
    public $subject;

    /** @var string Email body. */
    public $body;

    /** @var int Company ID for multi-tenant support. */
    public $companyid;

    /**
     * Available placeholders by template type.
     */
    public const PLACEHOLDERS = [
        'application_received' => [
            '{fullname}' => 'Recipient full name',
            '{vacancy_code}' => 'Vacancy code',
            '{vacancy_title}' => 'Vacancy title',
            '{application_id}' => 'Application ID',
            '{application_url}' => 'Link to application',
            '{sitename}' => 'Site name',
        ],
        'docs_validated' => [
            '{fullname}' => 'Recipient full name',
            '{vacancy_code}' => 'Vacancy code',
            '{vacancy_title}' => 'Vacancy title',
            '{application_url}' => 'Link to application',
            '{sitename}' => 'Site name',
        ],
        'docs_rejected' => [
            '{fullname}' => 'Recipient full name',
            '{vacancy_code}' => 'Vacancy code',
            '{vacancy_title}' => 'Vacancy title',
            '{rejected_docs}' => 'List of rejected documents',
            '{observations}' => 'Reviewer observations',
            '{application_url}' => 'Link to application',
            '{sitename}' => 'Site name',
        ],
        'review_complete' => [
            '{fullname}' => 'Recipient full name',
            '{vacancy_code}' => 'Vacancy code',
            '{vacancy_title}' => 'Vacancy title',
            '{summary}' => 'Document review summary',
            '{action_required}' => 'Required actions if any',
            '{application_url}' => 'Link to application',
            '{sitename}' => 'Site name',
        ],
        'interview' => [
            '{fullname}' => 'Recipient full name',
            '{vacancy_code}' => 'Vacancy code',
            '{vacancy_title}' => 'Vacancy title',
            '{interview_date}' => 'Interview date/time',
            '{interview_location}' => 'Interview location',
            '{interview_notes}' => 'Additional notes',
            '{sitename}' => 'Site name',
        ],
        'selected' => [
            '{fullname}' => 'Recipient full name',
            '{vacancy_code}' => 'Vacancy code',
            '{vacancy_title}' => 'Vacancy title',
            '{notes}' => 'Additional notes',
            '{sitename}' => 'Site name',
        ],
        'rejected' => [
            '{fullname}' => 'Recipient full name',
            '{vacancy_code}' => 'Vacancy code',
            '{vacancy_title}' => 'Vacancy title',
            '{notes}' => 'Rejection notes',
            '{sitename}' => 'Site name',
        ],
    ];

    /**
     * Get a template by code, optionally for a specific company.
     *
     * @param string $code Template code.
     * @param int $companyid Optional company ID for tenant-specific templates.
     * @return email_template|null Template object or null if not found.
     */
    public static function get(string $code, int $companyid = 0): ?self {
        global $DB;

        // First try company-specific template.
        if ($companyid > 0) {
            $record = $DB->get_record('local_jobboard_email_template', [
                'code' => $code,
                'companyid' => $companyid,
            ]);
            if ($record) {
                return self::from_record($record);
            }
        }

        // Fall back to global template (companyid = 0).
        $record = $DB->get_record('local_jobboard_email_template', [
            'code' => $code,
            'companyid' => 0,
        ]);

        if (!$record) {
            // Return default template from language strings.
            return self::get_default($code);
        }

        return self::from_record($record);
    }

    /**
     * Get default template from language strings.
     *
     * @param string $code Template code.
     * @return email_template|null Default template or null.
     */
    public static function get_default(string $code): ?self {
        $subjectkey = 'email_' . $code . '_subject';
        $bodykey = 'email_' . $code . '_body';

        if (!get_string_manager()->string_exists($subjectkey, 'local_jobboard')) {
            return null;
        }

        $template = new self();
        $template->code = $code;
        $template->subject = get_string($subjectkey, 'local_jobboard');
        $template->body = get_string($bodykey, 'local_jobboard');
        $template->companyid = 0;

        return $template;
    }

    /**
     * Create email_template from database record.
     *
     * @param \stdClass $record Database record.
     * @return email_template Template object.
     */
    public static function from_record(\stdClass $record): self {
        $template = new self();
        $template->code = $record->code;
        $template->subject = $record->subject;
        $template->body = $record->body;
        $template->companyid = $record->companyid ?? 0;
        return $template;
    }

    /**
     * Render the template with placeholder replacement.
     *
     * @param array $placeholders Associative array of placeholder => value.
     * @return array ['subject' => string, 'body' => string]
     */
    public function render(array $placeholders): array {
        global $SITE;

        // Add common placeholders.
        $placeholders['{sitename}'] = $SITE->fullname ?? 'Moodle';

        // Process placeholders.
        $subject = $this->subject;
        $body = $this->body;

        foreach ($placeholders as $key => $value) {
            // Ensure key has braces.
            if (strpos($key, '{') !== 0) {
                $key = '{' . $key . '}';
            }
            $subject = str_replace($key, (string) $value, $subject);
            $body = str_replace($key, (string) $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Save a custom template to the database.
     *
     * @param string $code Template code.
     * @param string $subject Email subject.
     * @param string $body Email body.
     * @param int $companyid Company ID (0 for global).
     * @return int Template ID.
     */
    public static function save(string $code, string $subject, string $body, int $companyid = 0): int {
        global $DB, $USER;

        $existing = $DB->get_record('local_jobboard_email_template', [
            'code' => $code,
            'companyid' => $companyid,
        ]);

        if ($existing) {
            $existing->subject = $subject;
            $existing->body = $body;
            $existing->timemodified = time();
            $existing->usermodified = $USER->id;
            $DB->update_record('local_jobboard_email_template', $existing);
            return $existing->id;
        }

        $record = new \stdClass();
        $record->code = $code;
        $record->subject = $subject;
        $record->body = $body;
        $record->companyid = $companyid;
        $record->timecreated = time();
        $record->timemodified = time();
        $record->usermodified = $USER->id;

        return $DB->insert_record('local_jobboard_email_template', $record);
    }

    /**
     * Reset a template to default values.
     *
     * @param string $code Template code.
     * @param int $companyid Company ID.
     * @return bool True if reset successful.
     */
    public static function reset_to_default(string $code, int $companyid = 0): bool {
        global $DB;

        return $DB->delete_records('local_jobboard_email_template', [
            'code' => $code,
            'companyid' => $companyid,
        ]);
    }

    /**
     * Get available placeholders for a template code.
     *
     * @param string $code Template code.
     * @return array Associative array of placeholder => description.
     */
    public static function get_placeholders(string $code): array {
        return self::PLACEHOLDERS[$code] ?? [
            '{fullname}' => 'Recipient full name',
            '{sitename}' => 'Site name',
        ];
    }

    /**
     * Get all template codes.
     *
     * @return array List of template codes.
     */
    public static function get_all_codes(): array {
        return array_keys(self::PLACEHOLDERS);
    }

    /**
     * Install default templates.
     *
     * @return void
     */
    public static function install_defaults(): void {
        global $DB;

        $defaultTemplates = [
            'application_received' => [
                'subject' => 'Application Received - {vacancy_code}',
                'body' => "Dear {fullname},\n\nYour application for \"{vacancy_title}\" (Code: {vacancy_code}) has been received.\n\nYou can track your application status at:\n{application_url}\n\nBest regards,\n{sitename}",
            ],
            'docs_validated' => [
                'subject' => 'Documents Validated - {vacancy_code}',
                'body' => "Dear {fullname},\n\nYour documents for the application to \"{vacancy_title}\" have been validated.\n\nYou can view your application at:\n{application_url}\n\nBest regards,\n{sitename}",
            ],
            'docs_rejected' => [
                'subject' => 'Document Review - Action Required - {vacancy_code}',
                'body' => "Dear {fullname},\n\nSome documents for your application to \"{vacancy_title}\" require attention.\n\nRejected documents:\n{rejected_docs}\n\nObservations:\n{observations}\n\nPlease review and reupload at:\n{application_url}\n\nBest regards,\n{sitename}",
            ],
            'review_complete' => [
                'subject' => 'Document Review Complete - {vacancy_code}',
                'body' => "Dear {fullname},\n\nThe review of your documents for \"{vacancy_title}\" has been completed.\n\n{summary}\n\n{action_required}\n\nView details at:\n{application_url}\n\nBest regards,\n{sitename}",
            ],
            'interview' => [
                'subject' => 'Interview Scheduled - {vacancy_code}',
                'body' => "Dear {fullname},\n\nYou have been scheduled for an interview for \"{vacancy_title}\".\n\nDate: {interview_date}\nLocation: {interview_location}\n\n{interview_notes}\n\nBest regards,\n{sitename}",
            ],
            'selected' => [
                'subject' => 'Congratulations! - {vacancy_code}',
                'body' => "Dear {fullname},\n\nCongratulations! You have been selected for \"{vacancy_title}\".\n\n{notes}\n\nBest regards,\n{sitename}",
            ],
            'rejected' => [
                'subject' => 'Application Update - {vacancy_code}',
                'body' => "Dear {fullname},\n\nThank you for your interest in \"{vacancy_title}\".\n\nAfter careful consideration, we regret to inform you that your application was not successful this time.\n\n{notes}\n\nWe encourage you to apply for future opportunities.\n\nBest regards,\n{sitename}",
            ],
        ];

        foreach ($defaultTemplates as $code => $data) {
            $existing = $DB->record_exists('local_jobboard_email_template', [
                'code' => $code,
                'companyid' => 0,
            ]);

            if (!$existing) {
                self::save($code, $data['subject'], $data['body'], 0);
            }
        }
    }
}
