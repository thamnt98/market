<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: January, 21 2021
 * Time: 6:14 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Setup\Patch\Data;

class UpdateProductCategoryName implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * Constructor.
     *
     * @param \Magento\Eav\Model\Config                         $eavConfig
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
        $this->eavConfig = $eavConfig;
    }

    public function apply()
    {
        $this->setup->startSetup();

        $this->update();

        $this->setup->endSetup();
    }

    public function update()
    {
        $conn = $this->setup->getConnection();
        try {
            $nameAttr = $this->eavConfig->getAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'name'
            );
            $catNameAttr = $this->eavConfig->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'category_names'
            );
        } catch (\Exception $e) {
            return;
        }

        $productCategoriesSelect = $conn
            ->select()
            ->from(['ccp_1' => 'catalog_category_product'], ["group_concat(DISTINCT replace(ccev.value, ',', '|||'))"])
            ->joinInner(['cce' => 'catalog_category_entity'], 'cce.entity_id = ccp_1.category_id', [])
            ->joinInner(['ccev' => 'catalog_category_entity_varchar'], 'ccev.row_id = cce.row_id', [])
            ->where('ccp_1.product_id = cpe.entity_id')
            ->where('ccev.attribute_id = ?', $nameAttr->getId());
        $select = $conn
            ->select()
            ->from(['cpe' => 'catalog_product_entity'], ['row_id'])
            ->joinInner(['ccp' => 'catalog_category_product'], 'ccp.product_id = cpe.entity_id', [])
            ->group('cpe.row_id')
            ->columns([
                "({$productCategoriesSelect->__toString()}) as categories"
            ]);

        $sql = "INSERT INTO {$catNameAttr->getBackendTable()} (row_id, attribute_id, value) VALUES";
        $data = $conn->fetchAll($select);

        foreach ($data as $index => $row) {
            $cats = str_replace(',', ' | ', $row['categories']);
            $cats = str_replace('|||', ',', $cats);
            $cats = str_replace("'", "\'", $cats);
            $sql .= " ({$row['row_id']}, {$catNameAttr->getId()}, '{$cats}')";

            if ($index < (count($data) -1)) {
                $sql .= ',';
            }
        }

        $sql .= 'ON DUPLICATE KEY UPDATE value=VALUES(value)';

        $conn->query($sql);
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
