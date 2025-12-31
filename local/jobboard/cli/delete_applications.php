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
 * CLI script for COMPLETE deletion of job applications.
 *
 * This script provides TOTAL elimination of applications with NO traces left:
 * - Interviewers and interviews
 * - Document validations
 * - Document files from Moodle storage
 * - Document records
 * - Workflow history logs
 * - Evaluation records
 * - Notification records
 * - Audit log entries (optional with --purge-audit)
 * - The application record itself
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

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
    'reason' => 'CLI complete deletion by administrator',
    'purge-audit' => false,
    'show-documents' => false,
    'show-history' => false,
    'show-interviews' => false,
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
    'P' => 'purge-audit',
    'D' => 'show-documents',
    'H' => 'show-history',
    'I' => 'show-interviews',
    'e' => 'export-json',
]);

if (!empty($unrecognized)) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
================================================================================
ISER Job Board - COMPLETE Application Deletion CLI v2.0
================================================================================

TOTAL ELIMINATION of job applications with NO TRACES LEFT in the database.
This script removes ALL related records from ALL tables.

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
  -d, --delete              Delete matching applications COMPLETELY
  -n, --dryrun              Simulate deletion without making changes
  -f, --force               Skip confirmation prompt (use with caution!)
  -r, --reason=TEXT         Reason for deletion (logged before purge)
                            Default: "CLI complete deletion by administrator"
  -P, --purge-audit         ALSO delete audit log entries (complete purge)
                            Without this flag, audit entries are preserved

DISPLAY OPTIONS:
  -V, --verbose             Show detailed output for each deletion step
  -D, --show-documents      Show documents for each application
  -H, --show-history        Show workflow history for each application
  -I, --show-interviews     Show scheduled interviews for each application
  -e, --export-json=FILE    Export application data to JSON before deletion

GENERAL OPTIONS:
  -h, --help                Show this help message

================================================================================
DATA DELETED (COMPLETE CASCADE - NO TRACES LEFT):
================================================================================

  1. INTERVIEWERS        (local_jobboard_interviewer)
     - Panel members assigned to interviews

  2. INTERVIEWS          (local_jobboard_interview)
     - Scheduled interviews, feedback, ratings

  3. DOCUMENT VALIDATIONS (local_jobboard_doc_validation)
     - Validation status, reviewer notes, rejection reasons

  4. DOCUMENT FILES      (Moodle file storage)
     - Physical files: PDFs, images, etc.

  5. DOCUMENTS           (local_jobboard_document)
     - Document metadata records

  6. WORKFLOW LOGS       (local_jobboard_workflow_log)
     - Status change history

  7. EVALUATIONS         (local_jobboard_evaluation)
     - If table exists, evaluation scores

  8. NOTIFICATIONS       (local_jobboard_notification)
     - Email notifications sent/pending

  9. AUDIT LOGS          (local_jobboard_audit) [with --purge-audit]
     - Create, update, delete, transition logs
     - Document validation audit entries

  10. APPLICATION        (local_jobboard_application)
      - The application record itself

================================================================================
EXAMPLES:
================================================================================

  # List all applications for a user by idnumber
  php delete_applications.php --list --idnumber=1234567890 --verbose

  # List with all details (documents, history, interviews)
  php delete_applications.php -l -i 1234567890 -D -H -I -V

  # Preview COMPLETE deletion (dry run)
  php delete_applications.php --delete --idnumber=1234567890 --dryrun --verbose

  # Delete a specific application by ID (keeps audit logs)
  php delete_applications.php --delete --application-id=123

  # TOTAL PURGE - Delete application AND all audit records
  php delete_applications.php --delete --application-id=123 --purge-audit --force

  # Delete multiple applications completely
  php delete_applications.php --delete --application-id=123,124,125 --purge-audit

  # Delete all applications for a user in a specific vacancy
  php delete_applications.php --delete --idnumber=1234567890 --vacancy-id=42 --purge-audit

  # Export backup to JSON, then delete completely
  php delete_applications.php --delete --idnumber=1234567890 --export-json=backup.json --purge-audit

  # Force delete without confirmation (DANGEROUS!)
  php delete_applications.php --delete --idnumber=1234567890 --purge-audit --force

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
$purgeaudit = $options['purge-audit'];
$showdocs = $options['show-documents'];
$showhistory = $options['show-history'];
$showinterviews = $options['show-interviews'];
$reason = $options['reason'];

