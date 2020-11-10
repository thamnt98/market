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

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * Sprint payment config path
	 */
	const ENABLE_MODULE               = 'payment/sprint/active';
	const IS_PRODUCTION               = 'payment/sprint/is_production';
	const CHANNEL_ID                  = 'payment/sprint/channel_id';
	const SECRET_KEY_STAGING          = 'payment/sprint/secret_key_staging';
	const SECRET_KEY_PROD             = 'payment/sprint/secret_key_prod';
	const CC_SECRET_KEY_STAGING       = 'payment/sprint/cc_secret_key_staging';
	const CC_SECRET_KEY_PROD          = 'payment/sprint/cc_secret_key_prod';
	const EXPIRY                      = 'payment/sprint/expiry';
	const ENABLE_TIMEOUT              = 'payment/sprint/enable_timeout';
	const TIMEOUT                     = 'payment/sprint/timeout';
	const NEW_ORDER_STATUS            = 'order_status';
	const PAYMENT_CHANNEL             = 'payment_channel';
	const PAYMENT_CHANNEL_NAME        = 'title';
	const PAYMENT_CHANNEL_ENVIRONMENT = 'environment';
	const INSTALLMENT_TERM            = 'term_channel_id';
	const PAYMENT_CHANNEL_ID          = 'channel_id';
	const CHANNEL_SERVICE_CODE        = 'service_code';
	const CHANNEL_REFUND_SERVICE_CODE = 'refund_service_code';
	const CHANNEL_INSERT_URL          = 'insert_transaction_url';
	const PAYMENT_CHANNEL_IPG         = 'ipg';
	const COMPANY_CODE                = 'company_code';
	const PAYMENT_HOWTOPAY            = 'howtopay';
	const PROMO_CODE                  = 'promo_code';

	/**
	 * Payment Notify URL
	 */
	const NOTIFY_URL = 'sprint/payment/flag';

	/**
	 * Credit card channel
	 */
	const CREDIT_CARD_CHANNEL     = 'credit_card';
	const VIRTUAL_ACCOUNT_CHANNEL = 'virtual_account';

	/**
	 * payment insert success flag
	 */
	const INSERT_SUCCESS_CODE = '00';

	/**
	 * payment flag status
	 */
	const PAYMENT_FLAG_SUCCESS_CODE = '00'; //Approved
	const PAYMENT_FLAG_DECLINED_01  = '01'; //General Reason
	const PAYMENT_FLAG_DECLINED_02  = '02'; //Has been paid
	const PAYMENT_FLAG_DECLINED_03  = '03'; //Invalid Parameter
	const PAYMENT_FLAG_DECLINED_04  = '04'; //Transaction expired
	const PAYMENT_FLAG_DECLINED_05  = '05'; //Transaction canceled
	const PAYMENT_FLAG_DECLINED_06  = '06'; //Incorect VA Number

	/**
	 * payment flag meesage
	 */
	const PAYMENT_FLAG_DECLINED_01_MESSAGE = 'Declined (General reason)'; //General Reason
	const PAYMENT_FLAG_DECLINED_02_MESSAGE = 'Declined (Transaction has been paid)'; //Has been paid
	const PAYMENT_FLAG_DECLINED_03_MESSAGE = 'Declined (Invalid Parameter)'; //Invalid Parameter
	const PAYMENT_FLAG_DECLINED_04_MESSAGE = 'Declined (Transaction Expired)'; //Transaction expired
	const PAYMENT_FLAG_DECLINED_05_MESSAGE = 'Declined (Transaction Cancelled)'; //Transaction canceled
	const PAYMENT_FLAG_DECLINED_06_MESSAGE = 'Declined (Incorrect Virtual Account Numbe)'; //Incorect VA Number

	/**
	 * transaction status from PaymentQuery API
	 */
	const TRANSACTION_STATUS_SUCCESS     = '00'; //Success
	const TRANSACTION_STATUS_DECLINED    = '01'; //Declined
	const TRANSACTION_STATUS_NOTFOUND    = '02'; //transaction not found
	const TRANSACTION_STATUS_NOPAYMENT   = '03'; //There is no payment for this transaction
	const TRANSACTION_STATUS_TECHPROBLEM = '04'; //Technical Problem

	/**
	 * API Baseurl
	 */
	const DEFAULT_PRODUCTION_BASE_URL  = 'payment/sprint/base_url_api_prod_default';
	const DEFAULT_DEVELOPMENT_BASE_URL = 'payment/sprint/base_url_api_dev_default';
	const PAID_ORDER_STATUS            = 'payment/sprint/paidorder';
	const GENERAL_NEW_ORDER_STATUS     = 'payment/sprint/order_status';
	const CC_PRODUCTION_BASE_URL       = 'payment/sprint/base_url_api_prod_cc';
	const CC_DEVELOPMENT_BASE_URL      = 'payment/sprint/base_url_api_dev_cc';

	/**
	 * API Constant URL
	 */
	const PAYMENT_REGISTER_URL = 'PaymentRegister';
	const AUTH_URL             = 'PostAuth';
	const CHECK_STATUS_PAYMENT = 'PaymentQuery';
	const REFUND_POST_URL      = 'PostPayment';

	/**
	 * API Environment
	 */
	const PRODUCTION  = 'production';
	const DEVELOPMENT = 'development';
	const STAGING     = 'staging';

	/**
	 * OMS Status
	 */
	const OMS_CANCEL_PAYMENT_ORDER   = '99';
	const OMS_SUCCESS_PAYMENT_OPRDER = '2';

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
	 * Get merchant id
	 *
	 * @return string
	 */
	public function getChannelId() {
		return $this->scopeConfig->getValue(self::CHANNEL_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get paid state
	 *
	 * @return string
	 */
	public function getPaidState() {
		return $this->scopeConfig->getValue(self::PAID_ORDER_STATUS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get secret key
	 *
	 * @return string $paymentMethod
	 * @return string
	 */
	public function getSecretKey($paymentMethod) {

		switch ($this->isProduction()) {
		case "1":
			$base = self::SECRET_KEY_PROD;
			break;

		default:
			$base = self::SECRET_KEY_STAGING;
			break;
		}

		$channel = $this->getPaymentChannel($paymentMethod);

		if ($channel === 'credit_card' || $paymentMethod == 'sprint_bcafullpayment_cc' || $paymentMethod == 'sprint_allbankfull_cc' || $paymentMethod == 'sprint_mega_cc') {
			switch ($this->isProduction()) {
			case "1":
				$base = self::CC_SECRET_KEY_PROD;
				break;

			default:
				$base = self::CC_SECRET_KEY_STAGING;
				break;
			}
		}

		return $this->scopeConfig->getValue($base, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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

	/**
	 * Get payment channel new order status
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getChannelNewOrderStatus($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::NEW_ORDER_STATUS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get new order status
	 *
	 * @return string | NULL
	 */
	public function getNewOrderStatus() {
		return $this->scopeConfig->getValue(self::GENERAL_NEW_ORDER_STATUS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get environtment
	 *
	 * @return string
	 */
	public function getEnvironment($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::PAYMENT_CHANNEL_ENVIRONMENT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get channel id
	 *
	 * @return string
	 */
	public function getPaymentChannelId($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::PAYMENT_CHANNEL_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get payment channel
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getPaymentChannel($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::PAYMENT_CHANNEL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get installment term
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getInstallmentTerm($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::INSTALLMENT_TERM, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get payment channel name
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getPaymentChannelName($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::PAYMENT_CHANNEL_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get payment channel service code
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getPaymentChannelServicecode($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::CHANNEL_SERVICE_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get payment channel refund service code
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getPaymentChannelRefundServicecode($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::CHANNEL_REFUND_SERVICE_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get Payment Promo Code
	 *
	 * @param string $paymentcode
	 * @return string|null
	 */
	public function getPromoCodePayment($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::PROMO_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get payment channel ipg
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getPaymentChannelIpg($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::PAYMENT_CHANNEL_IPG, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get payment channel insert url
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getPaymentChannelInsertUrl($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::CHANNEL_INSERT_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get company code
	 *
	 * @param $paymentcode string
	 * @return string | NULL
	 */
	public function getCompanyCode($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::COMPANY_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get payment channel 'how to pay'
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getHowtopay($paymentcode) {
		return $this->scopeConfig->getValue('payment/' . $paymentcode . '/' . self::PAYMENT_HOWTOPAY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get api base url
	 *
	 * @param $code string
	 * @return string | NULL
	 */
	public function getApiBaseUrl($paymentMethod) {
		$channel = $this->getPaymentChannel($paymentMethod);

		switch ($this->isProduction()) {
		case true:
			$base = self::DEFAULT_PRODUCTION_BASE_URL;
			break;

		default:
			$base = self::DEFAULT_DEVELOPMENT_BASE_URL;
			break;
		}

		if ($channel === 'credit_card' || $paymentMethod == 'sprint_bcafullpayment_cc' || $paymentMethod == 'sprint_allbankfull_cc' || $paymentMethod == 'sprint_mega_cc' || $paymentMethod == 'sprint_allbank_debit' || $paymentMethod == 'sprint_mega_debit' || $paymentMethod == 'sprint_bca_va' || $paymentMethod == 'sprint_permata_va') {
			switch ($this->isProduction()) {
			case true:
				$base = self::CC_PRODUCTION_BASE_URL;
				break;

			default:
				$base = self::CC_DEVELOPMENT_BASE_URL;
				break;
			}
		}

		return $this->scopeConfig->getValue($base, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get api URL
	 *
	 * @param string $path
	 * @return string
	 */
	public function getApiUrl($path = '', $paymentMethod) {
		$baseUrl = $this->getApiBaseUrl($paymentMethod);

		return $baseUrl . '/' . $path;
	}
}
