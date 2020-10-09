<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Api\Data;

/**
 * Interface CustomerInfoDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface CustomerInfoDataInterface
{
    const REFERENCE_NO = "reference_no";
    const CUSTOMER_NO = "customer_no";
    const CUSTOMER_NAME = "customer_name";
    const BILL_COUNT = "bill_count";
    const BILL_PERIODE = "bill_periode";
    const BILL_AMOUNT = "bill_amount";
    const ADMIN_FEE = "admin_fee";
    const TOTAL_AMOUNT = "total_amount";
    const STATUS = "status";
    const RESPONSE_CODE = "response_code";

    /**
     * @return string
     */
    public function getReferenceNo();

    /**
     * @return string
     */
    public function getCustomerNo();

    /**
     * @return string
     */
    public function getCustomerName();

    /**
     * @return int
     */
    public function getBillCount();

    /**
     * @return string
     */
    public function getBillPeriode();

    /**
     * @return float
     */
    public function getBillAmount();

    /**
     * @return float
     */
    public function getAdminFee();

    /**
     * @return float
     */
    public function getTotalAmount();

    /**
     * @return bool
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getResponseCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setReferenceNo($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomerNo($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomerName($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setBillCount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillPeriode($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setBillAmount($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setAdminFee($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setTotalAmount($value);

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
}
