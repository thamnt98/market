<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Api;

interface SmsVerificationInterface
{
    /**
     * @param string $phoneNumber
     * @param bool $checkExistCustomerPhone
     * @return bool
     * @throws \Exception
     */
    public function send(string $phoneNumber, bool $checkExistCustomerPhone): bool;

    /**
     * @param string $phoneNumber
     * @param string $verificationCode
     * @param string $action
     * @return string|bool
     * @throws \Exception
     */
    public function verify(string $phoneNumber, string $verificationCode, string $action = '');
}
