<?php
/**
 * Class OrderRepository
 * @package SM\DigitalProduct\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Order;

use \SM\DigitalProduct\Model\Source\Options\DigitalTypes;

class OrderRepository implements \SM\DigitalProduct\Api\OrderRepositoryInterface
{
    /**
     * @var \Magento\Framework\DB\Select
     */
    private $itemCollection;

    /**
     * @var \Magento\Sales\Model\Order\ProductOption
     */
    private $productOption;

    /**
     * OrderRepository constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection
     * @param \Magento\Sales\Model\Order\ProductOption $productOption
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection,
        \Magento\Sales\Model\Order\ProductOption $productOption
    ) {
        $this->itemCollection = $itemCollection;
        $this->productOption = $productOption;
    }

    /**
     * @inheritDoc
     */
    public function getList($customerId, $limit = 10)
    {
        $collection = $this->itemCollection;
        $collection->getSelect()->joinLeft(
            "sales_order",
            'main_table.order_id = sales_order.entity_id'
        )->where(
            'sales_order.customer_id = ? ',
            $customerId
        )->where(
            'sales_order.is_virtual = 1'
        )->where(
            'sales_order.status = "complete"'
        )->where(
            'sales_order.is_parent = 0'
        )->limit($limit);

        $collection->setOrder("sales_order.created_at", "desc");

        foreach ($collection->getItems() as $orderItem) {
            $this->productOption->add($orderItem);
            $orderItem->setServiceType($orderItem->getBuyRequest()->getServiceType());
            $orderItem->setName($this->itemName($orderItem));
        }
        return $collection->getItems();
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return string
     */
    private function itemName(\Magento\Sales\Api\Data\OrderItemInterface $orderItem)
    {
        if ($orderItem->getServiceType() !== DigitalTypes::MOBILE_PACKAGE_VALUE
            && $product = $orderItem->getProduct()) {
            return $product->getDenom();
        }

        return $orderItem->getName();
    }
}
