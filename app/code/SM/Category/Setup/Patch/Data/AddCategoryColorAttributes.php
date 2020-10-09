<?php

namespace SM\Category\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SM\Category\Helper\Config;

class AddCategoryColorAttributes implements DataPatchInterface
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

    /**
     * @return AddCategoryColorAttributes|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $attributeMainCategoryColor = [
            'label'    => 'Main Category Color',
            'type'     => 'varchar',
            'input'    => 'text',
            'visible'  => true,
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => self::DEFAULT_GROUP,
            'user_defined' => true,
            'system' => false,
            'position' => 30,
        ];

        $attributeSecondCategoryColor= [
            'label'    => 'Second Category Color',
            'type'     => 'varchar',
            'input'    => 'text',
            'visible'  => true,
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => self::DEFAULT_GROUP,
            'user_defined' => true,
            'system' => false,
            'position' => 35,
        ];

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            Config::MAIN_CATEGORY_COLOR,
            $attributeMainCategoryColor
        );

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            Config::SUB_CATEGORY_COLOR,
            $attributeSecondCategoryColor
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
