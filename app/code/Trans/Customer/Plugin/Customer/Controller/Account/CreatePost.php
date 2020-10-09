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

use Magento\Customer\Controller\Account\CreatePost as MageCreatePost;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;

/**
 *  Class Create Post
 */
class CreatePost 
{
	/**
	 * const new register post
	 */
	const NEW_REGISTER = 'register';

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $coreRegistry;
	
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $customerSession;
	
	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $urlInterface;
	
	/**
	 * @param \Magento\Framework\Controller\ResultFactory
	 */
	protected $resultFactory;

	/**
	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
	 */
	protected $collection;

	/**
	 * @param \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

	/**
	 * @param \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	/**
	 * @param \Trans\Core\Helper\Customer
	 */
	protected $customerHelper;

	/**
	 * @param \Trans\Customer\Api\AccountManagementInterface
	 */
	protected $account;

	/**
	 * @param \Trans\Integration\Helper\Config
	 */
	protected $integrationHelper;

	/**
	  * @param \Magento\Framework\App\RequestInterface $request
	  * @param \Magento\Framework\Controller\ResultFactory $resultFactory
	  * @param \Magento\Framework\UrlInterface $urlInterface
	  * @param \Magento\Framework\Message\ManagerInterface $messageManager
	  * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collection
	  * @param \Magento\Framework\Session\SessionManagerInterface $session
	  * @param \Magento\Customer\Model\Session $customerSession
	  * @param \Magento\Framework\App\Response\RedirectInterface $redirect
	  * @param \Trans\Customer\Api\AccountManagementInterface $account
	  * @param \Trans\Core\Helper\Customer $customerHelper
	  * @param \Trans\Integration\Helper\Config $integrationHelper
	*/
	public function __construct(
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\UrlInterface $urlInterface,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collection,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Trans\Customer\Api\AccountManagementInterface $account,
		\Trans\Core\Helper\Customer $customerHelper,
		\Trans\Integration\Helper\Config $integrationHelper
	)
	{	
		$this->resultFactory = $resultFactory;
		$this->messageManager = $messageManager;
		$this->urlInterface = $urlInterface;
		$this->redirect = $redirect;
		$this->request = $request;
		$this->collection = $collection;
		$this->coreRegistry = $coreRegistry;
		$this->account = $account;
		$this->customerSession = $customerSession;
		$this->customerHelper = $customerHelper;
		$this->integrationHelper = $integrationHelper;
	}
	
	/**
	 * phone number validation process
	 * 
	 * @param MageCreatePost $subject
	 * @param callable $proceed
	 * @return mixed
	 */
	public function aroundExecute(MageCreatePost $subject, callable $proceed)
	{ 
		$phone = $this->request->getParam('telephone');
		$customerData = $this->collection->create()
		              ->addFieldToFilter('telephone', $phone);

		//check data telephone is already exist or not
        if($customerData->getSize() > 0) 
        {
        	foreach($customerData as $customerdatas)
        	{ 
        		 $customerdatas['telephone'];
			}
        	
        	//get message error when data customer telephone already exist
        	$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
			$this->messageManager->addError(__('Phone number already exist'));
			$resultRedirect->setPath($this->redirect->getRefererUrl());
           				  
			return $resultRedirect;
		}

		$checkCentral = $this->account->checkCustomerRegister($phone, $this->request->getParam('email'));
		$checkCentral = json_decode($checkCentral, true);

		if($checkCentral) {
			$dataCentral = $checkCentral['status'];

			if(is_array($dataCentral)) {
				$dataCentral = $checkCentral['status'][0];
			}

			if(isset($dataCentral) && ($dataCentral['phone_number_status'] == 1 || $dataCentral['email_address_status'] == 1)) {
				$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
				$this->messageManager->addError(__('Phone number or email already exist'));
				$resultRedirect->setPath($this->redirect->getRefererUrl());
	           				  
				return $resultRedirect;		
			}
		}

		$fullname = $this->request->getParam('fullname');
		$name = $this->customerHelper->generateFirstnameLastname($fullname);
		$this->request->setParam('firstname', $name['firstname']);
		$this->request->setParam('lastname', $name['lastname']);
		
		if($this->integrationHelper->isEnableSmsVerification() && !$this->request->getParam('otp')) {
			$this->customerSession->setRegister($this->request->getParams());
			$this->customerSession->setActionPost('customer/account/createPost');

			$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath($this->urlInterface->getUrl('customer/index/auth'));
           				  
			return $resultRedirect;
		}

		if($this->customerSession->getVerified() != true) {
			$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
			$this->messageManager->addError(__('Verification code wrong.'));
			$resultRedirect->setPath($this->redirect->getRefererUrl());
			return $resultRedirect;
		}
		
		$this->customerSession->unsRegister();
		$this->customerSession->unsActionPost();
		$this->customerSession->unsVerified();

		return $proceed(); 
	}
}
