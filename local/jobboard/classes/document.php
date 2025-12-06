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
 * Document class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing an uploaded document.
 */
class document {

    /** @var int The document ID. */
    public $id = 0;

    /** @var int The application ID. */
    public $applicationid = 0;

    /** @var string The document type code. */
    public $documenttype = '';

    /** @var string The original filename. */
    public $filename = '';

    /** @var string The Moodle file contenthash. */
    public $contenthash = '';

    /** @var int The file size in bytes. */
    public $filesize = 0;

    /** @var string The MIME type. */
    public $mimetype = '';

    /** @var int|null The document issue date. */
    public $issuedate = null;

    /** @var int|null The document expiry date. */
    public $expirydate = null;

    /** @var int Whether the file is encrypted. */
    public $isencrypted = 0;

    /** @var int Whether this document is superseded. */
    public $issuperseded = 0;

    /** @var int The user who uploaded. */
    public $uploadedby = 0;

    /** @var int Creation timestamp. */
    public $timecreated = 0;

    /** @var \stdClass|null The validation record. */
    protected $validation = null;

    /** @var \stored_file|null The stored file. */
    protected $storedfile = null;

    /** Component name for file storage. */
    public const COMPONENT = 'local_jobboard';

    /** File area for application documents. */
    public const FILEAREA = 'application_documents';

    /**
     * Constructor.
     *
     * @param int|\stdClass|null $idorrecord Document ID, database record, or null.
     */
    public function __construct($idorrecord = null) {
        if ($idorrecord === null) {
            return;
        }

        if (is_object($idorrecord)) {
            $this->load_from_record($idorrecord);
        } else {
            $this->load((int) $idorrecord);
        }
    }

    /**
     * Load document from ID.
     *
     * @param int $id The document ID.
     * @throws \dml_exception If not found.
     */
    public function load(int $id): void {
        global $DB;

        $record = $DB->get_record('local_jobboard_document', ['id' => $id], '*', MUST_EXIST);
        $this->load_from_record($record);
    }

    /**
     * Load document from database record.
     *
     * @param \stdClass $record The database record.
     */
    public function load_from_record(\stdClass $record): void {
        $this->id = (int) $record->id;
        $this->applicationid = (int) $record->applicationid;
        $this->documenttype = $record->documenttype;
        $this->filename = $record->filename;
        $this->contenthash = $record->contenthash ?? '';
        $this->filesize = (int) ($record->filesize ?? 0);
        $this->mimetype = $record->mimetype ?? '';
        $this->issuedate = $record->issuedate ? (int) $record->issuedate : null;
        $this->expirydate = $record->expirydate ? (int) $record->expirydate : null;
        $this->isencrypted = (int) ($record->isencrypted ?? 0);
        $this->issuperseded = (int) ($record->issuperseded ?? 0);
        $this->uploadedby = (int) $record->uploadedby;
        $this->timecreated = (int) $record->timecreated;
    }

    /**
     * Get a document by ID.
     *
     * @param int $id The document ID.
     * @return self|null The document or null.
     */
    public static function get(int $id): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_document', ['id' => $id]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Create a new document from uploaded file.
     *
     * @param int $applicationid The application ID.
     * @param string $documenttype The document type code.
     * @param \stored_file $file The uploaded file.
     * @param int|null $issuedate Optional issue date.
     * @return self The created document.
     * @throws \moodle_exception If validation fails.
     */
    public static function create_from_file(
        int $applicationid,
        string $documenttype,
        \stored_file $file,
        ?int $issuedate = null
    ): self {
        global $DB, $USER;

        // Mark any existing document of this type as superseded.
        $DB->set_field('local_jobboard_document', 'issuperseded', 1, [
            'applicationid' => $applicationid,
            'documenttype' => $documenttype,
            'issuperseded' => 0,
        ]);

        $document = new self();
        $document->applicationid = $applicationid;
        $document->documenttype = $documenttype;
        $document->filename = $file->get_filename();
        $document->contenthash = $file->get_contenthash();
        $document->filesize = $file->get_filesize();
        $document->mimetype = $file->get_mimetype();
        $document->issuedate = $issuedate;
        $document->uploadedby = $USER->id;
        $document->timecreated = time();

        // Insert record.
        $record = $document->to_record();
        unset($record->id);
        $document->id = $DB->insert_record('local_jobboard_document', $record);

        // Create validation record.
        $validation = new \stdClass();
        $validation->documentid = $document->id;
        $validation->status = 'pending';
        $validation->timecreated = time();
        $DB->insert_record('local_jobboard_doc_validation', $validation);

        // Log audit.
        audit::log('document_uploaded', 'document', $document->id, [
            'documenttype' => $documenttype,
            'filename' => $document->filename,
        ]);

        // Trigger event.
        $event = \local_jobboard\event\document_uploaded::create([
            'objectid' => $document->id,
            'context' => \context_system::instance(),
            'other' => [
                'applicationid' => $applicationid,
                'documenttype' => $documenttype,
            ],
        ]);
        $event->trigger();

        return $document;
    }