/**
 * Display a formatted table header.
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
 */
function get_applications(array $options): array {
    global $DB;

    $params = [];
    $where = ['1=1'];

    if (!empty($options['application-id'])) {
        $appids = array_map('intval', explode(',', $options['application-id']));
        list($insql, $inparams) = $DB->get_in_or_equal($appids, SQL_PARAMS_NAMED, 'appid');
        $where[] = "a.id $insql";
        $params = array_merge($params, $inparams);
    }

    if (!empty($options['idnumber'])) {
        $idnumbers = array_map('trim', explode(',', $options['idnumber']));
        list($insql, $inparams) = $DB->get_in_or_equal($idnumbers, SQL_PARAMS_NAMED, 'idn');
        $where[] = "u.idnumber $insql";
        $params = array_merge($params, $inparams);
    }

    if (!empty($options['vacancy-id'])) {
        $where[] = 'a.vacancyid = :vacancyid';
        $params['vacancyid'] = (int) $options['vacancy-id'];
    }

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
 * Get interviews for an application.
 */
function get_application_interviews(int $applicationid): array {
    global $DB;

    $sql = "SELECT i.*,
                   (SELECT COUNT(*) FROM {local_jobboard_interviewer} iv WHERE iv.interviewid = i.id) as interviewer_count
            FROM {local_jobboard_interview} i
            WHERE i.applicationid = :applicationid
            ORDER BY i.scheduledtime DESC";

    return $DB->get_records_sql($sql, ['applicationid' => $applicationid]);
}

/**
 * Count ALL related records for an application (complete cascade).
 */
function count_all_related_records(int $applicationid, bool $includeaudit = false): array {
    global $DB;

    $counts = [
        'interviewers' => 0,
        'interviews' => 0,
        'documents' => 0,
        'validations' => 0,
        'workflow_logs' => 0,
        'evaluations' => 0,
        'notifications' => 0,
        'audit_logs' => 0,
        'files' => 0,
    ];

    // Get interviews.
    $interviews = $DB->get_records('local_jobboard_interview', ['applicationid' => $applicationid], '', 'id');
    $counts['interviews'] = count($interviews);

    // Count interviewers.
    if (!empty($interviews)) {
        $intids = array_keys($interviews);
        list($insql, $params) = $DB->get_in_or_equal($intids, SQL_PARAMS_NAMED);
        $counts['interviewers'] = $DB->count_records_select('local_jobboard_interviewer', "interviewid $insql", $params);
    }

    // Get documents.
    $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $applicationid], '', 'id');
    $counts['documents'] = count($documents);

    // Count validations.
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

    // Count notifications.
    try {
        $likeparam = '%"applicationid":' . $applicationid . '%';
        $counts['notifications'] = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_notification} WHERE " . $DB->sql_like('data', ':pattern'),
            ['pattern' => $likeparam]
        );
    } catch (Exception $e) {
        // Table structure may differ.
    }

    // Count audit logs if requested.
    if ($includeaudit) {
        // Application audit entries.
        $counts['audit_logs'] += $DB->count_records('local_jobboard_audit', [
            'entitytype' => 'application',
            'entityid' => $applicationid,
        ]);

        // Document audit entries.
        if (!empty($documents)) {
            $docids = array_keys($documents);
            list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
            $params['entitytype'] = 'document';
            $counts['audit_logs'] += $DB->count_records_select(
                'local_jobboard_audit',
                "entitytype = :entitytype AND entityid $insql",
                $params
            );
        }
    }

    return $counts;
}

