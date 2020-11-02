<?php

namespace SM\Sales\Model;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection as StatusCollection;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;
use SM\Sales\Api\Data\ParentOrderDataInterface;
use SM\Sales\Api\Data\SubOrderDataInterface;
use SM\Sales\Api\Data\SubOrderSearchResultsInterface;
use SM\Sales\Api\Data\SubOrderSearchResultsInterfaceFactory;
use SM\Sales\Api\ParentOrderRepositoryInterface;
use SM\Sales\Api\SubOrderRepositoryInterface;
use SM\Sales\Model\Order\ParentOrder;
use SM\Sales\Model\Order\SubOrder;
use SM\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use SM\Sales\Model\ResourceModel\Order\StatusHistory\Collection as StatusHistoryCollection;
use SM\Sales\Model\ResourceModel\Order\StatusHistory\CollectionFactory as StatusHistoryCollectionFactory;

/**
 * This Repository is only for testing
 *
 * Class SubOrderRepository
 * @package SM\Sales
 */
class SubOrderRepository implements SubOrderRepositoryInterface
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var SubOrderSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var SubOrder
     */
    protected $subOrder;

    /**
     * @var StatusCollectionFactory
     */
    protected $orderStatusCollectionFactory;

    /**
     * @var StatusHistoryCollectionFactory
     */
    protected $statusHistoryCollectionFactory;

    /**
     * @var Order\Status\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var ParentOrder
     */
    protected $parentOrder;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;


    /**
     * SubOrderRepository constructor.
     * @param SubOrder $subOrder
     * @param OrderRepository $orderRepository
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param SubOrderSearchResultsInterfaceFactory $searchResultsFactory
     * @param StatusCollectionFactory $orderStatusCollectionFactory
     * @param StatusHistoryCollectionFactory $statusHistoryCollectionFactory
     * @param Order\Status\HistoryFactory $historyFactory
     * @param ParentOrder $parentOrder
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        SubOrder $subOrder,
        OrderRepository $orderRepository,
        OrderCollectionFactory $orderCollectionFactory,
        SubOrderSearchResultsInterfaceFactory $searchResultsFactory,
        StatusCollectionFactory $orderStatusCollectionFactory,
        StatusHistoryCollectionFactory $statusHistoryCollectionFactory,
        Order\Status\HistoryFactory $historyFactory,
        ParentOrder $parentOrder,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->parentOrder = $parentOrder;
        $this->historyFactory = $historyFactory;
        $this->statusHistoryCollectionFactory = $statusHistoryCollectionFactory;
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->subOrder = $subOrder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->urlInterface = $urlInterface;
    }

    /**
     * @param int $subOrderId
     * @return ParentOrderDataInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getById($subOrderId)
    {
        /** @var OrderCollection $subOrderCollection */
        $subOrderCollection = $this->orderCollectionFactory->create();
        $subOrderCollection->selectSub();
        $subOrderCollection->addFieldToFilter(OrderInterface::ENTITY_ID, $subOrderId);

        if (!$subOrderCollection->getSize()) {
            throw new NoSuchEntityException(__("Order ID %1 is not exists", $subOrderId));
        }

        $parentOrderId = $subOrderCollection->getFirstItem()->getParentOrder();

        if (!$parentOrderId) {
            throw new NoSuchEntityException(__("Order ID %1 is not exists", $subOrderId));
        }

        /** @var Order $parentOrderModel */
        $parentOrderModel = $this->getParentOrder($parentOrderId, $subOrderId);
        $hasInvoice = (bool) $parentOrderModel->getReferenceInvoiceNumber();
        $result = $this->subOrder->handleSubOrders($subOrderCollection, $cancelType, $hasInvoice);
        $subOrders = $result->getData("sub_orders");

        return $this->parentOrder->parentOrderProcess(
            $parentOrderModel,
            $subOrders[$parentOrderId]??[],
            $hasInvoice
        );
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @param int $customerId
     * @return SubOrderSearchResultsInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getList(SearchCriteria $searchCriteria, $customerId)
    {
        /** @var SubOrderSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var OrderCollection $subOrderCollection */
        $subOrderCollection = $this->orderCollectionFactory
            ->create()
            ->selectSub()
            ->addFieldToFilter("customer_id", $customerId)
            ->itemFilter($searchCriteria)
            ->itemSort($searchCriteria);
        $subOrderCollection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());
        $result = $this->subOrder->handleSubOrders($subOrderCollection, $cancelType);
        $list = $result->getData("sub_orders");
        $subOrderResults = [];

        /** @var SubOrderDataInterface[] $subOrders */
        foreach ($list as $subOrders) {
            /** @var SubOrderDataInterface $subOrder */
            foreach ($subOrders as $subOrder) {
                if ($subOrder->getSubOrderId() != null && $subOrder->getSubOrderId() != '') {
                    /** @var Order $parentOrderModel */
                    $parentOrderModel = $this->getParentOrder($subOrder->getParentOrder(), $subOrder->getId());
                    if ($parentOrderModel->getReferenceInvoiceNumber()) {
                        $subOrder->setInvoiceLink($this->subOrder->getInvoiceLink($subOrder->getParentOrder()));
                    }
                    $subOrderResults[] = $subOrder;
                }
            }
        }

        $searchResults->setTotalCount($subOrderCollection->getSize());
        $searchResults->setItems($subOrderResults);
        return $searchResults;
    }

    /**
     * @param $parentOrderId
     * @param $subOrderId
     * @return OrderInterface
     * @throws InputException
     * @throws NoSuchEntityException
     */
    protected function getParentOrder($parentOrderId, $subOrderId)
    {
        /** @var OrderCollection $orderCollection */
        try {
            return $this->orderRepository->get($parentOrderId);
        } catch (InputException $e) {
            throw new InputException(__($e->getMessage()));
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__("Order ID %1 is not exists", $subOrderId));
        }
    }

    /**
     * @param int $subOrderId
     * @return bool|void
     * @throws AlreadyExistsException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function setReceivedById($subOrderId)
    {
        try {
            $this->subOrder->updateStatusComplete($subOrderId);
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        /** @var StatusCollection $statusCollection */
        $statusCollection = $this->orderStatusCollectionFactory->create();
        $result = [];
        /** @var \Magento\Sales\Model\Order\Status $status */
        foreach ($statusCollection as $status) {
            $result[$status->getStatus()] = $status->getLabel();
        }
        return json_encode($result);
    }

    /**
     * Function for testing
     *
     * @param int $orderId
     * @return bool
     * @throws \Exception
     */
    public function resetStatus($orderId)
    {
        /** @var StatusHistoryCollection $historyCollection */
        $historyCollection = $this->statusHistoryCollectionFactory->create();
        $historyCollection->addFieldToFilter(OrderStatusHistoryInterface::PARENT_ID, $orderId);
        $historyCollection->walk("delete");

        $statusHistory = $this->historyFactory->create();
        $statusHistory->setStatus(ParentOrderRepositoryInterface::STATUS_PENDING_PAYMENT);
        $statusHistory->setParentId($orderId);
        $statusHistory->setEntityName("order");
        $statusHistory->save();

        $order = $this->orderRepository->get($orderId);
        $order->setStatus(ParentOrderRepositoryInterface::STATUS_PENDING_PAYMENT);
        $this->orderRepository->save($order);

        return true;
    }
}
