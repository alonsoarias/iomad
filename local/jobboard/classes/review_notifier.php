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
 * Review notification helper for local_jobboard.
 *
 * Sends consolidated email notifications after document review completion.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for sending consolidated review notifications.
 */
class review_notifier {

    /** @var application The application being reviewed. */
    protected $application;

    /** @var \stdClass The applicant user object. */
    protected $applicant;

    /** @var vacancy The vacancy. */
    protected $vacancy;

    /** @var int Company ID for tenant-specific templates. */
    protected $companyid;

    /**
     * Constructor.
     *
     * @param application $application The application.
     */
    public function __construct(application $application) {
        global $DB;

        $this->application = $application;
        $this->applicant = $DB->get_record('user', ['id' => $application->userid], '*', MUST_EXIST);
        $this->vacancy = vacancy::get($application->vacancyid);
        $this->companyid = $this->vacancy->companyid ?? 0;
    }

    /**
     * Send the appropriate notification based on review outcome.
     *
     * @param string $reviewerobservations Optional observations from the reviewer.
     * @return bool True if email sent successfully.
     */
    public function send_review_notification(string $reviewerobservations = ''): bool {
        $stats = document::get_stats($this->application->id);

        // Determine which notification to send based on review outcome.
        if ($stats['rejected'] > 0) {
            return $this->send_docs_rejected_notification($reviewerobservations);
        } else {
            return $this->send_docs_validated_notification();
        }
    }

    /**
     * Send notification when documents are validated (all approved).
     *
     * @return bool True if email sent.
     */
    protected function send_docs_validated_notification(): bool {
        $template = email_template::get('docs_validated', $this->companyid);
        if (!$template) {
            return false;
        }

        $applicationurl = new \moodle_url('/local/jobboard/index.php', [
            'view' => 'myapplications',
        ]);

        $placeholders = [
            '{fullname}' => fullname($this->applicant),
            '{vacancy_code}' => $this->vacancy->code,
            '{vacancy_title}' => $this->vacancy->title,
            '{application_url}' => $applicationurl->out(false),
        ];

        return $this->send_email($template, $placeholders);
    }

    /**
     * Send notification when some documents are rejected.
     *
     * @param string $observations Reviewer observations.
     * @return bool True if email sent.
     */
    protected function send_docs_rejected_notification(string $observations = ''): bool {
        $template = email_template::get('docs_rejected', $this->companyid);
        if (!$template) {
            return false;
        }

        // Build the list of rejected documents.
        $rejectedDocs = document::get_rejected($this->application->id);
        $rejectedList = $this->format_rejected_documents($rejectedDocs);

        $applicationurl = new \moodle_url('/local/jobboard/index.php', [
            'view' => 'myapplications',
        ]);

        $placeholders = [
            '{fullname}' => fullname($this->applicant),
            '{vacancy_code}' => $this->vacancy->code,
            '{vacancy_title}' => $this->vacancy->title,
            '{rejected_docs}' => $rejectedList,
            '{observations}' => $observations ?: get_string('noobservations', 'local_jobboard'),
            '{application_url}' => $applicationurl->out(false),
        ];

        return $this->send_email($template, $placeholders);
    }

    /**
     * Send complete review notification with summary.
     *
     * @param string $observations Optional observations.
     * @return bool True if email sent.
     */
    public function send_review_complete_notification(string $observations = ''): bool {
        $template = email_template::get('review_complete', $this->companyid);
        if (!$template) {
            // Fall back to docs_validated or docs_rejected.
            return $this->send_review_notification($observations);
        }

        $stats = document::get_stats($this->application->id);
        $hasRejected = $stats['rejected'] > 0;

        // Build summary.
        $summary = $this->build_review_summary($stats);

        // Build action required text.
        $actionRequired = '';
        if ($hasRejected) {
            $actionRequired = get_string('email_action_reupload', 'local_jobboard');
        }

        $applicationurl = new \moodle_url('/local/jobboard/index.php', [
            'view' => 'myapplications',
        ]);

        $placeholders = [
            '{fullname}' => fullname($this->applicant),
            '{vacancy_code}' => $this->vacancy->code,
            '{vacancy_title}' => $this->vacancy->title,
            '{summary}' => $summary,
            '{action_required}' => $actionRequired,
            '{application_url}' => $applicationurl->out(false),
        ];

        return $this->send_email($template, $placeholders);
    }

    /**
     * Format list of rejected documents for email.
     *
     * @param array $documents Array of rejected document objects.
     * @return string Formatted list.
     */
    protected function format_rejected_documents(array $documents): string {
        if (empty($documents)) {
            return get_string('none', 'local_jobboard');
        }

        $lines = [];
        foreach ($documents as $doc) {
            $typename = $doc->get_type_display();
            $reason = $doc->validation_reason ?: get_string('noreason', 'local_jobboard');
            $lines[] = "- {$typename}: {$reason}";
        }

        return implode("\n", $lines);
    }

    /**
     * Build review summary text.
     *
     * @param array $stats Document statistics.
     * @return string Summary text.
     */
    protected function build_review_summary(array $stats): string {
        $lines = [];

        $lines[] = get_string('documentsreviewed', 'local_jobboard') . ': ' . $stats['total'];
        $lines[] = get_string('documentsapproved', 'local_jobboard') . ': ' . $stats['approved'];

        if ($stats['rejected'] > 0) {
            $lines[] = get_string('documentsrejected', 'local_jobboard') . ': ' . $stats['rejected'];

            // Add rejected document details.
            $rejectedDocs = document::get_rejected($this->application->id);
            if (!empty($rejectedDocs)) {
                $lines[] = '';
                $lines[] = get_string('rejecteddocuments', 'local_jobboard') . ':';
                $lines[] = $this->format_rejected_documents($rejectedDocs);
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Send the email using Moodle's messaging API.
     *
     * @param email_template $template The template to use.
     * @param array $placeholders The placeholder values.
     * @return bool True if sent successfully.
     */
    protected function send_email(email_template $template, array $placeholders): bool {
        global $SITE;

        // Render the template.
        $rendered = $template->render($placeholders);

        // Prepare the message.
        $eventdata = new \core\message\message();
        $eventdata->component = 'local_jobboard';
        $eventdata->name = 'documentreview';
        $eventdata->userfrom = \core_user::get_noreply_user();
        $eventdata->userto = $this->applicant;
        $eventdata->subject = $rendered['subject'];
        $eventdata->fullmessage = $rendered['body'];
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = nl2br(s($rendered['body']));
        $eventdata->smallmessage = $rendered['subject'];
        $eventdata->notification = 1;
        $eventdata->contexturl = new \moodle_url('/local/jobboard/index.php', ['view' => 'myapplications']);
        $eventdata->contexturlname = get_string('viewapplication', 'local_jobboard');

        try {
            $messageid = message_send($eventdata);
            if ($messageid) {
                // Log audit.
                audit::log('review_notification_sent', 'application', $this->application->id, [
                    'template' => $template->code,
                    'userid' => $this->applicant->id,
                ]);
                return true;
            }
        } catch (\Exception $e) {
            debugging('Failed to send review notification: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }

        return false;
    }

    /**
     * Static helper to send review notification for an application.
     *
     * @param int $applicationid The application ID.
     * @param string $observations Optional reviewer observations.
     * @return bool True if sent.
     */
    public static function notify(int $applicationid, string $observations = ''): bool {
        $app = new application($applicationid);
        $notifier = new self($app);
        return $notifier->send_review_notification($observations);
    }
}
