<?php

class SecureEncryption
{
    private $key;

    public function __construct($keyString)
    {
        if (empty($keyString)) {
            throw new Exception("Encryption key cannot be empty");
        }
        
        // ✅ LAZY LOAD: Load dependencies only when needed
        if (!class_exists('Defuse\\Crypto\\Crypto')) {
            if (!file_exists(ROOT_PATH . '/vendor/autoload.php')) {
                throw new Exception("Composer dependencies not installed");
            }
            require_once ROOT_PATH . '/vendor/autoload.php';
        }

        try {
            $this->key = $this->createKeyFromString($keyString);
        } catch (Exception $e) {
            throw new Exception("Failed to initialize encryption key: " . $e->getMessage());
        }
    }

    private function createKeyFromString($keyString)
    {
        // ✅ Coba load sebagai Defuse key format dulu
        if (strlen($keyString) === 64 && ctype_xdigit($keyString)) {
            try {
                return \Defuse\Crypto\Key::loadFromAsciiSafeString($keyString);
            } catch (Exception $e) {
                // Continue to try as regular string
            }
        }
        
        // ✅ Jika bukan Defuse key, convert string menjadi Defuse key
        return $this->convertStringToKey($keyString);
    }

    private function convertStringToKey($password)
    {
        // Use HKDF to derive a secure key from password
        $salt = hash('sha256', 'static_salt_for_key_derivation', true);
        $info = 'defuse_encryption_key_derivation';
        
        // Generate consistent key from password
        $keyMaterial = hash_hkdf('sha256', $password, 32, $info, $salt);
        
        // Convert to Defuse key format
        $hexKey = bin2hex($keyMaterial);
        return \Defuse\Crypto\Key::loadFromAsciiSafeString($hexKey);
    }

    public function encrypt($data)
    {
        if (empty($data)) {
            return false;
        }

        try {
            return \Defuse\Crypto\Crypto::encrypt((string)$data, $this->key);
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
            return \Defuse\Crypto\Crypto::decrypt($ciphertext, $this->key);
        } catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $e) {
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

    public static function generateDefuseKey()
    {
        if (!class_exists('Defuse\\Crypto\\Key')) {
            require_once ROOT_PATH . '/vendor/autoload.php';
        }
        return \Defuse\Crypto\Key::createNewRandomKey()->saveToAsciiSafeString();
    }

    public static function generateKeyFromString($password)
    {
        if (!class_exists('Defuse\\Crypto\\Key')) {
            require_once ROOT_PATH . '/vendor/autoload.php';
        }
        
        $salt = hash('sha256', 'static_salt_for_key_derivation', true);
        $info = 'defuse_encryption_key_derivation';
        
        $keyMaterial = hash_hkdf('sha256', $password, 32, $info, $salt);
        $hexKey = bin2hex($keyMaterial);
        
        return \Defuse\Crypto\Key::loadFromAsciiSafeString($hexKey)->saveToAsciiSafeString();
    }
}