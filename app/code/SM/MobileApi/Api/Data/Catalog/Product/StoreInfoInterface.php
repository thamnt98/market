<?php

namespace SM\MobileApi\Api\Data\Catalog\Product;

/**
 * Interface for store avaiable
 */
interface StoreInfoInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const NAME = 'name';
    const REGION = 'region';
    const CITY = 'city';
    const STREET = 'street';
    const POSTCODE = 'postcode';
    const OPEN_UNTIL = 'open_until';
    const PICK_UP_TIME = 'pick_up_time';
    const PICK_UP_DATE = "pick_up_date";

    /**
     * Get Store Name
     *
     * @return string
     */
    public function getName();

    /**
     * Set Store Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Store Region
     *
     * @return string
     */
    public function getRegion();

    /**
     * Set Store Region
     *
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region);

    /**
     * Get Store City
     *
     * @return string
     */
    public function getCity();

    /**
     * Set Store City
     *
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city);
    /**
     * Get Store Street
     *
     * @return string
     */
    public function getStreet();

    /**
     * Set IStore Street
     *
     * @param string $street
     *
     * @return $this
     */
    public function setStreet($street);
    /**
     * Get Postcode
     *
     * @return string
     */
    public function getPostcode();

    /**
     * Set Postcode
     *
     * @param string $postcode
     *
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * Get Open Time
     *
     * @return string
     */
    public function getOpenUntil();

    /**
     * Set  Open Time
     *
     * @param string $time
     *
     * @return $this
     */
    public function setOpenUntil($time);

    /**
     * Get Pick up time in my order
     * @return string
     */
    public function getPickUpTime();

    /**
     * Set Pick up time in my order
     * @param string $time
     * @return $this
     */
    public function setPickUpTime($time);

    /**
     * Get Pick Up Date for my order
     *
     * @return string
     */
    public function getPickUpDate();

    /**
     * Set Pick Up Date for my order
     *
     * @param string $value
     * @return $this
     */
    public function setPickUpDate($value);
}
