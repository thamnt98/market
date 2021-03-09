<?php
/**
 * SM\Review\Helper
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Review\Helper;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class Order
 * @package SM\Review\Helper
 */
class Order extends AbstractHelper
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * Order constructor.
     * @param Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(
        Context $context,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param int[] $orderIds
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getMainOrderCollection($orderIds)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection */
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToSelect([
            "reference_order_id",
            "created_at",
            "entity_id"
        ]);
        $orderCollection->addFieldToFilter("entity_id", ["in" => $orderIds]);
        return $orderCollection;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function sortCollection($searchCriteria, $orderCollection)
    {
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $orderCollection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == strtolower(SortOrder::SORT_ASC)) ? 'ASC' : 'DESC'
                );
            }
        }
        return $orderCollection;
    }
}
