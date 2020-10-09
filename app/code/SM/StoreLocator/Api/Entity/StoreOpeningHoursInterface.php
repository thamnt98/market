<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Api\Entity;

/**
 * Interface StoreOpeningHoursInterface
 * @package SM\StoreLocator\Api\Entity
 */
interface StoreOpeningHoursInterface
{
    const DAY = 'day';
    const OPEN = 'open';
    const CLOSE = 'close';
    const START = 'start';
    const END = 'end';

    /**
     * @return string
     */
    public function getDay(): string;

    /**
     * @return string
     */
    public function getOpen(): string;

    /**
     * @return string
     */
    public function getClose(): string;

    /**
     * @param string $day
     * @return self
     */
    public function setDay(string $day): self;

    /**
     * @param string $open
     * @return self
     */
    public function setOpen(string $open): self;

    /**
     * @param string $close
     * @return self
     */
    public function setClose(string $close): self;
}
