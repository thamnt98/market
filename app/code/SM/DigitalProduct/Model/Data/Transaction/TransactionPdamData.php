<?php


namespace SM\DigitalProduct\Model\Data\Transaction;

use SM\DigitalProduct\Api\Data\Transaction\TransactionPdamDataInterface;

/**
 * Class TransactionPdamData
 * @package SM\DigitalProduct\Model\Data\Transaction
 */
class TransactionPdamData extends TransactionData implements TransactionPdamDataInterface
{

    /**
     * @inheritDoc
     */
    public function getOperatorCode()
    {
        return $this->getData(self::OPERATOR_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setOperatorCode($value)
    {
        return $this->setData(self::OPERATOR_CODE, $value);
    }
}
