<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api\Repository;

use SM\AndromedaSms\Api\Entity\SmsVerificationInterface;

interface SmsVerificationRepositoryInterface
{
    /**
     * @param \SM\AndromedaSms\Api\Entity\SmsVerificationInterface $entity
     * @return \SM\AndromedaSms\Api\Entity\SmsVerificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SmsVerificationInterface $entity): SmsVerificationInterface;

    /**
     * @param string $phoneNumber
     * @param bool $activeOnly
     * @return \SM\AndromedaSms\Api\Entity\SmsVerificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByPhoneNumber(string $phoneNumber, bool $activeOnly=false): SmsVerificationInterface;

    /**
     * @param string $phoneNumber
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByPhoneNumber(string $phoneNumber): void;
}
