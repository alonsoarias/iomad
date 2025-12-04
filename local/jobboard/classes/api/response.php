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
 * API Response class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\api;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for handling API responses.
 */
class response {

    /** HTTP Status Codes */
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_CONFLICT = 409;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_TOO_MANY_REQUESTS = 429;
    public const HTTP_INTERNAL_ERROR = 500;

    /**
     * Send a success response.
     *
     * @param mixed $data The response data.
     * @param int $statuscode HTTP status code.
     * @param array $pagination Pagination info (optional).
     */
    public static function success($data, int $statuscode = self::HTTP_OK, array $pagination = []): void {
        self::send([
            'success' => true,
            'data' => $data,
            'pagination' => !empty($pagination) ? $pagination : null,
        ], $statuscode);
    }

    /**
     * Send an error response.
     *
     * @param string $message Error message.
     * @param string $code Error code.
     * @param int $statuscode HTTP status code.
     * @param array $details Additional error details (optional).
     */
    public static function error(
        string $message,
        string $code,
        int $statuscode = self::HTTP_BAD_REQUEST,
        array $details = []
    ): void {
        $error = [
            'code' => $code,
            'message' => $message,
        ];

        if (!empty($details)) {
            $error['details'] = $details;
        }

        self::send([
            'success' => false,
            'error' => $error,
        ], $statuscode);
    }

    /**
     * Send a response and exit.
     *
     * @param array $data The response data.
     * @param int $statuscode HTTP status code.
     */
    private static function send(array $data, int $statuscode): void {
        // Remove null values.
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        http_response_code($statuscode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send a bad request error.
     *
     * @param string $message Error message.
     * @param array $details Additional details.
     */
    public static function bad_request(string $message, array $details = []): void {
        self::error($message, 'bad_request', self::HTTP_BAD_REQUEST, $details);
    }

    /**
     * Send an unauthorized error.
     *
     * @param string $message Error message.
     */
    public static function unauthorized(string $message = 'Authentication required'): void {
        self::error($message, 'unauthorized', self::HTTP_UNAUTHORIZED);
    }

    /**
     * Send a forbidden error.
     *
     * @param string $message Error message.
     */
    public static function forbidden(string $message = 'Access denied'): void {
        self::error($message, 'forbidden', self::HTTP_FORBIDDEN);
    }

    /**
     * Send a not found error.
     *
     * @param string $message Error message.
     */
    public static function not_found(string $message = 'Resource not found'): void {
        self::error($message, 'not_found', self::HTTP_NOT_FOUND);
    }

    /**
     * Send a method not allowed error.
     *
     * @param array $allowedmethods Allowed HTTP methods.
     */
    public static function method_not_allowed(array $allowedmethods): void {
        header('Allow: ' . implode(', ', $allowedmethods));
        self::error(
            'Method not allowed. Allowed methods: ' . implode(', ', $allowedmethods),
            'method_not_allowed',
            self::HTTP_METHOD_NOT_ALLOWED
        );
    }

    /**
     * Send a rate limit exceeded error.
     *
     * @param int $retryafter Seconds until rate limit resets.
     * @param int $limit The rate limit.
     */
    public static function rate_limited(int $retryafter, int $limit): void {
        header('Retry-After: ' . $retryafter);
        self::error(
            "Rate limit exceeded. Maximum $limit requests per hour.",
            'rate_limit_exceeded',
            self::HTTP_TOO_MANY_REQUESTS
        );
    }

    /**
     * Send a validation error.
     *
     * @param array $errors Validation errors.
     */
    public static function validation_error(array $errors): void {
        self::error(
            'Validation failed',
            'validation_error',
            self::HTTP_UNPROCESSABLE_ENTITY,
            ['validation_errors' => $errors]
        );
    }

    /**
     * Send an internal server error.
     *
     * @param string $message Error message.
     */
    public static function internal_error(string $message = 'Internal server error'): void {
        self::error($message, 'internal_error', self::HTTP_INTERNAL_ERROR);
    }

    /**
     * Set rate limit headers.
     *
     * @param int $limit Rate limit.
     * @param int $remaining Remaining requests.
     * @param int $reset Reset timestamp.
     */
    public static function set_rate_limit_headers(int $limit, int $remaining, int $reset): void {
        header('X-RateLimit-Limit: ' . $limit);
        header('X-RateLimit-Remaining: ' . $remaining);
        header('X-RateLimit-Reset: ' . $reset);
    }

    /**
     * Set security headers.
     */
    public static function set_security_headers(): void {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}
