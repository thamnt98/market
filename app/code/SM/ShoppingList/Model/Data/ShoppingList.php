<?php

namespace SM\ShoppingList\Model\Data;

use Magento\Framework\DataObject;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;

class ShoppingList extends DataObject implements ShoppingListDataInterface
{

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
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function getSharingCode()
    {
        return $this->getData(self::SHARING_CODE);
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
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
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritdoc
     */
    public function setSharingCode($value)
    {
        return $this->setData(self::SHARING_CODE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setItems($value)
    {
        return $this->setData(self::ITEMS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setIsDefault($value)
    {
        return $this->setData(self::IS_DEFAULT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }
}
