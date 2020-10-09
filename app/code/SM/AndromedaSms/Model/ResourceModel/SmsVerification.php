<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\ResourceModel;

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
        $this->_init('sms_verification', 'entity_id');
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
