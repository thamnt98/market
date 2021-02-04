<?php
namespace SM\MobileApi\Api;

use Magento\Framework\Exception\CouldNotSaveException;

interface CartInterface
{

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
    public function removeCart($customerId, $cartId, $id);

    /**
     * Lists items that are assigned to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return \SM\MobileApi\Api\Data\CartItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getItems($cartId);

    /**
     * This API using for homepage screen - mobile app
     * To prevent call api rest/V1/cart/getcartitems frequently in homepage screen that will make feature show error
     * messages won't work (APO-4614)
     *
     * @param int $customerId
     * @return int
     * @throws NoSuchEntityException The specified cart does not exist.
     * @throws CouldNotSaveException Can't create quote for customer
     */
    public function getCartIdForCustomer($customerId);

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function setQuote(\Magento\Quote\Api\Data\CartInterface $quote);

    /**
     * @return Magento\Quote\Api\Data\CartInterface
     */
    public function getQuote();
}
