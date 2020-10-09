<?php


namespace SM\MobileApi\Model\Data\GTM;


use Magento\Framework\DataObject;

class Basket extends DataObject implements \SM\MobileApi\Api\Data\GTM\BasketInterface
{

    public function getBasketId()
    {
        return $this->getData(self::BASKET_ID);
    }

    public function setBasketId($value)
    {
        return $this->setData(self::BASKET_ID, $value);
    }

    public function getBasketValue()
    {
        return $this->getData(self::BASKET_VALUE);
    }

    public function setBasketValue($value)
    {
        return $this->setData(self::BASKET_VALUE, $value);
    }

    public function getBasketQty()
    {
        return $this->getData(self::BASKET_QTY);
    }

    public function setBasketQty($value)
    {
        return $this->setData(self::BASKET_QTY, $value);

    }
    public function getCartCount()
    {
        return $this->getData(self::CART_COUNT);
    }

    public function setCartCount($value)
    {
        return $this->setData(self::CART_COUNT, $value);
    }
}
