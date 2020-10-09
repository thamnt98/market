<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api\SmsVerification\TestMode;

use SM\AndromedaSms\Api\Data\SmsVerification\SendSms\TestMode\ResponseInterface as SendSmsResponse;
use SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterface as VerifySmsResponse;

interface ManagementInterface
{
    /**
     * @param string $authToken
     * @param string $phoneNumber
     * @param bool $is_check_phone_number
     * @param string $language
     * @return \SM\AndromedaSms\Api\Data\SmsVerification\SendSms\TestMode\ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function send(string $authToken, string $phoneNumber, bool $is_check_phone_number, string $language): SendSmsResponse;

    /**
     * @param string $authToken
     * @param string $verificationId
     * @param string $verificationCode
     * @return \SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function verify(string $authToken, string $verificationId, string $verificationCode): VerifySmsResponse;

    /**
     * @param string $phoneNumber
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getVerificationCodeByPhone(string $phoneNumber): string;

    /**
     * @return mixed[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToken(): array;
}
