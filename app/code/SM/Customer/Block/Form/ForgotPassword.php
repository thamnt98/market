<?php
/**
 * @category SM
 * @package SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Customer\Block\Form;

use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class ForgotPassword
 *
 * @package SM\Customer\Block\Form
 */
class ForgotPassword extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $cookiePersistent;

    /**
     * Login constructor.
     *
     * @param Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Persistent\Helper\Session $cookiePersistent
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Persistent\Helper\Session $cookiePersistent,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->cookiePersistent = $cookiePersistent;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);

        /*need recheck with Persistent*/
        /*$customerGroup = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        $loggedIn = $customerGroup ?? ($customerGroup ?? null);
        if ($this->cookiePersistent->isPersistent()) {
            $persistent = $this->cookiePersistent->getSession();
            $loggedIn = $loggedIn ?: $persistent->getGroupId();
        }

        return $loggedIn;*/
    }

    /**
     * Get minimum password length
     *
     * @return string
     * @since 100.1.0
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get number of password required character classes
     *
     * @return string
     * @since 100.1.0
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    /**
     * Get Ajax login url
     *
     * @return string
     */
    public function getAjaxLoginUrl()
    {
        return $this->getUrl('customer/ajax/login');
    }

    /**
     * Get Ajax login url
     *
     * @return string
     */
    public function getEmailPostRequest()
    {
        return $this->getRequest()->getParam('fgPass-request');
    }

    /**
     * @return string
     */
    public function getEmailCustomerRecovery()
    {
        return $this->getRequest()->getParam("email");
    }
}
