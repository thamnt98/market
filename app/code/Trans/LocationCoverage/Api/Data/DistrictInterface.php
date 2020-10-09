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
 * Interface for district.
 * @api
 */
interface DistrictInterface
{
    /**
     * Constant data store table Districts
     */
    const DISTRICT_ID         	= 'district_id';
    const ENTITY_ID         	= 'entity_id';
    const DISTRICT_NAME         = 'district';
    const DISTRICT_KEY         	= 'district_key';

     /**
     * Get District Id.
     *
     * @return int
     */
    public function getDistrictId();

    /**
     * Get Entity Id
     *
     * @return string
     */
    public function getEntityId();

    /**
     * Get District name
     *
     * @return string
     */
    public function getDistrictName();

    /**
     * Get District key
     *
     * @return string
     */
    public function getDistrictKey();

    /**
     * Set District Id
     *
     * @return $districtId
     */
    public function setDistrictId($districtId);

    /**
     * Set Entity Id
     *
     * @return $entityId
     */
    public function setEntityId($entityId);

    /**
     * Set District Name
     *
     * @return $districtName
     */
    public function setDistrictName($districtName);

    /**
     * Set District Key
     *
     * @return $districtKey
     */
    public function setDistrictKey($districtKey);
}
   