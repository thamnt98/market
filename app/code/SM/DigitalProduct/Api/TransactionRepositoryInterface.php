<?php

namespace SM\DigitalProduct\Api;

/**
 * Interface TransactionRepositoryInterface
 * @package SM\DigitalProduct\Api
 */
interface TransactionRepositoryInterface
{

    /**
     * @param int $productId
     * @param int $customerId
     * @param string $customerNumber
     * @param string $orderId
     * @return \SM\DigitalProduct\Api\Data\Transaction\TransactionMobileDataInterface
     */
    public function transactionMobile($productId, $customerId, $customerNumber, $orderId);

    /**
     * @param int $productId
     * @param string $meterNumber
     * @param int $customerId
     * @param string $customerNumber
     * @param string $orderId
     * @return \SM\DigitalProduct\Api\Data\Transaction\TransactionElectricityPrePaidDataInterface
     */
    public function transactionElectricityPrePaid($productId, $meterNumber, $customerId, $customerNumber, $orderId);

    /**
     * @param int$productId
     * @param int $customerId
     * @param string $customerNumber
     * @param string $orderId
     * @return \SM\DigitalProduct\Api\Data\Transaction\TransactionDataInterface
     */
    public function transactionElectricityPostPaid($productId, $customerId, $customerNumber, $orderId);

    /**
     * @param int $productId
     * @param int $customerId
     * @param string $customerNumber
     * @param string $orderId
     * @param string $operatorCode
     * @return \SM\DigitalProduct\Api\Data\Transaction\TransactionPdamDataInterface
     */
    public function transactionPdam($productId, $customerId, $customerNumber, $orderId, $operatorCode);

    /**
     * @param int $productId
     * @param int $customerId
     * @param string $customerNumber
     * @param string $orderId
     * @param string $paymentPeriod
     * @return \SM\DigitalProduct\Api\Data\Transaction\TransactionBpjsDataInterface
     */
    public function transactionBpjs($productId, $customerId, $customerNumber, $orderId, $paymentPeriod);

    /**
     * @param int $productId
     * @param int $customerId
     * @param string $customerNumber
     * @param string $orderId
     * @return \SM\DigitalProduct\Api\Data\Transaction\TransactionDataInterface
     */
    public function transactionMobilePostpaid($productId, $customerId, $customerNumber, $orderId);

    /**
     * @param int $productId
     * @param int $customerId
     * @param string $customerNumber
     * @param string $orderId
     * @return \SM\DigitalProduct\Api\Data\Transaction\TransactionDataInterface
     */
    public function transactionTelkom($productId, $customerId, $customerNumber, $orderId);
}
