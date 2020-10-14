<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Customer\Api\Data;
 
/**
 * @api
 */
interface CustomerIntegrationValidationsInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MSG_ERROR_JSON_ARRAY = 'request json must be type array';
    const MSG_ERROR_REQUIRE_FIELD = '%1 field is required.';

    /**
     * Set api result message
     * 
     * @param string[] $message
     * @return void
     */
    public function fields($data,$field);


   
}