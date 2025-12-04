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
 * API Token management class for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing API tokens.
 */
class api_token {

    /** @var int The token ID. */
    public $id = 0;

    /** @var string The hashed token. */
    public $token = '';

    /** @var int The user ID associated with the token. */
    public $userid = 0;

    /** @var string Token description. */
    public $description = '';

    /** @var array Token permissions. */
    public $permissions = [];

    /** @var array IP whitelist. */
    public $ipwhitelist = [];

    /** @var bool Whether the token is enabled. */
    public $enabled = true;

    /** @var int|null Valid from timestamp. */
    public $validfrom = null;

    /** @var int|null Valid until timestamp. */
    public $validuntil = null;

    /** @var int|null Last used timestamp. */
    public $lastused = null;

    /** @var int Created timestamp. */
    public $timecreated = 0;

    /** Available permissions for API tokens. */
    public const PERMISSIONS = [
        'view_vacancies',
        'view_vacancy_details',
        'create_application',
        'view_applications',
        'view_application_details',
        'upload_documents',
        'view_documents',
    ];

    /** Rate limit: requests per hour per token. */
    public const RATE_LIMIT = 100;

    /** Rate limit window in seconds (1 hour). */
    public const RATE_LIMIT_WINDOW = 3600;

    /**
     * Constructor.
     *
     * @param int|stdClass|null $idorrecord Token ID, record, or null.
     */
    public function __construct($idorrecord = null) {
        if ($idorrecord === null) {
            return;
        }

        if (is_object($idorrecord)) {
            $this->load_from_record($idorrecord);
        } else {
            $this->load((int) $idorrecord);
        }
    }

    /**
     * Load token from ID.
     *
     * @param int $id The token ID.
     */
    public function load(int $id): void {
        global $DB;

        $record = $DB->get_record('local_jobboard_api_token', ['id' => $id], '*', MUST_EXIST);
        $this->load_from_record($record);
    }

    /**
     * Load token from database record.
     *
     * @param \stdClass $record The database record.
     */
    public function load_from_record(\stdClass $record): void {
        $this->id = (int) $record->id;
        $this->token = $record->token;
        $this->userid = (int) $record->userid;
        $this->description = $record->description ?? '';
        $this->permissions = json_decode($record->permissions ?? '[]', true) ?: [];
        $this->ipwhitelist = json_decode($record->ipwhitelist ?? '[]', true) ?: [];
        $this->enabled = (bool) $record->enabled;
        $this->validfrom = $record->validfrom ? (int) $record->validfrom : null;
        $this->validuntil = $record->validuntil ? (int) $record->validuntil : null;
        $this->lastused = $record->lastused ? (int) $record->lastused : null;
        $this->timecreated = (int) $record->timecreated;
    }

    /**
     * Get a token by ID.
     *
     * @param int $id The token ID.
     * @return self|null The token or null.
     */
    public static function get(int $id): ?self {
        global $DB;

        $record = $DB->get_record('local_jobboard_api_token', ['id' => $id]);
        if (!$record) {
            return null;
        }

        return new self($record);
    }

    /**
     * Create a new API token.
     *
     * @param int $userid The user ID.
     * @param string $description Token description.
     * @param array $permissions Array of permission strings.
     * @param array $ipwhitelist Array of allowed IPs (optional).
     * @param int|null $validfrom Valid from timestamp (optional).
     * @param int|null $validuntil Valid until timestamp (optional).
     * @return array ['token' => raw token string, 'object' => api_token object]
     */
    public static function create(
        int $userid,
        string $description,
        array $permissions = [],
        array $ipwhitelist = [],
        ?int $validfrom = null,
        ?int $validuntil = null
    ): array {
        global $DB;

        // Generate secure random token.
        $rawtoken = self::generate_token();
        $hashedtoken = self::hash_token($rawtoken);

        // Validate permissions.
        $permissions = array_intersect($permissions, self::PERMISSIONS);

        $token = new self();
        $token->token = $hashedtoken;
        $token->userid = $userid;
        $token->description = $description;
        $token->permissions = $permissions;
        $token->ipwhitelist = $ipwhitelist;
        $token->enabled = true;
        $token->validfrom = $validfrom;
        $token->validuntil = $validuntil;
        $token->timecreated = time();

        $record = $token->to_record();
        unset($record->id);
        $token->id = $DB->insert_record('local_jobboard_api_token', $record);

        // Log audit.
        audit::log('api_token_created', 'api_token', $token->id, [
            'userid' => $userid,
            'description' => $description,
        ]);

        return [
            'token' => $rawtoken,
            'object' => $token,
        ];
    }

