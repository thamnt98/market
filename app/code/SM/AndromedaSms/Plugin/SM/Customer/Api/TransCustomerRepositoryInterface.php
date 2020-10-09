<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Plugin\SM\Customer\Api;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\AndromedaSms\Helper\Config;
use SM\AndromedaSms\Model\SmsVerification\Validator;
use SM\Customer\Api\TransCustomerRepositoryInterface as BaseTransCustomerRepositoryInterface;

class TransCustomerRepositoryInterface
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * CreateAccount constructor.
     * @param Validator $validator
     */
    public function __construct(
        Validator $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * @param BaseTransCustomerRepositoryInterface $subject
     * @param CustomerInterface $customer
     * @param null|string $passwordHash
     * @throws LocalizedException
     */
    public function beforeExecute(
        BaseTransCustomerRepositoryInterface $subject,
        CustomerInterface $customer,
        ?string $passwordHash = null
    ): void {
        if (!$customer->getId() && $customer->getCustomAttribute(Config::TELEPHONE_ATTRIBUTE_CODE)
            && $phoneNumber = $customer->getCustomAttribute(Config::TELEPHONE_ATTRIBUTE_CODE)->getValue()) {
            $this->validator->validateVerified($phoneNumber);
        }
    }
}
