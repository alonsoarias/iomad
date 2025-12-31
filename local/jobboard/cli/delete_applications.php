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
 * CLI script for managing and deleting job applications.
 *
 * This script provides comprehensive functionality for:
 * - Listing applications by user idnumber or application ID
 * - Deleting applications with full cascade (documents, validations, workflow logs, etc.)
 * - Dry-run mode for safe preview
 * - Detailed audit logging
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\audit;

// CLI options.
list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'idnumber' => null,
    'application-id' => null,
    'vacancy-id' => null,
    'status' => null,
    'list' => false,
    'delete' => false,
    'dryrun' => false,
    'verbose' => false,
    'force' => false,
    'reason' => 'CLI deletion by administrator',
    'all-for-user' => false,
    'show-documents' => false,
    'show-history' => false,
    'export-json' => null,
], [
    'h' => 'help',
    'i' => 'idnumber',
    'a' => 'application-id',
    'v' => 'vacancy-id',
    's' => 'status',
    'l' => 'list',
    'd' => 'delete',
    'n' => 'dryrun',
    'V' => 'verbose',
    'f' => 'force',
    'r' => 'reason',
    'A' => 'all-for-user',
    'D' => 'show-documents',
    'H' => 'show-history',
    'e' => 'export-json',
]);

if (!empty($unrecognized)) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
==============================================================================
ISER Job Board - Application Management CLI v1.0
==============================================================================

Comprehensive tool for managing and deleting job applications from the system.
Supports deletion by application ID or user idnumber with full cascade.

USAGE:
  php delete_applications.php [options]

IDENTIFICATION OPTIONS (at least one required):
  -i, --idnumber=ID         User idnumber to identify the applicant
                            Can specify multiple IDs separated by comma
  -a, --application-id=ID   Specific application ID(s) to process
                            Can specify multiple IDs separated by comma

FILTER OPTIONS:
  -v, --vacancy-id=ID       Filter by vacancy ID
  -s, --status=STATUS       Filter by application status
                            Values: draft, submitted, pending_dean_review,
                            dean_approved, dean_rejected, pending_hr_validation,
                            hr_validated, hr_rejected, withdrawn

ACTION OPTIONS:
  -l, --list                List applications (default if no action specified)
  -d, --delete              Delete matching applications
  -n, --dryrun              Simulate deletion without making changes
  -f, --force               Skip confirmation prompt (use with caution!)
  -r, --reason=TEXT         Reason for deletion (for audit log)
                            Default: "CLI deletion by administrator"
  -A, --all-for-user        Delete ALL applications for the user (requires --idnumber)

DISPLAY OPTIONS:
  -V, --verbose             Show detailed output
  -D, --show-documents      Show documents for each application
  -H, --show-history        Show workflow history for each application
  -e, --export-json=FILE    Export application data to JSON before deletion

GENERAL OPTIONS:
  -h, --help                Show this help message

DATA DELETED (CASCADE):
  When deleting an application, the following data is removed:
  1. Document validation records (local_jobboard_doc_validation)
  2. Document files from Moodle file storage
  3. Document records (local_jobboard_document)
  4. Workflow history logs (local_jobboard_workflow_log)
  5. Evaluation records (local_jobboard_evaluation) if table exists
  6. Related notifications (local_jobboard_notification)
  7. The application record itself (local_jobboard_application)

  An audit log entry is created for each deletion.

EXAMPLES:

  # List all applications for a user by idnumber
  php delete_applications.php --list --idnumber=1234567890

  # List applications with documents and history
  php delete_applications.php --list --idnumber=1234567890 --show-documents --show-history

  # List applications for multiple users
  php delete_applications.php --list --idnumber=1234567890,0987654321

  # Preview deletion (dry run)
  php delete_applications.php --delete --idnumber=1234567890 --dryrun --verbose

  # Delete a specific application by ID
  php delete_applications.php --delete --application-id=123 --reason="User requested deletion"

  # Delete multiple applications by ID
  php delete_applications.php --delete --application-id=123,124,125

  # Delete applications for a user in a specific vacancy
  php delete_applications.php --delete --idnumber=1234567890 --vacancy-id=42

  # Delete applications with a specific status
  php delete_applications.php --delete --idnumber=1234567890 --status=draft

  # Force delete without confirmation
  php delete_applications.php --delete --idnumber=1234567890 --force

  # Export to JSON before deletion
  php delete_applications.php --delete --idnumber=1234567890 --export-json=backup.json

  # Delete ALL applications for a user (dangerous!)
  php delete_applications.php --delete --idnumber=1234567890 --all-for-user --force

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Validate required parameters.
if (empty($options['idnumber']) && empty($options['application-id'])) {
    cli_error("You must specify --idnumber or --application-id. Use --help for usage.");
}

