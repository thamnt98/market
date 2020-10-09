<?php


namespace SM\MobileApi\Api\Data\GTM;


interface BasketInterface
{
    const BASKET_ID = 'basket_id';
    const BASKET_VALUE = 'basket_value';
    const BASKET_QTY = 'basket_qty';
    const CART_COUNT = 'cart_total';

    /**
     * @return $this
     */
    public function getBasketId();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketId($value);

    /**
     * @return $this
     */
    public function getBasketValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setBasketValue($value);

    /**
     * @return $this
     */
    public function getBasketQty();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketQty($value);

    /**
     * @return $this
     */
    public function getCartCount();

    /**
     * @param int $value
     * @return $this
     */
    public function setCartCount($value);

}
