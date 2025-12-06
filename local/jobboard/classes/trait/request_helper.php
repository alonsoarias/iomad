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
 * Request helper trait for local_jobboard.
 *
 * Provides common methods for handling HTTP request information
 * such as client IP address and user agent.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\trait;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait providing HTTP request utility methods.
 *
 * This trait consolidates common request-related functionality
 * that was previously duplicated across multiple classes.
 */
trait request_helper {

    /**
     * Get the client's IP address.
     *
     * Handles various proxy scenarios including X-Forwarded-For headers.
     * Returns '0.0.0.0' if no valid IP can be determined.
     *
     * @return string The validated IP address.
     */
    protected static function get_user_ip(): string {
        // Check for client IP header (when behind some proxies).
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Handle X-Forwarded-For header (common proxy header).
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Fall back to REMOTE_ADDR.
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        // Handle multiple IPs in X-Forwarded-For (take the first one).
        if (strpos($ip, ',') !== false) {
            $ips = explode(',', $ip);
            $ip = trim($ips[0]);
        }

        // Validate the IP address.
        $validatedip = filter_var($ip, FILTER_VALIDATE_IP);
        return $validatedip !== false ? $validatedip : '0.0.0.0';
    }

    /**
     * Get the client's user agent string.
     *
     * Returns a sanitized and truncated user agent string.
     * Maximum length is 512 characters to prevent storage issues.
     *
     * @return string The sanitized user agent (max 512 chars).
     */
    protected static function get_user_agent(): string {
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        // Sanitize the user agent to prevent XSS when displayed in logs.
        $useragent = clean_param($useragent, PARAM_TEXT);
        return substr($useragent, 0, 512);
    }
}
