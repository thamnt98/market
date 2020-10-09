<?php

namespace SM\Checkout\Api\Data\Checkout\StorePickUp;

interface SourceInterface
{
    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $distance
     * @return $this
     */
    public function setDistance($distance);

    /**
     * @return string
     */
    public function getDistance();
}
