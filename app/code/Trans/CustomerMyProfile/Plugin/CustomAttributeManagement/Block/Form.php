<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\CustomerMyProfile\Plugin\CustomAttributeManagement\Block;

/**
 * Class Form
 * @package Trans\CustomerMyProfile\Plugin\CustomAttributeManagement\Block
 */
class Form
{
    /**
     * @param \Trans\CustomerMyProfile\Helper\Data
     */
    protected $customerMyProfileHelper;

    /**
     * Dob constructor.
     * @param \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
     */
    public function __construct(
        \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
    ) {
        $this->customerMyProfileHelper = $customerMyProfileHelper;
    }

    /**
     * @param \Magento\CustomAttributeManagement\Block\Form $subject
     * @param \Closure $proceed
     * @return bool|mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetUserDefinedAttributes(
        \Magento\CustomAttributeManagement\Block\Form $subject,
        \Closure $proceed
    ) {
        $attributes = [];
        $ignore = $this->customerMyProfileHelper->ignoreAttribute();
        foreach ($subject->getForm()->getUserAttributes() as $attribute) {
            if ($attribute->getIsVisible() && !in_array($attribute->getAttributeCode(), $ignore)) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $attributes;
    }
}
