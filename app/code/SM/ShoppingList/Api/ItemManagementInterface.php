<?php


namespace SM\ShoppingList\Api;

/**
 * Interface ItemManagementInterface
 * @package SM\ShoppingList\Api
 */
interface ItemManagementInterface
{
    /**
     * @param int $customerId
     * @param int $productId
     * @param int[]|null $shoppingListIds
     * @return \SM\ShoppingList\Api\Data\ResultDataInterface
     */
    public function addItem($customerId, $productId, $shoppingListIds = []);
}
