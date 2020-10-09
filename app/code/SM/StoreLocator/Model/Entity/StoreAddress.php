<?php

namespace SM\StoreLocator\Model\Entity;

use SM\StoreLocator\Api\Entity\StoreAddressInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class StoreAddress
 * @package SM\StoreLocator\Model
 */
class StoreAddress extends AbstractExtensibleModel implements StoreAddressInterface
{
    /**
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->_getData(self::ADDRESS_LINE_1);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAddressLine1($value)
    {
        return $this->setData(self::ADDRESS_LINE_1, $value);
    }

    /**
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->_getData(self::ADDRESS_LINE_2);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAddressLine2($value)
    {
        return $this->setData(self::ADDRESS_LINE_2, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDistrict()
    {
        return $this->_getData(self::DISTRICT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDistrict($value)
    {
        return $this->setData(self::DISTRICT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDistrictId()
    {
        return $this->_getData(self::DISTRICT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setDistrictId($value)
    {
        return $this->setData(self::DISTRICT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubDistrict()
    {
        return $this->_getData(self::SUB_DISTRICT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubDistrict($value)
    {
        return $this->setData(self::SUB_DISTRICT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubDistrictId()
    {
        return $this->_getData(self::SUB_DISTRICT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubDistrictId($value)
    {
        return $this->setData(self::SUB_DISTRICT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegion()
    {
        return $this->_getData(self::REGION);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegion($value)
    {
        return $this->setData(self::REGION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegionId()
    {
        return $this->_getData(self::REGION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegionId($value)
    {
        return $this->setData(self::REGION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostCode()
    {
        return $this->_getData(self::POST_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostCode($value)
    {
        return $this->setData(self::POST_CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactNumber()
    {
        return $this->_getData(self::CONTACT_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setContactNumber($value)
    {
        return $this->setData(self::CONTACT_NUMBER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->_getData(self::COUNTRY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryCode($value)
    {
        return $this->setData(self::COUNTRY_CODE, $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCity($value)
    {
        return $this->setData(self::CITY, $value);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->_getData(self::CITY);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setLatitude($value)
    {
        return $this->setData(self::LATITUDE, $value);
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setLongitude($value)
    {
        return $this->setData(self::LONGITUDE, $value);
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * @param \SM\StoreLocator\Api\Entiy\StoreAddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \SM\StoreLocator\Api\Entity\StoreAddressExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @return \SM\StoreLocator\Api\Entity\StoreAddressExtensionInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }
}
