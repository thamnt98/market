<?php
/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Controller\MyProfile;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class ProcessEmail extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollection;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Customer
     */
    protected $customerModel;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Url
     */
    private $customerUrl;

    /**
     * @param \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Trans\Integration\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory
     */
    protected $customerCentralCollectionFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Customer $customerModel
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     * @param PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Url $customerUrl
     * @param \Trans\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Trans\Integration\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Customer $customerModel,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement,
        \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory,
        Url $customerUrl = null
    ) {
        $this->session = $customerSession;
        $this->customerModel = $customerModel;
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->customerCollection = $customerCollection;
        $this->accountManagement = $accountManagement;
        $this->customerCentralCollectionFactory = $customerCentralCollectionFactory;
        $this->eventManager = $context->getEventManager();
        $this->customerUrl = $customerUrl ?: ObjectManager::getInstance()->get(Url::class);
        parent::__construct($context);
    }

    /**
     * Send confirmation link or save update email
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost('send_verification_email')) {
            // try to confirm by email
            $email = $this->session->getCustomer()->getEmail();
            if ($email) {
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                try {
                    $this->customerAccountManagement->resendConfirmation(
                        $email,
                        $this->storeManager->getStore()->getWebsiteId()
                    );
                    $this->messageManager->addSuccess(__('Please check your email for confirmation key.'));
                } catch (InvalidTransitionException $e) {
                    $this->messageManager->addSuccess(__('This email does not require confirmation.'));
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Wrong email.'));
                    $resultRedirect->setPath('customermyprofile/myprofile/editemail');
                    return $resultRedirect;
                }
                $this->session->setUsername($email);
                $resultRedirect->setPath('customermyprofile/myprofile/editemail');
                return $resultRedirect;
            }

            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->getBlock('accountConfirmation')->setEmail(
                $this->getRequest()->getParam('email', $email)
            )->setLoginUrl(
                $this->customerUrl->getLoginUrl()
            );
            return $resultPage;
        } elseif ($this->getRequest()->getPost('submit_edit_email')) {
            $email = $this->getRequest()->getPost('email');
            // load customer info by id
            $customer = $this->customerRepository->getById($this->session->getCustomer()->getId());

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            //check data email is already exist or not
            $customerData = $this->customerCollection->create()
                ->addFieldToFilter('email', $email)
                ->addFieldToFilter('entity_id', ['neq' => $this->session->getCustomer()->getId()]);
            if ($customerData->getSize() > 0) {
                //get message error when data customer eamil already exist
                $this->messageManager->addError(__('Email already exist'));
                $resultRedirect->setPath('customermyprofile/myprofile/editemail');

                return $resultRedirect;
            }

            //check data email is already use or not
            $customerData = $this->customerCollection->create()
                ->addFieldToFilter('email', $email)
                ->addFieldToFilter('entity_id', $this->session->getCustomer()->getId());
            if ($customerData->getSize() > 0) {
                //get message error when data customer email already use
                $this->messageManager->addNotice(__('Email already use'));
                $resultRedirect->setPath('customermyprofile/myprofile/editemail');

                return $resultRedirect;
            }

            //check email is alredy exist or not in centralize
            $checkCentral = $this->accountManagement->checkCustomerRegister($this->session->getCustomer()->getTelephone(), $email);
            $checkCentral = json_decode($checkCentral, true);

            if ($checkCentral == null) {
                $this->messageManager->addError(__('We can\'t update customer information.'));
                $resultRedirect->setPath('customermyprofile/myprofile/editemail');
                return $resultRedirect;
            }

            if ($checkCentral) {
                if ($checkCentral['customer_email'] == 1) {
                    $this->messageManager->addError(__('We can\'t save, email already exist.'));
                    $resultRedirect->setPath('customermyprofile/myprofile/editemail');
                    return $resultRedirect;
                }
            }

            //check central id is exist or not
            $customerId = $this->session->getCustomer()->getId();
            $centralData = [];
            if ($customerId) {
                $centralId = '';
                $centralData = $this->customerCentralCollectionFactory->create()
                    ->addFieldToFilter('magento_customer_id', $customerId);
                if ($centralData->getSize() > 0) {
                    foreach ($centralData as $centralDatas) {
                        $centralId = $centralDatas['central_id'];
                    }
                }

                if ($centralId == "" || strlen($centralId) <= 10) {
                    $this->messageManager->addError(__('We can\'t save customer information.'));
                    $resultRedirect->setPath('customermyprofile/myprofile/editemail');
                    return $resultRedirect;
                }
            }

            try {
                // Update customer email
                $customer->setEmail($email);
                $customer->setConfirmation($this->customerModel->getRandomConfirmationKey());
                $this->customerRepository->save($customer);

                //send email confirmation
                try {
                    $this->customerAccountManagement->resendConfirmation(
                        $email,
                        $this->storeManager->getStore()->getWebsiteId()
                    );
                } catch (InvalidTransitionException $e) {
                    $this->messageManager->addSuccess(__('This email does not require confirmation.'));
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Wrong email.'));
                    $resultRedirect->setPath('customermyprofile/myprofile/editemail');
                    return $resultRedirect;
                }

                //Success Event Edit Customer
                $this->eventManager->dispatch('customer_account_edited', ['email' => $customer->getEmail()]);

                $this->messageManager->addSuccess(__('You saved the account information, please check your email for confirmation key.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t save the account information.'));
            }
            $resultRedirect->setPath('customermyprofile/myprofile/editemail');
            return $resultRedirect;
        }
    }
}
