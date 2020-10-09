<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy Surya Perdana <muhammad.randy@transdigital.co.id>
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Plugin\Customer\Controller\Account;

use Magento\Customer\Controller\Account\LoginPost as MageLoginPost;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class LoginPost
 */
class LoginPost
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
	protected $customer;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $session;

	/**
	 * @var \Magento\Framework\Controller\Result\RedirectFactory
	 */
	protected $resultRedirectFactory;

	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

	/**
	 * @var \Magento\Customer\Model\Account\Redirect
	 */
	protected $accountRedirect;

	/**
	 * @var \Magento\Customer\Model\AuthenticationInterface
	 */
	protected $authInterface;

	/**
	 * @var \Magento\Customer\Api\AccountManagementInterface
	 */
	protected $accountApi;
	
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;
	
	/**
	 * @var \Trans\Customer\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @var \Trans\Customer\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customer
	 * @param \Magento\Customer\Model\Session $session
	 * @param \Magento\Customer\Model\Account\Redirect $accountRedirect
	 * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect,
	 * @param \Magento\Customer\Api\AccountManagementInterface $accountApi
	 * @param \Magento\Customer\Model\AuthenticationInterface $authInterface
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Trans\Customer\Helper\Config $configHelper
	 * @param \Trans\Customer\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customer,
		\Magento\Customer\Model\Session $session,
		\Magento\Customer\Model\Account\Redirect $accountRedirect,
		\Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Customer\Api\AccountManagementInterface $accountApi,
		\Magento\Customer\Model\AuthenticationInterface $authInterface,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Trans\Customer\Helper\Config $configHelper,
		\Trans\Customer\Helper\Data $dataHelper
	)
	{
		$this->request = $request;
		$this->customerFactory = $customerFactory;
		$this->accountApi = $accountApi;
		$this->customer = $customer;
		$this->configHelper = $configHelper;
		$this->resultRedirectFactory = $resultRedirectFactory;
		$this->redirect = $redirect;
		$this->authInterface = $authInterface;
		$this->session = $session;
		$this->accountRedirect = $accountRedirect;
		$this->messageManager = $messageManager;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * phone number login process
	 * 
	 * @param MageLoginPost $subject
	 * @param callable $proceed
	 * @return mixed
	 */
	public function aroundExecute(MageLoginPost $subject, callable $proceed)
	{
		if($this->configHelper->isPhoneLoginEnabled()) 
		{
			
			$login = $this->request->getParams();
			if(isset($login['login'])) {
				$login = $login['login'];
			}
			
			if($login['username'] && !strpos($login['username'], '@') !== false ) 
			{
	            /* Get email id based on mobile number and login*/
	            $customereCollection = $this->customerFactory->create();
	            $customereCollection->addFieldToFilter("telephone", $login['username']);
	            
	            if($customereCollection->getSize() > 0) {
	            	$customer = $customereCollection->getFirstItem();
	            	$customerData = $this->customer->getById($customer->getId());
	            	$otp = $this->request->getParam('otp');
	            	
	            	if($otp) {
	                    $resultRedirect = $this->resultRedirectFactory->create();
	            		if($this->session->getVerified() == true) {
	                        $this->session->setCustomerDataAsLoggedIn($customerData);
		            		$this->session->regenerateId();

		                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
		                    $this->accountRedirect->clearRedirectCookie();
	                        // URL is checked to be internal in $this->_redirect->success()
	                        $resultRedirect->setUrl($this->redirect->success($redirectUrl));
	                        $this->session->getVerified();
	                        return $resultRedirect;
	            		} else {
	            			$this->messageManager->addError(__('Your verification code is wrong.'));
	            			$resultRedirect->setPath('*/*/login');
							return $resultRedirect;
	            		}
	            	}

		            foreach($customereCollection as $customerdata){ 
		                $login['username'] = $customerdata['email'];
		            }

		            $this->request->setPostValue('login', $login); 
	            }
	        } else {
		        $this->request->setPostValue('login', $login); 
	        }
		}
		
		try {
			return $proceed();
		} catch (\Error $err) {
			$resultRedirect = $this->resultRedirectFactory->create();
			
			try {
				$customer = $this->customer->get($login['username']);
				$locked = $this->authInterface->isLocked($customer->getId());

				if($locked) {
					$customerData['id'] = $customer->getId();
					$customerData['email'] = $customer->getEmail();
					$customerData['telephone'] = $this->dataHelper->getCustomerPhone($customer->__toArray());
					$this->session->setLockedAcc($customerData);
					$resultRedirect->setPath('customer/account/locked');
					return $resultRedirect;
				}
			} catch (NoSuchEntityException $e) {
				$resultRedirect->addError(__('Customer not found'));
			}

			$resultRedirect->setPath($this->redirect->getRefererUrl());		  
			return $resultRedirect;
		}
	}
}
