<?php

namespace SM\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Exception\InputException;

class UserExist extends Action
{
    Const CUSTOMER_HAS_NOT_ON_ECOSYSTEM = 'hasNotOnEco';
    Const CUSTOMER_EXIST_ON_MAGENTO = 'existOnMagento';
    Const CUSTOMER_EXIST_ON_ECOSYSTEM = 'existOnEco';
    Const CUSTOMER_HAS_NOT_ON_MAGENTO = 'hasNotOnMagento';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory
     */
    private $socialCollectionFactory;
    /**
     * @var \SM\Customer\Api\TransCustomerRepositoryInterface
     */
    protected $customerTransRepository;

    /**
     * @var \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * UserExist constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \SM\Customer\Api\TransCustomerRepositoryInterface $customerTransRepository
     * @param \Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory $socialCollectionFactory
     * @param \Trans\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\AuthenticationInterface $authentication,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \SM\Customer\Api\TransCustomerRepositoryInterface $customerTransRepository,
        \Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory $socialCollectionFactory,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->socialCollectionFactory = $socialCollectionFactory;
        $this->customerTransRepository = $customerTransRepository;
        $this->accountManagement = $accountManagement;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $user = $this->getRequest()->getParam('user');
        $user = preg_replace('/\s/', '', $user);
        $typeCheck = $this->getRequest()->getParam('type');
        $forgotForm = false;
        $lockForm = false;
        $customerEcosystem = self::CUSTOMER_HAS_NOT_ON_ECOSYSTEM;
        $customerOnMagento = self::CUSTOMER_HAS_NOT_ON_MAGENTO;
        $customerEcoData = NULL;
        $customerDataJson = NULL;
        if ($typeCheck && $typeCheck == 'forgot') {
            $forgotForm = true;
        } elseif ($typeCheck && $typeCheck == 'lock') {
            $lockForm = true;
        }
        $message = '';
        $customer = false;
        $lock = false;
        if ($this->isEmail($user)) {
            $type = 'email';
            try {
                $customer = $this->customerRepository->get($user);
                $socialCollection = $this->socialCollectionFactory->create();
                $customerSocial = $socialCollection
                    ->join("customer_entity", "customer_entity.entity_id = main_table.customer_id", ["password_hash"])
                    ->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();
                if ($customerSocial->getId() && $customerSocial->getData("password_hash") == NULL) {
                    $customerOnMagento = self::CUSTOMER_EXIST_ON_MAGENTO;
                    $message = __('You are linked with %1. Please sign in with your %1 account',
                        $customerSocial->getType());
                } else {
                    $message = $this->verify($customer);
                }
                if ($customer->getId()) {
                    $customerOnMagento = self::CUSTOMER_EXIST_ON_MAGENTO;
                }
            } catch (\Exception $e) {
                // user not registered
                if ($forgotForm) {
                    $message = __('You are not yet registered');
                } else {
                    $message = __('This email address is not registered');
                }
            }
        } else {
            $type = 'telephone';
            if (strlen($user) < 10 || strlen($user) > 16) {
                $message = __("Make sure you follow the format");
            }
            else {
                try {
                    $customer = $this->customerTransRepository->getByPhone($user);
                    $socialCollection = $this->socialCollectionFactory->create();
                    $customerSocial = $socialCollection->addFieldToFilter('customer_id',
                        $customer->getId())->getFirstItem();
                    if ($customerSocial->getId()) {
                        $customerOnMagento = self::CUSTOMER_EXIST_ON_MAGENTO;
                        $message = __('You are linked with %1. Please sign in with your %1 account',
                            $customerSocial->getType());
                    }
                    if ($customer->getId()) {
                        $customerOnMagento = self::CUSTOMER_EXIST_ON_MAGENTO;
                    }
                } catch (\Exception $e) {
                    // user not registered
                    if ($forgotForm) {
                        $message = __('You are not yet registered');
                    } else {
                        $message = __('This mobile number is not registered');
                    }
                }
            }
        }
        if ($message == '' && !$lockForm && $customer) {
            if ($this->authentication->isLocked($customer->getId())) {
                $message = __('Your account is locked');
                $lock = true;
            }
        }

        //return response if customer has exist account on magento system
        if ($customerOnMagento == self::CUSTOMER_EXIST_ON_MAGENTO) {
            $resultJson->setData([
                'status' => $lock,
                'type' => $type,
                'message' => $message,
                'customerEcosystem' => $customerEcosystem,
                'customerOnMagento' => $customerOnMagento,
                'central_id' => '',
                'customer_name' => '',
                'customer_email' => '',
                'customer_phone' => '',
                'customer_firstname' => '',
                'customer_lastname' => ''
            ]);

            return $resultJson;
        }

        /** check customer info on ecosystem exist */
        if ($customerOnMagento == self::CUSTOMER_HAS_NOT_ON_MAGENTO) {
            $responCheckInfoOnEco = $this->accountManagement->checkCustomerRegister($user, NULL);
            //if $responCheckInfoOnEco is true so customer on ecosystem
            if (!strpos($responCheckInfoOnEco, 'error')) {
                $checkCentral = $this->serializer->unserialize($responCheckInfoOnEco);
                $responStatus = $checkCentral['status'];
                $responinfoCustomer = '';
                if (is_array($responStatus)) {
                    foreach ($responStatus as $key => $respon) {
                        $responinfoCustomer = $respon;
                    }
                }

                if ($responinfoCustomer != '' && isset($responinfoCustomer['email_address_status']) && $responinfoCustomer['email_address_status'] == 1) {
                    $customerEcosystem = self::CUSTOMER_EXIST_ON_ECOSYSTEM;
                }
                if ($responinfoCustomer != '' && isset($responinfoCustomer['phone_number_status']) && $responinfoCustomer['phone_number_status'] == 1) {
                    $customerEcosystem = self::CUSTOMER_EXIST_ON_ECOSYSTEM;
                }

                /** get account info by phone number*/
                if ($customerEcosystem == self::CUSTOMER_EXIST_ON_ECOSYSTEM) {
                    $getCustomerData = $this->accountManagement->getCentralCustomerData($user, NULL);
                    $getCustomerData != NULL ? $customerDataJson = $this->serializer->unserialize($getCustomerData) : $customerDataJson;
                }
            }
        }

