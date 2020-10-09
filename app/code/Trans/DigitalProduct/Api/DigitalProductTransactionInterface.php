<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Api;

/**
 * @api
 */
interface DigitalProductTransactionInterface
{

    /**
     * Transaction Mobile ( Pulsa & Data )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function mobile($customerId, $customerNumber, $productId, $orderId);

    /**
     * Transaction BPJS Kesehatan
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param string $paymentPeriod
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function bpjsKesehatan($customerId, $customerNumber, $paymentPeriod, $productId, $orderId);

    /**
     * Transaction Electricity ( PLN Reguler )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param string $meterNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function electricity($customerId, $customerNumber, $meterNumber, $productId, $orderId);

    /**
     * Transaction Electricity Postpaid ( PLN Reguler )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function electricityPostpaid($customerId, $customerNumber, $productId, $orderId);

    /**
     * Inquire Telkom Postpaid
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function telkomPostpaid($customerId, $customerNumber, $productId, $orderId);

    /**
     * Inquire PDAM
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $operatorCode
     * @param  string $orderId
     * @return mixed
     */
    public function pdam($customerId, $customerNumber, $productId, $operatorCode, $orderId);

    /**
     * Inquire Mobile Postpaid ( Pascabayar )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function mobilePostpaid($customerId, $customerNumber, $productId, $orderId);
}
