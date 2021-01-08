<?php
/**
 * @category SM
 * @package SM_Review
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      dungnm<dungnm@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Review\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use SM\Review\Api\Data\CheckResultDataInterface;
use SM\Review\Api\Data\CheckResultDataInterfaceFactory;
use SM\Review\Api\Data\Product\ProductToBeReviewedInterface;
use SM\Review\Api\Data\Product\ProductToBeReviewedInterfaceFactory;
use SM\Review\Api\Data\ToBeReviewedInterface;
use SM\Review\Api\Data\ToBeReviewedInterfaceFactory;
use SM\Review\Api\Data\ToBeReviewedSearchResultsInterface;
use SM\Review\Api\ToBeReviewedRepositoryInterface;
use SM\Review\Helper\Data;
use SM\Review\Helper\Order;

/**
 * Class ToBeReviewedRepository
 * @package SM\Review\Model
 */
class ToBeReviewedRepository implements ToBeReviewedRepositoryInterface
{
    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ToBeReviewedInterfaceFactory
     */
    protected $toBeReviewedDataFactory;

    /**
     * @var ProductToBeReviewedInterfaceFactory
     */
    protected $productToBeReviewedDataFactory;

    /**
     * @var CheckResultDataInterfaceFactory
     */
    protected $checkResultDataFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Order
     */
    protected $orderHelper;

    private $orderCollection;

    /**
     * ToBeReviewedRepository constructor.
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param ToBeReviewedInterfaceFactory $toBeReviewedDataFactory
     * @param ProductToBeReviewedInterfaceFactory $productToBeReviewedDataFactory
     * @param CheckResultDataInterfaceFactory $checkResultDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param Data $dataHelper
     * @param Order $orderHelper
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        ToBeReviewedInterfaceFactory $toBeReviewedDataFactory,
        ProductToBeReviewedInterfaceFactory $productToBeReviewedDataFactory,
        CheckResultDataInterfaceFactory $checkResultDataFactory,
        DataObjectHelper $dataObjectHelper,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        Data $dataHelper,
        Order $orderHelper
    ) {
        $this->orderHelper = $orderHelper;
        $this->dataHelper = $dataHelper;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->checkResultDataFactory = $checkResultDataFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->toBeReviewedDataFactory = $toBeReviewedDataFactory;
        $this->productToBeReviewedDataFactory = $productToBeReviewedDataFactory;
    }

    public function getOrderCollection()
    {
        return $this->orderCollection;
    }

    public function setOrderCollection($value)
    {
        $this->orderCollection = $value;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria;
     * @param int $customerId
     * @return ToBeReviewedSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $customerId)
    {
        /** @var ToBeReviewedSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $itemCollection = $this->joinItemCollectionToSearch();
        $itemCollection->addFieldToFilter("main_order.customer_id", $customerId);
        $itemCollection = $this->filterCollectionByCriteria($searchCriteria, $itemCollection);
        $productIdsAndOrderIds = $this->prepareProductIdsAndOrderIds($itemCollection);
        $productIds = $productIdsAndOrderIds["product_ids"];
        $orderIds = $productIdsAndOrderIds["order_ids"];
        $products = $this->dataHelper->getProducts($productIds);

        /**
         * Order Collection
         */
        $orderCollection = $this->orderHelper->getMainOrderCollection($orderIds);
        $orderCollection = $this->orderHelper->sortCollection($searchCriteria, $orderCollection);

        $searchResults->setTotalCount($orderCollection->getSize());

        $orderCollection->setCurPage($searchCriteria->getCurrentPage());
        $orderCollection->setPageSize($searchCriteria->getPageSize());
        $this->setOrderCollection($orderCollection);

        /**
         * Prepare list item
         */
        $listItem = $this->prepareAndPopulateListItem($itemCollection, $products, $orderIds);
        $preparedList = $this->prepareListToBeReviewed($orderCollection);
        $items = [];
        foreach ($preparedList as $toBeReviewed) {
            if (isset($listItem[$toBeReviewed->getOrderId()])) {
                $toBeReviewed->setProducts($listItem[$toBeReviewed->getOrderId()]);
            }
            $items[] = $toBeReviewed;
        }

        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @return CheckResultDataInterface
     */
    public function isReviewAllowed($customerId, $productId)
    {
        /** @var CheckResultDataInterface $checkResult */
        $checkResult = $this->checkResultDataFactory->create();
        $checkResult->setIsAllow(0);
        $itemCollection = $this->joinItemCollectionToSearch();
        $itemCollection->addOrder("main_table.created_at", "DESC");
        $itemCollection->addFieldToFilter("main_order.customer_id", $customerId);
        $itemCollection->addFieldToFilter("product_id", $productId);

        /** @var Item $item */
        $item = $itemCollection->getFirstItem();
        if ($item->getId() && $item->getProductId() == $productId) {
            $checkResult->setOrderId($item->getOrderId());
            $checkResult->setIsAllow(1);
            return $checkResult;
        }

        return $checkResult;
    }

