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
 * REST API entry point for local_jobboard.
 *
 * Base URL: /local/jobboard/api/v1/
 *
 * Available endpoints:
 * - GET /vacancies - List published vacancies
 * - GET /vacancies/{id} - Get vacancy details
 * - GET /applications - List user's applications
 * - GET /applications/{id} - Get application details
 * - POST /applications - Create new application
 * - POST /applications/{id}/documents - Upload document
 * - GET /applications/{id}/documents - List application documents
 *
 * Authentication: Bearer token in Authorization header
 * Rate limit: 100 requests per hour per token
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable Moodle's default output handling for API.
define('AJAX_SCRIPT', true);
define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../../../config.php');

// Set up error handling for API.
set_exception_handler(function (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    $response = [
        'success' => false,
        'error' => [
            'code' => 'internal_error',
            'message' => 'An internal error occurred',
        ],
    ];

    // Include details in development mode.
    if (debugging()) {
        $response['error']['debug'] = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
});

// Handle the request.
$router = new \local_jobboard\api\router();
$router->handle();
