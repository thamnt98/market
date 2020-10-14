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
 * Class RecoveryProcess.
 */
class RecoveryProcess extends \Magento\Framework\App\Action\Action
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
        $this->logger->info('----Start ' . __CLASS__);
        
        $this->logger->info('verifed ' . $this->customerSession->getVerified());
        if($this->getRequest()->getParam('otp') && $this->customerSession->getVerified() == true) {
            $this->logger->info('----Process Recovery ----');
            try {
                $post = $this->getRequest()->getParams();
                $customer = $this->customerRepository->get($post['email']);
                
                $this->authInterface->unlock($customer->getId());
                $this->accountInterface->initateResetPassword($customer);
                $this->coreRegistry->register('customer', $customer);
                $this->customerSession->setUnlockedAcc($customer->getId());

                $this->logger->info('----Delete Session----');
            } catch (\Exception $e) {
                $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $this->messageManager->addError(__('Recovery account failed.'));
                $resultRedirect->setPath('customer/account/login');
                return $resultRedirect;        
            }
        } else if($this->getRequest()->getParam('token') && $this->getRequest()->getParam('uid')) {
            try {
                $customerId = $this->getRequest()->getParam('uid');
                $resetPasswordToken = $this->getRequest()->getParam('token');
                $this->accountManagement->validateResetPasswordLinkToken(null, $resetPasswordToken);
                $customer = $this->customerRepository->getById($customerId);
                $this->customerSession->setUnlockedAcc($customer->getId());
                
                $this->authInterface->unlock($customer->getId());
                
                $this->logger->info('----Delete Session----');
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $this->messageManager->addError(__('Your recovery account link has expired.'));
                $resultRedirect->setPath('*/account/login');
                $this->logger->info('----Delete Session----');
                return $resultRedirect;
            }
        }

        $this->customerSession->unsRegister();
        $this->customerSession->unsActionPost();
        $this->customerSession->unsLockedAcc();
        
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $this->messageManager->addSuccess(__('Recovery account success.'));
        $resultRedirect->setPath('customer/recovery/success');
        return $resultRedirect;
    }
}
