<?php


namespace SM\ShoppingList\Api;

/**
 * Interface ShoppingListItemRepositoryInterface
 * @package SM\ShoppingList\Api
 */
interface ShoppingListItemRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return \SM\ShoppingList\Api\Data\ShoppingListItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $itemId
     * @return \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface
     */
    public function getById($itemId);

    /**
     * @param \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface $item
     * @return \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface
     */
    public function create(\SM\ShoppingList\Api\Data\ShoppingListItemDataInterface $item);

    /**
     * @param int $itemId
     * @return bool
     */
    public function deleteById($itemId);

    /**
     * @param \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface $item
     * @param int[] $shoppingListIds
     * @return \SM\ShoppingList\Api\Data\ResultDataInterface
     */
    public function move(\SM\ShoppingList\Api\Data\ShoppingListItemDataInterface $item, $shoppingListIds);

    /**
     * @param int[] $shoppingListIds
     * @param int $productId
     * @param int $storeId
     * @return \SM\ShoppingList\Api\Data\ResultDataInterface
     */
    public function add($shoppingListIds, $productId, $storeId);
}
