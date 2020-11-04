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
namespace Trans\MepayTransmart\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Trans\Mepay\Helper\Response\Response;
use Trans\Mepay\Logger\Logger;
use Trans\Mepay\Gateway\Response\SalesPaymentTransactionHandler;
use Trans\Sprint\Api\Data\SprintResponseInterface;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Sprint\Helper\Config;

class TransmartSalesPaymentTransactionHandler extends SalesPaymentTransactionHandler 
{
  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * @var Json
   */
  private $json;

  /**
   * @var Response
   */
  private $response;

  /**
   * @var TransactionHelper
   */
  private $transactionHelper;

  /**
   * Constructor.
   * @param SubjectReader $subjectReader
   */
  public function __construct(
      SubjectReader $subjectReader,
      Json $json,
      Response $response,
      Logger $logger,
      TransactionHelper $transactionHelper
  ) {
      $this->subjectReader = $subjectReader;
      $this->json = $json;
      $this->response = $response;
      $this->logger = $logger;
      $this->transactionHelper = $transactionHelper;
      parent::__construct($subjectReader, $json, $response, $logger);
  }

  /**
   * Handle responseTrans\Mepay\Model
   * @param  array  $handlingSubject
   * @param  array  $response
   * @return void
   */
  public function handle(array $handlingSubject, array $response)
  {
    $paymentDO = $this->subjectReader->readPayment($handlingSubject);
    $orderPayment = $paymentDO->getPayment();
    if ($orderPayment instanceof Payment) {
      $resp = $this->response->unserialize($response);
      //$resp = $this->getDummyResponse();
      if (isset($resp[Response::RESPONSE_ID]) && $resp[Response::RESPONSE_ID]) {
        $this->savePayment($orderPayment, $resp);
        $this->updateStatusToOms($orderPayment);
      }
      $orderPayment->setAdditionalInformation([PaymentTransaction::RAW_DETAILS => $resp]);
    }
  }

  /**
   * Save payment gateway response
   * @param  array $resp
   * @param  Payment $orderPayment
   * @return void
   */
  protected function savePayment($orderPayment, $resp)
  {
    $orderPayment->setLastTransId($resp[Response::RESPONSE_ID]);
    $orderPayment->setTransactionId($resp[Response::RESPONSE_ID]);
    $orderPayment->setIsTransactionClosed($this->shouldCloseTransaction());
    $orderPayment->setShouldCloseParentTransaction($this->shouldCloseParentTransaction($orderPayment));
    $orderPayment->setTransactionAdditionalInfo(PaymentTransaction::RAW_DETAILS, $this->response->extract($resp));
  }
}