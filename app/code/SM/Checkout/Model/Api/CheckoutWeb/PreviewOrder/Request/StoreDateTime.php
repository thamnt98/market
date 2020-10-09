<?php

namespace SM\Checkout\Model\Api\CheckoutWeb\PreviewOrder\Request;

class StoreDateTime extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrder\Request\StoreDateTimeInterface
{
    const DATE = 'date';
    const TIME = 'time';

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
