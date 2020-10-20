<?php

namespace SM\Checkout\Api;

interface CartUpdateRepositoryInterface
{
    /**
     * Select All Item
     *
     * @param int $cartId
     * @param int $check
     * @return bool
     */
    public function selectAll($cartId, $check);

    /**
     * Remove Items
     *
     * @param int $cartId
     * @param string $itemIds
     * @return bool
     */
    public function removeIds($cartId, $itemIds);

    /**
     * Update quote by id
     *
     * @param int $cartId
     * @param int $itemId
     * @param int $check
     * @return bool
     */
    public function selectItem($cartId, $itemId, $check);

    /**
     * Update quote by list of item
     * @param int $cartId
     * @param \SM\Checkout\Api\Data\CartItem\UpdateItemInterface[] $items
     * @return \Magento\Quote\Api\Data\TotalsInterface Quote totals data.
     */
    public function updateSelectedItem($cartId, $items);
}
