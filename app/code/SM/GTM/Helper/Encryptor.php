<?php

namespace SM\GTM\Helper;

use SM\GTM\Api\EncryptorInterface;

/**
 * Class Encryptor
 * @package SM\GTM\Helper
 */
class Encryptor implements EncryptorInterface
{

    /**
     * @inheritDoc
     */
    public function encrypt(string $rawString)
    {
        if (function_exists('hash')) {
            return hash(self::ENCRYPT_SHA256_ALGORITHM, $rawString);
        }

        return $rawString;
    }
}
