<?php

namespace SM\MobileApi\Model;

use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use SM\MobileApi\Api\ReOrderQuicklyInterface;
use SM\MobileApi\Model\Data\Order\OrderItemFactory;
use SM\MobileApi\Model\Data\Order\OrderFactory;
use SM\MobileApi\Model\Data\Order\ListOrdersFactory;

class ReOrderQuickly implements ReOrderQuicklyInterface
{
    const STATE_CAN_REORDER = 'complete';
    /**
     * @var CollectionFactory
     */
    protected $orderFactory;
    /**
     * @var OrderItemFactory
     */
    protected $orderItemFactory;
    /**
     * @var OrderFactory
     */
    protected $orderDataFactory;
    /**
     * @var ListOrdersFactory
     */
    protected $listOrdersFactory;
    /**
     * @var \Magento\Sales\Helper\Reorder
     */
    protected $reorderHelper;

    public function __construct(
        CollectionFactory $orderFactory,
        OrderItemFactory $orderItemFactory,
        OrderFactory $orderDataFactory,
        ListOrdersFactory $listOrdersFactory,
        \Magento\Sales\Helper\Reorder $reorderHelper
    ) {
        $this->orderFactory      = $orderFactory;
        $this->orderItemFactory  = $orderItemFactory;
        $this->orderDataFactory  = $orderDataFactory;
        $this->listOrdersFactory = $listOrdersFactory;
        $this->reorderHelper     = $reorderHelper;
    }

    public function getOrderDetail($orderId)
    {
        // TODO: Implement getOrderDetail() method.
        return true;
    }

    public function getOrderHistory($customerId)
    {
        // TODO: Implement getOrderHistory() method.
        return true;
    }

    public function reOrder($orderId)
    {
        // TODO: Implement reOrder() method.
        return true;
    }

    public function getOrdersCanReorder($customerId, $pageSize=12, $currentPage=1)
    {
        $ordersCollection = $this->orderFactory->create();
        $ordersCollection->addFieldToFilter('customer_id', $customerId);
        $ordersCollection->addFieldToFilter('state', self::STATE_CAN_REORDER);
        $ordersCollection->setPageSize($pageSize);
        $ordersCollection->setCurPage($currentPage);
        $ordersCollection->setOrder('created_at', 'desc');
        $orders = [];
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($ordersCollection as $order) {
            if ($this->reorderHelper->canReorder($order->getId())) {
                $orders[] = $this->parseOrder($order);
            }
        }
        $response = $this->listOrdersFactory->create();
        $response->setTotal($ordersCollection->getTotalCount());
        $response->setPageSize($ordersCollection->getPageSize());
        $response->setOrders($orders);
        return $response;
    }

    public function parseOrderItem(Item $item)
    {
        $orderItemData = $this->orderItemFactory->create();
        $orderItemData->setSku($item->getSku());
        $orderItemData->setName($item->getName());
        $orderItemData->setQty($item->getQtyOrdered());
        return $orderItemData;
    }

    public function parseOrder(\Magento\Sales\Model\Order $orderCollection)
    {
        $items     = $orderCollection->getAllItems();
        $orderData = $this->orderDataFactory->create();
        foreach ($items as $item) {
            $itemData[] = $this->parseOrderItem($item);
        }
        $orderData->setBaseGrandTotal($orderCollection->getBaseGrandTotal());
        $orderData->setCreatedAt($orderCollection->getCreatedAt());
        $orderData->setIncrementId($orderCollection->getIncrementId());
        $orderData->setStatus($orderCollection->getStatus());
        $orderData->setOrderItems($itemData);
        return $orderData;
    }
}
