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
namespace Trans\Mepay\Model;

use Magento\Framework\DataObject;
use Trans\Mepay\Api\WebhookInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Trans\Mepay\Api\Data\InquiryInterfaceFactory;
use Trans\Mepay\Api\Data\InquiryInterface;
use Trans\Mepay\Api\Data\TransactionInterfaceFactory;
use Trans\Mepay\Api\Data\TransactionInterface;
use Trans\Mepay\Api\Data\ResponseInterfaceFactory;
use Trans\Mepay\Api\Data\ResponseInterface;
use Trans\Mepay\Model\Payment\StatusFactory;
use Trans\Mepay\Logger\LoggerWrite;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Request;
use Trans\Mepay\Helper\Response\Payment\Inquiry as InquiryResponseHelper;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Sprint\Helper\Config;

class Webhook extends DataObject implements WebhookInterface
{
  /**
   * @var string
   */
  const TYPE = 'type';

  /**
   * @var array
   */
  protected $inquiryData;

  /**
   * @var \InquiryInterface
   */
  protected $inquiry;

    /**
   * @var array
   */
  protected $transactionData;

  /**
   * @var \TransactionInterface
   */
  protected $transaction;

  /**
   * @var Signature
   */
  protected $signature;

  /**
   * @var \ResponseInterface
   */
  protected $response;

  /**
   * @var \StatusFactory
   */
  protected $statusFactory;

  /**
   * @var \LoggerWrite
   */
  protected $logger;

  /**
   * @var \Json
   */
  protected $json;

  /**
   * @var \Request
   */
  protected $request;

  /**
   * @var \InquiryResponseHelper
   */
  protected $inquiryResponseHelper;

  protected $transactionHelper;

  protected $eventManager;

  /**
   * Constructor
   * @param InquiryInterfaceFactory     $inquiryFactory
   * @param TransactionInterfaceFactory $transactionFactory
   * @param ResponseInterfaceFactory    $responseFactory
   * @param Signature                   $signature
   * @param StatusFactory               $statusFactory
   * @param LoggerWrite                 $logger
   * @param Json                        $json
   * @param Request                     $request
   * @param InquiryResponseHelper       $inquiryResponseHelper
   */
  public function __construct(
    InquiryInterfaceFactory $inquiryFactory,
    TransactionInterfaceFactory $transactionFactory,
    ResponseInterfaceFactory $responseFactory,
    Signature $signature,
    StatusFactory $statusFactory,
    LoggerWrite $logger,
    Json $json,
    Request $request,
    InquiryResponseHelper $inquiryResponseHelper,
    TransactionHelper $transactionHelper,
    EventManager $eventManager
  ) {
    $this->inquiry = $inquiryFactory->create();
    $this->transaction = $transactionFactory->create();
    $this->response = $responseFactory->create();
    $this->signature = $signature;
    $this->statusFactory = $statusFactory;
    $this->logger = $logger;
    $this->json = $json;
    $this->request = $request;
    $this->inquiryResponseHelper = $inquiryResponseHelper;
    $this->transactionHelper = $transactionHelper;
    $this->eventManager = $eventManager;
  }

    /**
   * Init
   * @param  InquiryInterface $inquiry
   * @param  TransactionInterface|null $transaction
   * @return void
   */
  protected function init($inquiry, $transaction = null)
  {
    $this->setInquiry($inquiry);
    $this->setTransaction($transaction);
    $this->extract();
  }

  /**
   * Set Inquiry
   * @param InquiryInterface $inqury
   * @return void
   */
  public function setInquiry($inquiry)
  {
    $this->inquiryData = $inquiry;
  }

  /**
   * Set transaction
   * @param TransactionInterface $transaction
   * @return  void
   */
  public function setTransaction($transaction)
  {
    $this->transactionData = $transaction;
  }

  /**
   * Extract
   * @return void
   */
  public function extract()
  {
    $this->extractInquiry();
    $this->extractTransaction();
  }

  /**
   * @inheritdoc
   */
  public function validate($type, $inquiry)
  {
    $this->init($inquiry);
    $status = $this->_validate($type);
    return $this->buildResponse($type, $status);
  }

