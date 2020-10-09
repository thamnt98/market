<?php

namespace SM\Category\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SM\Category\Helper\Config;

class AddCategoryMostPopularAttributes implements DataPatchInterface
{
    const DEFAULT_GROUP = 'General';

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

    public function apply()
    {
        $attributeData = [
            'label'    => 'Most Popular Category',
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
            'position' => 20,
        ];

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            Config::MOST_POPULAR_ATTRIBUTE_CODE,
            $attributeData
        );
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}
