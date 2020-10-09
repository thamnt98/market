<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api\Entity\SmsVerification\TestMode;

interface SmsVerificationInterface
{
    const VERIFICATION_ID = 'verification_id';
    const PHONE_NUMBER = 'phone_number';
    const VERIFICATION_CODE = 'verification_code';

    /**
     * @return string
     */
    public function getVerificationId(): string;

    /**
     * @param string $phoneNumber
     * @return self
     */
    public function setPhoneNumber(string $phoneNumber): self;

    /**
     * @return string
     */
    public function getPhoneNumber(): string;

    /**
     * @param string $verificationCode
     * @return self
     */
    public function setVerificationCode(string $verificationCode): self;

    /**
     * @return string
     */
    public function getVerificationCode(): string;
}
