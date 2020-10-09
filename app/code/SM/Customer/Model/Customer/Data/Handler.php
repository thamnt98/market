<?php

declare(strict_types=1);

namespace SM\Customer\Model\Customer\Data;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Handler
{
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Resolver constructor.
     * @param CustomerRegistry $customerRegistry
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        TimezoneInterface $timezone
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->timezone = $timezone;
    }

    /**
     * @param CustomerInterface $customerData
     * @return string
     * @throws NoSuchEntityException
     */
    public function handleResetPasswordToken(CustomerInterface $customerData): string
    {
        // Ignore weak encryption algorithm report due to customer 's requirement
        $resetToken = md5($customerData->getId() . $customerData->getEmail()); //phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        $secureData = $this->customerRegistry->retrieveSecureData($customerData->getId());
        $secureData->setRpToken($resetToken);
        $secureData->setRpTokenCreatedAt(
            (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT)
        );

        return $resetToken;
    }
}
