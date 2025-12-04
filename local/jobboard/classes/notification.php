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
 * Notification helper class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for queuing and managing notifications.
 */
class notification {

    /** @var string Template: Application received. */
    public const TEMPLATE_APPLICATION_RECEIVED = 'application_received';

    /** @var string Template: Application under review. */
    public const TEMPLATE_UNDER_REVIEW = 'under_review';

    /** @var string Template: Documents validated. */
    public const TEMPLATE_DOCS_VALIDATED = 'docs_validated';

    /** @var string Template: Documents rejected. */
    public const TEMPLATE_DOCS_REJECTED = 'docs_rejected';

    /** @var string Template: Interview scheduled. */
    public const TEMPLATE_INTERVIEW = 'interview';

    /** @var string Template: Application selected. */
    public const TEMPLATE_SELECTED = 'selected';

    /** @var string Template: Application rejected. */
    public const TEMPLATE_REJECTED = 'rejected';

    /** @var string Template: Vacancy closing soon. */
    public const TEMPLATE_CLOSING_SOON = 'closing_soon';

    /** @var string Template: New vacancy available. */
    public const TEMPLATE_NEW_VACANCY = 'new_vacancy';

    /**
     * Queue a notification for sending.
     *
     * @param int $userid Recipient user ID.
     * @param string $templatecode Template code.
     * @param array $data Placeholder data.
     * @return int Notification ID.
     */
    public static function queue(int $userid, string $templatecode, array $data = []): int {
        global $DB;

        $record = new \stdClass();
        $record->userid = $userid;
        $record->templatecode = $templatecode;
        $record->data = json_encode($data);
        $record->status = 'pending';
        $record->attempts = 0;
        $record->timecreated = time();

        return $DB->insert_record('local_jobboard_notification', $record);
    }

    /**
     * Queue application status notification.
     *
     * @param application $application Application object.
     * @param string $newstatus New status.
     * @param string|null $notes Optional notes.
     */
    public static function queue_status_change(application $application, string $newstatus, ?string $notes = null): void {
        global $DB;

        // Get vacancy.
        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);

        $data = [
            'vacancy_code' => $vacancy->code ?? '',
            'vacancy_title' => $vacancy->title ?? '',
            'application_id' => $application->id,
            'status' => get_string('status_' . $newstatus, 'local_jobboard'),
            'notes' => $notes ?? '',
            'application_url' => (new \moodle_url('/local/jobboard/application.php',
                ['id' => $application->id]))->out(false),
        ];

        // Map status to template.
        $templatemap = [
            'under_review' => self::TEMPLATE_UNDER_REVIEW,
            'docs_validated' => self::TEMPLATE_DOCS_VALIDATED,
            'docs_rejected' => self::TEMPLATE_DOCS_REJECTED,
            'interview' => self::TEMPLATE_INTERVIEW,
            'selected' => self::TEMPLATE_SELECTED,
            'rejected' => self::TEMPLATE_REJECTED,
        ];

        $template = $templatemap[$newstatus] ?? null;
        if ($template) {
            self::queue($application->userid, $template, $data);
        }
    }

    /**
     * Queue application received notification.
     *
     * @param application $application Application object.
     */
    public static function queue_application_received(application $application): void {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);

        $data = [
            'vacancy_code' => $vacancy->code ?? '',
            'vacancy_title' => $vacancy->title ?? '',
            'application_id' => $application->id,
            'application_url' => (new \moodle_url('/local/jobboard/application.php',
                ['id' => $application->id]))->out(false),
        ];

        self::queue($application->userid, self::TEMPLATE_APPLICATION_RECEIVED, $data);
    }

    /**
     * Queue vacancy closing notification.
     *
     * @param vacancy $vacancy Vacancy object.
     * @param int $userid User ID.
     * @param int $daysleft Days until closing.
     */
    public static function queue_closing_soon(vacancy $vacancy, int $userid, int $daysleft): void {
        $data = [
            'vacancy_code' => $vacancy->code,
            'vacancy_title' => $vacancy->title,
            'days_left' => $daysleft,
            'close_date' => userdate($vacancy->closedate, get_string('strftimedatetime', 'langconfig')),
            'vacancy_url' => (new \moodle_url('/local/jobboard/vacancy.php',
                ['id' => $vacancy->id]))->out(false),
        ];

        self::queue($userid, self::TEMPLATE_CLOSING_SOON, $data);
    }

    /**
     * Send notification immediately (bypass queue).
     *
     * @param int $userid Recipient user ID.
     * @param string $templatecode Template code.
     * @param array $data Placeholder data.
     * @return bool Success.
     */
    public static function send_immediate(int $userid, string $templatecode, array $data = []): bool {
        global $DB;

        $user = $DB->get_record('user', ['id' => $userid]);
        if (!$user || $user->deleted || $user->suspended) {
            return false;
        }

        // Get template.
        $template = $DB->get_record('local_jobboard_email_template', ['code' => $templatecode]);

        if ($template) {
            $subject = $template->subject;
            $body = $template->body;
        } else {
            $subject = get_string('notification_' . $templatecode . '_subject', 'local_jobboard');
            $body = get_string('notification_' . $templatecode . '_body', 'local_jobboard');
        }

        // Parse placeholders.
        $subject = self::parse_placeholders($subject, $data, $user);
        $body = self::parse_placeholders($body, $data, $user);

        // Send.
        $supportuser = \core_user::get_support_user();

        $message = new \core\message\message();
        $message->component = 'local_jobboard';
        $message->name = 'application_notification';
        $message->userfrom = $supportuser;
        $message->userto = $user;
        $message->subject = $subject;
        $message->fullmessage = html_to_text($body);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $body;
        $message->smallmessage = $subject;
        $message->notification = 1;

        return message_send($message) !== false;
    }

    /**
     * Parse placeholders in template.
     *
     * @param string $text Template text.
     * @param array $data Placeholder data.
     * @param object $user User object.
     * @return string Parsed text.
     */
    protected static function parse_placeholders(string $text, array $data, object $user): string {
        $replacements = [
            '{USER_NAME}' => fullname($user),
            '{USER_FIRSTNAME}' => $user->firstname,
            '{USER_LASTNAME}' => $user->lastname,
            '{USER_EMAIL}' => $user->email,
            '{SITE_NAME}' => format_string(get_site()->fullname),
        ];

        // Add custom data placeholders.
        foreach ($data as $key => $value) {
            $replacements['{' . strtoupper($key) . '}'] = $value;
        }

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Get pending notifications count.
     *
     * @return int Count of pending notifications.
     */
    public static function get_pending_count(): int {
        global $DB;
        return $DB->count_records('local_jobboard_notification', ['status' => 'pending']);
    }

    /**
     * Get failed notifications.
     *
     * @param int $limit Max records.
     * @return array Failed notifications.
     */
    public static function get_failed(int $limit = 100): array {
        global $DB;
        return $DB->get_records('local_jobboard_notification', ['status' => 'failed'],
            'timecreated DESC', '*', 0, $limit);
    }

    /**
     * Retry failed notifications.
     *
     * @return int Number of notifications reset for retry.
     */
    public static function retry_failed(): int {
        global $DB;

        return $DB->execute("UPDATE {local_jobboard_notification}
                             SET status = 'pending', attempts = 0, lasterror = NULL
                             WHERE status = 'failed'");
    }
}
