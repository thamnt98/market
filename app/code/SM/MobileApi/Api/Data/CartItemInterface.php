<?php

namespace SM\MobileApi\Api\Data;

interface CartItemInterface
{
    const ID     = 'id';
    const ITEMS   = 'items';
    const CART_MESSAGES = 'cart_messages';
    const BASKET_ID = 'basket_id';
    const BASKET_QTY = 'basket_qty';
    const BASKET_VALUE = 'basket_value';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $data
     * @return $this
     */
    public function setId($data);

    /**
     * @return \Magento\Quote\Api\Data\CartItemInterface[] Array of items.
     */
    public function getItems();

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $data
     * @return $this
     */
    public function setItems($data);

    /**
     * @return \SM\Checkout\Api\Data\CartMessage
     */
    public function getMessages();

    /**
     * @param \SM\Checkout\Api\Data\CartMessage $message
     * @return $this
     */
    public function setMessages($message);

    /**
     * @return int
     */
    public function getBasketID();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketID($value);

    /**
     * @return int
     */
    public function getBasketQty();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketQty($value);

    /**
     * @return string
     */
    public function getBasketValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setBasketValue($value);
}