/**
 * COMPLETE deletion of an application - NO TRACES LEFT.
 */
function delete_application_complete(object $app, string $reason, bool $dryrun, bool $verbose, bool $purgeaudit): array {
    global $DB, $USER;

    $stats = [
        'interviewers' => 0,
        'interviews' => 0,
        'documents' => 0,
        'validations' => 0,
        'workflow_logs' => 0,
        'evaluations' => 0,
        'notifications' => 0,
        'audit_logs' => 0,
        'files' => 0,
        'application' => 0,
    ];

    $fs = get_file_storage();
    $context = context_system::instance();

    // Get all related data first.
    $interviews = $DB->get_records('local_jobboard_interview', ['applicationid' => $app->id]);
    $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $app->id]);

    if (!$dryrun) {
        // ================================================================
        // 1. DELETE INTERVIEWERS (panel members)
        // ================================================================
        if (!empty($interviews)) {
            $intids = array_keys($interviews);
            list($insql, $params) = $DB->get_in_or_equal($intids, SQL_PARAMS_NAMED);
            $stats['interviewers'] = $DB->count_records_select('local_jobboard_interviewer', "interviewid $insql", $params);
            $DB->delete_records_select('local_jobboard_interviewer', "interviewid $insql", $params);
            if ($verbose && $stats['interviewers'] > 0) {
                echo "    [1/10] Deleted {$stats['interviewers']} interviewer(s)\n";
            }
        }

        // ================================================================
        // 2. DELETE INTERVIEWS
        // ================================================================
        $stats['interviews'] = count($interviews);
        if ($stats['interviews'] > 0) {
            $DB->delete_records('local_jobboard_interview', ['applicationid' => $app->id]);
            if ($verbose) {
                echo "    [2/10] Deleted {$stats['interviews']} interview(s)\n";
            }
        }

        // ================================================================
        // 3. DELETE DOCUMENT VALIDATIONS
        // ================================================================
        if (!empty($documents)) {
            $docids = array_keys($documents);
            list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
            $stats['validations'] = $DB->count_records_select('local_jobboard_doc_validation', "documentid $insql", $params);
            $DB->delete_records_select('local_jobboard_doc_validation', "documentid $insql", $params);
            if ($verbose && $stats['validations'] > 0) {
                echo "    [3/10] Deleted {$stats['validations']} document validation(s)\n";
            }
        }

        // ================================================================
        // 4. DELETE DOCUMENT FILES FROM MOODLE STORAGE
        // ================================================================
        foreach ($documents as $doc) {
            $files = $fs->get_area_files($context->id, 'local_jobboard', 'application_documents', $doc->id, 'id', false);
            foreach ($files as $file) {
                $file->delete();
                $stats['files']++;
            }
        }
        if ($verbose && $stats['files'] > 0) {
            echo "    [4/10] Deleted {$stats['files']} physical file(s) from storage\n";
        }

        // ================================================================
        // 5. DELETE DOCUMENT RECORDS
        // ================================================================
        $stats['documents'] = count($documents);
        if ($stats['documents'] > 0) {
            // First delete audit entries for documents if purging.
            if ($purgeaudit && !empty($documents)) {
                $docids = array_keys($documents);
                list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
                $params['entitytype'] = 'document';
                $docauditcount = $DB->count_records_select(
                    'local_jobboard_audit',
                    "entitytype = :entitytype AND entityid $insql",
                    $params
                );
                $DB->delete_records_select(
                    'local_jobboard_audit',
                    "entitytype = :entitytype AND entityid $insql",
                    $params
                );
                $stats['audit_logs'] += $docauditcount;
            }

            $DB->delete_records('local_jobboard_document', ['applicationid' => $app->id]);
            if ($verbose) {
                echo "    [5/10] Deleted {$stats['documents']} document record(s)\n";
            }
        }

        // ================================================================
        // 6. DELETE WORKFLOW LOGS
        // ================================================================
        $stats['workflow_logs'] = $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);
        if ($stats['workflow_logs'] > 0) {
            $DB->delete_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);
            if ($verbose) {
                echo "    [6/10] Deleted {$stats['workflow_logs']} workflow log(s)\n";
            }
        }

        // ================================================================
        // 7. DELETE EVALUATIONS (if table exists)
        // ================================================================
        if ($DB->get_manager()->table_exists('local_jobboard_evaluation')) {
            $stats['evaluations'] = $DB->count_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
            if ($stats['evaluations'] > 0) {
                $DB->delete_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
                if ($verbose) {
                    echo "    [7/10] Deleted {$stats['evaluations']} evaluation(s)\n";
                }
            }
        }

        // ================================================================
        // 8. DELETE NOTIFICATIONS
        // ================================================================
        try {
            // Notifications reference applicationid in JSON data field.
            $likeparam = '%"applicationid":' . $app->id . '%';
            $notifcount = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {local_jobboard_notification} WHERE " . $DB->sql_like('data', ':pattern'),
                ['pattern' => $likeparam]
            );
            if ($notifcount > 0) {
                $sql = "DELETE FROM {local_jobboard_notification} WHERE " . $DB->sql_like('data', '?');
                $DB->execute($sql, [$likeparam]);
                $stats['notifications'] = $notifcount;
                if ($verbose) {
                    echo "    [8/10] Deleted {$stats['notifications']} notification(s)\n";
                }
            }
        } catch (Exception $e) {
            if ($verbose) {
                echo "    [8/10] Note: Could not process notifications ({$e->getMessage()})\n";
            }
        }

        // ================================================================
        // 9. DELETE AUDIT LOGS (if --purge-audit)
        // ================================================================
        if ($purgeaudit) {
            // Delete application audit entries.
            $appauditcount = $DB->count_records('local_jobboard_audit', [
                'entitytype' => 'application',
                'entityid' => $app->id,
            ]);
            if ($appauditcount > 0) {
                $DB->delete_records('local_jobboard_audit', [
                    'entitytype' => 'application',
                    'entityid' => $app->id,
                ]);
                $stats['audit_logs'] += $appauditcount;
            }

            // Also delete any audit entries that reference this application in extradata.
            try {
                $likeparam = '%"applicationid":' . $app->id . '%';
                $extraaudit = $DB->count_records_sql(
                    "SELECT COUNT(*) FROM {local_jobboard_audit} WHERE " . $DB->sql_like('extradata', ':pattern'),
                    ['pattern' => $likeparam]
                );
                if ($extraaudit > 0) {
                    $sql = "DELETE FROM {local_jobboard_audit} WHERE " . $DB->sql_like('extradata', '?');
                    $DB->execute($sql, [$likeparam]);
                    $stats['audit_logs'] += $extraaudit;
                }
            } catch (Exception $e) {
                // Ignore.
            }

            if ($verbose && $stats['audit_logs'] > 0) {
                echo "    [9/10] PURGED {$stats['audit_logs']} audit log(s)\n";
            }
        } else {
            if ($verbose) {
                echo "    [9/10] Audit logs PRESERVED (use --purge-audit to remove)\n";
            }
        }

        // ================================================================
        // 10. DELETE THE APPLICATION RECORD
        // ================================================================
        $DB->delete_records('local_jobboard_application', ['id' => $app->id]);
        $stats['application'] = 1;
        if ($verbose) {
            echo "    [10/10] DELETED application record ID: {$app->id}\n";
        }

    } else {
        // ================================================================
        // DRY RUN - Just count everything
        // ================================================================

        // Count interviewers.
        if (!empty($interviews)) {
            $intids = array_keys($interviews);
            list($insql, $params) = $DB->get_in_or_equal($intids, SQL_PARAMS_NAMED);
            $stats['interviewers'] = $DB->count_records_select('local_jobboard_interviewer', "interviewid $insql", $params);
        }

        $stats['interviews'] = count($interviews);
        $stats['documents'] = count($documents);

        // Count validations.
        if (!empty($documents)) {
            $docids = array_keys($documents);
            list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
            $stats['validations'] = $DB->count_records_select('local_jobboard_doc_validation', "documentid $insql", $params);
        }

        // Count files.
        foreach ($documents as $doc) {
            $files = $fs->get_area_files($context->id, 'local_jobboard', 'application_documents', $doc->id, 'id', false);
            $stats['files'] += count($files);
        }

        $stats['workflow_logs'] = $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);

        if ($DB->get_manager()->table_exists('local_jobboard_evaluation')) {
            $stats['evaluations'] = $DB->count_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
        }

        // Count notifications.
        try {
            $likeparam = '%"applicationid":' . $app->id . '%';
            $stats['notifications'] = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {local_jobboard_notification} WHERE " . $DB->sql_like('data', ':pattern'),
                ['pattern' => $likeparam]
            );
        } catch (Exception $e) {
            // Ignore.
        }

        // Count audit logs.
        if ($purgeaudit) {
            $stats['audit_logs'] += $DB->count_records('local_jobboard_audit', [
                'entitytype' => 'application',
                'entityid' => $app->id,
            ]);

            if (!empty($documents)) {
                $docids = array_keys($documents);
                list($insql, $params) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED);
                $params['entitytype'] = 'document';
                $stats['audit_logs'] += $DB->count_records_select(
                    'local_jobboard_audit',
                    "entitytype = :entitytype AND entityid $insql",
                    $params
                );
            }

            try {
                $likeparam = '%"applicationid":' . $app->id . '%';
                $stats['audit_logs'] += $DB->count_records_sql(
                    "SELECT COUNT(*) FROM {local_jobboard_audit} WHERE " . $DB->sql_like('extradata', ':pattern'),
                    ['pattern' => $likeparam]
                );
            } catch (Exception $e) {
                // Ignore.
            }
        }

        $stats['application'] = 1;
    }

    return $stats;
}