    /**
     * Generate a secure random token.
     *
     * @return string The raw token (64 characters hex).
     */
    private static function generate_token(): string {
        return bin2hex(random_bytes(32));
    }

    /**
     * Hash a token for storage.
     *
     * @param string $rawtoken The raw token.
     * @return string The hashed token.
     */
    private static function hash_token(string $rawtoken): string {
        return hash('sha256', $rawtoken);
    }

    /**
     * Validate and get token from raw token string.
     *
     * @param string $rawtoken The raw token from request.
     * @return self|null The token object if valid, null otherwise.
     */
    public static function validate(string $rawtoken): ?self {
        global $DB;

        $hashedtoken = self::hash_token($rawtoken);

        $record = $DB->get_record('local_jobboard_api_token', ['token' => $hashedtoken]);
        if (!$record) {
            return null;
        }

        $token = new self($record);

        // Check if enabled.
        if (!$token->enabled) {
            return null;
        }

        // Check validity period.
        $now = time();
        if ($token->validfrom && $now < $token->validfrom) {
            return null;
        }
        if ($token->validuntil && $now > $token->validuntil) {
            return null;
        }

        return $token;
    }

    /**
     * Validate token and check IP whitelist.
     *
     * @param string $rawtoken The raw token.
     * @param string $clientip The client IP address.
     * @return self|null The token if valid, null otherwise.
     */
    public static function validate_with_ip(string $rawtoken, string $clientip): ?self {
        $token = self::validate($rawtoken);
        if (!$token) {
            return null;
        }

        // Check IP whitelist.
        if (!$token->is_ip_allowed($clientip)) {
            return null;
        }

        return $token;
    }

