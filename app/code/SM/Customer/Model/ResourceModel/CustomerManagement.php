<?php

namespace SM\Customer\Model\ResourceModel;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\Webapi\Exception as HTTPExceptionCodes;
use Psr\Log\LoggerInterface;
use SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface;
use SM\AndromedaSms\Api\SmsVerificationInterface;
use SM\AndromedaSms\Model\SmsVerification\Calculator;
use SM\AndromedaSms\Model\SmsVerification\Validator;
use SM\Customer\Api\TransCustomerManagementInterface;
use SM\Customer\Api\TransCustomerRepositoryInterface;
use SM\Customer\Helper\Config;
use SM\Customer\Helper\Customer as CustomerHelper;
use SM\Customer\Model\Api\Data\RecoveryPhoneResultFactory;
use SM\Customer\Model\Customer\ResetPasswordToken;
use SM\Customer\Model\Email\Sender as EmailSender;
use SM\Customer\Model\ValidateHash;

class CustomerManagement implements TransCustomerManagementInterface
{
    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;

    /**
     * @var CustomerHelper
     */
    protected $customerHelper;

    /**
     * @var SmsVerificationInterface
     */
    protected $otpManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var AddressRegistry|mixed|null
     */
    protected $addressRegistry;

    /**
     * @var Sender
     */
    protected $email;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerModel
     */
    protected $customerModel;

    /**
     * @var Config
     */
    protected $customerConfigHelper;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Calculator
     */
    protected $calculator;

    /**
     * @var SmsVerificationRepositoryInterface
     */
    protected $repository;

    /**
     * @var RecoveryPhoneResultFactory
     */
    protected $resultRecoveryPhone;

    /**
     * @var ResetPasswordToken
     */
    protected $resetPasswordToken;

