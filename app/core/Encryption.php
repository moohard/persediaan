<?php

class Encryption
{

    private const CIPHER = 'aes-128-cbc';

    private $key;

    public function __construct()
    {

        $this->key = ENCRYPTION_KEY;
    }

    public function encrypt($data)
    {

        $iv_length  = openssl_cipher_iv_length(self::CIPHER);
        $iv         = openssl_random_pseudo_bytes($iv_length);
        $ciphertext = openssl_encrypt($data, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);
        $encrypted  = base64_encode($iv . $ciphertext);

        return strtr($encrypted, '+/', '-_');
    }

    public function decrypt($encrypted_data)
    {

        $data         = strtr($encrypted_data, '-_', '+/');
        $decoded_data = base64_decode($data, TRUE);
        if ($decoded_data === FALSE) return FALSE;
        $iv_length  = openssl_cipher_iv_length(self::CIPHER);
        $iv         = substr($decoded_data, 0, $iv_length);
        $ciphertext = substr($decoded_data, $iv_length);
        if (strlen($iv) < $iv_length) return FALSE;
        return openssl_decrypt($ciphertext, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);
    }

}