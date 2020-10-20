<?php

namespace SM\Checkout\Model\Api\CheckoutWeb\ItemsValidMethod;

class Item extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterface
{
    const ITEM_ID = 'item_id';
    const VALID_METHOD = 'valid_method';

    /**
     * {@inheritdoc}
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setValidMethod($validMethod)
    {
        return $this->setData(self::VALID_METHOD, $validMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidMethod()
    {
        return $this->_get(self::VALID_METHOD);
    }
}
