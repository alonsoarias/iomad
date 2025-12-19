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
 * Download all documents for an application as a ZIP file.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educacion Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$applicationid = required_param('applicationid', PARAM_INT);
$sesskey = required_param('sesskey', PARAM_RAW);

// Validate session key.
if (!confirm_sesskey($sesskey)) {
    throw new moodle_exception('invalidsesskey');
}

// Check capability.
$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Get application directly from DB to avoid class loading issues.
$application = $DB->get_record('local_jobboard_application', ['id' => $applicationid]);
if (!$application) {
    throw new moodle_exception('error:invalidapplication', 'local_jobboard');
}

// Get applicant info for filename.
$applicant = $DB->get_record('user', ['id' => $application->userid], 'id, firstname, lastname, idnumber');
if (!$applicant) {
    throw new moodle_exception('error:invaliduser', 'local_jobboard');
}

// Get all documents for this application.
$documents = $DB->get_records('local_jobboard_document', [
    'applicationid' => $applicationid,
    'issuperseded' => 0
], 'timecreated ASC');

if (empty($documents)) {
    throw new moodle_exception('error:nodocuments', 'local_jobboard');
}

// Create ZIP file.
$lastname = clean_filename($applicant->lastname);
$firstname = clean_filename($applicant->firstname);
$idnumber = clean_filename($applicant->idnumber ?: (string)$applicant->id);

$zipfilename = "documentos_{$lastname}_{$firstname}_{$idnumber}_" . date('Ymd_His') . '.zip';

// Create temporary file for ZIP.
$tempdir = make_temp_directory('local_jobboard_zip');
$tempzippath = $tempdir . '/' . $zipfilename;

$zip = new ZipArchive();
$result = $zip->open($tempzippath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($result !== true) {
    throw new moodle_exception('error:cannotcreatezip', 'local_jobboard');
}

$filesadded = 0;
$fs = get_file_storage();
$syscontext = context_system::instance();

// Get all files for this application once (more efficient).
$allfiles = $fs->get_area_files(
    $syscontext->id,
    'local_jobboard',
    'application_documents',
    $applicationid,
    'id',
    false
);

// Get document types for naming text files.
$doctypes = $DB->get_records('local_jobboard_doctype', [], '', 'code, name');

foreach ($documents as $doc) {
    // Handle text documents (they don't have physical files in Moodle file storage).
    if ($doc->mimetype === 'text/plain') {
        // For text documents, get content from application data.
        $appdata = json_decode($application->applicationdata ?? '{}', true);
        if (is_array($appdata) && isset($appdata[$doc->documenttype])) {
            $textcontent = $appdata[$doc->documenttype];
            if (is_string($textcontent) && !empty(trim($textcontent))) {
                // Use document type name for the filename if available.
                $typename = isset($doctypes[$doc->documenttype]) ?
                    clean_filename($doctypes[$doc->documenttype]->name) :
                    $doc->documenttype;
                $zip->addFromString($typename . '.txt', $textcontent);
                $filesadded++;
            }
        }
        continue;
    }

    // Handle physical files.
    foreach ($allfiles as $file) {
        if ($file->get_filepath() === '/' . $doc->documenttype . '/' &&
            $file->get_filename() === $doc->filename) {

            // Get file content.
            $content = $file->get_content();
            if (!empty($content)) {
                // Use original filename directly (flat structure, no folders).
                $zip->addFromString($doc->filename, $content);
                $filesadded++;
            }
            break;
        }
    }
}

$zip->close();

if ($filesadded === 0) {
    @unlink($tempzippath);
    throw new moodle_exception('error:nodocumentstodownload', 'local_jobboard');
}

// Verify ZIP file exists and has content.
if (!file_exists($tempzippath) || filesize($tempzippath) === 0) {
    @unlink($tempzippath);
    throw new moodle_exception('error:cannotcreatezip', 'local_jobboard');
}

// Send the ZIP file to the browser.
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipfilename . '"');
header('Content-Length: ' . filesize($tempzippath));
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($tempzippath);

// Clean up.
@unlink($tempzippath);

exit;
