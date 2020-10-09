<?php

declare(strict_types=1);

namespace SM\Customer\Model\Customer\Data;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Customer\Helper\Config;

class Checker
{
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
}
