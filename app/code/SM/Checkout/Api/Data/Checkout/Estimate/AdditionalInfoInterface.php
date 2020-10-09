<?php

namespace SM\Checkout\Api\Data\Checkout\Estimate;

interface AdditionalInfoInterface
{
    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterface $data
     * @return $this
     */
    public function setStorePickUp($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterface
     */
    public function getStorePickUp();
}
