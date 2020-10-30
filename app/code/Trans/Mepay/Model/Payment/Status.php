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
      $this->transactionHelper->addTransactionData($inquiry->getId(), $inquiry, $transaction);

      $transactionData = $this->transactionHelper->getAuthorizeByTxnId($inquiry->getId())->getFirstItem();
      if ($transactionData->getId()) {
        $this->authorize->handle($transaction, $inquiry, $token); 
      }
      switch(strtolower($transaction->getStatus())){
        case self::CAPTURE : 
          $this->capture->handle($transaction, $inquiry, $token);
          break;
        case self::PAID : 
          $this->paid->handle($transaction, $inquiry, $token);
          break;
        case self::FAILED:
          $this->failed->handle($transaction, $inquiry, $token);
          break;
      }

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

}
