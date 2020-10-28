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
namespace Trans\Mepay\Helper\Payment;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface as TransactionBuilder;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Trans\Mepay\Helper\Response\Response as ResponseHelper;
use Trans\Mepay\Helper\Response\Payment\Inquiry as InquiryResponseHelper;
use Trans\Mepay\Logger\LoggerWrite;

class Transaction extends AbstractHelper
{
  /**
   * @var string
   */
  const SELECTION_URL = 'selections';

  /**
   * @var string
   */
  const CHECKOUT_URL = 'checkout';

  /**
   * @var SearchCriteriaBuilder
   */
  protected $searchBuilder;

  /**
   * @var OrderRepositoryInterface
   */
  protected $orderRepo;

  /**
   * @var OrderInterfaceFactory
   */
  protected $orderfactory;

  /**
   * @var TransactionRepositoryInterface
   */
  protected $transactionRepo;

  /**
   * @var TransactionBuilder
   */
  protected $transactionBuilder;

  /**
   * @var ResponseHelper
   */
  protected $responseHelper;

  /**
   * @var InquiryResponseHelper
   */
  protected $inquiryResponseHelper;

  /**
   * Json
   */
  protected $json;

  /**
   * @var LoggerWrite
   */
  protected $logger;

  /**
   * Constructor
   * @param Context                        $context
   * @param SearchCriteriaBuilder          $searchBuilder
   * @param OrderRepositoryInterface       $orderRepo
   * @param OrderInterfaceFactory          $orderfactory
   * @param TransactionRepositoryInterface $transactionRepo
   * @param TransactionBuilder             $transactionBuilder
   * @param  LoggerWrite $logger
   */
  public function __construct(
    Context $context,
    SearchCriteriaBuilder $searchBuilder,
    OrderRepositoryInterface $orderRepo,
    OrderInterfaceFactory $orderfactory,
    TransactionRepositoryInterface $transactionRepo,
    TransactionBuilder $transactionBuilder,
    ResponseHelper $responseHelper,
    InquiryResponseHelper $inquiryResponseHelper,
    Json $json,
    LoggerWrite $logger
  ) {
    $this->searchBuilder = $searchBuilder;
    $this->orderRepo = $orderRepo;
    $this->orderfactory = $orderfactory;
    $this->transactionRepo = $transactionRepo;
    $this->transactionBuilder = $transactionBuilder;
    $this->responseHelper = $responseHelper;
    $this->inquiryResponseHelper = $inquiryResponseHelper;
    $this->json = $json;
    $this->logger = $logger;
    parent::__construct($context);
  }

  /**
   * Get order
   * @param  mixed $id
   * @return \Magento\Sales\Api\Data\OrderPaymentInterface
   */
  public function getOrder($id)
  {
    return $this->orderRepo->get($id);
  }

  /**
   * Get order by increment id
   * @param  int $incrementId
   * @return \Magento\Sales\Api\Data\OrderInterface
   */
  public function getOrderByIncrementId($incrementId)
  {
    return $this->orderfactory->create()->loadByIncrementId($incrementId);
  }

  /**
   * Save order
   * @param  \Magento\Sales\Api\Data\OrderInterface $order
   * @return \Magento\Sales\Api\Data\OrderInterface
   */
  public function saveOrder($order)
  {
    return $this->orderRepo->save($order);
  }

  /**
   * Get authorize transaction by txnid
   * @param  string $txnId
   * @return \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
   */
  public function getAuthorizeByTxnId($txnId)
  {
    $searchCriteria = $this->getSearchCriteria([
      TransactionInterface::TXN_TYPE => TransactionInterface::TYPE_AUTH,
      TransactionInterface::TXN_ID => $txnId,
      TransactionInterface::IS_CLOSED => 0
    ]);
    return $this->transactionRepo->getList($searchCriteria);
  }

