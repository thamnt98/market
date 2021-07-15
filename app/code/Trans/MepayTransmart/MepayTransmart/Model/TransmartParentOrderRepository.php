<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Model;

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
use SM\Sales\Model\ParentOrderRepository;
use Trans\Mepay\Helper\Data as MepayHelper;
use Magento\Sales\Api\OrderRepositoryInterface;

class TransmartParentOrderRepository extends ParentOrderRepository
{

    protected $orderRepo;

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
     * @param \SM\MobileApi\Model\Authorization\TokenUserContext $tokenUserContext
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
        StoreManagerInterface $storeManager,
        OrderRepositoryInterface $orderRepo,
        \SM\MobileApi\Model\Authorization\TokenUserContext $tokenUserContext
    ) {
        $this->orderRepo = $orderRepo;
        parent::__construct(
          $parentOrder,
          $subOrder,
          $orderItemCollectionFactory,
          $searchResultsFactory,
          $orderCollectionFactory,
          $orderDataFactory,
          $appEmulation,
          $imageHelper,
          $storeManager,
          $tokenUserContext
        );
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
        $order = $this->orderRepo->get($orderId);
        $method = $order->getPayment()->getMethod();
        if (MepayHelper::isMegaMethod($method)) {
          return $this->getByIdMega($customerId, $order);
        }
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
                ["eq" => $orderId],
                ["eq" => $orderId]
            ]);

        /** @var Order $parentOrder */
        $parentOrder = $this->orderCollectionFactory
            ->create()
            ->selectParent()
            ->addFieldToFilter(OrderInterface::CUSTOMER_ID, $customerId)
            ->addFieldToFilter(OrderInterface::ENTITY_ID, $orderId)
            ->getFirstItem();

        if (!empty($subOrderCollection->getSize()) && empty($parentOrder->getData())) {
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
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return ParentOrderDataInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getByIdMega($customerId, $order)
    {

       /** @var OrderCollection $orderCollection */
        $subOrderCollection = $this->orderCollectionFactory
            ->create()
            ->selectSub()
            ->addFieldToFilter([
                "main_table.customer_id",
                "main_table.reference_number",
                "main_table.entity_id"
            ], [
                ["eq" => $customerId],
                ["eq" => $order->getReferenceNumber()],
                ["eq" => $order->getId()]
            ]);

        /** @var Order $parentOrder */
        $parentOrder = $this->orderCollectionFactory
            ->create()
            ->selectParent()
            ->addFieldToFilter(OrderInterface::CUSTOMER_ID, $customerId)
            ->addFieldToFilter('reference_number', $order->getReferenceNumber())
            ->getFirstItem();

        if (!empty($subOrderCollection->getSize()) && empty($parentOrder->getData())) {
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

}
