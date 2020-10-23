<?php

namespace SM\Checkout\Model\Api\CheckoutWeb;

class EstimateShipping extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\EstimateShippingInterface
{
    const RELOAD = 'reload';
    const ITEMS_VALID_METHOD = 'items_valid_method';
    const ERROR = 'error';
    const IS_SPLIT_ORDER = 'is_split_order';
    const STOCK_MESSAGE = 'stock_message';
    const SHOW_EACH_ITEMS = 'show_each_items';

    /**
     * {@inheritdoc}
     */
    public function setReload($reload)
    {
        return $this->setData(self::RELOAD, $reload);
    }

    /**
     * {@inheritdoc}
     */
    public function getReload()
    {
        return $this->_get(self::RELOAD);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemsValidMethod($itemsValidMethod)
    {
        return $this->setData(self::ITEMS_VALID_METHOD, $itemsValidMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsValidMethod()
    {
        return $this->_get(self::ITEMS_VALID_METHOD);
    }

    /**
     * {@inheritdoc}
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->_get(self::ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSplitOrder($isSplitOrder)
    {
        return $this->setData(self::IS_SPLIT_ORDER, $isSplitOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSplitOrder()
    {
        return $this->_get(self::IS_SPLIT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setStockMessage($stockMessage)
    {
        return $this->setData(self::STOCK_MESSAGE, $stockMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function getStockMessage()
    {
        return $this->_get(self::STOCK_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setShowEachItems($showEachItems)
    {
        return $this->setData(self::SHOW_EACH_ITEMS, $showEachItems);
    }

    /**
     * {@inheritdoc}
     */
    public function getShowEachItems()
    {
        return $this->_get(self::SHOW_EACH_ITEMS);
    }
}
