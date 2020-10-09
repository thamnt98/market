<?php

namespace SM\DigitalProduct\Model\Data\Inquire;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\Inquire\PdamBillDataInterface;

/**
 * Class PdamBillData
 * @package SM\DigitalProduct\Model\Data\Inquire
 */
class PdamBillData extends DataObject implements PdamBillDataInterface
{

    /**
     * @inheritDoc
     */
    public function getBillDate()
    {
        return $this->getData(self::BILL_DATE);
    }

    /**
     * @inheritDoc
     */
    public function getKubikasi()
    {
        return $this->getData(self::KUBIKASI);
    }

    /**
     * @inheritDoc
     */
    public function getPenalty()
    {
        return $this->getData(self::PENALTY);
    }

    /**
     * @inheritDoc
     */
    public function getBillAmount()
    {
        return $this->getData(self::BILL_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getWaterusageBill()
    {
        return $this->getData(self::WATERUSAGE_BILL);
    }

    /**
     * @inheritDoc
     */
    public function getTotalFee()
    {
        return $this->getData(self::TOTAL_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getDetailFee()
    {
        return $this->getData(self::DETAIL_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getLiftUsage()
    {
        return $this->getData(self::LIFT_USAGE);
    }

    /**
     * @inheritDoc
     */
    public function getTotalUsage()
    {
        return $this->getData(self::TOTAL_USAGE);
    }

    /**
     * @inheritDoc
     */
    public function getDetailUsage()
    {
        return $this->getData(self::DETAIL_USAGE);
    }

    /**
     * @inheritDoc
     */
    public function setBillDate($value)
    {
        return $this->setData(self::BILL_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setKubikasi($value)
    {
        return $this->setData(self::KUBIKASI, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPenalty($value)
    {
        return $this->setData(self::PENALTY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBillAmount($value)
    {
        return $this->setData(self::BILL_AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setWaterusageBill($value)
    {
        return $this->setData(self::WATERUSAGE_BILL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotalFee($value)
    {
        return $this->setData(self::TOTAL_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDetailFee($value)
    {
        return $this->setData(self::DETAIL_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setLiftUsage($value)
    {
        return $this->setData(self::LIFT_USAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotalUsage($value)
    {
        return $this->setData(self::TOTAL_USAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDetailUsage($value)
    {
        return $this->setData(self::DETAIL_USAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInfoText()
    {
        return $this->getData(self::INFO_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setInfoText($value)
    {
        return $this->setData(self::INFO_TEXT, $value);
    }
}
