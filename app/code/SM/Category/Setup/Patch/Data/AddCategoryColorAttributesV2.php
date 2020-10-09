<?php


namespace SM\Category\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SM\Category\Helper\Config;

/**
 * Class AddCategoryColorAttributesV2
 * @package SM\Category\Setup\Patch\Data
 */
class AddCategoryColorAttributesV2 implements DataPatchInterface
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
        $attributeFavoriteBrandColor = [
            'label'    => 'Favorite Brand Color',
            'type'     => 'varchar',
            'input'    => 'text',
            'visible'  => true,
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => self::DEFAULT_GROUP,
            'user_defined' => true,
            'system' => false,
            'position' => 40,
        ];

        $attributeProductColor= [
            'label'    => 'Product Color',
            'type'     => 'varchar',
            'input'    => 'text',
            'visible'  => true,
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => self::DEFAULT_GROUP,
            'user_defined' => true,
            'system' => false,
            'position' => 45,
        ];

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            Config::FAVORITE_BRAND_COLOR,
            $attributeFavoriteBrandColor
        );

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            Config::PRODUCT_CATEGORY_COLOR,
            $attributeProductColor
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