    /**
     * Store uploaded file from form.
     *
     * @param int $applicationid The application ID.
     * @param string $documenttype The document type code.
     * @param int $draftitemid The draft file item ID from the form.
     * @param int|null $issuedate Optional issue date.
     * @return self|null The created document or null if no file.
     */
    public static function store_from_draft(
        int $applicationid,
        string $documenttype,
        int $draftitemid,
        ?int $issuedate = null
    ): ?self {
        global $USER;

        $context = \context_system::instance();
        $fs = get_file_storage();

        // Get files from draft area.
        $draftfiles = $fs->get_area_files(
            \context_user::instance($USER->id)->id,
            'user',
            'draft',
            $draftitemid,
            'id DESC',
            false
        );

        if (empty($draftfiles)) {
            return null;
        }

        // Get the first file.
        $draftfile = reset($draftfiles);

        // Validate file.
        if (!self::validate_file($draftfile)) {
            throw new \moodle_exception('error:invalidfile', 'local_jobboard');
        }

        // Save to plugin file area.
        $filerecord = [
            'contextid' => $context->id,
            'component' => self::COMPONENT,
            'filearea' => self::FILEAREA,
            'itemid' => $applicationid,
            'filepath' => '/' . $documenttype . '/',
            'filename' => $draftfile->get_filename(),
        ];

        // Delete any existing file of this type.
        $existingfiles = $fs->get_area_files(
            $context->id,
            self::COMPONENT,
            self::FILEAREA,
            $applicationid,
            'id',
            false
        );

        foreach ($existingfiles as $existing) {
            if ($existing->get_filepath() === '/' . $documenttype . '/') {
                $existing->delete();
            }
        }

        // Create new file.
        $storedfile = $fs->create_file_from_storedfile($filerecord, $draftfile);

        // Create document record.
        return self::create_from_file($applicationid, $documenttype, $storedfile, $issuedate);
    }

    /**
     * Validate uploaded file.
     *
     * @param \stored_file $file The file to validate.
     * @return bool True if valid.
     */
    public static function validate_file(\stored_file $file): bool {
        // Check file size.
        $maxsize = local_jobboard_get_max_filesize();
        if ($file->get_filesize() > $maxsize) {
            return false;
        }

        // Check MIME type.
        $allowedmimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
        ];

        if (!in_array($file->get_mimetype(), $allowedmimes)) {
            return false;
        }

