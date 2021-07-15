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

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface as TransactionBuilder;
use Trans\Mepay\Api\Data\TransactionInterface as MepayTransactionInterface;
use Trans\Mepay\Helper\Response\Payment\Inquiry as InquiryResponseHelper;
use Trans\Mepay\Helper\Response\Payment\Transaction as TransactionResponseHelper;
use Trans\Mepay\Helper\Response\Response as ResponseHelper;
use Trans\Mepay\Logger\LoggerWrite;

class Transaction extends AbstractHelper {
	/**
	 * @var string
	 */
	const SELECTION_URL = 'selections';

	/**
	 * @var string
	 */
	const CHECKOUT_URL = 'checkout';

	/**
	 * @var string
	 */
	const TRANSACTION = 'transaction';

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

	protected $transactionResponseHelper;

	/**
	 * Json
	 */
	protected $json;

	/**
	 * @var LoggerWrite
	 */
	protected $logger;

	/**
	 * @var SortOrderBuilder
	 */
	protected $sortOrderBuilder;

	/**
	 * @var \Magento\Sales\Model\ResourceModel\Order
	 */
	protected $salesOrderResource;

	/**
	 * Constructor
	 * @param Context                        $context
	 * @param SearchCriteriaBuilder          $searchBuilder
	 * @param OrderRepositoryInterface       $orderRepo
	 * @param OrderInterfaceFactory          $orderfactory
	 * @param TransactionRepositoryInterface $transactionRepo
	 * @param \Magento\Sales\Model\ResourceModel\Order $salesOrderResource
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
		TransactionResponseHelper $transactionResponseHelper,
		\Magento\Sales\Model\ResourceModel\Order $salesOrderResource,
		Json $json,
		SortOrderBuilder $sortOrderBuilder,
		LoggerWrite $logger
	) {
		$this->searchBuilder             = $searchBuilder;
		$this->orderRepo                 = $orderRepo;
		$this->orderfactory              = $orderfactory;
		$this->transactionRepo           = $transactionRepo;
		$this->transactionBuilder        = $transactionBuilder;
		$this->responseHelper            = $responseHelper;
		$this->salesOrderResource        = $salesOrderResource;
		$this->inquiryResponseHelper     = $inquiryResponseHelper;
		$this->transactionResponseHelper = $transactionResponseHelper;
		$this->json                      = $json;
		$this->sortOrderBuilder          = $sortOrderBuilder;
		$this->logger                    = $logger;
		parent::__construct($context);
	}

	/**
	 * Get order
	 * @param  mixed $id
	 * @return \Magento\Sales\Api\Data\OrderPaymentInterface
	 */
	public function getOrder($id) {
		return $this->orderRepo->get($id);
	}

	/**
	 * Get order by increment id
	 * @param  int $incrementId
	 * @return \Magento\Sales\Api\Data\OrderInterface
	 */
	public function getOrderByIncrementId($incrementId) {
		return $this->orderfactory->create()->loadByIncrementId($incrementId);
	}

	/**
	 * Save order
	 * @param  \Magento\Sales\Api\Data\OrderInterface $order
	 * @return \Magento\Sales\Api\Data\OrderInterface
	 */
	public function saveOrder($order) {
		return $this->orderRepo->save($order);
	}

	/**
	 * Get authorize transaction by txnid
	 * @param  string $txnId
	 * @return \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
	 */
	public function getAuthorizeByTxnId($txnId) {
		$searchCriteria = $this->getSearchCriteria([
			TransactionInterface::TXN_TYPE => TransactionInterface::TYPE_AUTH,
			TransactionInterface::TXN_ID => $txnId,
			TransactionInterface::IS_CLOSED => 0,
		]);
		return $this->transactionRepo->getList($searchCriteria);
	}

	/**
	 * Get authorize transaction
	 * @return Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
	 */
	public function getAuthorizedTxn() {
		$searchCriteria = $this->getSearchCriteria([
			TransactionInterface::TXN_TYPE => TransactionInterface::TYPE_AUTH,
			TransactionInterface::IS_CLOSED => 0,
		]);
		return $this->transactionRepo->getList($searchCriteria);
	}

	/**
	 * Get capture transaction by txnid
	 * @param  string $txnId
	 * @return \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
	 */
	public function getCaptureByTxnId($txnId) {
		$searchCriteria = $this->getSearchCriteria([
			TransactionInterface::TXN_ID => $txnId,
			TransactionInterface::IS_CLOSED => 0
		]);
		return $this->transactionRepo->getList($searchCriteria);

	}

	/**
	 * Get transaction by txn id
	 * @param  int $txnId
	 * @return Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
	 */
	public function getTxnByTxnId($txnId) {
		$searchCriteria = $this->getSearchCriteria([
			TransactionInterface::TXN_ID => $txnId,
		]);
		return $this->transactionRepo->getList($searchCriteria);

	}

