<?php

namespace SM\DigitalProduct\Api\Data\Transaction;

/**
 * Interface TransactionMobileDataInterface
 * @package SM\DigitalProduct\Api\Data\Transaction
 */
interface TransactionMobileDataInterface extends TransactionDataInterface
{
    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getCreated();

    /**
     * @return string
     */
    public function getChanged();

    /**
     * @return string
     */
    public function getCustomerNumber();

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getResponseCode();

    /**
     * @return string
     */
    public function getSerialNumber();

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @return \SM\DigitalProduct\Api\Data\Transaction\MobileProductIdDataInterface
     */
    public function getProductId();

    /**
     * @param \SM\DigitalProduct\Api\Data\Transaction\MobileProductIdDataInterface $value
     * @return $this
     */
    public function setProductId($value);

    /**
     * @return string
     */
    public function getTransactionData();
}
