<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\SmsVerification;

use Magento\Framework\Exception\LocalizedException;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterface;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterfaceFactory;
use SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface;

class Management
{
    /**
     * @var SmsVerificationInterfaceFactory
     */
    protected $entityFactory;

    /**
     * @var SmsVerificationRepositoryInterface
     */
    protected $repository;

    /**
     * Management constructor.
     * @param SmsVerificationInterfaceFactory $entityFactory
     * @param SmsVerificationRepositoryInterface $repository
     */
    public function __construct(
        SmsVerificationInterfaceFactory $entityFactory,
        SmsVerificationRepositoryInterface $repository
    ) {
        $this->entityFactory = $entityFactory;
        $this->repository = $repository;
    }

    /**
     * @param string $phoneNumber
     * @param string $verificationId
     * @param int $failedAttempt
     * @throws LocalizedException
     */
    public function create(string $phoneNumber, string $verificationId): void
    {
        /** @var SmsVerificationInterface $entity */
        $entity = $this->entityFactory->create();
        $entity->setPhoneNumber($phoneNumber);
        $entity->setVerificationId($verificationId);

        $this->repository->save($entity);
    }

    /**
     * @param SmsVerificationInterface $entity
     * @throws LocalizedException
     */
    public function updateVerified(SmsVerificationInterface $entity): void
    {
        $entity->setIsVerified(1);

        $this->repository->save($entity);
    }
}
