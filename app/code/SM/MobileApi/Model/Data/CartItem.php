<?php

namespace SM\MobileApi\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\MobileApi\Api\Data\CartItemInterface;

class CartItem extends AbstractExtensibleObject implements CartItemInterface
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($data)
    {
        return $this->setData(self::ID, $data);
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * @inheritdoc
     */
    public function setItems($data)
    {
        return $this->setData(self::ITEMS, $data);
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return $this->_get(self::CART_MESSAGES);
    }

    /**
     * @inheritdoc
     */
    public function setMessages($message)
    {
        return $this->setData(self::CART_MESSAGES, $message);
    }

    /**
     * @inheritdoc
     */
    public function getBasketID()
    {
        return $this->_get(self::BASKET_ID);
    }

    /**
     * @inheritdoc
     */
    public function setBasketID($value)
    {
        return $this->setData(self::BASKET_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getBasketQty()
    {
        return $this->_get(self::BASKET_QTY);
    }

    /**
     * @inheritdoc
     */
    public function setBasketQty($value)
    {
        return $this->setData(self::BASKET_QTY, $value);
    }

    /**
     * @inheritdoc
     */
    public function getBasketValue()
    {
        return $this->_get(self::BASKET_VALUE);
    }
    /**
     * @inheritdoc
     */
    public function setBasketValue($value)
    {
        return $this->setData(self::BASKET_VALUE, $value);
    }
}
