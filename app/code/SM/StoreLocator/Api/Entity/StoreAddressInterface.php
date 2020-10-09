<?php

namespace SM\StoreLocator\Api\Entity;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface StoreAddressInterface extends ExtensibleDataInterface
{
    const STREET = 'street';
    const ADDRESS_LINE_1 = 'address_line_1';
    const ADDRESS_LINE_2 = 'address_line_2';
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
    public function getAddressLine1();

    /**
     * @param string $value
     * @return $this
     */
    public function setAddressLine1($value);

    /**
     * @return string
     */
    public function getAddressLine2();

    /**
     * @param string $value
     * @return $this
     */
    public function setAddressLine2($value);

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
     * @param \SM\StoreLocator\Api\Entity\StoreAddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\SM\StoreLocator\Api\Entity\StoreAddressExtensionInterface $extensionAttributes);

    /**
     * @return \SM\StoreLocator\Api\Entity\StoreAddressExtensionInterface
     */
    public function getExtensionAttributes();
}
