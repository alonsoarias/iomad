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
 * reCAPTCHA helper for Job Board.
 *
 * Supports both reCAPTCHA Enterprise and standard reCAPTCHA verification.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educacion Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for reCAPTCHA verification.
 */
class recaptcha_helper {

    /** @var string reCAPTCHA Enterprise API endpoint */
    const ENTERPRISE_API_URL = 'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s';

    /** @var string reCAPTCHA Standard API endpoint */
    const STANDARD_API_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /** @var string reCAPTCHA Enterprise JavaScript URL */
    const ENTERPRISE_JS_URL = 'https://www.google.com/recaptcha/enterprise.js';

    /** @var string reCAPTCHA Standard JavaScript URL */
    const STANDARD_JS_URL = 'https://www.google.com/recaptcha/api.js';

    /**
     * Check if reCAPTCHA is enabled.
     *
     * @return bool
     */
    public static function is_enabled(): bool {
        return (bool) get_config('local_jobboard', 'recaptcha_enabled');
    }

    /**
     * Get the reCAPTCHA type (enterprise or standard).
     *
     * @return string
     */
    public static function get_type(): string {
        return get_config('local_jobboard', 'recaptcha_type') ?: 'enterprise';
    }

    /**
     * Check if using Enterprise version.
     *
     * @return bool
     */
    public static function is_enterprise(): bool {
        return self::get_type() === 'enterprise';
    }

    /**
     * Get the site key.
     *
     * @return string
     */
    public static function get_site_key(): string {
        return get_config('local_jobboard', 'recaptcha_sitekey') ?: '';
    }

    /**
     * Get the score threshold.
     *
     * @return float
     */
    public static function get_threshold(): float {
        $threshold = get_config('local_jobboard', 'recaptcha_threshold');
        return $threshold !== false ? (float) $threshold : 0.5;
    }

    /**
     * Get the JavaScript URL for reCAPTCHA.
     *
     * @return string
     */
    public static function get_js_url(): string {
        if (self::is_enterprise()) {
            return self::ENTERPRISE_JS_URL;
        }
        return self::STANDARD_JS_URL;
    }

    /**
     * Render the reCAPTCHA widget HTML.
     *
     * @param string $action The action name for the assessment (e.g., 'SIGNUP', 'LOGIN', 'APPLY').
     * @return string HTML to include in the form.
     */
    public static function render_widget(string $action = 'SUBMIT'): string {
        if (!self::is_enabled()) {
            return '';
        }

        $sitekey = self::get_site_key();
        if (empty($sitekey)) {
            return '';
        }

        $html = '';

        if (self::is_enterprise()) {
            // reCAPTCHA Enterprise with checkbox widget.
            $html .= '<script src="' . s(self::ENTERPRISE_JS_URL) . '" async defer></script>';
            $html .= '<div class="form-group row fitem mb-3">';
            $html .= '<div class="col-md-3"></div>';
            $html .= '<div class="col-md-9">';
            $html .= '<div class="g-recaptcha" data-sitekey="' . s($sitekey) . '" data-action="' . s($action) . '"></div>';
            $html .= '</div></div>';
        } else {
            // Standard reCAPTCHA v2.
            $html .= '<script src="' . s(self::STANDARD_JS_URL) . '" async defer></script>';
            $html .= '<div class="form-group row fitem mb-3">';
            $html .= '<div class="col-md-3"></div>';
            $html .= '<div class="col-md-9">';
            $html .= '<div class="g-recaptcha" data-sitekey="' . s($sitekey) . '"></div>';
            $html .= '</div></div>';
        }

        return $html;
    }

    /**
     * Verify a reCAPTCHA token.
     *
     * @param string $token The response token from the client.
     * @param string $expectedaction The expected action (for Enterprise).
     * @return array ['success' => bool, 'score' => float|null, 'error' => string|null]
     */
    public static function verify(string $token, string $expectedaction = 'SUBMIT'): array {
        if (empty($token)) {
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_required', 'local_jobboard'),
            ];
        }

        if (self::is_enterprise()) {
            return self::verify_enterprise($token, $expectedaction);
        }

