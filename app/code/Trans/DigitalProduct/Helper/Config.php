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
namespace Trans\DigitalProduct\Helper;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * Digital Product config path
	 */
	const ENABLE_MODULE  = 'digitalproduct/altera/active';
	const IS_PRODUCTION  = 'digitalproduct/altera/is_production';
	const ENABLE_TIMEOUT = 'digitalproduct/altera/enable_timeout';
	const EXPIRY         = 'digitalproduct/altera/expiry';
	const TIMEOUT        = 'digitalproduct/altera/timeout';

	/**
	 * API URL
	 */
	const BASE_URL_STAGING = 'digitalproduct/altera/base_url_staging';
	const BASE_URL_PROD    = 'digitalproduct/altera/base_url_production';
	const URL_PRODUCT      = 'digitalproduct/altera/url_product';

	/**
	 * Constant Path URL
	 */
	const URL_PATH_BPJS_KESEHATAN       = '/bpjs_kesehatan.json';
	const URL_PATH_ELECTRICITY          = '/electricity.json';
	const URL_PATH_ELECTRICITY_POSTPAID = '/electricity_postpaid.json';
	const URL_PATH_TELKOM_POSTPAID      = '/telkom_postpaid.json';
	const URL_PATH_PDAM                 = '/pdam.json';
	const URL_PATH_MOBILE_POSTPAID      = '/mobile_postpaid.json';
	const URL_PATH_MOBILE               = '/mobile.json';

	/**
	 * Credential
	 */
	const USERNAME_STAGING = 'digitalproduct/altera/username_staging';
	const USERNAME_PROD    = 'digitalproduct/altera/username_production';
	const SECRET_STAGING   = 'digitalproduct/altera/secret_staging';
	const SECRET_PROD      = 'digitalproduct/altera/secret_production';

	/**
	 * Action
	 */
	const ACTION_INQUIRE     = 'inquire';
	const ACTION_TRANSACTION = 'transaction';
	const ACTION_OPERATOR    = 'operator';

	/**
	 * get config value
	 *
	 * @param string $path
	 * @return string
	 */
	public function getConfigValue($path) {
		if ($path) {
			return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		}
	}

	/**
	 * is payment enable
	 *
	 * @return bool
	 */
	public function isEnable() {
		return $this->scopeConfig->getValue(self::ENABLE_MODULE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * is production
	 *
	 * @return bool
	 */
	public function isProduction() {
		return $this->scopeConfig->getValue(self::IS_PRODUCTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get api url
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getApiBaseUrl() {

		switch ($this->isProduction()) {
		case true:
			$url = self::BASE_URL_PROD;
			break;

		default:
			$url = self::BASE_URL_STAGING;
			break;
		}

		return $this->scopeConfig->getValue($url, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get api url
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getUsername() {

		switch ($this->isProduction()) {
		case true:
			$url = self::USERNAME_PROD;
			break;

		default:
			$url = self::USERNAME_STAGING;
			break;
		}

		return $this->scopeConfig->getValue($url, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get api url
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getSecret() {

		switch ($this->isProduction()) {
		case true:
			$url = self::SECRET_PROD;
			break;

		default:
			$url = self::SECRET_STAGING;
			break;
		}

		return $this->scopeConfig->getValue($url, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get expiry
	 *
	 * @return string
	 */
	public function getProductUrl() {
		return $this->scopeConfig->getValue(self::URL_PRODUCT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get expiry
	 *
	 * @return string
	 */
	public function getExpiry() {
		return $this->scopeConfig->getValue(self::EXPIRY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get timeout
	 *
	 * @return string
	 */
	public function getTimeout() {
		return $this->scopeConfig->getValue(self::TIMEOUT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * is timeout enable
	 *
	 * @return bool
	 */
	public function isTimeoutEnable() {
		return $this->scopeConfig->getValue(self::ENABLE_TIMEOUT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}
