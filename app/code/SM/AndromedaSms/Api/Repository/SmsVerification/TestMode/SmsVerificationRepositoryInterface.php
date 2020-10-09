<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api\Repository\SmsVerification\TestMode;

use SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface;

interface SmsVerificationRepositoryInterface
{
    /**
     * @param \SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface $entity
     * @return \SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SmsVerificationInterface $entity): SmsVerificationInterface;

    /**
     * @param string $verificationId
     * @return \SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByVerificationId(string $verificationId): SmsVerificationInterface;

    /**
     * @param string $phoneNumber
     * @return \SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByPhoneNumber(string $phoneNumber): SmsVerificationInterface;

    /**
     * @param string $phoneNumber
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByPhoneNumber(string $phoneNumber): void;
}
