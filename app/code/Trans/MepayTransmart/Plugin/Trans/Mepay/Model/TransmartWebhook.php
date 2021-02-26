<?php
/**
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 *
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Edi Suryadi <edi.suryadi@cdcorpdigital.com>
 *
 */

namespace Trans\MepayTransmart\Plugin\Trans\Mepay\Model;

class TransmartWebhook
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * @var \Trans\MepayTransmart\Observer\TransmartSetEmailNewOrderData
     */
    protected $transmartSetEmailNewOrderData;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Trans\MepayTransmart\Observer\TransmartSetEmailNewOrderData $transmartSetEmailNewOrderData
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderSender = $orderSender;
        $this->transmartSetEmailNewOrderData = $transmartSetEmailNewOrderData;
    }

    /**
     * Around Notif method
     *
     * @param  \Trans\Mepay\Model\Webhook $subject
     * @param  callable  $proceed
     * @param  string  $type
     * @param  object  $transaction
     * @param  object  $inquiry
     * @param  string  $token
     * @return array
     */
    public function aroundNotif(
        \Trans\Mepay\Model\Webhook $subject,
        callable $proceed,
        $type,
        $transaction = null,
        $inquiry = null,
        $token = null
    ){
        $result = $proceed($type, $transaction, $inquiry, $token);

        $orderId = $this->getOrderId($inquiry);
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        if($order && $order->getId()){
            $payment = $order->getPayment();
            $paymentMethod = $payment->getMethod();
            if($paymentMethod == 'trans_mepay_va'){
                $vaNumber = $this->transmartSetEmailNewOrderData->getBankMegaVaNumber($payment->getLastTransId());
                if($vaNumber){
                    //$this->orderSender->send($order, true);
                }
            }
        }

        return $result;
    }

    /**
     * Get Order ID from inquiry data
     *
     * @param array $inquiry
     * @return string|null
     */
    protected function getOrderId($inquiry)
    {
        $orderId = null;
        if($inquiry) {
            $inquiryData = [$inquiry];
            if(is_array($inquiry)){
                $inquiryData = $inquiry;
            }
            elseif(is_object($inquiry)){
                $inquiryData = $inquiry->getData();
            }

            if($inquiryData) {
                if (array_key_exists('order', $inquiryData)) {
                    $orderData = $inquiryData['order'];
                    $arrOrderData = [];
                    if(is_object($orderData)){
                        $arrOrderData = $orderData->getData();
                    }
                    elseif(is_array($orderData)){
                        $arrOrderData = $orderData;
                    }

                    if(!empty($arrOrderData)){
                        if(array_key_exists('id', $arrOrderData) && $arrOrderData['id']){
                            $orderId = $arrOrderData['id'];
                            if(strpos($orderId, '-') !== false) {
                                $orderIdTmp = explode('-', $orderId);
                                if(count($orderIdTmp) == 2){
                                    $orderId = $orderIdTmp[1];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $orderId;
    }
}
