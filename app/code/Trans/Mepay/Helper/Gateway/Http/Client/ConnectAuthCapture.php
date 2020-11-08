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
   * Constructor
   * @param Context                      $context           
   * @param Json                         $json              
   * @param PostCaptureTransactionStatus $client            
   * @param TransactionHelper            $transactionHelper 
   * @param AuthCapture                  $autchCapture      
   */
  public function __construct(
    Context $context,
    Json $json,
    PostCaptureTransactionStatus $client,
    TransactionHelper $transactionHelper,
    AuthCapture $autchCapture
  ) {
    $this->json = $json;
    $this->client = $client;
    $this->transactionHelper = $transactionHelper;
    $this->authCapture = $authCapture;
    parent::__construct($context);
  }

  /**
   * Send capture request
   * @return void
   */
  public function send()
  {
    if (isset($this->inquiry['id']) && isset($this->transaction['id'])) {
      $this->client->create($this->inquiry['id'], $this->transaction['id'], $this->getBodyParams());
      $this->authCapture->create($this->inquiry['id'], $this->transaction['id'], $this->getBodyParams());
    }
  }

  /**
   * Get body params
   * @return array
   */
  public function getBodyParams()
  {
    try {
      return [
        TransactionInterface::AUTHORIZATION_CODE => $this->transaction[TransactionInterface::AUTHORIZATION_CODE],
        TransactionInterface::AMOUNT => $this->transaction[TransactionInterface::AMOUNT],
        self::NEW_AMOUNT => $this->getNewAmount()
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
   * Set transaction by order id
   * @param  $orderId 
   */
  public function setTxnByOrderId($orderId)
  {
    $txn = $this->transactionHelper->getLastOrderTransaction($orderId);
    if ($txn->getTransMepayInquiry()) {
      $this->inquiry = $this->json->unserialize($txn->getTransMepayInquiry());
    }
    if ($txn->getTransMepayTransaction()) {
      $this->transaction = $this->json->unserialize($this->getTransMepayTransaction());
    }
  }
}