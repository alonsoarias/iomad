<?php
// This file is part of Moodle
declare(strict_types=1);

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
 * Scheduled task to cleanup old data.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to clean up old notifications, audit logs, and enforce data retention.
 *
 * This task implements data retention policies in compliance with:
 * - Colombian Habeas Data law (Ley 1581/2012)
 * - GDPR requirements
 */
class cleanup_old_data extends \core\task\scheduled_task {

    /**
     * Get task name.
     *
     * @return string Task name.
     */
    public function get_name(): string {
        return get_string('task:cleanupolddata', 'local_jobboard');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        mtrace("Starting Job Board data cleanup...");

        // Get retention periods from config.
        $notificationdays = get_config('local_jobboard', 'notificationretentiondays');
        if (empty($notificationdays)) {
            $notificationdays = 90; // Default 90 days.
        }

        $auditdays = get_config('local_jobboard', 'auditretentiondays');
        if (empty($auditdays)) {
            $auditdays = 365; // Default 1 year.
        }

        // Data retention for applications (default 5 years = 1825 days).
        $applicationretentiondays = get_config('local_jobboard', 'applicationretentiondays');
        if (empty($applicationretentiondays)) {
            $applicationretentiondays = 1825; // Default 5 years for Habeas Data compliance.
        }

        // Calculate thresholds.
        $notificationthreshold = time() - ($notificationdays * 24 * 60 * 60);
        $auditthreshold = time() - ($auditdays * 24 * 60 * 60);
        $applicationthreshold = time() - ($applicationretentiondays * 24 * 60 * 60);

        // Delete old sent notifications.
        $deletednotifications = $DB->delete_records_select(
            'local_jobboard_notification',
            "status = 'sent' AND timecreated < :threshold",
            ['threshold' => $notificationthreshold]
        );

        // Delete old failed notifications.
        $deletedfailed = $DB->delete_records_select(
            'local_jobboard_notification',
            "status = 'failed' AND timecreated < :threshold",
            ['threshold' => $notificationthreshold]
        );

        // Delete old audit logs (but keep important ones longer).
        $deletedaudit = $DB->delete_records_select(
            'local_jobboard_audit',
            "timecreated < :threshold AND action NOT IN ('application_created', 'application_selected', 'exemption_created', 'user_data_deleted')",
            ['threshold' => $auditthreshold]
        );

        // Clean up superseded documents that are very old.
        $docthreshold = time() - (2 * 365 * 24 * 60 * 60); // 2 years.
        $deleteddocs = $this->cleanup_old_documents($docthreshold);

        // Cleanup old applications based on retention policy.
        $deletedapps = $this->cleanup_old_applications($applicationthreshold);

        // Cleanup expired API tokens.
        $deletedtokens = $this->cleanup_expired_tokens();

        // Cleanup expired exemptions.
        $deletedexemptions = $this->cleanup_expired_exemptions();

        mtrace("Cleanup completed:");
        mtrace("  - Sent notifications deleted: {$deletednotifications}");
        mtrace("  - Failed notifications deleted: {$deletedfailed}");
        mtrace("  - Audit logs deleted: {$deletedaudit}");
        if ($deleteddocs > 0) {
            mtrace("  - Superseded documents deleted: {$deleteddocs}");
        }
        if ($deletedapps > 0) {
            mtrace("  - Old applications archived/deleted: {$deletedapps}");
        }
        if ($deletedtokens > 0) {
            mtrace("  - Expired API tokens deleted: {$deletedtokens}");
        }
        if ($deletedexemptions > 0) {
            mtrace("  - Expired exemptions cleaned: {$deletedexemptions}");
        }

        // Log the cleanup for auditing.
        \local_jobboard\audit::log('cleanup_executed', 'system', 0, [
            'notifications' => $deletednotifications + $deletedfailed,
            'audit_logs' => $deletedaudit,
            'documents' => $deleteddocs,
            'applications' => $deletedapps,
            'tokens' => $deletedtokens,
        ]);
    }

    /**
     * Cleanup old applications based on retention policy.
     *
     * Only applications in final states (rejected, withdrawn) that are older
     * than the retention period are deleted. Selected applications are archived
     * but documents may be cleaned.
     *
     * @param int $threshold Time threshold.
     * @return int Number of applications processed.
     */
    protected function cleanup_old_applications(int $threshold): int {
        global $DB;

        $processed = 0;
        $context = \context_system::instance();
        $fs = get_file_storage();

        // Get old applications in final states eligible for deletion.
        $oldapps = $DB->get_records_select(
            'local_jobboard_application',
            "status IN ('rejected', 'withdrawn') AND timecreated < :threshold",
            ['threshold' => $threshold]
        );

        foreach ($oldapps as $app) {
            // Delete related documents and files.
            $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $app->id]);

            foreach ($documents as $doc) {
                // Delete validation records.
                $DB->delete_records('local_jobboard_doc_validation', ['documentid' => $doc->id]);

                // Delete files.
                $files = $fs->get_area_files(
                    $context->id,
                    'local_jobboard',
                    'application_documents',
                    $app->id,
                    'id',
                    false
                );

                foreach ($files as $file) {
                    $file->delete();
                }
            }

            // Delete document records.
            $DB->delete_records('local_jobboard_document', ['applicationid' => $app->id]);

            // Delete workflow logs.
            $DB->delete_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);

            // Delete the application.
            $DB->delete_records('local_jobboard_application', ['id' => $app->id]);

            $processed++;
        }

        return $processed;
    }

    /**
     * Cleanup expired API tokens.
     *
     * @return int Number of tokens deleted.
     */
    protected function cleanup_expired_tokens(): int {
        global $DB;

        $now = time();

        // Delete tokens that expired more than 30 days ago.
        $threshold = $now - (30 * 24 * 60 * 60);

        return $DB->delete_records_select(
            'local_jobboard_api_token',
            "validuntil IS NOT NULL AND validuntil < :threshold",
            ['threshold' => $threshold]
        );
    }

    /**
     * Cleanup expired exemptions (mark as inactive, don't delete for audit trail).
     *
     * @return int Number of exemptions processed.
     */
    protected function cleanup_expired_exemptions(): int {
        global $DB;

        // We don't delete exemptions, but we could log expired ones.
        // This is a placeholder for any future cleanup logic.
        return 0;
    }

    /**
     * Clean up old superseded documents.
     *
     * @param int $threshold Time threshold.
     * @return int Number of documents deleted.
     */
    protected function cleanup_old_documents(int $threshold): int {
        global $DB;

        // Get old superseded documents.
        $olddocs = $DB->get_records_select(
            'local_jobboard_document',
            "issuperseded = 1 AND timecreated < :threshold",
            ['threshold' => $threshold]
        );

        $fs = get_file_storage();
        $deleted = 0;
        $context = \context_system::instance();

        foreach ($olddocs as $doc) {
            // Delete the file.
            $files = $fs->get_area_files(
                $context->id,
                'local_jobboard',
                'application_documents',
                $doc->applicationid,
                'id',
                false
            );

            foreach ($files as $file) {
                if ($file->get_filepath() === '/' . $doc->documenttype . '/' &&
                    $file->get_filename() === $doc->filename) {
                    $file->delete();
                }
            }

            // Delete validation record.
            $DB->delete_records('local_jobboard_doc_validation', ['documentid' => $doc->id]);

            // Delete the document record.
            $DB->delete_records('local_jobboard_document', ['id' => $doc->id]);
            $deleted++;
        }

        return $deleted;
    }
}
