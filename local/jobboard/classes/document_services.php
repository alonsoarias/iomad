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
 * Document conversion services for local_jobboard.
 *
 * Provides document conversion functionality similar to mod_assign's editpdf,
 * allowing preview of documents that have been converted to PDF.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

use core_files\converter;
use core_files\conversion;
use stored_file;
use moodle_url;
use context_system;

/**
 * Document conversion services class.
 *
 * Handles conversion of documents to PDF format for preview purposes.
 */
class document_services {

    /** Component name. */
    const COMPONENT = 'local_jobboard';

    /** File area for converted PDFs. */
    const CONVERTED_PDF_FILEAREA = 'converted';

    /** File area for preview images. */
    const PREVIEW_IMAGE_FILEAREA = 'preview';

    /**
     * Supported MIME types for conversion.
     */
    const CONVERTIBLE_MIMETYPES = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
        'text/plain',
        'text/html',
        'application/rtf',
    ];

    /**
     * MIME types that can be displayed directly.
     */
    const DIRECT_PREVIEW_MIMETYPES = [
        'application/pdf',
    ];

    /**
     * Check if a document can be previewed (directly or after conversion).
     *
     * @param document $document The document to check.
     * @return bool True if the document can be previewed.
     */
    public static function can_preview(document $document): bool {
        // Check if it's directly previewable.
        if (in_array($document->mimetype, self::DIRECT_PREVIEW_MIMETYPES)) {
            return true;
        }

        // Check if it can be converted.
        return self::can_convert_to_pdf($document);
    }

    /**
     * Check if a document can be converted to PDF.
     *
     * @param document $document The document to check.
     * @return bool True if the document can be converted.
     */
    public static function can_convert_to_pdf(document $document): bool {
        // First check if MIME type is in our convertible list.
        if (!in_array($document->mimetype, self::CONVERTIBLE_MIMETYPES)) {
            return false;
        }

        // Check if Moodle's converter can handle it.
        $file = self::get_stored_file($document);
        if (!$file) {
            return false;
        }

        $converter = new converter();
        return $converter->can_convert_storedfile_to($file, 'pdf');
    }

    /**
     * Get the stored file for a document.
     *
     * @param document $document The document.
     * @return stored_file|false The stored file or false.
     */
    public static function get_stored_file(document $document): stored_file|false {
        $fs = get_file_storage();
        $context = context_system::instance();

        $file = $fs->get_file(
            $context->id,
            'local_jobboard',
            'documents',
            $document->applicationid,
            '/',
            $document->filename
        );

        return $file ?: false;
    }

    /**
     * Start or get the conversion for a document.
     *
     * @param document $document The document to convert.
     * @param bool $forcerefresh Force a new conversion.
     * @return conversion|null The conversion object or null if not convertible.
     */
    public static function start_conversion(document $document, bool $forcerefresh = false): ?conversion {
        if (!self::can_convert_to_pdf($document)) {
            return null;
        }

        $file = self::get_stored_file($document);
        if (!$file) {
            return null;
        }

        $converter = new converter();
        return $converter->start_conversion($file, 'pdf', $forcerefresh);
    }

    /**
     * Get the conversion status for a document.
     *
     * @param document $document The document.
     * @return int|null Conversion status or null if no conversion exists.
     */
    public static function get_conversion_status(document $document): ?int {
        $file = self::get_stored_file($document);
        if (!$file) {
            return null;
        }

        $conversions = conversion::get_conversions_for_file($file, 'pdf');
        if (empty($conversions)) {
            return null;
        }

        $conv = reset($conversions);
        return $conv->get('status');
    }

    /**
     * Get the preview URL for a document.
     *
     * Returns the appropriate URL for previewing a document:
     * - For directly previewable types (PDF, images): returns the original file URL
     * - For convertible types: returns the converted PDF URL if available
     *
     * @param document $document The document.
     * @param bool $startconversion Whether to start conversion if not already done.
     * @return array Array with 'url', 'mimetype', 'status' keys.
     */
    public static function get_preview_info(document $document, bool $startconversion = true): array {
        $result = [
            'url' => null,
            'mimetype' => $document->mimetype,
            'status' => 'unavailable',
            'original_url' => $document->get_download_url(),
            'can_convert' => false,
            'conversion_status' => null,
        ];

        // Check if directly previewable.
        if (in_array($document->mimetype, self::DIRECT_PREVIEW_MIMETYPES)) {
            $result['url'] = $document->get_download_url();
            $result['status'] = 'ready';
            return $result;
        }

        // Check if convertible.
        if (!self::can_convert_to_pdf($document)) {
            return $result;
        }

        $result['can_convert'] = true;

        $file = self::get_stored_file($document);
        if (!$file) {
            return $result;
        }

        // Check for existing conversion.
        $conversions = conversion::get_conversions_for_file($file, 'pdf');

        if (empty($conversions) && $startconversion) {
            // Start a new conversion.
            $conv = self::start_conversion($document);
            if ($conv) {
                $conversions = [$conv];
            }
        }

        if (empty($conversions)) {
            $result['status'] = 'pending';
            return $result;
        }

        $conv = reset($conversions);
        $status = $conv->get('status');
        $result['conversion_status'] = $status;

        switch ($status) {
            case conversion::STATUS_COMPLETE:
                $destfile = $conv->get_destfile();
                if ($destfile) {
                    $result['url'] = self::get_file_url($destfile);
                    $result['mimetype'] = 'application/pdf';
                    $result['status'] = 'ready';
                }
                break;

            case conversion::STATUS_IN_PROGRESS:
            case conversion::STATUS_PENDING:
                $result['status'] = 'converting';
                break;

            case conversion::STATUS_FAILED:
                $result['status'] = 'failed';
                break;
        }

        return $result;
    }

    /**
     * Get a URL for a stored file.
     *
     * @param stored_file $file The file.
     * @return string The file URL.
     */
    protected static function get_file_url(stored_file $file): string {
        return moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename(),
            false
        )->out(false);
    }

    /**
     * Poll conversion status for a document.
     *
     * @param document $document The document.
     * @return array Updated preview info.
     */
    public static function poll_conversion(document $document): array {
        $file = self::get_stored_file($document);
        if (!$file) {
            return self::get_preview_info($document, false);
        }

        $conversions = conversion::get_conversions_for_file($file, 'pdf');
        if (!empty($conversions)) {
            $conv = reset($conversions);
            $status = $conv->get('status');

            // If still in progress, poll for updates.
            if ($status === conversion::STATUS_IN_PROGRESS || $status === conversion::STATUS_PENDING) {
                $converter = new converter();
                $converter->poll_conversion($conv);
            }
        }

        return self::get_preview_info($document, false);
    }

    /**
     * Get human-readable status message.
     *
     * @param string $status The status code.
     * @return string The status message.
     */
    public static function get_status_message(string $status): string {
        switch ($status) {
            case 'ready':
                return get_string('conversionready', 'local_jobboard');
            case 'converting':
                return get_string('conversioninprogress', 'local_jobboard');
            case 'pending':
                return get_string('conversionpending', 'local_jobboard');
            case 'failed':
                return get_string('conversionfailed', 'local_jobboard');
            default:
                return get_string('previewunavailable', 'local_jobboard');
        }
    }

    /**
     * Check if document converters are available in the system.
     *
     * @return bool True if at least one converter is available.
     */
    public static function converters_available(): bool {
        $converter = new converter();
        // Check if we can convert a common format.
        return $converter->can_convert_format_to('docx', 'pdf') ||
               $converter->can_convert_format_to('doc', 'pdf') ||
               $converter->can_convert_format_to('odt', 'pdf');
    }

    /**
     * Get list of supported file extensions for conversion.
     *
     * @return array List of extensions that can be converted.
     */
    public static function get_supported_extensions(): array {
        $converter = new converter();
        $extensions = [];
        $formatmap = [
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'rtf' => 'application/rtf',
        ];

        foreach ($formatmap as $ext => $mime) {
            if ($converter->can_convert_format_to($ext, 'pdf')) {
                $extensions[] = $ext;
            }
        }

        return $extensions;
    }
}
