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

namespace Trans\CustomerMyProfile\Controller\Mobile;

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
     * Save constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \SM\Customer\Api\TransCustomerProfileInterface $transCustomerProfile
     * @param \SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface $smsVerificationRepository
     * @param \Trans\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \SM\Customer\Api\TransCustomerProfileInterface $transCustomerProfile,
        \SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface $smsVerificationRepository,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement,
        \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->serializer = $serializer;
        $this->transCustomerProfile = $transCustomerProfile;
        $this->smsVerificationRepository = $smsVerificationRepository;
        $this->accountManagement = $accountManagement;
        $this->customerCentralCollectionFactory = $customerCentralCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
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
        $validateParams = $this->validateParams();
        if ($validateParams['status']) {
            try {
                $telephoneIncludePrefix = $this->getRequest()->getParam('telephone');
                $telephone = '628' . preg_replace("/^(^\+628|^628|^08|^8)/", '', $telephoneIncludePrefix);
                $customerId = $this->customerSession->getCustomerId();
                if ($this->transCustomerProfile->isExistUser($telephone, 'telephone')) {
                    $this->messageManager->addErrorMessage(
                        __("Your mobile number has already been registered.")
                    );
                    $resultRedirect->setPath('*/myprofile/editmobilenumber');
                } else {
                    //check mobile number is already exist or not in centralize
                    $checkCentral = $this->accountManagement->checkCustomerRegister($telephoneIncludePrefix, $this->customerSession->getCustomer()->getEmail());
                    try {
                        $checkCentral = $this->serializer->unserialize($checkCentral);
                        if (isset($checkCentral['error']) && $checkCentral['error'] == true) {
                            $this->messageManager->addErrorMessage(__('System error. We can\'t update customer information.'));
                            $resultRedirect->setPath('*/myprofile/editmobilenumber');
                            return $resultRedirect;
                        }
                        if (isset($checkCentral['customer_mobile']) &&  $checkCentral['customer_mobile']== 1) {
                            $this->messageManager->addErrorMessage(__('Your mobile number has already been registered.'));
                            $resultRedirect->setPath('*/myprofile/editmobilenumber');
                            return $resultRedirect;
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage(__('We can\'t update customer information.'));
                        $resultRedirect->setPath('*/myprofile/editmobilenumber');
                        return $resultRedirect;
                    }

                    //check central id is exist or not
                    $centralData = $this->customerCentralCollectionFactory->create()
                        ->addFieldToFilter('magento_customer_id', $this->customerSession->getCustomerId())
                        ->getFirstItem();
                    if ($centralData->getId() && strlen($centralData->getCentralId()) <= 10) {
                        $this->messageManager->addError(__('We can\'t save customer information.'));
                        $resultRedirect->setPath('*/myprofile/editmobilenumber');
                        return $resultRedirect;
                    }
                    $customer = $this->customerRepository->getById($customerId)->setCustomAttribute('telephone', $telephone);
                    $customer = $this->customerRepository->save($customer);
                    $this->messageManager->addSuccess(
                        __("Your mobile number has been changed.")
                    );
                    $this->_eventManager->dispatch(
                        'trans_customer_save_telephone_after',
                        ['customer' => $customer]
                    );
                    $this->_eventManager->dispatch(
                        'customer_account_edited',
                        [
                            'email' => $customer->getEmail(),
                            'type' => \Trans\CustomerMyProfile\Helper\Data::CHANGE_TELEPHONE
                        ]
                    );
                    $resultRedirect->setPath('customer/account');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    $e->getMessage()
                );
                $resultRedirect->setPath('*/myprofile/editmobilenumber');
            }
        } else {
            $this->messageManager->addErrorMessage(
                $validateParams['message']
            );
            $resultRedirect->setPath('*/myprofile/editmobilenumber');
        }
        return $resultRedirect;
    }

    /**
     * @return array
     */
    protected function validateParams()
    {
        $params = $this->getRequest()->getParams();
        $message = '';
        $status = true;
        if (!isset($params['otp'])) {
            $message = __("Can't save telephone. Missing OTP value.");
            $status = false;
            return ['status' => $status, 'message' => $message];
        }

        if (!isset($params['telephone'])) {
            $message = __("Telephone invalid.");
            $status = false;
            return ['status' => $status, 'message' => $message];
        }

        if ($this->smsVerificationRepository->getByPhoneNumber($params['telephone'])->getIsVerified() == 0) {
            $message = __("Failed to verify OTP.");
            $status = false;
        }
        return ['status' => $status, 'message' => $message];
    }
}