// Determine action.
$dolist = $options['list'];
$dodelete = $options['delete'];

// If no action specified, default to list.
if (!$dolist && !$dodelete) {
    $dolist = true;
}

$verbose = $options['verbose'];
$dryrun = $options['dryrun'];
$force = $options['force'];
$showdocs = $options['show-documents'];
$showhistory = $options['show-history'];
$reason = $options['reason'];

/**
 * Display a formatted table header.
 *
 * @param array $columns Column definitions with widths.
 */
function display_table_header(array $columns): void {
    $line = '';
    $header = '';
    foreach ($columns as $col) {
        $header .= str_pad($col['title'], $col['width']) . ' | ';
        $line .= str_repeat('-', $col['width']) . '-+-';
    }
    echo rtrim($line, '+-') . "\n";
    echo rtrim($header, ' | ') . "\n";
    echo rtrim($line, '+-') . "\n";
}

/**
 * Display a table row.
 *
 * @param array $values Values for each column.
 * @param array $columns Column definitions with widths.
 */
function display_table_row(array $values, array $columns): void {
    $row = '';
    $i = 0;
    foreach ($columns as $col) {
        $value = $values[$i] ?? '';
        if (strlen($value) > $col['width'] - 3) {
            $value = substr($value, 0, $col['width'] - 3) . '...';
        }
        $row .= str_pad($value, $col['width']) . ' | ';
        $i++;
    }
    echo rtrim($row, ' | ') . "\n";
}

/**
 * Get applications based on criteria.
 *
 * @param array $options CLI options.
 * @return array Array of application records.
 */
function get_applications(array $options): array {
    global $DB;

    $params = [];
    $where = ['1=1'];

    // Filter by application ID(s).
    if (!empty($options['application-id'])) {
        $appids = array_map('intval', explode(',', $options['application-id']));
        list($insql, $inparams) = $DB->get_in_or_equal($appids, SQL_PARAMS_NAMED, 'appid');
        $where[] = "a.id $insql";
        $params = array_merge($params, $inparams);
    }

    // Filter by user idnumber(s).
    if (!empty($options['idnumber'])) {
        $idnumbers = array_map('trim', explode(',', $options['idnumber']));
        list($insql, $inparams) = $DB->get_in_or_equal($idnumbers, SQL_PARAMS_NAMED, 'idn');
        $where[] = "u.idnumber $insql";
        $params = array_merge($params, $inparams);
    }

    // Filter by vacancy ID.
    if (!empty($options['vacancy-id'])) {
        $where[] = 'a.vacancyid = :vacancyid';
        $params['vacancyid'] = (int) $options['vacancy-id'];
    }

    // Filter by status.
    if (!empty($options['status'])) {
        $where[] = 'a.status = :status';
        $params['status'] = $options['status'];
    }

    $wheresql = implode(' AND ', $where);

    $sql = "SELECT a.*,
                   v.code as vacancy_code,
                   v.title as vacancy_title,
                   v.location as vacancy_location,
                   v.modality as vacancy_modality,
                   v.status as vacancy_status,
                   u.id as user_id,
                   u.firstname,
                   u.lastname,
                   u.email,
                   u.idnumber as user_idnumber,
                   c.name as convocatoria_name,
                   c.code as convocatoria_code
            FROM {local_jobboard_application} a
            JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
            JOIN {user} u ON u.id = a.userid
            LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
            WHERE $wheresql
            ORDER BY u.idnumber, a.timecreated DESC";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Get documents for an application.
 *
 * @param int $applicationid Application ID.
 * @return array Array of document records.
 */
function get_application_documents(int $applicationid): array {
    global $DB;

    $sql = "SELECT d.*,
                   dt.name as doctype_name,
                   dt.category as doctype_category,
                   dv.status as validation_status,
                   dv.notes as validation_notes,
                   dv.rejectreason
            FROM {local_jobboard_document} d
            LEFT JOIN {local_jobboard_doctype} dt ON dt.code = d.documenttype
            LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
            WHERE d.applicationid = :applicationid
            ORDER BY d.timecreated ASC";

    return $DB->get_records_sql($sql, ['applicationid' => $applicationid]);
}

/**
 * Get workflow history for an application.
 *
 * @param int $applicationid Application ID.
 * @return array Array of workflow log records.
 */
function get_workflow_history(int $applicationid): array {
    global $DB;

    $sql = "SELECT wl.*,
                   u.firstname,
                   u.lastname
            FROM {local_jobboard_workflow_log} wl
            LEFT JOIN {user} u ON u.id = wl.changedby
            WHERE wl.applicationid = :applicationid
            ORDER BY wl.timecreated DESC";

    return $DB->get_records_sql($sql, ['applicationid' => $applicationid]);
}

/**
 * Count related records for an application.
 *
 * @param int $applicationid Application ID.
 * @return array Counts of related records.
 */
function count_related_records(int $applicationid): array {
    global $DB;

    $counts = [
        'documents' => 0,
        'validations' => 0,
        'workflow_logs' => 0,
        'evaluations' => 0,
        'notifications' => 0,
        'files' => 0,
    ];

    // Count documents.
    $counts['documents'] = $DB->count_records('local_jobboard_document', ['applicationid' => $applicationid]);

    // Count validations.
    $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $applicationid], '', 'id');
    if (!empty($documents)) {
        $docids = array_keys($documents);
        list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
        $counts['validations'] = $DB->count_records_select('local_jobboard_doc_validation', "documentid $insql", $params);
    }

    // Count workflow logs.
    $counts['workflow_logs'] = $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $applicationid]);

    // Count evaluations (if table exists).
    if ($DB->get_manager()->table_exists('local_jobboard_evaluation')) {
        $counts['evaluations'] = $DB->count_records('local_jobboard_evaluation', ['applicationid' => $applicationid]);
    }

    // Count files.
    $fs = get_file_storage();
    $context = context_system::instance();
    foreach ($documents as $doc) {
        $files = $fs->get_area_files($context->id, 'local_jobboard', 'application_documents', $doc->id, 'id', false);
        $counts['files'] += count($files);
    }

    return $counts;
}

