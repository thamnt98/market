<?php

namespace SM\StoreLocator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface StoreAddressInterface extends ExtensibleDataInterface
{
    const STREET_NUMBER = 'street_number';
    const BUILDING = 'building';
    const SOI = 'soi';
    const STREET = 'street';
    const DISTRICT = 'district';
    const DISTRICT_ID = 'district_id';
    const SUB_DISTRICT = 'sub_district';
    const SUB_DISTRICT_ID = 'sub_district_id';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const POST_CODE = 'post_code';
    const CONTACT_NUMBER = 'contact_number';
    const COUNTRY_CODE = 'country_code';
    const CITY = 'city';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';

    /**
     * @return string
     */
    public function getStreetNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setStreetNumber($value);

    /**
     * @return string
     */
    public function getBuilding();

    /**
     * @param string $value
     * @return $this
     */
    public function setBuilding($value);

    /**
     * @return string
     */
    public function getSoi();

    /**
     * @param string $value
     * @return $this
     */
    public function setSoi($value);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $value
     * @return $this
     */
    public function setStreet($value);

    /**
     * @return string
     */
    public function getDistrict();

    /**
     * @param string $value
     * @return $this
     */
    public function setDistrict($value);

    /**
     * @return int
     */
    public function getDistrictId();

    /**
     * @param int $value
     * @return $this
     */
    public function setDistrictId($value);

    /**
     * @return string
     */
    public function getSubDistrict();

    /**
     * @param string $value
     * @return $this
     */
    public function setSubDistrict($value);

    /**
     * @return int
     */
    public function getSubDistrictId();

    /**
     * @param int $value
     * @return $this
     */
    public function setSubDistrictId($value);

    /**
     * @return string
     */
    public function getRegion();

    /**
     * @param string $value
     * @return $this
     */
    public function setRegion($value);

    /**
     * @return int
     */
    public function getRegionId();

    /**
     * @param int $value
     * @return $this
     */
    public function setRegionId($value);

    /**
     * @return string
     */
    public function getPostCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setPostCode($value);

    /**
     * @return string
     */
    public function getContactNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setContactNumber($value);

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setCountryCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCity($value);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $value
     * @return $this
     */
    public function setLatitude($value);

    /**
     * @return string
     */
    public function getLatitude();

    /**
     * @param string $value
     * @return $this
     */
    public function setLongitude($value);

    /**
     * @return string
     */
    public function getLongitude();

    /**
     * @param \SM\StoreLocator\Api\Data\StoreAddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\SM\StoreLocator\Api\Data\StoreAddressExtensionInterface $extensionAttributes);

    /**
     * @return \SM\StoreLocator\Api\Data\StoreAddressExtensionInterface
     */
    public function getExtensionAttributes();
}
