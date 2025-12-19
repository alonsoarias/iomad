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
 * CLI script to check, repair, and update document file storage.
 *
 * Usage:
 *   php local/jobboard/cli/check_files.php --applicationid=7
 *   php local/jobboard/cli/check_files.php --repair
 *   php local/jobboard/cli/check_files.php --update-text
 *   php local/jobboard/cli/check_files.php --repair --update-text
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
    'repair' => false,
    'update-text' => false,
    'dry-run' => false,
    'help' => false,
], [
    'a' => 'applicationid',
    'r' => 'repair',
    'u' => 'update-text',
    'd' => 'dry-run',
    'h' => 'help',
]);

if ($options['help']) {
    echo "
Check, repair, and update document file storage.

Options:
  -a, --applicationid=ID    Application ID to check/repair (default: all)
  -r, --repair              Repair missing files from draft areas
  -u, --update-text         Update text documents from txt to html format
  -d, --dry-run             Show what would be done without making changes
  -h, --help                Show this help

Examples:
  php local/jobboard/cli/check_files.php --applicationid=7
  php local/jobboard/cli/check_files.php --repair --dry-run
  php local/jobboard/cli/check_files.php --update-text
  php local/jobboard/cli/check_files.php --repair --update-text
";
    exit(0);
}

$applicationid = (int)$options['applicationid'];
$dorepair = $options['repair'];
$doupdatetext = $options['update-text'];
$dryrun = $options['dry-run'];

echo "\n=== JOBBOARD DOCUMENT FILE CHECKER ===\n";
if ($dryrun) {
    echo "*** DRY RUN MODE - No changes will be made ***\n";
}
echo "\n";

// 1. Get document records.
echo "1. DOCUMENT RECORDS FROM DATABASE:\n";
echo str_repeat('-', 60) . "\n";

$params = [];
$where = '';
if ($applicationid > 0) {
    $where = 'WHERE d.applicationid = :applicationid';
    $params['applicationid'] = $applicationid;
}

$sql = "SELECT d.*, a.userid
        FROM {local_jobboard_document} d
        JOIN {local_jobboard_application} a ON a.id = d.applicationid
        $where
        ORDER BY d.applicationid, d.id";

$documents = $DB->get_records_sql($sql, $params);

if (empty($documents)) {
    echo "No document records found.\n";
    exit(0);
}

foreach ($documents as $doc) {
    echo sprintf("  ID:%d | App:%d | Type:%s | File:%s | MIME:%s\n",
        $doc->id, $doc->applicationid, $doc->documenttype, $doc->filename, $doc->mimetype);
}
echo "Total: " . count($documents) . " document records\n\n";

// 2. Check Moodle file storage.
echo "2. FILES IN MOODLE STORAGE:\n";
echo str_repeat('-', 60) . "\n";

$fs = get_file_storage();
$context = context_system::instance();
echo "System Context ID: " . $context->id . "\n\n";

// Check for all application IDs found.
$applicationids = array_unique(array_column($documents, 'applicationid'));

foreach ($applicationids as $appid) {
    echo "Application ID: $appid\n";

    // Get all files for this application.
    $files = $fs->get_area_files(
        $context->id,
        'local_jobboard',
        'application_documents',
        $appid,
        'id',
        false
    );

    if (empty($files)) {
        echo "  ** NO FILES FOUND in storage! **\n";
    } else {
        foreach ($files as $file) {
            echo sprintf("  Found: %s%s (hash: %s...)\n",
                $file->get_filepath(),
                $file->get_filename(),
                substr($file->get_contenthash(), 0, 12));
        }
    }
    echo "\n";
}

// 3. Check ALL files for local_jobboard component.
echo "3. ALL FILES FOR local_jobboard COMPONENT:\n";
echo str_repeat('-', 60) . "\n";

$sql = "SELECT f.id, f.contextid, f.component, f.filearea, f.itemid, f.filepath, f.filename, f.contenthash
        FROM {files} f
        WHERE f.component = 'local_jobboard'
        AND f.filename != '.'
        ORDER BY f.filearea, f.itemid, f.id
        LIMIT 100";

$allfiles = $DB->get_records_sql($sql);

if (empty($allfiles)) {
    echo "** NO FILES found for local_jobboard component! **\n";
    echo "This means files were NEVER stored in Moodle file storage.\n\n";
} else {
    foreach ($allfiles as $f) {
        echo sprintf("  ctx:%d area:%s itemid:%d path:%s name:%s\n",
            $f->contextid, $f->filearea, $f->itemid, $f->filepath, $f->filename);
    }
    echo "Total: " . count($allfiles) . " files\n\n";
}

