<?php
/**
 * @category SM
 * @package  SM_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hung Pham <hungpv@smartosc.com>
 *
 * Copyright © 2020 Smartosc. All rights reserved.
 * http://www.smartosc.com
 */

namespace SM\Customer\Plugin\Magento\Customer\Api;

use Magento\Customer\Api\CustomerRepositoryInterface as BaseCustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use SM\Customer\Helper\Config;

class TelephoneResolverPlugin
{
    /**
     * @var Config
     */
    private $customerConfig;

    /**
     * TelephoneResolverPlugin constructor.
     * @param Config $customerConfig
     */
    public function __construct(
        Config $customerConfig
    ) {
        $this->customerConfig = $customerConfig;
    }

    public function beforeSave(
        BaseCustomerRepositoryInterface $subject,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        $telephone = $customer->getCustomAttribute('telephone');
        if ($telephone) {
            $telephone->setValue($this->customerConfig->trimTelephone($telephone->getValue()));
        }

        return [$customer, $passwordHash];
    }
}
