<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Helper;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $datetime;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $timezone;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;

	/**
	 * @var \Trans\DigitalProduct\Logger\Logger
	 */
	protected $logger;

	/**
	 * @var \Trans\DigitalProduct\Helper\Config
	 */
	protected $config;

	/**
	 * @var \Trans\Integration\Helper\Curl
	 */
	protected $curlHelper;

	/**
	 * @param \Magento\Framework\App\Helper\Context                $context
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime          $datetime
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	 * @param \Magento\Framework\Serialize\Serializer\Json         $json
	 * @param \Trans\DigitalProduct\Logger\Logger                  $logger
	 * @param \Trans\DigitalProduct\Helper\Config                  $config
	 * @param \Trans\Integration\Helper\Curl                       $curlHelper
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $datetime,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Trans\DigitalProduct\Logger\Logger $logger,
		\Trans\DigitalProduct\Helper\Config $config,
		\Trans\DigitalProduct\Helper\Curl $curlHelper
	) {
		parent::__construct($context);

		$this->datetime   = $datetime;
		$this->timezone   = $timezone;
		$this->json       = $json;
		$this->logger     = $logger;
		$this->config     = $config;
		$this->curlHelper = $curlHelper;
	}

	/**
	 * Get config helper
	 *
	 * @return \Trans\DigitalProduct\Helper\Config
	 */
	public function getConfigHelper() {
		return $this->config;
	}

	/**
	 * Hit Altera API method Post
	 *
	 * @param array $data
	 * @return array
	 */
	public function doHitApiAlteraPost($data, $action, $path) {
		$this->logger->info('===== ' . $action . ' ===== Start');

		$userName  = $this->config->getUsername();
		$password  = $this->config->getSecret();
		$url       = $this->getApiUrl($action, $path);
		$datas     = $this->serializeJson($data);
		$basicAuth = base64_encode("{$userName}:{$password}");
		$headers   = [
			"User-Agent"    => $this->config->getUsername(),
			"Accept"        => "application/json",
			"Authorization" => "Basic {$basicAuth}",
			"Content-Type"  => "application/json",
		];

		$this->logger->info('URL = ' . $url);

		try {
			$responseJson = $this->curlHelper->post($url, $headers, $datas);
		} catch (\Exception $e) {
			$this->logger->info('HIT API ERROR = ' . $e->getMessage());
			return false;
		} catch (StateException $e) {
			$this->logger->info('HIT API ERROR = ' . $e->getMessage());
			return false;
		}

		$this->logger->info('RESPONSE = ' . $responseJson);

		$this->logger->info('===== ' . $action . ' ===== End');

		return $responseJson;
	}

	/**
	 * Hit Altera API method Post
	 *
	 * @param array $data
	 * @return array
	 */
	public function doHitApiAlteraGet($data, $action, $path) {
		$this->logger->info('===== ' . $action . ' ===== Start');

		$userName  = $this->config->getUsername();
		$password  = $this->config->getSecret();
		$url       = $this->getApiUrl($action, $path);
		$datas     = $this->serializeJson($data);
		$basicAuth = base64_encode("{$userName}:{$password}");
		$headers   = [
			"User-Agent"    => $this->config->getUsername(),
			"Accept"        => "application/json",
			"Authorization" => "Basic {$basicAuth}",
			"Content-Type"  => "application/json",
		];

		$this->logger->info('URL = ' . $url);

		try {
			$responseJson = $this->curlHelper->post($url, $headers, $datas);
		} catch (\Exception $e) {
			$this->logger->info('HIT API ERROR = ' . $e->getMessage());
			return false;
		} catch (StateException $e) {
			$this->logger->info('HIT API ERROR = ' . $e->getMessage());
			return false;
		}

		$this->logger->info('RESPONSE = ' . $responseJson);

		$this->logger->info('===== ' . $action . ' ===== End');

		return $responseJson;
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
	 * @return datetime
	 */
	public function getTodayTimezone() {
		return $this->timezone->date()->format('d-m-Y H:i:s');
	}

	/**
	 * Create json serialize
	 *
	 * @param array $data
	 * @return string | null
	 */
	public function serializeJson($data = null) {
		if (!empty($data)) {
			$data = $this->json->serialize($data);
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
			$data = $this->json->unserialize($data);
		}

		return $data;
	}

	/**
	 * get logger
	 *
	 * @return Trans\DigitalProduct\Logger\Logger
	 */
	public function getLogger() {
		return $this->logger;
	}

	public function getApiUrl($action, $path) {
		$apiBaseUrl = $this->config->getApiBaseUrl();
		// $apiUrl     = $this->getEndApiUrl($productId);

		return $apiBaseUrl . $action . $path;
	}

	// public function getEndApiUrl($productId) {
	// 	$urlResult  = Config::DEFAULT_URL_PRODUCT;
	// 	$productUrl = $this->config->getProductUrl();
	// 	$urls       = $this->unserializeJson($productUrl);
	// 	foreach ($urls as $url) {
	// 		if ((string) $productId === $url['product_id']) {
	// 			$urlResult = $url['product_url'];
	// 		}
	// 	}

	// 	return $urlResult;
	// }
}