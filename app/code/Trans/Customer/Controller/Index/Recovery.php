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
use Magento\Framework\App\Area;

/**
 * Class Recovery.
 */
class Recovery extends \Magento\Framework\App\Action\Action
{
    /**
     * constant recovery method
     */
    const METHOD_EMAIL = 'email';
    const METHOD_TELEPHONE = 'telephone';
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
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    protected $authInterface;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountInterface;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Trans\Customer\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Trans\Customer\Helper\Config
     */
    protected $config;

    /**
     * @var \Trans\Customer\Helper\Email
     */
    protected $emailHelper;

    /**
     * @var \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\AuthenticationInterface $authInterface
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Trans\Customer\Api\AccountManagementInterface $accountInterface
     * @param \Trans\Customer\Logger\Logger $logger
     * @param \Trans\Customer\Helper\Config $config
     * @param \Trans\Customer\Helper\Email $emailHelper
     * @param \Trans\Core\Helper\Customer $customerHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\AuthenticationInterface $authInterface,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Trans\Customer\Api\AccountManagementInterface $accountInterface,
        \Trans\Customer\Logger\Logger $logger,
        \Trans\Customer\Helper\Config $config,
        \Trans\Core\Helper\Email $emailHelper,
        \Trans\Core\Helper\Customer $customerHelper
    ) {
        $this->session = $session;
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->authInterface = $authInterface;
        $this->customerRepository = $customerRepository;
        $this->accountInterface = $accountInterface;
        $this->accountManagement = $accountManagement;
        $this->logger = $logger;
        $this->config = $config;
        $this->emailHelper = $emailHelper;
        $this->customerHelper = $customerHelper;
        parent::__construct($context);
    }

    /**
     * Customer Register send OTP
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->customerSession->getLockedAcc()) {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account/login');
                          
            return $resultRedirect;
        }

        $this->logger->info('----Start ' . __CLASS__);
        $customerLocked = $this->customerSession->getLockedAcc();
        
        if($this->getRequest()->getParam('method')) {
            $method = $this->getRequest()->getParam('method');

            $this->logger->info('$method' . $method);
            $this->logger->info('$email' . $customerLocked['email']);

            if($method == self::METHOD_TELEPHONE) {
                $this->customerSession->setRegister($customerLocked);
                $this->customerSession->setActionPost($this->_url->getUrl('customer/index/recoveryProcess'));

                $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('customer/index/auth');
                
                $this->logger->info('----End ' . __CLASS__);
                return $resultRedirect;
            }

            if($method == self::METHOD_EMAIL) {
                $recovery = $this->recoveryWithEmail($customerLocked['email']);
                if($recovery) {
                    $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('customer/recovery/email');
                                  
                    $this->customerSession->unsLockedAcc();
                    $this->logger->info('----End ' . __CLASS__);
                    return $resultRedirect;
                }
            }
        }
    }

    /**
     * Recovery with email
     * 
     * @param string $email
     * @return bool
     */
    public function recoveryWithEmail(string $email)
    {
        $customerData = $this->customerRepository->get($email);
        $this->accountInterface->initateResetPassword($customerData);
        
        if($customerData instanceof \Magento\Customer\Api\Data\CustomerInterface) {
            $customerData = $this->customerHelper->getFullCustomerObject($customerData);
            $var['customer'] = $customerData;
            $var['url'] = $this->_url->getUrl('customer/index/recoveryProcess', ['token' => $customerData->getRpToken(), 'uid' => $customerData->getId()]);
            $templateId = $this->config->getEmailTemplateId();
            $this->emailHelper->sendEmail($customerData->getEmail(), $var, $templateId);
            return true;
        }
    }
}