/**
 * Delete an application with full cascade.
 *
 * @param object $app Application record.
 * @param string $reason Reason for deletion.
 * @param bool $dryrun If true, don't actually delete.
 * @param bool $verbose Show detailed output.
 * @return array Statistics of deleted records.
 */
function delete_application_cascade(object $app, string $reason, bool $dryrun, bool $verbose): array {
    global $DB, $USER;

    $stats = [
        'documents' => 0,
        'validations' => 0,
        'workflow_logs' => 0,
        'evaluations' => 0,
        'notifications' => 0,
        'files' => 0,
        'application' => 0,
    ];

    $fs = get_file_storage();
    $context = context_system::instance();

    // Get documents.
    $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $app->id]);

    if (!$dryrun) {
        // 1. Delete document validations.
        if (!empty($documents)) {
            $docids = array_keys($documents);
            list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
            $stats['validations'] = $DB->count_records_select('local_jobboard_doc_validation', "documentid $insql", $params);
            $DB->delete_records_select('local_jobboard_doc_validation', "documentid $insql", $params);
            if ($verbose) {
                echo "    - Deleted {$stats['validations']} document validation(s)\n";
            }
        }

        // 2. Delete document files from storage.
        foreach ($documents as $doc) {
            $files = $fs->get_area_files($context->id, 'local_jobboard', 'application_documents', $doc->id, 'id', false);
            foreach ($files as $file) {
                $file->delete();
                $stats['files']++;
            }
        }
        if ($verbose && $stats['files'] > 0) {
            echo "    - Deleted {$stats['files']} file(s) from storage\n";
        }

        // 3. Delete document records.
        $stats['documents'] = count($documents);
        $DB->delete_records('local_jobboard_document', ['applicationid' => $app->id]);
        if ($verbose && $stats['documents'] > 0) {
            echo "    - Deleted {$stats['documents']} document record(s)\n";
        }

        // 4. Delete workflow logs.
        $stats['workflow_logs'] = $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);
        $DB->delete_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);
        if ($verbose && $stats['workflow_logs'] > 0) {
            echo "    - Deleted {$stats['workflow_logs']} workflow log(s)\n";
        }

        // 5. Delete evaluations (if table exists).
        if ($DB->get_manager()->table_exists('local_jobboard_evaluation')) {
            $stats['evaluations'] = $DB->count_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
            if ($stats['evaluations'] > 0) {
                $DB->delete_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
                if ($verbose) {
                    echo "    - Deleted {$stats['evaluations']} evaluation(s)\n";
                }
            }
        }

        // 6. Delete related notifications.
        try {
            // Notifications may reference applicationid in JSON data field.
            $likeparam = '%"applicationid":' . $app->id . '%';
            $notifcount = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {local_jobboard_notification}
                 WHERE userid = :userid AND " . $DB->sql_like('data', ':pattern'),
                ['userid' => $app->userid, 'pattern' => $likeparam]
            );
            if ($notifcount > 0) {
                $sql = "DELETE FROM {local_jobboard_notification}
                        WHERE userid = ? AND " . $DB->sql_like('data', '?');
                $DB->execute($sql, [$app->userid, $likeparam]);
                $stats['notifications'] = $notifcount;
                if ($verbose) {
                    echo "    - Deleted {$stats['notifications']} notification(s)\n";
                }
            }
        } catch (Exception $e) {
            if ($verbose) {
                echo "    - Note: Could not process notifications ({$e->getMessage()})\n";
            }
        }

        // 7. Capture full state for audit before deletion.
        $previousstate = [
            'id' => $app->id,
            'vacancyid' => $app->vacancyid,
            'userid' => $app->userid,
            'status' => $app->status,
            'vacancy_code' => $app->vacancy_code,
            'vacancy_title' => $app->vacancy_title,
            'user_idnumber' => $app->user_idnumber,
            'user_name' => $app->firstname . ' ' . $app->lastname,
            'user_email' => $app->email,
            'timecreated' => $app->timecreated,
            'documents_deleted' => $stats['documents'],
            'files_deleted' => $stats['files'],
            'reason' => $reason,
        ];

        // 8. Delete the application record.
        $DB->delete_records('local_jobboard_application', ['id' => $app->id]);
        $stats['application'] = 1;

        // 9. Log audit entry.
        audit::log(
            audit::ACTION_DELETE,
            audit::ENTITY_APPLICATION,
            $app->id,
            [
                'vacancyid' => $app->vacancyid,
                'userid' => $app->userid,
                'user_idnumber' => $app->user_idnumber,
                'reason' => $reason,
                'deleted_via' => 'CLI',
                'deletedby' => $USER->id,
            ],
            $previousstate,
            null
        );

        // 10. Trigger event.
        try {
            $event = \local_jobboard\event\application_deleted::create([
                'objectid' => $app->id,
                'context' => context_system::instance(),
                'userid' => $USER->id,
                'other' => [
                    'vacancyid' => $app->vacancyid,
                    'applicantuserid' => $app->userid,
                    'user_idnumber' => $app->user_idnumber,
                    'reason' => $reason,
                    'via' => 'CLI',
                ],
            ]);
            $event->trigger();
        } catch (Exception $e) {
            // Event triggering is not critical.
            if ($verbose) {
                echo "    - Note: Could not trigger event ({$e->getMessage()})\n";
            }
        }

    } else {
        // Dry run - just count.
        $stats['documents'] = count($documents);

        if (!empty($documents)) {
            $docids = array_keys($documents);
            list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
            $stats['validations'] = $DB->count_records_select('local_jobboard_doc_validation', "documentid $insql", $params);
        }

        foreach ($documents as $doc) {
            $files = $fs->get_area_files($context->id, 'local_jobboard', 'application_documents', $doc->id, 'id', false);
            $stats['files'] += count($files);
        }

        $stats['workflow_logs'] = $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);

        if ($DB->get_manager()->table_exists('local_jobboard_evaluation')) {
            $stats['evaluations'] = $DB->count_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
        }

        try {
            $likeparam = '%"applicationid":' . $app->id . '%';
            $stats['notifications'] = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {local_jobboard_notification}
                 WHERE userid = :userid AND " . $DB->sql_like('data', ':pattern'),
                ['userid' => $app->userid, 'pattern' => $likeparam]
            );
        } catch (Exception $e) {
            // Ignore.
        }

        $stats['application'] = 1;
    }

    return $stats;
}

