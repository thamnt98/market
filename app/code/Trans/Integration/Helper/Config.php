<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Trans\Integration\Model\Config\Source\Env;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constant config path
     * Centralize Magento
     */
    const CENTRALIZE_API_URL_DEV = 'integration/centralize/api_url_dev';
    const CENTRALIZE_API_URL_PROD = 'integration/centralize/api_url_prod';
    const CENTRALIZE_API_ENV = 'integration/centralize/centralize_env';
    const CENTRALIZE_ENABLE_STATIC_OTP = 'integration/centralize/enabled_static_otp';
    const CENTRALIZE_STATIC_OTP = 'integration/centralize/static_otp_code';
    const CENTRALIZE_API_KEY = 'integration/centralize/api_key';
    const ENABLE_SMS_VERIFICATION = 'integration/centralize/enabled_sms_verification';
    const CENTRALIZE_URL_SEND_UPDATE_CUSTOMER = 'integration/centralize/customer_update_integration';
    const CENTRALIZE_URL_SEND_NEW_CUSTOMER = 'integration/centralize/customer_new_register';
    const CENTRALIZE_URL_SEND_VERIFICATION = 'integration/centralize/send_sms_verifcation';
    const CENTRALIZE_URL_VERIFY_VERIFICATION = 'integration/centralize/verify_sms_verification';
    const CENTRALIZE_URL_CHECK_DATA_CUSTOMER = 'integration/centralize/check_data_customer';
    const CENTRALIZE_URL_GET_DATA_CUSTOMER = 'integration/centralize/customer_detail_url';
    const CENTRALIZE_LOGIN_VERIFICATION_CHECK_PHONE = 'integration/centralize/check_phone_number_central';
    const MAGENTO_ADMIN_TOKEN_BEARER = 'integration/centralize/admin_bearer_token';

    /**
     * @var \Trans\Integration\Api\IntegrationChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository
    ) {
        parent::__construct($context);
        $this->channelRepository = $channelRepository;
    }

    /**
     * Get config value by path
     * 
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * is env production
     * 
     * @return bool
     */
    public function isProduction()
    {
        return $this->getConfigValue(self::CENTRALIZE_API_ENV) === Env::PROD ? true : false;
    }

    /**
     * is static otp enabled
     * 
     * @return bool
     */
    public function isEnableStaticOtp()
    {
        return $this->getConfigValue(self::CENTRALIZE_ENABLE_STATIC_OTP);
    }

    /**
     * get static otp
     * 
     * @return array
     */
    public function getStaticOtp()
    {
        $data = $this->getConfigValue(self::CENTRALIZE_STATIC_OTP);
        $data = preg_replace('/\s/', '', $data);
        $xdata = explode(',', $data);

        return $xdata;
    }

    /**
     * get centralize api url
     * 
     * @return bool
     */
    public function getCentralizeApiUrl()
    {
        $channelId = $this->getConfigValue(self::CENTRALIZE_API_URL_DEV);
        
        if($this->isProduction()) {
            $channelId = $this->getConfigValue(self::CENTRALIZE_API_URL_PROD);
        }

        try {
            $channel = $this->channelRepository->getById($channelId);
            return $channel->getUrl();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * is sms verification enabled
     * 
     * @return bool
     */
    public function isEnableSmsVerification()
    {
        return $this->getConfigValue(self::ENABLE_SMS_VERIFICATION);
    }

    /**
     * is check phone number to central DB before send verification code
     * 
     * @return bool
     */
    public function isCheckPhoneVerificationLogin()
    {
        return $this->getConfigValue(self::CENTRALIZE_LOGIN_VERIFICATION_CHECK_PHONE);
    }

    /**
     * get centralize api url
     * 
     * @return string
     */
    public function getCentralizeApiKey()
    {
        return $this->getConfigValue(self::CENTRALIZE_API_KEY);
    }

    /**
     * get send sms verification url
     * 
     * @return string
     */
    public function getSendVerificationUrl()
    {
        return $this->getCentralizeApiUrl() . $this->getConfigValue(self::CENTRALIZE_URL_SEND_VERIFICATION);
    }

    /**
     * get verify sms verification url
     * 
     * @return string
     */
    public function getVerifyVerificationUrl()
    {
        return $this->getCentralizeApiUrl() . $this->getConfigValue(self::CENTRALIZE_URL_VERIFY_VERIFICATION);
    }

    /**
     * check data customer url
     * 
     * @return string
     */
    public function getCheckDataCustomerUrl()
    {
        return $this->getCentralizeApiUrl() . $this->getConfigValue(self::CENTRALIZE_URL_CHECK_DATA_CUSTOMER);
    }

    /**
     * get data customer url
     * 
     * @return string
     */
    public function getDataCustomerUrl()
    {
        return $this->getCentralizeApiUrl() . $this->getConfigValue(self::CENTRALIZE_URL_GET_DATA_CUSTOMER);
    }

    /**
     * get new send customer data integration url
     *
     * @return string
     */
    public function getSendNewCustomerUrl()
    {
        return $this->getCentralizeApiUrl() . $this->getConfigValue(self::CENTRALIZE_URL_SEND_NEW_CUSTOMER);
    }

    /**
     * get send update customer data integration url
     *
     * @return string
     */
    public function getSendUpdateCustomerUrl()
    {
        return $this->getCentralizeApiUrl() . $this->getConfigValue(self::CENTRALIZE_URL_SEND_UPDATE_CUSTOMER);
    }

    /**
     * get magento admin token bearer
     *
     * @return string
     */
    public function getMagentoTokenBearer()
    {
        return $this->getConfigValue(self::MAGENTO_ADMIN_TOKEN_BEARER);
    }
}
