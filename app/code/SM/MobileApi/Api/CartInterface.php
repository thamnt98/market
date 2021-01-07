<?php
namespace SM\MobileApi\Api;

use Magento\Framework\Exception\CouldNotSaveException;

interface CartInterface
{

    /**
     * @param int $cartId
     * @param int $customerId
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $cartItem
     * @return boolean
     */
    public function addToCart($cartId, $customerId, $cartItem);

    /**
     * @return \SM\MobileApi\Api\Data\GTM\BasketInterface
     */
    public function getCartCount();

    /**
     * @param int $cartId
     * @param int $id
     * @return boolean
     */
    public function removeCart($cartId, $id);

    /**
     * Lists items that are assigned to a specified cart.
     *
     * @return \SM\MobileApi\Api\Data\CartItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getItems();

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
