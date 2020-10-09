<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode;

interface ResponseInterface
{
    const IS_VERIFIED = 'is_verified';

    /**
     * @param bool $isVerified
     * @return self
     */
    public function setIsVerified(bool $isVerified): self;

    /**
     * @return bool
     */
    public function getIsVerified(): bool;
}
