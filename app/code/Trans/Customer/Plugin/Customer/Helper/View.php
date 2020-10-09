<?php

/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Plugin\Customer\Helper;

use Magento\Customer\Helper\View as CustomerView;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 *  Class View
 */
class View
{
    /**
     * @param \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * @param \Trans\Core\Helper\Customer $customerHelper
     */
    public function __construct(
        \Trans\Core\Helper\Customer $customerHelper
    ) {
        $this->customerHelper = $customerHelper;
    }

    /**
     * {inherit}
     */
    public function aroundGetCustomerName(CustomerView $subject, callable $proceed, CustomerInterface $customerData)
    {
        $name = $proceed($customerData);

        $expl = explode(' ', $name);

        $name = $this->customerHelper->generateFullnameByArray($expl);

        return $name;
    }
}
