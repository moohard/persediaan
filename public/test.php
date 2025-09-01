<?php

// Test debug script
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

// Load environment variables FIRST
if (file_exists(ROOT_PATH . '../vendor/autoload.php'))
{
    require_once ROOT_PATH . '../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

// Define constants AFTER loading .env
define('ENVIRONMENT', $_ENV['APP_ENV'] ?? 'production');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost');
define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? 'fallback_encryption_key_here');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Now load helper functions
require_once APP_PATH . '/core/Helper.php';
require_once APP_PATH . '/core/SecureEncryption.php';

echo "<h2>Environment Test</h2>";
// Test script
$encryption = new SecureEncryption(ENCRYPTION_KEY);
$testId = 123;

// Encrypt
$encrypted = $encryption->encryptId($testId);
echo "Encrypted: $encrypted\n";

// Decrypt
$decrypted = $encryption->decryptId($encrypted);
echo "Decrypted: $decrypted\n";
echo "Success: " . ($decrypted === $testId ? 'YES' : 'NO') . "\n";

// Test tampering
$tampered = substr($encrypted, 0, -5) . 'xxxxx';
$result = $encryption->decryptId($tampered);
echo "Tampered test: " . ($result === false ? 'FAILED (good)' : 'SUCCESS (bad)') . "\n";