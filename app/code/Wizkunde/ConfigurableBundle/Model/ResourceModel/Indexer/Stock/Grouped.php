<?php

namespace Wizkunde\ConfigurableBundle\Model\ResourceModel\Indexer\Stock;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

class Grouped extends \Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\DefaultStock
{
    /**
     * Get the select object for get stock status by grouped product ids
     *
     * @param int|array $entityIds
     * @param bool $usePrimaryTable use primary or temporary index table
     * @return \Magento\Framework\DB\Select
     */
    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
        $connection = $this->getConnection();
        $idxTable = $usePrimaryTable ? $this->getMainTable() : $this->getIdxTable();
        $metadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $select = parent::_getStockStatusSelect($entityIds, $usePrimaryTable);
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        )->columns(
            ['e.entity_id', 'cis.website_id', 'cis.stock_id']
        )->joinLeft(
            ['l' => $this->getTable('catalog_product_link')],
            'e.' . $metadata->getLinkField() . ' = l.product_id AND l.link_type_id=' .
            \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED,
            []
        )->joinLeft(
            ['le' => $this->getTable('catalog_product_entity')],
            'le.entity_id = l.linked_product_id',
            []
        )->joinLeft(
            ['i' => $idxTable],
            'i.product_id = l.linked_product_id AND cis.website_id = i.website_id AND cis.stock_id = i.stock_id',
            []
        )->columns(
            ['qty' => new \Zend_Db_Expr('0')]
        );

        $statusExpression = $this->getStatusExpression($connection);

        $stockStatusExpr = $connection->getLeastSql(["MAX(i.stock_status)", "MIN({$statusExpression})"]);

        $select->columns(['status' => $stockStatusExpr]);

        if ($entityIds !== null) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        return $select;
    }
}
