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
 * Export documents as ZIP archive.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\vacancy;
use local_jobboard\audit;

// Parameters.
$applicationid = optional_param('applicationid', 0, PARAM_INT);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);

require_login();

$context = context_system::instance();

// Check capability.
if (!has_capability('local/jobboard:viewallapplications', $context) &&
    !has_capability('local/jobboard:reviewdocuments', $context)) {
    throw new moodle_exception('nopermission', 'local_jobboard');
}

// Determine what to export.
if ($applicationid) {
    // Export documents for a single application.
    $application = application::get($applicationid);
    if (!$application) {
        throw new moodle_exception('applicationnotfound', 'local_jobboard');
    }

    $vacancy = vacancy::get($application->vacancyid);
    $applicant = $DB->get_record('user', ['id' => $application->userid]);

    // Get all documents for this application.
    $documents = document::get_for_application($applicationid, false);

    if (empty($documents)) {
        throw new moodle_exception('nodocuments', 'local_jobboard');
    }

    // Create ZIP filename.
    $zipfilename = clean_filename(sprintf(
        'documentos_%s_%s_%s.zip',
        $vacancy->code,
        $applicant->lastname . '_' . $applicant->firstname,
        date('Ymd_His')
    ));

    // Export single application documents.
    export_documents_zip($documents, $zipfilename, $vacancy, $applicant);

} else if ($vacancyid) {
    // Export all documents for a vacancy.
    require_capability('local/jobboard:exportdata', $context);

    $vacancy = vacancy::get($vacancyid);
    if (!$vacancy) {
        throw new moodle_exception('vacancynotfound', 'local_jobboard');
    }

    // Get all applications for this vacancy.
    $result = application::get_list(['vacancyid' => $vacancyid], 'timecreated', 'ASC', 0, 0);
    $applications = $result['applications'];

    if (empty($applications)) {
        throw new moodle_exception('noapplicationsfound', 'local_jobboard');
    }

    // Create ZIP filename.
    $zipfilename = clean_filename(sprintf(
        'documentos_%s_todos_%s.zip',
        $vacancy->code,
        date('Ymd_His')
    ));

    // Export all documents for vacancy.
    export_vacancy_documents_zip($applications, $zipfilename, $vacancy);

} else {
    throw new moodle_exception('invalidparameters', 'local_jobboard');
}

/**
 * Export documents for a single application as ZIP.
 *
 * @param array $documents Array of document records.
 * @param string $zipfilename Output filename.
 * @param object $vacancy Vacancy object.
 * @param object $applicant User object.
 */
function export_documents_zip(array $documents, string $zipfilename, $vacancy, $applicant): void {
    global $CFG;

    require_once($CFG->libdir . '/filestorage/zip_packer.php');

    $fs = get_file_storage();
    $tempdir = make_temp_directory('jobboard_export');
    $tempfiles = [];

    foreach ($documents as $doc) {
        // Get the stored file.
        $file = $fs->get_file(
            context_system::instance()->id,
            'local_jobboard',
            'document',
            $doc->id,
            '/',
            $doc->filename
        );

        if (!$file) {
            continue;
        }

        // Create folder structure: DOCTYPE/filename.
        $doctype = get_string('doctype_' . $doc->documenttype, 'local_jobboard');
        $subfolder = clean_filename($doctype);

        // Create unique filename to avoid conflicts.
        $filename = $doc->filename;
        $filepath = $subfolder . '/' . $filename;

        // Copy file to temp directory.
        $temppath = $tempdir . '/' . $subfolder;
        if (!is_dir($temppath)) {
            mkdir($temppath, 0755, true);
        }

        $destfile = $temppath . '/' . $filename;
        $file->copy_content_to($destfile);
        $tempfiles[$filepath] = $destfile;
    }

    if (empty($tempfiles)) {
        throw new moodle_exception('nodocuments', 'local_jobboard');
    }

    // Create ZIP archive.
    $zippath = $tempdir . '/' . $zipfilename;
    $zippacker = new zip_packer();
    $result = $zippacker->archive_to_pathname($tempfiles, $zippath);

    if (!$result) {
        // Cleanup temp files.
        foreach ($tempfiles as $tempfile) {
            @unlink($tempfile);
        }
        throw new moodle_exception('zipexportfailed', 'local_jobboard');
    }

    // Log the export.
    audit::log('document_export', 'application', $GLOBALS['applicationid'] ?? 0, [
        'document_count' => count($documents),
        'vacancy_id' => $vacancy->id,
    ]);

    // Send the file.
    send_file($zippath, $zipfilename, 0, 0, false, true, '', true);

    // Cleanup.
    foreach ($tempfiles as $tempfile) {
        @unlink($tempfile);
    }
    @unlink($zippath);
}

