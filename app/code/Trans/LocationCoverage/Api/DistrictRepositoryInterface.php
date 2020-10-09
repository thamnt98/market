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

use Trans\LocationCoverage\Api\Data\DistrictInterface;

/**
 * DistrictRepositoryInterface
 * @api
 */
interface DistrictRepositoryInterface
{
    /**
     * @param DistrictInterface $templates
     * @return mixed
     */
    public function save(DistrictInterface $templates);

    /**
     * @param $value
     * @return mixed
     */
    public function getById($value);

    /**
     * @param DistrictInterface $templates
     * @return mixed
     */
    public function delete(DistrictInterface $templates);

    /**
     * @param $value
     * @return mixed
     */
    public function deleteById($value);
}