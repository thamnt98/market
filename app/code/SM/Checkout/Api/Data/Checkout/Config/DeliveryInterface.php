<?php

namespace SM\Checkout\Api\Data\Checkout\Config;

interface DeliveryInterface
{
    /**
     * @param int $date
     * @return $this
     */
    public function setFromDate($date);

    /**
     * @return int
     */
    public function getFromDate();

    /**
     * @param int $date
     * @return $this
     */
    public function setToDate($date);

    /**
     * @return int
     */
    public function getToDate();

    /**
     * @param int $time
     * @return $this
     */
    public function setStartTime($time);

    /**
     * @return int
     */
    public function getStartTime();

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
