<?php


namespace SM\DigitalProduct\Model\Data\Transaction;

use SM\DigitalProduct\Api\Data\Transaction\TransactionBpjsDataInterface;

/**
 * Class TransactionBpjsData
 * @package SM\DigitalProduct\Model\Data\Transaction
 */
class TransactionBpjsData extends TransactionData implements TransactionBpjsDataInterface
{

    /**
     * @inheritDoc
     */
    public function getPaymentPeriod()
    {
        return $this->getData(self::PAYMENT_PERIOD);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentPeriod($value)
    {
        return $this->setData(self::PAYMENT_PERIOD, $value);
    }
}
