<?php


namespace SM\ShoppingList\Api\Data;

/**
 * Interface ShoppingListItemSearchResultsInterface
 * @package SM\ShoppingList\Api\Data
 */
interface ShoppingListItemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface[]
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
