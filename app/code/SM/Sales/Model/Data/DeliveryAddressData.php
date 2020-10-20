<?php

namespace SM\Sales\Model\Data;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\DeliveryAddressDataInterface;

/**
 * Class DeliveryAddressData
 * @package SM\Sales\Model\Data
 */
class DeliveryAddressData extends DataObject implements DeliveryAddressDataInterface
{

    /**
     * @inheritDoc
     */
    public function getFullName()
    {
        return $this->getData(self::FULL_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setFullName($value)
    {
        return $this->setData(self::FULL_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($value)
    {
        return $this->setData(self::STREET, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($value)
    {
        return $this->setData(self::CITY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @inheritDoc
     */
    public function setCountry($value)
    {
        return $this->setData(self::COUNTRY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAddressName()
    {
        return $this->getData(self::ADDRESS_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setAddressName($value)
    {
        return $this->setData(self::ADDRESS_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($value)
    {
        return $this->setData(self::POSTCODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($value)
    {
        return $this->setData(self::TELEPHONE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setAddress($value)
    {
        return $this->setData(self::ADDRESS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProvince()
    {
        return $this->getData(self::PROVINCE);
    }

    /**
     * @inheritDoc
     */
    public function setProvince($value)
    {
        return $this->setData(self::PROVINCE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDistrict()
    {
        return $this->getData(self::DISTRICT);
    }

    /**
     * @inheritDoc
     */
    public function setDistrict($value)
    {
        return $this->setData(self::DISTRICT, $value);
    }
}
