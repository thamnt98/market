<?php
declare(strict_types=1);

namespace SM\Customer\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Context;

class Login implements ArgumentInterface
{
    /**
     * Request
     *
     * @var RequestInterface
     */
    protected $_request;
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Login constructor.
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->_request = $context->getRequest();
    }

    public function isShowLoginPopup()
    {
        if (!empty($this->getRequest()->getParam('recovery'))) {
            return false;
        }

        return true;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->_request;
    }

    public function isSocialLogin()
    {
        return $this->customerSession->getIsSocial();
    }
}
