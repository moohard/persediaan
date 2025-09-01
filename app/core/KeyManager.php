<?php

class KeyManager
{

    private static $keyCache = [];

    public static function getEncryptionKey($context = 'default')
    {

        if (isset(self::$keyCache[$context]))
        {
            return self::$keyCache[$context];
        }

        // Try to get context-specific key
        $key = self::getContextKey($context);

        // Fallback to global key
        if (!$key && $context !== 'default')
        {
            $key = self::getContextKey('default');
        }

        self::$keyCache[$context] = $key;

        return $key;
    }

    private static function getContextKey($context)
    {

        $keyPath = APP_PATH . "/storage/keys/{$context}.key";

        // Load from file if exists
        if (file_exists($keyPath))
        {
            return file_get_contents($keyPath);
        }

        // Generate new key if not exists
        return self::generateKey($context);
    }

    public static function generateKey($context = 'default')
    {

        require_once 'vendor/autoload.php';
        $key = SecureEncryption::generateKey();

        // Ensure directory exists
        $keyDir = APP_PATH . '/storage/keys';
        if (!is_dir($keyDir))
        {
            mkdir($keyDir, 0700, TRUE);
        }

        // Save key to file
        $keyPath = $keyDir . "/{$context}.key";
        file_put_contents($keyPath, $key, LOCK_EX);
        chmod($keyPath, 0600);

        return $key;
    }

    public static function rotateKey($context = 'default')
    {

        $oldKey = self::getContextKey($context);
        $newKey = self::generateKey($context . '_' . time());

        // TODO: Implement key rotation logic
        // Migrate data encrypted with old key to new key

        return $newKey;
    }

}