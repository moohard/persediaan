<?php

class SimpleKeyManager
{
    public static function getEncryptionKey($context = 'default')
    {
        $keyPath = APP_PATH . "/storage/keys/{$context}.key";
        
        if (file_exists($keyPath)) {
            return file_get_contents($keyPath);
        }
        
        // Generate simple key tanpa dependency Defuse
        $key = bin2hex(random_bytes(32)); // 64-character hex string
        
        // Ensure directory exists
        $keyDir = APP_PATH . '/storage/keys';
        if (!is_dir($keyDir)) {
            mkdir($keyDir, 0700, true);
        }

        file_put_contents($keyPath, $key, LOCK_EX);
        chmod($keyPath, 0600);
        
        return $key;
    }
}