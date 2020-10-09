<?php

namespace SM\DigitalProduct\Api\Data\Transaction;

/**
 * Interface TransactionElectricityPrePaidDataInterface
 * @package SM\DigitalProduct\Api\Data\Transaction
 */
interface TransactionElectricityPrePaidDataInterface extends TransactionDataInterface
{
    const METER_NUMBER = "meter_number";
    const TOKEN = "token";

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
     * @return \SM\DigitalProduct\Api\Data\Transaction\ProductIdDataInterface
     */
    public function getProductId();

    /**
     * @return string
     */
    public function getMeterNumber();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @return string
     */
    public function getTransactionData();

    /**
     * @param string $value
     * @return $this
     */
    public function setMeterNumber($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setToken($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionData($value);
}
