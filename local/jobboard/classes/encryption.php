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
 * Encryption helper class for local_jobboard.
 *
 * Provides AES-256-GCM encryption for files at rest.
 * This is an OPTIONAL feature that can be enabled via plugin settings.
 *
 * WARNING: Enabling encryption impacts performance (~20% slower) and
 * increases storage requirements (~33% more). Backup of the encryption
 * key is CRITICAL - without it, encrypted files are unrecoverable.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for handling file encryption and decryption.
 */
class encryption {

    /** Cipher method - AES-256-GCM for authenticated encryption. */
    private const CIPHER = 'aes-256-gcm';

    /** Tag length for GCM mode. */
    private const TAG_LENGTH = 16;

    /** IV length for AES. */
    private const IV_LENGTH = 12;

    /** Config key for the encryption key. */
    private const KEY_CONFIG = 'encryption_key';

    /** @var string|null Cached encryption key. */
    private static ?string $cachedkey = null;

    /**
     * Check if encryption is enabled.
     *
     * @return bool True if encryption is enabled.
     */
    public static function is_enabled(): bool {
        return (bool) get_config('local_jobboard', 'encryption_enabled');
    }

    /**
     * Enable encryption and generate key if needed.
     *
     * @return bool True if enabled successfully.
     * @throws \moodle_exception If key generation fails.
     */
    public static function enable(): bool {
        // Check if OpenSSL extension is available.
        if (!extension_loaded('openssl')) {
            throw new \moodle_exception('error:opensslrequired', 'local_jobboard');
        }

        // Generate key if not exists.
        if (!self::has_key()) {
            self::generate_key();
        }

        set_config('encryption_enabled', 1, 'local_jobboard');
        return true;
    }

    /**
     * Disable encryption.
     *
     * Note: This does NOT decrypt existing files. They will remain
     * encrypted and require the key to access.
     */
    public static function disable(): void {
        set_config('encryption_enabled', 0, 'local_jobboard');
    }

    /**
     * Check if encryption key exists.
     *
     * @return bool True if key exists.
     */
    public static function has_key(): bool {
        $key = get_config('local_jobboard', self::KEY_CONFIG);
        return !empty($key);
    }

    /**
     * Generate a new encryption key.
     *
     * WARNING: This will invalidate any existing encrypted data!
     *
     * @return string The generated key (base64 encoded) for backup.
     * @throws \moodle_exception If key generation fails.
     */
    public static function generate_key(): string {
        // Generate 256-bit key.
        $key = openssl_random_pseudo_bytes(32, $strong);

        if (!$strong) {
            throw new \moodle_exception('error:weakrandom', 'local_jobboard');
        }

        // Store key (base64 encoded for storage).
        $encodedkey = base64_encode($key);
        set_config(self::KEY_CONFIG, $encodedkey, 'local_jobboard');

        // Clear cache.
        self::$cachedkey = null;

        // Log key generation (NOT the key itself!).
        audit::log('encryption_key_generated', 'system', 0);

        return $encodedkey;
    }

    /**
     * Get the encryption key.
     *
     * @return string The raw encryption key.
     * @throws \moodle_exception If no key exists.
     */
    private static function get_key(): string {
        if (self::$cachedkey !== null) {
            return self::$cachedkey;
        }

        $encodedkey = get_config('local_jobboard', self::KEY_CONFIG);
        if (empty($encodedkey)) {
            throw new \moodle_exception('error:noencryptionkey', 'local_jobboard');
        }

        self::$cachedkey = base64_decode($encodedkey);
        return self::$cachedkey;
    }

    /**
     * Import an encryption key.
     *
     * Used for disaster recovery when restoring from backup.
     *
     * @param string $encodedkey Base64 encoded key.
     * @return bool True if import successful.
     * @throws \moodle_exception If key is invalid.
     */
    public static function import_key(string $encodedkey): bool {
        $key = base64_decode($encodedkey, true);

        if ($key === false || strlen($key) !== 32) {
            throw new \moodle_exception('error:invalidencryptionkey', 'local_jobboard');
        }

        set_config(self::KEY_CONFIG, $encodedkey, 'local_jobboard');
        self::$cachedkey = null;

        audit::log('encryption_key_imported', 'system', 0);

        return true;
    }

