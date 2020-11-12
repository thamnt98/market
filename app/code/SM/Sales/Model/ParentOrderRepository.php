<?php
/**
 * @category Magento
 * @package SM\Sales\Model
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model;

use Magento\Catalog\Helper\Image;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use SM\Sales\Api\Data\ParentOrderDataInterface;
use SM\Sales\Api\Data\ParentOrderSearchResultsInterface;
use SM\Sales\Api\Data\ParentOrderSearchResultsInterfaceFactory;
use SM\Sales\Api\Data\ReorderQuickly\OrderDataInterface;
use SM\Sales\Api\Data\ReorderQuickly\OrderDataInterfaceFactory;
use SM\Sales\Api\ParentOrderRepositoryInterface;
use SM\Sales\Model\Order\ParentOrder;
use SM\Sales\Model\Order\SubOrder;
use SM\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use SM\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use SM\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use SM\Sales\Model\Data\HandleOrderStatusHistory;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class ParentOrderRepository
 * @package SM\Sales\Model
 */
class ParentOrderRepository implements ParentOrderRepositoryInterface
{
    const IN_PROGRESS = "in-progress";
    const COMPLETED = "completed";
    const LIST_TYPE = "list_type";

    const SORT_LATEST = "sort_latest";
    const SORT_STATUS = "sort_status";

    const OPTION_TYPE_BUNDLE = "bundle";
    const OPTION_TYPE_CONFIGURABLE = "configurable";

    const WEB_KEY = 'web';
    const MB_KEY = 'mb';

    const DEFAULT_PAGE_SIZE = 3;

    const CANCELLED_DIFFERENT_TYPE = 99;
    const CANCELLED_DEFAULT = 0;

    const MAP_MESSAGE_TYPE_WEB = [
        self::CANCELLED_DEFAULT  => null,
        self::CANCELLED_DIFFERENT_TYPE  => 'Your order has been cancelled for several reasons. Please see details to get more info.',
        HandleOrderStatusHistory::CANCEL_BY_PAYMENT => 'Your order has been cancelled for going past the payment due time.',
        HandleOrderStatusHistory::CANCEL_BY_DELIVERY => 'Your order has been cancelled due to unsuccessful delivery. <a href="%1">Contact Us</a> for more info.',
        HandleOrderStatusHistory::CANCEL_BY_PICKUP => 'Your order has been cancelled for going past the pick-up due time.',
    ];

    const MAP_MESSAGE_TYPE_MOBILE = [
        self::CANCELLED_DEFAULT  => null,
        self::CANCELLED_DIFFERENT_TYPE  => 'Cancelled for several reasons. Please see details.',
        HandleOrderStatusHistory::CANCEL_BY_PAYMENT => 'Cancelled for going past the payment due time.',
        HandleOrderStatusHistory::CANCEL_BY_DELIVERY => 'Cancelled due to unsuccessful delivery.',
        HandleOrderStatusHistory::CANCEL_BY_PICKUP => 'Cancelled for going past the pick-up due time.',
    ];

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;
    /**
     * @var ParentOrderSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var OrderCollection
     */
    protected $orderCollection;

    /**
     * @var SubOrder
     */
    protected $subOrder;

    /**
     * @var ParentOrder
     */
    protected $parentOrder;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var OrderDataInterfaceFactory
     */
    protected $orderDataFactory;

    /**
     * @var Emulation
     */
    protected $appEmulation;
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $tokenUserContext;

    /**
     * ParentOrderRepository constructor.
     * @param ParentOrder $parentOrder
     * @param SubOrder $subOrder
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param ParentOrderSearchResultsInterfaceFactory $searchResultsFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderDataInterfaceFactory $orderDataFactory
     * @param Emulation $appEmulation
     * @param Image $imageHelper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Webapi\Model\Authorization\TokenUserContext $tokenUserContext
     */
    public function __construct(
        ParentOrder $parentOrder,
        SubOrder $subOrder,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ParentOrderSearchResultsInterfaceFactory $searchResultsFactory,
        OrderCollectionFactory $orderCollectionFactory,
        OrderDataInterfaceFactory $orderDataFactory,
        Emulation $appEmulation,
        Image $imageHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->appEmulation = $appEmulation;
        $this->imageHelper = $imageHelper;
        $this->orderDataFactory = $orderDataFactory;
        $this->parentOrder = $parentOrder;
        $this->subOrder = $subOrder;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
    }

