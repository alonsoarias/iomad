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
 * Scheduled task to send pending notifications.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to process and send pending email notifications.
 */
class send_notifications extends \core\task\scheduled_task {

    /**
     * Get task name.
     *
     * @return string Task name.
     */
    public function get_name(): string {
        return get_string('task:sendnotifications', 'local_jobboard');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        // Get pending notifications.
        $notifications = $DB->get_records('local_jobboard_notification', [
            'status' => 'pending',
        ], 'timecreated ASC', '*', 0, 100);

        $sent = 0;
        $failed = 0;

        foreach ($notifications as $notification) {
            try {
                $success = $this->send_notification($notification);

                if ($success) {
                    $notification->status = 'sent';
                    $notification->timesent = time();
                    $sent++;
                } else {
                    $notification->attempts++;
                    if ($notification->attempts >= 3) {
                        $notification->status = 'failed';
                    }
                    $notification->lasterror = 'Send failed';
                    $failed++;
                }
            } catch (\Exception $e) {
                $notification->attempts++;
                if ($notification->attempts >= 3) {
                    $notification->status = 'failed';
                }
                $notification->lasterror = $e->getMessage();
                $failed++;
            }

            $DB->update_record('local_jobboard_notification', $notification);
        }

        mtrace("Notifications sent: {$sent}, failed: {$failed}");
    }

    /**
     * Send a single notification.
     *
     * @param object $notification Notification record.
     * @return bool Success.
     */
    protected function send_notification(object $notification): bool {
        global $DB;

        // Get recipient.
        $user = $DB->get_record('user', ['id' => $notification->userid]);
        if (!$user || $user->deleted || $user->suspended) {
            return false;
        }

        // Get email template.
        $template = $DB->get_record('local_jobboard_email_template', [
            'code' => $notification->templatecode,
        ]);

        if (!$template) {
            // Use default strings.
            $subject = get_string('notification_' . $notification->templatecode . '_subject', 'local_jobboard');
            $body = get_string('notification_' . $notification->templatecode . '_body', 'local_jobboard');
        } else {
            $subject = $template->subject;
            $body = $template->body;
        }

        // Parse placeholders.
        $data = json_decode($notification->data, true) ?? [];
        $subject = $this->parse_placeholders($subject, $data, $user);
        $body = $this->parse_placeholders($body, $data, $user);

        // Send email.
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
    protected function parse_placeholders(string $text, array $data, object $user): string {
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
}
