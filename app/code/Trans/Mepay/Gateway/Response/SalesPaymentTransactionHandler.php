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
namespace Trans\Mepay\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Trans\Mepay\Helper\Response\Response;
use Trans\Mepay\Gateway\Request\PaymentSourceMethodDataBuilder;
use Trans\Mepay\Logger\Logger;

class SalesPaymentTransactionHandler implements HandlerInterface
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
   * Constructor.
   * @param SubjectReader $subjectReader
   */
  public function __construct(
      SubjectReader $subjectReader,
      Json $json,
      Response $response,
      Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->json = $json;
      $this->response = $response;
      $this->logger = $logger;
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
      $resp = $this->getDummyResponse();
      if (isset($resp[Response::RESPONSE_ID]) && $resp[Response::RESPONSE_ID]) {
        $this->savePayment($orderPayment, $resp);
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
    if (isset($resp[PaymentSourceMethodDataBuilder::PAYMENT_SOURCE_METHOD])) {
        $orderPayment->setCcType($resp[PaymentSourceMethodDataBuilder::PAYMENT_SOURCE_METHOD]);
    }
    $orderPayment->setLastTransId($resp[Response::RESPONSE_ID]);
    $orderPayment->setTransactionId($resp[Response::RESPONSE_ID]);
    $orderPayment->setIsTransactionClosed($this->shouldCloseTransaction());
    $orderPayment->setShouldCloseParentTransaction($this->shouldCloseParentTransaction($orderPayment));
    $orderPayment->setTransactionAdditionalInfo(PaymentTransaction::RAW_DETAILS, $this->response->extract($resp));
  }

  /**
   * Whethertransaction should
   * @return bool
   */
  protected function shouldCloseTransaction()
  {
    return false;
  }

  /**
   * Whether parent transaction should closed
   * @param  Payment $orderPayment
   * @return bool
   */
  protected function shouldCloseParentTransaction(Payment $orderPayment)
  {
      return false;
  }

  /**
   * Get dummy response
   * @return array
   */
  protected function getDummyResponse()
  {
    $dummy = '{"id":"7f6d769f7-7ffc-4184-a77e-d70f77a9085f","createdTime":"2020-10-12T15:20:53.918Z","referenceId":"MI000000076","status":"unpaid","amount":37,"currency":"USD","paymentSources":["visa","megacc","megava","megaqris","megawallet","megadebit","brankasdirect"],"urls":{"selections":"https://checkout.dev.megapay.app/checkout/eKNsqr52734JUkhqbEPXvr","checkout":"https://checkout.dev.megapay.app/checkout/eKNsqr52734JUkhqbEPXvr"},"paymentSourceMethod":"authcapture"}';
    return $this->json->unserialize($dummy);
  }
}
