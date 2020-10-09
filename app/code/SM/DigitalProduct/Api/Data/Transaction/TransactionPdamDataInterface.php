<?php


namespace SM\DigitalProduct\Api\Data\Transaction;

/**
 * Interface TransactionPdamDataInterface
 * @package SM\DigitalProduct\Api\Data\Transaction
 */
interface TransactionPdamDataInterface extends TransactionDataInterface
{
    const OPERATOR_CODE = "operator_code";

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
    public function getTransactionData();

    /**
     * @return string
     */
    public function getOperatorCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setOperatorCode($value);
}
