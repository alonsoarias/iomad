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
 * Supports multiple export modes:
 * - Single application: applicationid parameter
 * - Vacancy: vacancyid parameter (all applications)
 * - Convocatoria: convocatoriaid parameter (all vacancies in convocatoria)
 * - Company: companyid parameter (all vacancies for a company)
 *
 * Folder structure for bulk exports:
 * - Company/Vacancy_Code/Applicant_Name/DocumentType/filename
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
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);
$companyid = optional_param('companyid', 0, PARAM_INT);
$format = optional_param('format', 'flat', PARAM_ALPHA); // flat or structured

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

} else if ($convocatoriaid) {
    // Export all documents for a convocatoria.
    require_capability('local/jobboard:exportdata', $context);

    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid], '*', MUST_EXIST);

    // Get all vacancies for this convocatoria.
    $vacancies = $DB->get_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);

    if (empty($vacancies)) {
        throw new moodle_exception('novacanciesinconvocatoria', 'local_jobboard');
    }

    // Create ZIP filename.
    $zipfilename = clean_filename(sprintf(
        'documentos_%s_%s.zip',
        $convocatoria->code,
        date('Ymd_His')
    ));

    // Export with structured folders.
    export_convocatoria_documents_zip($vacancies, $zipfilename, $convocatoria);

} else if ($companyid) {
    // Export all documents for a company.
    require_capability('local/jobboard:exportdata', $context);

    // Verify company exists if IOMAD is installed.
    $companyname = 'Company_' . $companyid;
    if (local_jobboard_is_iomad_installed()) {
        $company = $DB->get_record('company', ['id' => $companyid]);
        if (!$company) {
            throw new moodle_exception('companynotfound', 'local_jobboard');
        }
        $companyname = $company->shortname ?: $company->name;
    }

    // Get all vacancies for this company.
    $vacancies = $DB->get_records('local_jobboard_vacancy', ['companyid' => $companyid]);

    if (empty($vacancies)) {
        throw new moodle_exception('novacanciesforcompany', 'local_jobboard');
    }

    // Create ZIP filename.
    $zipfilename = clean_filename(sprintf(
        'documentos_%s_%s.zip',
        $companyname,
        date('Ymd_His')
    ));

    // Export with structured folders by company.
    export_company_documents_zip($vacancies, $zipfilename, $companyid, $companyname);

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

/**
 * Export all documents for a convocatoria as ZIP.
 *
 * Structure: Vacancy_Code/Applicant_Name/DocumentType/filename
 *
 * @param array $vacancies Array of vacancy records.
 * @param string $zipfilename Output filename.
 * @param object $convocatoria Convocatoria object.
 */
function export_convocatoria_documents_zip(array $vacancies, string $zipfilename, $convocatoria): void {
    global $CFG, $DB;

    require_once($CFG->libdir . '/filestorage/zip_packer.php');

    $fs = get_file_storage();
    $tempdir = make_temp_directory('jobboard_export');
    $tempfiles = [];
    $doccount = 0;
    $appcount = 0;

    foreach ($vacancies as $vacancy) {
        $vacancyfolder = clean_filename($vacancy->code . '_' . substr($vacancy->title, 0, 30));

        // Get all applications for this vacancy.
        $applications = $DB->get_records('local_jobboard_application', ['vacancyid' => $vacancy->id]);

        foreach ($applications as $app) {
            $appcount++;
            $applicant = $DB->get_record('user', ['id' => $app->userid]);
            if (!$applicant) {
                continue;
            }

            $applicantfolder = clean_filename(sprintf(
                '%s_%s_%s',
                $applicant->lastname,
                $applicant->firstname,
                $applicant->idnumber ?: $applicant->id
            ));

            // Get documents for this application.
            $documents = document::get_by_application($app->id);

            foreach ($documents as $doc) {
                $file = get_document_file($doc);
                if (!$file) {
                    continue;
                }

                $doctypename = $doc->get_type_display();
                $doctypefolder = clean_filename($doctypename);
                $filepath = $vacancyfolder . '/' . $applicantfolder . '/' . $doctypefolder . '/' . $doc->filename;

                $temppath = $tempdir . '/' . $vacancyfolder . '/' . $applicantfolder . '/' . $doctypefolder;
                if (!is_dir($temppath)) {
                    mkdir($temppath, 0755, true);
                }

                $destfile = $temppath . '/' . $doc->filename;
                $file->copy_content_to($destfile);
                $tempfiles[$filepath] = $destfile;
                $doccount++;
            }
        }
    }

    if (empty($tempfiles)) {
        throw new moodle_exception('nodocuments', 'local_jobboard');
    }

    // Create manifest file.
    $manifestcontent = create_export_manifest($convocatoria->name, count($vacancies), $appcount, $doccount);
    $manifestpath = $tempdir . '/MANIFEST.txt';
    file_put_contents($manifestpath, $manifestcontent);
    $tempfiles['MANIFEST.txt'] = $manifestpath;

    // Create ZIP archive.
    $zippath = $tempdir . '/' . $zipfilename;
    $zippacker = new zip_packer();
    $result = $zippacker->archive_to_pathname($tempfiles, $zippath);

    if (!$result) {
        cleanup_temp_files($tempfiles);
        throw new moodle_exception('zipexportfailed', 'local_jobboard');
    }

    // Log the export.
    audit::log('convocatoria_document_export', 'convocatoria', $convocatoria->id, [
        'document_count' => $doccount,
        'application_count' => $appcount,
        'vacancy_count' => count($vacancies),
    ]);

    // Send the file.
    send_file($zippath, $zipfilename, 0, 0, false, true, '', true);

    // Cleanup.
    cleanup_temp_files($tempfiles);
    @unlink($zippath);
}

/**
 * Export all documents for a company as ZIP.
 *
 * Structure: Vacancy_Code/Applicant_Name/DocumentType/filename
 *
 * @param array $vacancies Array of vacancy records.
 * @param string $zipfilename Output filename.
 * @param int $companyid Company ID.
 * @param string $companyname Company name for manifest.
 */
function export_company_documents_zip(array $vacancies, string $zipfilename, int $companyid, string $companyname): void {
    global $CFG, $DB;

    require_once($CFG->libdir . '/filestorage/zip_packer.php');

    $fs = get_file_storage();
    $tempdir = make_temp_directory('jobboard_export');
    $tempfiles = [];
    $doccount = 0;
    $appcount = 0;

    foreach ($vacancies as $vacancy) {
        $vacancyfolder = clean_filename($vacancy->code . '_' . substr($vacancy->title, 0, 30));

        // Get all applications for this vacancy.
        $applications = $DB->get_records('local_jobboard_application', ['vacancyid' => $vacancy->id]);

        foreach ($applications as $app) {
            $appcount++;
            $applicant = $DB->get_record('user', ['id' => $app->userid]);
            if (!$applicant) {
                continue;
            }

            $applicantfolder = clean_filename(sprintf(
                '%s_%s_%s',
                $applicant->lastname,
                $applicant->firstname,
                $applicant->idnumber ?: $applicant->id
            ));

            // Get documents for this application.
            $documents = document::get_by_application($app->id);

            foreach ($documents as $doc) {
                $file = get_document_file($doc);
                if (!$file) {
                    continue;
                }

                $doctypename = $doc->get_type_display();
                $doctypefolder = clean_filename($doctypename);
                $filepath = $vacancyfolder . '/' . $applicantfolder . '/' . $doctypefolder . '/' . $doc->filename;

                $temppath = $tempdir . '/' . $vacancyfolder . '/' . $applicantfolder . '/' . $doctypefolder;
                if (!is_dir($temppath)) {
                    mkdir($temppath, 0755, true);
                }

                $destfile = $temppath . '/' . $doc->filename;
                $file->copy_content_to($destfile);
                $tempfiles[$filepath] = $destfile;
                $doccount++;
            }
        }
    }

    if (empty($tempfiles)) {
        throw new moodle_exception('nodocuments', 'local_jobboard');
    }

    // Create manifest file.
    $manifestcontent = create_export_manifest($companyname, count($vacancies), $appcount, $doccount);
    $manifestpath = $tempdir . '/MANIFEST.txt';
    file_put_contents($manifestpath, $manifestcontent);
    $tempfiles['MANIFEST.txt'] = $manifestpath;

    // Create ZIP archive.
    $zippath = $tempdir . '/' . $zipfilename;
    $zippacker = new zip_packer();
    $result = $zippacker->archive_to_pathname($tempfiles, $zippath);

    if (!$result) {
        cleanup_temp_files($tempfiles);
        throw new moodle_exception('zipexportfailed', 'local_jobboard');
    }

    // Log the export.
    audit::log('company_document_export', 'company', $companyid, [
        'document_count' => $doccount,
        'application_count' => $appcount,
        'vacancy_count' => count($vacancies),
    ]);

    // Send the file.
    send_file($zippath, $zipfilename, 0, 0, false, true, '', true);

    // Cleanup.
    cleanup_temp_files($tempfiles);
    @unlink($zippath);
}

/**
 * Get the stored file for a document.
 *
 * @param document $doc Document object.
 * @return \stored_file|null The stored file or null.
 */
function get_document_file($doc): ?\stored_file {
    $fs = get_file_storage();
    $context = context_system::instance();

    // Try application_documents filearea first.
    $files = $fs->get_area_files(
        $context->id,
        document::COMPONENT,
        document::FILEAREA,
        $doc->applicationid,
        'id',
        false
    );

    foreach ($files as $file) {
        if ($file->get_filepath() === '/' . $doc->documenttype . '/' &&
            $file->get_filename() === $doc->filename) {
            return $file;
        }
    }

    // Fallback to old filearea 'document'.
    $file = $fs->get_file(
        $context->id,
        'local_jobboard',
        'document',
        $doc->id,
        '/',
        $doc->filename
    );

    return $file ?: null;
}

/**
 * Create export manifest content.
 *
 * @param string $name Export name (convocatoria or company name).
 * @param int $vacancycount Number of vacancies.
 * @param int $appcount Number of applications.
 * @param int $doccount Number of documents.
 * @return string Manifest content.
 */
function create_export_manifest(string $name, int $vacancycount, int $appcount, int $doccount): string {
    $date = userdate(time(), get_string('strftimedatetime', 'langconfig'));

    return <<<EOF
========================================
EXPORT MANIFEST - Job Board
========================================

Export Name: {$name}
Export Date: {$date}

Statistics:
-----------
Vacancies: {$vacancycount}
Applications: {$appcount}
Documents: {$doccount}

Folder Structure:
-----------------
Vacancy_Code/
  └── Applicant_LastName_FirstName_ID/
      └── DocumentType/
          └── filename.pdf

========================================
Generated by local_jobboard
========================================
EOF;
}
