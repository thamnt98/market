<?php

namespace SM\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;

class ResetPassTk extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * SendRecovery constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->customerRegistry = $customerRegistry;
        $this->url = $context->getUrl();
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $data = ['login' => false, 'url' => $this->url->getBaseUrl()];
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $customerName = $customer->getName();
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $urlRedirect = $this->url->getBaseUrl() . '?recoverytoken=' . $customerSecure->getRpToken() . '&email=' . $customer->getEmail() . '&name=' . $customerName . '&account=back';
            $data = ['login' => true, 'url' => $urlRedirect];
        }
        $resultJson->setData($data);
        return $resultJson;
    }
}