        // Check extension.
        $allowedexts = local_jobboard_get_allowed_extensions();
        $ext = strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedexts)) {
            return false;
        }

        return true;
    }

    /**
     * Get the stored file.
     *
     * @return \stored_file|null The file or null.
     */
    public function get_stored_file(): ?\stored_file {
        if ($this->storedfile !== null) {
            return $this->storedfile;
        }

        $context = \context_system::instance();
        $fs = get_file_storage();

        $files = $fs->get_area_files(
            $context->id,
            self::COMPONENT,
            self::FILEAREA,
            $this->applicationid,
            'id',
            false
        );

        foreach ($files as $file) {
            if ($file->get_filepath() === '/' . $this->documenttype . '/' &&
                $file->get_filename() === $this->filename) {
                $this->storedfile = $file;
                return $this->storedfile;
            }
        }

        return null;
    }

    /**
     * Get file download URL.
     *
     * @param bool $forcedownload Force download instead of view.
     * @return \moodle_url The download URL.
     */
    public function get_download_url(bool $forcedownload = true): \moodle_url {
        $file = $this->get_stored_file();
        if (!$file) {
            return new \moodle_url('/');
        }

        return \moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(), // Use the actual itemid from the stored file.
            $file->get_filepath(),
            $file->get_filename(),
            $forcedownload
        );
    }

    /**
     * Get the validation record.
     *
     * @return \stdClass|null The validation record.
     */
    public function get_validation(): ?\stdClass {
        global $DB;

        if ($this->validation === null) {
            $this->validation = $DB->get_record('local_jobboard_doc_validation', [
                'documentid' => $this->id,
            ]);
        }

        return $this->validation ?: null;
    }

    /**
     * Get validation status.
     *
     * @return string The status (pending, approved, rejected).
     */
    public function get_validation_status(): string {
        $validation = $this->get_validation();
        return $validation ? $validation->status : 'pending';
    }

    /**
     * Get validation status display.
     *
     * @return string The localized status.
     */
    public function get_validation_status_display(): string {
        return get_string('docstatus:' . $this->get_validation_status(), 'local_jobboard');
    }

    /**
     * Get document type definition.
     *
     * @return \stdClass|null The document type record.
     */
    public function get_document_type(): ?\stdClass {
        global $DB;

        return $DB->get_record('local_jobboard_doctype', ['code' => $this->documenttype]) ?: null;
    }

    /**
     * Get document type display name.
     *
     * @return string The type name.
     */
    public function get_type_display(): string {
        $doctype = $this->get_document_type();
        if ($doctype) {
            return $doctype->name;
        }

        // Fallback to language string.
        $stringid = 'doctype:' . $this->documenttype;
        if (get_string_manager()->string_exists($stringid, 'local_jobboard')) {
            return get_string($stringid, 'local_jobboard');
        }

        return $this->documenttype;
    }

    /**
     * Check if document issue date is expired based on max age.
     *
     * @param int $maxdays Maximum days since issue.
     * @return bool True if expired.
     */
    public function is_issue_date_expired(int $maxdays): bool {
        if (!$this->issuedate) {
            return false;
        }

        $dayssince = local_jobboard_days_between($this->issuedate, time());
        return $dayssince > $maxdays;
    }

    /**
     * Get days since issue date.
     *
     * @return int|null Days since issue or null.
     */
    public function get_days_since_issue(): ?int {
        if (!$this->issuedate) {
            return null;
        }

        return local_jobboard_days_between($this->issuedate, time());
    }

    /**
     * Convert to database record.
     *
     * @return \stdClass The database record.
     */
    public function to_record(): \stdClass {
        return (object) [
            'id' => $this->id ?: null,
            'applicationid' => $this->applicationid,
            'documenttype' => $this->documenttype,
            'filename' => $this->filename,
            'contenthash' => $this->contenthash,
            'filesize' => $this->filesize,
            'mimetype' => $this->mimetype,
            'issuedate' => $this->issuedate,
            'expirydate' => $this->expirydate,
            'isencrypted' => $this->isencrypted,
            'issuperseded' => $this->issuperseded,
            'uploadedby' => $this->uploadedby,
            'timecreated' => $this->timecreated,
        ];
    }

    /**
     * Delete the document and its file.
     */
    public function delete(): void {
        global $DB;

        // Delete file.
        $file = $this->get_stored_file();
        if ($file) {
            $file->delete();
        }

        // Delete validation record.
        $DB->delete_records('local_jobboard_doc_validation', ['documentid' => $this->id]);

        // Delete document record.
        $DB->delete_records('local_jobboard_document', ['id' => $this->id]);

        // Log audit.
        audit::log('document_deleted', 'document', $this->id);
    }

    /**
     * Get document types for a vacancy.
     *
     * @param int $vacancyid The vacancy ID.
     * @param int|null $userid User ID to check exemptions.
     * @return array Array of document requirement records.
     */
    public static function get_required_types(int $vacancyid, ?int $userid = null): array {
        global $DB;

        // Get requirements for this vacancy.
        $requirements = $DB->get_records('local_jobboard_doc_requirement', [
            'vacancyid' => $vacancyid,
        ], 'sortorder ASC');

        // If no requirements, get all enabled document types.
        if (empty($requirements)) {
            $doctypes = $DB->get_records('local_jobboard_doctype', ['enabled' => 1], 'sortorder ASC');

            $requirements = [];
            foreach ($doctypes as $doctype) {
                $req = new \stdClass();
                $req->vacancyid = $vacancyid;
                $req->documenttype = $doctype->code;
                $req->required = 1;
                $req->acceptedformats = 'pdf,jpg,png';
                $req->maxsize = null;
                $req->requiresissuedate = $doctype->defaultmaxagedays ? 1 : 0;
                $req->maxagedays = $doctype->defaultmaxagedays;
                $req->instructions = $doctype->requirements;
                $req->sortorder = $doctype->sortorder;
                $req->doctype = $doctype;
                $requirements[$doctype->code] = $req;
            }
        } else {
            // Attach doctype info.
            foreach ($requirements as $req) {
                $req->doctype = $DB->get_record('local_jobboard_doctype', ['code' => $req->documenttype]);
            }
        }

        // Apply ISER exemption if applicable.
        if ($userid) {
            $exemption = local_jobboard_get_user_exemption($userid);
            if ($exemption) {
                $exemptedcodes = json_decode($exemption->exempteddocs ?? '[]', true);
                if (empty($exemptedcodes)) {
                    $exemptedcodes = local_jobboard_get_iser_exempted_doctypes();
                }

                foreach ($requirements as $code => $req) {
                    if (in_array($code, $exemptedcodes)) {
                        unset($requirements[$code]);
                    }
                }
            }
        }

        return $requirements;
    }

    /**
     * Format file size for display.
     *
     * @return string Formatted size.
     */
    public function get_filesize_display(): string {
        return display_size($this->filesize);
    }
}
