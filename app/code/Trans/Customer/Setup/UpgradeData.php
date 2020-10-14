<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT CT CORP Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * @package Trans\Customer\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.4', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                'customer_address',
                'pinpoint_location',
                [
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'Pinpoint Location',
                    'visible' => true,
                    'required' => true,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => false,
                ]
            );
            $customAttribute = $this->eavConfig->getAttribute('customer_address', 'pinpoint_location');

            $customAttribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address']
            );

            $customAttribute->save();
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.5', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                'customer_address',
                'latitude',
                [
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'Latitude',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => false,
                ]
            );
            $customAttribute = $this->eavConfig->getAttribute('customer_address', 'latitude');

            $customAttribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address']
            );

            $customAttribute->save();
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.6', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                'customer_address',
                'longitude',
                [
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'Longitude',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => false,
                ]
            );
            $customAttribute = $this->eavConfig->getAttribute('customer_address', 'longitude');

            $customAttribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address']
            );

            $customAttribute->save();
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.7', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                'customer_address',
                'recipient_email',
                [
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'Recipient Email',
                    'nullable' => true,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => false,
                ]
            );
            $customAttribute = $this->eavConfig->getAttribute('customer_address', 'recipient_email');

            $customAttribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address']
            );

            $customAttribute->save();
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.8', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                'customer_address',
                'address_tag',
                [
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'Address Tag',
                    'visible' => true,
                    'required' => true,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => false,
                ]
            );
            $customAttribute = $this->eavConfig->getAttribute('customer_address', 'address_tag');

            $customAttribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address']
            );

            $customAttribute->save();
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.9', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                'customer_address',
                'district',
                [
                    'type' => 'varchar',
                    'input' => 'text',
                    'label' => 'District',
                    'visible' => true,
                    'required' => true,
                    'user_defined' => true,
                    'system' => false,
                    'group' => 'General',
                    'global' => true,
                    'visible_on_front' => false,
                ]
            );
            $customAttribute = $this->eavConfig->getAttribute('customer_address', 'district');

            $customAttribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address']
            );

            $customAttribute->save();
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.1.0', '<')) {
            $readAdapter = $setup->getConnection('core_read');
            $writeAdapter = $setup->getConnection('core_write');
            $table = $readAdapter->getTableName('core_config_data');
            $defaultCountry = $readAdapter->select()
                ->from(
                    [$table],
                    ['config_id']
                )->where("path = ?", 'general/country/default');
            $defaultCountryListConfig = $readAdapter->fetchCol($defaultCountry);
            if (!empty($defaultCountryListConfig)) {
                $writeAdapter->update($table, ['value' => 'ID'], 'config_id IN (' . implode(",", $defaultCountryListConfig) . ')');
            } else {
                $writeAdapter->insert($table, ['scope' => 'default', 'scope_id' => 0, 'path' => 'general/country/default', 'value' => 'ID']);
            }

            $allowCountry = $readAdapter->select()
                ->from(
                    [$table],
                    ['config_id']
                )->where("path = ?", 'general/country/allow');
            $allowCountryListConfig = $readAdapter->fetchCol($allowCountry);
            if (!empty($allowCountryListConfig)) {
                $writeAdapter->update($table, ['value' => 'ID'], 'config_id IN (' . implode(",", $allowCountryListConfig) . ')');
            } else {
                $writeAdapter->insert($table, ['scope' => 'default', 'scope_id' => 0, 'path' => 'general/country/allow', 'value' => 'ID']);
            }
        }

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.1.1', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute('customer_address', 'pinpoint_location', ['is_required' => 0]);
        }

        $setup->endSetup();
    }
}
