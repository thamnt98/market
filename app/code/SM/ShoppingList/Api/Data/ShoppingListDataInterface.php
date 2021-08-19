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
    const LEFT = "left";
    const IS_EXIST = "is_exist";

    /**
     * @param int $value
     * @return $this
     */
    public function setIsDefault($value);

    /**
     * @return int|null
     */
    public function getIsDefault();

    /**
     * @return int|null
     */
    public function getWishlistId();

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getSharingCode();

    /**
     * @return int|null
     */
    public function getLeft();

    /**
     * @return bool|null
     */
    public function getIsExist();

    /**
     * @return \SM\ShoppingList\Api\Data\ShoppingListItemDataInterface[]|null
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

    /**
     * @param int $value
     * @return $this
     */
    public function setLeft($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsExist($value);
}
