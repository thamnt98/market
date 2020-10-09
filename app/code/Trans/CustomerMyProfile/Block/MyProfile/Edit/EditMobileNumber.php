<?php
/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Block\MyProfile\Edit;

/**
 * Class EditMobileNumber
 */
class EditMobileNumber extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \SM\AndromedaSms\Helper\Config
     */
    protected $config;

    /**
     * EditMobileNumber constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \SM\AndromedaSms\Helper\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \SM\AndromedaSms\Helper\Config $config,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerSession = $customerSession;
        $this->eavConfig = $eavConfig;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        $telephone = '';
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('telephone')) {
            $telephone = $customer->getCustomAttribute('telephone')->getValue();
            $telephone = '08' . preg_replace("/^(^\+628|^628|^08|^8)/", '', $telephone);
        }

        return $telephone;
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
     * get edit mobile number url
     *
     * @return string
     */
    public function getEditMobileNumberUrl()
    {
        return $this->getUrl('customermyprofile/myprofile/setunsverified/');
    }

    /**
     * @return int
     */
    public function getVerificationCodeExpiredIn()
    {
        return $this->config->getVerificationCodeExpiredIn();
    }
}
