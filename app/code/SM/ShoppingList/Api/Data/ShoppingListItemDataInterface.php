<?php

namespace SM\ShoppingList\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Interface ShoppingListItemDataInterface
 * @package SM\ShoppingList\Api\Data
 */
interface ShoppingListItemDataInterface extends CustomAttributesDataInterface
{
    const SHOPPING_LIST_ITEM_ID = 'wishlist_item_id';
    const SHOPPING_LIST_ID = 'wishlist_id';
    const PRODUCT_ID = 'product_id';
    const STORE_ID = 'store_id';
    const ADDED_AT = 'added_at';

    /**
     * @return int
     */
    public function getWishlistItemId();

    /**
     * @return int
     */
    public function getWishlistId();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getAddedAt();

    /**
     * @param int $itemId
     * @return $this
     */
    public function setWishlistItemId($itemId);

    /**
     * @param int $shoppingListId
     * @return $this
     */
    public function setWishlistId($shoppingListId);

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @param int $storeId
     * @return $this
     */

    public function setStoreId($storeId);

    /**
     * @param string $addedAt
     * @return $this
     */
    public function setAddedAt($addedAt);
}
