<?php

class ContextAwareEncryption
{

    private $encryption;

    private $context;

    public function __construct($context = 'default')
    {

        $key              = KeyManager::getEncryptionKey($context);
        $this->encryption = new SecureEncryption($key);
        $this->context    = $context;
    }

    public function encryptId($id, $additionalData = NULL)
    {

        $payload = [
            'id'        => (int) $id,
            'timestamp' => time(),
            'nonce'     => bin2hex(random_bytes(8)),
            'context'   => $this->context,
        ];

        if ($additionalData)
        {
            $payload['data'] = $additionalData;
        }

        // Add HMAC for additional verification
        $payload['hmac'] = $this->generateHmac($payload);

        return $this->encryption->encrypt(json_encode($payload));
    }

    public function decryptId($encryptedId, $maxAge = 3600)
    {

        $decrypted = $this->encryption->decrypt($encryptedId);
        if (!$decrypted)
        {
            return FALSE;
        }

        $payload = json_decode($decrypted, TRUE);
        if (!$this->validatePayload($payload, $maxAge))
        {
            return FALSE;
        }

        return (int) $payload['id'];
    }

    private function generateHmac($payload)
    {

        $dataToSign = $payload['id'] . $payload['timestamp'] . $payload['nonce'] . $this->context;
        return hash_hmac('sha256', $dataToSign, ENCRYPTION_KEY);
    }

    private function validatePayload($payload, $maxAge)
    {

        if (!isset($payload['id'], $payload['timestamp'], $payload['nonce'], $payload['hmac'], $payload['context']))
        {
            return FALSE;
        }

        // Check context
        if ($payload['context'] !== $this->context)
        {
            return FALSE;
        }

        // Check expiration
        if (time() - $payload['timestamp'] > $maxAge)
        {
            return FALSE;
        }

        // Verify HMAC
        $expectedHmac = $this->generateHmac($payload);
        return hash_equals($payload['hmac'], $expectedHmac);
    }

}