   /**
     * Event pre dispatch for hit status to OMS
     * @param string $orderId
     * @param string $status
     */
    protected function updateStatusToOms($inquiry)
    {
        $collTxn = $this->transactionHelper->getTxnByTxnId($inquiry->getId())->getFirstItem();
        $order = $this->transactionHelper->getOrder($collTxn->getOrderId());
        $this->eventManager->dispatch(
            'update_payment_oms',
            [
                'reference_number' => $order->getReferenceNumber(),
                'payment_status' => Config::OMS_SUCCESS_PAYMENT_OPRDER,
            ]
        );
    }

  /**
   * @inheritdoc
   */
  public function received($type, $transaction, $inquiry, $token = null)
  {
    $this->init($inquiry, $transaction);
    $status = $this->_validate($type);
    $this->updateTransaction($status, $transaction, $inquiry, $token);
    return $this->buildResponse($type, $status);
  }

  /**
   * @inheritdoc
   */
  public function notif($type, $transaction = null, $inquiry = null, $token = null)
  {
    //$this->logger->log($this->request->getHeader('signature'));
    $this->logger->loggingModelWebhook('notif',[
      'type'=>$type,
      'transaction'=>$transaction,
      'inquiry'=>$inquiry,
      'token'=>$token
    ]);

    if (strtolower($type) == ResponseInterface::PAYMENT_RECEIVED_TYPE)
       return $this->received($type, $transaction, $inquiry, $token);
    else
      return $this->validate($type, $inquiry);

  }

  /**
   * Extract inquiry
   * @return void
   */
  protected function extractInquiry() 
  {
    foreach ($this->inquiryData as $key => $value) {
      foreach ($value as $index => $data) {
          switch ($index) {

          case InquiryInterface::ORDER :
            $this->inquiry->setData($index, $this->inquiry->extractOrder($data));
          break;

          case InquiryInterface::CUSTOMER :
            $this->inquiry->setData($index, $this->inquiry->extractCustomer($data));
          break;

          case InquiryInterface::MERCHANT :
            $this->inquiry->setData($index, $this->inquiry->extractMerchant($data));
          break;

          default:
            $this->inquiry->setData($index, $data);
            break;
        }
      }
    }
  }

  /**
   * Extract transaction
   * @return void
   */
  protected function extractTransaction()
  {
    if ($this->transactionData) {
      foreach ($this->transactionData as $key => $value) {
        switch ($key) {
          case TransactionInterface::STATUS_DATA :
            $this->transaction->setData($key, $this->transaction->extractStatusData($value));
            break;
          
          default:
            $this->transaction->setData($key, $value);
            break;
        }
      }
    }
  }

  /**
   * Validate inquiry
   * @return string
   */
  protected function _validate($type)
  {
    $id = $this->inquiry->getId();
    $this->updateStatusToOms($this->inquiry);
    $status = $this->statusFactory->create();
    if ($id && $status->isExist($id)) {
      if ($type == ResponseInterface::PAYMENT_VALIDATE_TYPE) {
        return ResponseInterface::STATUS_OK;
      }
     return ResponseInterface::STATUS_ACK;
    }

    if ($type == ResponseInterface::PAYMENT_VALIDATE_TYPE)
      return ResponseInterface::STATUS_FAILED;
    return ResponseInterface::STATUS_NACK;
  }

  /**
   * Update transaction
   * @param  string $status
   * @param  \Trans\Mepay\Api\Data\TransactionInterface $transaction
   * @param  \Trans\Mepay\Api\Data\InquiryInterface $inquiry
   * @return void
   */
  public function updateTransaction($status, $transaction, $inquiry, $token)
  {
    if ($status == ResponseInterface::STATUS_ACK) {
      $status = $this->statusFactory->create();
      $status->update($transaction, $inquiry, $token);
    }
  }

  /**
   * Build response
   * @param  string $type
   * @param  string $status
   * @return \ResponseInterface
   */
  protected function buildResponse($type, $status)
  {
    $this->response->setStatus($status);
    $this->response->setValidateSignature($this->signature->generateMd5Signature($this->request->getHeader('Signature')));
    if ($type == ResponseInterface::PAYMENT_VALIDATE_TYPE) {
      $this->response->setInquiry($this->inquiryData);
    }
   $this->logger->loggingModelWebhook('response',['type'=>$type, 'status'=>$status, 'response'=>$this->response]);
   return $this->response;
  }

  /**
   * Check token
   * @param  string $token
   * @return boolean
   */
  public function checkToken($token)
  {
    if($token)
      return true;
    return false;
  }
}
