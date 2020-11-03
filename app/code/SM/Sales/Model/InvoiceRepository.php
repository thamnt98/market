<?php

namespace SM\Sales\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use SM\Sales\Api\Data\DeliveryAddressDataInterface;
use SM\Sales\Api\Data\DeliveryAddressDataInterfaceFactory;
use SM\Sales\Api\Data\Invoice\InvoiceInterface;
use SM\Sales\Api\Data\Invoice\InvoiceInterfaceFactory;
use SM\Sales\Api\Data\Invoice\SubInvoiceInterface;
use SM\Sales\Api\Data\Invoice\SubInvoiceInterfaceFactory;
use SM\Sales\Api\Data\Invoice\SubInvoiceItemInterface;
use SM\Sales\Api\Data\Invoice\SubInvoiceItemInterfaceFactory;
use SM\Sales\Api\Data\PaymentInfoDataInterface;
use SM\Sales\Model\Order\ParentOrder;
use SM\Sales\Model\Order\SubOrder;
use SM\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class InvoiceRepository
 * @package SM\Sales\Model
 */
class InvoiceRepository implements \SM\Sales\Api\InvoiceRepositoryInterface
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var OrderCollection
     */
    protected $orderCollectionFactory;

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var SubInvoiceItemInterfaceFactory
     */
    protected $itemDataFactory;

    /**
     * @var SubInvoiceInterfaceFactory
     */
    protected $subInvoiceDataFactory;

    /**
     * @var InvoiceInterfaceFactory
     */
    protected $invoiceDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ParentOrder
     */
    protected $parentOrder;

    /**
     * @var SubOrder
     */
    protected $subOrder;

    /**
     * @var DeliveryAddressDataInterfaceFactory
     */
    protected $deliveryDataFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     *
     * @param DateTime $date
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param SubInvoiceItemInterfaceFactory $subInvoiceItemDataFactory
     * @param SubInvoiceInterfaceFactory $subInvoiceDataFactory
     * @param InvoiceInterfaceFactory $invoiceDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param ParentOrder $parentOrder
     * @param SubOrder $subOrder
     * @param BlockFactory $blockFactory
     * @param PageFactory $resultPageFactory
     * @param DeliveryAddressDataInterfaceFactory $deliveryDataFactory
     */

    public function __construct(
        DateTime $date,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        SubInvoiceItemInterfaceFactory $subInvoiceItemDataFactory,
        SubInvoiceInterfaceFactory $subInvoiceDataFactory,
        InvoiceInterfaceFactory $invoiceDataFactory,
        DataObjectHelper $dataObjectHelper,
        OrderCollectionFactory $orderCollectionFactory,
        ParentOrder $parentOrder,
        SubOrder $subOrder,
        BlockFactory $blockFactory,
        PageFactory $resultPageFactory,
        DirectoryList $directoryList,
        DeliveryAddressDataInterfaceFactory $deliveryDataFactory
    ) {
        $this->deliveryDataFactory = $deliveryDataFactory;
        $this->subOrder = $subOrder;
        $this->parentOrder = $parentOrder;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->subInvoiceDataFactory = $subInvoiceDataFactory;
        $this->itemDataFactory = $subInvoiceItemDataFactory;
        $this->invoiceDataFactory = $invoiceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->date = $date;
        $this->blockFactory = $blockFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->directoryList = $directoryList;
    }

    /**
     * @param int $orderId
     * @param int $customerId
     * @return string
     * @throws NoSuchEntityException
     * @throws \Mpdf\MpdfException
     */
    public function getById($customerId, $orderId)
    {
        $data = $this->getDataInvoice($customerId, $orderId);
        return $this->getHTML($data);
    }

    /**
     * @param int $orderId
     * @param int $customerId
     * @return InvoiceInterface
     * @throws NoSuchEntityException
     */
    public function getDataInvoice($customerId, $orderId)
    {
        $mainOrder = $this->getMainOrder($customerId, $orderId);
        $subOrderCollection = $this->getSubOrderCollection($customerId, $orderId);

        $orderIds = $this->getOrderIds($subOrderCollection);
        $orderItemCollection = $this->getOrderItemCollection($orderIds);
        $orderItems = $this->populateOrderItems($orderItemCollection);

        $sourceInformation = $this->subOrder->getStoreInformation($subOrderCollection);
        $subInvoices = $this->prepareSubInvoices($subOrderCollection, $orderItems, $sourceInformation);

        $paymentInfo = $this->getPaymentInfo($mainOrder);
        return $this->populateMainInvoice($mainOrder, $subInvoices, $paymentInfo);
    }

    public function isDigital($mainOrder)
    {
        return $mainOrder->getIsVirtual();
    }

    /**
     * @param string $date
     * @return string
     */
    public function convertMonth($date)
    {
        return $this->date->date('d F Y', $date);
    }

    /**
     * @param int $mainOrderId
     * @param int $customerId
     * @return Order
     * @throws NoSuchEntityException
     */
    protected function getMainOrder($customerId, $mainOrderId)
    {
        /** @var Order $mainOrder */
        $mainOrder = $this->orderCollectionFactory->create()
            ->addFieldToSelect([
                "reference_invoice_number",
                "increment_id",
                "is_virtual",
                "reference_number",
                "subtotal",
                "shipping_amount",
                "discount_amount",
                "grand_total",
                "is_virtual",
                "quote_id"
            ])
            ->join(
                "sales_invoice",
                "main_table.entity_id = sales_invoice.order_id",
                [
                    "created_at"
                ]
            )
            ->join(
                "quote",
                "quote.entity_id = main_table.quote_id",
                [
                    "service_fee"
                ]
            )
            ->addFieldToFilter("main_table.customer_id", $customerId)
            ->addFieldToFilter("main_table.entity_id", $mainOrderId)
            ->getFirstItem();

        if (!$mainOrder->getId()) {
            throw new NoSuchEntityException(__("Order with ID %1 does not have any invoice", $mainOrderId));
        }

        return $mainOrder;
    }

    /**
     * @param int $customerId
     * @param int $mainOrderId
     * @return OrderCollection
     */
    protected function getSubOrderCollection($customerId, $mainOrderId)
    {
        /** @var OrderCollection $orderCollection */
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection
            ->selectSub()
            ->addFieldToSelect("discount_amount")
            ->addFieldToFilter("customer_id", $customerId)
            ->addFieldToFilter("parent_order", $mainOrderId);
        return $orderCollection;
    }

    /**
     * @param OrderCollection $orderCollection
     * @return int[]
     */
    private function getOrderIds($orderCollection)
    {
        $orderIds = [];
        /** @var Order $order */
        foreach ($orderCollection as $order) {
            $orderIds[] = $order->getEntityId();
        }
        return $orderIds;
    }

    /**
     * @param int[] $orderIds
     * @return OrderItemCollection
     */
    private function getOrderItemCollection($orderIds)
    {
        /** @var OrderItemCollection $orderItemCollection */
        $orderItemCollection = $this->orderItemCollectionFactory->create();
        $orderItemCollection
            ->addFieldToSelect([
                "order_id",
                "name",
                "qty" => "qty_ordered",
                "row_total"
            ])
            ->addFieldToFilter("order_id", ["in" => $orderIds]);
        return $orderItemCollection;
    }

    /**
     * @param OrderItemCollection $orderItemCollection
     * @return SubInvoiceItemInterface[][]
     */
    private function populateOrderItems($orderItemCollection)
    {
        $orderItems = [];
        /** @var Order\Item $item */
        foreach ($orderItemCollection as $item) {
            /** @var SubInvoiceItemInterface $itemData */
            $itemData = $this->itemDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $itemData,
                $item->getData(),
                SubInvoiceItemInterface::class
            );
            $orderItems[$item->getOrderId()][] = $itemData;
        }
        return $orderItems;
    }

    /**
     * @param OrderCollection $orderCollection
     * @param SubInvoiceItemInterface[][] $orderItems
     * @param \Magento\Framework\DataObject[]|SourceInterface[] $sourceInformation
     * @return SubInvoiceInterface[]
     */
    private function prepareSubInvoices($orderCollection, $orderItems, $sourceInformation)
    {
        $invoices = [];
        /** @var  $order */
        foreach ($orderCollection as $order) {
            $subInvoiceData = $this->populateSubInvoice($order, $orderItems);

            if ($this->subOrder->isStorePickUp($order)) {
                $subInvoiceData->setStoreInfo($this->subOrder->getStoreInfo($order, $sourceInformation));
            } else {
                $subInvoiceData->setDeliveryAddress($this->getDeliveryData($order));
            }

            $invoices[] = $subInvoiceData;
        }
        return $invoices;
    }

    /**
     * @param Order $orderModel
     * @param SubInvoiceItemInterface[][] $orderItems
     * @return SubInvoiceInterface
     */
    private function populateSubInvoice($orderModel, $orderItems)
    {
        /** @var SubInvoiceInterface $subInvoiceData */
        $subInvoiceData = $this->subInvoiceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $subInvoiceData,
            $orderModel->getData(),
            SubInvoiceInterface::class
        );

        if (isset($orderItems[$orderModel->getEntityId()])) {
            $items = $orderItems[$orderModel->getEntityId()];
            $qty = $this->calculateQty($items);
            $subInvoiceData
                ->setItemAmount($qty)
                ->setItems($items);
        }
        return $subInvoiceData;
    }

    /**
     * @param Order $mainOrder
     * @param SubInvoiceInterface[] $subInvoices
     * @param PaymentInfoDataInterface $paymentInfo
     * @return InvoiceInterface
     */
    private function populateMainInvoice($mainOrder, $subInvoices, $paymentInfo)
    {
        /** @var InvoiceInterface $invoiceData */
        $invoiceData = $this->invoiceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $invoiceData,
            $mainOrder->getData(),
            InvoiceInterface::class
        );
        $invoiceData
            ->setIsDigital($mainOrder->getIsVirtual())
            ->setPaymentMethod($mainOrder->getPayment()->getMethodInstance()->getTitle())
            ->setShippingAmount($mainOrder->getShippingAmount())
            ->setCreatedAt($this->convertMonth($invoiceData->getCreatedAt()))
            ->setPaymentInfo($paymentInfo)
            ->setSubInvoices($subInvoices);
        return $invoiceData;
    }

    /**
     * @param SubInvoiceItemInterface[] $items
     * @return int
     */
    private function calculateQty($items)
    {
        $qty = 0;
        foreach ($items as $item) {
            $qty += $item->getQty();
        }
        return $qty;
    }

    /**
     * @param Order $mainOrder
     * @return \SM\Sales\Api\Data\PaymentInfoDataInterface|null
     */
    private function getPaymentInfo($mainOrder)
    {
        $payment = $mainOrder->getPayment();
        if ($payment) {
            return $this->parentOrder->paymentInfoProcess($payment, $mainOrder->getReferenceNumber());
        }
        return null;
    }

    /**
     * @param Order\Invoice $invoice
     * @return DeliveryAddressDataInterface
     */
    private function getDeliveryData($subOrderModel)
    {
        $street = str_replace(PHP_EOL, ",", $subOrderModel->getData("street"));

        /** @var DeliveryAddressDataInterface $deliveryData */
        $deliveryData = $this->deliveryDataFactory->create();
        $deliveryData->setFullName($this->subOrder->getFullName($subOrderModel));
        $deliveryData->setStreet($street);
        $deliveryData->setCountry($subOrderModel->getData("district"));
        $deliveryData->setCity($subOrderModel->getData("city"));
        $deliveryData->setAddressName($subOrderModel->getData("address_tag"));
        $deliveryData->setTelephone($subOrderModel->getData("telephone"));
        return $deliveryData;
    }

    /**
     * @param \SM\Sales\Api\Data\Invoice\InvoiceInterface $invoice
     * @return string
     */
    protected function getHTML($invoice)
    {
        $rootPath  =  $this->directoryList->getRoot();
        $filepath = $rootPath . '/app/code/SM/Sales/view/frontend/web/css/invoice.css';
        $styleContent = file_get_contents($filepath);
        $style ='<style> ' . $styleContent . ' </style>';
        /** @var \SM\Sales\Block\Invoice\Content $contentBlock */
        $contentBlock = $this->blockFactory->createBlock("SM\Sales\Block\Invoice\Content");
        $contentBlock->setTemplate("SM_Sales::invoice/content.phtml");
        $contentBlock->setInvoice($invoice);
        return $this->setPageInvoice($contentBlock->toHtml(), $style);
    }

    public function setPageInvoice($content, $style)
    {
        return '<html><head>'
            . $style
            . '</head><body>'
            . $content
            . '</body></html>';
    }
}
