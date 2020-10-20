<?php

namespace SM\Checkout\Api\Data\Checkout\Config;

interface StorePickUpInterface
{
    /**
     * @param int $dateLimit
     * @return $this
     */
    public function setDateLimit($dateLimit);

    /**
     * @return int
     */
    public function getDateLimit();

    /**
     * @param int $time
     * @return $this
     */
    public function setTodayStartTime($time);

    /**
     * @return int
     */
    public function getTodayStartTime();

    /**
     * @param int $time
     * @return $this
     */
    public function setNextStartTime($time);

    /**
     * @return int
     */
    public function getNextStartTime();

    /**
     * @param int $time
     * @return $this
     */
    public function setEndTime($time);

    /**
     * @return int
     */
    public function getEndTime();
}