    /**
     * @return OrderCollection
     */
    public function getOrderCollection()
    {
        return $this->orderCollection;
    }

    /**
     * @param OrderCollection $orderCollection
     * @return ParentOrderRepository
     */
    public function setOrderCollection($orderCollection)
    {
        $this->orderCollection = $orderCollection;
        return $this;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return ParentOrderSearchResultsInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $customerId)
    {
        /** @var ParentOrderSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var ParentOrderDataInterface[] $list */
        $list = $this->getListCollection($searchCriteria, $customerId);

        $searchResults->setItems($list);
        $searchResults->setTotalCount($this->getOrderCollection()->getSize());
        return $searchResults;
    }

    /**
     * @param int $orderId
     * @param int $customerId
     * @return ParentOrderDataInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId, $orderId)
    {
        /** @var OrderCollection $orderCollection */
        $subOrderCollection = $this->orderCollectionFactory
            ->create()
            ->selectSub()
            ->addFieldToFilter([
                "main_table.customer_id",
                "main_table.parent_order",
                "main_table.entity_id"
            ], [
                ["eq" => $customerId],
                ["eq" => $orderId]
            ]);

        /** @var Order $parentOrder */
        $parentOrder = $this->orderCollectionFactory
            ->create()
            ->selectParent()
            ->addFieldToFilter(OrderInterface::CUSTOMER_ID, $customerId)
            ->addFieldToFilter(OrderInterface::ENTITY_ID, $orderId)
            ->getFirstItem();

        if (empty($subOrderCollection->getSize()) && empty($parentOrder->getData())) {
            return null;
        }

        $hasInvoice = (bool) $parentOrder->getReferenceInvoiceNumber();
        $result = $this->subOrder->handleSubOrders($subOrderCollection, $cancelType, $hasInvoice);
        $subOrders = $result->getData("sub_orders");

        $orderData = $this->parentOrder->parentOrderProcess(
            $parentOrder,
            $subOrders[$parentOrder->getEntityId()]??[]
        );

        if (!empty($cancelType) && $orderData->getStatus() == ParentOrderRepositoryInterface::STATUS_ORDER_CANCELED) {
            $this->setCancelMessage($cancelType, $orderData);
        }
        if ($orderData->getPaymentInfo()) {
            $transactionId = $orderData->getPaymentInfo()->getTransactionId();
            $orderData->setTransactionId($transactionId ? $transactionId : null);
        }

