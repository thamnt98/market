<?php

namespace SM\Checkout\Model\Api\CheckoutWeb;

class EstimateItem extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\EstimateItemInterface
{
    const ITEM_ID = 'item_id';
    const QTY = 'qty';
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const SHIPPING_METHOD = 'shipping_method';

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
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddressId($shippingAddressId)
    {
        return $this->setData(self::SHIPPING_ADDRESS_ID, $shippingAddressId);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddressId()
    {
        return $this->_get(self::SHIPPING_ADDRESS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethodSelected($shippingMethod)
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethodSelected()
    {
        return $this->_get(self::SHIPPING_METHOD);
    }
}
