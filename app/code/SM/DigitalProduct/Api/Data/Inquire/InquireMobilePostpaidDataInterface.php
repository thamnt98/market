<?php

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface InquireMobilePostpaidDataInterface
 * @package SM\DigitalProduct\Api\Data\Inquire
 */
interface InquireMobilePostpaidDataInterface extends ResponseDataInterface
{
    const REFERENCE_NO = "reference_no";
    const CUSTOMER_NO = "customer_no";
    const CUSTOMER_NAME = "customer_name";
    const BILL_COUNT = "bill_count";
    const BILL_PERIODE = "bill_periode";
    const BILL_AMOUNT = "bill_amount";
    const ADMIN_FEE = "admin_fee";
    const TOTAL_AMOUNT = "total_amount";

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
     * @return string
     */
    public function getBillCount();

    /**
     * @return string
     */
    public function getBillPeriode();

    /**
     * @return string
     */
    public function getBillAmount();

    /**
     * @return string
     */
    public function getAdminFee();

    /**
     * @return string
     */
    public function getTotalAmount();

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
     * @param string $value
     * @return $this
     */
    public function setBillCount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillPeriode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAdminFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTotalAmount($value);
}
