<?php
/**
 * Class UpdateCTAButton
 * @package SM\Catalog\Model\CtaAttribute
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Model\CTAButton;

use SM\Catalog\Setup\Patch\Data\AddHideCTAttribute;

class Query
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * Query constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    /**
     * @param $storeId
     * @param $currentCategoryId
     * @param $productIds
     */
    public function getProductsFilter($storeId, $currentCategoryId, &$productIds)
    {
        foreach ($productIds as $productId) {
            $categoryIds = $this->getCategoryIdsByProductId($productId);
            foreach ($categoryIds as $categoryId) {
                if ($categoryId == $currentCategoryId) {
                    continue;
                }
                $ctaValues = $this->getCTAValues($categoryId, $storeId);
                if ($ctaValues) {
                    foreach ($ctaValues as $value) {
                        $key = array_search($productId, $productIds);
                        if ($value == 1 && $key !== false && $key >= 0) {
                            unset($productIds[$key]);
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $productId
     * @return array
     */
    protected function getCategoryIdsByProductId($productId)
    {
        $tableName = $this->resource->getTableName('catalog_category_product');
        $select = $this->connection->select()
            ->from(
                ['main_table' => $tableName],
                [
                    'category_id',
                ]
            )->where("product_id =?", $productId);
        return $this->connection->fetchCol($select);
    }

    /**
     * @return string
     */
    protected function getCTAttributeId()
    {
        $tableName = $this->resource->getTableName('eav_attribute');
        $select = $this->connection->select()
            ->from(
                ['main_table' => $tableName],
                [
                    'attribute_id',
                ]
            )->where("attribute_code = :attribute_code");
        $bind = [":attribute_code" => AddHideCTAttribute::CTA_ATTRIBUTE];

        return $this->connection->fetchOne($select, $bind);
    }

    /**
     * @param $categoryId
     * @return string
     */
    private function getCategoryRowId($categoryId)
    {
        $tableName = $this->resource->getTableName('catalog_category_entity');
        $select = $this->connection->select()
            ->from(
                ['main_table' => $tableName],
                [
                    'row_id',
                ]
            )->where("entity_id = ?", $categoryId);

        return $this->connection->fetchOne($select);
    }

    /**
     * @param $categoryId
     * @param $storeId
     * @return array
     */
    public function getCTAValues($categoryId, $storeId)
    {
        $tableName = $this->resource->getTableName('catalog_category_entity_int');
        $rowId = $this->getCategoryRowId($categoryId);

        $select = $this->connection->select()
            ->from(
                ['main_table' => $tableName],
                ['value']
            )->where(
                "row_id =?",
                $rowId
            )->where(
                "attribute_id =?",
                $this->getCTAttributeId()
            )->where("store_id =?", $storeId);

        return $this->connection->fetchCol($select);
    }
}
