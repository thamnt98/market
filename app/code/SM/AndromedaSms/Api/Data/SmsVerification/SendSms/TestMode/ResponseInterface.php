<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api\Data\SmsVerification\SendSms\TestMode;

interface ResponseInterface
{
    const VERIFICATION_ID = 'verification_id';

    /**
     * @param string $verificationId
     * @return self
     */
    public function setVerificationId(string $verificationId): self;

    /**
     * @return string
     */
    public function getVerificationId(): string;
}
