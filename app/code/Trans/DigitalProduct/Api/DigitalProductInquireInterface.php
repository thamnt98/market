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
interface DigitalProductInquireInterface
{

    /**
     * Inquire BPJS Kesehatan
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  string $paymentPeriod
     * @param  int $productId
     * @return mixed
     */
    public function bpjsKesehatan($customerId, $customerNumber, $paymentPeriod, $productId);

    /**
     * Inquire Electricity ( PLN Reguler )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function electricity($customerId, $customerNumber, $productId);

    /**
     * Inquire Electricity Postpaid ( PLN Token )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function electricityPostpaid($customerId, $customerNumber, $productId);

    /**
     * Inquire Telkom Postpaid
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function telkomPostpaid($customerId, $customerNumber, $productId);

    /**
     * Inquire PDAM
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $operatorCode
     * @return mixed
     */
    public function pdam($customerId, $customerNumber, $productId, $operatorCode);

    /**
     * Inquire Mobile Postpaid ( Pascabayar )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function mobilePostpaid($customerId, $customerNumber, $productId);
}
