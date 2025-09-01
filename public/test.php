<?php
// Simple test tanpa framework complexity
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

// Load .env manually
if (file_exists(ROOT_PATH . '/.env')) {
    $env = parse_ini_file(ROOT_PATH . '/.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Load encryption
require_once APP_PATH . '/core/SecureEncryption.php';

echo "<h2>Testing Encryption</h2>";

try {
    // Test 1: Generate proper Defuse key
    echo "<h3>1. Generating Defuse Key</h3>";
    $defuseKey = SecureEncryption::generateDefuseKey();
    echo "Generated Defuse Key: $defuseKey<br>";
    echo "Length: " . strlen($defuseKey) . " characters<br><br>";
    
    // Test 2: Generate from string
    echo "<h3>2. Generating from String</h3>";
    $stringKey = "SecretKeyForATK1"; // Your current key
    $convertedKey = SecureEncryption::generateKeyFromString($stringKey);
    echo "Original String: $stringKey<br>";
    echo "Converted Defuse Key: $convertedKey<br>";
    echo "Length: " . strlen($convertedKey) . " characters<br><br>";
    
    // Test 3: Test encryption with converted key
    echo "<h3>3. Testing Encryption</h3>";
    $encryption = new SecureEncryption($convertedKey);
    
    $testId = 123;
    $encrypted = $encryption->encryptId($testId);
    $decrypted = $encryption->decryptId($encrypted);
    
    echo "Test ID: $testId<br>";
    echo "Encrypted: $encrypted<br>";
    echo "Decrypted: $decrypted<br>";
    echo "Success: " . ($decrypted === $testId ? 'YES' : 'NO') . "<br><br>";
    
    // Test 4: Test with your encrypted ID
    echo "<h3>4. Testing Your Encrypted ID</h3>";
    $yourEncryptedId = '9Kn9OLZMcDbVhOmn1RnMczqLUK5VnnwPPLdHBd0Ap_C7G3CHfct7g8Kg9IzD7d5zHbUqVqUp4MPyIsj9zv274Q';
    $result = $encryption->decryptId($yourEncryptedId);
    
    echo "Your Encrypted ID: $yourEncryptedId<br>";
    echo "Decryption Result: " . ($result !== false ? $result : 'FAILED') . "<br>";
    echo "Valid: " . ($result !== false ? 'YES' : 'NO') . "<br>";
    
    // Test 5: Test with direct string key
    echo "<h3>5. Testing with String Key (Auto-convert)</h3>";
    $encryption2 = new SecureEncryption($stringKey);
    $result2 = $encryption2->decryptId($yourEncryptedId);
    echo "Result with string key: " . ($result2 !== false ? $result2 : 'FAILED') . "<br>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    echo "<pre>Trace: " . $e->getTraceAsString() . "</pre>";
}

// Update .env with new key recommendation
echo "<h3>6. Next Steps</h3>";
echo "Copy this key to your .env file:<br>";
echo "<code>ENCRYPTION_KEY=\"$convertedKey\"</code><br>";
echo "Then restart your application.";
