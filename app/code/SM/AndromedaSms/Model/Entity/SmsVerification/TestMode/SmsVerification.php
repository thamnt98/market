<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\Entity\SmsVerification\TestMode;

use Magento\Framework\Model\AbstractModel;
use SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface;
use SM\AndromedaSms\Model\ResourceModel\SmsVerification\TestMode\SmsVerification as ResourceModel;

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
    public function getVerificationId(): string
    {
        return (string) $this->getId();
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
    public function setVerificationCode(string $verificationCode): SmsVerificationInterface
    {
        return $this->setData(self::VERIFICATION_CODE, $verificationCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getVerificationCode(): string
    {
        return $this->getData(self::VERIFICATION_CODE);
    }
}
