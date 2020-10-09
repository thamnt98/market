<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\Entity;

use Magento\Framework\Model\AbstractModel;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterface;
use SM\AndromedaSms\Model\ResourceModel\SmsVerification as ResourceModel;

class SmsVerification extends AbstractModel implements SmsVerificationInterface
{
    /**
     * Construct method
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setVerificationId(string $verificationId): SmsVerificationInterface
    {
        return $this->setData(self::VERIFICATION_ID, $verificationId);
    }

    /**
     * {@inheritdoc}
     */
    public function getVerificationId(): string
    {
        return $this->getData(self::VERIFICATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPhoneNumber(string $phoneNumber): SmsVerificationInterface
    {
        return $this->setData(self::PHONE_NUMBER, $phoneNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhoneNumber(): string
    {
        return $this->getData(self::PHONE_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVerified(int $isVerified): SmsVerificationInterface
    {
        return $this->setData(self::IS_VERIFIED, $isVerified);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVerified(): int
    {
        return (int) $this->getData(self::IS_VERIFIED);
    }

    /**
     * {@inheritdoc}
     */
    public function setFailedAttempt(int $failedAttempt): SmsVerificationInterface
    {
        return $this->setData(self::FAILED_ATTEMPT, $failedAttempt);
    }

    /**
     * {@inheritdoc}
     */
    public function getFailedAttempt(): int
    {
        return (int) $this->getData(self::FAILED_ATTEMPT);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }
}
