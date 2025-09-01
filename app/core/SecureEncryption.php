<?php

require_once 'vendor/autoload.php';

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

class SecureEncryption
{
    private $key;

    public function __construct($keyString)
    {
        if (empty($keyString)) {
            throw new Exception("Encryption key cannot be empty");
        }
        
        // Convert string key to Defuse Key object
        try {
            // Jika key sudah dalam format Defuse Key
            if (strlen($keyString) === 64 && ctype_xdigit($keyString)) {
                $this->key = Key::loadFromAsciiSafeString($keyString);
            } else {
                // Generate key dari string menggunakan HKDF
                $this->key = $this->deriveKeyFromString($keyString);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to initialize encryption key: " . $e->getMessage());
        }
    }

    private function deriveKeyFromString($password)
    {
        // Use HKDF to derive a secure key from password
        $salt = hash('sha256', 'static_salt_' . ENCRYPTION_KEY, true);
        $info = 'defuse_encryption_key';
        
        $keyMaterial = hash_hkdf('sha256', $password, 32, $info, $salt);
        return Key::loadFromAsciiSafeString(bin2hex($keyMaterial));
    }

    public function encrypt($data)
    {
        if (empty($data)) {
            return false;
        }

        try {
            return Crypto::encrypt((string)$data, $this->key);
        } catch (Exception $e) {
            error_log("Encryption failed: " . $e->getMessage());
            return false;
        }
    }

    public function decrypt($ciphertext)
    {
        if (empty($ciphertext)) {
            return false;
        }

        try {
            return Crypto::decrypt($ciphertext, $this->key);
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            error_log("Decryption failed: Invalid key or tampered data");
            return false;
        } catch (Exception $e) {
            error_log("Decryption failed: " . $e->getMessage());
            return false;
        }
    }

    public function encryptId($id)
    {
        if (!is_numeric($id)) {
            throw new Exception("ID must be numeric");
        }

        // Add timestamp to prevent replay attacks
        $data = [
            'id' => (int)$id,
            'timestamp' => time(),
            'nonce' => bin2hex(random_bytes(8))
        ];

        $jsonData = json_encode($data);
        return $this->encrypt($jsonData);
    }

    public function decryptId($encryptedId, $maxAge = 3600)
    {
        $decrypted = $this->decrypt($encryptedId);
        if (!$decrypted) {
            return false;
        }

        $data = json_decode($decrypted, true);
        if (!isset($data['id'], $data['timestamp'], $data['nonce'])) {
            return false;
        }

        // Check if token is expired
        if (time() - $data['timestamp'] > $maxAge) {
            error_log("Encrypted ID expired: " . (time() - $data['timestamp']) . " seconds old");
            return false;
        }

        return (int)$data['id'];
    }

    public static function generateKey()
    {
        return Key::createNewRandomKey()->saveToAsciiSafeString();
    }
}