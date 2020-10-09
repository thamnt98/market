<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\Repository\SmsVerification\TestMode;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface;
use SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterfaceFactory;
use SM\AndromedaSms\Api\Repository\SmsVerification\TestMode\SmsVerificationRepositoryInterface;
use SM\AndromedaSms\Model\Entity\SmsVerification\TestMode\SmsVerification;
use SM\AndromedaSms\Model\ResourceModel\SmsVerification\TestMode\SmsVerification as ResourceModel;

class SmsVerificationRepository implements SmsVerificationRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var SmsVerificationInterfaceFactory
     */
    protected $smsVerificationInterfaceFactory;

    /**
     * RedirectRepository constructor
     *
     * @param ResourceModel $resourceModel
     * @param SmsVerificationInterfaceFactory $smsVerificationInterfaceFactory
     */
    public function __construct(
        ResourceModel $resourceModel,
        SmsVerificationInterfaceFactory $smsVerificationInterfaceFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->smsVerificationInterfaceFactory = $smsVerificationInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(SmsVerificationInterface $entity): SmsVerificationInterface
    {
        try {
            /** @var SmsVerification $entity */
            $this->resourceModel->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save sms verification: %1', $exception->getMessage()));
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getByVerificationId(string $verificationId): SmsVerificationInterface
    {
        $entity = $this->smsVerificationInterfaceFactory->create();

        /** @var SmsVerification $entity */
        $this->resourceModel->load($entity, $verificationId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('Could not found sms verification for verification ID %1', $verificationId));
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getByPhoneNumber(string $phoneNumber): SmsVerificationInterface
    {
        $entity = $this->smsVerificationInterfaceFactory->create();

        /** @var SmsVerification $entity */
        $this->resourceModel->load($entity, $phoneNumber, SmsVerificationInterface::PHONE_NUMBER);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('Could not found sms verification for phone number %1', $phoneNumber));
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function deleteByPhoneNumber(string $phoneNumber): void
    {
        $this->resourceModel->deleteByPhoneNumber($phoneNumber);
    }
}
