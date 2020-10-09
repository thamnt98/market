<?php

namespace SM\Customer\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\RequestLog;
use Magento\User\Helper\Data;
use SM\Customer\Model\ResourceModel\Customer as CustomerResourceModel;

class Customer extends AbstractHelper
{
    const OAUTH_LOCK_MAX_FAILURES_COUNT = 'oauth/authentication_lock/max_failures_count';

    /**
     * @var CustomerTokenServiceInterface
     */
    protected $customerToken;

    /**
     * @var CustomerResourceModel
     */
    protected $customerResourceModel;

    /**
     * @var TokenModelFactory
     */
    protected $tokenModelFactory;

    /**
     * @var Data
     */
    protected $userHelper;

    /**
     * @var EncryptorInterface
     */
    protected $encrypt;

    /**
     * @var AuthenticationInterface
     */
    protected $customerAuthentication;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepo;

    /**
     * @var CollectionFactory
     */
    protected $tokenCollectionFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var RequestLog
     */
    protected $tokenRequestLog;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        Context $context,
        CustomerTokenServiceInterface $customerTokenService,
        CustomerResourceModel $customerResourceModel,
        TokenModelFactory $tokenFactory,
        Data $userHelper,
        EncryptorInterface $encryptor,
        CollectionFactory $tokenCollectionFactory,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        AuthenticationInterface $authentication,
        CustomerRepositoryInterface $customerRepository,
        RequestLog $tokenRequestLog,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->customerToken            = $customerTokenService;
        $this->customerResourceModel    = $customerResourceModel;
        $this->tokenModelFactory        = $tokenFactory;
        $this->userHelper               = $userHelper;
        $this->encrypt                  = $encryptor;
        $this->customerAuthentication   = $authentication;
        $this->customerRepo             = $customerRepository;
        $this->tokenCollectionFactory   = $tokenCollectionFactory;
        $this->customerFactory          = $customerFactory;
        $this->customerSession          = $customerSession;
        $this->tokenRequestLog          = $tokenRequestLog;
        $this->scopeConfig              = $scopeConfig;
    }

    public function isEmail($mail)
    {
        return (!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/", $mail)) ? false : true;
    }

    public function isPhone($phone)
    {
        return (!preg_match("/^\+?(62|08|8)[0-9]+$/", $phone)) ? false : true;
    }

    /**
     * @param $username
     * @param $password
     * @return string
     * @throws AuthenticationException
     */
    public function getCustomerToken($username, $password)
    {
        return $this->customerToken->createCustomerAccessToken($username, $password);
    }

    /**
     * @param $phoneNumber
     * @return int|null
     * @throws LocalizedException
     */
    public function getByPhone($phoneNumber)
    {
        return $this->customerResourceModel->getCustomerIdByPhoneNumber($phoneNumber);
    }

    /**
     * @param $customerId
     * @return string
     */
    public function createTokenByCustomerId($customerId)
    {
        return $this->tokenModelFactory->create()->createCustomerToken($customerId)->getToken();
    }

    public function getResetPasswordToken()
    {
        return $this->userHelper->generateResetPasswordLinkToken();
    }

    public function getPasswordHash($password)
    {
        return $this->encrypt->getHash($password, true);
    }

    public function getCurrentTime()
    {
        return date(DateTime::DATETIME_PHP_FORMAT);
    }

    public function isCustomerLock($customerId)
    {
        $isLocked = $this->customerAuthentication->isLocked($customerId);
        if (!$isLocked) {
            return false;
        }

        return true;
    }

    public function isRequestTokenLock($customer)
    {
        $failuresCount = $this->tokenRequestLog->getFailuresCount(
            $customer->getEmail(),
            RequestThrottler::USER_TYPE_CUSTOMER
        );

        if ($failuresCount >= $this->scopeConfig->getValue(self::OAUTH_LOCK_MAX_FAILURES_COUNT)) {
            return true;
        }

        return  false;
    }

    public function unLockCustomer($customerId)
    {
        $this->customerAuthentication->unlock($customerId);
    }

    /**
     * @param $customerId
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function unLockTokenRequestCustomer($customerId)
    {
        $customer = $this->customerRepo->getById($customerId);
        $this->tokenRequestLog->resetFailuresCount($customer->getEmail(), RequestThrottler::USER_TYPE_CUSTOMER);
    }

    public function logout($customerId)
    {
        $customerModel = $this->customerFactory->create()->load($customerId);
        $cusDataModel  = $customerModel->getDataModel();
        $time          = time();
        $cusDataModel->setCustomAttribute('last_time_change_pwd', $time);
        $this->customerSession->setLastTimeChangePwdWhenLogged($time);
        $customerModel->updateData($cusDataModel);
        $customerModel->save();
        $this->tokenCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->walk('delete');
    }
}
