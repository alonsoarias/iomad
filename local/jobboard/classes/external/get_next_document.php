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

namespace local_jobboard\external;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_jobboard\document;

/**
 * External API for getting the next pending document.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_next_document extends external_api {

    /**
     * Define parameters for execute.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'applicationid' => new external_value(PARAM_INT, 'The application ID'),
        ]);
    }

    /**
     * Get the next pending document for review.
     *
     * @param int $applicationid The application ID.
     * @return array Document info or empty if none pending.
     */
    public static function execute(int $applicationid): array {
        global $DB;

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
            'applicationid' => $applicationid,
        ]);

        // Context validation.
        $context = \context_system::instance();
        self::validate_context($context);

        // Check for review capabilities: reviewdocuments, manageworkflow, or dean capabilities.
        $canreview = has_capability('local/jobboard:reviewdocuments', $context);
        $canmanage = has_capability('local/jobboard:manageworkflow', $context);
        $isdean = has_capability('local/jobboard:reviewprofiles', $context) ||
                  has_capability('local/jobboard:approveprofile', $context);
        if (!$canreview && !$canmanage && !$isdean) {
            throw new \required_capability_exception($context, 'local/jobboard:reviewdocuments', 'nopermissions', '');
        }

        try {
            $documents = document::get_by_application($params['applicationid']);
            $nextdoc = null;
            $docindex = 0;

            foreach ($documents as $doc) {
                $docindex++;
                if ($doc->status === 'pending') {
                    $nextdoc = $doc;
                    break;
                }
            }

            if (!$nextdoc) {
                return [
                    'hasnext' => false,
                    'documentid' => 0,
                    'typename' => '',
                    'filename' => '',
                    'docindex' => 0,
                    'previewurl' => '',
                    'downloadurl' => '',
                    'ispdf' => false,
                    'isimage' => false,
                ];
            }

            // Get document type name.
            $doctype = $nextdoc->get_document_type();
            $typename = $doctype ? $doctype->name : $nextdoc->documenttype;

            // Get preview/download URLs.
            $file = $nextdoc->get_stored_file();
            $previewurl = '';
            $downloadurl = '';
            $ispdf = false;
            $isimage = false;

            if ($file) {
                $downloadurl = $nextdoc->get_download_url(true)->out(false);
                $mimetype = $file->get_mimetype();
                $ispdf = ($mimetype === 'application/pdf');
                $isimage = strpos($mimetype, 'image/') === 0;

                if ($ispdf || $isimage) {
                    $previewurl = $nextdoc->get_download_url(false)->out(false);
                }
            }

            return [
                'hasnext' => true,
                'documentid' => $nextdoc->id,
                'typename' => $typename,
                'filename' => $nextdoc->filename,
                'docindex' => $docindex,
                'previewurl' => $previewurl,
                'downloadurl' => $downloadurl,
                'ispdf' => $ispdf,
                'isimage' => $isimage,
            ];
        } catch (\Exception $e) {
            return [
                'hasnext' => false,
                'documentid' => 0,
                'typename' => '',
                'filename' => '',
                'docindex' => 0,
                'previewurl' => '',
                'downloadurl' => '',
                'ispdf' => false,
                'isimage' => false,
            ];
        }
    }

    /**
     * Define return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'hasnext' => new external_value(PARAM_BOOL, 'Whether there is a next document'),
            'documentid' => new external_value(PARAM_INT, 'The document ID'),
            'typename' => new external_value(PARAM_TEXT, 'Document type name'),
            'filename' => new external_value(PARAM_TEXT, 'Original filename'),
            'docindex' => new external_value(PARAM_INT, 'Document index in list'),
            'previewurl' => new external_value(PARAM_URL, 'Preview URL'),
            'downloadurl' => new external_value(PARAM_URL, 'Download URL'),
            'ispdf' => new external_value(PARAM_BOOL, 'Whether the document is a PDF'),
            'isimage' => new external_value(PARAM_BOOL, 'Whether the document is an image'),
        ]);
    }
}
