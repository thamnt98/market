<?php

namespace SM\Checkout\Api\Data\Checkout\Estimate;

interface PreviewOrderInterface
{
    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

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
     * @param string $methodTitle
     * @return $this
     */
    public function setShippingMethodTitle($methodTitle);

    /**
     * @return string
     */
    public function getShippingMethodTitle();

    /**
     * @param float $shippingFee
     * @return $this
     */
    public function setShippingFee($shippingFee);

    /**
     * @return float
     */
    public function getShippingFee();

    /**
     * @param float $shippingFeeNotDiscount
     * @return $this
     */
    public function setShippingFeeNotDiscount($shippingFeeNotDiscount);

    /**
     * @return float
     */
    public function getShippingFeeNotDiscount();

    /**
     * @param int $itemTotal
     * @return $this
     */
    public function setItemTotal($itemTotal);

    /**
     * @return int
     */
    public function getItemTotal();

    /**
     * @param int $addressId
     * @return $this
     */
    public function setAddressId($addressId);

    /**
     * @return string
     */
    public function getAddressId();

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

    /**
     * @param int[] $items
     * @return $this
     */
    public function setItems($items);

    /**
     * @return int[]
     */
    public function getItems();
}
