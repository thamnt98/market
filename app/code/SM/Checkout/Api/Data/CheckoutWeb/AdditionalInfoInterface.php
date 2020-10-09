<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface AdditionalInfoInterface
{
    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\AdditionalInfo\StorePickUpInterface $storePickUp
     * @return $this
     */
    public function setStorePickUp($storePickUp);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\AdditionalInfo\StorePickUpInterface
     */
    public function getStorePickUp();
}
