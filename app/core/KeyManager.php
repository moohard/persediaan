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

        // ✅ LAZY LOAD: Require hanya ketika needed
        if (!class_exists('Defuse\\Crypto\\Key'))
        {
            if (!file_exists(ROOT_PATH . '/vendor/autoload.php'))
            {
                throw new Exception("Composer dependencies not installed. Run 'composer install' first.");
            }
            require_once ROOT_PATH . '/vendor/autoload.php';
        }

        try
        {
            $key = \Defuse\Crypto\Key::createNewRandomKey()->saveToAsciiSafeString();
        } catch (Exception $e)
        {
            throw new Exception("Failed to generate encryption key: " . $e->getMessage());
        }

        // Ensure directory exists
        $keyDir = APP_PATH . '/storage/keys';
        if (!is_dir($keyDir))
        {
            mkdir($keyDir, 0700, TRUE);
        }

        // Save key to file
        $keyPath = $keyDir . "/{$context}.key";
        if (file_put_contents($keyPath, $key, LOCK_EX) === FALSE)
        {
            throw new Exception("Failed to save key to: " . $keyPath);
        }

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

    public static function keyExists($context = 'default')
    {

        $keyPath = APP_PATH . "/storage/keys/{$context}.key";
        return file_exists($keyPath);
    }

}