        return $orderData;
    }

    /**
     * @param $cancelType
     * @param $orderData
     */
    private function setCancelMessage($cancelType, $orderData)
    {
        $cancelMessage = $this->getCancelMessages($cancelType);
        $orderData->setCancelMessage($cancelMessage[self::WEB_KEY]);
        $orderData->setCancelMessageMobile($cancelMessage[self::MB_KEY]);
    }

    /**
     * @param $cancelType
     * @return array|null[]
     */
    private function getCancelMessages($cancelType)
    {
        foreach ($cancelType as $type) {
            if (count(array_keys($cancelType, $type)) == count($cancelType)) {
                return $this->mapCancelType($type);
            } else {
                return $this->mapCancelType(self::CANCELLED_DIFFERENT_TYPE);
            }
        }

        return $this->mapCancelType(self::CANCELLED_DEFAULT);
    }

    /**
     * @param $type
     * @return array
     */
    private function mapCancelType($type)
    {
        return [
            self::WEB_KEY => __(self::MAP_MESSAGE_TYPE_WEB[$type], $this->parentOrder->getUrl('help/contactus/')),
            self::MB_KEY => __(self::MAP_MESSAGE_TYPE_MOBILE[$type])
        ];
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return OrderItemCollection
     */
    private function getItemResults(SearchCriteriaInterface $searchCriteria, $customerId)
    {
        /** @var OrderItemCollection $orderItemCollection */
        $orderItemCollection = $this->orderItemCollectionFactory->create();
        $orderItemCollection->selectToSearch();
        $orderItemCollection->addFieldToFilter("sub_order.customer_id", $customerId);
        $orderItemCollection->itemFilter($searchCriteria);
        return $orderItemCollection;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getListCollection(SearchCriteriaInterface $searchCriteria, $customerId)
    {
        $itemResults = $this->getItemResults($searchCriteria, $customerId);

        $parentOrderIds = $this->subOrder->itemProcess($itemResults);
        $listParentOrders = array_unique($parentOrderIds);

        /** @var OrderCollection $orderCollection */
        $orderCollection = $this->orderCollectionFactory
            ->create()
            ->selectParent()
            ->itemSort($searchCriteria)
            ->addFieldToFilter("main_table.entity_id", ["in" => $listParentOrders]);

        $orderCollection
            ->setCurPage(
                $searchCriteria->getCurrentPage()
            )
            ->setPageSize(
                $searchCriteria->getPageSize()
            );

        $this->setOrderCollection($orderCollection);
        $mainOrderIds = $this->getMainOrderIds($orderCollection);

        /** @var OrderCollection $orderCollection */
        $subOrderCollection = $this->orderCollectionFactory
            ->create()
            ->selectSub()
            ->itemSort($searchCriteria)
            ->addFieldToFilter(
                [
                "main_table.parent_order",
                "main_table.entity_id"
                ],
                [
                    ["in" => $mainOrderIds],
                    ["in" => $mainOrderIds]
                ]
            );

        $result = $this->subOrder->handleSubOrders($subOrderCollection, $cancelType);
        $subOrders = $result->getData("sub_orders");
        $orderResults = [];
        /** @var Order $parentOrder */
        foreach ($orderCollection as $parentOrder) {
            $orderData = $this->parentOrder->parentOrderProcess(
                $parentOrder,
                $subOrders[$parentOrder->getEntityId()]??[]
            );

            if (!empty($cancelType)
                && $orderData->getStatus() == ParentOrderRepositoryInterface::STATUS_ORDER_CANCELED) {
                $this->setCancelMessage($cancelType, $orderData);
            }

            $transactionId = $orderData->getPaymentInfo()->getTransactionId();
            $orderData->setTransactionId($transactionId ? $transactionId : null);

            $orderResults[] = $orderData;
        }

        return $orderResults;
    }

    /**
     * @param OrderCollection $orderCollection
     * @return int[]
     */
    public function getMainOrderIds($orderCollection)
    {
        $mainOrderIds = [];
        foreach ($orderCollection as $mainOrder) {
            array_push($mainOrderIds, $mainOrder->getEntityId());
        }
        return $mainOrderIds;
    }

    /**
     * @param int $customerId
     * @return OrderDataInterface[]
     * @throws NoSuchEntityException
     */
    public function getListReorderQuickly($customerId)
    {
        $this->appEmulation->startEnvironmentEmulation(
            $this->storeManager->getStore()->getId(),
            Area::AREA_FRONTEND,
            true
        );

        /** @var OrderCollection $orderCollection */
        $orderCollection = $this->orderCollectionFactory
            ->create()
            ->selectReorderQuickly()
            ->addFieldToFilter(OrderInterface::CUSTOMER_ID, $customerId)
            ->addFieldToFilter(OrderInterface::STATUS, self::STATUS_COMPLETE)
            ->setOrder(OrderInterface::CREATED_AT, OrderCollection::SORT_ORDER_DESC)
            ->setPageSize(self::DEFAULT_PAGE_SIZE);

        $results = [];

        /** @var Order $order */
        foreach ($orderCollection as $order) {
            /** @var OrderDataInterface $orderData */
            $orderData = $this->orderDataFactory->create();
            $orderData
                ->setCreatedAt($order->getCreatedAt())
                ->setGrandTotal($order->getGrandTotal())
                ->setEntityId($order->getEntityId());

            if (!is_null($order->getPayment()) && $order->getPayment()->getLastTransId()) {
                $orderData->setTransactionId($order->getPayment()->getLastTransId());
            }
            $itemImages = [];
            $countItem = 0;
            $left = 0;
            foreach ($order->getItemsCollection() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }

                $countItem++;
                if ($countItem <= 4) {
                    $itemImages[] = $this->imageHelper->init(
                        $item->getProduct(),
                        'product_base_image'
                    )->getUrl();
                } else {
                    $left++;
                }
            }
            $orderData->setItemImages($itemImages);
            $orderData->setItemLeft($left);
            $results[] = $orderData;
        }

        $this->appEmulation->stopEnvironmentEmulation();
        return $results;
    }
}
