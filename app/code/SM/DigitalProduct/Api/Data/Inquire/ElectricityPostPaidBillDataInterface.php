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
 * Interface ElectricityPostPaidBillDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface ElectricityPostPaidBillDataInterface
{
    const PRODUK = "produk";
    const BILL_PERIOD = "bill_period";
    const DUE_DATE = "due_date";
    const METER_READ_DATE = "meter_read_date";
    const TOTAL_ELECTRICITY_BILL = "total_electricity_bill";
    const INCENTIVE = "incentive";
    const VALUE_ADDED_TAX = "value_added_tax";
    const PENALTY_FEE = "penalty_fee";
    const PREVIOUS_METER_READING1 = "previous_meter_reading1";
    const CURRENT_METER_READING1 = "current_meter_reading1";
    const PREVIOUS_METER_READING2 = "previous_meter_reading2";
    const CURRENT_METER_READING2 = "current_meter_reading2";
    const PREVIOUS_METER_READING3 = "previous_meter_reading3";
    const CURRENT_METER_READING3 = "current_meter_reading3";

    /**
     * @param string $value
     * @return $this
     */
    public function setProduk($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillPeriod($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDueDate($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMeterReadDate($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTotalElectricityBill($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setIncentive($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setValueAddedTax($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPenaltyFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPreviousMeterReading1($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrentMeterReading1($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPreviousMeterReading2($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrentMeterReading2($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPreviousMeterReading3($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrentMeterReading3($value);

    /**
     * @return string
     */
    public function getProduk();

    /**
     * @return string
     */
    public function getBillPeriod();

    /**
     * @return string
     */
    public function getDueDate();

    /**
     * @return string
     */
    public function getMeterReadDate();

    /**
     * @return string
     */
    public function getTotalElectricityBill();

    /**
     * @return string
     */
    public function getIncentive();

    /**
     * @return string
     */
    public function getValueAddedTax();

    /**
     * @return string
     */
    public function getPenaltyFee();

    /**
     * @return string
     */
    public function getPreviousMeterReading1();

    /**
     * @return string
     */
    public function getCurrentMeterReading1();

    /**
     * @return string
     */
    public function getPreviousMeterReading2();

    /**
     * @return string
     */
    public function getCurrentMeterReading2();

    /**
     * @return string
     */
    public function getPreviousMeterReading3();

    /**
     * @return string
     */
    public function getCurrentMeterReading3();
}
