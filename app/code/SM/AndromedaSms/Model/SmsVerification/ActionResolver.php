<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\SmsVerification;

use Magento\Framework\Exception\LocalizedException;
use SM\Customer\Model\Customer\Management as CustomerManagement;

class ActionResolver
{
    const RESET_PASSWORD = 'reset_password';

    /**
     * @var CustomerManagement
     */
    protected $customerManagement;

    /**
     * ActionResolver constructor.
     * @param CustomerManagement $customerManagement
     */
    public function __construct(
        CustomerManagement $customerManagement
    ) {
        $this->customerManagement = $customerManagement;
    }

    /**
     * @param string $phoneNumber
     * @param string $action
     * @return string|bool
     * @throws LocalizedException
     */
    public function resolve(string $phoneNumber, string $action)
    {
        switch ($action) {
            case self::RESET_PASSWORD:
                return $this->customerManagement->updateResetPasswordToken($phoneNumber);
            default:
                return true;
        }
    }
}
