<?php


namespace SM\DigitalProduct\Model\Data\Transaction;

use SM\DigitalProduct\Api\Data\Transaction\TransactionElectricityPrePaidDataInterface;

/**
 * Class TransactionElectricityPrePaidData
 * @package SM\DigitalProduct\Model\Data\Transaction
 */
class TransactionElectricityPrePaidData extends TransactionData implements TransactionElectricityPrePaidDataInterface
{
    /**
     * @inheritDoc
     */
    public function getMeterNumber()
    {
        return $this->getData(self::METER_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getToken()
    {
        return $this->getData(self::TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setMeterNumber($value)
    {
        return $this->setData(self::METER_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setToken($value)
    {
        return $this->setData(self::TOKEN, $value);
    }
}
