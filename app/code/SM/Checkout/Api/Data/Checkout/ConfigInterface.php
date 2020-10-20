<?php

namespace SM\Checkout\Api\Data\Checkout;

interface ConfigInterface
{
    /**
     * @param \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterface $data
     * @return $this
     */
    public function setStorePickUpDateTime($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterface
     */
    public function getStorePickUpDateTime();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterface $data
     * @return $this
     */
    public function setDeliveryDateTime($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterface
     */
    public function getDeliveryDateTime();
}
