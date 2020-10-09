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

/**
 * Interface for city.
 * @api
 */
interface CityInterface
{
	/**
     * Constant data store table Regency
     */
    const ENTITY_ID         = 'entity_id';
    const REGION_ID         = 'region_id';
    const CITY_NAME         = 'city';
    
    /**
     * Get Entity Id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Get Region Id
     *
     * @return string
     */
    public function getRegionId();

    /**
     * Get City name
     *
     * @return string
     */
    public function getCityName();

    /**
     * Set Entity Id
     *
     * @return $entityId
     */
    public function setEntityId($entityId);

    /**
     * Set Region Id
     *
     * @return $regionId
     */
    public function setRegionId($regionId);

    /**
     * Set City name
     *
     * @return $cityName
     */
    public function setCityName($cityName);
}
