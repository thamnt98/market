<?php

namespace SM\Checkout\Model\Api\Estimate;

class PreviewOrder extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterface
{
    const TITLE = 'title';
    const SHIPPING_METHOD = 'shipping_method';
    const SHIPPING_METHOD_TITLE = 'shipping_method_title';
    const FREE_SHIPPING = 'free_shipping';
    const SHIPPING_FEE = 'shipping_fee';
    const SHIPPING_FEE_NOT_DISCOUNT = 'shipping_fee_not_discount';
    const ITEM_TOTAL = 'item_total';
    const ADDRESS_ID = 'address_id';
    const DATE = 'date';
    const TIME = 'time';
    const ITEMS = 'item';

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
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
    public function setShippingMethodTitle($methodTitle)
    {
        return $this->setData(self::SHIPPING_METHOD_TITLE, $methodTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethodTitle()
    {
        return $this->_get(self::SHIPPING_METHOD_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFreeShipping($freeShipping)
    {
        return $this->setData(self::FREE_SHIPPING, $freeShipping);
    }


    /**
     * {@inheritdoc}
     */
    public function getFreeShipping()
    {
        return $this->_get(self::FREE_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingFee($shippingFee)
    {
        return $this->setData(self::SHIPPING_FEE, $shippingFee);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingFee()
    {
        return $this->_get(self::SHIPPING_FEE);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingFeeNotDiscount($shippingFeeNotDiscount)
    {
        return $this->setData(self::SHIPPING_FEE_NOT_DISCOUNT, $shippingFeeNotDiscount);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingFeeNotDiscount()
    {
        return $this->_get(self::SHIPPING_FEE_NOT_DISCOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemTotal($itemTotal)
    {
        return $this->setData(self::ITEM_TOTAL, $itemTotal);
    }


    /**
     * {@inheritdoc}
     */
    public function getItemTotal()
    {
        return $this->_get(self::ITEM_TOTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddressId($addressId)
    {
        return $this->setData(self::ADDRESS_ID, $addressId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddressId()
    {
        return $this->_get(self::ADDRESS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->_get(self::DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTime($time)
    {
        return $this->setData(self::TIME, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        return $this->_get(self::TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }
}
