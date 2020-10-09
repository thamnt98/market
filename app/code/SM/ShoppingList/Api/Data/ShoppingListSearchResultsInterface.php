<?php

namespace SM\ShoppingList\Api\Data;

/**
 * Interface ShoppingListSearchResultsInterface
 * @package SM\ShoppingList\Api\Data
 */
interface ShoppingListSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get shopping list.
     *
     * @return \SM\ShoppingList\Api\Data\ShoppingListDataInterface[]
     */
    public function getItems();

    /**
     * Set shopping list.
     *
     * @param \SM\ShoppingList\Api\Data\ShoppingListDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
