<?php
namespace SM\MobileApi\Api;

interface CartInterface{

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $cartItem
     * @return boolean
     */
    public function addToCart($cartItem);

    /**
     * @param int $cartId
     * @param int $customerId
     * @return \SM\MobileApi\Api\Data\GTM\BasketInterface
     */
    public function getCartCount($cartId, $customerId);

    /**
     * @param int $cartId
     * @param int $id
     * @param int $customerId
     * @return boolean
     */
    public function removeCart($customerId,$cartId,$id);


    /**
     * Lists items that are assigned to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return \SM\MobileApi\Api\Data\CartItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getItems($cartId);
}
