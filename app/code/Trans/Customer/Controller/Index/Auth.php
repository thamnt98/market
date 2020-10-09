<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\Exception\StateException;

/**
 * Class Auth.
 */
class Auth extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Trans\Sms\Helper\Apicall
     */
    protected $apiCall;

    /**
     * @var \Trans\Integration\Helper\Config
     */
    protected $configApi;

    /**
     * @var \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Trans\Integration\Logger\Logger $logger
     * @param \Trans\Integration\Helper\Curl $apiCall
     * @param \Trans\Integration\Helper\Config $configApi
     * @param \Trans\Customer\Api\AccountManagementInterface $accountManagement
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Registry $coreRegistry,
        \Trans\Integration\Logger\Logger $logger,
        \Trans\Integration\Helper\Curl $apiCall,
        \Trans\Integration\Helper\Config $configApi,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement
    ) {
        $this->session = $session;
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        $this->apiCall = $apiCall;
        $this->configApi = $configApi;
        $this->accountManagement = $accountManagement;
        parent::__construct($context);
    }

    /**
     * Customer Register send OTP
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->customerSession->getRegister()) {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
                          
            return $resultRedirect;
        }
        
        $this->logger->info('----Start ' . __CLASS__);
        try {
            $customer = $this->customerSession->getRegister();
            
            $hit = $this->accountManagement->sendSmsVerification($customer['telephone'], 0);
            $this->logger->info($hit);
            $response = json_decode($hit, true);
            
            if(!isset($response['errorCode'])) {
                $this->coreRegistry->register('verification_id', $response['verification_id']);
            }
        } catch (StateException $err) {
            $this->logger->info($err->getMessage());
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set('Authorize');
        $resultPage->setHeader('Authentication-Required', 'true');

        $this->coreRegistry->register('register', $this->customerSession->getRegister());
        $this->coreRegistry->register('action_post', $this->customerSession->getActionPost());
        $this->logger->info('----End ' . __CLASS__);

        return $resultPage;
    }
}
