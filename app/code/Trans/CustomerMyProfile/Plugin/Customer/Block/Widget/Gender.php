<?php

/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Plugin\Customer\Block\Widget;

use Magento\Customer\Block\Widget\Gender as MageGender;

/**
 *  Class Gender
 */
class Gender
{
    /**
     * @param \Trans\CustomerMyProfile\Helper\Data
     */
    protected $customerMyProfileHelper;

    /**
     * Gender constructor.
     * @param \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
     */
    public function __construct(
        \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
    ) {
        $this->customerMyProfileHelper = $customerMyProfileHelper;
    }

    /**
     * disable gender form in register
     *
     * @param MageGender $subject
     * @param callable $proceed
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsEnabled(MageGender $subject, callable $proceed)
    {
        //disable gender form in register
        if ($this->customerMyProfileHelper->getDisableGenderRegister() == 1) {
            return false;
        }

        return $proceed();
    }
}
