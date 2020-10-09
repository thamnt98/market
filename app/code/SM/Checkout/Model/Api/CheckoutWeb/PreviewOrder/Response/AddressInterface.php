<?php

namespace SM\Checkout\Model\Api\CheckoutWeb\PreviewOrder\Request;

class DeliveryDateTime extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrder\Request\DeliveryDateTimeInterface
{
    const ADDRESS = 'address';
    const DATE = 'date';
    const TIME = 'time';

    /**
     * {@inheritdoc}
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->_get(self::ADDRESS);
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
