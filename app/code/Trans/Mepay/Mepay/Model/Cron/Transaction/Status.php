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
namespace Trans\Mepay\Model\Cron\Transaction;

use Magento\Framework\Serialize\Serializer\Json;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Helper\Payment\Transaction;
use Trans\Mepay\Gateway\Http\GetTransactionStatus;
use Trans\Mepay\Gateway\Http\Client\Connect;

class Status 
{
    /**
     * @var string
     */
    const INQUIRY_ID = 'id';

    /**
     * @var \Trans\Mepay\Model\Config\Config
     */
    protected $config;

    /**
     * @var \Trans\Mepay\Helper\Payment\Transaction
     */
    protected $transaction;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Trans\Mepay\Gateway\Http\GetTransactionStatus
     */
    protected $transfer;

  /**
   * @var \Trans\Mepay\Gateway\Http\Client\Connect
   */
  protected $connector;

    /**
     * Constructor
     * @param Json $json
     * @param Config $config
     * @param Transaction $transaction
     * @param GetTransactionStatus $transfer
     * @param Connect $connector
     */
    public function __construct(
        Json $json,
        Config $config,
        Transaction $transaction,
        GetTransactionStatus $transfer,
        Connect $connector
    ){
        $this->json = $json;
        $this->config = $config;
        $this->transaction = $transaction;
        $this->transfer = $transfer;
        $this->connector = $connector;
    }

    /**
     * Check order transaction status by orderId
     * @param int $orderId
     * @return array
     */
    public function checkOrderTransactionStatusById($orderId)
    {
        $inquiry = $this->getInquiryByOrderId($orderId);
        return $this->send($inquiry[self::INQUIRY_ID]);
    }

    /**
     * Check order transaction status by order
     * @param  \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    public function checkOrderTransactionStatus($order)
    {
        return $this->checkOrderTransactionStatusById($order->getId());
    }

    /**
     * Check transaction status to PG
     * @param string $inquiryId
     * @return array
     */
    public function send($inquiryId)
    {
        try {
            $transfer = $this->transfer->create($inquiryId);
            return $this->connector->placeRequest($transfer);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Inquiry by Order id
     * @param  int $orderId
     * @return array
     */
    public function getInquiryByOrderId($orderId)
    {
        $inquiry = [];
        if ($transaction = $this->transaction->getPgResponse($orderId)){
            $inquiry = $this->json->unserialize($transaction);
            if (\json_last_error() == JSON_ERROR_NONE) {
                return $inquiry;
            }
        }
        return $inquiry;
    }
}