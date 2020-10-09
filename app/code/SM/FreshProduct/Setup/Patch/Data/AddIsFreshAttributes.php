<?php

namespace SM\FreshProduct\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class AddIsFreshAttributes implements DataPatchInterface
{
    const VERSION = '0.0.1';

    const DEFAULT_GROUP = 'General';

    const IS_FRESH = 'is_fresh';

    /**
     * @var CategorySetup
     */
    private $categorySetup;

    /**
     * AddCustomerDistrictAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetup = $categorySetupFactory->create(['setup' => $moduleDataSetup]);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function apply(): void
    {
        $attributeData = [
            'label'    => 'Is Fresh',
            'type'     => 'int',
            'input'    => 'select',
            'source'   => Boolean::class,
            'visible'  => true,
            'default'  => '0',
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => self::DEFAULT_GROUP,
            'user_defined' => true,
            'system' => false,
            'position' => 999,
        ];

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            self::IS_FRESH,
            $attributeData
        );
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getAliases(): array
    {
        return [];
    }
}
