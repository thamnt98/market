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

/**
 *  Class Telephone
 */
class Telephone
{
    /**
     * override template default
     *
     * @param \Magento\Customer\Block\Widget\Telephone $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeToHtml(\Magento\Customer\Block\Widget\Telephone $subject)
    {
        $subject->setTemplate('Trans_CustomerMyProfile::widget/telephone.phtml');
    }
}
