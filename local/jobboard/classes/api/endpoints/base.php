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
 * Base API endpoint class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\api\endpoints;

defined('MOODLE_INTERNAL') || die();

use local_jobboard\api_token;
use local_jobboard\api\response;

/**
 * Base class for API endpoints.
 */
abstract class base {

    /** @var api_token The authenticated token. */
    protected api_token $token;

    /** @var array Path parameters. */
    protected array $params;

    /** @var array Request body. */
    protected array $body;

    /** @var array Query parameters. */
    protected array $query;

    /** @var int The user ID associated with the token. */
    protected int $userid;

    /**
     * Constructor.
     *
     * @param api_token $token The authenticated token.
     * @param array $params Path parameters.
     * @param array $body Request body.
     * @param array $query Query parameters.
     */
    public function __construct(api_token $token, array $params, array $body, array $query) {
        $this->token = $token;
        $this->params = $params;
        $this->body = $body;
        $this->query = $query;
        $this->userid = $token->userid;
    }

    /**
     * Get a path parameter.
     *
     * @param string $name Parameter name.
     * @param mixed $default Default value.
     * @return mixed The parameter value.
     */
    protected function param(string $name, $default = null) {
        return $this->params[$name] ?? $default;
    }

    /**
     * Get a query parameter.
     *
     * @param string $name Parameter name.
     * @param mixed $default Default value.
     * @return mixed The parameter value.
     */
    protected function query(string $name, $default = null) {
        return $this->query[$name] ?? $default;
    }

    /**
     * Get a body parameter.
     *
     * @param string $name Parameter name.
     * @param mixed $default Default value.
     * @return mixed The parameter value.
     */
    protected function input(string $name, $default = null) {
        return $this->body[$name] ?? $default;
    }

    /**
     * Get an integer path parameter.
     *
     * @param string $name Parameter name.
     * @return int|null The integer value or null.
     */
    protected function param_int(string $name): ?int {
        $value = $this->param($name);
        return $value !== null ? (int) $value : null;
    }

    /**
     * Get an integer query parameter.
     *
     * @param string $name Parameter name.
     * @param int|null $default Default value.
     * @return int|null The integer value.
     */
    protected function query_int(string $name, ?int $default = null): ?int {
        $value = $this->query($name);
        return $value !== null ? (int) $value : $default;
    }

    /**
     * Validate required body fields.
     *
     * @param array $required Required field names.
     * @return array Validation errors.
     */
    protected function validate_required(array $required): array {
        $errors = [];

        foreach ($required as $field) {
            if (!isset($this->body[$field]) || $this->body[$field] === '') {
                $errors[$field] = "The $field field is required";
            }
        }

        return $errors;
    }

    /**
     * Validate body fields against rules.
     *
     * @param array $rules Validation rules [field => [rules]].
     * @return array Validation errors.
     */
    protected function validate(array $rules): array {
        $errors = [];

        foreach ($rules as $field => $fieldrules) {
            $value = $this->body[$field] ?? null;

            foreach ($fieldrules as $rule => $param) {
                $error = $this->apply_rule($field, $value, $rule, $param);
                if ($error) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Apply a validation rule.
     *
     * @param string $field Field name.
     * @param mixed $value Field value.
     * @param string $rule Rule name.
     * @param mixed $param Rule parameter.
     * @return string|null Error message or null.
     */
    private function apply_rule(string $field, $value, string $rule, $param): ?string {
        switch ($rule) {
            case 'required':
                if ($param && ($value === null || $value === '')) {
                    return "The $field field is required";
                }
                break;

            case 'string':
                if ($param && $value !== null && !is_string($value)) {
                    return "The $field field must be a string";
                }
                break;

            case 'integer':
                if ($param && $value !== null && !is_numeric($value)) {
                    return "The $field field must be an integer";
                }
                break;

            case 'boolean':
                if ($param && $value !== null && !is_bool($value) && !in_array($value, [0, 1, '0', '1'], true)) {
                    return "The $field field must be a boolean";
                }
                break;

            case 'array':
                if ($param && $value !== null && !is_array($value)) {
                    return "The $field field must be an array";
                }
                break;

            case 'min':
                if (is_string($value) && strlen($value) < $param) {
                    return "The $field field must be at least $param characters";
                }
                if (is_numeric($value) && $value < $param) {
                    return "The $field field must be at least $param";
                }
                break;

            case 'max':
                if (is_string($value) && strlen($value) > $param) {
                    return "The $field field must not exceed $param characters";
                }
                if (is_numeric($value) && $value > $param) {
                    return "The $field field must not exceed $param";
                }
                break;

            case 'in':
                if ($value !== null && !in_array($value, $param, true)) {
                    return "The $field field must be one of: " . implode(', ', $param);
                }
                break;

            case 'email':
                if ($param && $value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "The $field field must be a valid email address";
                }
                break;
        }

        return null;
    }

    /**
     * Get pagination parameters.
     *
     * @param int $defaultperpage Default items per page.
     * @param int $maxperpage Maximum items per page.
     * @return array ['page' => int, 'perpage' => int, 'offset' => int]
     */
    protected function get_pagination(int $defaultperpage = 25, int $maxperpage = 100): array {
        $page = max(1, $this->query_int('page', 1));
        $perpage = min($maxperpage, max(1, $this->query_int('limit', $defaultperpage)));

        return [
            'page' => $page,
            'perpage' => $perpage,
            'offset' => ($page - 1) * $perpage,
        ];
    }

    /**
     * Build pagination response data.
     *
     * @param int $total Total items.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @return array Pagination data.
     */
    protected function build_pagination(int $total, int $page, int $perpage): array {
        return [
            'total' => $total,
            'page' => $page,
            'per_page' => $perpage,
            'total_pages' => (int) ceil($total / $perpage),
        ];
    }

    /**
     * Success response.
     *
     * @param mixed $data Response data.
     * @param int $statuscode HTTP status code.
     * @param array $pagination Pagination info.
     */
    protected function success($data, int $statuscode = 200, array $pagination = []): void {
        response::success($data, $statuscode, $pagination);
    }

    /**
     * Created response.
     *
     * @param mixed $data Response data.
     */
    protected function created($data): void {
        response::success($data, response::HTTP_CREATED);
    }

    /**
     * Error response.
     *
     * @param string $message Error message.
     * @param string $code Error code.
     * @param int $statuscode HTTP status code.
     */
    protected function error(string $message, string $code, int $statuscode = 400): void {
        response::error($message, $code, $statuscode);
    }
}
