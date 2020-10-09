<?php


namespace SM\Customer\Model\Api\Data;


use SM\Customer\Api\Data\RecoveryPhoneResultInterface;

class RecoveryPhoneResult extends \Magento\Framework\Model\AbstractExtensibleModel implements RecoveryPhoneResultInterface
{

    /**
     * @inheritDoc
     */
    public function getCustomerToken()
    {
        return $this->getData(self::CUSTOMER_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerToken($customerToken)
    {
        return $this->setData(self::CUSTOMER_TOKEN, $customerToken);
    }

    /**
     * @inheritDoc
     */
    public function getResetPasswordToken()
    {
        return $this->getData(self::RESET_PASSWORD_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setResetPasswordToken($resetPasswordToken)
    {
        return $this->setData(self::RESET_PASSWORD_TOKEN, $resetPasswordToken);
    }
}
