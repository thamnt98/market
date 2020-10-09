<?php

namespace SM\Customer\Api;

use Magento\Framework\Webapi\Exception as HTTPExceptionCodes;

interface TransCustomerManagementInterface
{
    /**
     * @param string $phone
     * @return boolean
     */
    public function verifyPhone($phone);

    /**
     * @param string $email
     * @param string $password
     * @return string
     */
    public function loginByEmail($email, $password);

    /**
     * @param string $phone
     * @param string $otpCode
     * @return integer
     */
    public function loginByPhone($phone, $otpCode);

    /**
     * @param string $phoneNumber
     * @param string $newPassword
     * @return string
     */
    public function resetPasswordByPhoneNumber(string $phoneNumber, string $newPassword);

    /**
     * @param string $phone
     * @param string $otpCode
     * @return \SM\Customer\Api\Data\RecoveryPhoneResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function recoveryAccountWithPhone($phone, $otpCode);

    /**
     * @param string $email
     * @return string|bool
     */
    public function recoveryAccountWithEmail($email);

    /**
     * @param int $customerId
     * @param string $resetPasswordToken
     * @param string $newPassword
     * @throws HTTPExceptionCodes
     * @return bool
     */
    public function resetPasswordRecovery($customerId, $resetPasswordToken, $newPassword);

    /**
     * @param integer $customerId
     * @param string $newEmail
     * @return boolean
     */
    public function sendEmailVerify($customerId, $newEmail);

    /**
     * @return string
     * @throws HTTPExceptionCodes
     */
    public function loginByFaceId();
}
