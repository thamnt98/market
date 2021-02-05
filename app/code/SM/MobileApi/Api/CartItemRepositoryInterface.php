<?php

namespace SM\MobileApi\Api;

interface CartItemRepositoryInterface
{
    /**
     * Add/update the specified cart item.
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem The item.
     * @return \SM\MobileApi\Api\Data\CartItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified item could not be saved to the cart.
     * @throws \Magento\Framework\Exception\InputException The specified item or cart is not valid.
     */
    public function addToCart(\Magento\Quote\Api\Data\CartItemInterface $cartItem);

    /**
     * Add/update the specified cart item.
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem The item.
     * @return \SM\MobileApi\Api\Data\CartItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified item could not be saved to the cart.
     * @throws \Magento\Framework\Exception\InputException The specified item or cart is not valid.
     */
    public function update(\Magento\Quote\Api\Data\CartItemInterface $cartItem);
}
