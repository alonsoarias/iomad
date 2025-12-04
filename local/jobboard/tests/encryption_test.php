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
 * Unit tests for the encryption class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Test class for encryption functionality.
 *
 * @coversDefaultClass \local_jobboard\encryption
 */
class encryption_test extends \advanced_testcase {

    /**
     * Set up before each test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test that encryption is disabled by default.
     *
     * @covers ::is_enabled
     */
    public function test_encryption_disabled_by_default(): void {
        $this->assertFalse(encryption::is_enabled());
    }

    /**
     * Test enabling encryption.
     *
     * @covers ::enable
     * @covers ::is_enabled
     */
    public function test_enable_encryption(): void {
        encryption::enable();
        $this->assertTrue(encryption::is_enabled());
    }

    /**
     * Test disabling encryption.
     *
     * @covers ::disable
     * @covers ::is_enabled
     */
    public function test_disable_encryption(): void {
        encryption::enable();
        $this->assertTrue(encryption::is_enabled());

        encryption::disable();
        $this->assertFalse(encryption::is_enabled());
    }

    /**
     * Test key generation.
     *
     * @covers ::generate_key
     */
    public function test_generate_key(): void {
        $key = encryption::generate_key();

        // Key should be base64 encoded 32 bytes = 44 characters.
        $this->assertNotEmpty($key);
        $this->assertEquals(44, strlen($key));

        // Key should be valid base64.
        $decoded = base64_decode($key, true);
        $this->assertNotFalse($decoded);
        $this->assertEquals(32, strlen($decoded));
    }

    /**
     * Test importing a valid key.
     *
     * @covers ::import_key
     */
    public function test_import_valid_key(): void {
        $key = encryption::generate_key();
        $result = encryption::import_key($key);
        $this->assertTrue($result);
    }

    /**
     * Test importing an invalid key.
     *
     * @covers ::import_key
     */
    public function test_import_invalid_key(): void {
        // Too short.
        $result = encryption::import_key('short');
        $this->assertFalse($result);

        // Invalid base64.
        $result = encryption::import_key('!!!invalid base64!!!');
        $this->assertFalse($result);
    }

    /**
     * Test encryption and decryption round trip.
     *
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function test_encrypt_decrypt_roundtrip(): void {
        // Generate and import key.
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        $plaintext = 'This is a secret message that needs to be encrypted!';

        $encrypted = encryption::encrypt($plaintext);

        // Encrypted should not equal plaintext.
        $this->assertNotEquals($plaintext, $encrypted);

        // Encrypted should be base64 encoded.
        $this->assertNotFalse(base64_decode($encrypted, true));

        $decrypted = encryption::decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Test encryption with empty string.
     *
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function test_encrypt_empty_string(): void {
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        $plaintext = '';

        $encrypted = encryption::encrypt($plaintext);
        $decrypted = encryption::decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Test encryption with binary data.
     *
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function test_encrypt_binary_data(): void {
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        // Generate random binary data.
        $plaintext = random_bytes(1024);

        $encrypted = encryption::encrypt($plaintext);
        $decrypted = encryption::decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Test encryption with large data.
     *
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function test_encrypt_large_data(): void {
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        // Generate 1MB of random data.
        $plaintext = random_bytes(1024 * 1024);

        $encrypted = encryption::encrypt($plaintext);
        $decrypted = encryption::decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Test that different plaintexts produce different ciphertexts.
     *
     * @covers ::encrypt
     */
    public function test_different_plaintexts_different_ciphertexts(): void {
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        $plaintext1 = 'Message 1';
        $plaintext2 = 'Message 2';

        $encrypted1 = encryption::encrypt($plaintext1);
        $encrypted2 = encryption::encrypt($plaintext2);

        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    /**
     * Test that same plaintext produces different ciphertexts (due to random IV).
     *
     * @covers ::encrypt
     */
    public function test_same_plaintext_different_ciphertexts(): void {
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        $plaintext = 'Same message';

        $encrypted1 = encryption::encrypt($plaintext);
        $encrypted2 = encryption::encrypt($plaintext);

        // Due to random IV, same plaintext should produce different ciphertexts.
        $this->assertNotEquals($encrypted1, $encrypted2);

        // But both should decrypt to the same plaintext.
        $this->assertEquals($plaintext, encryption::decrypt($encrypted1));
        $this->assertEquals($plaintext, encryption::decrypt($encrypted2));
    }

    /**
     * Test decryption with wrong key fails.
     *
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function test_decrypt_with_wrong_key_fails(): void {
        $key1 = encryption::generate_key();
        encryption::import_key($key1);
        encryption::enable();

        $plaintext = 'Secret message';
        $encrypted = encryption::encrypt($plaintext);

        // Change to a different key.
        $key2 = encryption::generate_key();
        encryption::import_key($key2);

        // Decryption should fail or return wrong data.
        $decrypted = encryption::decrypt($encrypted);

        // With AES-GCM, decryption with wrong key should fail (return false or throw).
        $this->assertNotEquals($plaintext, $decrypted);
    }

    /**
     * Test decryption of tampered data fails.
     *
     * @covers ::decrypt
     */
    public function test_decrypt_tampered_data_fails(): void {
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        $plaintext = 'Secret message';
        $encrypted = encryption::encrypt($plaintext);

        // Tamper with the encrypted data.
        $decoded = base64_decode($encrypted);
        $tampered = $decoded;
        $tampered[10] = chr(ord($tampered[10]) ^ 0xFF);
        $tamperedEncrypted = base64_encode($tampered);

        // Decryption should fail with tampered data.
        $decrypted = encryption::decrypt($tamperedEncrypted);

        $this->assertNotEquals($plaintext, $decrypted);
    }

    /**
     * Test encrypting when disabled returns null.
     *
     * @covers ::encrypt
     */
    public function test_encrypt_when_disabled(): void {
        encryption::disable();

        $result = encryption::encrypt('test');

        $this->assertNull($result);
    }

    /**
     * Test decrypting when disabled returns null.
     *
     * @covers ::decrypt
     */
    public function test_decrypt_when_disabled(): void {
        encryption::disable();

        $result = encryption::decrypt('test');

        $this->assertNull($result);
    }

    /**
     * Test encrypting without key configured.
     *
     * @covers ::encrypt
     */
    public function test_encrypt_without_key(): void {
        // Enable but don't import key.
        set_config('encryption_enabled', true, 'local_jobboard');

        $result = encryption::encrypt('test');

        $this->assertNull($result);
    }

    /**
     * Test encryption with unicode content.
     *
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function test_encrypt_unicode(): void {
        $key = encryption::generate_key();
        encryption::import_key($key);
        encryption::enable();

        $plaintext = 'Unicode: áéíóú ñ 日本語 🎉 emoji ☀️';

        $encrypted = encryption::encrypt($plaintext);
        $decrypted = encryption::decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }
}
