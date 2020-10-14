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

namespace Trans\Customer\Preference\Customer\Model\Data;

use Magento\Customer\Model\Data\Customer as DataCustomer;
use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Customer
 */
class Customer extends DataCustomer
{
    /**
     * @var \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadataService
     * @param \Trans\Core\Helper\Customer $customerHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $attributeValueFactory,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        \Trans\Core\Helper\Customer $customerHelper,
        $data = []
    ) {
        $this->customerHelper = $customerHelper;
        parent::__construct($extensionFactory, $attributeValueFactory, $metadataService, $data);
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        $name = [$this->getFirstname(), $this->getLastname()];
        $fullName = $this->customerHelper->generateFullnameByArray($name);
        return $fullName;
    }
}
