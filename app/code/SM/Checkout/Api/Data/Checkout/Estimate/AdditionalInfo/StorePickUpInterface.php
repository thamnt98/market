<?php

namespace SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo;

interface StorePickUpInterface
{
    /**
     * @param \Magento\InventoryApi\Api\Data\SourceInterface $store
     * @return $this
     */
    public function setStore($store);

    /**
     * @return \Magento\InventoryApi\Api\Data\SourceInterface
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