    /**
     * Encrypt data.
     *
     * @param string $plaintext The data to encrypt.
     * @return string The encrypted data (IV + tag + ciphertext, base64 encoded).
     * @throws \moodle_exception If encryption fails.
     */
    public static function encrypt(string $plaintext): string {
        if (!self::is_enabled()) {
            return $plaintext;
        }

        $key = self::get_key();

        // Generate random IV.
        $iv = openssl_random_pseudo_bytes(self::IV_LENGTH);

        // Encrypt with authentication tag.
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new \moodle_exception('error:encryptionfailed', 'local_jobboard');
        }

        // Combine IV + tag + ciphertext and encode.
        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * Decrypt data.
     *
     * @param string $encrypted The encrypted data (base64 encoded).
     * @return string The decrypted plaintext.
     * @throws \moodle_exception If decryption fails.
     */
    public static function decrypt(string $encrypted): string {
        // Check if this looks like base64 encoded data.
        $decoded = base64_decode($encrypted, true);
        if ($decoded === false) {
            // Not encrypted, return as-is.
            return $encrypted;
        }

        // Check minimum length (IV + tag + at least 1 byte).
        if (strlen($decoded) < self::IV_LENGTH + self::TAG_LENGTH + 1) {
            // Not encrypted, return as-is.
            return $encrypted;
        }

        $key = self::get_key();

        // Extract components.
        $iv = substr($decoded, 0, self::IV_LENGTH);
        $tag = substr($decoded, self::IV_LENGTH, self::TAG_LENGTH);
        $ciphertext = substr($decoded, self::IV_LENGTH + self::TAG_LENGTH);

        // Decrypt.
        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new \moodle_exception('error:decryptionfailed', 'local_jobboard');
        }

        return $plaintext;
    }

    /**
     * Encrypt a file and return encrypted content.
     *
     * @param \stored_file $file The file to encrypt.
     * @return string The encrypted content.
     */
    public static function encrypt_file(\stored_file $file): string {
        $content = $file->get_content();
        return self::encrypt($content);
    }

    /**
     * Decrypt file content.
     *
     * @param string $encrypted The encrypted content.
     * @return string The decrypted content.
     */
    public static function decrypt_file(string $encrypted): string {
        return self::decrypt($encrypted);
    }

    /**
     * Check if a file appears to be encrypted.
     *
     * This is a heuristic check - it checks if the content looks like
     * our encrypted format (base64 with proper length).
     *
     * @param string $content File content.
     * @return bool True if appears encrypted.
     */
    public static function is_encrypted(string $content): bool {
        // Try to decode as base64.
        $decoded = base64_decode($content, true);
        if ($decoded === false) {
            return false;
        }

        // Check minimum length for encrypted content.
        return strlen($decoded) >= self::IV_LENGTH + self::TAG_LENGTH + 1;
    }

    /**
     * Get key backup instructions.
     *
     * @return string Instructions for backing up the key.
     */
    public static function get_backup_instructions(): string {
        $key = get_config('local_jobboard', self::KEY_CONFIG);
        if (empty($key)) {
            return get_string('encryption:nokeytobackup', 'local_jobboard');
        }

        return get_string('encryption:backupinstructions', 'local_jobboard', $key);
    }

    /**
     * Verify the encryption system is working correctly.
     *
     * @return bool True if verification passes.
     */
    public static function verify(): bool {
        if (!self::has_key()) {
            return false;
        }

        try {
            $testdata = 'Jobboard encryption test: ' . time();
            $encrypted = self::encrypt($testdata);
            $decrypted = self::decrypt($encrypted);

            return $testdata === $decrypted;
        } catch (\Exception $e) {
            return false;
        }
    }
}