// 4. Check if files exist in draft areas (might not have been processed).
echo "4. FILES IN USER DRAFT AREAS (might indicate failed processing):\n";
echo str_repeat('-', 60) . "\n";

// Get the users who created the applications.
$userids = array_unique(array_column($documents, 'userid'));
echo "Checking draft areas for " . count($userids) . " user(s)...\n";

foreach ($userids as $userid) {
    $usercontext = context_user::instance($userid, IGNORE_MISSING);
    if (!$usercontext) {
        echo "  User $userid: context not found\n";
        continue;
    }

    $draftfiles = $fs->get_area_files(
        $usercontext->id,
        'user',
        'draft',
        false,  // All itemids
        'itemid, id',
        false
    );

    if (!empty($draftfiles)) {
        echo "  User $userid: " . count($draftfiles) . " draft file(s) found\n";
        foreach ($draftfiles as $df) {
            echo sprintf("    draft itemid:%d - %s%s\n",
                $df->get_itemid(),
                $df->get_filepath(),
                $df->get_filename());
        }
    } else {
        echo "  User $userid: no draft files\n";
    }
}

// 5. Search for files by contenthash from document records.
echo "5. SEARCH BY CONTENTHASH (find files anywhere in system):\n";
echo str_repeat('-', 60) . "\n";

foreach ($documents as $doc) {
    if (empty($doc->contenthash) || $doc->mimetype === 'text/plain') {
        continue;
    }

    echo "Doc ID {$doc->id} ({$doc->documenttype}): hash={$doc->contenthash}\n";

    // Search for this contenthash in ALL files.
    $sql = "SELECT f.id, f.contextid, f.component, f.filearea, f.itemid, f.filepath, f.filename
            FROM {files} f
            WHERE f.contenthash = :hash
            AND f.filename != '.'
            LIMIT 5";
    $matchingfiles = $DB->get_records_sql($sql, ['hash' => $doc->contenthash]);

    if (empty($matchingfiles)) {
        echo "  ** File NOT FOUND anywhere in system! **\n";
    } else {
        foreach ($matchingfiles as $mf) {
            echo sprintf("  FOUND: component=%s, area=%s, itemid=%d, path=%s%s\n",
                $mf->component, $mf->filearea, $mf->itemid, $mf->filepath, $mf->filename);
        }
    }
}

echo "\n6. DIAGNOSIS:\n";
echo str_repeat('-', 60) . "\n";

// Summarize issues.
$docsWithFiles = [];
$docsWithoutFiles = [];

foreach ($documents as $doc) {
    if ($doc->mimetype === 'text/plain') {
        continue; // Text documents don't have files.
    }

    $files = $fs->get_area_files(
        $context->id,
        'local_jobboard',
        'application_documents',
        $doc->applicationid,
        'id',
        false
    );

    $found = false;
    $filepath = '/' . $doc->documenttype . '/';

    foreach ($files as $file) {
        if ($file->get_filepath() === $filepath) {
            $found = true;
            break;
        }
    }

    if ($found) {
        $docsWithFiles[] = $doc;
    } else {
        $docsWithoutFiles[] = $doc;
    }
}

if (!empty($docsWithoutFiles)) {
    echo "** PROBLEM: " . count($docsWithoutFiles) . " document(s) have records but NO physical files! **\n\n";
    echo "Affected documents:\n";
    foreach ($docsWithoutFiles as $doc) {
        echo sprintf("  - ID:%d (App:%d) %s: %s\n",
            $doc->id, $doc->applicationid, $doc->documenttype, $doc->filename);
    }
    echo "\nPossible causes:\n";
    echo "  1. Files were never uploaded (form submission issue)\n";
    echo "  2. Files failed to move from draft area\n";
    echo "  3. Files were stored in a different location\n";
    echo "  4. Database was restored without file storage\n";
} else {
    echo "All document records have corresponding files. No issues found.\n";
}

