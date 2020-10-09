<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model\Data\Inquire;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\Inquire\ElectricityPostPaidBillDataInterface;

/**
 * Class ElectricityPostPaidBillData
 * @package SM\DigitalProduct\Model\Data
 */
class ElectricityPostPaidBillData extends DataObject implements ElectricityPostPaidBillDataInterface
{

    /**
     * @inheritDoc
     */
    public function setProduk($value)
    {
        return $this->setData(self::PRODUK, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBillPeriod($value)
    {
        return $this->setData(self::BILL_PERIOD, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDueDate($value)
    {
        return $this->setData(self::DUE_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setMeterReadDate($value)
    {
        return $this->setData(self::METER_READ_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotalElectricityBill($value)
    {
        return $this->setData(self::TOTAL_ELECTRICITY_BILL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setIncentive($value)
    {
        return $this->setData(self::INCENTIVE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setValueAddedTax($value)
    {
        return $this->setData(self::VALUE_ADDED_TAX, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPenaltyFee($value)
    {
        return $this->setData(self::PENALTY_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPreviousMeterReading1($value)
    {
        return $this->setData(self::PREVIOUS_METER_READING1, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCurrentMeterReading1($value)
    {
        return $this->setData(self::CURRENT_METER_READING1, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPreviousMeterReading2($value)
    {
        return $this->setData(self::PREVIOUS_METER_READING2, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCurrentMeterReading2($value)
    {
        return $this->setData(self::CURRENT_METER_READING2, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPreviousMeterReading3($value)
    {
        return $this->setData(self::PREVIOUS_METER_READING3, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCurrentMeterReading3($value)
    {
        return $this->setData(self::CURRENT_METER_READING3, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProduk()
    {
        return $this->getData(self::PRODUK);
    }

    /**
     * @inheritDoc
     */
    public function getBillPeriod()
    {
        return $this->getData(self::BILL_PERIOD);
    }

    /**
     * @inheritDoc
     */
    public function getDueDate()
    {
        return $this->getData(self::DUE_DATE);
    }

    /**
     * @inheritDoc
     */
    public function getMeterReadDate()
    {
        return $this->getData(self::METER_READ_DATE);
    }

    /**
     * @inheritDoc
     */
    public function getTotalElectricityBill()
    {
        return $this->getData(self::TOTAL_ELECTRICITY_BILL);
    }

    /**
     * @inheritDoc
     */
    public function getIncentive()
    {
        return $this->getData(self::INCENTIVE);
    }

    /**
     * @inheritDoc
     */
    public function getValueAddedTax()
    {
        return $this->getData(self::VALUE_ADDED_TAX);
    }

    /**
     * @inheritDoc
     */
    public function getPenaltyFee()
    {
        return $this->getData(self::PENALTY_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getPreviousMeterReading1()
    {
        return $this->getData(self::PREVIOUS_METER_READING1);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentMeterReading1()
    {
        return $this->getData(self::CURRENT_METER_READING1);
    }

    /**
     * @inheritDoc
     */
    public function getPreviousMeterReading2()
    {
        return $this->getData(self::PREVIOUS_METER_READING2);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentMeterReading2()
    {
        return $this->getData(self::CURRENT_METER_READING2);
    }

    /**
     * @inheritDoc
     */
    public function getPreviousMeterReading3()
    {
        return $this->getData(self::PREVIOUS_METER_READING3);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentMeterReading3()
    {
        return $this->getData(self::CURRENT_METER_READING3);
    }
}
