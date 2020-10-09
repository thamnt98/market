<?php

namespace SM\ShoppingList\Api;

use SM\ShoppingList\Api\Data\ShoppingListDataInterface;

/**
 * Interface ShoppingListRepositoryInterface
 * @package SM\ShoppingList\Api
 */
interface ShoppingListRepositoryInterface
{

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return \SM\ShoppingList\Api\Data\ShoppingListSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $customerId);

    /**
     * @param int $shoppingListId
     * @return \SM\ShoppingList\Api\Data\ShoppingListDataInterface
     */
    public function getById($shoppingListId);

    /**
     * Delete shopping list by ID.
     * @param int $shoppingListId
     * @return bool true on success
     */
    public function delete($shoppingListId);

    /**
     * @param \SM\ShoppingList\Api\Data\ShoppingListDataInterface $shoppingList
     * @param int $customerId
     * @return \SM\ShoppingList\Api\Data\ShoppingListDataInterface
     */
    public function create(\SM\ShoppingList\Api\Data\ShoppingListDataInterface $shoppingList, $customerId);

    /**
     * @param int $shoppingListId
     * @param int $customerId
     * @return \SM\ShoppingList\Api\Data\ShoppingListDataInterface
     */
    public function share($shoppingListId, $customerId);

    /**
     * @param ShoppingListDataInterface $shoppingList
     * @param int $customerId
     * @return \SM\ShoppingList\Api\Data\ShoppingListDataInterface
     */
    public function update(\SM\ShoppingList\Api\Data\ShoppingListDataInterface $shoppingList, $customerId);

    /**
     * @return string
     */
    public function getDefaultShoppingListName();

    /**
     * @return int
     */
    public function getLimitShoppingListNumber();
}
