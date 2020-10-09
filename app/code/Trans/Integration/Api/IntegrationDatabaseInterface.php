<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Api;

interface IntegrationDatabaseInterface
{
    /**
     * Error Message
     */
    const MSG_REQUIRE_PARAM= 'Parameter are empty !';
    /**
     * @param $table
     * @param $query
     * @return mixed
     */
    public function getRow($table,$query);


}