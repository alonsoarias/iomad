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
 * AJAX endpoint for document conversion status.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

use local_jobboard\document;
use local_jobboard\document_services;

$documentid = required_param('documentid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);

require_login();
require_sesskey();

$context = context_system::instance();

header('Content-Type: application/json');

try {
    // Get document.
    $document = document::get($documentid);
    if (!$document) {
        throw new moodle_exception('documentnotfound', 'local_jobboard');
    }

    // Check capability - user must be able to review documents or own the document.
    $canreview = has_capability('local/jobboard:reviewdocuments', $context);
    $application = \local_jobboard\application::get($document->applicationid);
    $isowner = ($application && $application->userid == $USER->id);

    if (!$canreview && !$isowner) {
        throw new moodle_exception('nopermission');
    }

    $result = [];

    switch ($action) {
        case 'status':
            // Get current conversion status without starting new conversion.
            $result = document_services::get_preview_info($document, false);
            $result['status_message'] = document_services::get_status_message($result['status']);
            break;

        case 'start':
            // Start conversion if not already done.
            $result = document_services::get_preview_info($document, true);
            $result['status_message'] = document_services::get_status_message($result['status']);
            break;

        case 'poll':
            // Poll for conversion updates.
            $result = document_services::poll_conversion($document);
            $result['status_message'] = document_services::get_status_message($result['status']);
            break;

        case 'info':
            // Get full document info including conversion capabilities.
            $result = [
                'documentid' => $document->id,
                'filename' => $document->filename,
                'mimetype' => $document->mimetype,
                'can_preview' => document_services::can_preview($document),
                'can_convert' => document_services::can_convert_to_pdf($document),
                'converters_available' => document_services::converters_available(),
                'preview' => document_services::get_preview_info($document, false),
            ];
            break;

        default:
            throw new moodle_exception('invalidaction', 'local_jobboard');
    }

    $result['success'] = true;
    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
