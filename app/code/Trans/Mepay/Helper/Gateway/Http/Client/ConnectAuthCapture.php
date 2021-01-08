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
namespace Trans\Mepay\Helper\Gateway\Http\Client;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Trans\Mepay\Api\Data\TransactionInterface;
use Trans\Mepay\Api\Data\InquiryInterface;
use Trans\Mepay\Gateway\Http\PostCaptureTransactionStatus;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Mepay\Model\Payment\Update\AuthCapture;

class ConnectAuthCapture extends AbstractHelper
{
  /**
   * @var string
   */
  const NEW_AMOUNT = 'newAmount';

  /**
   * @var string
   */
  const AMOUNT = 'amount';

  /**
   * @var array
   */
  protected $inquiry;

  /**
   * @var array
   */
  protected $transaction;

  /**
   * @var string
   */
  protected $newAmount;

  /**
   * @var string
   */
  protected $adjustmentAmount;

  /**
   * @var string
   */
  protected $amount;

  /**
   * @var string
   */
  protected $refNumber;

  /**
   * @var Json
   */
  protected $json;

  /**
   * @var PostCaptureTransactionStatus
   */
  protected $client;

  /**
   * @var TransactionHelper
   */
  protected $transactionHelper;

  /**
   * @var AuthCapture
   */
  protected $authCapture;

  /**
   * @var \Trans\Mepay\Gateway\Http\Client\Connect
   */
  protected $connect;

  /**
   * Constructor
   * @param Context $context           
   * @param Json $json              
   * @param PostCaptureTransactionStatus $client            
   * @param TransactionHelper $transactionHelper 
   * @param AuthCapture $autchCapture      
   * @param \Trans\Mepay\Gateway\Http\Client\Connect $connect      
   */
  public function __construct(
    Context $context,
    Json $json,
    PostCaptureTransactionStatus $client,
    TransactionHelper $transactionHelper,
    AuthCapture $authCapture,
    \Trans\Mepay\Gateway\Http\Client\Connect $connect
  ) {
    $this->json = $json;
    $this->client = $client;
    $this->transactionHelper = $transactionHelper;
    $this->authCapture = $authCapture;
    $this->connect = $connect;
    parent::__construct($context);
  }

  /**
   * Send capture request
   * @return void
   */
  public function send()
  {
    if (isset($this->inquiry['id']) && isset($this->transaction['id'])) {
      $transferBuilder = $this->client->create($this->inquiry['id'], $this->transaction['id'], $this->getBodyParams());
      return $this->connect->placeRequest($transferBuilder);
    }
  }

  /**
   * Get body params
   * @return array
   */
  public function getBodyParams()
  {
    try {
      $params = $this->prepareParam();

      return [
        TransactionInterface::AUTHORIZATION_CODE => $this->transaction[TransactionInterface::AUTHORIZATION_CODE],
        // TransactionInterface::AMOUNT => $this->transaction[TransactionInterface::AMOUNT],
        TransactionInterface::AMOUNT => $params['amount'],
        self::NEW_AMOUNT => $params['new_amount']
      ];
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Get new amount
   * @return string
   */
  public function getNewAmount()
  {
    return $this->newAmount;
  }

  /**
   * Set new amount
   * @param $input
   */
  public function setNewAmount($input)
  {
    $this->newAmount = $input;
  }

  /**
   * Set new amount
   * @param $input
   */
  public function setAdjustmentAmount($input)
  {
    $this->adjustmentAmount = $input;
  }

  /**
   * Set new amount
   * @param $input
   */
  public function getAdjustmentAmount()
  {
    return $this->adjustmentAmount;
  }

  /**
   * Get amount
   * @return string
   */
  public function getAmount()
  {
    return $this->amount;
  }

  /**
   * Set amount
   * @param $input
   */
  public function setAmount($input)
  {
    $this->amount = $input;
  }

  /**
   * Set reference number
   * @param $input
   */
  public function setReferenceNumber($input)
  {
    $this->refNumber = $input;
  }

  /**
   * get reference number
   * @param $input
   */
  public function getReferenceNumber()
  {
    return $this->refNumber;
  }

  /**
   * Set transaction by order id
   * @param  $orderId 
   */
  public function setTxnByOrderId($refNumber)
  {
    $order = $this->transactionHelper->getSalesOrderArrayParent($refNumber);
    $orderId = $order['entity_id'];
    
    // $txn = $this->transactionHelper->getLastOrderTransaction($orderId);
    $txn = $this->transactionHelper->getSalesPaymentTransactionByOrderId($orderId);
    
    if (isset($txn['trans_mepay_inquiry'])) {
      $this->inquiry = $this->json->unserialize($txn['trans_mepay_inquiry']);
    }
    if (isset($txn['trans_mepay_transaction'])) {
      $this->transaction = $this->json->unserialize($txn['trans_mepay_transaction']);
    }
  }

  /**
   * prepare param
   *
   * @return array
   */
  protected function prepareParam()
  {
    $result['amount'] = 0;
    $result['new_amount'] = 0;
    
    $adjAmount = $this->getAdjustmentAmount();
    $refNumber = $this->getReferenceNumber();
    $order = $this->transactionHelper->getSalesOrderArrayParent($refNumber);
    
    if(!empty($order)) {
      $grandTotal = $order['grand_total'];

      $result['amount'] = (int) $grandTotal;
      $result['new_amount'] = (int) $grandTotal - (int) $adjAmount;
    }

    return $result;
  }
}