<?php

namespace Wizkunde\ConfigurableBundle\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'bundle_cascade_qty',
            [
                'group' => 'Configurable Bundle',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Cascade Quantity',
                'input' => 'select',
                'source' => '\Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'default'  => '0',
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible' => true,
                'visible_on_front' => false,
                'apply_to' => 'bundle',
                'unique' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'bundle_attribute_qty_mode',
            [
                'group' => 'Configurable Bundle',
                'type' => 'varchar',
                'label' => 'Attribute Quantity Mode',
                'input' => 'select',
                'source' => \Wizkunde\ConfigurableBundle\Model\Config\Source\QtyMode::class,
                'required' => false,
                'sort_order' => 60,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible' => true,
                'visible_on_front' => false,
                'apply_to' => 'bundle',
                'unique' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'bundle_attribute_qty',
            [
                'group' => 'Configurable Bundle',
                'type' => 'varchar',
                'label' => 'Quantity Attribute',
                'input' => 'select',
                'source' => \Wizkunde\ConfigurableBundle\Model\Config\Source\Attribute::class,
                'required' => false,
                'sort_order' => 70,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible' => true,
                'visible_on_front' => false,
                'apply_to' => 'bundle',
                'unique' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'bundle_attribute_sync',
            [
                'group' => 'Configurable Bundle',
                'type' => 'int',
                'label' => 'Synced Attributes',
                'input' => 'select',
                'source' => '\Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 80,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'default'  => '0',
                'visible' => true,
                'visible_on_front' => false,
                'apply_to' => 'bundle',
                'unique' => false
            ]
        );
    }
}
