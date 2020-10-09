<?php
/**
 * Class SortOrderInterface
 * @package SM\StoreLocator\Api\Data\Request
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\StoreLocator\Api\Data\Request;

interface SortOrderInterface
{
    const LAT = 'lat';
    const LONG = 'long';

    /**
     * Get sorting field.
     *
     * @return string
     */
    public function getField();

    /**
     * Get sorting direction.
     *
     * @return string
     */
    public function getDirection();

    /**
     * @return float
     */
    public function getLat();

    /**
     * @return float
     */
    public function getLong();

    /**
     * @param string $field
     * @return  $this
     */
    public function setField($field);

    /**
     * @param string $field
     * @return $this
     */
    public function setDirection($field);

    /**
     * @param float $lat
     * @return mixed
     */
    public function setLat(float $lat);

    /**
     * @param float $long
     * @return mixed
     */
    public function setLong(float $long);
}