    /**
     * Check if an IP is allowed.
     *
     * @param string $ip The IP address to check.
     * @return bool True if allowed.
     */
    public function is_ip_allowed(string $ip): bool {
        // Empty whitelist means all IPs allowed.
        if (empty($this->ipwhitelist)) {
            return true;
        }

        // Check if IP is in whitelist.
        foreach ($this->ipwhitelist as $allowed) {
            if ($this->ip_matches($ip, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP matches a pattern (supports CIDR notation).
     *
     * @param string $ip The IP to check.
     * @param string $pattern The pattern (IP or CIDR).
     * @return bool True if matches.
     */
    private function ip_matches(string $ip, string $pattern): bool {
        // Exact match.
        if ($ip === $pattern) {
            return true;
        }

        // CIDR notation.
        if (strpos($pattern, '/') !== false) {
            list($subnet, $bits) = explode('/', $pattern);
            $bits = (int) $bits;

            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);

            return ($ip & $mask) === ($subnet & $mask);
        }

        return false;
    }

    /**
     * Check if token has a specific permission.
     *
     * @param string $permission The permission to check.
     * @return bool True if has permission.
     */
    public function has_permission(string $permission): bool {
        return in_array($permission, $this->permissions);
    }

    /**
     * Record token usage.
     */
    public function record_usage(): void {
        global $DB;

        $this->lastused = time();
        $DB->set_field('local_jobboard_api_token', 'lastused', $this->lastused, ['id' => $this->id]);
    }

    /**
     * Check rate limit for this token.
     *
     * @return array ['allowed' => bool, 'remaining' => int, 'reset' => timestamp]
     */
    public function check_rate_limit(): array {
        $cache = \cache::make('local_jobboard', 'api_rate_limit');
        $key = 'token_' . $this->id;

        $data = $cache->get($key);
        $now = time();

        if (!$data || $data['window_start'] + self::RATE_LIMIT_WINDOW < $now) {
            // New window.
            $data = [
                'count' => 1,
                'window_start' => $now,
            ];
            $cache->set($key, $data);

            return [
                'allowed' => true,
                'remaining' => self::RATE_LIMIT - 1,
                'reset' => $now + self::RATE_LIMIT_WINDOW,
            ];
        }

        // Check limit.
        if ($data['count'] >= self::RATE_LIMIT) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset' => $data['window_start'] + self::RATE_LIMIT_WINDOW,
            ];
        }

        // Increment counter.
        $data['count']++;
        $cache->set($key, $data);

        return [
            'allowed' => true,
            'remaining' => self::RATE_LIMIT - $data['count'],
            'reset' => $data['window_start'] + self::RATE_LIMIT_WINDOW,
        ];
    }

    /**
     * Update token.
     *
     * @param \stdClass $data Updated data.
     */
    public function update(\stdClass $data): void {
        global $DB;

        if (isset($data->description)) {
            $this->description = $data->description;
        }
        if (isset($data->permissions)) {
            $this->permissions = array_intersect($data->permissions, self::PERMISSIONS);
        }
        if (isset($data->ipwhitelist)) {
            $this->ipwhitelist = $data->ipwhitelist;
        }
        if (isset($data->enabled)) {
            $this->enabled = (bool) $data->enabled;
        }
        if (array_key_exists('validfrom', (array) $data)) {
            $this->validfrom = $data->validfrom;
        }
        if (array_key_exists('validuntil', (array) $data)) {
            $this->validuntil = $data->validuntil;
        }

        $DB->update_record('local_jobboard_api_token', $this->to_record());

        audit::log('api_token_updated', 'api_token', $this->id);
    }

    /**
     * Revoke (disable) the token.
     */
    public function revoke(): void {
        global $DB;

        $this->enabled = false;
        $DB->set_field('local_jobboard_api_token', 'enabled', 0, ['id' => $this->id]);

        audit::log('api_token_revoked', 'api_token', $this->id);
    }

    /**
     * Delete the token.
     */
    public function delete(): void {
        global $DB;

        $DB->delete_records('local_jobboard_api_token', ['id' => $this->id]);

        audit::log('api_token_deleted', 'api_token', $this->id);
    }

    /**
     * Convert to database record.
     *
     * @return \stdClass The database record.
     */
    public function to_record(): \stdClass {
        return (object) [
            'id' => $this->id ?: null,
            'token' => $this->token,
            'userid' => $this->userid,
            'description' => $this->description,
            'permissions' => json_encode($this->permissions),
            'ipwhitelist' => json_encode($this->ipwhitelist),
            'enabled' => $this->enabled ? 1 : 0,
            'validfrom' => $this->validfrom,
            'validuntil' => $this->validuntil,
            'lastused' => $this->lastused,
            'timecreated' => $this->timecreated,
        ];
    }

    /**
     * Get tokens for a user.
     *
     * @param int $userid The user ID.
     * @return array Array of api_token objects.
     */
    public static function get_user_tokens(int $userid): array {
        global $DB;

        $records = $DB->get_records('local_jobboard_api_token', ['userid' => $userid], 'timecreated DESC');
        $tokens = [];

        foreach ($records as $record) {
            $tokens[] = new self($record);
        }

        return $tokens;
    }

    /**
     * Get all tokens.
     *
     * @param bool $enabledonly Only return enabled tokens.
     * @return array Array of api_token objects.
     */
    public static function get_all(bool $enabledonly = false): array {
        global $DB;

        $params = [];
        if ($enabledonly) {
            $params['enabled'] = 1;
        }

        $records = $DB->get_records('local_jobboard_api_token', $params, 'timecreated DESC');
        $tokens = [];

        foreach ($records as $record) {
            $tokens[] = new self($record);
        }

        return $tokens;
    }

    /**
     * Get user object associated with this token.
     *
     * @return \stdClass|null The user object.
     */
    public function get_user(): ?\stdClass {
        global $DB;

        return $DB->get_record('user', ['id' => $this->userid]) ?: null;
    }

    /**
     * Get permission display names.
     *
     * @return array Permission code => display name.
     */
    public static function get_permission_names(): array {
        $names = [];
        foreach (self::PERMISSIONS as $permission) {
            $names[$permission] = get_string('api:permission:' . $permission, 'local_jobboard');
        }
        return $names;
    }

    /**
     * Format permissions for display.
     *
     * @return string Comma-separated permission names.
     */
    public function get_permissions_display(): string {
        $names = self::get_permission_names();
        $display = [];

        foreach ($this->permissions as $permission) {
            $display[] = $names[$permission] ?? $permission;
        }

        return implode(', ', $display);
    }

    /**
     * Check if token is currently valid (within validity period).
     *
     * @return bool True if valid.
     */
    public function is_valid(): bool {
        if (!$this->enabled) {
            return false;
        }

        $now = time();
        if ($this->validfrom && $now < $this->validfrom) {
            return false;
        }
        if ($this->validuntil && $now > $this->validuntil) {
            return false;
        }

        return true;
    }

    /**
     * Get token status string.
     *
     * @return string Status: active, disabled, expired, not_yet_valid.
     */
    public function get_status(): string {
        if (!$this->enabled) {
            return 'disabled';
        }

        $now = time();
        if ($this->validfrom && $now < $this->validfrom) {
            return 'not_yet_valid';
        }
        if ($this->validuntil && $now > $this->validuntil) {
            return 'expired';
        }

        return 'active';
    }

    /**
     * Get status display string.
     *
     * @return string Localized status.
     */
    public function get_status_display(): string {
        return get_string('api:token:status:' . $this->get_status(), 'local_jobboard');
    }
}
