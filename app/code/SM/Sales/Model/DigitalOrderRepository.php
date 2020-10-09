<?php

namespace SM\Sales\Model;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use SM\Sales\Api\Data\DetailItemDataInterfaceFactory;
use SM\Sales\Api\Data\DigitalOrderDataInterface;
use SM\Sales\Api\Data\DigitalOrderDataInterfaceFactory;
use SM\Sales\Api\Data\PaymentInfoDataInterface;
use SM\Sales\Api\DigitalOrderRepositoryInterface;
use SM\Sales\Model\Order\ParentOrder;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class DigitalOrderRepository
 * @package SM\Sales\Model
 */
class DigitalOrderRepository implements DigitalOrderRepositoryInterface
{
    /**
     * @var DetailItemDataInterfaceFactory
     */
    protected $itemDataFactory;

    /**
     * @var ParentOrder
     */
    protected $parentOrder;

    /**
     * @var DigitalOrderDataInterfaceFactory
     */
    protected $digitalOrderDataFactory;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * DigitalOrderRepository constructor.
     * @param DetailItemDataInterfaceFactory $itemDataFactory
     * @param ParentOrder $parentOrder
     * @param DigitalOrderDataInterfaceFactory $digitalOrderDataFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        DetailItemDataInterfaceFactory $itemDataFactory,
        ParentOrder $parentOrder,
        DigitalOrderDataInterfaceFactory $digitalOrderDataFactory,
        OrderCollectionFactory $orderCollectionFactory,
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->digitalOrderDataFactory = $digitalOrderDataFactory;
        $this->parentOrder = $parentOrder;
        $this->itemDataFactory = $itemDataFactory;
    }

    /**
     * @param int $orderId
     * @return \SM\Sales\Api\Data\DigitalOrderDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($orderId)
    {
        /** @var Order $order */
        $order = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter("entity_id", $orderId)
            ->addFieldToFilter("is_virtual", 1)
            ->getFirstItem();
        if ($order->getEntityId() && isset(array_values($order->getItems())[0])) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            $item = array_values($order->getItems())[0];

            /** @var DigitalOrderDataInterface $digitalOrderData */
            $digitalOrderData = $this->digitalOrderDataFactory->create();

            $payment = $order->getPayment();

            $itemBuyRequest = $item->getProductOptionByCode('info_buyRequest') ?? [];
            /** @var PaymentInfoDataInterface $paymentData */
            $paymentData = $this->parentOrder->paymentInfoProcess($payment, $order->getReferenceNumber());
            $digitalOrderData
                ->setParentOrderId($order->getEntityId())
                ->setReferenceNumber($order->getData(DigitalOrderDataInterface::REFERENCE_NUMBER))
                ->setReferenceOrderId($order->getData(DigitalOrderDataInterface::REFERENCE_ORDER_ID))
                ->setReferenceInvoiceNumber($order->getData(DigitalOrderDataInterface::REFERENCE_INVOICE_NUMBER))
                ->setProductName($item->getName())
                ->setSku($item->getSku())
                ->setPrice($item->getPrice())
                ->setBuyRequest(json_encode($itemBuyRequest))
                ->setCreatedAt($order->getCreatedAt())
                ->setUpdatedAt($order->getUpdatedAt())
                ->setSubtotal($order->getSubtotal())
                ->setGrandTotal($order->getGrandTotal())
                ->setStatus($order->getStatus())
                ->setStatusLabel($order->getStatusLabel())
                ->setPaymentMethod($payment->getMethodInstance()->getTitle())
                ->setPaymentInfo($paymentData)
                ->setProductOption($item->getProductOption());

            if ($order->getData(DigitalOrderDataInterface::REFERENCE_INVOICE_NUMBER)) {
                $digitalOrderData->setInvoiceLink(
                    $this->urlInterface->getUrl(
                        "sales/invoice/view",
                        ["id" => $order->getEntityId()]
                    )
                );
            }

            $this->parentOrder->voucherDetailProcess($digitalOrderData, $order);

            if (isset($itemBuyRequest["service_type"])) {
                $serviceType = $itemBuyRequest["service_type"];
                if ($serviceType == \SM\DigitalProduct\Helper\Category\Data::ELECTRICITY_TOKEN_VALUE) {
                    $digitalOrderData->setPrice($this->denomProcess($item));
                }
            }

            return $digitalOrderData;
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return array|int|string|null
     */
    private function denomProcess($item)
    {
        $product = $item->getProduct();
        if ($product->getAttributeText("denom")) {
            return $product->getAttributeText("denom");
        }
        return 0;
    }
}