        if ($customerEcosystem == self::CUSTOMER_EXIST_ON_ECOSYSTEM
            && $customerOnMagento == self::CUSTOMER_HAS_NOT_ON_MAGENTO
            && isset($customerDataJson['central_id'])) {
            $resultJson->setData([
                'status' => $lock,
                'type' => $type,
                'message' => $message,
                'customerEcosystem' => $customerEcosystem,
                'customerOnMagento' => $customerOnMagento,
                'central_id' => $customerDataJson['central_id'],
                'customer_name' => $customerDataJson['customer_name'],
                'customer_email' => $customerDataJson['customer_email'],
                'customer_phone' => $customerDataJson['customer_phone'],
                'customer_firstname' => $customerDataJson['customer_first_name'],
                'customer_lastname' => $customerDataJson['customer_last_name']
            ]);
        } else {
            $resultJson->setData([
                'status' => $lock,
                'type' => $type,
                'message' => $message,
                'customerEcosystem' => $customerEcosystem,
                'customerOnMagento' => $customerOnMagento,
                'central_id' => '',
                'customer_name' => '',
                'customer_email' => '',
                'customer_phone' => '',
                'customer_firstname' => '',
                'customer_lastname' => ''
            ]);
        }

        return $resultJson;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Framework\Phrase|string
     */
    protected function verify($customer)
    {
        $customerAttribute = $customer->getCustomAttribute(\SM\Customer\Helper\Config::IS_VERIFIED_EMAIL_ATTRIBUTE_CODE);
        if ($customerAttribute != NULL) {
            if ($customerAttribute->getValue()) {
                return '';
            }
        }

        return __("You haven't verified this email. Please check your inbox");
    }

    /**
     * Check is email
     * @param string $mail
     * @return bool
     */
    protected function isEmail($mail)
    {
        return (!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/", $mail)) ? false : true;
    }
}
