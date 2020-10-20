<?php

namespace SM\Checkout\Api\Data\CheckoutWeb\PreviewOrder\Response;

interface AddressInterface
{
    /**
     * @param string $address
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

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
