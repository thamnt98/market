<?php

namespace SM\ShoppingList\Api\Data;

/**
 * Interface ShoppingListItemDataInterface
 * @package SM\ShoppingList\Api\Data
 */
interface ShoppingListItemDataInterface
{
    const SHOPPING_LIST_ITEM_ID = 'wishlist_item_id';
    const SHOPPING_LIST_ID = 'wishlist_id';
    const PRODUCT_ID = 'product_id';
    const STORE_ID = 'store_id';
    const ADDED_AT = 'added_at';
    const IMAGE = "image";
    const PRODUCT = "product";

    /**
     * @return int|null
     */
    public function getWishlistItemId();

    /**
     * @return int|null
     */
    public function getWishlistId();

    /**
     * @return int|null
     */
    public function getProductId();

    /**
     * @return int|null
     */
    public function getStoreId();

    /**
     * @return string|null
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

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface|null
     */
    public function getProduct();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface $value
     * @return $this
     */
    public function setProduct($value);

    /**
     * @return string|null
     */
    public function getImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setImage($value);
}
