<?php
/**
 * Class AddHideCTAttribute
 * @package SM\Catalog\Setup\Patch\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;

class AddHideCTAttribute implements DataPatchInterface
{
    const CTA_ATTRIBUTE = 'hide_cta_button';
    const IS_CHANGED_CTA_VALUE = 'is_changed_cta';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        /** @var EavSetup $categorySetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        try {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                self::CTA_ATTRIBUTE,
                [
                    'type' => 'int',
                    'label' => 'Hide CTA Buttons',
                    'input' => 'checkbox',
                    'required' => false,
                    'sort_order' => 10,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'transmart',
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                self::IS_CHANGED_CTA_VALUE,
                [
                    'type' => 'int',
                    'label' => 'Is Change CTA',
                    'input' => 'checkbox',
                    'required' => false,
                    'sort_order' => 10,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group' => 'transmart',
                ]
            );

            $eavSetup->addAttribute(
                Product::ENTITY,
                self::CTA_ATTRIBUTE,
                [
                    'type' => 'int',
                    'frontend' => '',
                    'label' => 'Hide CTA Buttons',
                    'group' => 'Product Details',
                    'input' => 'text',
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => false,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_for_sort_by' => false,
                    'unique' => false,
                    'used_in_product_listing' => true,
                    'apply_to' => ''
                ]
            );
        } catch (\Exception $e) {
        }
    }
}
