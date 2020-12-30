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
use Magento\Framework\Stdlib\DateTime\DateTime;
use Trans\Sprint\Api\SprintResponseRepositoryInterface;
use SM\Sales\Observer\SetEmailNewOrderData;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Mepay\Logger\LoggerWrite;
use Trans\Mepay\Helper\Data;

class TransmartSetEmailNewOrderData extends SetEmailNewOrderData
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * @var TransactionHelper
     */
    protected $transactionHelper;

    /**
     * @LoggerWrite
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * SetEmailNewOrderData constructor.
     * @param Json $json
     * @param TransactionHelper $transactionHelper
     * @param LoggerWrite $logger
     * @param PriceHelper $priceHelper
     * @param UrlInterface $url
     * @param DateTime $date
     * @param SprintResponseRepositoryInterface $sprintResponseRepository
     */
    public function __construct(
        Json $json,
        TransactionHelper $transactionHelper,
        LoggerWrite $logger,
        PriceHelper $priceHelper,
        UrlInterface $url,
        DateTime $date,
        SprintResponseRepositoryInterface $sprintResponseRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->json = $json;
        $this->logger = $logger;
        $this->transactionHelper = $transactionHelper;
        $this->timezone = $timezone;
        parent::__construct($priceHelper, $url, $date, $sprintResponseRepository);
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
        if (Data::isMegaMethod($paymentMethod)) {
            $additionalData = [
                "order_increment" => $order->getReferenceNumber(),
                "order_total" => $this->getPrice($order->getGrandTotal()),
                "virtual_account_number" => $this->getBankMegaVaNumber($payment->getLastTransId()),
                "payment_method_title" => $paymentMethodTitle,
                "order_url" => $orderUrl,
                "is_va" => $this->verifyPayment($paymentMethod, "va"),
                "is_cc" => $this->verifyPayment($paymentMethod, "cc"),
                "is_store_pick_up" => $order->getShippingMethod() == "store_pickup_store_pickup",
                "delivery_method" => $this->getDeliveryMethod($order->getShippingMethod(), $order->getShippingDescription()),
                "expire_time" => '',
                "expire_time_string" => '',
                "is_va_mega" => ($paymentMethod == 'trans_mepay_va')? true : false,
                "is_va_bca" => false
            ];

            if($paymentMethod == 'trans_mepay_va'){
                $expireTime = $this->getBankMegaVaExpireTime($payment->getLastTransId());
                $expireTime = str_replace('T',' ', $expireTime);
                $expireTime = substr($expireTime, 0, strpos($expireTime, "."));
                if($expireTime) {
                    $dateTime = new \DateTime($expireTime, new \DateTimeZone('UTC'));
                    $expireTimeString = $this->timezone->date($dateTime)->format('d F Y h:i A');
                    $additionalData['expire_time_string'] = $expireTimeString;
                }

            }
        } else {
            $additionalData = [
                "order_increment" => $order->getReferenceNumber(),
                "order_total" => $this->getPrice($order->getGrandTotal()),
                "virtual_account_number" => $this->getPaycode($order->getQuoteId()),
                "payment_method_title" => $paymentMethodTitle,
                "order_url" => $orderUrl,
                "is_va" => $this->verifyPayment($paymentMethod, "va"),
                "is_cc" => $this->verifyPayment($paymentMethod, "cc"),
                "is_store_pick_up" => $order->getShippingMethod() == "store_pickup_store_pickup",
                "delivery_method" => $this->getDeliveryMethod($order->getShippingMethod(), $order->getShippingDescription()),
                "expire_time" => date("l", $this->getExpireTime($order->getQuoteId())),
                "expire_time_string" => $this->getExpireTimeString($order->getQuoteId()),
                "is_va_mega" => false,
                "is_va_bca" => ($paymentMethod == 'sprint_bca_va')? true : false
            ];
        }


        $transportObject->setData("additional_data", $additionalData);

        return $this;
    }

    /**
     * @param  int $txnId
     * @return collection
     */
    public function getBankMegaVa($txnId)
    {
        $result = null;
        if ($txnId) {
            $collection = $this->transactionHelper->getTxnByTxnId($txnId);
            $collection->getSelect()->limit(1)->order('transaction_id desc');
            $result =  $collection->getFirstItem();
        }
        return $result;
    }

    public function getStatusDataBankMegaVa($txnId)
    {
        $statusData = [];
        $data = $this->getBankMegaVa($txnId);
        if ($data && $data->getId()) {
            $transMepayTransaction = $data->getData('trans_mepay_transaction');
            if($transMepayTransaction && is_string($transMepayTransaction)){
                $transaction = json_decode($transMepayTransaction);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $transactionData = [$transaction];
                    if(is_array($transaction)){
                        $transactionData = $transaction;
                    }
                    elseif(is_object($transaction)){
                        $transactionData = (array) $transaction;
                    }

                    if(array_key_exists('statusData', $transactionData)) {
                        $statusDataTmp = $transactionData['statusData'];
                        if(is_object($statusDataTmp)){
                            $statusData = (array) $statusDataTmp;
                        }
                        elseif(is_array($statusDataTmp)){
                            $statusData = $statusDataTmp;
                        }
                    }
                }
            }
        }

        return $statusData;
    }

    /**
     * Get VA Number of Bank Mega VA
     *
     * @param  int $txnId
     * @return string
     */
    public function getBankMegaVaNumber($txnId)
    {
        $vaNumber = '';
        $statusData = $this->getStatusDataBankMegaVa($txnId);
        if(is_array($statusData) && !empty($statusData)){
            if(array_key_exists('vaNumber', $statusData) && $statusData['vaNumber']){
                $vaNumber = $statusData['vaNumber'];
            }
        }

        return $vaNumber;
    }

    /**
     * Get Expire Time of Bank Mega VA
     *
     * @param  int $txnId
     * @return string
     */
    public function getBankMegaVaExpireTime($txnId)
    {
        $expireTime = '';
        $statusData = $this->getStatusDataBankMegaVa($txnId);
        if(is_array($statusData) && !empty($statusData)){
            if(array_key_exists('expireTime', $statusData) && $statusData['expireTime']){
                $expireTime = $statusData['expireTime'];
            }
        }

        return $expireTime;
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
