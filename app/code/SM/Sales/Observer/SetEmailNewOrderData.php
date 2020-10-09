<?php

namespace SM\Sales\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Trans\Sprint\Api\SprintResponseRepositoryInterface;

/**
 * Class SetEmailNewOrderData
 * @package SM\Sales\Observer
 */
class SetEmailNewOrderData implements ObserverInterface
{
    /**
     * @var \Trans\Sprint\Api\SprintResponseRepositoryInterface
     */
    protected $sprintResponseRepository;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * SetEmailNewOrderData constructor.
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url,
        SprintResponseRepositoryInterface $sprintResponseRepository
    ) {
        $this->sprintResponseRepository = $sprintResponseRepository;
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var OrderSender $sender */
        $sender = $observer->getEvent()->getSender();

        /** @var DataObject $transportObject */
        $transportObject = $observer->getEvent()->getTransport();

        /** @var Order $order */
        $order = $transportObject->getData("order");

        if ($order->getIsVirtual()) {
            $orderUrl = $this->url->getUrl("sales/order/digital", ["id" => $order->getId()]);
        } else {
            $orderUrl = $this->url->getUrl("sales/order/physical", ["id" => $order->getId()]);
        }
        $payment = $order->getPayment();
        $paymentMethodTitle = $payment->getMethodInstance()->getTitle();
        $paymentMethod = $payment->getMethod();

        $additionalData = [
            "order_increment" => $order->getReferenceNumber(),
            "order_total" => (float)$order->getGrandTotal(),
            "virtual_account_number" => $this->getPaycode($order->getReferenceNumber()),
            "payment_method_title" => $paymentMethodTitle,
            "order_url" => $orderUrl,
            "is_va" => $this->verifyPayment($paymentMethod, "va"),
            "is_cc" => $this->verifyPayment($paymentMethod, "cc"),
            "is_store_pick_up" => $order->getShippingMethod() == "store_pickup_store_pickup",
            "delivery_method" => $this->getDeliveryMethod($order->getShippingMethod(), $order->getShippingDescription())
        ];

        $transportObject->setData("additional_data", $additionalData);

        return $this;
    }

    /**
     * @param string $paymentMethod
     * @param string $methodCodeShort
     * @return bool
     */
    private function verifyPayment($paymentMethod, $methodCodeShort)
    {
        $paymentMethodSplit = explode("_", $paymentMethod);

        if (is_array($paymentMethodSplit)) {
            $methodShort = $paymentMethodSplit[count($paymentMethodSplit) - 1];
            if ($methodShort == $methodCodeShort) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $shippingMethod
     * @param string $shippingDescription
     * @return \Magento\Framework\Phrase|mixed|string
     */
    private function getDeliveryMethod($shippingMethod, $shippingDescription)
    {
        if ($shippingMethod == "store_pickup_store_pickup") {
            return __("Pick Up in Store");
        } else {
            $shippingDescription = explode(" - ", $shippingDescription);
            if (isset($shippingDescription[1])) {
                return $shippingDescription[1];
            }
        }
        return __("Not available");
    }

    protected function getPayCode($order)
    {
        try {
            $sprintOrder = $this->sprintResponseRepository->getByTransactionNo($order);
            return $sprintOrder->getCustomerAccount();
        } catch (\Exception $e) {
        }
    }
}
