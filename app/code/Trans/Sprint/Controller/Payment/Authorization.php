<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 * @author   Edi Suryadu <edi.suryadi@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Controller\Payment;

use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Trans\Sprint\Helper\Config;
use Trans\Sprint\Helper\Data;

/**
 * Class Authorization
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Authorization extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
	/**
	 * @var Magento\Checkout\Model\Session
	 */
	protected $session;

	/**
	 * @var Magento\Checkout\CustomerData\Cart
	 */
	protected $cart;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var \Magento\Sales\Model\Order
	 */
	protected $salesOrder;

	/**
	 * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
	 */
	protected $remoteAddress;

	/**
	 * @var \Trans\DokuPayment\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var UrlBuilder
	 */
	protected $urlBuilder;

	/**
	 * @var \Trans\Sprint\Api\Data\SprintResponseInterfaceFactory
	 */
	protected $sprintResponse;

	/**
	 * @var \Trans\Sprint\Api\SprintResponseRepositoryInterface
	 */
	protected $sprintRepository;

	/**
	 * @var \Trans\Sprint\Model\ResourceModel\SprintResponse
	 */
	protected $sprintResource;

	/**
	 * @var \Trans\Sprint\Helper\SalesOrder
	 */
	protected $orderHelper;

	/**
	 * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
	 */
	protected $orderSender;

	/**
	 * @param \Magento\Framework\App\Action\Context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	 * @param \Magento\Sales\Model\Order $salesOrder
	 * @param Cart $cart
	 * @param Session $session
	 * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
	 * @param \Trans\DokuPayment\Helper\Data $dataHelper
	 * @param \Trans\Sprint\Api\Data\SprintResponseInterfaceFactory $sprintResponse
	 * @param \Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResRepository
	 * @param \Trans\Sprint\Model\ResourceModel\SprintResponse $sprintResource
	 * @param \Trans\Sprint\Helper\SalesOrder $orderHelper
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Sales\Model\Order $salesOrder,
		Session $session,
		Cart $cart,
		\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
		Data $dataHelper,
		\Trans\Sprint\Api\Data\SprintResponseInterfaceFactory $sprintResponse,
		\Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintRepository,
		\Trans\Sprint\Model\ResourceModel\SprintResponse $sprintResource,
		\Trans\Sprint\Helper\SalesOrder $orderHelper,
		\Magento\Payment\Model\Config $paymentConfig,
		\Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
	) {
		parent::__construct($context);

		$this->resultJsonFactory = $resultJsonFactory;
		$this->salesOrder        = $salesOrder;
		$this->session           = $session;
		$this->cart              = $cart;
		$this->remoteAddress     = $remoteAddress;
		$this->dataHelper        = $dataHelper;
		$this->sprintResponse    = $sprintResponse;
		$this->sprintRepository  = $sprintRepository;
		$this->sprintResource = $sprintResource;
		$this->paymentConfig     = $paymentConfig;
		$this->orderHelper       = $orderHelper;
		$this->orderSender = $orderSender;

		$this->config     = $this->dataHelper->getConfigHelper();
		$this->logger     = $this->dataHelper->getLogger();
		$this->urlBuilder = $this->dataHelper->getUrlBuilder();
	}

	/**
	 * Execute insert transaction to sprint asia API
	 *
	 * @return json
	 */
	public function execute() {
		$this->logger->info('===== Authorization Controller ===== Start');

		$checkoutOrder = $this->session->getLastRealOrder();
		$params        = $this->getRequest()->getParams();

		$cardTokenUse = 'CREATETOKEN';
		$cardToken    = '';

		if ($params) {
			if (isset($params['card_token_use'])) {
				if ($params['card_token_use']) {
					$cardTokenUse = 'USETOKEN';
				}
			}

			if (isset($params['card_token'])) {
				$cardToken = $params['card_token'];
			}
		}

		$orderIncrementId = $checkoutOrder->getIncrementId();
		$expire           = $this->config->getExpiry();

		$order = $this->salesOrder->loadByIncrementId($orderIncrementId);
		$this->logger->info('additional information = ' . $this->dataHelper->serializeJson($order->getPayment()->getData()));

		$paymentMethod                = $order->getPayment()->getMethod();
		$paymentAdditionalInformation = $order->getPayment()->getAdditionalInformation();

		$refNumber = $this->sprintResource->getReferenceNumber($orderIncrementId);
		$this->logger->info('reference_number = ' . $refNumber);

		$transactionNo = $refNumber ? $refNumber : $orderIncrementId;
		$grandTotal    = $order->getGrandTotal();
		$serviceFee    = $this->getServiceFee($order);
		if ($refNumber) {
			$subOrder   = $this->orderHelper->getSubOrders($refNumber);
			$grandTotal = $this->orderHelper->getSubOrdersGrandTotal($subOrder);
			$serviceFee = $this->orderHelper->getSubOrdersServiceFee($subOrder);
		}

		$billingData = $order->getBillingAddress()->getData();
		if ($paymentMethod == 'sprint_mega_cc' || 'sprint_allbankfull_cc') {
			$globalFeature = $this->dataHelper->serializeJson(
				array(
					'tenor' => $order->getSprintTermChannelid(),
					'promoCode' => $this->config->getPromoCodePayment($paymentMethod),
				));
		} else {
			array();
		}
		$this->logger->info('Billing data = ' . $this->dataHelper->serializeJson($billingData));

		$orderDate = $this->dataHelper->convertDatetime($order->getCreatedAt());

		$dataPayment['channelId']           = $this->getChannelId($order);
		$dataPayment['currency']            = Data::CURRENCY;
		$dataPayment['transactionNo']       = $transactionNo;
		$dataPayment['transactionAmount']   = round($grandTotal, 2);
		$dataPayment['transactionFee']      = $serviceFee;
		$dataPayment['transactionDate']     = $orderDate;
		$dataPayment['transactionExpire']   = date('Y-m-d H:i:s', strtotime('+' . $expire . ' minutes', strtotime($dataPayment['transactionDate'])));
		$dataPayment['callbackURL']         = $this->urlBuilder->getUrl('checkout/onepage/success');
		$dataPayment['description']         = $this->dataHelper->getDescription($order);
		$dataPayment['customerAccount']     = $this->getCustomerAccount($order, $paymentMethod);
		$dataPayment['customerName']        = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
		$dataPayment['customerPhone']       = $billingData['telephone'];
		$dataPayment['customerEmail']       = $order->getCustomerEmail();
		$dataPayment['customerBillAddress'] = $billingData['street'] . ' ' . isset($billingData['district']);
		$dataPayment['customerBillCity']    = $billingData['city'];
		$dataPayment['customerBillState']   = $billingData['region'];
		$dataPayment['customerBillZipCode'] = $billingData['postcode'];
		$dataPayment['customerBillCountry'] = 'ID';
		$dataPayment['customerIp']          = $order->getRemoteIp();
		$dataPayment['authCode']            = $this->dataHelper->doAuthCode(array('channel_id' => $dataPayment['channelId'], 'transaction_no' => $dataPayment['transactionNo'], 'transaction_amount' => $dataPayment['transactionAmount']), $paymentMethod);
		$dataPayment['cardTokenUse']        = $cardTokenUse;
		$dataPayment['cardToken']           = $cardToken;
		$dataPayment['serviceCode']         = $this->config->getPaymentChannelServicecode($paymentMethod);
		$dataPayment['transactionFeature']  = $globalFeature;

		$this->logger->info('----> Transaction Featured -----> = ' . $globalFeature);
		$this->logger->info('Data Payment = ' . $this->dataHelper->serializeJson($dataPayment));

		$hit = $this->hitApi($dataPayment, $paymentMethod);

		if ($hit != false) {
			$counter = 1;
			while ($hit['insertStatus'] != '00' && strpos($hit['insertMessage'], 'Exist') !== false) {
				$dataPayment['transactionNo'] = $orderIncrementId . '-' . $counter;
				$dataPayment['authCode']      = $this->dataHelper->doAuthCode(array('channel_id' => $dataPayment['channelId'], 'transaction_no' => $dataPayment['transactionNo'], 'transaction_amount' => $dataPayment['transactionAmount']), $paymentMethod);
				$order->setSprintTransactionNo($dataPayment['transactionNo']);
				$order->save();
				$hit = $this->hitApi($dataPayment, $paymentMethod);
				$counter++;
			}
			if ($hit['insertStatus'] == '00') {
				$this->session->setIsRedirectedToSprint(true);
			}
			$this->saveResponse($order, $dataPayment, $hit);

			if($paymentMethod == 'sprint_bca_va' && $dataPayment['customerAccount'] && $dataPayment['transactionExpire']){
				$this->orderSender->send($order, true);
			}
		}

		$this->logger->info('===== Authorization Controller ===== End');
		$result = $this->resultJsonFactory->create();
		return $result->setData($hit);
	}

	/**
	 * Get service fee
	 *
	 * @param Magento\Sales\Api\Data\OrderInterface $order
	 * @return int
	 */
	protected function getServiceFee($order) {
		$serviceFee = 0;

		if ($order->getData('service_fee')) {
			$serviceFee = $order->getData('service_fee');
		}

		return (int) $serviceFee;
	}

	/**
	 * Hit API and retry 3 times if timeout
	 *
	 * @param array $dataPayment
	 * @param string $paymentMethod
	 * @return array | bool
	 */
	protected function hitApi($dataPayment, $paymentMethod) {
		$try = 1;
		$hit = false;
		while ($hit == false && $try <= 3) {
			$this->logger->info('Try hit API ' . $try);
			$this->logger->info('Data Payment = ' . $this->dataHelper->serializeJson($dataPayment));
			$hit = $this->dataHelper->doHitApi($dataPayment, Config::PAYMENT_REGISTER_URL, $paymentMethod);
			$this->logger->info('$hit = ' . $this->dataHelper->serializeJson($hit));
			$try++;
		}

		return $hit;
	}

	/**
	 * Save API response
	 *
	 * @param Magento\Sales\Model\Order $order
	 * @param json $response
	 */
	protected function saveResponse($order, $dataPayment, $response) {
		$this->logger->info('Save Response Start');
		$orderIncrementId = $order->getIncrementId();
		$sprintResponse   = $this->sprintRepository->getByTransactionNo($orderIncrementId);

		$this->logger->info($this->dataHelper->serializeJson($response));

		if ($order && $response) {
			try {
				$data['store_id']         = $this->session->getQuote()->getStoreId();
				$data['quote_id']         = $order->getQuoteId();
				$data['channel_id']       = $this->getChannelId($order);
				$data['transaction_no'] = $dataPayment['transactionNo'];
				$data['currency']         = $response['currency'];
				$data['insert_status']    = $response['insertStatus'];
				$data['insert_message']   = $response['insertMessage'];
				$data['insert_id']        = isset($response['insertId']) ? $response['insertId'] : '';
				$data['redirect_url']     = isset($response['redirectURL']) ? $response['redirectURL'] : null;
				$data['redirect_data']    = isset($response['redirectData']) ? json_encode($response['redirectData']) : null;
				$data['additional_data']  = isset($response['additionalData']) ? $this->dataHelper->serializeJson($response['additionalData']) : null;
				$data['payment_method']   = $order->getPayment()->getMethod();
				$data['customer_account'] = $dataPayment['customerAccount'];
				$data['insert_date']      = $dataPayment['transactionDate'];
				$data['expire_date']      = $dataPayment['transactionExpire'];
				$data['flag']             = 'pending';
				$this->logger->info('Data to save : ' . $this->dataHelper->serializeJson($data));
				$sprintResponse->addData($data);
				$this->sprintRepository->save($sprintResponse);
			} catch (\Exception $e) {
				$this->logger->info('Generate error ' . $e->getMessage());
				return false;
			}
		}

		$this->logger->info('Save Response End');
		return true;
	}

	/**
	 * Get payment channel id
	 *
	 * @param Magento\Sales\Model\Order
	 * @return string
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	protected function getChannelId($order) {
		$payment       = $order->getPayment();
		$paymentMethod = $payment->getMethod();
		$this->logger->info('$paymentMethod = ' . $paymentMethod);

		$channel = $this->config->getPaymentChannel($paymentMethod);
		$this->logger->info('$channel = ' . $channel);

		$condition = $channel === Config::CREDIT_CARD_CHANNEL;

		switch ($condition) {
		case true:
			$term = $order->getSprintTermChannelid();
			if (!$term) {
				$additionalInformation = $payment->getAdditionalInformation();
				if (isset($additionalInformation['tenor'])) {$term = $additionalInformation['tenor'];}
			}

			$installmentTerm = $this->config->getInstallmentTerm($paymentMethod);
			$terms           = $this->dataHelper->unserializeJson($installmentTerm);

			$channelId = $this->config->getPaymentChannelId($paymentMethod);
			if (!empty($terms) && is_array($terms)) {
				foreach ($terms as $key => $value) {

					if ($value['term'] === $term) {

						$channelId = $value['channelId'];
					}
				}
			}

			break;

		case false:
			$channelId = $this->config->getPaymentChannelId($paymentMethod);
			break;
		}

		$this->logger->info('$channelId = ' . $channelId);
		return $channelId;
	}

	protected function getCustomerAccount($order, $paymentMethod) {
		$channel         = $this->config->getPaymentChannel($paymentMethod);
		$customerAccount = substr(str_replace(' ', '', $order->getCustomerId() . $order->getCustomerFirstname() . $order->getCustomerLastname()), 0, 50);

		if ($channel === Config::VIRTUAL_ACCOUNT_CHANNEL) {
			$customerAccount = $this->dataHelper->generateCustomerAccount($paymentMethod, $order->getCustomerId());
		}

		return $customerAccount;
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
