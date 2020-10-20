<?php

namespace SM\Checkout\Api\Data\Checkout\Estimate;

interface ItemInterface
{
    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param string $method
     * @return $this
     */
    public function setShippingMethod($method);

    /**
     * @return string
     */
    public function getShippingMethod();

    /**
     * @param int $addressId
     * @return $this
     */
    public function setShippingAddress($addressId);

    /**
     * @return string
     */
    public function getShippingAddress();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterface $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterface
     */
    public function getAdditionalInfo();
}
