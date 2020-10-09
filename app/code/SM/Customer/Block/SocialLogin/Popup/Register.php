<?php

namespace SM\Customer\Block\SocialLogin\Popup;

use Magento\Customer\Model\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;

/**
 * Class Register
 * @package SM\Customer\Block\SocialLogin\Popup
 */
class Register extends Template
{

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * Checking customer register status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * Get prepared social register buttons
     *
     * @return array;
     */
    public function getButtons()
    {
        // TODO: Add Buttons
        return [];
    }

    /**
     * Get register url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        // TODO: Change action url
        return $this->getUrl('customer/social/register');
    }
}
