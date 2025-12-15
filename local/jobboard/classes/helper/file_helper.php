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
 * File helper class for local_jobboard.
 *
 * Provides file handling utilities for uploads, validation, and management.
 * Extracted from lib.php for better code organization.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * File helper class.
 */
class file_helper {

    /** @var string File area for application documents */
    const FILEAREA_DOCUMENTS = 'application_documents';

    /** @var string File area for vacancy attachments */
    const FILEAREA_VACANCY = 'vacancy_attachments';

    /** @var int Default max file size in bytes (10MB) */
    const DEFAULT_MAX_SIZE = 10485760;

    /** @var array Default allowed extensions */
    const DEFAULT_EXTENSIONS = ['pdf'];

    /** @var array MIME type mapping for allowed extensions */
    const MIME_TYPES = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    /**
     * Get the maximum file size allowed for uploads.
     *
     * @return int File size in bytes.
     */
    public static function get_max_filesize(): int {
        $configsize = get_config('local_jobboard', 'maxfilesize');

        if ($configsize) {
            return (int) $configsize * 1024 * 1024; // MB to bytes.
        }

        return self::DEFAULT_MAX_SIZE;
    }

    /**
     * Get allowed file extensions.
     *
     * @return array Array of allowed extensions.
     */
    public static function get_allowed_extensions(): array {
        $config = get_config('local_jobboard', 'allowedformats');

        if ($config) {
            return array_map('trim', explode(',', $config));
        }

        return self::DEFAULT_EXTENSIONS;
    }

    /**
     * Validate uploaded file type.
     *
     * @param \stored_file $file The file to validate.
     * @param array|null $allowedformats Allowed formats or null for defaults.
     * @return bool True if valid.
     */
    public static function validate_file_type(\stored_file $file, ?array $allowedformats = null): bool {
        $allowedformats = $allowedformats ?? self::get_allowed_extensions();

        $filename = $file->get_filename();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedformats)) {
            return false;
        }

        $mimetype = $file->get_mimetype();

        if (isset(self::MIME_TYPES[$extension])) {
            if ($mimetype !== self::MIME_TYPES[$extension]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate file size.
     *
     * @param \stored_file $file The file to validate.
     * @param int|null $maxsize Maximum size in bytes, null for default.
     * @return bool True if valid.
     */
    public static function validate_file_size(\stored_file $file, ?int $maxsize = null): bool {
        $maxsize = $maxsize ?? self::get_max_filesize();
        return $file->get_filesize() <= $maxsize;
    }

    /**
     * Get file info for display.
     *
     * @param \stored_file $file The file.
     * @return array File info array.
     */
    public static function get_file_info(\stored_file $file): array {
        return [
            'filename' => $file->get_filename(),
            'filesize' => $file->get_filesize(),
            'filesize_formatted' => display_size($file->get_filesize()),
            'mimetype' => $file->get_mimetype(),
            'extension' => strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION)),
            'timecreated' => $file->get_timecreated(),
            'timemodified' => $file->get_timemodified(),
            'contenthash' => $file->get_contenthash(),
        ];
    }

    /**
     * Get download URL for a document.
     *
     * @param int $documentid The document ID.
     * @param string $filename The filename.
     * @param bool $forcedownload Force download instead of preview.
     * @return \moodle_url The download URL.
     */
    public static function get_document_url(int $documentid, string $filename, bool $forcedownload = false): \moodle_url {
        $context = \context_system::instance();

        $url = \moodle_url::make_pluginfile_url(
            $context->id,
            'local_jobboard',
            self::FILEAREA_DOCUMENTS,
            $documentid,
            '/',
            $filename,
            $forcedownload
        );

        return $url;
    }

    /**
     * Count files for an application.
     *
     * @param int $applicationid The application ID.
     * @return int Number of files.
     */
    public static function count_application_files(int $applicationid): int {
        $context = \context_system::instance();
        $fs = get_file_storage();

        $files = $fs->get_area_files(
            $context->id,
            'local_jobboard',
            self::FILEAREA_DOCUMENTS,
            $applicationid,
            'filename',
            false
        );

        return count($files);
    }

    /**
     * Get all files for an application.
     *
     * @param int $applicationid The application ID.
     * @return array Array of stored_file objects.
     */
    public static function get_application_files(int $applicationid): array {
        $context = \context_system::instance();
        $fs = get_file_storage();

        return $fs->get_area_files(
            $context->id,
            'local_jobboard',
            self::FILEAREA_DOCUMENTS,
            $applicationid,
            'filename',
            false
        );
    }

    /**
     * Delete all files for an application.
     *
     * @param int $applicationid The application ID.
     * @return bool True if successful.
     */
    public static function delete_application_files(int $applicationid): bool {
        $context = \context_system::instance();
        $fs = get_file_storage();

        return $fs->delete_area_files(
            $context->id,
            'local_jobboard',
            self::FILEAREA_DOCUMENTS,
            $applicationid
        );
    }

    /**
     * Calculate total size of files for an application.
     *
     * @param int $applicationid The application ID.
     * @return int Total size in bytes.
     */
    public static function get_application_files_size(int $applicationid): int {
        $files = self::get_application_files($applicationid);
        $total = 0;

        foreach ($files as $file) {
            $total += $file->get_filesize();
        }

        return $total;
    }

    /**
     * Get human-readable file size string.
     *
     * @param int $bytes Size in bytes.
     * @return string Formatted size.
     */
    public static function format_filesize(int $bytes): string {
        return display_size($bytes);
    }

    /**
     * Check if file extension is allowed.
     *
     * @param string $filename The filename to check.
     * @param array|null $allowedformats Allowed formats or null for defaults.
     * @return bool True if extension is allowed.
     */
    public static function is_extension_allowed(string $filename, ?array $allowedformats = null): bool {
        $allowedformats = $allowedformats ?? self::get_allowed_extensions();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return in_array($extension, $allowedformats);
    }

    /**
     * Get file picker options for document uploads.
     *
     * @return array Options array for file picker.
     */
    public static function get_filepicker_options(): array {
        $extensions = self::get_allowed_extensions();
        $accepted = array_map(function ($ext) {
            return '.' . $ext;
        }, $extensions);

        return [
            'maxbytes' => self::get_max_filesize(),
            'maxfiles' => 1,
            'accepted_types' => $accepted,
            'areamaxbytes' => self::get_max_filesize(),
            'return_types' => FILE_INTERNAL,
        ];
    }
}
