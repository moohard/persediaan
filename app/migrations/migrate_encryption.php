<?php

require_once 'app/core/SecureEncryption.php';

class EncryptionMigrator
{
    private $oldEncryption;
    private $newEncryption;

    public function __construct()
    {
        // Old encryption (for backward compatibility)
        $this->oldEncryption = new Encryption(ENCRYPTION_KEY);
        
        // New encryption
        $this->newEncryption = new SecureEncryption(ENCRYPTION_KEY);
    }

    public function migrateId($oldEncryptedId)
    {
        try {
            // Decrypt with old method
            $decrypted = $this->oldEncryption->decryptConsistent($oldEncryptedId);
            
            if (!$decrypted || !is_numeric($decrypted)) {
                return false;
            }

            // Encrypt with new method
            return $this->newEncryption->encryptId($decrypted);
            
        } catch (Exception $e) {
            error_log("Migration failed for $oldEncryptedId: " . $e->getMessage());
            return false;
        }
    }

    public function isNewFormat($encryptedId)
    {
        // Check if already in new format
        try {
            $decoded = base64_decode(strtr($encryptedId, '-_', '+/'));
            return $decoded !== false && strlen($decoded) > 64;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Usage example
$migrator = new EncryptionMigrator();
$oldId = '9Kn9OLZMcDbVhOmn1RnMczqLUK5VnnwPPLdHBd0Ap_C7G3CHfct7g8Kg9IzD7d5zHbUqVqUp4MPyIsj9zv274Q';
$newId = $migrator->migrateId($oldId);

echo "Old ID: $oldId\n";
echo "New ID: $newId\n";
echo "Valid: " . ($migrator->isNewFormat($newId) ? 'YES' : 'NO') . "\n";