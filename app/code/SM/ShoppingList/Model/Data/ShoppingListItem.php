<?php

namespace SM\ShoppingList\Model\Data;

use Magento\Framework\DataObject;
use SM\ShoppingList\Api\Data\ShoppingListItemDataExtensionInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;

class ShoppingListItem extends DataObject implements ShoppingListItemDataInterface
{
    use CustomAttributeAware;

    /**
     * @inheritdoc
     */
    public function getWishlistItemId()
    {
        return $this->getData(self::SHOPPING_LIST_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function getWishlistId()
    {
        return $this->getData(self::SHOPPING_LIST_ID);
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function getAddedAt()
    {
        return $this->getData(self::ADDED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setWishlistItemId($value)
    {
        return $this->setData(self::SHOPPING_LIST_ITEM_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setWishlistId($value)
    {
        return $this->setData(self::SHOPPING_LIST_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setAddedAt($value)
    {
        return $this->setData(self::ADDED_AT, $value);
    }
}
