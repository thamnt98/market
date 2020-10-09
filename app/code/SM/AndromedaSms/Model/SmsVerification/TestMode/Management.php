<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\SmsVerification\TestMode;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use SM\AndromedaApi\Model\Integration\Preparator as IntegrationPreparator;
use SM\AndromedaSms\Api\Data\SmsVerification\SendSms\TestMode\ResponseInterface as SendSmsResponse;
use SM\AndromedaSms\Api\Data\SmsVerification\SendSms\TestMode\ResponseInterfaceFactory as SendSmsResponseFactory;
use SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterface as VerifySmsResponse;
use SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterfaceFactory as VerifySmsResponseFactory;
use SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterface;
use SM\AndromedaSms\Api\Entity\SmsVerification\TestMode\SmsVerificationInterfaceFactory;
use SM\AndromedaSms\Api\Repository\SmsVerification\TestMode\SmsVerificationRepositoryInterface;
use SM\AndromedaSms\Api\SmsVerification\TestMode\ManagementInterface;

class Management implements ManagementInterface
{
    const VERIFICATION_CODE_LENGTH = 6;

    /**
     * @var SmsVerificationInterfaceFactory
     */
    protected $entityFactory;

    /**
     * @var SmsVerificationRepositoryInterface
     */
    protected $repository;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var SendSmsResponseFactory
     */
    protected $sendSmsResponseFactory;

    /**
     * @var VerifySmsResponseFactory
     */
    protected $verifySmsResponseFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var IntegrationPreparator
     */
    protected $integrationPreparator;

    /**
     * Management constructor.
     * @param SmsVerificationInterfaceFactory $entityFactory
     * @param SmsVerificationRepositoryInterface $repository
     * @param Validator $validator
     * @param SendSmsResponseFactory $sendSmsResponseFactory
     * @param VerifySmsResponseFactory $verifySmsResponseFactory
     * @param TimezoneInterface $timezone
     * @param IntegrationPreparator $integrationPreparator
     */
    public function __construct(
        SmsVerificationInterfaceFactory $entityFactory,
        SmsVerificationRepositoryInterface $repository,
        Validator $validator,
        SendSmsResponseFactory $sendSmsResponseFactory,
        VerifySmsResponseFactory $verifySmsResponseFactory,
        TimezoneInterface $timezone,
        IntegrationPreparator $integrationPreparator
    ) {
        $this->entityFactory = $entityFactory;
        $this->repository = $repository;
        $this->validator = $validator;
        $this->sendSmsResponseFactory = $sendSmsResponseFactory;
        $this->verifySmsResponseFactory = $verifySmsResponseFactory;
        $this->timezone = $timezone;
        $this->integrationPreparator = $integrationPreparator;
    }

    /**
     * @inheritDoc
     */
    public function send(string $authToken, string $phoneNumber, bool $is_check_phone_number, string $language): SendSmsResponse
    {
        $this->validator->validateAuthToken($authToken);

        $this->repository->deleteByPhoneNumber($phoneNumber);
        $smsVerification = $this->create($phoneNumber);

        return $this->responseSendSms($smsVerification);
    }

    /**
     * @inheritDoc
     */
    public function verify(string $authToken, string $verificationId, string $verificationCode): VerifySmsResponse
    {
        $this->validator->validateAuthToken($authToken);

        try {
            /** @var SmsVerificationInterface $smsVerification */
            $smsVerification = $this->repository->getByVerificationId($verificationId);
            $isVerified = $smsVerification->getVerificationCode() == $verificationCode;
        } catch (LocalizedException $exception) {
            $isVerified = false;
        }

        return $this->responseVerifySms($isVerified);
    }

    /**
     * @inheritDoc
     */
    public function getVerificationCodeByPhone(string $phoneNumber): string
    {
        /** @var SmsVerificationInterface $smsVerification */
        $smsVerification = $this->repository->getByPhoneNumber($phoneNumber);

        return $smsVerification->getVerificationCode();
    }

    /**
     * @inheritDoc
     */
    public function getToken(): array
    {
        $now = $this->timezone->date();
        $response = [];
        $i = 0;

        do {
            $date = clone $now;
            $date->modify("+{$i} minute");

            $response[] = [
                'locale_time' => $date->format(DateTime::DATETIME_PHP_FORMAT),
                'token' => $this->integrationPreparator->getToken($date),
                'utc_time' => $date->format(DateTime::DATETIME_PHP_FORMAT),
            ];

            $i++;
        } while ($i < 5);

        return $response;
    }

    /**
     * @param string $phoneNumber
     * @return SmsVerificationInterface
     * @throws LocalizedException
     */
    protected function create(string $phoneNumber): SmsVerificationInterface
    {
        /** @var SmsVerificationInterface $entity */
        $entity = $this->entityFactory->create();
        $entity->setPhoneNumber($phoneNumber);

        $verificationCode = (string) rand(100000, 999999);
        $entity->setVerificationCode($verificationCode);

        $this->repository->save($entity);

        return $entity;
    }

    /**
     * @param SmsVerificationInterface $smsVerification
     * @return SendSmsResponse
     */
    protected function responseSendSms(SmsVerificationInterface $smsVerification): SendSmsResponse
    {
        $response = $this->sendSmsResponseFactory->create();
        $response->setVerificationId($smsVerification->getVerificationId());
        return $response;
    }

    /**
     * @param bool $isVerified
     * @return VerifySmsResponse
     */
    protected function responseVerifySms(bool $isVerified): VerifySmsResponse
    {
        $response = $this->verifySmsResponseFactory->create();
        $response->setIsVerified($isVerified);
        return $response;
    }
}
