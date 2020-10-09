<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\ResourceModel\SmsVerification\TestMode;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterface;

class SmsVerification extends AbstractDb
{
    /**
     * Construct method
     */
    protected function _construct()
    {
        $this->_init('testmode_andromeda_sms_verification', 'verification_id');
    }

    /**
     * @param string $phoneNumber
     * @throws LocalizedException
     */
    public function deleteByPhoneNumber(string $phoneNumber): void
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), [SmsVerificationInterface::PHONE_NUMBER . ' = ?' => $phoneNumber]);
    }
}
