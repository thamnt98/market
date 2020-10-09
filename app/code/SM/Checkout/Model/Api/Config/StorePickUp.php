<?php

namespace SM\Checkout\Model\Api\Config;

class StorePickUp extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterface
{
    const DATE_LIMIT = 'date_limit';
    const TODAY_START_TIME = 'today_start_time';
    const NEXT_START_TIME = 'next_start_time';
    const END_TIME = 'end_time';

    /**
     * {@inheritdoc}
     */
    public function setDateLimit($dateLimit)
    {
        return $this->setData(self::DATE_LIMIT, $dateLimit);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateLimit()
    {
        return $this->_get(self::DATE_LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTodayStartTime($time)
    {
        return $this->setData(self::TODAY_START_TIME, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function getTodayStartTime()
    {
        return $this->_get(self::TODAY_START_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setNextStartTime($time)
    {
        return $this->setData(self::NEXT_START_TIME, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStartTime()
    {
        return $this->_get(self::NEXT_START_TIME);
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
