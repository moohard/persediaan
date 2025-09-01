<?php

class NativeEncryption
{

    private $key;

    private $cipher   = "aes-256-gcm";

    private $ivLength;

    public function __construct($key)
    {

        if (empty($key))
        {
            throw new Exception("Encryption key cannot be empty");
        }

        // Derive consistent key from string
        $this->key      = hash_hkdf('sha256', $key, 32, 'aes-256-gcm-encryption');
        $this->ivLength = openssl_cipher_iv_length($this->cipher);
    }

    public function encryptId($id)
    {

        if (!is_numeric($id))
        {
            throw new Exception("ID must be numeric");
        }

        $iv   = random_bytes($this->ivLength);
        $data = [
            'id'        => (int) $id,
            'timestamp' => time(),
            'nonce'     => bin2hex(random_bytes(8)),
        ];

        $jsonData = json_encode($data);
        $tag      = '';

        $ciphertext = openssl_encrypt(
            $jsonData,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
        );

        return base64_encode($iv . $tag . $ciphertext);
    }

    public function decryptId($encryptedId, $maxAge = 3600)
    {

        $data       = base64_decode($encryptedId);
        $iv         = substr($data, 0, $this->ivLength);
        $tag        = substr($data, $this->ivLength, 16);
        $ciphertext = substr($data, $this->ivLength + 16);

        $decrypted = openssl_decrypt(
            $ciphertext,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
        );

        if (!$decrypted)
        {
            return FALSE;
        }

        $data = json_decode($decrypted, TRUE);
        if (!isset($data['id'], $data['timestamp'], $data['nonce']))
        {
            return FALSE;
        }

        if (time() - $data['timestamp'] > $maxAge)
        {
            return FALSE;
        }

        return (int) $data['id'];
    }

}