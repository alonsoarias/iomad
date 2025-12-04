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

declare(strict_types=1);

/**
 * Documents API endpoint for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\api\endpoints;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\api\response;

/**
 * API endpoint for document operations.
 */
class documents extends base {

    /**
     * GET /applications/{id}/documents - List documents for an application.
     */
    public function list(): void {
        global $DB;

        $applicationid = $this->param_int('id');

        if (!$applicationid) {
            response::bad_request('Invalid application ID');
        }

        $application = application::get($applicationid);

        if (!$application) {
            response::not_found('Application not found');
        }

        // Check access.
        if ($application->userid != $this->userid) {
            response::forbidden('You do not have access to this application');
        }

        // Get documents.
        $records = $DB->get_records('local_jobboard_document', [
            'applicationid' => $applicationid,
            'issuperseded' => 0,
        ], 'timecreated ASC');

        $documents = [];
        foreach ($records as $record) {
            $doc = new document($record);
            $documents[] = $this->format_document($doc);
        }

        $this->success($documents);
    }

    /**
     * POST /applications/{id}/documents - Upload a document.
     */
    public function upload(): void {
        $applicationid = $this->param_int('id');

        if (!$applicationid) {
            response::bad_request('Invalid application ID');
        }

        $application = application::get($applicationid);

        if (!$application) {
            response::not_found('Application not found');
        }

        // Check access.
        if ($application->userid != $this->userid) {
            response::forbidden('You do not have access to this application');
        }

        // Check application status allows document upload.
        if (!in_array($application->status, ['submitted', 'docs_rejected'])) {
            response::bad_request('Documents cannot be uploaded in the current application status');
        }

        // Validate document type.
        $documenttype = $this->input('document_type');
        if (empty($documenttype)) {
            response::validation_error(['document_type' => 'Document type is required']);
        }

        $documenttype = clean_param($documenttype, PARAM_ALPHANUMEXT);

        // Validate issue date if provided.
        $issuedate = null;
        if ($this->input('issue_date')) {
            $issuedate = strtotime($this->input('issue_date'));
            if ($issuedate === false) {
                response::validation_error(['issue_date' => 'Invalid date format. Use YYYY-MM-DD']);
            }
            if ($issuedate > time()) {
                response::validation_error(['issue_date' => 'Issue date cannot be in the future']);
            }
        }

        // Check for uploaded file.
        if (empty($_FILES['file'])) {
            response::validation_error(['file' => 'File is required']);
        }

        $uploadedfile = $_FILES['file'];

        // Validate file upload.
        if ($uploadedfile['error'] !== UPLOAD_ERR_OK) {
            $errormsg = $this->get_upload_error_message($uploadedfile['error']);
            response::bad_request($errormsg);
        }

        // Validate file size.
        $maxsize = local_jobboard_get_max_filesize();
        if ($uploadedfile['size'] > $maxsize) {
            response::validation_error([
                'file' => 'File size exceeds maximum allowed (' . display_size($maxsize) . ')',
            ]);
        }

        // Validate file type.
        $allowedmimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $uploadedfile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimetype, $allowedmimes)) {
            response::validation_error([
                'file' => 'Invalid file type. Allowed types: PDF, JPG, PNG',
            ]);
        }

        // Validate extension.
        $allowedexts = local_jobboard_get_allowed_extensions();
        $ext = strtolower(pathinfo($uploadedfile['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedexts)) {
            response::validation_error([
                'file' => 'Invalid file extension. Allowed: ' . implode(', ', $allowedexts),
            ]);
        }

        // Store file in Moodle file system.
        try {
            $document = $this->store_uploaded_file($applicationid, $documenttype, $uploadedfile, $issuedate);
        } catch (\Exception $e) {
            response::internal_error('Failed to store document');
        }

        $this->created([
            'id' => $document->id,
            'type' => $document->documenttype,
            'filename' => $document->filename,
            'filesize' => $document->filesize,
            'mimetype' => $document->mimetype,
            'issue_date' => $document->issuedate ? date('Y-m-d', $document->issuedate) : null,
            'uploaded_at' => date('Y-m-d\TH:i:sP', $document->timecreated),
            'message' => 'Document uploaded successfully',
        ]);
    }

    /**
     * Store an uploaded file.
     *
     * @param int $applicationid Application ID.
     * @param string $documenttype Document type.
     * @param array $uploadedfile Uploaded file info.
     * @param int|null $issuedate Issue date.
     * @return document The created document.
     */
    private function store_uploaded_file(
        int $applicationid,
        string $documenttype,
        array $uploadedfile,
        ?int $issuedate
    ): document {
        global $DB, $USER;

        $context = \context_system::instance();
        $fs = get_file_storage();

        // Mark existing documents of this type as superseded.
        $DB->set_field('local_jobboard_document', 'issuperseded', 1, [
            'applicationid' => $applicationid,
            'documenttype' => $documenttype,
            'issuperseded' => 0,
        ]);

        // Delete existing files of this type.
        $existingfiles = $fs->get_area_files(
            $context->id,
            document::COMPONENT,
            document::FILEAREA,
            $applicationid,
            'id',
            false
        );

        foreach ($existingfiles as $existing) {
            if ($existing->get_filepath() === '/' . $documenttype . '/') {
                $existing->delete();
            }
        }

        // Create new file record.
        $filerecord = [
            'contextid' => $context->id,
            'component' => document::COMPONENT,
            'filearea' => document::FILEAREA,
            'itemid' => $applicationid,
            'filepath' => '/' . $documenttype . '/',
            'filename' => $uploadedfile['name'],
        ];

        $storedfile = $fs->create_file_from_pathname($filerecord, $uploadedfile['tmp_name']);

        // Create document record.
        $doc = new document();
        $doc->applicationid = $applicationid;
        $doc->documenttype = $documenttype;
        $doc->filename = $storedfile->get_filename();
        $doc->contenthash = $storedfile->get_contenthash();
        $doc->filesize = $storedfile->get_filesize();
        $doc->mimetype = $storedfile->get_mimetype();
        $doc->issuedate = $issuedate;
        $doc->uploadedby = $USER->id;
        $doc->timecreated = time();

        $record = $doc->to_record();
        unset($record->id);
        $doc->id = $DB->insert_record('local_jobboard_document', $record);

        // Create validation record.
        $validation = new \stdClass();
        $validation->documentid = $doc->id;
        $validation->isvalid = 0;
        $validation->timecreated = time();
        $DB->insert_record('local_jobboard_doc_validation', $validation);

        // Log audit.
        \local_jobboard\audit::log('document_uploaded_api', 'document', $doc->id, [
            'documenttype' => $documenttype,
            'filename' => $doc->filename,
        ]);

        return $doc;
    }

    /**
     * Format a document for API response.
     *
     * @param document $doc The document.
     * @return array Formatted document data.
     */
    private function format_document(document $doc): array {
        $validation = $doc->get_validation();

        return [
            'id' => $doc->id,
            'type' => $doc->documenttype,
            'type_display' => $doc->get_type_display(),
            'filename' => $doc->filename,
            'filesize' => $doc->filesize,
            'filesize_display' => $doc->get_filesize_display(),
            'mimetype' => $doc->mimetype,
            'issue_date' => $doc->issuedate ? date('Y-m-d', $doc->issuedate) : null,
            'days_since_issue' => $doc->get_days_since_issue(),
            'validation_status' => $doc->get_validation_status(),
            'validation_status_display' => $doc->get_validation_status_display(),
            'reject_reason' => $validation && !$validation->isvalid ? $validation->rejectreason : null,
            'validator_notes' => $validation ? $validation->notes : null,
            'uploaded_at' => date('Y-m-d\TH:i:sP', $doc->timecreated),
        ];
    }

    /**
     * Get error message for upload error code.
     *
     * @param int $error The upload error code.
     * @return string The error message.
     */
    private function get_upload_error_message(int $error): string {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error (no temp directory)',
            UPLOAD_ERR_CANT_WRITE => 'Server configuration error (cannot write to disk)',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by server extension',
        ];

        return $messages[$error] ?? 'Unknown upload error';
    }
}
