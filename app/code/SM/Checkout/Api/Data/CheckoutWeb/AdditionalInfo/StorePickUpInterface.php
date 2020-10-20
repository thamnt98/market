<?php

namespace SM\Checkout\Api\Data\CheckoutWeb\AdditionalInfo;

interface StorePickUpInterface
{
    /**
     * @param string $storeCode
     * @return $this
     */
    public function setStoreCode($storeCode);

    /**
     * @return string
     */
    public function getStoreCode();

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
