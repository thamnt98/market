<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 02 2020
 * Time: 10:18 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Setup\Patch\Data;

class AddAllFilterListToCategory implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \SM\LayeredNavigation\Helper\Data\FilterList
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * AddAllFilterListToCategory constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \SM\LayeredNavigation\Helper\Data\FilterList      $helper
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \SM\LayeredNavigation\Helper\Data\FilterList $helper
    ) {
        $this->helper = $helper;
        $this->setup = $setup;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->setup->startSetup();

        $this->update();

        $this->setup->endSetup();
    }

    protected function update()
    {
        $attributeFilters = $this->helper->getAllOptions();
        if (count($attributeFilters) < 1) {
            return;
        }

        $attributeFilters = array_keys($attributeFilters);
        $conn = $this->setup->getConnection();
        $select = $conn->select()->from('catalog_category_entity', ['entity_id']);
        $categoryIds = $conn->fetchCol($select);
        foreach ($categoryIds as $categoryId) {
            foreach ($attributeFilters as $key => $attrCode) {
                $conn->insert(
                    \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList::TABLE_NAME,
                    [
                        'category_id'    => $categoryId,
                        'attribute_code' => $attrCode,
                        'position'       => $key
                    ]
                );
            }
        }
    }
}
