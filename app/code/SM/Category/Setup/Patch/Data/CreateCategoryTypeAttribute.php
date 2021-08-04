<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Category
 *
 * Date: June, 09 2021
 * Time: 4:29 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Category\Setup\Patch\Data;

class CreateCategoryTypeAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Setup\EavSetupFactory                $eavSetupFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->setup           = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->setup->startSetup();

        $this->update();

        $this->setup->endSetup();
    }

    /**
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update()
    {
        $attrCode = 'category_type';
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);

        if ($eavSetup->getAttribute(\Magento\Catalog\Model\Category::ENTITY, $attrCode)) {
            return;
        }

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            $attrCode,
            [
                'label'        => 'Category Type',
                'type'         => 'varchar',
                'input'        => 'select',
                'visible'      => true,
                'required'     => false,
                'default'      => \SM\Category\Model\Entity\Attribute\Source\CategoryType::TYPE_DEFAULT,
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'        => 'General Information',
                'source'       => \SM\Category\Model\Entity\Attribute\Source\CategoryType::class,
                'user_defined' => true,
                'system'       => false,
                'sort_order'   => 50,
            ]
        );
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