/**
 * Export applications to JSON.
 *
 * @param array $applications Array of application records.
 * @param string $filename Output filename.
 */
function export_to_json(array $applications, string $filename): void {
    global $DB;

    $export = [];

    foreach ($applications as $app) {
        $appdata = [
            'id' => $app->id,
            'vacancy' => [
                'id' => $app->vacancyid,
                'code' => $app->vacancy_code,
                'title' => $app->vacancy_title,
                'location' => $app->vacancy_location,
                'modality' => $app->vacancy_modality,
            ],
            'applicant' => [
                'id' => $app->user_id,
                'idnumber' => $app->user_idnumber,
                'firstname' => $app->firstname,
                'lastname' => $app->lastname,
                'email' => $app->email,
            ],
            'status' => $app->status,
            'statusnotes' => $app->statusnotes,
            'isexemption' => (bool) $app->isexemption,
            'consentgiven' => (bool) $app->consentgiven,
            'consenttimestamp' => $app->consenttimestamp ? date('Y-m-d H:i:s', $app->consenttimestamp) : null,
            'coverletter' => $app->coverletter,
            'timecreated' => date('Y-m-d H:i:s', $app->timecreated),
            'timemodified' => $app->timemodified ? date('Y-m-d H:i:s', $app->timemodified) : null,
            'documents' => [],
            'workflow_history' => [],
        ];

        // Add documents.
        $documents = get_application_documents($app->id);
        foreach ($documents as $doc) {
            $appdata['documents'][] = [
                'id' => $doc->id,
                'type' => $doc->documenttype,
                'type_name' => $doc->doctype_name,
                'filename' => $doc->filename,
                'filesize' => $doc->filesize,
                'mimetype' => $doc->mimetype,
                'validation_status' => $doc->validation_status,
                'validation_notes' => $doc->validation_notes,
                'timecreated' => date('Y-m-d H:i:s', $doc->timecreated),
            ];
        }

        // Add workflow history.
        $history = get_workflow_history($app->id);
        foreach ($history as $entry) {
            $appdata['workflow_history'][] = [
                'previous_status' => $entry->previousstatus,
                'new_status' => $entry->newstatus,
                'changed_by' => $entry->firstname . ' ' . $entry->lastname,
                'comments' => $entry->comments,
                'timestamp' => date('Y-m-d H:i:s', $entry->timecreated),
            ];
        }

        $export[] = $appdata;
    }

    $json = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($filename, $json);
    echo "Exported " . count($applications) . " application(s) to: $filename\n\n";
}

