<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Api\Data;

interface RegencyInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const CITY_ID = 'city_id';
    const REGION_ID = 'region_id';
    const CITY_NAME = 'city_name';

    /**
     * Get CityId.
     *
     * @return int
     */
    public function getCityId();

    /**
     * Set CityId.
     */
    public function setCityId($cityId);

    /**
     * Get RegionId.
     *
     * @return int
     */
    public function getRegionId();

    /**
     * Set RegionId.
     */
    public function setRegionId($regionId);

    /**
     * Get City Name.
     *
     * @return varchar
     */
    public function getCityName();

    /**
     * Set City Name.
     */
    public function setCityName($cityName);
}