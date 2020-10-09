<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\Data\SmsVerification\SendSms\TestMode;

use Magento\Framework\DataObject;
use SM\AndromedaSms\Api\Data\SmsVerification\SendSms\TestMode\ResponseInterface;

class Response extends DataObject implements ResponseInterface
{
    /**
     * @inheritDoc
     */
    public function setVerificationId(string $verificationId): ResponseInterface
    {
        return $this->setData(self::VERIFICATION_ID, $verificationId);
    }
    /**
     * @inheritDoc
     */
    public function getVerificationId(): string
    {
        return $this->getData(self::VERIFICATION_ID);
    }
}
