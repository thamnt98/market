<?php


namespace SM\DigitalProduct\Model\Data\Transaction;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\Transaction\TransactionDataInterface;

/**
 * Class TransactionData
 * @package SM\DigitalProduct\Model\Data\Transaction
 */
class TransactionData extends DataObject implements TransactionDataInterface
{

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getCreated()
    {
        return $this->getData(self::CREATED);
    }

    /**
     * @inheritDoc
     */
    public function getChanged()
    {
        return $this->getData(self::CHANGED);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerNumber()
    {
        return $this->getData(self::CUSTOMER_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getResponseCode()
    {
        return $this->getData(self::RESPONSE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getSerialNumber()
    {
        return $this->getData(self::SERIAL_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionId($value)
    {
        return $this->setData(self::TRANSACTION_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCreated($value)
    {
        return $this->setData(self::CREATED, $value);
    }

    /**
     * @inheritDoc
     */
    public function setChanged($value)
    {
        return $this->setData(self::CHANGED, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerNumber($value)
    {
        return $this->setData(self::CUSTOMER_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setResponseCode($value)
    {
        return $this->setData(self::RESPONSE_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSerialNumber($value)
    {
        return $this->setData(self::SERIAL_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionData()
    {
        return $this->getData(self::TRANSACTION_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionData($value)
    {
        return $this->setData(self::TRANSACTION_DATA, $value);
    }
}
