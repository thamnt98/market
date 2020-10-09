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
namespace Trans\Sprint\Helper;

use Trans\Sprint\Helper\Config;

/**
 * Class Config
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * Payment currency
	 */
	const CURRENCY = 'IDR';

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $jsonSerialize;

	/**
	 * @var \Magento\Framework\HTTP\Client\Curl
	 */
	protected $curl;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $timezone;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \Trans\Sprint\Helper\Config
	 */
	protected $config;

	/**
	 * @var \Trans\Sprint\Logger\Logger
	 */
	protected $logger;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerialize
	 * @param \Magento\Framework\HTTP\Client\Curl $curl
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Trans\Sprint\Helper\Config $config
	 * @param \Trans\Sprint\Logger\Logger $logger
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Serialize\Serializer\Json $jsonSerialize,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		Config $config,
		\Trans\Sprint\Logger\Logger $logger,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
	) {
		$this->jsonSerialize               = $jsonSerialize;
		$this->curl                        = $curl;
		$this->timezone                    = $timezone;
		$this->storeManager                = $storeManager;
		$this->config                      = $config;
		$this->logger                      = $logger;
		$this->urlBuilder                  = $context->getUrlBuilder();
		$this->customerRepositoryInterface = $customerRepositoryInterface;

		parent::__construct($context);
	}

	/**
	 * Get Logger
	 *
	 * @return \Trans\Sprint\Logger\Logger
	 */
	public function getStoreManager() {
		return $this->storeManager;
	}

	/**
	 * Get Logger
	 *
	 * @return \Trans\Sprint\Logger\Logger
	 */
	public function getUrlBuilder() {
		return $this->urlBuilder;
	}

	/**
	 * Get Logger
	 *
	 * @return \Trans\Sprint\Logger\Logger
	 */
	public function getLogger() {
		return $this->logger;
	}

	/**
	 * Get config helper
	 *
	 * @return \Trans\Sprint\Helper\Config
	 */
	public function getConfigHelper() {
		return $this->config;
	}

	/**
	 * Get Magento Curl
	 *
	 * @return \Magento\Framework\HTTP\Client\Curl
	 */
	public function getCurl() {
		return $this->curl;
	}

	/**
	 * create words
	 *
	 * @param $data array
	 * @return string
	 */
	public function doAuthCode($data, $paymentMethod) {
		$this->logger->info('===== doAuthCode ===== Start');
		$result = '';

		$this->logger->info('PARAMS = ' . $this->serializeJson($data));

		if (!empty($data['transaction_no'])) {
			$result .= $data['transaction_no'];
		}

		if (!empty($data['transaction_amount'])) {
			$result .= $data['transaction_amount'];
		}

		if (!empty($data['channel_id'])) {
			$result .= $data['channel_id'];
		}

		if (!empty($data['transaction_status'])) {
			$result .= $data['transaction_status'];
		}

		if (!empty($data['insert_id'])) {
			$result .= $data['insert_id'];
		}

		$result .= $this->config->getSecretKey($paymentMethod);

		$this->logger->info('STRING = ' . $result);
		$hash = hash('sha256', $result);
		$this->logger->info('RESULT = ' . $hash);

		$this->logger->info('===== doAuthCode ===== End');
		return $hash;
	}

	/**
	 * Hit sprint payment API
	 *
	 * @param $data array
	 * @return json
	 */
	public function doHitApi($data, $action, $paymentMethod) {
		$this->logger->info('===== ' . $action . ' ===== Start');

		$url = $this->config->getApiUrl($action, $paymentMethod);
		$this->logger->info('URL = ' . $url);

		try {
			if ($this->config->isTimeoutEnable()) {
				$this->curl->setOption(CURLOPT_CONNECTTIMEOUT, 0);
				$this->curl->setTimeout($this->config->getTimeout());
			}
			$this->curl->post($url, $data);
		} catch (\Exception $e) {
			$this->logger->info('HIT API ERROR = ' . $e->getMessage());
			return false;
		}

		$responseJson = $this->curl->getBody();

		if (is_string($responseJson)) {
			$responseJson = $this->unserializeJson($responseJson);
		}

		$this->logger->info('RESPONSE = ' . $this->serializeJson($responseJson));

		$this->logger->info('===== ' . $action . ' ===== End');

		return $responseJson;
	}

	/**
	 * Get Description
	 *
	 * @param Magento\Sales\Model\Order
	 * @return string
	 */
	public function getDescription($order) {
		$payment     = $order->getPayment();
		$method      = $payment->getMethodInstance();
		$methodTitle = $method->getTitle();

		$channel = $this->config->getPaymentChannel($payment->getMethod());

		$result = $methodTitle . '.';

		if ($channel === Config::CREDIT_CARD_CHANNEL) {
			$term = $order->getSprintTermChannelid();
			if ($term) {
				$result .= ' Tenor cicilan ' . $term . ' bulan.';
			}
		}

		return $result;
	}

	/**
	 * convert datetime format
	 *
	 * @param $datetime string
	 * @return datetiime
	 */
	public function convertDatetime($datetime) {
		if ($datetime) {
			//Convert to store timezone
			$created = $this->timezone->date(new \DateTime($datetime));

			$dateAsString = $created->format('Y-m-d H:i:s');

			return $dateAsString;
		}
	}

	/**
	 * Get today timezone
	 *
	 * @param string $format
	 * @return datetime
	 */
	public function getTodayTimezone($format = 'd-m-Y H:i:s') {
		return $this->timezone->date()->format($format);
	}

	/**
	 * Get time difference
	 *
	 * @param datetime $startdata
	 * @param datetime $enddata
	 * @return float
	 */
	protected function differenceInHours($startdate, $enddate) {
		$starttimestamp = strtotime($startdate);
		$endtimestamp   = strtotime($enddate);
		$difference     = abs($endtimestamp - $starttimestamp) / 3600;

		return $difference;
	}

	/**
	 * Convert hours to minute
	 *
	 * @param float $hours
	 * @return float
	 */
	protected function convertHoursToMinute($hours) {
		return $hours * 60;
	}

	/**
	 * Create json serialize
	 *
	 * @param array $data
	 * @return string | null
	 */
	public function serializeJson($data = null) {
		if (!empty($data)) {
			$data = $this->jsonSerialize->serialize($data);
		}

		return $data;
	}

	/**
	 * decode json serialize
	 *
	 * @param string $data
	 * @return array
	 */
	public function unserializeJson($data = null) {
		if (!empty($data)) {
			$data = $this->jsonSerialize->unserialize($data);
		}

		return $data;
	}

	/**
	 * generate customer account for Virtual Account
	 *
	 * @param  int $paymentMethod
	 * @param  string $customerId
	 * @return string
	 */
	public function generateCustomerAccount($paymentMethod, $customerId) {
		$companyCode         = $this->config->getCompanyCode($paymentMethod);
		$customerData        = $this->customerRepositoryInterface->getById($customerId);
		$customerPhoneNumber = $customerData->getCustomAttribute('telephone')->getValue();
		$length              = 16 - (int) strlen($companyCode);
		$customeraccount     = sprintf('%0' . $length . 'd', $customerPhoneNumber);
		return $companyCode . $customeraccount;
	}
}