        return self::verify_standard($token);
    }

    /**
     * Verify token with reCAPTCHA Enterprise API.
     *
     * @param string $token The response token.
     * @param string $expectedaction The expected action.
     * @return array
     */
    private static function verify_enterprise(string $token, string $expectedaction): array {
        $projectid = get_config('local_jobboard', 'recaptcha_project_id');
        $apikey = get_config('local_jobboard', 'recaptcha_api_key');
        $sitekey = self::get_site_key();

        if (empty($projectid) || empty($apikey) || empty($sitekey)) {
            debugging('reCAPTCHA Enterprise: Missing configuration (project_id, api_key, or sitekey)', DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_config_error', 'local_jobboard'),
            ];
        }

        $url = sprintf(self::ENTERPRISE_API_URL, $projectid, $apikey);

        $requestbody = [
            'event' => [
                'token' => $token,
                'expectedAction' => $expectedaction,
                'siteKey' => $sitekey,
            ],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestbody),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlerror = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($curlerror)) {
            debugging('reCAPTCHA Enterprise: cURL error - ' . $curlerror, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_network_error', 'local_jobboard'),
            ];
        }

        if ($httpcode !== 200) {
            debugging('reCAPTCHA Enterprise: HTTP error ' . $httpcode . ' - ' . $response, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_api_error', 'local_jobboard'),
            ];
        }

        $result = json_decode($response, true);

        if (empty($result) || !isset($result['tokenProperties'])) {
            debugging('reCAPTCHA Enterprise: Invalid response - ' . $response, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_invalid_response', 'local_jobboard'),
            ];
        }

        // Check token validity.
        $tokenprops = $result['tokenProperties'];
        if (!($tokenprops['valid'] ?? false)) {
            $reason = $tokenprops['invalidReason'] ?? 'UNKNOWN';
            debugging('reCAPTCHA Enterprise: Invalid token - ' . $reason, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_invalid_token', 'local_jobboard'),
            ];
        }

        // Check action matches.
        $tokenaction = $tokenprops['action'] ?? '';
        if (strcasecmp($tokenaction, $expectedaction) !== 0) {
            debugging('reCAPTCHA Enterprise: Action mismatch - expected: ' . $expectedaction . ', got: ' . $tokenaction, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_action_mismatch', 'local_jobboard'),
            ];
        }

        // Get the risk score.
        $score = $result['riskAnalysis']['score'] ?? 0;
        $threshold = self::get_threshold();

        if ($score < $threshold) {
            debugging('reCAPTCHA Enterprise: Score too low - ' . $score . ' < ' . $threshold, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => $score,
                'error' => get_string('recaptcha_score_too_low', 'local_jobboard'),
            ];
        }

        return [
            'success' => true,
            'score' => $score,
            'error' => null,
        ];
    }

    /**
     * Verify token with standard reCAPTCHA API.
     *
     * @param string $token The response token.
     * @return array
     */
    private static function verify_standard(string $token): array {
        $secretkey = get_config('local_jobboard', 'recaptcha_secretkey');

        if (empty($secretkey)) {
            debugging('reCAPTCHA Standard: Missing secret key', DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_config_error', 'local_jobboard'),
            ];
        }

        $postdata = [
            'secret' => $secretkey,
            'response' => $token,
            'remoteip' => getremoteaddr(),
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => self::STANDARD_API_URL,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postdata),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $curlerror = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($curlerror)) {
            debugging('reCAPTCHA Standard: cURL error - ' . $curlerror, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_network_error', 'local_jobboard'),
            ];
        }

        $result = json_decode($response, true);

        if (empty($result)) {
            debugging('reCAPTCHA Standard: Invalid response - ' . $response, DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => null,
                'error' => get_string('recaptcha_invalid_response', 'local_jobboard'),
            ];
        }

        if (!($result['success'] ?? false)) {
            $errors = $result['error-codes'] ?? [];
            debugging('reCAPTCHA Standard: Verification failed - ' . implode(', ', $errors), DEBUG_DEVELOPER);
            return [
                'success' => false,
                'score' => $result['score'] ?? null,
                'error' => get_string('recaptcha_failed', 'local_jobboard'),
            ];
        }

        // For v3, check the score.
        $score = $result['score'] ?? null;
        if ($score !== null) {
            $threshold = self::get_threshold();
            if ($score < $threshold) {
                return [
                    'success' => false,
                    'score' => $score,
                    'error' => get_string('recaptcha_score_too_low', 'local_jobboard'),
                ];
            }
        }

        return [
            'success' => true,
            'score' => $score,
            'error' => null,
        ];
    }

    /**
     * Get the token from POST data.
     *
     * @return string
     */
    public static function get_token_from_request(): string {
        // Enterprise uses g-recaptcha-response, same as standard v2.
        return $_POST['g-recaptcha-response'] ?? '';
    }

    /**
     * Validate reCAPTCHA for a form submission.
     *
     * Convenience method that combines getting the token and verifying it.
     *
     * @param string $action The expected action.
     * @return string|null Error message if validation fails, null if successful.
     */
    public static function validate_form(string $action = 'SUBMIT'): ?string {
        if (!self::is_enabled()) {
            return null; // reCAPTCHA disabled, pass validation.
        }

        $sitekey = self::get_site_key();
        if (empty($sitekey)) {
            return null; // Not configured, pass validation.
        }

        $token = self::get_token_from_request();
        $result = self::verify($token, $action);

        return $result['success'] ? null : $result['error'];
    }
}