// ============================================================
// REPAIR FUNCTIONALITY
// ============================================================
if ($dorepair && !empty($docsWithoutFiles)) {
    echo "\n7. REPAIR MISSING FILES:\n";
    echo str_repeat('-', 60) . "\n";

    $repaired = 0;
    $failed = 0;

    foreach ($docsWithoutFiles as $doc) {
        echo "Repairing Doc ID {$doc->id} ({$doc->documenttype})...\n";

        // Skip text documents - they don't have physical files.
        if (in_array($doc->mimetype, ['text/plain', 'text/html'])) {
            echo "  -> Skipped (text document)\n";
            continue;
        }

        // Try to find file by contenthash anywhere in the system.
        if (empty($doc->contenthash)) {
            echo "  -> FAILED: No contenthash stored\n";
            $failed++;
            continue;
        }

        $sql = "SELECT f.*
                FROM {files} f
                WHERE f.contenthash = :hash
                AND f.filename != '.'
                LIMIT 1";
        $sourcefile = $DB->get_record_sql($sql, ['hash' => $doc->contenthash]);

        if (!$sourcefile) {
            echo "  -> FAILED: File not found by contenthash\n";
            $failed++;
            continue;
        }

        // Get the stored_file object.
        $storedfile = $fs->get_file_by_id($sourcefile->id);
        if (!$storedfile) {
            echo "  -> FAILED: Could not load stored file\n";
            $failed++;
            continue;
        }

        if ($dryrun) {
            echo "  -> Would copy from {$sourcefile->component}/{$sourcefile->filearea}\n";
            $repaired++;
            continue;
        }

        // Create new file in the correct location.
        $filepath = '/' . $doc->documenttype . '/';
        $filerecord = [
            'contextid' => $context->id,
            'component' => 'local_jobboard',
            'filearea' => 'application_documents',
            'itemid' => $doc->applicationid,
            'filepath' => $filepath,
            'filename' => $doc->filename,
        ];

        try {
            // Check if file already exists.
            $existingfile = $fs->get_file(
                $context->id,
                'local_jobboard',
                'application_documents',
                $doc->applicationid,
                $filepath,
                $doc->filename
            );

            if ($existingfile) {
                echo "  -> Skipped (file already exists)\n";
                continue;
            }

            // Copy the file.
            $newfile = $fs->create_file_from_storedfile($filerecord, $storedfile);
            echo "  -> REPAIRED (copied from {$sourcefile->component}/{$sourcefile->filearea})\n";
            $repaired++;
        } catch (Exception $e) {
            echo "  -> FAILED: " . $e->getMessage() . "\n";
            $failed++;
        }
    }

    echo "\nRepair summary: $repaired repaired, $failed failed\n";

    if ($dryrun && $repaired > 0) {
        echo "\nTo apply repairs, run without --dry-run\n";
    }
}

// ============================================================
// UPDATE TEXT DOCUMENTS (txt -> html)
// ============================================================
if ($doupdatetext) {
    echo "\n8. UPDATE TEXT DOCUMENTS (txt -> html):\n";
    echo str_repeat('-', 60) . "\n";

    // Find all text documents with text/plain mimetype.
    $textparams = [];
    $textwhere = "d.mimetype = 'text/plain' AND d.issuperseded = 0";
    if ($applicationid > 0) {
        $textwhere .= " AND d.applicationid = :applicationid";
        $textparams['applicationid'] = $applicationid;
    }

    $sql = "SELECT d.*, a.userid, a.applicationdata
            FROM {local_jobboard_document} d
            JOIN {local_jobboard_application} a ON a.id = d.applicationid
            WHERE $textwhere
            ORDER BY d.applicationid, d.id";

    $textdocuments = $DB->get_records_sql($sql, $textparams);

    echo "Found " . count($textdocuments) . " text document(s) with text/plain mimetype.\n\n";

    if (empty($textdocuments)) {
        echo "No text documents to update.\n";
    } else {
        $updated = 0;
        $errors = 0;

        foreach ($textdocuments as $doc) {
            echo "Doc ID {$doc->id} (App:{$doc->applicationid}, Type:{$doc->documenttype}):\n";
            echo "  Current: mimetype={$doc->mimetype}, filename={$doc->filename}\n";

            // Determine new values.
            $newmimetype = 'text/html';
            $newfilename = $doc->filename;

            // Update filename extension from .txt to .html.
            if (substr($newfilename, -4) === '.txt') {
                $newfilename = substr($newfilename, 0, -4) . '.html';
            } else if (strpos($newfilename, '.') === false) {
                // No extension, add .html.
                $newfilename .= '.html';
            }

            echo "  New: mimetype={$newmimetype}, filename={$newfilename}\n";

            if ($dryrun) {
                echo "  -> Would update\n";
                $updated++;
            } else {
                try {
                    $DB->set_field('local_jobboard_document', 'mimetype', $newmimetype, ['id' => $doc->id]);
                    $DB->set_field('local_jobboard_document', 'filename', $newfilename, ['id' => $doc->id]);
                    echo "  -> UPDATED\n";
                    $updated++;
                } catch (Exception $e) {
                    echo "  -> ERROR: " . $e->getMessage() . "\n";
                    $errors++;
                }
            }
            echo "\n";
        }

        echo "Update summary: $updated updated, $errors errors\n";

        if ($dryrun && $updated > 0) {
            echo "\nTo apply updates, run without --dry-run\n";
        }
    }
}

echo "\n=== END ===\n\n";
