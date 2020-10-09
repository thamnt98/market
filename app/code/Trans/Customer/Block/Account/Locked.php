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
namespace Trans\Customer\Block\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;

/**
 * Class Locked
 */
class Locked extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Trans\Core\Helper\Customer $customerHelper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Trans\Core\Helper\Customer $customerHelper,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->customerHelper = $customerHelper;
        
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * get customer data
     * 
     * @return array
     */
    public function getCustomer()
    {
        if($this->coreRegistry->registry('customer')) {
            return $this->coreRegistry->registry('customer');
        }

        return $this->coreRegistry->registry('locked_account');
    }

    /**
     * get customer phone number
     * 
     * @return string
     */
    public function getCustomerTelephone()
    {
        $customer = $this->getCustomer();
        
        if(isset($customer['custom_attributes']['telephone'])) {
            return $customer['custom_attributes']['telephone']['value'];
        }
    }

    /**
     * get recovery url
     * 
     * @param string $method email|telephone
     * @return string
     */
    public function getRecoveryUrl(string $method)
    {
        return $this->getUrl('customer/index/recovery', ['method' => $method]);
    }

    /**
     * get homepage url
     * 
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->getUrl('/');
    }

    /**
     * get reset password url
     * 
     * @return string
     */
    public function getResetPasswordUrl()
    {
        $customer = $this->customerHelper->getFullCustomerObject($this->getCustomer());
        return $this->getUrl('customer/account/createPassword', ['token' => $customer->getRpToken()]);
    }

    /**
     * get forgot password url
     * 
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->getUrl('customer/account/forgotpassword');
    }
}