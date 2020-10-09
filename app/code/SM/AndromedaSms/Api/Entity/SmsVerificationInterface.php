<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api\Entity;

interface SmsVerificationInterface
{
    const VERIFICATION_ID = 'verification_id';
    const PHONE_NUMBER = 'phone_number';
    const IS_VERIFIED = 'is_verified';
    const FAILED_ATTEMPT = 'failed_attempt';
    const CREATED_AT = 'created_at';

    /**
     * @param string $verificationId
     * @return self
     */
    public function setVerificationId(string $verificationId): self;

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
     * @param int $isVerified
     * @return self
     */
    public function setIsVerified(int $isVerified): self;

    /**
     * @return int
     */
    public function getIsVerified(): int;

    /**
     * @param int $failedAttempt
     * @return self
     */
    public function setFailedAttempt(int $failedAttempt): self;

    /**
     * @return int
     */
    public function getFailedAttempt(): int;

    /**
     * @return string
     */
    public function getCreatedAt(): string;
}
