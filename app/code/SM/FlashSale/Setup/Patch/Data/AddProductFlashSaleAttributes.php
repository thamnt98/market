<?php
declare(strict_types=1);

namespace SM\FlashSale\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class AddProductFlashSaleAttributes implements DataPatchInterface
{
    const IS_FLASHSALE = 'is_flashsale';
    const FLASHSALE_QTY  = 'flashsale_qty';
    const FLASHSALE_QTY_PER_CUSTOMER  = 'flashsale_qty_per_customer';


    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * CreateIsMagicConfigurableProductAttribute constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(EavSetupFactory $eavSetupFactory, ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::IS_FLASHSALE,
            [
                'type' => 'int',
                'frontend' => '',
                'label' => 'Is Flash Sale',
                'group' => 'General',
                'input' => 'select',
                'source' => 'SM\FlashSale\Model\Boolean',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_for_sort_by' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::FLASHSALE_QTY,
            [
                'type' => 'int',
                'frontend' => '',
                'label' => 'Flash Sale Quantity',
                'group' => 'General',
                'input' => 'text',
                'frontend_class' => 'validate-greater-than-zero',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 1,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_for_sort_by' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::FLASHSALE_QTY_PER_CUSTOMER,
            [
                'type' => 'int',
                'frontend' => '',
                'label' => 'Flash Sale Quantity Per Customer',
                'group' => 'General',
                'input' => 'text',
                'frontend_class' => 'validate-greater-than-zero',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 1,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_for_sort_by' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }
}
