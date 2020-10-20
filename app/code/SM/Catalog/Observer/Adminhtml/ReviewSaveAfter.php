<?php
/**
 * Class ReviewSaveAfter
 * @package SM\Catalog\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Catalog\Setup\Patch\Data\AddProductSortByAttributes as Attributes;

class ReviewSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory
     */
    private $summaryCollectionFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * ReviewSaveAfter constructor.
     * @param \Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory $sumColFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory $sumColFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->summaryCollectionFactory = $sumColFactory;
        $this->productRepository = $productRepository;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    /**
     * Save rating_sum attribute value
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Review\Model\Review $review
         * @var \Magento\Catalog\Model\Product $product
         */
        $review = $observer->getEvent()->getDataObject();
        if ($review->getId()) {
            /**
             * Don't need try catch. Because handle in when call Save
             */
            $entityType = $review->getEntityIdByCode($review::ENTITY_PRODUCT_CODE);

            if ($entityType && $review->getStatusId() == $review::STATUS_APPROVED) {
                $product = $this->productRepository->getById($review->getEntityPkValue());
                $this->checkAndUpdateAttribute($review, $product, $entityType);
            }
        }
    }

    /**
     * @param $review
     * @param $product
     * @param $entityType
     */
    protected function checkAndUpdateAttribute($review, $product, $entityType)
    {
        $rowId = $product->getRowId();
        $ratingAttributeId = $this->getRatingAttributeId();

        foreach ($review->getStores() as $store) {
            if ($store == 0) {
                continue;
            }

            $summary = $this->summaryCollectionFactory->create()
                ->addEntityFilter($product->getId(), $entityType)
                ->addStoreFilter($store)
                ->getFirstItem();

            $sum = $summary->getData('rating_summary');
            if (empty($sum)) {
                continue;
            }

            $tableName = $this->resource->getTableName('catalog_product_entity_int');
            $this->updateAttributeData($rowId, $store, $sum, $tableName, $ratingAttributeId);
        }
    }

    /**
     * @param $rowId
     * @param $store
     * @param $sum
     * @param $tableName
     * @param $ratingAttributeId
     */
    protected function updateAttributeData($rowId, $store, $sum, $tableName, $ratingAttributeId)
    {
        $this->connection = $this->resource->getConnection();
        $select = $this->connection->select()
            ->from(
                ['main_table' => $tableName],
                ['value']
            )->where(
                "row_id = :row_id"
            )->where(
                "attribute_id = :attribute_id"
            )->where("store_id = :store_id");

        $bind = [":row_id" => $rowId, ":attribute_id" => $ratingAttributeId, ":store_id" => $store];
        $rowData = $this->connection->fetchOne($select, $bind);

        if (!$rowData) {
            $data = [
                'attribute_id' => $ratingAttributeId,
                'store_id' => $store,
                'value' => $sum,
                'row_id' => $rowId
            ];
            $this->connection->insertArray(
                $tableName,
                ['attribute_id', 'store_id', 'value', 'row_id'],
                [$data]
            );
        } else {
            $data = [
                'value' => $sum,
            ];
            $this->connection->update($tableName, $data, "`row_id`='{$rowId}'");
        }
    }

    /**
     * @return string
     */
    protected function getRatingAttributeId()
    {
        $tableName = $this->resource->getTableName('eav_attribute');
        $select = $this->connection->select()
            ->from(
                ['main_table' => $tableName],
                [
                    'attribute_id',
                ]
            )->where("attribute_code = :attribute_code");
        $bind = [":attribute_code" => Attributes::RATING];

        return $this->connection->fetchOne($select, $bind);
    }
}