/**
 * Export applications to JSON (complete backup before deletion).
 */
function export_to_json(array $applications, string $filename): void {
    global $DB;

    $export = [
        'export_date' => date('Y-m-d H:i:s'),
        'export_reason' => 'Backup before complete deletion',
        'applications' => [],
    ];

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
            'interviews' => [],
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
                'reject_reason' => $doc->rejectreason,
                'timecreated' => date('Y-m-d H:i:s', $doc->timecreated),
            ];
        }

        // Add workflow history.
        $history = get_workflow_history($app->id);
        foreach ($history as $entry) {
            $appdata['workflow_history'][] = [
                'previous_status' => $entry->previousstatus,
                'new_status' => $entry->newstatus,
                'changed_by' => trim($entry->firstname . ' ' . $entry->lastname),
                'comments' => $entry->comments,
                'timestamp' => date('Y-m-d H:i:s', $entry->timecreated),
            ];
        }

        // Add interviews.
        $interviews = get_application_interviews($app->id);
        foreach ($interviews as $int) {
            $appdata['interviews'][] = [
                'id' => $int->id,
                'scheduled_time' => date('Y-m-d H:i:s', $int->scheduledtime),
                'duration' => $int->duration,
                'type' => $int->interviewtype,
                'location' => $int->location,
                'status' => $int->status,
                'rating' => $int->rating,
                'feedback' => $int->feedback,
                'recommendation' => $int->recommendation,
                'interviewer_count' => $int->interviewer_count,
            ];
        }

        $export['applications'][] = $appdata;
    }

    $json = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($filename, $json);
    echo "Exported " . count($applications) . " application(s) to: $filename\n\n";
}

