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
 * CLI script to repair document files that failed to move from draft area.
 *
 * This script finds document records where the physical file is missing
 * from the permanent storage, but exists in the user's draft area.
 * It then copies the file to the correct location.
 *
 * Usage: php local/jobboard/cli/repair_files.php [--applicationid=7] [--dry-run]
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Parse command line options.
list($options, $unrecognized) = cli_get_params([
    'applicationid' => 0,
    'dry-run' => false,
    'help' => false,
], [
    'a' => 'applicationid',
    'd' => 'dry-run',
    'h' => 'help',
]);

if ($options['help']) {
    echo "
Repair document files that failed to move from draft area.

Options:
  -a, --applicationid=ID    Application ID to repair (default: all with issues)
  -d, --dry-run             Show what would be done without making changes
  -h, --help                Show this help

Example:
  php local/jobboard/cli/repair_files.php --applicationid=7 --dry-run
  php local/jobboard/cli/repair_files.php --applicationid=7
";
    exit(0);
}

$applicationid = (int)$options['applicationid'];
$dryrun = $options['dry-run'];

echo "\n=== JOBBOARD DOCUMENT FILE REPAIR ===\n";
if ($dryrun) {
    echo "*** DRY RUN MODE - No changes will be made ***\n";
}
echo "\n";

$fs = get_file_storage();
$syscontext = context_system::instance();

// Find documents that need repair.
$params = [];
$where = "d.mimetype != 'text/plain'"; // Skip text documents.

if ($applicationid > 0) {
    $where .= " AND d.applicationid = :applicationid";
    $params['applicationid'] = $applicationid;
}

$sql = "SELECT d.*, a.userid
        FROM {local_jobboard_document} d
        JOIN {local_jobboard_application} a ON a.id = d.applicationid
        WHERE $where
        ORDER BY d.applicationid, d.id";

$documents = $DB->get_records_sql($sql, $params);

echo "Found " . count($documents) . " document record(s) to check.\n\n";

$repaired = 0;
$skipped = 0;
$failed = 0;

foreach ($documents as $doc) {
    echo "Checking Doc ID {$doc->id} (App:{$doc->applicationid}, {$doc->documenttype})...\n";

    // Check if file already exists in correct location.
    $expectedpath = '/' . $doc->documenttype . '/';
    $existingfiles = $fs->get_area_files(
        $syscontext->id,
        'local_jobboard',
        'application_documents',
        $doc->applicationid,
        'id',
        false
    );

    $fileexists = false;
    foreach ($existingfiles as $ef) {
        if ($ef->get_filepath() === $expectedpath) {
            $fileexists = true;
            echo "  ✓ File already exists: {$ef->get_filename()}\n";
            break;
        }
    }

    if ($fileexists) {
        $skipped++;
        continue;
    }

    // File doesn't exist - search by contenthash.
    if (empty($doc->contenthash)) {
        echo "  ✗ No contenthash recorded - cannot find file\n";
        $failed++;
        continue;
    }

    // Search for file with this contenthash.
    $sql = "SELECT f.*
            FROM {files} f
            WHERE f.contenthash = :hash
            AND f.filename != '.'
            ORDER BY f.id DESC
            LIMIT 1";
    $sourcefile = $DB->get_record_sql($sql, ['hash' => $doc->contenthash]);

    if (!$sourcefile) {
        echo "  ✗ File with hash {$doc->contenthash} not found anywhere\n";
        $failed++;
        continue;
    }

    echo "  Found source: {$sourcefile->component}/{$sourcefile->filearea} - {$sourcefile->filename}\n";

    // Get the stored_file object.
    $storedfile = $fs->get_file_by_id($sourcefile->id);
    if (!$storedfile) {
        echo "  ✗ Could not retrieve stored file by ID {$sourcefile->id}\n";
        $failed++;
        continue;
    }

    // Create file record for new location.
    $filerecord = [
        'contextid' => $syscontext->id,
        'component' => 'local_jobboard',
        'filearea' => 'application_documents',
        'itemid' => $doc->applicationid,
        'filepath' => $expectedpath,
        'filename' => $doc->filename, // Use the filename from document record.
    ];

    if ($dryrun) {
        echo "  → Would copy to: application_documents/{$doc->applicationid}{$expectedpath}{$doc->filename}\n";
        $repaired++;
    } else {
        try {
            // Check if a file with the same name already exists and delete it.
            $existingfile = $fs->get_file(
                $syscontext->id,
                'local_jobboard',
                'application_documents',
                $doc->applicationid,
                $expectedpath,
                $doc->filename
            );
            if ($existingfile) {
                $existingfile->delete();
            }

            // Create the new file.
            $newfile = $fs->create_file_from_storedfile($filerecord, $storedfile);

            if ($newfile) {
                echo "  ✓ REPAIRED: Copied to application_documents/{$doc->applicationid}{$expectedpath}{$doc->filename}\n";
                $repaired++;
            } else {
                echo "  ✗ Failed to create file\n";
                $failed++;
            }
        } catch (Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "Checked: " . count($documents) . " documents\n";
echo "Already OK: $skipped\n";
echo "Repaired: $repaired\n";
echo "Failed: $failed\n";

if ($dryrun && $repaired > 0) {
    echo "\nTo apply repairs, run without --dry-run:\n";
    echo "  php local/jobboard/cli/repair_files.php";
    if ($applicationid > 0) {
        echo " --applicationid=$applicationid";
    }
    echo "\n";
}

echo "\n=== END ===\n\n";
