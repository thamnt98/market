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

namespace Trans\LocationCoverage\Api;

interface AllLocationRepositoryInterface
{
    /**
     * @api
     * @param string $region
     * @return mixed[]
     */
    public function getRegionCode(string $region): array;

    /**
     * @api
     * @param string $city
     * @return mixed[]
     */
    public function getCityCode(string $city): array;

    /**
     * @api
     * @param string $district
     * @return mixed[]
     */
    public function getDistrictCode(string $district): array;

    /**
     * @api
     * @param string $districtId
     * @return string
     */
    public function districtName($districtId);

    /**
     * @api
     * @param string $cityId
     * @return string
     */
    public function cityName($cityId);
}
