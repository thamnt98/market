<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Customer\Block\Widget;

/**
 * Class VerifyEmail
 * @package SM\Customer\Block\Widget
 */
class VerifyEmail extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'widget/verify-email.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * MyAddress constructor.
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Get current customer
     *
     * Return stored customer or get it from session
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @since 102.0.1
     */
    public function getCustomer(): \Magento\Customer\Api\Data\CustomerInterface
    {
        $customer = $this->getData('customer');
        if ($customer === null) {
            $customer = $this->currentCustomer->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * Get verified email
     *
     * @return boolean
     */
    public function getVerifiedEmail()
    {
        if (!$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            return true;
        }

        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('is_verified_email')) {
            if ($customer->getCustomAttribute('is_verified_email')->getValue() == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getMyAccountUrl()
    {
        return $this->getUrl('customer/account');
    }

    /**
     * @return string
     */
    public function getCloseActionUrl()
    {
        return $this->getUrl('customer/verify/closeWidgetVerifyEmail');
    }

    /**
     * @return string
     */
    public function validateWidget()
    {
        return $this->getUrl('customer/verify/validateWidget');
    }
}
