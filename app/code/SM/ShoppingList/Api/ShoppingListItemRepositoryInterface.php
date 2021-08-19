<?php


namespace SM\ShoppingList\Api;

/**
 * Interface ShoppingListItemRepositoryInterface
 * @package SM\ShoppingList\Api
 */
interface ShoppingListItemRepositoryInterface
{
    /**
     * @param int $itemId
     * @return bool
     */
    public function deleteById($itemId);

    /**
     * @param int $itemId
     * @param int[] $shoppingListIds
     * @return \SM\ShoppingList\Api\Data\ResultDataInterface
     */
    public function move($itemId, $shoppingListIds);

    /**
     * @param int[] $shoppingListIds
     * @param int $productId
     * @return \SM\ShoppingList\Api\Data\ResultDataInterface
     */
    public function add($shoppingListIds, $productId);
}
