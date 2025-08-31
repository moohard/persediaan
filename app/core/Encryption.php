<?php

class Encryption
{

    private $key;

    private $cipher = "AES-128-CBC";

    public function __construct($key)
    {

        // Pastikan kunci selalu 32 byte untuk AES-256 (yang digunakan oleh sha256)
        // Meskipun cipher kita AES-128, menggunakan hash yang lebih kuat untuk derivasi kunci adalah praktik yang baik.
        $this->key = hash('sha256', $key, TRUE);
    }

    public function encrypt($data)
    {

        $ivlen          = openssl_cipher_iv_length($this->cipher);
        $iv             = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        $hmac           = hash_hmac('sha256', $ciphertext_raw, $this->key, TRUE);

        // Gabungkan semuanya dan enkode dengan Base64
        $encoded = base64_encode($iv . $hmac . $ciphertext_raw);

        // [PERBAIKAN] Jadikan URL-safe
        return strtr($encoded, '+/', '-_');
    }

    public function decrypt($data)
    {

        // [PERBAIKAN] Kembalikan dari format URL-safe ke Base64 standar
        $data_decoded = strtr($data, '-_', '+/');

        $c = base64_decode($data_decoded);
        if ($c === FALSE)
        {
            return FALSE;
        }

        $ivlen = openssl_cipher_iv_length($this->cipher);
        if (mb_strlen($c, '8bit') < ($ivlen + 32))
        {
            return FALSE;
        }

        $iv             = substr($c, 0, $ivlen);
        $hmac           = substr($c, $ivlen, 32);
        $ciphertext_raw = substr($c, $ivlen + 32);

        $original_plaintext = openssl_decrypt($ciphertext_raw, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        if ($original_plaintext === FALSE)
        {
            return FALSE;
        }

        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, TRUE);
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }

        return FALSE;
    }

}