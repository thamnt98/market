<?php
/**
 * Class Bestsellers
 * @package SM\Catalog\Plugin\Model\ResourceModel\Report
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Plugin\Model\ResourceModel\Report;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use SM\Catalog\Setup\Patch\Data\AddProductSortByAttributes as Attributes;

class Bestsellers
{
    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Report\Bestsellers
     */
    private $subject;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @param ResolverInterface $localeResolver
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResolverInterface $localeResolver,
        ProductRepositoryInterface $productRepositoryInterface,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->localeResolver = $localeResolver;
        $this->productRepository = $productRepositoryInterface;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Model\ResourceModel\Report\Bestsellers $subject
     * @param $result
     * @param $from
     * @param $to
     * @return mixed
     */
    public function afterAggregate(
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers $subject,
        $result,
        $from = null,
        $to = null
    ) {
        $this->subject = $subject;
        $this->connection = $subject->getConnection();
        if ($data = $this->getBestSellersData($from, $to)) {
            try {
                $attributeId = $this->getBestSellersAttributeId();
                $tableName = $subject->getTable('catalog_product_entity_decimal');

                foreach ($data as $row) {
                    try {
                        $row['store_id'] = 0;
                        $productRowId = $this->productRepository->getById($row['product_id'])->getRowId();
                        $this->updateAttributeData($productRowId, $row, $tableName, $attributeId);
                    } catch (\Exception $e) {
                        $this->logger->critical(__FILE__ . $e->getMessage());
                        continue;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->critical(__FILE__ . $e->getMessage());
            }
        }
        return $result;
    }

    /**
     * @param $from
     * @param $to
     * @return array
     */
    protected function getBestSellersData($from, $to)
    {
        $connection = $this->connection;
        $table = $this->subject->getTable('sales_order_item');
        $select = $connection->select();
        $currentDate = $this->dateTime->gmtDate('Y-m-d');

        $columns = [
            'product_id' => 'source_table.product_id',
            'qty_ordered' => new \Zend_Db_Expr('SUM(source_table.qty_ordered)'),
        ];

        $select->from(
            ['source_table' => $table],
            $columns
        )->joinLeft(
            ['order' => $this->subject->getTable('sales_order')],
            'source_table.order_id = order.entity_id',
            []
        )->where(
            'order.state != ?',
            \Magento\Sales\Model\Order::STATE_CANCELED
        );

        if ($from !== null || $to !== null) {
            $select->where(
                'source_table.created_at >= ?',
                $currentDate . ' 00:00:00'
            );
        }

        $select->group(['product_id']);

        return $connection->fetchAll($select);
    }

    /**
     * @param $productRowId
     * @param $row
     * @param $tableName
     * @param $attributeId
     */
    protected function updateAttributeData(
        $productRowId,
        $row,
        $tableName,
        $attributeId
    ) {
        $connection = $connection = $this->connection;
        $select = $connection->select()
            ->from(
                ['main_table' => $tableName],
                ['value']
            )->where(
                "row_id = :row_id"
            )->where(
                "attribute_id = :attribute_id"
            )->where("store_id = :store_id");

        $bind = [":row_id" => $productRowId, ":attribute_id" => $attributeId, ":store_id" => $row['store_id']];
        $rowData = $connection->fetchOne($select, $bind);

        if (!$rowData) {
            $data = [
                'attribute_id' => $attributeId,
                'store_id' => $row['store_id'],
                'value' => $row['qty_ordered'],
                'row_id' => $productRowId
            ];
            $connection->insertArray(
                $tableName,
                ['attribute_id', 'store_id', 'value', 'row_id'],
                [$data]
            );
        } else {
            $data = [
                'value' => $row['qty_ordered'],
            ];
            $connection->update($tableName, $data, "`row_id`='{$productRowId}'");
        }
    }

    /**
     * @return string
     */
    protected function getBestSellersAttributeId()
    {
        $tableName = $this->subject->getTable('eav_attribute');
        $select = $this->connection->select()
            ->from(
                ['main_table' => $tableName],
                [
                    'attribute_id',
                ]
            )->where("attribute_code = :attribute_code");
        $bind = [":attribute_code" => Attributes::BEST_SELLERS];

        return $this->connection->fetchOne($select, $bind);
    }
}
