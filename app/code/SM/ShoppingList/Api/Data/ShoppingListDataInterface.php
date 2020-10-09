<?php

namespace SM\ShoppingList\Api\Data;

/**
 * Interface ShoppingListDataInterface
 * @package SM\ShoppingList\Api\Data
 */
interface ShoppingListDataInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SHOPPING_LIST_ID = 'wishlist_id';
    const CUSTOMER_ID = 'customer_id';
    const NAME = 'name';
    const SHARING_CODE = 'sharing_code';
    const ITEMS = 'items';
    const IS_DEFAULT = "is_default";

    /**
     * @param int $value
     * @return $this
     */
    public function setIsDefault($value);

    /**
     * @return int
     */
    public function getIsDefault();

    /**
     * @return int
     */
    public function getWishlistId();

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getSharingCode();

    /**
     * @return \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface[]
     */
    public function getItems();

    /**
     * @param int $shoppingListId
     * @return $this
     */
    public function setWishlistId($shoppingListId);

    /**
     * $param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @param string $sharingCode
     * @return $this
     */
    public function setSharingCode($sharingCode);
    /**
     * @param \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface[] $items
     * @return $this
     */
    public function setItems($items);
}
