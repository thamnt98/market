<?php

namespace SM\Sales\Model\Order;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Psr\Log\LoggerInterface;
use SM\Sales\Api\ParentOrderRepositoryInterface;
use SM\Sales\Helper\StatusState;
use SM\Sales\Model\ParentOrderRepository;
use SM\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Updater
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * @var StatusState
     */
    protected $stateHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Identifier for history item
     *
     * @var string
     */
    protected $entityType = 'order';

    /**
     * OrderPlugin constructor.
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param OrderCollectionFactory                    $orderCollectionFactory
     * @param OrderFactory                              $orderFactory
     * @param OrderResource                             $orderResource
     * @param StatusState                               $stateHelper
     * @param LoggerInterface                           $logger
     * @param ResourceConnection                        $resourceConnection
     * @param DateTime                                  $dateTime
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        OrderCollectionFactory $orderCollectionFactory,
        OrderFactory $orderFactory,
        OrderResource $orderResource,
        StatusState $stateHelper,
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        DateTime $dateTime
    ) {
        $this->stateHelper = $stateHelper;
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->dateTime = $dateTime;
        $this->eventManager = $eventManager;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function updateStatusOrder($order)
    {
        //Set order status to complete with order repository will make orders were set to closed status because Magento
        //is check orders which aren't have invoice. So we using sql raw to update status, state order
        $resource               = $this->resourceConnection;
        $connection             = $this->resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $saleOrder              = $resource->getTableName('sales_order');
        $saleOrderGrid          = $resource->getTableName('sales_order_grid');
        $saleOrderStatusHistory = $resource->getTableName('sales_order_status_history');

        try {
            $where = ['entity_id = ?' => $order->getEntityId()];
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

            //Insert data to sales_order_status_history table
            $currentTime = $this->dateTime->gmtDate();
            $comment     = __('Order has been Successfully Completed');
            $data =  [
                'parent_id'            => $order->getEntityId(),
                'is_customer_notified' => $order->getCustomerNoteNotify(),
                'is_visible_on_front'  => 0,
                'comment'              => $comment,
                'status'               => $order->getStatus(),
                'created_at'           => $currentTime,
                'entity_name'          => $this->entityType
            ];

            $connection->insert($saleOrderStatusHistory, $data);
            $connection->commit();
            $this->eventManager->dispatch(
                'sm_sales_order_status_update',
                ['order' => $order]
            );
            $this->updateParentOrderStatus($order);
        } catch (\Exception $exception) {
            $connection->rollBack();
            $this->logger->critical('Can\'t update order status', ['exception' => $exception]);
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    public function updateParentOrderStatus($order)
    {
        try {
            if ($order->getData("is_parent") == 0) {
                $this->parentId = $parentId = $order->getData("parent_order");
                /** @var Order $parentOrder */
                $parentOrder = $this->orderFactory->create();
                $this->orderResource->load($parentOrder, $parentId);

                /** @var OrderCollection $orderCollection */
                $orderCollection = $this->orderCollectionFactory->create();
                $orderCollection->addFieldToFilter("parent_order", ["eq" => $parentId]);
                $orderCollection->sortByStatus()->load();

                $countOrder = $orderCollection->getSize();
                $countCancel = 0;
                $countComplete = 0;

                /** @var Order $subOrder */
                foreach ($orderCollection as $subOrder) {
                    if ($subOrder->getStatus() == ParentOrderRepository::STATUS_COMPLETE ||
                        $subOrder->getStatus() == ParentOrderRepository::STATUS_ORDER_CANCELED) {
                        if ($subOrder->getStatus() == ParentOrderRepository::STATUS_ORDER_CANCELED) {
                            $countCancel++;
                        } else {
                            $countComplete++;
                        }
                    } else {
                        /** @var Order $firstOrder */
                        $firstOrder = $orderCollection->getFirstItem();
                        if ($firstOrder->getStatus() != $parentOrder->getStatus()) {
                            $this->updateParentStatus(
                                $this->stateHelper->getState($firstOrder->getStatus()),
                                $firstOrder->getStatus()
                            );
                            return $this;
                        }
                    }
                }

                if ($countCancel + $countComplete == $countOrder) {
                    if ($countCancel == $orderCollection->getSize()) {
                        $this->updateParentStatus(
                            ParentOrderRepository::STATUS_ORDER_CANCELED,
                            ParentOrderRepository::STATUS_ORDER_CANCELED
                        );
                    } else {
                        $this->updateParentStatus(
                            ParentOrderRepository::STATUS_COMPLETE,
                            ParentOrderRepository::STATUS_COMPLETE
                        );
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->critical('Error when set status parent order', ['exception' => $exception]);
        }
    }

    /**
     * @param $state
     * @param $status
     */
    private function updateParentStatus($state, $status)
    {
        $connection = $this->orderResource->getConnection();
        try {
            $table = $this->orderResource->getMainTable();
            $connection->beginTransaction();
            $connection->update(
                $table,
                [
                    'state'  => $state,
                    'status' => $status
                ],
                "entity_id = $this->parentId"
            );

            $connection->update(
                $table . "_grid",
                [
                    'status' => $status
                ],
                "entity_id = $this->parentId"
            );
            $connection->commit();
        } catch (LocalizedException $e) {
            $connection->rollBack();
        }
    }
}