    /**
     * @param OrderItemCollection $itemCollection
     * @param ProductInterface[] $products
     * @param int[] $orderIds
     * @return ProductToBeReviewedInterface[][]
     */
    private function prepareAndPopulateListItem($itemCollection, $products, $orderIds)
    {
        $list = [];
        /** @var Item $item */
        foreach ($itemCollection as $item) {
            if (in_array($item->getOrderId(), $orderIds)) {
                $productToBeReviewed = $this->productToBeReviewedDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $productToBeReviewed,
                    [
                        "product_name" => $item->getName(),
                        "product_id" => $item->getProductId()
                    ],
                    ProductToBeReviewedInterface::class
                );
                if (isset($products[$item->getProductId()])) {
                    $productToBeReviewed
                        ->setProductImage(
                            $this->dataHelper->getMediaUrl($products[$item->getProductId()]->getData("image"))
                        )
                        ->setProductUrl($products[$item->getProductId()]->getProductUrl());
                }

                $list[$item->getOrderId()][$item->getProductId()] = $productToBeReviewed;
            }
        }
        return $list;
    }

    /**
     * @param OrderCollection $orderCollection
     * @return ToBeReviewedInterface[]
     */
    private function prepareListToBeReviewed($orderCollection)
    {
        $list = [];
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orderCollection as $order) {
            /** @var ToBeReviewedInterface $toBeReviewed */
            $toBeReviewed = $this->toBeReviewedDataFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $toBeReviewed,
                [
                    "time_created" => $this->dataHelper->dateFormat($order->getCreatedAt()),
                    "reference_number" => $order->getData('reference_order_id'),
                    "order_id" => $order->getEntityId()
                ],
                ToBeReviewedInterface::class
            );
            $list[$order->getEntityId()] = $toBeReviewed;
        }
        return $list;
    }

    /**
     * @param OrderItemCollection $orderItemCollection
     * @return array[]
     */
    private function prepareProductIdsAndOrderIds($orderItemCollection)
    {
        $productIds = [];
        $orderIds = [];
        /** @var Item $item */
        foreach ($orderItemCollection as $item) {
            $productIds[] = $item->getProductId();
            $orderIds[] = $item->getOrderId();
        }
        return [
            "product_ids" => array_unique($productIds),
            "order_ids" => array_unique($orderIds)
        ];
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OrderItemCollection $orderItemCollection
     * @return OrderItemCollection
     */
    private function filterCollectionByCriteria($searchCriteria, $orderItemCollection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == "key") {
                    $orderItemCollection->addFieldToFilter(
                        [
                            "main_table.name",
                            "main_table.sku",
                            "main_order.reference_number"
                        ],
                        [
                            ["like" => "%" . $filter->getValue() . "%"],
                            ["like" => "%" . $filter->getValue() . "%"],
                            ["like" => "%" . $filter->getValue() . "%"]
                        ]
                    );
                    continue;
                }

                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getValue() == "customer_id") {
                    $fields[] = 'main_order.' . $filter->getField();
                } else {
                    $fields[] = 'main_table.' . $filter->getField();
                }
                if ($condition == 'like') {
                    $conditions[] = [$condition => '%' . $filter->getValue() . '%'];
                } else {
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $orderItemCollection->addFieldToFilter($fields, $conditions);
            }
        }
        return $orderItemCollection;
    }

    /**
     * @return OrderItemCollection
     */
    private function joinItemCollectionToSearch()
    {
        /** @var OrderItemCollection $orderItemCollection */
        $orderItemCollection = $this->orderItemCollectionFactory->create();
        $expression = new \Zend_Db_Expr(
            '(`main_table`.`qty_ordered` - `main_table`.`qty_refunded`)'
        );
        $orderItemCollection->addFieldToSelect([
            "order_id",
            "parent_item_id",
            "sku",
            "name",
            "product_id",
            "created_at"
        ]);

        $orderItemCollection->join(
            ["main_order" => "sales_order"],
            "main_table.order_id = main_order.entity_id",
            [
                "entity_id",
                "reference_number",
                "customer_id",
                "status"
            ]
        );

        $orderItemCollection->getSelect()->joinLeft(
            "review",
            "main_order.entity_id = review.order_id and review.entity_pk_value = main_table.product_id",
            [
                "review_id"
            ]
        )->group("main_table.item_id");

        $orderItemCollection->addFieldToFilter("main_order.is_parent", 0);
        $orderItemCollection->addFieldToFilter("main_table.parent_item_id", ["null" => true]);
        $orderItemCollection->addFieldToFilter("main_order.reference_number", ["neq" => "null"]);
        $orderItemCollection->addFieldToFilter("review.review_id", ["null" => true]);
        $orderItemCollection->addFieldToFilter("main_order.status", "complete");
        $orderItemCollection->addFieldToFilter($expression, ["gt" => 0]);
        return $orderItemCollection;
    }
}