	public function getLastOrderTransaction($orderId) {
		$sortOrder      = $this->sortOrderBuilder->setField(TransactionInterface::TRANSACTION_ID)->setDirection('DESC')->create();
		$searchCriteria = $this->getSearchCriteria([
			TransactionInterface::ORDER_ID => $orderId,
		])->setSortOrders([$sortOrder]);
		return $this->transactionRepo->getList($searchCriteria)->getFirstItem();
	}

	public function getSalesPaymentTransactionByOrderId($orderId) {
		$connection = $this->salesOrderResource->getConnection();
		$table      = $connection->getTableName('sales_payment_transaction');

		$query = $connection->select();
		$query->from(
			$table,
			['*']
		)->where('order_id = ?', $orderId)->where('trans_mepay_inquiry IS NOT NULL');

		return $connection->fetchRow($query);
	}

	/**
	 * Get search criteria
	 * @param  array $filters
	 * @return \Magento\Framework\Api\SearchCriteriaInterface
	 */
	protected function getSearchCriteria($filters) {
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
	public function getTransaction($id) {
		return $this->transactionRepo->get($id);
	}

	public function saveTransaction($txn) {
		return $this->transactionRepo->save($txn);
	}

	/**
	 * Build capture transaction
	 * @param   $payment
	 * @param   $order
	 * @param   $transaction
	 * @return
	 */
	public function buildCaptureTransaction($payment, $order, $transaction) {
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
	public function buildAuthorizeTransaction($payment, $order, $transaction) {
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
	public function addTransactionData($id, $inquiry, $transaction) {
		try {

			$inquiryData     = $this->inquiryResponseHelper->convertToArray($inquiry);
			$transactionData = $this->convertArray(self::TRANSACTION, $transaction->getData());
			$txn             = $this->getTransaction($id);
			$txn->setTransMepayInquiry($this->json->serialize($inquiryData));
			$txn->setTransMepayTransaction($this->json->serialize($transactionData));
			$this->transactionRepo->save($txn);

		} catch (\Exception $e) {
			$this->logger->log($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Convert to Array
	 * @param  string $mode
	 * @param  array $data
	 * @return array
	 */
	public function convertArray($mode, $data) {
		if ($mode == self::TRANSACTION) {
			foreach ($data as $key => $value) {
				if ($key == MepayTransactionInterface::STATUS_DATA) {
					$value      = $value->getData();
					$data[$key] = $value;
				}
			}
		}
		return $data;
	}

	/**
	 * Get void transaction
	 * @param  int $txnId
	 * @return Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection
	 */
	public function getVoidTransaction($orderId = null) {
		$param                                  = [];
		$param[TransactionInterface::TXN_TYPE]  = TransactionInterface::TYPE_VOID;
		$param[TransactionInterface::IS_CLOSED] = 0;
		if ($orderId) {
			$param['order_id'] = $orderId;
		}
		$searchCriteria = $this->getSearchCriteria($param);
		return $this->transactionRepo->getList($searchCriteria);
	}

	/**
	 * Get sales order data by reference_number with raw query
	 *
	 * @param string $referenceNumber
	 * @return array
	 */
	public function getSalesOrderArray($referenceNumber = null) {
		if ($referenceNumber) {
			$connection = $this->salesOrderResource->getConnection();
			$table      = $connection->getTableName('sales_order');

			$query = $connection->select();
			$query->from(
				$table,
				['*']
			)->where('reference_number = ?', $referenceNumber)->where('is_parent = ?', 0);

			return $collection = $connection->fetchRow($query);
		}
	}

	/**
	 * Get sales order data by reference_number with raw query
	 *
	 * @param string $referenceNumber
	 * @return array
	 */
	public function getSalesOrderArrayParent($referenceNumber = null, $joinPayment = false) {
		if ($referenceNumber) {
			$connection = $this->salesOrderResource->getConnection();
			$table      = $connection->getTableName('sales_order');

			$query = $connection->select();
			$query->from(
				$table,
				['*']
			)->where('reference_number = ?', $referenceNumber)->where('is_parent = ?', 1);

			if($joinPayment) {
				$query->joinLeft(['payment' => 'sales_order_payment'], 'sales_order.entity_id = payment.parent_id', ['payment.method']);
			}

			return $collection = $connection->fetchRow($query);
		}
	}

	public function getPgResponse($orderId)
	{
		$lists = $this->getTxnCriteriaByOrderId($orderId);
		foreach ($lists as $key => $value) {
			if($value->getTransMepayInquiry()) {
				if ($value->getTxnType() <> TransactionInterface::TYPE_VOID)
					return $value->getTransMepayInquiry();
			}
		}
		return '';
	}

	public function getPgTransaction($orderId)
	{
		$lists = $this->getTxnCriteriaByOrderId($orderId);
		foreach ($lists as $key => $value) {
			if($value->getTransMepayTransaction()) {
				return $value->getTransMepayTransaction();
			}
		}
		return '';
	}

	public function getTxnCriteriaByOrderId($orderId)
	{
		$param = ['order_id' => $orderId];
		$searchCriteria = $this->getSearchCriteria($param);
		return $this->transactionRepo->getList($searchCriteria);
	}
}