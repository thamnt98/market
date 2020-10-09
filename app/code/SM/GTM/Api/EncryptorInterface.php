<?php

namespace SM\GTM\Api;

/**
 * Interface EncryptorInterface
 * @package SM\GTM\Api
 */
interface EncryptorInterface
{
    const ENCRYPT_SHA256_ALGORITHM = 'SHA256';

    /**
     * @param string $rawString
     * @return string
     */
    public function encrypt(string $rawString);
}