// ============================================================================
// MAIN EXECUTION
// ============================================================================

cli_heading('ISER Job Board - Application Management CLI v1.0');

// Get applications.
$applications = get_applications($options);

if (empty($applications)) {
    echo "No applications found matching the criteria.\n";
    exit(0);
}

$totalapps = count($applications);

// Group by user for display.
$byuser = [];
foreach ($applications as $app) {
    $key = $app->user_idnumber ?: 'no_idnumber_' . $app->user_id;
    if (!isset($byuser[$key])) {
        $byuser[$key] = [
            'user' => [
                'id' => $app->user_id,
                'idnumber' => $app->user_idnumber,
                'name' => $app->firstname . ' ' . $app->lastname,
                'email' => $app->email,
            ],
            'applications' => [],
        ];
    }
    $byuser[$key]['applications'][] = $app;
}

echo "Found $totalapps application(s) for " . count($byuser) . " user(s)\n\n";

// Export to JSON if requested.
if (!empty($options['export-json'])) {
    export_to_json($applications, $options['export-json']);
}

// ============================================================================
// LIST MODE
// ============================================================================

if ($dolist || ($dodelete && $verbose)) {
    foreach ($byuser as $userkey => $userdata) {
        echo str_repeat('=', 80) . "\n";
        echo "User: {$userdata['user']['name']}\n";
        echo "  ID Number: {$userdata['user']['idnumber']}\n";
        echo "  Email: {$userdata['user']['email']}\n";
        echo "  Moodle User ID: {$userdata['user']['id']}\n";
        echo "  Applications: " . count($userdata['applications']) . "\n";
        echo str_repeat('-', 80) . "\n";

        $columns = [
            ['title' => 'App ID', 'width' => 7],
            ['title' => 'Status', 'width' => 20],
            ['title' => 'Vacancy Code', 'width' => 15],
            ['title' => 'Vacancy Title', 'width' => 30],
            ['title' => 'Location', 'width' => 15],
            ['title' => 'Created', 'width' => 19],
        ];

        display_table_header($columns);

        foreach ($userdata['applications'] as $app) {
            display_table_row([
                $app->id,
                $app->status,
                $app->vacancy_code,
                $app->vacancy_title,
                $app->vacancy_location,
                date('Y-m-d H:i:s', $app->timecreated),
            ], $columns);

            // Show documents.
            if ($showdocs) {
                $documents = get_application_documents($app->id);
                if (!empty($documents)) {
                    echo "  Documents (" . count($documents) . "):\n";
                    foreach ($documents as $doc) {
                        $status = $doc->validation_status ?? 'pending';
                        $size = round($doc->filesize / 1024, 1) . ' KB';
                        echo "    - [{$status}] {$doc->documenttype}: {$doc->filename} ({$size})\n";
                    }
                }
            }

            // Show workflow history.
            if ($showhistory) {
                $history = get_workflow_history($app->id);
                if (!empty($history)) {
                    echo "  Workflow History:\n";
                    foreach ($history as $entry) {
                        $date = date('Y-m-d H:i', $entry->timecreated);
                        $by = $entry->firstname . ' ' . $entry->lastname;
                        echo "    - [$date] {$entry->previousstatus} -> {$entry->newstatus} (by $by)\n";
                    }
                }
            }

            // Show related record counts.
            if ($verbose) {
                $counts = count_related_records($app->id);
                echo "  Related Records: ";
                echo "Docs: {$counts['documents']}, ";
                echo "Validations: {$counts['validations']}, ";
                echo "Workflow: {$counts['workflow_logs']}, ";
                echo "Files: {$counts['files']}\n";
            }
        }

        echo "\n";
    }
}

