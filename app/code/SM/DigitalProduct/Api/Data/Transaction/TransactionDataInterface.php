<?php

namespace SM\DigitalProduct\Api\Data\Transaction;

/**
 * Interface TransactionDataInterface
 * @package SM\DigitalProduct\Api\Data\Transaction
 */
interface TransactionDataInterface
{
    const TRANSACTION_ID = "transaction_id";
    const TYPE = "type";
    const CREATED = "created";
    const CHANGED = "changed";
    const CUSTOMER_NUMBER  = "customer_number";
    const ORDER_ID = "order_id";
    const PRICE = "price";
    const STATUS = "status";
    const RESPONSE_CODE = "response_code";
    const SERIAL_NUMBER = "serial_number";
    const AMOUNT = "amount";
    const PRODUCT_ID = "product_id";
    const TRANSACTION_DATA = "transaction_data";

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
     * @param string $value
     * @return $this
     */
    public function setTransactionId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCreated($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setChanged($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomerNumber($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPrice($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setResponseCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSerialNumber($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAmount($value);

    /**
     * @param \SM\DigitalProduct\Api\Data\Transaction\ProductIdDataInterface $value
     * @return $this
     */
    public function setProductId($value);

    /**
     * @return string
     */
    public function getTransactionData();

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionData($value);
}
