<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy Surya Perdana <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

 namespace Trans\Customer\Plugin\Customer\Controller\Account;

 use Magento\Customer\Controller\Account\ForgotPasswordPost as MageForgotPasswordPost;
 use Magento\Framework\UrlInterface;
 use Magento\Framework\Exception\NoSuchEntityException;

 /**
  * Class ForgotPasswordPost
  */
 class ForgotPasswordPost
 {	
 	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
	 */
	protected $customerFactory;

	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepository;

	/**
	 * @var \Trans\Customer\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @var \Magento\Framework\Controller\ResultFactory
	 */
	protected $resultFactory;

	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $customerSession;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $urlInterface;

	/**
	 * @var \Trans\Core\Helper\Customer
	 */
	protected $customerHelper;

	/**
	 * @var \Trans\Customer\Api\AccountManagementInterface
	 */
	protected $account;

	/**
	 * @var \Trans\Integration\Helper\Config
	 */
	protected $integrationHelper;

	/**
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
	 * @param \Magento\Framework\Controller\ResultFactory $resultFactory
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\UrlInterface $urlInterface
	 * @param \Trans\Customer\Api\AccountManagementInterface $account
	 * @param \Trans\Core\Helper\Customer $customerHelper
	 * @param \Trans\Customer\Helper\Config $configHelper
	 * @param \Trans\Integration\Helper\Config $integrationHelper
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Framework\Controller\ResultFactory $resultFactory,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\UrlInterface $urlInterface,
		\Trans\Customer\Api\AccountManagementInterface $account,
		\Trans\Core\Helper\Customer $customerHelper,
		\Trans\Customer\Helper\Config $configHelper,
		\Trans\Integration\Helper\Config $integrationHelper
	)
	{
		$this->request = $request;
		$this->customerFactory = $customerFactory;
		$this->customerRepository = $customerRepository;
		$this->configHelper = $configHelper;
		$this->resultFactory = $resultFactory;
		$this->redirect = $redirect;
		$this->account = $account;
		$this->urlInterface = $urlInterface;
		$this->customerSession = $customerSession;
		$this->customerHelper = $customerHelper;
		$this->messageManager = $messageManager;
		$this->integrationHelper = $integrationHelper;
	}

	/**
	 * phone number forgotpassword process
	 * 
	 * @param MageForgotPasswordPost $subject
	 * @param callable $proceed
	 * @return mixed
	 */
	public function aroundExecute(MageForgotPasswordPost $subject, callable $proceed)
	{
		$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
		$username = $this->request->getParam('username');
		
		if(!strpos($username, '@') !== false && $this->configHelper->isPhoneLoginEnabled()) 
		{
			$this->request->setPostValue('telephone', $username); 
            
            /* Get email id based on mobile number and login*/
            $customereCollection = $this->customerFactory->create();
            $customereCollection->addFieldToFilter("telephone", $username);
            
            if($customereCollection->getSize() > 0) 
            {
            	$customer = $customereCollection->getFirstItem();
	            $username = $customer->getEmail();
				$this->request->setPostValue('email', $username);
				if($this->integrationHelper->isEnableSmsVerification() && !$this->request->getParam('otp')) {
					$this->customerSession->setRegister($this->request->getParams());
					$this->customerSession->setActionPost('customer/account/forgotpasswordpost/');

					$resultRedirect->setPath($this->urlInterface->getUrl('customer/index/auth'));
		           				  
					return $resultRedirect;
				}
	        } else {
				$this->messageManager->addError(__('Your Phone number or Email is not registered/wrong'));
				$resultRedirect->setPath($this->redirect->getRefererUrl());
						  
				return $resultRedirect;
	        }
		} else {
			$this->request->setPostValue('email', $username); 
			$email = $this->request->getParam('email') ?: $username;
			
			try {
				$customer = $this->customerRepository->get($email);
			} catch (NoSuchEntityException $e) {
				$this->messageManager->addError(__('Your email not registered yet.'));
				$resultRedirect->setPath($this->redirect->getRefererUrl());
				return $resultRedirect;
			} catch (\Exception $e) {
				$this->messageManager->addError(__('Error something went wrong when processing forgot password.'));
				$resultRedirect->setPath($this->redirect->getRefererUrl());
				return $resultRedirect;
			}
		}

		if($this->customerSession->getVerified() == true) {
			$customer = $this->customerRepository->get($this->request->getParam('email'));
			$initiateReset = $this->account->initateResetPassword($customer);
			$customerData = $this->customerHelper->getFullCustomerObject($customer);

			if($initiateReset) {
				$resultRedirect->setPath('customer/account/createPassword', ['token' => $customerData->getRpToken()]);
				$this->customerSession->unsVerified();
				$this->customerSession->unsRegister();
				$this->customerSession->unsActionPost();
				return $resultRedirect;
			}
		}
		
		return $proceed();
	}
 }	