// ============================================================================
// MAIN EXECUTION
// ============================================================================

cli_heading('ISER Job Board - COMPLETE Application Deletion CLI v2.0');

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

echo "Found $totalapps application(s) for " . count($byuser) . " user(s)\n";
if ($purgeaudit) {
    echo "AUDIT PURGE: Enabled (--purge-audit) - ALL traces will be removed\n";
} else {
    echo "AUDIT PURGE: Disabled - Audit logs will be preserved\n";
}
echo "\n";

// Export to JSON if requested.
if (!empty($options['export-json'])) {
    export_to_json($applications, $options['export-json']);
}

// ============================================================================
// LIST MODE
// ============================================================================

if ($dolist || ($dodelete && $verbose)) {
    foreach ($byuser as $userkey => $userdata) {
        echo str_repeat('=', 100) . "\n";
        echo "USER: {$userdata['user']['name']}\n";
        echo "  ID Number: {$userdata['user']['idnumber']}\n";
        echo "  Email: {$userdata['user']['email']}\n";
        echo "  Moodle User ID: {$userdata['user']['id']}\n";
        echo "  Applications: " . count($userdata['applications']) . "\n";
        echo str_repeat('-', 100) . "\n";

        $columns = [
            ['title' => 'App ID', 'width' => 7],
            ['title' => 'Status', 'width' => 22],
            ['title' => 'Vacancy Code', 'width' => 15],
            ['title' => 'Vacancy Title', 'width' => 28],
            ['title' => 'Location', 'width' => 12],
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
                    echo "  DOCUMENTS (" . count($documents) . "):\n";
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
                    echo "  WORKFLOW HISTORY (" . count($history) . "):\n";
                    foreach ($history as $entry) {
                        $date = date('Y-m-d H:i', $entry->timecreated);
                        $by = trim($entry->firstname . ' ' . $entry->lastname);
                        echo "    - [$date] {$entry->previousstatus} -> {$entry->newstatus} (by $by)\n";
                    }
                }
            }

            // Show interviews.
            if ($showinterviews) {
                $interviews = get_application_interviews($app->id);
                if (!empty($interviews)) {
                    echo "  INTERVIEWS (" . count($interviews) . "):\n";
                    foreach ($interviews as $int) {
                        $date = date('Y-m-d H:i', $int->scheduledtime);
                        echo "    - [$date] {$int->interviewtype} | Status: {$int->status} | ";
                        echo "Panelists: {$int->interviewer_count}\n";
                    }
                }
            }

            // Show related record counts.
            if ($verbose) {
                $counts = count_all_related_records($app->id, $purgeaudit);
                echo "  RELATED RECORDS TO DELETE:\n";
                echo "    Interviews: {$counts['interviews']}, Interviewers: {$counts['interviewers']}\n";
                echo "    Documents: {$counts['documents']}, Validations: {$counts['validations']}, Files: {$counts['files']}\n";
                echo "    Workflow logs: {$counts['workflow_logs']}, Evaluations: {$counts['evaluations']}\n";
                echo "    Notifications: {$counts['notifications']}";
                if ($purgeaudit) {
                    echo ", Audit logs: {$counts['audit_logs']}";
                }
                echo "\n";
            }
        }

        echo "\n";
    }
}

