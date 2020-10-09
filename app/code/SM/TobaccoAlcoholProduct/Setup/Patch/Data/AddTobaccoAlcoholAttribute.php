<?php
/**
 * SM\TobaccoAlcoholProduct\Setup\Patch\Data
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;

/**
 * Class AddTobaccoAlcoholAttribute
 * @package SM\TobaccoAlcoholProduct\Setup\Patch\Data
 */
class AddTobaccoAlcoholAttribute implements DataPatchInterface
{
    const IS_TOBACCO = "is_tobacco";
    const IS_ALCOHOL = "is_alcohol";

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
                self::IS_TOBACCO,
                [
                    'type' => 'int',
                    'label' => 'Is Tobacco',
                    'input'    => 'boolean',
                    'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'visible'  => true,
                    'default'  => '0',
                    'required' => false,
                    'sort_order' => 100,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'transmart',
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                self::IS_ALCOHOL,
                [
                    'type' => 'int',
                    'label' => 'Is Tobacco',
                    'input'    => 'boolean',
                    'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'visible'  => true,
                    'default'  => '0',
                    'required' => false,
                    'sort_order' => 110,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'transmart',
                ]
            );
        } catch (\Exception $e) {
        }
    }
}
