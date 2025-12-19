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
 * CLI script to update text documents from text/plain to text/html.
 *
 * This script updates all text documents (like carta_intencion) to use
 * the new text/html mimetype and .html extension instead of text/plain and .txt.
 *
 * Usage: php local/jobboard/cli/update_text_documents.php [--dry-run]
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
    'dry-run' => false,
    'help' => false,
], [
    'd' => 'dry-run',
    'h' => 'help',
]);

if ($options['help']) {
    echo "
Update text documents from text/plain to text/html.

This script updates all text documents (like carta_intencion) to use
the new text/html mimetype and .html extension.

Options:
  -d, --dry-run    Show what would be done without making changes
  -h, --help       Show this help

Example:
  php local/jobboard/cli/update_text_documents.php --dry-run
  php local/jobboard/cli/update_text_documents.php
";
    exit(0);
}

$dryrun = $options['dry-run'];

echo "\n=== UPDATE TEXT DOCUMENTS TO HTML ===\n";
if ($dryrun) {
    echo "*** DRY RUN MODE - No changes will be made ***\n";
}
echo "\n";

// Find all text documents with text/plain mimetype.
$sql = "SELECT d.*, a.userid, a.applicationdata
        FROM {local_jobboard_document} d
        JOIN {local_jobboard_application} a ON a.id = d.applicationid
        WHERE d.mimetype = 'text/plain'
        AND d.issuperseded = 0
        ORDER BY d.applicationid, d.id";

$documents = $DB->get_records_sql($sql);

echo "Found " . count($documents) . " text document(s) with text/plain mimetype.\n\n";

if (empty($documents)) {
    echo "No documents to update.\n";
    exit(0);
}

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($documents as $doc) {
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

echo "=== SUMMARY ===\n";
echo "Total documents: " . count($documents) . "\n";
echo "Updated: $updated\n";
echo "Errors: $errors\n";

if ($dryrun && $updated > 0) {
    echo "\nTo apply updates, run without --dry-run:\n";
    echo "  php local/jobboard/cli/update_text_documents.php\n";
}

echo "\n=== END ===\n\n";
