<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface EstimateItemInterface
{
    /**
     * @param string $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * @return string
     */
    public function getItemId();

    /**
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * @return int
     */
    public function getQty();

    /**
     * @param string $shippingAddressId
     * @return $this
     */
    public function setShippingAddressId($shippingAddressId);

    /**
     * @return string
     */
    public function getShippingAddressId();

    /**
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethodSelected($shippingMethod);

    /**
     * @return string
     */
    public function getShippingMethodSelected();
}
