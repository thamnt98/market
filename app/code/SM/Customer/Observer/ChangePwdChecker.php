<?php

namespace SM\Customer\Observer;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class ChangePwdChecker implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * ChangePwdChecker constructor.
     * @param Session $session
     * @param CustomerFactory $customerFactory
     * @param UrlInterface $url
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        Session $session,
        CustomerFactory $customerFactory,
        UrlInterface $url,
        ResponseFactory $responseFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->customerSession = $session;
        $this->customerFactory = $customerFactory;
        $this->url = $url;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->customerSession->isLoggedIn() &&
            $observer->getRequest()->getRouteName() != 'livechat' &&
            !$observer->getRequest()->isXmlHttpRequest()
        ) {
            $customerRep = $this->customerFactory->create()->load($this->customerSession->getId());
            $lastTimeChangPwdWhenLogged = $this->customerSession->getLastTimeChangePwdWhenLogged();
            $lastTimeChangePwd=$customerRep->getLastTimeChangePwd();
            if (!empty($lastTimeChangPwdWhenLogged) && !empty($lastTimeChangePwd)) {
                if ($lastTimeChangePwd != $lastTimeChangPwdWhenLogged) {
                    $redirectUrl = $this->url->getUrl("customer/account/logout");
                    $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
                }
            }
        }
    }
}
