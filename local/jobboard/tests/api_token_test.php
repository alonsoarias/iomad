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
 * Unit tests for the API token class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Test class for API token functionality.
 *
 * @coversDefaultClass \local_jobboard\api_token
 */
class api_token_test extends \advanced_testcase {

    /** @var \stdClass Test user. */
    protected $user;

    /**
     * Set up before each test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->user = $this->getDataGenerator()->create_user();
    }

    /**
     * Test creating a new API token.
     *
     * @covers ::create
     */
    public function test_create_token(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies', 'view_vacancy_details']
        );

        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('object', $result);
        $this->assertNotEmpty($result['token']);
        $this->assertInstanceOf(api_token::class, $result['object']);

        // Token should be 64 characters (hex encoded 32 bytes).
        $this->assertEquals(64, strlen($result['token']));

        // Object should have correct properties.
        $this->assertEquals($this->user->id, $result['object']->userid);
        $this->assertEquals('Test token', $result['object']->description);
        $this->assertTrue($result['object']->enabled);
        $this->assertContains('view_vacancies', $result['object']->permissions);
    }

    /**
     * Test that created tokens are hashed in database.
     *
     * @covers ::create
     */
    public function test_token_is_hashed_in_database(): void {
        global $DB;

        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies']
        );

        $rawToken = $result['token'];
        $record = $DB->get_record('local_jobboard_api_token', ['id' => $result['object']->id]);

        // Raw token should not match stored token (stored is hashed).
        $this->assertNotEquals($rawToken, $record->token);

        // Stored token should be SHA256 hash (64 characters hex).
        $this->assertEquals(64, strlen($record->token));
    }

    /**
     * Test validating a token.
     *
     * @covers ::validate
     */
    public function test_validate_token(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies']
        );

        $validated = api_token::validate($result['token']);

        $this->assertNotNull($validated);
        $this->assertEquals($result['object']->id, $validated->id);
        $this->assertEquals($this->user->id, $validated->userid);
    }

    /**
     * Test validating an invalid token.
     *
     * @covers ::validate
     */
    public function test_validate_invalid_token(): void {
        $validated = api_token::validate('invalid_token_string');

        $this->assertNull($validated);
    }

    /**
     * Test validating a disabled token.
     *
     * @covers ::validate
     * @covers ::revoke
     */
    public function test_validate_disabled_token(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies']
        );

        // Revoke the token.
        $result['object']->revoke();

        // Validation should fail.
        $validated = api_token::validate($result['token']);

        $this->assertNull($validated);
    }

    /**
     * Test token validity period - not yet valid.
     *
     * @covers ::validate
     */
    public function test_token_not_yet_valid(): void {
        $futureTime = time() + 3600; // 1 hour from now.

        $result = api_token::create(
            $this->user->id,
            'Future token',
            ['view_vacancies'],
            [],
            $futureTime,
            null
        );

        $validated = api_token::validate($result['token']);

        $this->assertNull($validated);
    }

    /**
     * Test token validity period - expired.
     *
     * @covers ::validate
     */
    public function test_token_expired(): void {
        $pastTime = time() - 3600; // 1 hour ago.

        $result = api_token::create(
            $this->user->id,
            'Expired token',
            ['view_vacancies'],
            [],
            null,
            $pastTime
        );

        $validated = api_token::validate($result['token']);

        $this->assertNull($validated);
    }

    /**
     * Test token with valid validity period.
     *
     * @covers ::validate
     */
    public function test_token_within_validity_period(): void {
        $pastTime = time() - 3600; // 1 hour ago.
        $futureTime = time() + 3600; // 1 hour from now.

        $result = api_token::create(
            $this->user->id,
            'Valid period token',
            ['view_vacancies'],
            [],
            $pastTime,
            $futureTime
        );

        $validated = api_token::validate($result['token']);

        $this->assertNotNull($validated);
    }

    /**
     * Test IP whitelist - empty whitelist allows all.
     *
     * @covers ::is_ip_allowed
     */
    public function test_ip_whitelist_empty_allows_all(): void {
        $result = api_token::create(
            $this->user->id,
            'No IP restriction',
            ['view_vacancies'],
            [] // Empty whitelist.
        );

        $this->assertTrue($result['object']->is_ip_allowed('192.168.1.1'));
        $this->assertTrue($result['object']->is_ip_allowed('10.0.0.1'));
        $this->assertTrue($result['object']->is_ip_allowed('8.8.8.8'));
    }

    /**
     * Test IP whitelist with specific IPs.
     *
     * @covers ::is_ip_allowed
     */
    public function test_ip_whitelist_specific_ips(): void {
        $result = api_token::create(
            $this->user->id,
            'IP restricted',
            ['view_vacancies'],
            ['192.168.1.1', '10.0.0.1']
        );

        $this->assertTrue($result['object']->is_ip_allowed('192.168.1.1'));
        $this->assertTrue($result['object']->is_ip_allowed('10.0.0.1'));
        $this->assertFalse($result['object']->is_ip_allowed('192.168.1.2'));
        $this->assertFalse($result['object']->is_ip_allowed('8.8.8.8'));
    }

    /**
     * Test IP whitelist with CIDR notation.
     *
     * @covers ::is_ip_allowed
     */
    public function test_ip_whitelist_cidr(): void {
        $result = api_token::create(
            $this->user->id,
            'CIDR restricted',
            ['view_vacancies'],
            ['192.168.1.0/24']
        );

        $this->assertTrue($result['object']->is_ip_allowed('192.168.1.1'));
        $this->assertTrue($result['object']->is_ip_allowed('192.168.1.100'));
        $this->assertTrue($result['object']->is_ip_allowed('192.168.1.254'));
        $this->assertFalse($result['object']->is_ip_allowed('192.168.2.1'));
        $this->assertFalse($result['object']->is_ip_allowed('10.0.0.1'));
    }

    /**
     * Test validate_with_ip.
     *
     * @covers ::validate_with_ip
     */
    public function test_validate_with_ip(): void {
        $result = api_token::create(
            $this->user->id,
            'IP restricted',
            ['view_vacancies'],
            ['192.168.1.0/24']
        );

        $validated = api_token::validate_with_ip($result['token'], '192.168.1.50');
        $this->assertNotNull($validated);

        $validated = api_token::validate_with_ip($result['token'], '10.0.0.1');
        $this->assertNull($validated);
    }

    /**
     * Test has_permission.
     *
     * @covers ::has_permission
     */
    public function test_has_permission(): void {
        $result = api_token::create(
            $this->user->id,
            'Limited permissions',
            ['view_vacancies', 'view_vacancy_details']
        );

        $this->assertTrue($result['object']->has_permission('view_vacancies'));
        $this->assertTrue($result['object']->has_permission('view_vacancy_details'));
        $this->assertFalse($result['object']->has_permission('create_application'));
        $this->assertFalse($result['object']->has_permission('invalid_permission'));
    }

    /**
     * Test only valid permissions are stored.
     *
     * @covers ::create
     */
    public function test_invalid_permissions_filtered(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies', 'invalid_permission', 'another_invalid']
        );

        $this->assertCount(1, $result['object']->permissions);
        $this->assertContains('view_vacancies', $result['object']->permissions);
        $this->assertNotContains('invalid_permission', $result['object']->permissions);
    }

    /**
     * Test record_usage updates lastused timestamp.
     *
     * @covers ::record_usage
     */
    public function test_record_usage(): void {
        global $DB;

        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies']
        );

        $this->assertNull($result['object']->lastused);

        $result['object']->record_usage();

        // Reload from database.
        $record = $DB->get_record('local_jobboard_api_token', ['id' => $result['object']->id]);

        $this->assertNotNull($record->lastused);
        $this->assertGreaterThanOrEqual(time() - 2, $record->lastused);
    }

    /**
     * Test rate limiting.
     *
     * @covers ::check_rate_limit
     */
    public function test_rate_limit(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies']
        );

        // First request should be allowed.
        $rateResult = $result['object']->check_rate_limit();
        $this->assertTrue($rateResult['allowed']);
        $this->assertEquals(api_token::RATE_LIMIT - 1, $rateResult['remaining']);

        // Make more requests.
        for ($i = 0; $i < api_token::RATE_LIMIT - 1; $i++) {
            $rateResult = $result['object']->check_rate_limit();
        }

        // Should now be at the limit.
        $this->assertFalse($rateResult['allowed']);
        $this->assertEquals(0, $rateResult['remaining']);
    }

    /**
     * Test token update.
     *
     * @covers ::update
     */
    public function test_update_token(): void {
        $result = api_token::create(
            $this->user->id,
            'Original description',
            ['view_vacancies']
        );

        $updateData = (object) [
            'description' => 'Updated description',
            'permissions' => ['view_vacancies', 'view_vacancy_details', 'create_application'],
            'enabled' => false,
        ];

        $result['object']->update($updateData);

        // Reload.
        $updated = api_token::get($result['object']->id);

        $this->assertEquals('Updated description', $updated->description);
        $this->assertFalse($updated->enabled);
        $this->assertCount(3, $updated->permissions);
    }

    /**
     * Test token deletion.
     *
     * @covers ::delete
     */
    public function test_delete_token(): void {
        global $DB;

        $result = api_token::create(
            $this->user->id,
            'To be deleted',
            ['view_vacancies']
        );

        $tokenId = $result['object']->id;
        $this->assertTrue($DB->record_exists('local_jobboard_api_token', ['id' => $tokenId]));

        $result['object']->delete();

        $this->assertFalse($DB->record_exists('local_jobboard_api_token', ['id' => $tokenId]));
    }

    /**
     * Test get_user_tokens.
     *
     * @covers ::get_user_tokens
     */
    public function test_get_user_tokens(): void {
        // Create multiple tokens for user.
        api_token::create($this->user->id, 'Token 1', ['view_vacancies']);
        api_token::create($this->user->id, 'Token 2', ['view_applications']);
        api_token::create($this->user->id, 'Token 3', ['create_application']);

        // Create token for different user.
        $user2 = $this->getDataGenerator()->create_user();
        api_token::create($user2->id, 'Other user token', ['view_vacancies']);

        $tokens = api_token::get_user_tokens($this->user->id);

        $this->assertCount(3, $tokens);
        foreach ($tokens as $token) {
            $this->assertEquals($this->user->id, $token->userid);
        }
    }

    /**
     * Test get_all tokens.
     *
     * @covers ::get_all
     */
    public function test_get_all_tokens(): void {
        api_token::create($this->user->id, 'Token 1', ['view_vacancies']);
        $result = api_token::create($this->user->id, 'Token 2', ['view_applications']);
        api_token::create($this->user->id, 'Token 3', ['create_application']);

        // Disable one.
        $result['object']->revoke();

        // Get all.
        $all = api_token::get_all(false);
        $this->assertCount(3, $all);

        // Get only enabled.
        $enabled = api_token::get_all(true);
        $this->assertCount(2, $enabled);
    }

    /**
     * Test is_valid method.
     *
     * @covers ::is_valid
     */
    public function test_is_valid(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies']
        );

        $this->assertTrue($result['object']->is_valid());

        $result['object']->revoke();
        $this->assertFalse($result['object']->is_valid());
    }

    /**
     * Test get_status method.
     *
     * @covers ::get_status
     */
    public function test_get_status(): void {
        // Active token.
        $result = api_token::create(
            $this->user->id,
            'Active token',
            ['view_vacancies']
        );
        $this->assertEquals('active', $result['object']->get_status());

        // Disabled token.
        $result['object']->revoke();
        $this->assertEquals('disabled', $result['object']->get_status());

        // Expired token.
        $expiredResult = api_token::create(
            $this->user->id,
            'Expired token',
            ['view_vacancies'],
            [],
            null,
            time() - 3600
        );
        $this->assertEquals('expired', $expiredResult['object']->get_status());

        // Not yet valid token.
        $futureResult = api_token::create(
            $this->user->id,
            'Future token',
            ['view_vacancies'],
            [],
            time() + 3600,
            null
        );
        $this->assertEquals('not_yet_valid', $futureResult['object']->get_status());
    }

    /**
     * Test get_permission_names.
     *
     * @covers ::get_permission_names
     */
    public function test_get_permission_names(): void {
        $names = api_token::get_permission_names();

        $this->assertIsArray($names);
        $this->assertCount(count(api_token::PERMISSIONS), $names);
        $this->assertArrayHasKey('view_vacancies', $names);
        $this->assertArrayHasKey('create_application', $names);
    }

    /**
     * Test get_permissions_display.
     *
     * @covers ::get_permissions_display
     */
    public function test_get_permissions_display(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies', 'view_vacancy_details']
        );

        $display = $result['object']->get_permissions_display();

        $this->assertIsString($display);
        $this->assertNotEmpty($display);
    }

    /**
     * Test get_user returns user object.
     *
     * @covers ::get_user
     */
    public function test_get_user(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies']
        );

        $user = $result['object']->get_user();

        $this->assertNotNull($user);
        $this->assertEquals($this->user->id, $user->id);
        $this->assertEquals($this->user->username, $user->username);
    }

    /**
     * Test to_record conversion.
     *
     * @covers ::to_record
     */
    public function test_to_record(): void {
        $result = api_token::create(
            $this->user->id,
            'Test token',
            ['view_vacancies', 'view_applications'],
            ['192.168.1.0/24']
        );

        $record = $result['object']->to_record();

        $this->assertIsObject($record);
        $this->assertEquals($result['object']->id, $record->id);
        $this->assertEquals($this->user->id, $record->userid);
        $this->assertEquals('Test token', $record->description);
        $this->assertEquals(1, $record->enabled);

        // Permissions should be JSON encoded.
        $permissions = json_decode($record->permissions, true);
        $this->assertIsArray($permissions);
        $this->assertContains('view_vacancies', $permissions);
    }
}
