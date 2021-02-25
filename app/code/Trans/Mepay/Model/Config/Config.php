<?php
/**
 * @category Trans
 * @package  Trans_MgPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
 namespace Trans\Mepay\Model\Config;

 use Magento\Framework\App\Config\ScopeConfigInterface;
 use Magento\Store\Model\ScopeInterface;
 use Trans\Mepay\Model\Config\Source\Environment;
 use Trans\Mepay\Model\Config\Provider\Mepay;
 use Trans\Mepay\Model\Config\Provider\Cc;
 use Trans\Mepay\Model\Config\Provider\Va;
 use Trans\Mepay\Model\Config\Provider\Qris;
 use Trans\Mepay\Model\Config\Provider\Debit;
 use Trans\Mepay\Model\Config\Provider\CcDebit;
 use Trans\Mepay\Model\Config\Provider\AllbankCc;
 use Trans\Mepay\Model\Config\Provider\AllbankDebit;
 use Trans\Mepay\Logger\Logger;

 class Config
 {
   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_ACTIVE = 'payment/trans_mepay/active';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_ENVIRONMENT = 'payment/trans_mepay/environment';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_MERCHANT_HOST = 'payment/trans_mepay/merchant_host';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_API_KEY = 'payment/trans_mepay/api_key';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_SECRET_KEY = 'payment/trans_mepay/secret_key';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_DEV_BASE_URL = 'payment/trans_mepay/dev_base_url';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_PROD_BASE_URL = 'payment/trans_mepay/prod_base_url';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_MERCHANT_REFERENCE_URL = 'payment/trans_mepay/merchant_reference_url';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_INQUIRY_URL = 'payment/trans_mepay/inquiry_url';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_IS_AUTHCAPTURE = 'payment/trans_mepay/is_authcapture';

   /**
    * @var  string
    */
   const PAYMENT_TRANS_MEPAY_STATUS_PULL_FORMAT = 'payment/trans_mepay/status_pull_format';

    /**
    * @var  string
    */
   CONST PAYMENT_TRANS_MEPAY_STATUS_PULL_DYNAMIC = 'payment/trans_mepay/status_pull_dynamic';

  /**
    * @var  string
    */
   const PAYMENT_TRANS_MEPAY_STATUS_CAPTURE_INIT_URL = 'payment/trans_mepay/status_capture_init_url';

    /**
    * @var  string
    */
   const PAYMENT_TRANS_MEPAY_STATUS_CAPTURE_INIT = 'payment/trans_mepay/status_capture_init';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_DEBUG = 'payment/trans_mepay/debug';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_CC_ACTIVE = 'payment/trans_mepay_cc/active';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_CC_TITLE = 'payment/trans_mepay_cc/title';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_CC_ORDER_STATUS = 'payment/trans_mepay_cc/order_status';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_CC_ORDER_STATE = 'payment/trans_mepay_cc/order_state';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_VA_ACTIVE = 'payment/trans_mepay_va/active';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_VA_TITLE = 'payment/trans_mepay_va/title';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_VA_ORDER_STATUS = 'payment/trans_mepay_va/order_status';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_VA_ORDER_STATE = 'payment/trans_mepay_va/order_state';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_QRIS_ACTIVE = 'payment/trans_mepay_qris/active';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_QRIS_TITLE = 'payment/trans_mepay_qris/title';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_QRIS_ORDER_STATUS = 'payment/trans_mepay_qris/order_status';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_QRIS_ORDER_STATE = 'payment/trans_mepay_qris/order_state';

  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_DEBIT_ACTIVE = 'payment/trans_mepay_debit/active';

  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_DEBIT_TITLE = 'payment/trans_mepay_debit/title';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_DEBIT_ORDER_STATUS = 'payment/trans_mepay_debit/order_status';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_DEBIT_ORDER_STATE = 'payment/trans_mepay_debit/order_state';

   /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_ACTIVE = 'payment/trans_mepay_allbankccdebit/active';

  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_TITLE = 'payment/trans_mepay_allbankccdebit/title';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_ORDER_STATUS = 'payment/trans_mepay_allbankccdebit/order_status';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_ORDER_STATE = 'payment/trans_mepay_allbankccdebit/order_state';

  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCC_ACTIVE = 'payment/trans_mepay_allbank_cc/active';

  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCC_TITLE = 'payment/trans_mepay_allbank_cc/title';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCC_ORDER_STATUS = 'payment/trans_mepay_allbank_cc/order_status';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKCC_ORDER_STATE = 'payment/trans_mepay_allbank_cc/order_state';

  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_ACTIVE = 'payment/trans_mepay_allbank_debit/active';

  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_TITLE = 'payment/trans_mepay_allbank_debit/title';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_ORDER_STATUS = 'payment/trans_mepay_allbank_debit/order_status';
 
  /**
   * @var string
   */
  const PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_ORDER_STATE = 'payment/trans_mepay_allbank_debit/order_state';

   /**
    * @var string
    */
   const PAYMENT_TRANS_MEPAY_CONTENT_TYPE = 'payment/trans_mepay/content_type';

   /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Trans\Mepay\Logger\Logger
     */
    protected $logger;

    /**
     * Constructor
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger               $logger
     */
    public function __construct(
      ScopeConfigInterface $scopeConfig,
      Logger $logger
    ) {
      $this->scopeConfig = $scopeConfig;
      $this->logger = $logger;
    }

    /**
     * Get Value
     * @param  string $path
     * @return string
     */
    public function getValue($path)
    {
      return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is active
     * @param  string  $paymentCode
     * @return boolean
     */
    public function isActive($paymentCode = '')
    {
      switch($paymentCode) {
        case Mepay::CODE : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_ACTIVE);
          break;
        case Cc::CODE_CC : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_CC_ACTIVE);
          break;
        case CcDebit::CODE : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_ACTIVE);
          break;
        case Va::CODE_VA : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_VA_ACTIVE);
          break;
        case Qris::CODE_QRIS : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_QRIS_ACTIVE);
          break;
        case Debit::CODE : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_DEBIT_ACTIVE);
          break;
        case AllbankCc::CODE : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCC_ACTIVE);
          break;
        case AllbankDebit::CODE : return (bool) $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_ACTIVE);
          break;
      }
      return false;
    }

    /**
     * Get environment
     * @return string
     */
    public function getEnvironment()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_ENVIRONMENT);
    }

    /**
     * Get merchant host
     * @return string
     */
    public function getMerchantHost()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_MERCHANT_HOST);
    }

    /**
     * Get secret key
     * @return string
     */
    public function getApiKey()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_API_KEY);
    }

    /**
     * Get secret key
     * @return string
     */
    public function getSecretKey()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_SECRET_KEY);
    }

    /**
     * Get dev base url
     * @return string
     */
    public function getDevBaseUrl()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_DEV_BASE_URL);
    }

    /**
     * Get prod base url
     * @return string
     */
    public function getProdBaseUrl()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_PROD_BASE_URL);
    }

    /**
     * Get merchant reference url
     * @return string
     */
    public function getMerchantReferenceUrl()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_MERCHANT_REFERENCE_URL);
    }

    /**
     * Get Inquiry Url
     * @return string
     */
    public function getInquiryUrl()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_INQUIRY_URL);
    }

    /**
     * Get Is Auth Capture
     * @return string
     */
    public function getIsAuthCapture()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_IS_AUTHCAPTURE);
    }

    /**
     * Get status pull format
     * @return string
     */
    public function getStatusPullFormat()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_STATUS_PULL_FORMAT);
    }

    /**
     * Get status pull dynamic
     * @return string
     */
    public function getStatusPullDynamic()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_STATUS_PULL_DYNAMIC);
    }

    /**
     * Get status capture init url
     * @return string
     */
    public function getStatusCaptureInitUrl()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_STATUS_CAPTURE_INIT_URL);
    }

    /**
     * Get status capture init
     * @return string
     */
    public function getStatusCaptureInit()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_STATUS_CAPTURE_INIT);
    }

    /**
     * Get Content Type
     * @return string
     */
    public function getContentType()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_CONTENT_TYPE);
    }

    /**
     * Is debug
     * @return boolean
     */
    public function isDebug()
    {
      return $this->getValue(self::PAYMENT_TRANS_MEPAY_DEBUG);
    }

    /**
     * Get title
     * @param  string $paymentCode
     * @return string
     */
    public function getTitle($paymentCode = '')
    {
      $title = '';
      switch($paymentCode) {
        case Cc::CODE_CC : $title = $this->getValue(self::PAYMENT_TRANS_MEPAY_CC_TITLE);
          break;
        case Va::CODE_VA : $title = $this->getValue(self::PAYMENT_TRANS_MEPAY_VA_TITLE);
          break;
        case Qris::CODE_QRIS : $title = $this->getValue(self::PAYMENT_TRANS_MEPAY_QRIS_TITLE);
          break;
        case Debit::CODE : $title = $this->getValue(self::PAYMENT_TRANS_MEPAY_DEBIT_TITLE);
          break;
        case CcDebit::CODE : $title = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_TITLE);
          break;
        case AllbankCc::CODE : $title = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCC_TITLE);
          break;
        case AllbankDebit::CODE : $title = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_TITLE);
          break;
      }
      return $title;
    }

    /**
     * Get order status
     * @param  string $paymentCode
     * @return string
     */
    public function getOrderStatus($paymentCode)
    {
      $status = '';
      switch($paymentCode) {
        case Cc::CODE_CC : $status = $this->getValue(self::PAYMENT_TRANS_MEPAY_CC_ORDER_STATUS);
          break;
        case Va::CODE_VA : $status = $this->getValue(self::PAYMENT_TRANS_MEPAY_VA_ORDER_STATUS);
          break;
        case Qris::CODE_QRIS : $status = $this->getValue(self::PAYMENT_TRANS_MEPAY_QRIS_ORDER_STATUS);
          break;
        case Debit::CODE : $status = $this->getValue(self::PAYMENT_TRANS_MEPAY_DEBIT_ORDER_STATUS);
          break;
        case CcDebit::CODE : $status = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_ORDER_STATUS);
          break;
        case AllbankCc::CODE : $status = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCC_ORDER_STATUS);
          break;
        case AllbankDebit::CODE : $status = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_ORDER_STATUS);
          break;
      }
      return $status;
    }

    /**
     * Get order state
     * @param  string $paymentCode
     * @return string
     */
    public function getOrderState($paymentCode)
    {
      $state = '';
      switch($paymentCode) {
        case Cc::CODE_CC : $state = $this->getValue(self::PAYMENT_TRANS_MEPAY_CC_ORDER_STATE);
          break;
        case Va::CODE_VA : $state = $this->getValue(self::PAYMENT_TRANS_MEPAY_VA_ORDER_STATE);
          break;
        case Qris::CODE_QRIS : $state = $this->getValue(self::PAYMENT_TRANS_MEPAY_QRIS_ORDER_STATE);
          break;
        case Debit::CODE : $state = $this->getValue(self::PAYMENT_TRANS_MEPAY_DEBIT_ORDER_STATE);
          break;
        case CcDebit::CODE : $state = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCCDEBIT_ORDER_STATE);
          break;
        case AllbankCc::CODE : $state = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKCC_ORDER_STATE);
          break;
        case AllbankDebit::CODE : $state = $this->getValue(self::PAYMENT_TRANS_MEPAY_ALLBANKDEBIT_ORDER_STATE);
          break;
      }
      return $state;
    }

    /**
     * Get endpoint desc
     * @return string
     */
    public function getEndpointUri()
    {
      if($this->getEnvironment() == Environment::ENVIRONMENT_PRODUCTION) {
        return $this->getProdBaseUrl().$this->getInquiryUrl();
      }
      return $this->getDevBaseUrl().$this->getInquiryUrl();
    }

    /**
     * Get status pull url
     * @param  string $param
     * @return string]
     */
    public function getStatusPullUrl($param)
    {
      $url = $this->getStatusPullFormat();
      $dynamicUrl = $this->getStatusPullDynamic();
      $pullUrl = str_replace($dynamicUrl, $param, $url);
      if($this->getEnvironment() == Environment::ENVIRONMENT_PRODUCTION) {
        return $this->getProdBaseUrl().$pullUrl;
      }
      return $this->getDevBaseUrl().$pullUrl;
    }

    /**
     * Get capture Url
     * @param   $inquiryId
     * @param   $transactionId
     * @return  string
     */
    public function getStatusCaptureUrl($inquiryId, $transactionId)
    {
      $sections = explode(';', $this->getStatusCaptureInit());
      $secIqy = (isset($sections[0]))? $sections[0] : '';
      $secTxn = (isset($sections[1]))? $sections[1] : '';
      $captureUrl = str_replace($secIqy, $inquiryId, $this->getStatusCaptureInitUrl());
      $captureUrl = str_replace($secTxn, $transactionId, $captureUrl);
      if($this->getEnvironment() == Environment::ENVIRONMENT_PRODUCTION) {
        return $this->getProdBaseUrl().$captureUrl;
      }
      return $this->getDevBaseUrl().$captureUrl;
    }
 }
