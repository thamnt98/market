<?php

namespace SM\Checkout\Api\Data\CheckoutWeb\AdditionalInfo;

interface StorePickUpInterface
{
    /**
     * @param string $store
     * @return $this
     */
    public function setStore($store);

    /**
     * @return string
     */
    public function getStore();

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
