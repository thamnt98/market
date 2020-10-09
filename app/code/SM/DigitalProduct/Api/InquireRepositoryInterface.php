<?php

namespace SM\DigitalProduct\Api;

/**
 * Interface InquireRepositoryInterface
 * @package SM\DigitalProduct\Api
 */
interface InquireRepositoryInterface
{
    /**
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @return \SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPrePaidDataInterface
     */
    public function inquireElectricityPrePaid($customerNumber, $customerId, $productId);

    /**
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @return \SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPostPaidDataInterface
     */
    public function inquireElectricityPostPaid($customerNumber, $customerId, $productId);

    /**
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @param string $operatorCode
     * @return \SM\DigitalProduct\Api\Data\Inquire\InquirePdamDataInterface
     */
    public function inquirePdam($customerNumber, $customerId, $productId, $operatorCode);

    /**
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @param string $paymentPeriod
     * @return \SM\DigitalProduct\Api\Data\Inquire\InquireBpjsDataInterface
     */
    public function inquireBpjs($customerNumber, $customerId, $productId, $paymentPeriod);

    /**
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @return \SM\DigitalProduct\Api\Data\Inquire\InquireMobilePostpaidDataInterface
     */
    public function inquireMobilePostpaid($customerNumber, $customerId, $productId);

    /**
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @return \SM\DigitalProduct\Api\Data\Inquire\InquireTelkomDataInterface
     */
    public function inquireTelkom($customerNumber, $customerId, $productId);
}
