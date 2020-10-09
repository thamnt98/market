<?php
/**
 * @category Magento
 * @package SM\Sales\Observer
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use SM\Sales\Helper\StatusState;
use SM\Sales\Model\ParentOrderRepository;
use SM\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class UpdateStatus
 * @package SM\Sales\Observer
 */
class UpdateStatusObserver implements ObserverInterface
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
     * @var int
     */
    private $parentId;

    /**
     * OrderPlugin constructor.
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderFactory $orderFactory
     * @param OrderResource $orderResource
     * @param StatusState $stateHelper
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrderFactory $orderFactory,
        OrderResource $orderResource,
        StatusState $stateHelper
    ) {
        $this->stateHelper = $stateHelper;
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if (!$order->getId()) {
            return $this;
        }

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
        }
        return $this;
    }

    /**
     * @param $state
     * @param $status
     * @throws LocalizedException
     */
    private function updateParentStatus($state, $status)
    {
        try {
            $connection = $this->orderResource->getConnection();
            $table = $this->orderResource->getMainTable();

            $connection->update(
                $table,
                [
                    'state' => $state,
                    'status' => $status
                ],
                "entity_id =$this->parentId"
            );

            $connection->update(
                $table . "_grid",
                [
                    'status' => $status
                ],
                "entity_id =$this->parentId"
            );
        } catch (LocalizedException $e) {
        }
    }
}