// ============================================================================
// DELETE MODE
// ============================================================================

if ($dodelete) {
    echo str_repeat('=', 80) . "\n";
    echo $dryrun ? "DRY RUN - DELETION PREVIEW\n" : "DELETION MODE\n";
    echo str_repeat('=', 80) . "\n";
    echo "Applications to delete: $totalapps\n";
    echo "Reason: $reason\n";

    if ($dryrun) {
        echo "\n*** DRY RUN - No changes will be made ***\n";
    }

    // Confirmation (unless force mode).
    if (!$force && !$dryrun) {
        echo "\n";
        echo "WARNING: This will permanently delete $totalapps application(s) and ALL related data!\n";
        echo "This action CANNOT be undone.\n\n";

        echo "Type 'DELETE' to confirm: ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim($line) !== 'DELETE') {
            echo "\nDeletion cancelled.\n";
            exit(0);
        }
        echo "\n";
    }

    // Process deletions.
    $totals = [
        'applications' => 0,
        'documents' => 0,
        'validations' => 0,
        'workflow_logs' => 0,
        'evaluations' => 0,
        'notifications' => 0,
        'files' => 0,
    ];

    foreach ($applications as $app) {
        echo "Processing Application ID: {$app->id}\n";
        echo "  User: {$app->firstname} {$app->lastname} ({$app->user_idnumber})\n";
        echo "  Vacancy: {$app->vacancy_code} - {$app->vacancy_title}\n";
        echo "  Status: {$app->status}\n";

        $stats = delete_application_cascade($app, $reason, $dryrun, $verbose);

        foreach ($stats as $key => $value) {
            if (isset($totals[$key])) {
                $totals[$key] += $value;
            }
        }

        if ($dryrun) {
            echo "  -> Would DELETE this application\n";
        } else {
            echo "  -> DELETED\n";
        }
        echo "\n";
    }

    // Summary.
    echo str_repeat('=', 80) . "\n";
    echo $dryrun ? "DRY RUN SUMMARY (no changes made):\n" : "DELETION SUMMARY:\n";
    echo str_repeat('=', 80) . "\n";
    printf("  Applications:        %d\n", $totals['applications']);
    printf("  Documents:           %d\n", $totals['documents']);
    printf("  Document Validations: %d\n", $totals['validations']);
    printf("  Workflow Logs:       %d\n", $totals['workflow_logs']);
    printf("  Evaluations:         %d\n", $totals['evaluations']);
    printf("  Notifications:       %d\n", $totals['notifications']);
    printf("  Files Removed:       %d\n", $totals['files']);
    echo str_repeat('=', 80) . "\n";

    if ($dryrun) {
        echo "\n*** DRY RUN - Run without --dryrun to actually delete ***\n";
    } else {
        echo "\n=== DELETION COMPLETE ===\n";
        echo "All deletions have been logged in the audit table.\n";
    }
}

exit(0);
