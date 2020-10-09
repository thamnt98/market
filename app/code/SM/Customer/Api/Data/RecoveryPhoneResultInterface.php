<?php


namespace SM\Customer\Api\Data;


interface RecoveryPhoneResultInterface
{
    const CUSTOMER_TOKEN = 'customer_token';
    const RESET_PASSWORD_TOKEN = 'reset_password_token';

    /**
     * @return string
     */
    public function getCustomerToken();

    /**
     * @param string $customerToken
     * @return $this
     */
    public function setCustomerToken($customerToken);

    /**
     * @return string
     */
    public function getResetPasswordToken();

    /**
     * @param string $resetPasswordToken
     * @return $this
     */
    public function setResetPasswordToken($resetPasswordToken);
}
