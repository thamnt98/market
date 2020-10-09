<?php

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface PdamDetailUsageDataInterface
 * @package SM\DigitalProduct\Api\Data\Inquire
 */
interface PdamDetailUsageDataInterface
{
    const USAGE1 = "usage1";
    const USAGE2 = "usage2";
    const USAGE3 = "usage3";
    const USAGE4 = "usage4";

    /**
     * @return string
     */
    public function getUsage1();

    /**
     * @return string
     */
    public function getUsage2();

    /**
     * @return string
     */
    public function getUsage3();

    /**
     * @return string
     */
    public function getUsage4();

    /**
     * @param string $value
     * @return $this
     */
    public function setUsage1($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setUsage2($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setUsage3($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setUsage4($value);
}
