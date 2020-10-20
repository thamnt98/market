<?php
/**
 * SM\FreshProductApi\Model\Data
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\FreshProductApi\Model\Data;

use Magento\Framework\DataObject;
use SM\FreshProductApi\Api\Data\FreshProductInterface;

/**
 * Class FreshProduct
 * @package SM\FreshProductApi\Model\Data
 */
class FreshProduct extends DataObject implements FreshProductInterface
{
    /**
     * @inheridoc
     */
    public function getOwnCourier()
    {
        return $this->getData(self::OWN_COURIER);
    }

    /**
     * @inheridoc
     */
    public function setOwnCourier($value)
    {
        return $this->setData(self::OWN_COURIER, $value);
    }

    /**
     * @inheridoc
     */
    public function getBasePriceInKg()
    {
        return $this->getData(self::BASE_PRICE_IN_KG);
    }

    /**
     * @inheridoc
     */
    public function setBasePriceInKg($value)
    {
        return $this->setData(self::BASE_PRICE_IN_KG, $value);
    }

    /**
     * @inheridoc
     */
    public function getPromoPriceInKg()
    {
        return $this->getData(self::PROMO_PRICE_IN_KG);
    }

    /**
     * @inheridoc
     */
    public function setPromoPriceInKg($value)
    {
        return $this->setData(self::PROMO_PRICE_IN_KG, $value);
    }

    /**
     * @inheridoc
     */
    public function getIsDecimal()
    {
        return $this->getData(self::IS_DECIMAL);
    }

    /**
     * @inheridoc
     * @param bool $value
     * @return FreshProduct
     */
    public function setIsDecimal($value)
    {
        return $this->setData(self::IS_DECIMAL, $value);
    }

    /**
     * @inheridoc
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * @inheridoc
     */
    public function setWeight($value)
    {
        return $this->setData(self::WEIGHT, $value);
    }

    /**
     * @inheridoc
     */
    public function getSoldIn()
    {
        return $this->getData(self::SOLD_IN);
    }

    /**
     * @inheridoc
     */
    public function setSoldIn($value)
    {
        return $this->setData(self::SOLD_IN, $value);
    }

    /**
     * @inheridoc
     */
    public function getPriceInKg()
    {
        return $this->getData(self::PRICE_IN_KG);
    }

    /**
     * @inheridoc
     */
    public function setPriceInKg($value)
    {
        return $this->setData(self::PRICE_IN_KG, $value);
    }
}
