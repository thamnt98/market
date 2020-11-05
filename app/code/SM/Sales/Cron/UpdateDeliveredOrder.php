<?php

namespace SM\Sales\Cron;

use Magento\Framework\App\ResourceConnection;
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
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * UpdateDeliveredOrder constructor.
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param StatusHistoryCollectionFactory $statusHistoryCollectionFactory
     * @param Order $orderResourceModel
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        StatusHistoryCollectionFactory $statusHistoryCollectionFactory,
        Order $orderResourceModel,
        ResourceConnection $resourceConnection
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->statusHistoryCollectionFactory = $statusHistoryCollectionFactory;
        $this->orderResourceModel = $orderResourceModel;
        $this->resourceConnection = $resourceConnection;
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

        //If order status,state is set to closed because order don't have invoice, so we force magento set order to complete by sql raw
        //This is workaround solution.
        //https://jira.smartosc.com/browse/APO-5557
        $resource      = $this->resourceConnection;
        $connection    = $this->resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $saleOrder     = $resource->getTableName('sales_order');
        $saleOrderGrid = $resource->getTableName('sales_order_grid');

        try {
            $where = ['entity_id IN (?)' => $orderIds];
            $connection->beginTransaction();
            //Update sale_order
            $connection->update($saleOrder, [
                'state'  => ParentOrderRepositoryInterface::STATUS_COMPLETE,
                'status' => ParentOrderRepositoryInterface::STATUS_COMPLETE
            ], $where);

            //Update sale_order_grid
            $connection->update($saleOrderGrid, [
                'status' => ParentOrderRepositoryInterface::STATUS_COMPLETE
            ], $where);

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }
}
