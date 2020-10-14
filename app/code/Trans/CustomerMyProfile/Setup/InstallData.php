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

namespace Trans\CustomerMyProfile\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Attribute
     */
    private $attributeResource;

    /**
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);


        $eavSetup->removeAttribute(Customer::ENTITY, "marital_status");

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $eavSetup->addAttribute(Customer::ENTITY, 'marital_status', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'Marital Status',
            'input' => 'select',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
            'required' => false,
            'unique' => false,
            'user_defined' => true,
            'visible' => true,
            'sort_order' => 100,
            'position' => 100,
            'system' => 0,
            'option' => [
                'values' => [
                    0 => 'Single',
                    1 => 'Married',
                ],
            ],
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'marital_status');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);

        $this->attributeResource->save($attribute);

        $eavSetup->removeAttribute(Customer::ENTITY, "nik");

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $eavSetup->addAttribute(Customer::ENTITY, 'nik', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'NIK',
            'input' => 'text',
            'required' => false,
            'unique' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 101,
            'position' => 101,
            'system' => 0,
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'nik');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);

        $this->attributeResource->save($attribute);
    }
}
