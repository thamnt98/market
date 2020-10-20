<?php

namespace SM\FreshProductApi\Api\Data;

/**
 * Interface FreshProductDataInterface
 * @package SM\FreshProductApi\Api\Data
 */
interface FreshProductInterface
{
    const OWN_COURIER = "own_courier";
    const BASE_PRICE_IN_KG = "base_price_in_kg";
    const PROMO_PRICE_IN_KG = "promo_price_in_kg";
    const IS_DECIMAL = "is_decimal";
    const WEIGHT = "weight";
    const SOLD_IN = "sold_in";
    const PRICE_IN_KG = "price_in_kg";


    /**
     * @return bool
     */
    public function getOwnCourier();

    /**
     * @param bool $value
     * @return $this
     */
    public function setOwnCourier($value);

    /**
     * @return int
     */
    public function getBasePriceInKg();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasePriceInKg($value);

    /**
     * @return int
     */
    public function getPromoPriceInKg();

    /**
     * @param int $value
     * @return $this
     */
    public function setPromoPriceInKg($value);

    /**
     * @return bool
     */
    public function getIsDecimal();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsDecimal($value);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $value
     * @return $this
     */
    public function setWeight($value);

    /**
     * @return string
     */
    public function getSoldIn();

    /**
     * @param string $value
     * @return $this
     */
    public function setSoldIn($value);

    /**
     * @return bool
     */
    public function getPriceInKg();

    /**
     * @param bool $value
     * @return $this
     */
    public function setPriceInKg($value);
}