    /**
     * @var ValidateHash
     */
    protected $validateHash;
    /**
     * @var CoreSession
     */
    private $coreSession;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var \Magento\Webapi\Model\Authorization\TokenUserContext
     */
    protected $tokenUserContext;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * ActionResolver constructor.
     * @param CoreSession $coreSession
     * @param TransCustomerRepositoryInterface $transCustomerRepository
     * @param CustomerHelper $customerHelper
     * @param SmsVerificationInterface $otpManagement
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CustomerRegistry $customerRegistry
     * @param EmailSender $emailSender
     * @param LoggerInterface $logger
     * @param CustomerModel $customerModel
     * @param Config $customerConfigHelper
     * @param SmsVerificationRepositoryInterface $repository
     * @param Calculator $calculator
     * @param Validator $validator
     * @param RecoveryPhoneResultFactory $recoveryPhoneResultFactory
     * @param ResetPasswordToken $resetPasswordToken
     * @param ValidateHash $validateHash
     * @param AddressRegistry|null $addressRegistry
     * @param State $state
     * @param \Magento\Webapi\Model\Authorization\TokenUserContext $tokenUserContext
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        CoreSession $coreSession,
        TransCustomerRepositoryInterface $transCustomerRepository,
        CustomerHelper $customerHelper,
        SmsVerificationInterface $otpManagement,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerRegistry $customerRegistry,
        EmailSender $emailSender,
        LoggerInterface $logger,
        CustomerModel $customerModel,
        Config $customerConfigHelper,
        SmsVerificationRepositoryInterface $repository,
        Calculator $calculator,
        Validator $validator,
        RecoveryPhoneResultFactory $recoveryPhoneResultFactory,
        ResetPasswordToken $resetPasswordToken,
        ValidateHash $validateHash,
        AddressRegistry $addressRegistry = null,
        State $state,
        \Magento\Webapi\Model\Authorization\TokenUserContext $tokenUserContext,
        \Magento\Framework\Registry $registry
    ) {
        $this->transCustomerRepository = $transCustomerRepository;
        $this->customerHelper          = $customerHelper;
        $this->otpManagement           = $otpManagement;
        $this->customerRepository      = $customerRepositoryInterface;
        $this->customerRegistry        = $customerRegistry;
        $this->email                   = $emailSender;
        $this->logger                  = $logger;
        $this->customerModel           = $customerModel;
        $objectManager                 = ObjectManager::getInstance();
        $this->addressRegistry         = $addressRegistry ?: $objectManager->get(AddressRegistry::class);
        $this->customerConfigHelper    = $customerConfigHelper;
        $this->repository              = $repository;
        $this->calculator              = $calculator;
        $this->validator               = $validator;
        $this->resultRecoveryPhone     = $recoveryPhoneResultFactory;
        $this->resetPasswordToken      = $resetPasswordToken;
        $this->validateHash            = $validateHash;
        $this->coreSession             = $coreSession;
        $this->state                   = $state;
        $this->tokenUserContext        = $tokenUserContext;
        $this->registry                = $registry;
    }

    /**
     * Verify Phone and send OTP
     * @param $phone
     * @return bool
     * @throws LocalizedException
     * @throws Exception
     */
    public function verifyPhone($phone)
    {
        if (strlen($phone) < 10 || strlen($phone) > 16) {
            throw new InputException(__("Make sure you follow the format"));
        }

        //Verify Phone
        $isPhoneNumber      = $this->customerHelper->isPhone($phone);
        $convertPhoneNumber = $this->customerConfigHelper->trimTelephone($phone);
        $customerId         = $this->customerHelper->getByPhone($convertPhoneNumber);
        if (!$isPhoneNumber) {
            throw new LocalizedException(__('Make sure you follow the format'));
        }

        if ($customerId == null) {
            throw new LocalizedException(__('The mobile number is not registered'));
        }

        //Send Otp
        $sendOtp = $this->otpManagement->send($phone, false);
        return ($sendOtp) ? true : false;
    }

    /**
     * @param $email
     * @param $password
     * @return string
     * @throws AuthenticationException
     * @throws Exception
     */
    public function loginByEmail($email, $password)
    {
        try {
            $customer             = $this->customerRepository->get($email);
            $isCustomerLocked     = $this->customerHelper->isCustomerLock($customer->getId());
            $isRequestTokenLocked = $this->customerHelper->isRequestTokenLock($customer);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }

        if ($isCustomerLocked || $isRequestTokenLocked) {
            $message = __('Your account is locked!');
            throw new HTTPExceptionCodes(
                $message,
                HTTPExceptionCodes::HTTP_FORBIDDEN,
                HTTPExceptionCodes::HTTP_FORBIDDEN
            );
        }

        $customerToken = $this->customerHelper->getCustomerToken($email, $password);
        if ($customerToken) {
            //If customer login success, remove failures_count in customer_entity table
            //Do this function to sync with request_log failures in oauth_token_request_log table
            //Request Lock make customer can't login so we need sync two feature to mobile can handle case customer locked
            $this->customerHelper->unLockCustomer($customer->getId());
            $this->coreSession->setLoginTypeGtm('Email');
        }
        return $customerToken;
    }

    /**
     * @param string $phone
     * @param string $otpCode
     * @return int|void
     * @throws Exception
     */
    public function loginByPhone($phone, $otpCode)
    {
        $convertPhoneNumber = $this->customerConfigHelper->trimTelephone($phone);
        $customerId         = $this->customerHelper->getByPhone($convertPhoneNumber);
        if (!$customerId) {
            throw new Exception(sprintf(__('The phone number %s is not exits'), $phone));
        }

        $this->verifyFailedAttempt($phone);
        $this->otpManagement->verify($phone, $otpCode);

        $customer             = $this->customerRepository->getById($customerId);
        $isCustomerLocked     = $this->customerHelper->isCustomerLock($customer->getId());
        $isRequestTokenLocked = $this->customerHelper->isRequestTokenLock($customer);

        if ($isCustomerLocked || $isRequestTokenLocked) {
            $message = __('Your account is locked!');
            throw new HTTPExceptionCodes(
                $message,
                HTTPExceptionCodes::HTTP_FORBIDDEN,
                HTTPExceptionCodes::HTTP_FORBIDDEN
            );
        }

        $this->coreSession->setLoginTypeGtm('Phone Number');

//        if($this->isLoggedInByAPI()){
//
//        }

        return $this->customerHelper->createTokenByCustomerId($customerId);
    }

    /**
     * @param $phone
     * @throws LocalizedException
     */
    protected function verifyFailedAttempt($phone)
    {
        try {
            $smsVerification = $this->repository->getByPhoneNumber($phone);
        } catch (NoSuchEntityException $exception) {
            $smsVerification = null;
        }
        $failedAttempt = $this->calculator->calculateFailedAttempt($smsVerification);
        $this->validator->validateFailedAttempt($failedAttempt, $smsVerification);
    }

    /**
     * @param string $phoneNumber
     * @param string $passwords
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function resetPasswordByPhoneNumber(string $phoneNumber, string $passwords)
    {
        $convertPhoneNumber = $this->customerConfigHelper->trimTelephone($phoneNumber);
        $customerId         = $this->customerHelper->getByPhone($convertPhoneNumber);
        $customer           = $this->customerRepository->getById($customerId);

        $this->disableAddressValidation($customer);
        try {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $this->validateHash->validate($customerSecure, $passwords);
            $customerSecure->setRpToken($this->customerHelper->getResetPasswordToken());
            $customerSecure->setRpTokenCreatedAt($this->customerHelper->getCurrentTime());
            $customerSecure->setPasswordHash($this->customerHelper->getPasswordHash($passwords));
            $this->setIgnoreValidationFlag($customer);
            $this->customerRepository->save($customer);
            $message = __('Your password is changed');
            return $message->getText();
        } catch (Exception $exception) {
            $message = __('Cannot change customer password, please check again.');
            throw new Exception($message, HTTPExceptionCodes::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Set ignore_validation_flag for reset password flow to skip unnecessary address and customer validation
     * @param CustomerInterface $customer
     */
    protected function setIgnoreValidationFlag($customer)
    {
        $customer->setData('ignore_validation_flag', true);
    }

    /**
     * Disable Customer Address Validation
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    protected function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }

    /**
     * @param string $email
     * @return bool|string
     * @throws Exception
     */
    public function recoveryAccountWithEmail($email)
    {
        try {
            $customer = $this->customerRepository->get($email);
            $this->email->sendRecoveryEmail($customer, 'recovery');
            return true;
        } catch (NoSuchEntityException $exception) {
            throw new NoSuchEntityException(__('This email address is not registered'));
        } catch (Exception $e) {
            throw new HTTPExceptionCodes(
                __('Sorry, we can\'t send email at this time'),
                0,
                HTTPExceptionCodes::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param string $phone
     * @param string $otpCode
     * @return \SM\Customer\Api\Data\RecoveryPhoneResultInterface|\SM\Customer\Model\Api\Data\RecoveryPhoneResult
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function recoveryAccountWithPhone($phone, $otpCode)
    {
        $convertPhoneNumber = $this->customerConfigHelper->trimTelephone($phone);
        $customerId         = $this->customerHelper->getByPhone($convertPhoneNumber);
        $resetPasswordToken = null;

        if (!$customerId) {
            throw new NoSuchEntityException(__('The mobile number is not registered'));
        }

        $this->otpManagement->verify($phone, $otpCode);
        $this->customerHelper->unLockCustomer($customerId);
        $this->customerHelper->unLockTokenRequestCustomer($customerId);

        $customerToken = $this->customerHelper->createTokenByCustomerId($customerId);
        if ($this->resetPasswordToken->addResetPasswordToken($customerToken)) {
            $resetPasswordToken = $this->resetPasswordToken->getResetPasswordToken($customerToken, $customerId);
        }

        $result = $this->resultRecoveryPhone->create();
        $result->setCustomerToken($customerToken);
        $result->setResetPasswordToken($resetPasswordToken);

        return $result;
    }

    /**
     * @param $customerId
     * @param $resetPasswordToken
     * @param $newPassword
     * @return bool
     * @throws HTTPExceptionCodes
     * @throws Exception
     */
    public function resetPasswordRecovery($customerId, $resetPasswordToken, $newPassword)
    {
        //Verify resetPasswordToken
        $this->resetPasswordToken->verifyResetPasswordToken($resetPasswordToken, $customerId);

        //Change password customer
        try {
            $customer = $this->customerRepository->getById($customerId);

            $this->disableAddressValidation($customer);
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $this->validateHash->validate($customerSecure, $newPassword);
            $customerSecure->setRpToken($this->customerHelper->getResetPasswordToken());
            $customerSecure->setRpTokenCreatedAt($this->customerHelper->getCurrentTime());
            $customerSecure->setPasswordHash($this->customerHelper->getPasswordHash($newPassword));
            $this->setIgnoreValidationFlag($customer);
            $this->customerRepository->save($customer);
        } catch (LocalizedException $exception) {
            throw new HTTPExceptionCodes(
                __('Sorry, we can\'t change password right now!.'),
                0,
                HTTPExceptionCodes::HTTP_FORBIDDEN
            );
        }

        //After change password, remove reset password token
        $this->resetPasswordToken->removeResetPasswordToken($customerId, $resetPasswordToken);

        return true;
    }

    /**
     * Change and send email
     * @param int $customerId
     * @param string $newEmail
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function sendEmailVerify($customerId, $newEmail)
    {
        $customerModel = $this->customerModel->load($customerId);
        if ($customerModel->getEmail() == $newEmail) {
            throw new LocalizedException(__('Make sure your email is different than old email'));
        }
        try {
            $customerModel->setEmail($newEmail);
            $customerModel->save();
            $customer = $customerModel->getDataModel();
            $this->disableAddressValidation($customer);
            $this->setIgnoreValidationFlag($customer);
            $this->email->sendVerifyEmail($customer);
            return true;
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage());
            throw new LocalizedException(__('Something wrong while send email verify'));
        }
    }

    /**
     * @inheritDoc
     */
    public function loginByFaceId()
    {
        //get customer id base on customer token
        $userId = $this->tokenUserContext->getUserId();

        if ($userId == null) {
            throw new HTTPExceptionCodes(__('Token expires or invalid'), 0, HTTPExceptionCodes::HTTP_UNAUTHORIZED);
        }

        try {
            $customer             = $this->customerRepository->getById($userId);
            $isCustomerLocked     = $this->customerHelper->isCustomerLock($customer->getId());
            $isRequestTokenLocked = $this->customerHelper->isRequestTokenLock($customer);
        } catch (NoSuchEntityException $noSuchEntityException) {
            throw new HTTPExceptionCodes(
                __('No such entity with customer id %customerId', $userId),
                0,
                HTTPExceptionCodes::HTTP_UNAUTHORIZED
            );
        } catch (LocalizedException $e) {
            throw new HTTPExceptionCodes(__($e->getMessage()), 0, HTTPExceptionCodes::HTTP_UNAUTHORIZED);
        }

        if ($isCustomerLocked || $isRequestTokenLocked) {
            $message = __('Your account is locked!');
            throw new HTTPExceptionCodes(
                $message,
                HTTPExceptionCodes::HTTP_FORBIDDEN,
                HTTPExceptionCodes::HTTP_FORBIDDEN
            );
        }

        return $this->customerHelper->createTokenByCustomerId($userId);
    }

    public function isLoggedInByAPI()
    {
        if ($this->state->getAreaCode() == Area::AREA_WEBAPI_REST && (bool)$this->tokenUserContext->getUserId()) {
            return true;
        }
    }
}