/**
 * Export all documents for a vacancy as ZIP.
 *
 * @param array $applications Array of application records.
 * @param string $zipfilename Output filename.
 * @param object $vacancy Vacancy object.
 */
function export_vacancy_documents_zip(array $applications, string $zipfilename, $vacancy): void {
    global $CFG, $DB;

    require_once($CFG->libdir . '/filestorage/zip_packer.php');

    $fs = get_file_storage();
    $tempdir = make_temp_directory('jobboard_export');
    $tempfiles = [];
    $doccount = 0;

    foreach ($applications as $app) {
        // Get applicant info.
        $applicant = $DB->get_record('user', ['id' => $app->userid]);
        if (!$applicant) {
            continue;
        }

        // Create folder for applicant.
        $applicantfolder = clean_filename(sprintf(
            '%s_%s_%s',
            $applicant->lastname,
            $applicant->firstname,
            $applicant->idnumber ?: $applicant->id
        ));

        // Get documents for this application.
        $documents = document::get_for_application($app->id, false);

        foreach ($documents as $doc) {
            // Get the stored file.
            $file = $fs->get_file(
                context_system::instance()->id,
                'local_jobboard',
                'document',
                $doc->id,
                '/',
                $doc->filename
            );

            if (!$file) {
                continue;
            }

            // Create path: APPLICANT/DOCTYPE/filename.
            $doctype = get_string('doctype_' . $doc->documenttype, 'local_jobboard');
            $subfolder = clean_filename($doctype);
            $filepath = $applicantfolder . '/' . $subfolder . '/' . $doc->filename;

            // Create temp directory structure.
            $temppath = $tempdir . '/' . $applicantfolder . '/' . $subfolder;
            if (!is_dir($temppath)) {
                mkdir($temppath, 0755, true);
            }

            $destfile = $temppath . '/' . $doc->filename;
            $file->copy_content_to($destfile);
            $tempfiles[$filepath] = $destfile;
            $doccount++;
        }
    }

    if (empty($tempfiles)) {
        throw new moodle_exception('nodocuments', 'local_jobboard');
    }

    // Create ZIP archive.
    $zippath = $tempdir . '/' . $zipfilename;
    $zippacker = new zip_packer();
    $result = $zippacker->archive_to_pathname($tempfiles, $zippath);

    if (!$result) {
        // Cleanup temp files.
        cleanup_temp_files($tempfiles);
        throw new moodle_exception('zipexportfailed', 'local_jobboard');
    }

    // Log the export.
    audit::log('bulk_document_export', 'vacancy', $vacancy->id, [
        'document_count' => $doccount,
        'application_count' => count($applications),
    ]);

    // Send the file.
    send_file($zippath, $zipfilename, 0, 0, false, true, '', true);

    // Cleanup.
    cleanup_temp_files($tempfiles);
    @unlink($zippath);
}

/**
 * Cleanup temporary files.
 *
 * @param array $tempfiles Array of temp file paths.
 */
function cleanup_temp_files(array $tempfiles): void {
    foreach ($tempfiles as $tempfile) {
        if (file_exists($tempfile)) {
            @unlink($tempfile);
        }
    }
}
