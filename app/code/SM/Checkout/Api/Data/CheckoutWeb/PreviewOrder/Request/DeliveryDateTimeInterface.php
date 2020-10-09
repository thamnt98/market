<?php

namespace SM\Checkout\Api\Data\CheckoutWeb\PreviewOrder\Request;

interface DeliveryDateTimeInterface
{
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
     * @param string $date
     * @return $this
     */
    public function setDate($date);

    /**
     * @return string
     */
    public function getDate();

    /**
     * @param string $time
     * @return $this
     */
    public function setTime($time);

    /**
     * @return string
     */
    public function getTime();
}
