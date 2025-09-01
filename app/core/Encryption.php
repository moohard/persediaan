<?php

class Encryption
{
    private $key;
    private $cipher = "AES-256-CBC";
    private $iv;

    public function __construct($key)
    {
        if (empty($key)) {
            throw new Exception("Encryption key cannot be empty");
        }
        
        // Generate fixed IV dari key (consistent untuk same key)
        $this->key = hash('sha256', $key, TRUE);
        $this->iv = $this->generateFixedIV($this->key);
    }

    private function generateFixedIV($key)
    {
        // Generate fixed IV dari key menggunakan HMAC
        return substr(hash_hmac('sha256', 'fixed_iv', $key, TRUE), 0, 16);
    }

    public function encrypt($data)
    {
        if (empty($data)) {
            return false;
        }
        
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $ciphertext_raw = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $this->iv);
        
        if ($ciphertext_raw === false) {
            error_log("Encryption failed for data: " . $data);
            return false;
        }
        
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, TRUE);

        // Gabungkan semuanya dan enkode dengan Base64
        $encoded = base64_encode($this->iv . $hmac . $ciphertext_raw);

        // Jadikan URL-safe
        return strtr($encoded, '+/', '-_');
    }

    public function decrypt($data)
    {
        if (empty($data)) {
            return false;
        }
        
        // Kembalikan dari format URL-safe ke Base64 standar
        $data_decoded = strtr($data, '-_', '+/');
        $c = base64_decode($data_decoded);
        
        if ($c === FALSE) {
            error_log("Base64 decode failed for: " . $data);
            return FALSE;
        }

        $ivlen = openssl_cipher_iv_length($this->cipher);
        if (mb_strlen($c, '8bit') < ($ivlen + 32)) {
            error_log("Invalid encrypted data length: " . strlen($c));
            return FALSE;
        }

        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, 32);
        $ciphertext_raw = substr($c, $ivlen + 32);

        // Verifikasi IV matches our fixed IV
        if ($iv !== $this->iv) {
            error_log("IV verification failed");
            return FALSE;
        }

        $original_plaintext = openssl_decrypt($ciphertext_raw, $this->cipher, $this->key, OPENSSL_RAW_DATA, $this->iv);
        if ($original_plaintext === FALSE) {
            error_log("Decryption failed for data: " . $data);
            return FALSE;
        }

        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, TRUE);
        if (hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }

        error_log("HMAC verification failed");
        return FALSE;
    }

    // Method untuk encrypt yang consistent (menggunakan fixed IV)
    public function encryptConsistent($data)
    {
        return $this->encrypt($data);
    }

    // Method untuk decrypt yang consistent
    public function decryptConsistent($data)
    {
        return $this->decrypt($data);
    }
}