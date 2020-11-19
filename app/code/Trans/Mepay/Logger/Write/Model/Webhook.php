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
namespace Trans\Mepay\Logger\Write\Model;

use Magento\Framework\Serialize\Serializer\Json;
use Trans\Mepay\Helper\Response\Payment\Inquiry;
use Trans\Mepay\Helper\Response\Payment\Transaction;

class Webhook
{
  /**
   * @var [type]
   */
  protected $inquiry;

  /**
   * @var [type]
   */
  protected $json;

  /**
   * @var [type]
   */
  protected $transaction;

  /**
   * Constructor method
   * @param Json $json
   * @param Inquiry $inquiry
   * @param Transaction $transaction
   */
  public function __construct(
    Json $json,
    Inquiry $inquiry,
    Transaction $transaction
  ) {
    $this->json = $json;
    $this->inquiry = $inquiry;
    $this->transaction = $transaction;
  }

  /**
   * Logging input notification
   * @param \Trans\Mepay\Logger\LoggerWrite $logger
   * @param string $type
   * @param \Magento\Framework\DataObject $transaction
   * @param \Magento\Framework\DataObject $inquiry
   * @param string $token
   * @return void
   */
  public function logNotif($logger, $type, $transaction, $inquiry, $token)
  {
    $logger->debug('================= Webhook Input Logger start ===============');
    $logger->debug('Payment Type: '.$type);
    if ($transaction) {
      $logger->debug('Transaction =========');
      $logger->debug($this->json->serialize($this->transaction->convertToArray($transaction)));
    }
    if ($inquiry){
      $logger->debug('inquiry =========');
      $logger->debug($this->json->serialize($this->inquiry->convertToArray($inquiry)));
    }
    if ($token)
      $logger->debug('Token: '.$token);
    $logger->debug('================= Webhook Input Logger end ===============');
  }

  /**
   * Logging for response notification
   * @param \Trans\Mepay\Logger\LoggerWrite $logger
   * @param string $type
   * @param string $status
   * @param \Magento\Framework\DataObject  $response
   * @return void
   */
  public function logResponse($logger, $type, $status, $response)
  {
    $logger->debug('================= Webhook Response Logger start ===============');
    $logger->debug('Payment Type: '.$type);
    $logger->debug('Response Status: '.$status);
    if ($response->getInquiry()) {
      $logger->debug('Inquiry: ');
      $logger->debug($this->json->serialize($this->inquiry->convertToArray($response->getInquiry())));
    }
    $logger->debug('================= Webhook Response Logger end ===============');
  }
}
