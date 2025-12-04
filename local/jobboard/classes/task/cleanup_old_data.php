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
 * Scheduled task to cleanup old data.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to clean up old notifications and audit logs.
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

        // Get retention periods from config.
        $notificationdays = get_config('local_jobboard', 'notificationretentiondays');
        if (empty($notificationdays)) {
            $notificationdays = 90; // Default 90 days.
        }

        $auditdays = get_config('local_jobboard', 'auditretentiondays');
        if (empty($auditdays)) {
            $auditdays = 365; // Default 1 year.
        }

        // Calculate thresholds.
        $notificationthreshold = time() - ($notificationdays * 24 * 60 * 60);
        $auditthreshold = time() - ($auditdays * 24 * 60 * 60);

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
            "timecreated < :threshold AND action NOT IN ('application_created', 'application_selected', 'exemption_created')",
            ['threshold' => $auditthreshold]
        );

        // Clean up superseded documents that are very old.
        $docthreshold = time() - (2 * 365 * 24 * 60 * 60); // 2 years.
        $this->cleanup_old_documents($docthreshold);

        mtrace("Cleanup completed:");
        mtrace("  - Sent notifications deleted: {$deletednotifications}");
        mtrace("  - Failed notifications deleted: {$deletedfailed}");
        mtrace("  - Audit logs deleted: {$deletedaudit}");
    }

    /**
     * Clean up old superseded documents.
     *
     * @param int $threshold Time threshold.
     */
    protected function cleanup_old_documents(int $threshold): void {
        global $DB;

        // Get old superseded documents.
        $olddocs = $DB->get_records_select(
            'local_jobboard_document',
            "issuperseded = 1 AND timecreated < :threshold",
            ['threshold' => $threshold]
        );

        $fs = get_file_storage();
        $deleted = 0;

        foreach ($olddocs as $doc) {
            // Delete the file.
            $context = \context_system::instance();
            $files = $fs->get_area_files(
                $context->id,
                'local_jobboard',
                'application_documents',
                $doc->id,
                'id',
                false
            );

            foreach ($files as $file) {
                $file->delete();
            }

            // Delete the record.
            $DB->delete_records('local_jobboard_document', ['id' => $doc->id]);
            $deleted++;
        }

        if ($deleted > 0) {
            mtrace("  - Superseded documents deleted: {$deleted}");
        }
    }
}
