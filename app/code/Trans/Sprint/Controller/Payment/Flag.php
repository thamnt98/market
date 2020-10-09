<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Controller\Payment;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Trans\Sprint\Api\Data\SprintResponseInterface;
use Trans\Sprint\Helper\Config as configHelper;

/**
 * Class Flag
 */
class Flag extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
	/**
	 * @var \Trans\Sprint\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var configHelper
	 */
	protected $config;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var \Trans\Sprint\Api\PaymentNotifyInterface
	 */
	protected $notifyInterface;

	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;

	/**
	 * @var \Trans\Sprint\Api\SprintCustomerTokenizationRepositoryInterface
	 */
	protected $tokenizeRepository;

	/**
	 * @var \Trans\Sprint\Api\SprintResponseRepositoryInterface
	 */
	protected $sprintResponse;

	/**
	 * @var \Trans\Sprint\Helper\SalesOrder
	 */
	protected $orderHelper;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Trans\Sprint\Helper\Data $dataHelper
	 * @param \Trans\Sprint\Api\PaymentNotifyInterface $notifyInterface
	 * @param \Trans\Sprint\Api\SprintCustomerTokenizationRepositoryInterface $tokenizeRepository
	 * @param \Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResponse
	 * @param \Trans\Sprint\Helper\SalesOrder $orderHelper
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Trans\Sprint\Helper\Data $dataHelper,
		\Trans\Sprint\Api\PaymentNotifyInterface $notifyInterface,
		\Magento\Framework\App\Request\Http $request,
		\Trans\Sprint\Api\SprintCustomerTokenizationRepositoryInterface $tokenizeRepository,
		\Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResponse,
		\Trans\Sprint\Helper\SalesOrder $orderHelper
	) {
		parent::__construct($context);

		$this->resultJsonFactory = $resultJsonFactory;
		$this->dataHelper = $dataHelper;
		$this->notifyInterface = $notifyInterface;
		$this->request = $request;
		$this->tokenizeRepository = $tokenizeRepository;
		$this->sprintResponse = $sprintResponse;
		$this->orderHelper = $orderHelper;

		$this->logger = $this->dataHelper->getLogger();
		$this->config = $this->dataHelper->getConfigHelper();
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$this->logger->info('===== Notify Controller ===== Start');
		$postData = $this->getRequest()->getParams();

		$responseData = $this->generateResponse($postData);

		if ($responseData['is_success']) {
			try {
				if($postData) {
					$this->logger->info('post : ' . $this->dataHelper->serializeJson($postData));
					$this->notifyInterface->processingNotify($postData);				
				}
			} catch (\Exception $e) {
				$this->logger->info('===== Notify Controller ===== Generate code error : ' . $e->getMessage());
			}

			try {
				if (isset($postData['cardToken'])) {
					$this->tokenizeRepository->saveCardToken($postData['transactionNo'], $postData['cardNo'], $postData['cardToken']);
				}
			} catch (\Exception $e) {
			    $this->logger->info('Save token error = ' . $e->getMessage());
			}
		}

		
		$result = $this->resultJsonFactory->create();
		$this->logger->info('Repsonse Data = ' . json_encode($responseData));
		$this->logger->info('===== Notify Controller ===== End');
		return $result->setData($responseData['data']);
	}

	/**
	 * generate response data
	 *
	 * @param array $postData
	 * @return json
	 */
	protected function generateResponse($postData) {
		$data = array();
		$result = array();
		$is_success = false;

		if ($postData) {
			$getByTransNo = $this->sprintResponse->getByTransactionNo($postData['transactionNo']);			
			$transactionStatus = array(configHelper::PAYMENT_FLAG_SUCCESS_CODE, configHelper::PAYMENT_FLAG_DECLINED_01, configHelper::PAYMENT_FLAG_DECLINED_02, configHelper::PAYMENT_FLAG_DECLINED_03, configHelper::PAYMENT_FLAG_DECLINED_04, configHelper::PAYMENT_FLAG_DECLINED_05, configHelper::PAYMENT_FLAG_DECLINED_06);

			// amount
			$subOrder = $this->orderHelper->getSubOrdersWithInc($postData['transactionNo']);
			$amountTrans = $this->orderHelper->getSubOrdersGrandTotal($subOrder);

			// authcode
			$authCode = $this->dataHelper->doAuthCode(array('channel_id' => $postData['channelId'], 'transaction_no' => $postData['transactionNo'], 'transaction_amount' => $postData['transactionAmount'], 'transaction_status' => $postData['transactionStatus'], 'insert_id' => $postData['insertId']), $getByTransNo->getPaymentMethod());
			
			// status order
			$statusOrder = $this->orderHelper->getStatusOrders($subOrder);

			$data['channelId'] = $postData['channelId'];
			$data['currency'] = $postData['currency'];

			// check if transaction no not exist on sprint_response table
			if (!$getByTransNo->getTransactionNo()) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_TRANSACTIONNO_MESSAGE;
			}
			// check validation (channel ID)
			elseif ($postData['channelId'] != $getByTransNo->getChannelId()) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_CHANNELID_MESSAGE;
			}

			// check validation (transactionNo)
			elseif ($postData['transactionNo'] != $getByTransNo->getTransactionNo()) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_TRANSACTIONNO_MESSAGE;
			}

			// check validation (transactionAmount)
			elseif ($postData['transactionAmount'] != $amountTrans) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_TRANSACTIONAMOUNT_MESSAGE;
			}

			// check validation (insertId)
			elseif ($postData['insertId'] != $getByTransNo->getInsertId()) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_INSERTID_MESSAGE;
			}

			// check validation (authCode)
			elseif ($postData['authCode'] != $authCode) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_AUTHCODE_MESSAGE;
			}

			// check validation (currency)
			elseif ($postData['currency'] != $getByTransNo->getCurrency()) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_CURRENCY_MESSAGE;
			}

			// check validation (transactionStatus)
			elseif (!in_array($postData['transactionStatus'], $transactionStatus)) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_TRANSACTIONSTATUS_MESSAGE;
			}

			// check validation (if 01)
			elseif ($postData['transactionStatus'] == configHelper::PAYMENT_FLAG_DECLINED_01) {
				$data['paymentStatus'] = configHelper::PAYMENT_FLAG_DECLINED_01;
				$data['paymentMessage'] = configHelper::PAYMENT_FLAG_DECLINED_01_MESSAGE;
			}

			// check validation (if 02)
			elseif ($postData['transactionStatus'] == configHelper::PAYMENT_FLAG_DECLINED_02) {
				$data['paymentStatus'] = configHelper::PAYMENT_FLAG_DECLINED_02;
				$data['paymentMessage'] = configHelper::PAYMENT_FLAG_DECLINED_02_MESSAGE;
			}

			// check validation (if 03)
			elseif ($postData['transactionStatus'] == configHelper::PAYMENT_FLAG_DECLINED_03) {
				$data['paymentStatus'] = configHelper::PAYMENT_FLAG_DECLINED_03;
				$data['paymentMessage'] = configHelper::PAYMENT_FLAG_DECLINED_03_MESSAGE;
			}

			// check validation (if 04)
			elseif ($postData['transactionStatus'] == configHelper::PAYMENT_FLAG_DECLINED_04) {
				$data['paymentStatus'] = configHelper::PAYMENT_FLAG_DECLINED_04;
				$data['paymentMessage'] = configHelper::PAYMENT_FLAG_DECLINED_04_MESSAGE;
			}

			// check validation (if 05)
			elseif ($postData['transactionStatus'] == configHelper::PAYMENT_FLAG_DECLINED_05) {
				$data['paymentStatus'] = configHelper::PAYMENT_FLAG_DECLINED_05;
				$data['paymentMessage'] = configHelper::PAYMENT_FLAG_DECLINED_05_MESSAGE;
			}

			// check validation (if 06)
			elseif ($postData['transactionStatus'] == configHelper::PAYMENT_FLAG_DECLINED_06) {
				$data['paymentStatus'] = configHelper::PAYMENT_FLAG_DECLINED_06;
				$data['paymentMessage'] = configHelper::PAYMENT_FLAG_DECLINED_06_MESSAGE;
			}

			// check validation (customerAccount)
			elseif ($postData['customerAccount'] != $getByTransNo->getCustomerAccount()) {
				$data['paymentStatus'] = SprintResponseInterface::INVALID_CODE;
				$data['paymentMessage'] = SprintResponseInterface::INVALID_CUSTOMERACCOUNT_MESSAGE;
			}

			// check Double Payment
			elseif ($statusOrder == SprintResponseInterface::IN_PROCESS_ORDER) {
				$data['paymentStatus'] = SprintResponseInterface::DOUBLE_PAYMENT_CODE;
				$data['paymentMessage'] = SprintResponseInterface::DOUBLE_PAYMENT_MESSAGE;
			}

			// check validation (expired)
			elseif ($postData['transactionDate'] > $getByTransNo->getExpireDate()) {
				$data['paymentStatus'] = SprintResponseInterface::EXPIRED_CODE;
				$data['paymentMessage'] = SprintResponseInterface::EXPIRED_MESSAGE;
			}

			// check canceled by admin
			elseif ($statusOrder == SprintResponseInterface::CANCELED_ORDER) {
				$data['paymentStatus'] = SprintResponseInterface::CANCEL_BY_ADMIN_CODE;
				$data['paymentMessage'] = SprintResponseInterface::CANCEL_BY_ADMIN_MESSAGE;
			}

			else {
				$data['paymentStatus'] = SprintResponseInterface::SUCCESS_CODE;
				$data['paymentMessage'] = SprintResponseInterface::SUCCESS_TRANSACTION_MESSAGE;
				$is_success = true;
			}

			$data['flagType'] = $postData['flagType'];
			$data['paymentReffId'] = $postData['paymentReffId'];

			$result['data'] = $data;
		}
		
		$result['is_success'] = $is_success;

		return $result;
	}

	/**
	 * [createCsrfValidationException description]
	 * @param  RequestInterface $request
	 * @return mixing
	 */
	public function createCsrfValidationException(RequestInterface $request):  ? InvalidRequestException {
		return null;
	}

	/**
	 * [validateForCsrf description]
	 * @param  RequestInterface $request
	 * @return boolean
	 */
	public function validateForCsrf(RequestInterface $request) :  ? bool {
		return true;
	}
}
