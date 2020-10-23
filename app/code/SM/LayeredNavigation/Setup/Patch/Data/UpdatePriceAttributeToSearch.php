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

class UpdatePriceAttributeToSearch implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * AddAllFilterListToCategory constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
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
        $eavAttrTable = $this->setup->getTable('eav_attribute');
        $catalogAttrTable = $this->setup->getTable('catalog_eav_attribute');
        if (!$this->setup->tableExists($eavAttrTable) || !$this->setup->tableExists($eavAttrTable)) {
            return;
        }

        $conn = $this->setup->getConnection();
        $select = $conn->select()
            ->from($eavAttrTable, 'attribute_id')
            ->where('attribute_code LIKE \'%base_price%\'')
            ->orWhere('attribute_code LIKE \'%promo_price%\'');

        $attrIds = $conn->fetchCol($select);
        if (empty($attrIds)) {
            return;
        }

        $where = 'attribute_id IN (' . implode(',', $attrIds) . ')';
        $conn->update(
            $catalogAttrTable,
            ['is_searchable' => 1],
            $where
        );
    }
}
