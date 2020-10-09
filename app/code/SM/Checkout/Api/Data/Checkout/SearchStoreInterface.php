<?php

namespace SM\Checkout\Api\Data\Checkout;

interface SearchStoreInterface
{
    const STORE = 'store';
    const DISTANCE = 'distance';

    /**
     * @param \Magento\InventoryApi\Api\Data\SourceInterface $data
     * @return $this
     */
    public function setStore($data);

    /**
     * @return \Magento\InventoryApi\Api\Data\SourceInterface
     */
    public function getStore();

    /**
     * @param float $distance
     * @return $this
     */
    public function setDistance($distance);

    /**
     * @return float
     */
    public function getDistance();
}
