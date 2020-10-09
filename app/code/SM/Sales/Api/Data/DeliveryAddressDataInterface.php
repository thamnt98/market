<?php


namespace SM\Sales\Api\Data;

/**
 * Interface DeliveryAddressDataInterface
 * @package SM\Sales\Api\Data
 */
interface DeliveryAddressDataInterface
{
    const FULL_NAME = "full_name";
    const ADDRESS = "address";
    const STREET = "street";
    const PROVINCE = "province";
    const CITY = "city";
    const COUNTRY = "country";
    const ADDRESS_NAME = 'address_name';
    const POSTCODE = "postcode";
    const TELEPHONE = "telephone";

    /**
     * @return string
     */
    public function getFullName();

    /**
     * @param string $value
     * @return $this
     */
    public function setFullName($value);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param string $value
     * @return $this
     */
    public function setAddress($value);

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
    public function getProvince();

    /**
     * @param string $value
     * @return $this
     */
    public function setProvince($value);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $value
     * @return $this
     */
    public function setCity($value);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $value
     * @return $this
     */
    public function setCountry($value);

    /**
     * @return string
     */
    public function getAddressName();

    /**
     * @param string $value
     * @return $this
     */
    public function setAddressName($value);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $value
     * @return $this
     */
    public function setPostcode($value);

    /**
     * @return string
     */
    public function getTelephone();

    /**
     * @param string $value
     * @return $this
     */
    public function setTelephone($value);
}