  /**
   * Get authorize transaction
   * @return Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
   */
  public function getAuthorizedTxn()
  {
    $searchCriteria = $this->getSearchCriteria([
      TransactionInterface::TXN_TYPE => TransactionInterface::TYPE_AUTH,
      TransactionInterface::IS_CLOSED => 0
    ]);
    return $this->transactionRepo->getList($searchCriteria);
  }

  /**
   * Get transaction by txn id
   * @param  int $txnId
   * @return Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
   */
  public function getTxnByTxnId($txnId)
  {
    $searchCriteria = $this->getSearchCriteria([
      TransactionInterface::TXN_ID => $txnId
    ]);
    return $this->transactionRepo->getList($searchCriteria);

  }

  /**
   * Get search criteria
   * @param  array $filters
   * @return \Magento\Framework\Api\SearchCriteriaInterface
   */
  protected function getSearchCriteria($filters)
  {
    foreach ($filters as $key => $value) {
      $this->searchBuilder->addFilter($key, $value);
    }
    return $this->searchBuilder->create();
  }

  /**
   * Get transaction by their transaction_id
   * @param  int $id
   * @return TransactionInterface
   */
  public function getTransaction($id)
  {
    return $this->transactionRepo->get($id);
  }

  public function saveTransaction($txn)
  {
    return $this->transactionRepo->save($txn);
  }

  /**
   * Build capture transaction
   * @param   $payment
   * @param   $order
   * @param   $transaction
   * @return
   */
  public function buildCaptureTransaction($payment, $order, $transaction)
  {
   // var_dump($transaction->getId());die();
    return $this->transactionRepo->save(
      $this->transactionBuilder->setPayment($payment)
      ->setOrder($order)
      ->setTransactionId($transaction->getId())
      ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => $this->responseHelper->extract($transaction->getData())])
      ->setFailSafe(true)
      ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE)
    );
  }

    /**
   * Build authorize transaction
   * @param   $payment
   * @param   $order
   * @param   $transaction
   * @return
   */
  public function buildAuthorizeTransaction($payment, $order, $transaction)
  {
    return $this->transactionRepo->save(
      $this->transactionBuilder->setPayment($payment)
      ->setOrder($order)
      ->setTransactionId($transaction->getId())
      ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => $this->responseHelper->extract($transaction->getData())])
      ->setFailSafe(true)
      ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH)
    );
  }

  /**
   * Adding updated transaction data
   * @param $inquiry
   * @param $transaction
   * @return  boolean
   */
  public function addTransactionData($txnId, $inquiry, $transaction)
  {
    try {

      $searchCriteria = $this->getSearchCriteria([
        TransactionInterface::TXN_ID => $txnId
      ]);

      $collection = $this->transactionRepo->getList($searchCriteria);
      if ($collection->getSize()) {
        foreach ($collection as $key => $value) {
          $id = $value->getTransactionId();
          $inquiryData = $this->inquiryResponseHelper->convertToArray($inquiry);
          $transactionData = $transaction->getData();
          $txn = $this->getTransaction($id);
          if ($txn->getId()) {
            $txn->setTransMepayInquiry($this->json->serialize($inquiryData));
            $txn->setTransMepayTransaction($this->json->serialize($transactionData));
            $this->transactionRepo->save($txn);
          }
        }
      }
    } catch (\Exception $e) {
      $this->logger->log($e->getMessage());
      throw $e;
    }
  }

  /**
   * Get void transaction
   * @param  int $txnId
   * @return Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
   */
  public function getVoidTransaction($orderId=null)
  {
    $param = [];
    $param[TransactionInterface::TXN_TYPE] = TransactionInterface::TYPE_VOID;
    $param[TransactionInterface::IS_CLOSED] = 0;
    if($orderId){
      $param['order_id'] = $orderId;
    }
    $searchCriteria = $this->getSearchCriteria($param);
    return $this->transactionRepo->getList($searchCriteria);
  }
}
