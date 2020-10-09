<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\AndromedaApi\Model\Integration\Client;
use SM\AndromedaSms\Api\Data\SmsVerification\SendSms\TestMode\ResponseInterface as SendSmsResponse;
use SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterface as VerifySmsResponse;
use SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface;
use SM\AndromedaSms\Api\SmsVerificationInterface;
use SM\AndromedaSms\Helper\Config;
use SM\AndromedaSms\Model\SmsVerification\ActionResolver;
use SM\AndromedaSms\Model\SmsVerification\Calculator;
use SM\AndromedaSms\Model\SmsVerification\Management;
use SM\AndromedaSms\Model\SmsVerification\Validator;
use SM\Customer\Api\TransCustomerRepositoryInterface;

class SmsVerification implements SmsVerificationInterface
{
    const SEND_SMS_API_PATH = '/ma/send/sms/verification/v1.0';
    const VERIFY_SMS_API_PATH = '/ma/verify/sms/verification/v1.0';

    const PHONE_NUMBER = 'phone_number';
    const IS_CHECK_PHONE_NUMBER = 'is_check_phone_number';
    const LANGUAGE = 'language';
    const VERIFICATION_ID = 'verification_id';
    const VERIFICATION_CODE = 'verification_code';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Management
     */
    protected $management;

    /**
     * @var SmsVerificationRepositoryInterface
     */
    protected $repository;

    /**
     * @var Calculator
     */
    protected $calculator;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;

    /**
     * @var ActionResolver
     */
    protected $actionResolver;

    /**
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountManagement;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    protected $authentication;

    /**
     * SmsVerification constructor.
     * @param Client $client
     * @param Config $config
     * @param Management $management
     * @param SmsVerificationRepositoryInterface $repository
     * @param Calculator $calculator
     * @param Validator $validator
     * @param TransCustomerRepositoryInterface $transCustomerRepository
     * @param ActionResolver $actionResolver
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     */
    public function __construct(
        Client $client,
        Config $config,
        Management $management,
        SmsVerificationRepositoryInterface $repository,
        Calculator $calculator,
        Validator $validator,
        TransCustomerRepositoryInterface $transCustomerRepository,
        ActionResolver $actionResolver,
        \Magento\Customer\Model\AccountManagement $accountManagement,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Customer\Model\AuthenticationInterface $authentication
    ) {
        $this->client = $client;
        $this->config = $config;
        $this->management = $management;
        $this->repository = $repository;
        $this->calculator = $calculator;
        $this->validator = $validator;
        $this->transCustomerRepository = $transCustomerRepository;
        $this->actionResolver = $actionResolver;
        $this->transCustomerRepository = $transCustomerRepository;
        $this->actionResolver = $actionResolver;
        $this->mathRandom = $mathRandom;
        $this->accountManagement = $accountManagement;
        $this->customerSession = $customerSessionFactory->create();
        $this->authentication = $authentication;
    }

    /**
     * @inheritDoc
     */
    public function send(string $phoneNumber, bool $checkExistCustomerPhone): bool
    {
        if (strlen($phoneNumber) < 10 || strlen($phoneNumber) > 16) {
            throw new InputException(__("Make sure you follow the format"));
        }

        try {
            $smsVerification = $this->repository->getByPhoneNumber($phoneNumber);
        } catch (NoSuchEntityException $exception) {
            $smsVerification = null;
        }

        if ($checkExistCustomerPhone) {
            try {
                $this->transCustomerRepository->getByPhone($phoneNumber);
            } catch (NoSuchEntityException $exception) {
                throw new LocalizedException(__($exception->getMessage()));
            }
        }

        $failedAttempt = $this->calculator->calculateFailedAttempt($smsVerification);
        $this->validator->validateFailedAttempt($failedAttempt, $smsVerification);

        $body = $this->prepareSendRequest($phoneNumber, $checkExistCustomerPhone);

        /** @var SendSmsResponse $result */
        $result = $this->client->send(
            SendSmsResponse::class,
            self::SEND_SMS_API_PATH,
            Client::HTTP_POST,
            $body
        );

        $this->repository->deleteByPhoneNumber($phoneNumber);
        $this->management->create($phoneNumber, $result->getVerificationId());

        return true;
    }

    /**
     * @inheritDoc
     */
    public function verify(string $phoneNumber, string $verificationCode, string $action = '')
    {
        $smsVerification = $this->repository->getByPhoneNumber($phoneNumber, true);
        $this->validator->validateExpired($smsVerification);
        $failedAttempt = $this->calculator->calculateFailedAttempt($smsVerification);
        $this->validator->validateFailedAttempt($failedAttempt, $smsVerification);
        $body = $this->prepareVerifyRequest($smsVerification->getVerificationId(), $verificationCode);

        /** @var VerifySmsResponse $result */
        $result = $this->client->send(
            VerifySmsResponse::class,
            self::VERIFY_SMS_API_PATH,
            Client::HTTP_POST,
            $body
        );
        if (!$result->getIsVerified()) {
            $smsVerification->setFailedAttempt($failedAttempt)->save();
        } else {
            if ($action == 'lock') {
                try {
                    $customerData = $this->transCustomerRepository->getByPhone($phoneNumber);
                    $newPasswordToken = $this->mathRandom->getUniqueHash();
                    $this->accountManagement->changeResetPasswordLinkToken($customerData, $newPasswordToken);
                    $customerId = $customerData->getId();
                    $this->authentication->unlock($customerId);
                    $this->customerSession->loginById($customerId);
                } catch (\Exception $e) {
                    throw new LocalizedException(__($e->getMessage()));
                }
            }
        }
        $this->validator->validateVerifySmsResponse($result);

        $this->management->updateVerified($smsVerification);

        return $this->actionResolver->resolve($phoneNumber, $action);
    }

    /**
     * @param string $phoneNumber
     * @param int $checkExistCustomerPhone
     * @return array
     */
    protected function prepareSendRequest($phoneNumber, $checkExistCustomerPhone): array
    {
        if ($checkExistCustomerPhone) {
            $checkExistCustomerPhone = 1;
        } else {
            $checkExistCustomerPhone = 0;
        }
        return [
            self::PHONE_NUMBER => $phoneNumber,
            self::IS_CHECK_PHONE_NUMBER => $checkExistCustomerPhone,
            self::LANGUAGE => $this->config->getSmsLanguage(),
        ];
    }

    /**
     * @param string $verificationId
     * @param string $verificationCode
     * @return array
     */
    protected function prepareVerifyRequest(string $verificationId, string $verificationCode): array
    {
        return [
            self::VERIFICATION_ID => $verificationId,
            self::VERIFICATION_CODE => $verificationCode,
        ];
    }
}
