<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Api;


interface IntegrationStoreRepositoryInterface
{


    /**
     * @param $methodTag
     * @return mixed
     */
    public function prepareMethod($methodTag);

    /**
     * @param $mdId
     * @return mixed
     */
    public function prepareData($mdId);

    /**
     * @param $data
     * @return mixed
     */
    public function saveData($data);
}