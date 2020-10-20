<?php

namespace SM\Checkout\Model\Api\Estimate;

class Item extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\Estimate\ItemInterface
{
    const ITEM_ID = 'item_id';
    const SHIPPING_METHOD = 'shipping_method';
    const SHIPPING_ADDRESS = 'shipping_address';
    const ADDITIONAL_INFO = 'additional_info';

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
    public function setShippingMethod($method)
    {
        return $this->setData(self::SHIPPING_METHOD, $method);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethod()
    {
        return $this->_get(self::SHIPPING_METHOD);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress($addressId)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $addressId);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->_get(self::SHIPPING_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalInfo($additionalInfo)
    {
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInfo()
    {
        return $this->_get(self::ADDITIONAL_INFO);
    }
}
