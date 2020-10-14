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

namespace Trans\Customer\Plugin\Customer\Controller\Ajax;

use Magento\Customer\Controller\Ajax\Login as MageLoginAjax;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Login
 */
class Login
{	
	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $helper;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

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
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;

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
	 * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
	 */
	protected $cookieMetadata;
	
	/**
	 * @var \Magento\Framework\Stdlib\CookieManagerInterface
	 */
	protected $cookieManager;
	
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
	 * @param \Magento\Framework\Json\Helper\Data $helper
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customer
	 * @param \Magento\Customer\Model\Session $session
	 * @param \Magento\Customer\Model\Account\Redirect $accountRedirect
	 * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect,
	 * @param \Magento\Customer\Model\AuthenticationInterface $authInterface
	 * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadata
	 * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
	 * @param \Trans\Customer\Helper\Config $configHelper
	 * @param \Trans\Customer\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\Json\Helper\Data $helper,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customer,
		\Magento\Customer\Model\Session $session,
		\Magento\Customer\Model\Account\Redirect $accountRedirect,
		\Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Customer\Model\AuthenticationInterface $authInterface,
		\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadata,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
		\Trans\Customer\Helper\Config $configHelper,
		\Trans\Customer\Helper\Data $dataHelper
	)
	{
		$this->request = $request;
		$this->cookieMetadata = $cookieMetadata;
		$this->cookieManager = $cookieManager;
		$this->helper = $helper;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->scopeConfig = $scopeConfig;
		$this->customerFactory = $customerFactory;
		$this->customer = $customer;
		$this->configHelper = $configHelper;
		$this->resultRedirectFactory = $resultRedirectFactory;
		$this->redirect = $redirect;
		$this->authInterface = $authInterface;
		$this->session = $session;
		$this->accountRedirect = $accountRedirect;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * phone number login process
	 * 
	 * @param MageLoginPost $subject
	 * @param callable $proceed
	 * @return mixed
	 */
	public function aroundExecute(MageLoginAjax $subject, callable $proceed)
	{
		if($this->configHelper->isPhoneLoginEnabled()) 
		{
			$login = $this->helper->jsonDecode($this->request->getContent());
			
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
	            	$otp = $login['otp'];
	            	
	            	if($otp) {
	            		$response = [
				            'errors' => false,
				            'message' => __('Login successful.')
				        ];
				        if($this->session->getVerified() == true) {
	                        $this->session->setCustomerDataAsLoggedIn($customerData);
				            $this->session->regenerateId();
				            $redirectRoute = $this->accountRedirect->getRedirectCookie();
				            
				            if ($this->cookieManager->getCookie('mage-cache-sessid')) {
				                $metadata = $this->cookieMetadata->createCookieMetadata();
				                $metadata->setPath('/');
				                $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
				            }

				            if (!$this->scopeConfig->getValue('customer/startup/redirect_dashboard') && $redirectRoute) {
				                $response['redirectUrl'] = $this->redirect->success($redirectRoute);
				                $this->accountRedirect->clearRedirectCookie();
				            }
	            		} else {
	            			$response = [
				                'errors' => true,
				                'message' => __('Your verification code is wrong.'),
				            ];
	            		}

	            		/** @var \Magento\Framework\Controller\Result\Json $resultJson */
				        $resultJson = $this->resultJsonFactory->create();
				        return $resultJson->setData($response);
	            	}

		            $login['username'] = $customer->getEmail();

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
