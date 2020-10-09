<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\Repository;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterface;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterfaceFactory;
use SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface;
use SM\AndromedaSms\Model\Entity\SmsVerification;
use SM\AndromedaSms\Model\ResourceModel\SmsVerification as ResourceModel;

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
    public function getByPhoneNumber(string $phoneNumber, bool $activeOnly=false): SmsVerificationInterface
    {
        $entity = $this->smsVerificationInterfaceFactory->create();

        /** @var SmsVerification $entity */
        $this->resourceModel->load($entity, $phoneNumber, SmsVerificationInterface::PHONE_NUMBER);
        if (!$entity->getId() || ($activeOnly && $entity->getIsVerified())) {
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
