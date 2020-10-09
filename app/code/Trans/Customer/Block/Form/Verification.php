<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Customer\Block\Form;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;

/**
 * Class Verification
 */
class Verification extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Trans\Integration\Helper\Config
     */
    protected $configHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Trans\Integration\Helper\Config $configHelper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Trans\Integration\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->configHelper = $configHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Js params
     *
     * @return string
     */
    public function getFormParams()
    {
    	$registerData = $this->getRegistrationData();

        $params = [
            'telephone'  => $registerData['telephone'],
            'isNeedCheck'  => $this->isNeedCheck()
        ];

        return json_encode($params);
    }

    /**
     * get new customer data registration
     * 
     * @return array
     */
    public function getRegistrationData()
    {
    	return $this->coreRegistry->registry('register');
    }

    /**
     * get new customer data registration
     * 
     * @return array
     */
    public function getTokenBearer()
    {
        return $this->configHelper->getMagentoTokenBearer();
    }

    /**
     * get verification id
     * 
     * @return string
     */
    public function getVerificationId()
    {
    	return $this->coreRegistry->registry('verification_id');
    }

    /**
     * get auth page url
     * 
     * @return string
     */
    public function getAuthPageUrl()
    {
    	return $this->getUrl('customer/index/auth');
    }

    /**
     * get verify url
     * 
     * @return string
     */
    public function getVerifyUrl()
    {
    	return $this->getUrl('rest/default/V1/customer/auth-verification');
    }

    /**
     * get send sms verification url
     * 
     * @return string
     */
    public function getSendSmsVerificationUrl()
    {
    	return $this->getUrl('rest/default/V1/customer/send-sms-verification');
    }

    /**
     * get redirect url if success
     * 
     * @return string
     */
    public function getRedirectIfSuccess()
    {
    	$path = $this->getData('redirect_if_success');
    	if($path) {
    		return $this->getUrl($path);
    	}
    }

    /**
     * get redirect url if failed
     * 
     * @return string
     */
    public function getRedirectIfFailed()
    {
    	$path = $this->getData('redirect_if_failed');
    	if($path) {
    		return $this->getUrl($path);
    	}
    }

    /**
     * get form class identifier
     * 
     * @return string
     */
    public function getFormClass()
    {
    	return $this->getData('form_class');
    }

    /**
     * is submit if success
     * 
     * @return string
     */
    public function isSubmitForm()
    {
    	return $this->getData('submit_if_success') ? $this->getData('submit_if_success') : false;
    }

    /**
     * is need check
     * 
     * @return string
     */
    public function isNeedCheck()
    {
        return $this->getData('is_need_check') ? $this->getData('is_need_check') : false;
    }

    /**
     * is login
     * 
     * @return string
     */
    public function isHide()
    {
        return $this->getData('is_hide') ? $this->getData('is_hide') : false;
    }
}