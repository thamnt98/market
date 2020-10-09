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

use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPostPaidDataInterface;

/**
 * Class InquireElectricityPostPaidData
 * @package SM\DigitalProduct\Model\Data
 */
class InquireElectricityPostPaidData extends InquireElectricityData implements InquireElectricityPostPaidDataInterface
{
    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getOutstandingBill()
    {
        return $this->getData(self::OUTSTANDING_BILL);
    }

    /**
     * @inheritDoc
     */
    public function getBillStatus()
    {
        return $this->getData(self::BILL_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getBlthSummary()
    {
        return $this->getData(self::BLTH_SUMMARY);
    }

    /**
     * @inheritDoc
     */
    public function getDMeterSummary()
    {
        return $this->getData(self::D_METER_SUMMARY);
    }

    /**
     * @inheritDoc
     */
    public function getBills()
    {
        return $this->getData(self::BILLS);
    }

    /**
     * @inheritDoc
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOutstandingBill($value)
    {
        return $this->setData(self::OUTSTANDING_BILL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBillStatus($value)
    {
        return $this->setData(self::BILL_STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBlthSummary($value)
    {
        return $this->setData(self::BLTH_SUMMARY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDMeterSummary($value)
    {
        return $this->setData(self::D_METER_SUMMARY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBills($value)
    {
        return $this->setData(self::BILLS, $value);
    }
}
