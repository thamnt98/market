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

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.1') < 0) {
            $eavSetup->removeAttribute(Customer::ENTITY, "profile_picture");

            $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
            $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

            $eavSetup->addAttribute(Customer::ENTITY, 'profile_picture', [
                // Attribute parameters
                'type' => 'varchar',
                'label' => 'Profile Picture',
                'input' => 'image',
                'backend' => 'Trans\CustomerMyProfile\Model\Attribute\Backend\ProfilePicture',
                'required' => false,
                'unique' => false,
                'user_defined' => true,
                'visible' => true,
                'sort_order' => 10,
                'position' => 10,
                'system' => 0,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true

            ]);

            $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'profile_picture');
            $attribute->setData('attribute_set_id', $attributeSetId);
            $attribute->setData('attribute_group_id', $attributeGroupId);

            $attribute->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit'
            ]);

            $this->attributeResource->save($attribute);
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.2') < 0) {

            //Update attribute profile picture only use in form edit customer & edit customer (admin)
            $profilePicture = $this->eavConfig->getAttribute(Customer::ENTITY, 'profile_picture');
            $profilePicture->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_edit'
            ]);

            $this->attributeResource->save($profilePicture);

            //Update attribute marital status only use in form edit customer & edit customer (admin)
            $maritalStatus = $this->eavConfig->getAttribute(Customer::ENTITY, 'marital_status');
            $maritalStatus->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_edit'
            ]);

            $this->attributeResource->save($maritalStatus);

            //Update attribute nik only use in form edit customer & edit customer (admin)
            $nik = $this->eavConfig->getAttribute(Customer::ENTITY, 'nik');
            $nik->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_edit'
            ]);

            $this->attributeResource->save($nik);
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.3') < 0) {
            $eavSetup->removeAttribute(Customer::ENTITY, "dob_change_number");
            $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
            $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

            $eavSetup->addAttribute(Customer::ENTITY, 'dob_change_number', [
                // Attribute parameters
                'type' => 'varchar',
                'label' => 'Date of Birth Change Number',
                'input' => 'text',
                'backend' => '',
                'required' => false,
                'unique' => false,
                'user_defined' => true,
                'visible' => true,
                'sort_order' => 91,
                'position' => 91,
                'system' => 0,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true

            ]);
            $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'dob_change_number');
            $attribute->setData('attribute_set_id', $attributeSetId);
            $attribute->setData('attribute_group_id', $attributeGroupId);
            $attribute->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_edit'
            ]);

            $this->attributeResource->save($attribute);
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.4') < 0) {
            $eavSetup->updateAttribute(Customer::ENTITY, 'nik', ['frontend_label' => "KTP Number"]);
            $eavSetup->updateAttribute(Customer::ENTITY, 'dob', ['is_visible' => 1]);
            $eavSetup->updateAttribute(Customer::ENTITY, 'gender', ['is_visible' => 1]);
        }

        if ($context->getVersion() == ''|| version_compare($context->getVersion(), '1.0.5') < 0) {
            $eavSetup->updateAttribute(Customer::ENTITY, 'marital_status', 'backend_type', 'int');
        }
    }
}
