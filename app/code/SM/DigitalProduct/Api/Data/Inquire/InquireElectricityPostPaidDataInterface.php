<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface InquireElectricityPostPaidDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface InquireElectricityPostPaidDataInterface extends InquireElectricityDataInterface
{
    const AMOUNT = "amount";
    const OUTSTANDING_BILL = "outstanding_bill";
    const BILL_STATUS = "bill_status";
    const BLTH_SUMMARY = "blth_summary";
    const D_METER_SUMMARY = "d_meter_summary";
    const BILLS = "bills";
    /**
     * @return string
     */
    public function getAmount();

    /**
     * @return string
     */
    public function getOutstandingBill();

    /**
     * @return string
     */
    public function getBillStatus();

    /**
     * @return string
     */
    public function getBlthSummary();

    /**
     * @return string
     */
    public function getDMeterSummary();

    /**
     * @return \SM\DigitalProduct\Api\Data\Inquire\ElectricityPostPaidBillDataInterface[]
     */
    public function getBills();

    /**
     * @param string $value
     * @return $this
     */
    public function setAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOutstandingBill($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillStatus($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBlthSummary($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDMeterSummary($value);

    /**
     * @param \SM\DigitalProduct\Api\Data\Inquire\ElectricityPostPaidBillDataInterface[] $value
     * @return $this
     */
    public function setBills($value);
}
