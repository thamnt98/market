<?php

namespace SM\Checkout\Model\Api\Estimate\AdditionalInfo;

class StorePickUp extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterface
{
    const STORE = 'store';
    const DATE = 'date';
    const TIME = 'time';

    /**
     * {@inheritdoc}
     */
    public function setStore($store)
    {
        return $this->setData(self::STORE, $store);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->_get(self::STORE);
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
}
