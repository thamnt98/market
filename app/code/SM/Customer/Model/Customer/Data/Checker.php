<?php

declare(strict_types=1);

namespace SM\Customer\Model\Customer\Data;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Customer\Helper\Config;

class Checker
{
    const IS_CHANGE_EMAIL = 'isChangeEmail';
    const IS_CHANGE_TELEPHONE = 'isChangeTelephone';
    const IS_CHANGE_PERSONAL_INFO = 'isChangePersonalInformation';

    /**
     * @var CustomerRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $lastUsernameChecked;

    /**
     * Checker constructor.
     * @param CustomerRepositoryInterface $repository
     */
    public function __construct(
        CustomerRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param CustomerInterface $customerData
     * @return bool
     * @throws LocalizedException
     */
    public function isRequireEmailVerified(CustomerInterface $customerData): bool
    {
        if (!$customerData->getId()) {
            return true;
        }

        $customer = $this->repository->getById($customerData->getId());
        if ($customerData->getEmail() != $customer->getEmail()) {
            return true;
        }

        return false;
    }

    /**
     * @param int $customerId
     * @return bool
     * @throws LocalizedException
     */
    public function isEmailVerified(int $customerId): bool
    {
        $customer = $this->repository->getById($customerId);
        return (bool) $customer->getCustomAttribute(Config::IS_VERIFIED_EMAIL_ATTRIBUTE_CODE)->getValue();
    }

    /**
     * @param string $username
     * @return null|string
     */
    public function checkUsername(string $username): ?string
    {
        if (strpos($username, '@') !== false) {
            $this->lastUsernameChecked = Config::EMAIL_ATTRIBUTE_CODE;
            return Config::EMAIL_ATTRIBUTE_CODE;
        }

        $this->lastUsernameChecked = Config::PHONE_ATTRIBUTE_CODE;
        return Config::PHONE_ATTRIBUTE_CODE;
    }

    /**
     * @return bool
     */
    public function isUsingEmail(): bool
    {
        return $this->lastUsernameChecked == Config::EMAIL_ATTRIBUTE_CODE;
    }

    /**
     * @param CustomerInterface $customerData
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function createCustomerChecker($customerData)
    {
        $customerId      = $customerData->getId();
        $customerChecker = $this->initCustomerCheckerArray();

        if (!isset($customerId)) {
            return $customerChecker;
        }

        $previousCustomerData = $this->repository->getById($customerId);

        //Verify customer change email
        if ($previousCustomerData->getEmail() !== $customerData->getEmail()) {
            $customerChecker[self::IS_CHANGE_EMAIL] = true;
        }

        //Verify customer change telephone
        $previousTelephone = $previousCustomerData->getCustomAttribute(Config::PHONE_ATTRIBUTE_CODE)->getValue();
        $currentTelephone  = $customerData->getCustomAttribute(Config::PHONE_ATTRIBUTE_CODE)->getValue();
        if ($previousTelephone !== $currentTelephone) {
            $customerChecker[self::IS_CHANGE_TELEPHONE] = true;
        }

        //Verify customer change personal information
        $previousMaritalStatus = $previousCustomerData->getCustomAttribute(Config::MARITAL_STATUS) ?
            $previousCustomerData->getCustomAttribute(Config::MARITAL_STATUS)->getValue() : null;
        $currentMaritalStatus  = $customerData->getCustomAttribute(Config::MARITAL_STATUS) ?
            $customerData->getCustomAttribute(Config::MARITAL_STATUS)->getValue() : null;

        if ($previousCustomerData->getDob() != $customerData->getDob()
            || $previousCustomerData->getGender() != $customerData->getGender()
            || $previousCustomerData->getFirstname() != $customerData->getFirstname()
            || $previousCustomerData->getLastname() != $customerData->getLastname()
            || $previousMaritalStatus != $currentMaritalStatus
        ) {
            $customerChecker[self::IS_CHANGE_PERSONAL_INFO] = true;
        }

        return $customerChecker;
    }

    /**
     * @return array
     */
    protected function initCustomerCheckerArray()
    {
        $customerChecker = [];

        $customerChecker[self::IS_CHANGE_EMAIL] = false;
        $customerChecker[self::IS_CHANGE_PERSONAL_INFO] = false;
        $customerChecker[self::IS_CHANGE_TELEPHONE] = false;

        return $customerChecker;
    }
}
