<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\Data\SmsVerification\VerifySms\TestMode;

use Magento\Framework\DataObject;
use SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterface;

class Response extends DataObject implements ResponseInterface
{
    /**
     * @inheritDoc
     */
    public function setIsVerified(bool $isVerified): ResponseInterface
    {
        return $this->setData(self::IS_VERIFIED, $isVerified);
    }
    /**
     * @inheritDoc
     */
    public function getIsVerified(): bool
    {
        return $this->getData(self::IS_VERIFIED);
    }
}
