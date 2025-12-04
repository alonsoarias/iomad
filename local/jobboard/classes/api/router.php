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
 * API Router class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\api;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\api_token;
use local_jobboard\audit;

/**
 * Class for routing API requests.
 */
class router {

    /** @var api_token|null The authenticated token. */
    private ?api_token $token = null;

    /** @var string The HTTP method. */
    private string $method;

    /** @var string The request path. */
    private string $path;

    /** @var array Path parameters extracted from route. */
    private array $params = [];

    /** @var array Request body (for POST/PUT). */
    private array $body = [];

    /** @var array Query parameters. */
    private array $query = [];

    /** @var float Request start time. */
    private float $starttime;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->starttime = microtime(true);
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = $this->parse_path();
        $this->query = $_GET;
        $this->body = $this->parse_body();
    }

    /**
     * Parse the request path.
     *
     * @return string The cleaned path.
     */
    private function parse_path(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $basepath = '/local/jobboard/api/v1';

        // Remove query string.
        $path = parse_url($uri, PHP_URL_PATH);

        // Remove base path.
        if (strpos($path, $basepath) === 0) {
            $path = substr($path, strlen($basepath));
        }

        // Clean up.
        $path = '/' . trim($path, '/');

        return $path;
    }

    /**
     * Parse the request body.
     *
     * @return array The parsed body.
     */
    private function parse_body(): array {
        $contenttype = $_SERVER['CONTENT_TYPE'] ?? '';

        // JSON body.
        if (strpos($contenttype, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            return is_array($data) ? $data : [];
        }

        // Form data.
        if (strpos($contenttype, 'application/x-www-form-urlencoded') !== false ||
            strpos($contenttype, 'multipart/form-data') !== false) {
            return $_POST;
        }

        return [];
    }

    /**
     * Handle the API request.
     */
    public function handle(): void {
        // Set security headers.
        response::set_security_headers();

        // Check if API is enabled.
        if (!get_config('local_jobboard', 'api_enabled')) {
            response::forbidden('API is disabled');
        }

        // Require HTTPS in production.
        if (!$this->is_secure() && !PHPUNIT_TEST && !defined('BEHAT_TEST')) {
            response::forbidden('HTTPS is required');
        }

        // Handle CORS preflight.
        if ($this->method === 'OPTIONS') {
            $this->handle_cors();
            exit;
        }

        // Authenticate.
        $this->authenticate();

        // Check rate limit.
        $this->check_rate_limit();

        // Route the request.
        $this->route();
    }

    /**
     * Check if the connection is secure (HTTPS).
     *
     * @return bool True if secure.
     */
    private function is_secure(): bool {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
            return true;
        }
        return false;
    }

    /**
     * Handle CORS preflight request.
     */
    private function handle_cors(): void {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Access-Control-Max-Age: 86400');
        http_response_code(204);
    }

    /**
     * Authenticate the request.
     */
    private function authenticate(): void {
        $authheader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Check for Bearer token.
        if (!preg_match('/^Bearer\s+(.+)$/i', $authheader, $matches)) {
            response::unauthorized('Missing or invalid Authorization header. Use: Authorization: Bearer <token>');
        }

        $rawtoken = $matches[1];
        $clientip = $this->get_client_ip();

        // Validate token with IP check.
        $this->token = api_token::validate_with_ip($rawtoken, $clientip);

        if (!$this->token) {
            // Log failed authentication attempt.
            audit::log('api_auth_failed', 'api', 0, [
                'ip' => $clientip,
            ]);
            response::unauthorized('Invalid or expired API token');
        }

        // Record usage.
        $this->token->record_usage();
    }

    /**
     * Get the client IP address.
     *
     * @return string The client IP.
     */
    private function get_client_ip(): string {
        // Check for forwarded IP (behind proxy).
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Check rate limit.
     */
    private function check_rate_limit(): void {
        $result = $this->token->check_rate_limit();

        response::set_rate_limit_headers(
            api_token::RATE_LIMIT,
            $result['remaining'],
            $result['reset']
        );

        if (!$result['allowed']) {
            response::rate_limited($result['reset'] - time(), api_token::RATE_LIMIT);
        }
    }

    /**
     * Route the request to the appropriate handler.
     */
    private function route(): void {
        $routes = $this->get_routes();

        foreach ($routes as $route) {
            if ($route['method'] !== $this->method) {
                continue;
            }

            $pattern = $this->route_to_regex($route['path']);
            if (preg_match($pattern, $this->path, $matches)) {
                // Extract named parameters.
                $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Check permission.
                if (!empty($route['permission']) && !$this->token->has_permission($route['permission'])) {
                    response::forbidden('Insufficient permissions for this endpoint');
                }

                // Call handler.
                $this->call_handler($route['handler']);
                return;
            }
        }

        // No route matched.
        response::not_found('Endpoint not found: ' . $this->path);
    }

    /**
     * Get the route definitions.
     *
     * @return array Route definitions.
     */
    private function get_routes(): array {
        return [
            // Vacancies.
            [
                'method' => 'GET',
                'path' => '/vacancies',
                'handler' => ['vacancies', 'list'],
                'permission' => 'view_vacancies',
            ],
            [
                'method' => 'GET',
                'path' => '/vacancies/{id}',
                'handler' => ['vacancies', 'get'],
                'permission' => 'view_vacancy_details',
            ],

            // Applications.
            [
                'method' => 'GET',
                'path' => '/applications',
                'handler' => ['applications', 'list'],
                'permission' => 'view_applications',
            ],
            [
                'method' => 'GET',
                'path' => '/applications/{id}',
                'handler' => ['applications', 'get'],
                'permission' => 'view_application_details',
            ],
            [
                'method' => 'POST',
                'path' => '/applications',
                'handler' => ['applications', 'create'],
                'permission' => 'create_application',
            ],

            // Documents.
            [
                'method' => 'POST',
                'path' => '/applications/{id}/documents',
                'handler' => ['documents', 'upload'],
                'permission' => 'upload_documents',
            ],
            [
                'method' => 'GET',
                'path' => '/applications/{id}/documents',
                'handler' => ['documents', 'list'],
                'permission' => 'view_documents',
            ],
        ];
    }

    /**
     * Convert route path to regex pattern.
     *
     * @param string $route The route path.
     * @return string The regex pattern.
     */
    private function route_to_regex(string $route): string {
        // Escape slashes.
        $pattern = str_replace('/', '\/', $route);

        // Replace {param} with named capture groups.
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $pattern);

        return '/^' . $pattern . '$/';
    }

    /**
     * Call a handler method.
     *
     * @param array $handler Handler definition [controller, method].
     */
    private function call_handler(array $handler): void {
        list($controller, $method) = $handler;

        $classname = "\\local_jobboard\\api\\endpoints\\{$controller}";

        if (!class_exists($classname)) {
            response::internal_error("Controller not found: $controller");
        }

        $instance = new $classname($this->token, $this->params, $this->body, $this->query);

        if (!method_exists($instance, $method)) {
            response::internal_error("Method not found: $method");
        }

        try {
            // Log API request.
            $this->log_request($controller, $method);

            // Call handler.
            $instance->$method();
        } catch (\moodle_exception $e) {
            response::bad_request($e->getMessage());
        } catch (\Exception $e) {
            response::internal_error('An error occurred');
        }
    }

    /**
     * Log the API request.
     *
     * @param string $controller Controller name.
     * @param string $method Method name.
     */
    private function log_request(string $controller, string $method): void {
        $elapsed = microtime(true) - $this->starttime;

        audit::log('api_request', 'api', 0, [
            'endpoint' => $this->path,
            'method' => $this->method,
            'controller' => $controller,
            'action' => $method,
            'token_id' => $this->token->id,
            'response_time_ms' => round($elapsed * 1000, 2),
        ]);
    }

    /**
     * Get the authenticated token.
     *
     * @return api_token|null The token.
     */
    public function get_token(): ?api_token {
        return $this->token;
    }
}
