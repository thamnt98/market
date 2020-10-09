<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model\Data;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\CustomerInfoDataInterface;

/**
 * Class CustomerInfoData
 * @package SM\DigitalProduct\Model\Data
 */
class CustomerInfoData extends DataObject implements CustomerInfoDataInterface
{

    /**
     * @inheritDoc
     */
    public function getReferenceNo()
    {
        return $this->getData(self::REFERENCE_NO);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerNo()
    {
        return $this->getData(self::CUSTOMER_NO);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getBillCount()
    {
        return $this->getData(self::BILL_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function getBillPeriode()
    {
        return $this->getData(self::BILL_PERIODE);
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
    public function getAdminFee()
    {
        return $this->getData(self::ADMIN_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getTotalAmount()
    {
        return $this->getData(self::TOTAL_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getResponseCode()
    {
        return $this->getData(self::RESPONSE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setReferenceNo($value)
    {
        return $this->setData(self::REFERENCE_NO, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerNo($value)
    {
        return $this->setData(self::CUSTOMER_NO, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerName($value)
    {
        return $this->setData(self::CUSTOMER_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBillCount($value)
    {
        return $this->setData(self::BILL_COUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBillPeriode($value)
    {
        return $this->setData(self::BILL_PERIODE, $value);
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
    public function setAdminFee($value)
    {
        return $this->setData(self::ADMIN_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotalAmount($value)
    {
        return $this->setData(self::TOTAL_AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setResponseCode($value)
    {
        return $this->setData(self::RESPONSE_CODE, $value);
    }
}
