<?php

namespace SM\Sales\Cron;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Model\Order\Status\History as StatusHistory;
use Magento\Sales\Model\ResourceModel\Order;
use SM\Sales\Api\ParentOrderRepositoryInterface;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use SM\Sales\Model\ResourceModel\Order\StatusHistory\Collection as StatusHistoryCollection;
use SM\Sales\Model\ResourceModel\Order\StatusHistory\CollectionFactory as StatusHistoryCollectionFactory;

/**
 * Class UpdateDeliveredOrder
 * @package SM\Sales\Cron
 */
class UpdateDeliveredOrder
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var StatusHistoryCollectionFactory
     */
    protected $statusHistoryCollectionFactory;

    /**
     * @var Order
     */
    private $orderResourceModel;

    /**
     * UpdateDeliveredOrder constructor.
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param StatusHistoryCollectionFactory $statusHistoryCollectionFactory
     * @param Order $orderResourceModel
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        StatusHistoryCollectionFactory $statusHistoryCollectionFactory,
        Order $orderResourceModel
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->statusHistoryCollectionFactory = $statusHistoryCollectionFactory;
        $this->orderResourceModel = $orderResourceModel;
    }

    /**
     * Execute method
     */
    public function execute()
    {
        /** @var StatusHistoryCollection $historyCollection */
        $historyCollection = $this->statusHistoryCollectionFactory->create();
        $historyCollection->addFieldToSelect(
            [
                OrderStatusHistoryInterface::PARENT_ID,
                OrderStatusHistoryInterface::STATUS,
                OrderStatusHistoryInterface::CREATED_AT
            ]
        );
        $historyCollection->addFieldToFilter(
            OrderStatusHistoryInterface::STATUS,
            ParentOrderRepositoryInterface::STATUS_DELIVERED
        );

        $orderIds = [];
        /** @var StatusHistory $history */
        foreach ($historyCollection as $history) {
            $deliveredTime = strtotime($history->getCreatedAt());
            $now = strtotime("-24 hours", time());
            if ($now > $deliveredTime) {
                $orderIds[] = $history->getParentId();
            }
        }

        if (empty($orderIds)) {
            return;
        }

        $this->updateOrderStatus($orderIds);
    }

    /**
     * @param array $orderIds
     */
    private function updateOrderStatus(array $orderIds)
    {
        /**
         * @var \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection
         */
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter(
                OrderInterface::ENTITY_ID,
                ["in" => $orderIds]
            )->addFieldToFilter(
                OrderInterface::STATE,
                ["neq" => ParentOrderRepositoryInterface::STATUS_COMPLETE]
            )->addFieldToFilter(
                OrderInterface::STATUS,
                ["neq" => ParentOrderRepositoryInterface::STATUS_COMPLETE]
            )->setPage(1, 100);

        foreach ($orderCollection as $order) {
            $order->setState(ParentOrderRepositoryInterface::STATUS_COMPLETE)
                ->setStatus(ParentOrderRepositoryInterface::STATUS_COMPLETE)
                ->addCommentToStatusHistory("Order has been Successfully Completed");
            try {
                $this->orderResourceModel->save($order);
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
