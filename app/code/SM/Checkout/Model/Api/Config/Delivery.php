<?php

namespace SM\Checkout\Model\Api\Config;

class Delivery extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterface
{
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const START_TIME = 'start_time';
    const END_TIME = 'end_time';

    /**
     * {@inheritdoc}
     */
    public function setFromDate($date)
    {
        return $this->setData(self::FROM_DATE, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        return $this->_get(self::FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($date)
    {
        return $this->setData(self::TO_DATE, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        return $this->_get(self::TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartTime($time)
    {
        return $this->setData(self::START_TIME, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartTime()
    {
        return $this->_get(self::START_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setEndTime($time)
    {
        return $this->setData(self::END_TIME, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndTime()
    {
        return $this->_get(self::END_TIME);
    }
}
