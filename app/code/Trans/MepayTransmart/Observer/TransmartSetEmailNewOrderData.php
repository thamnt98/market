<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Serialize\Serializer\Json;
use Trans\Sprint\Api\SprintResponseRepositoryInterface;
use SM\Sales\Observer\SetEmailNewOrderData;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Mepay\Logger\LoggerWrite;
use Trans\Mepay\Helper\Data;

class TransmartSetEmailNewOrderData extends SetEmailNewOrderData
{
    protected $json;
    protected $transactionHelper;
    protected $logger;

    /**
     * SetEmailNewOrderData constructor.
     * @param PriceHelper $priceHelper
     * @param UrlInterface $url
     * @param SprintResponseRepositoryInterface $sprintResponseRepository
     */
    public function __construct(
        Json $json,
        TransactionHelper $transactionHelper,
        LoggerWrite $logger,
        PriceHelper $priceHelper,
        UrlInterface $url,
        SprintResponseRepositoryInterface $sprintResponseRepository
    ) {
        $this->json = $json;
        $this->logger = $logger;
        $this->transactionHelper = $transactionHelper;
        parent::__construct($priceHelper, $url, $sprintResponseRepository);
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
            "order_total" => $this->getPrice($order->getGrandTotal()),
            "virtual_account_number" => (Data::isMegaMethod($paymentMethod))? $this->getBankMegaVaNumber($payment->getLastTransId()) : $this->getPaycode($order->getQuoteId()),
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
     * @param  int $txnId
     * @return collection
     */
    public function getBankMegaVa($txnId)
    {
      $collection = $this->transactionHelper->getTxnByTxnId($txnId);
      $collection->getSelect()->limit(1)->order('transaction_id desc');
      return $collection->getFirstItem();
    }

    /**
     * @param  int $txnId
     * @return string
     */
    public function getBankMegaVaNumber($txnId)
    {
      $data = $this->getBankMegaVa($txnId);
      $transaction = $data->getData();
      return isset($transaction['statusData']['vaNumber'])? $transaction['statusData']['vaNumber'] : '';
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
}