// ============================================================================
// DELETE MODE
// ============================================================================

if ($dodelete) {
    echo str_repeat('=', 100) . "\n";
    echo $dryrun ? "DRY RUN - DELETION PREVIEW (NO CHANGES)\n" : "COMPLETE DELETION MODE\n";
    echo str_repeat('=', 100) . "\n";
    echo "Applications to delete: $totalapps\n";
    echo "Reason: $reason\n";
    echo "Audit purge: " . ($purgeaudit ? "YES - ALL traces removed" : "NO - Audit logs preserved") . "\n";

    if ($dryrun) {
        echo "\n*** DRY RUN - No changes will be made ***\n";
    }

    // Confirmation (unless force mode).
    if (!$force && !$dryrun) {
        echo "\n";
        echo str_repeat('!', 100) . "\n";
        echo "WARNING: This will PERMANENTLY delete $totalapps application(s) and ALL related data!\n";
        if ($purgeaudit) {
            echo "         INCLUDING ALL AUDIT LOGS - NO TRACE WILL REMAIN!\n";
        }
        echo "         This action CANNOT be undone.\n";
        echo str_repeat('!', 100) . "\n\n";

        echo "Type 'DELETE' to confirm (or 'PURGE' if using --purge-audit): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);

        $expectedconfirm = $purgeaudit ? 'PURGE' : 'DELETE';
        if ($line !== $expectedconfirm) {
            echo "\nDeletion cancelled. Expected '$expectedconfirm'.\n";
            exit(0);
        }
        echo "\n";
    }

    // Process deletions.
    $totals = [
        'interviewers' => 0,
        'interviews' => 0,
        'documents' => 0,
        'validations' => 0,
        'workflow_logs' => 0,
        'evaluations' => 0,
        'notifications' => 0,
        'audit_logs' => 0,
        'files' => 0,
        'applications' => 0,
    ];

    foreach ($applications as $app) {
        echo str_repeat('-', 80) . "\n";
        echo "Processing Application ID: {$app->id}\n";
        echo "  User: {$app->firstname} {$app->lastname} ({$app->user_idnumber})\n";
        echo "  Vacancy: {$app->vacancy_code} - {$app->vacancy_title}\n";
        echo "  Status: {$app->status}\n";
        echo "  Created: " . date('Y-m-d H:i:s', $app->timecreated) . "\n";

        $stats = delete_application_complete($app, $reason, $dryrun, $verbose, $purgeaudit);

        foreach ($stats as $key => $value) {
            if ($key === 'application') {
                $totals['applications'] += $value;
            } else if (isset($totals[$key])) {
                $totals[$key] += $value;
            }
        }

        if ($dryrun) {
            echo "  -> Would DELETE this application completely\n";
        } else {
            echo "  -> DELETED COMPLETELY" . ($purgeaudit ? " (no traces)" : "") . "\n";
        }
    }

    // Summary.
    echo "\n" . str_repeat('=', 100) . "\n";
    echo $dryrun ? "DRY RUN SUMMARY (no changes made):\n" : "COMPLETE DELETION SUMMARY:\n";
    echo str_repeat('=', 100) . "\n";
    printf("  Applications deleted:        %d\n", $totals['applications']);
    echo str_repeat('-', 50) . "\n";
    printf("  Interviews:                  %d\n", $totals['interviews']);
    printf("  Interview panelists:         %d\n", $totals['interviewers']);
    printf("  Documents:                   %d\n", $totals['documents']);
    printf("  Document validations:        %d\n", $totals['validations']);
    printf("  Physical files removed:      %d\n", $totals['files']);
    printf("  Workflow logs:               %d\n", $totals['workflow_logs']);
    printf("  Evaluations:                 %d\n", $totals['evaluations']);
    printf("  Notifications:               %d\n", $totals['notifications']);
    if ($purgeaudit) {
        printf("  Audit logs PURGED:           %d\n", $totals['audit_logs']);
    } else {
        echo "  Audit logs:                  PRESERVED\n";
    }
    echo str_repeat('=', 100) . "\n";

    // Calculate total records.
    $totalrecords = array_sum($totals);
    printf("\nTOTAL RECORDS DELETED: %d\n", $totalrecords);

    if ($dryrun) {
        echo "\n*** DRY RUN - Run without --dryrun to actually delete ***\n";
    } else {
        echo "\n=== COMPLETE DELETION FINISHED ===\n";
        if ($purgeaudit) {
            echo "All traces have been permanently removed from the database.\n";
        } else {
            echo "Audit logs have been preserved for compliance.\n";
        }
    }
}

exit(0);
