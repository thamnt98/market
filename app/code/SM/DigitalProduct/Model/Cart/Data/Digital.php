<?php
/**
 * Class Digital
 * @package SM\DigitalProduct\Model\Cart\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Cart\Data;

use SM\DigitalProduct\Api\Data\DigitalInterface;

class Digital extends AbstractDataObject implements \SM\DigitalProduct\Api\Data\DigitalInterface
{
    /**
     * @inheritDoc
     */
    public function getServiceType()
    {
        return $this->_get(self::SERVICE_TYPE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setServiceType($value)
    {
        $this->setData(self::SERVICE_TYPE, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOperator()
    {
        return $this->_get(self::OPERATOR);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOperator($value)
    {
        $this->setData(self::OPERATOR, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMobileNumber()
    {
        return $this->_get(self::MOBILE_NUMBER);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMobileNumber($value)
    {
        $this->setData(self::MOBILE_NUMBER, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->_get(self::CUSTOMER_NAME);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setName($value)
    {
        $this->setData(self::CUSTOMER_NAME, $value);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getCustomerName()
    {
        return $this->_get(self::CUSTOMER_NAME);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCustomerName($value)
    {
        $this->setData(self::CUSTOMER_NAME, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($value)
    {
        $this->setData(self::CUSTOMER_ID, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdminFee()
    {
        return $this->_get(self::ADMIN_FEE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setAdminFee($value)
    {
        $this->setData(self::ADMIN_FEE, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPayUntil()
    {
        return $this->_get(self::PAY_UNTIL);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setPayUntil($value)
    {
        $this->setData(self::PAY_UNTIL, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMeterNumber()
    {
        return $this->_get(self::METER_NUMBER);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMeterNumber($value)
    {
        $this->setData(self::METER_NUMBER, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInfo()
    {
        return $this->_get(self::INFORMATION);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInfo($value)
    {
        $this->setData(self::INFORMATION, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPeriod()
    {
        return $this->_get(self::PERIOD);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setPeriod($value)
    {
        $this->setData(self::PERIOD, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaterialNumber()
    {
        return $this->_get(self::MATERIAL_NUMBER);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMaterialNumber($value)
    {
        $this->setData(self::MATERIAL_NUMBER, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setPrice($value)
    {
        $this->setData(self::PRICE, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBill()
    {
        return $this->_get(self::BILL);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setBill($value)
    {
        $this->setData(self::BILL, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        return $this->getData(self::POWER);
    }

    /**
     * @inheritDoc
     */
    public function setPower($value)
    {
        return $this->setData(self::POWER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setProductName($value)
    {
        return $this->setData(self::PRODUCT_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIncentiveAndTaxFee()
    {
        return $this->getData(self::INCENTIVE_AND_TAX_FEE);
    }

    /**
     * @inheritDoc
     */
    public function setIncentiveAndTaxFee($value)
    {
        return $this->setData(self::INCENTIVE_AND_TAX_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal()
    {
        return $this->getData(self::SUBTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setSubtotal($value)
    {
        return $this->setData(self::SUBTOTAL, $value);
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
    public function setPenalty($value)
    {
        return $this->setData(self::PENALTY, $value);
    }
}
