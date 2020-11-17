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
namespace Trans\Mepay\Model\Payment;

use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Logger\LoggerWrite;
use Trans\Mepay\Helper\Payment\Transaction;
use Trans\Mepay\Model\Payment\Status\Capture;
use Trans\Mepay\Model\Payment\Status\Paid;
use Trans\Mepay\Model\Payment\Status\Authorize;
use Trans\Mepay\Model\Payment\Status\Failed;

class Status
{
  /**
   * @var  string
   */
  const AUTHORIZE = 'authorize';

  /**
   * @var  string
   */
  const CAPTURE = 'capture';

  /**
   * @var  string
   */
  const SETTLEMENT = 'settlement';

  /**
   * @var  string
   */
  const PAID = 'paid';

  /**
   * @var  string
   */
  const CANCEL = 'cancel';

  /**
   * @var  string
   */
  const FAILED = 'failed';

  /**
   * @var  string
   */
  const SUBMITTED = 'submitted';

  /**
   * @var  string
   */
  const DECLINED = 'declined';

  /**
   * @var  string
   */
  const PENDING = 'pending';

  /**
   * @var  string
   */
  const VALIDATED = 'validated';

  /**
   * @var  string
   */
  const PROCESSING = 'processing';

  /**
   * @var  string
   */
  const AUTHORIZED = 'authorized';

  /**
   * @var  string
   */
  const CAPTURED = 'captured';

  /**
   * @var  string
   */
  const AUTHORIZATION = 'authorization';

  /**
   * @var  string
   */
  const VOID = 'void';

  /**
   * @var Config
   */
  protected $config;

  /**
   * @var 
   */
  protected $transactionHelper;

  /**
   * @var Logger
   */
  protected $logger;

  /**
   * @var Capture
   */
  protected $capture;

  /**
   * @var Paid
   */
  protected $paid;

  /**
   * @var Authorize
   */
  protected $authorize;

  /**
   * @var Failed
   */
  protected $failed;

  /**
   * Constructor
   * @param Config $config
   * @param Logger $logger
   * @param Capture $capture
   */
  public function __construct(
    Config $config,
    Transaction $transactionHelper,
    LoggerWrite $logger,
    Capture $capture,
    Paid $paid,
    Authorize $authorize,
    Failed $failed
  ) {
    $this->config = $config;
    $this->transactionHelper = $transactionHelper;
    $this->logger = $logger;
    $this->capture = $capture;
    $this->paid = $paid;
    $this->authorize = $authorize;
    $this->failed = $failed;
  }

  /**
   * Update transaction
   * @param  $transactionData
   * @param  $inquiryId
   * @param  $status
   * @param  $token
   * @return void
   */
  public function update($transaction, $inquiry, $token = null)
  {
    if ($transaction) {
      //$this->transactionHelper->addTransactionData($inquiry->getId(), $inquiry, $transaction);

      $transactionDatas = $this->transactionHelper->getAuthorizeByTxnId($inquiry->getId());
      foreach ($transactionDatas as $key => $value) {
        if ($value->getTransactionId()) {
            $this->logger->log('== {{authorize_operation}} ==');
            $this->authorize->handle($value->getTransactionId(), $transaction, $inquiry, $token); 
         }
      }

      if ($this->isCapture($transaction->getStatus())) {
          $this->logger->log('== {{capture_operation}} ==');
          $this->capture->handle($transaction, $inquiry, $token);
      }

      if ($this->isFailed($transaction->getStatus())) {
          $this->logger->log('== {{failed_operation}} ==');
          $this->failed->handle($transaction, $inquiry, $token);
      }

      $this->saveAuthenticateCode($transaction);


    }
  }

  /**
   * Check is transaction exist
   * @param  int $txnId
   * @return boolean
   */
  public function isExist($txnId)
  {
    $collection = $this->transactionHelper->getTxnByTxnId($txnId);
    if($collection->getSize())
      return true;
    return false;
  }

  /**
   * Is status authorize
   * @param  string  $status 
   * @return boolean         
   */
  public function isAuthorize($status)
  {
    $status = strtolower($status);
    $mapAuthorize = [
      self::AUTHORIZATION,
      self::AUTHORIZED,
      self::AUTHORIZE,
      self::SUBMITTED,
      self::PENDING,
      self::VALIDATED,
      self::PROCESSING
    ];
    if(in_array($status, $mapAuthorize))
      return true;
    return false;
  }

  /**
   * Is status capture
   * @param  string  $status 
   * @return boolean         
   */
  public function isCapture($status)
  {
    $status = strtolower($status);
    $mapCapture = [
      self::CAPTURED,
      self::CAPTURE,
      self::PAID
    ];
    if(in_array($status, $mapCapture))
      return true;
    return false;
  }

  /**
   * Is status failed
   * @param  string  $status 
   * @return boolean         
   */
  public function isFailed($status)
  {
    $status = strtolower($status);
    $mapFailed = [
      self::FAILED,
      self::DECLINED,
      self::CANCEL,
      self::VOID
    ];
    if(in_array($status, $mapFailed))
      return true;
    return false;
  }

  /**
   * Save authenticate code
   * @param  $txn
   * @return void
   */
  public function saveAuthenticateCode($txn)
  {
    if ($txn->getAuthenticationCode()) {
      $savedTxn = $this->transactionHelper->getTxnByTxnId($txn->getId());
      $savedTxn->setTransMepayAuthenticateCode($txn->getAuthenticationCode());
      $this->transactionHelper->saveTransaction($savedTxn);
    }
  }

}
