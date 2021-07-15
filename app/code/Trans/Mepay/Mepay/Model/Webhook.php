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
use Trans\Mepay\Model\Payment\Status;
use Trans\Mepay\Model\Payment\StatusFactory;
use Trans\Mepay\Logger\LoggerWrite;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Request;
use Trans\Mepay\Helper\Response\Payment\Inquiry as InquiryResponseHelper;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;

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
   * @var array
   */
  protected $invalidToken = ['undefined'];

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

  /**
   * @var \Trans\Mepay\Helper\Response\Payment\Transaction
   */
  protected $transactionResponse;

  /**
   * @var \Trans\Mepay\Helper\Order
   */
  protected $orderHelper;

  protected $transactionHelper;

  protected $eventManager;

  protected $restoreHelper;

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
   * @param \Trans\Mepay\Helper\Response\Payment\Transaction $transactionResponse
   * @param \Trans\Mepay\Helper\Order $orderHelper
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
    EventManager $eventManager,
    \Trans\Mepay\Helper\Response\Payment\Transaction $transactionResponse,
    \Trans\Mepay\Helper\Order $orderHelper,
    \Trans\Mepay\Helper\Restore $restoreHelper
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
    $this->transactionResponse = $transactionResponse;
    $this->orderHelper = $orderHelper;
    $this->restoreHelper = $restoreHelper;
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
   * @inheritdoc
   */
  public function received($type, $transaction, $inquiry, $token = null)
  {
    if (in_array(strtolower($token), $this->invalidToken)) {
      $token = null;
    }
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

    if (strtolower($type) == ResponseInterface::PAYMENT_RECEIVED_TYPE) {
        return $this->received($type, $transaction, $inquiry, $token);
    } else {
        $validate = $this->validate($type, $inquiry);

        /*
        if($validate->getStatus() == ResponseInterface::STATUS_OK) {
            $inquryData = $this->inquiryResponseHelper->convertToArray($inquiry);
            if(isset($inquryData['order']['id'])) {
                $refNumber = $inquryData['order']['id'];
                $salesOrder = $this->transactionHelper->getSalesOrderArrayParent($refNumber, true);
                $payment = $salesOrder['method'];
                
                if (strpos($payment, 'cc') !== false) {
                    $transactionData = $this->transactionResponse->convertToArray($transaction);
                    
                    if(isset($transactionData['status'])) {
                        $status = $transactionData['status'];
                        if($status == Status::DECLINED) {
                            $this->orderHelper->doCancelationOrderByRefNumber($refNumber);
                            $quoteId = $this->orderHelper->getQuoteIdByReffNumber($refNumber);
                            $this->restoreHelper->restoreQuote($quoteId);
                        }
                      
                    }
                }
            }
        }
        */
        
        return $validate;
    }
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
