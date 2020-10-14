<?php
/**
 * @category    Trans
 * @package     Trans_CustomerMyProfile
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\CustomerMyProfile\Controller\Email;

use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Class Save
 * @package Trans\CustomerMyProfile\Controller\Mobile
 */
class Save extends \Magento\Customer\Controller\AbstractAccount implements HttpPostActionInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \SM\Customer\Api\TransCustomerProfileInterface
     */
    protected $transCustomerProfile;

    /**
     * @var \SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface
     */
    protected $smsVerificationRepository;

    /**
     * @var \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory
     */
    protected $customerCentralCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $customerResource;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \SM\Customer\Model\Email\Sender
     */
    protected $emailSender;

    /**
     * Save constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResource
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \SM\Customer\Api\TransCustomerProfileInterface $transCustomerProfile
     * @param \SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface $smsVerificationRepository
     * @param \Trans\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \SM\Customer\Model\Email\Sender $emailSender
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \SM\Customer\Api\TransCustomerProfileInterface $transCustomerProfile,
        \SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface $smsVerificationRepository,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement,
        \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \SM\Customer\Model\Email\Sender $emailSender
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->serializer = $serializer;
        $this->transCustomerProfile = $transCustomerProfile;
        $this->smsVerificationRepository = $smsVerificationRepository;
        $this->accountManagement = $accountManagement;
        $this->customerCentralCollectionFactory = $customerCentralCollectionFactory;
        parent::__construct($context);
        $this->customerResource = $customerResource;
        $this->customerFactory = $customerFactory;
        $this->currentCustomer = $currentCustomer;
        $this->emailSender = $emailSender;
    }

    /**
     * Get current customer
     *
     * Return stored customer or get it from session
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @since 102.0.1
     */
    public function getCustomer(): \Magento\Customer\Api\Data\CustomerInterface
    {
        return $this->currentCustomer->getCustomer();
    }

    /**
     * @return bool
     */
    public function isVerifyEmail()
    {
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('is_verified_email')) {
            if ($customer->getCustomAttribute('is_verified_email')->getValue() == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->customerSession->isLoggedIn()) {
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }
        if (!$this->getRequest()->getParam('email')) {
            $this->messageManager->addErrorMessage(
                __("Email invalid.")
            );
            $resultRedirect->setPath('*/myprofile/editemail');
            return $resultRedirect;
        }

        if ($this->getRequest()->getParam('email') == $this->customerSession->getCustomer()->getEmail()) {
            if (!$this->isVerifyEmail()) {
                $this->emailSender->sendVerifyEmail($this->getCustomer());
                $this->messageManager->addSuccessMessage(
                    __('We have sent a verification email to %1. Please check your inbox.',
                        $this->customerSession->getCustomer()->getEmail())
                );
            }
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }

        $email = $this->getRequest()->getParam('email');
        if ($this->transCustomerProfile->isExistUser($email, 'email')) {
            $this->messageManager->addErrorMessage(
                __("Your email has already been registered")
            );
            $resultRedirect->setPath('*/myprofile/editemail');
            return $resultRedirect;
        }

        //check email is already exist or not in centralize
        $checkCentral = $this->accountManagement->checkCustomerRegister($this->customerSession->getCustomer()->getTelephone(), $email);
        try {
            $checkCentral = $this->serializer->unserialize($checkCentral);
            if (isset($checkCentral['error']) && $checkCentral['error'] == true) {
                $this->messageManager->addErrorMessage(__('We can\'t update customer information.'));
                $resultRedirect->setPath('*/myprofile/editemail');
                return $resultRedirect;
            }
            if (isset($checkCentral['customer_email']) &&  $checkCentral['customer_email']== 1) {
                $this->messageManager->addErrorMessage(__('We can\'t save, email already exist.'));
                $resultRedirect->setPath('*/myprofile/editemail');
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t update customer information.'));
            $resultRedirect->setPath('*/myprofile/editemail');
            return $resultRedirect;
        }
        //check central id is exist or not
        $centralData = $this->customerCentralCollectionFactory->create()
            ->addFieldToFilter('magento_customer_id', $this->customerSession->getCustomerId())
            ->getFirstItem();
        if ($centralData->getId() && strlen($centralData->getCentralId()) <= 10) {
            $this->messageManager->addErrorMessage(__('We can\'t save customer information.'));
            $resultRedirect->setPath('*/myprofile/editemail');
            return $resultRedirect;
        }

        try {
            //TODO: Write API
            $customerId = $this->customerSession->getCustomerId();

            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->customerFactory->create();
            $customer->load($customerId);
            $customer->setData('email', $email)
                ->setData(\SM\Customer\Helper\Config::IS_VERIFIED_EMAIL_ATTRIBUTE_CODE, false)
                ->save();

            $this->messageManager->addSuccessMessage(
                __('We have sent a verification email to %1. Please check your inbox.',
                    $email)
            );
            $this->_eventManager->dispatch(
                'trans_customer_save_email_after',
                ['customer' => $customer->getDataModel()]
            );
            $this->_eventManager->dispatch(
                'customer_account_edited',
                ['email' => $customer->getEmail(), 'type' => \Trans\CustomerMyProfile\Helper\Data::CHANGE_EMAIL]
            );
            $resultRedirect->setPath('*/myprofile/editemail');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('*/myprofile/editemail');
        }
        return $resultRedirect;
    }
}
