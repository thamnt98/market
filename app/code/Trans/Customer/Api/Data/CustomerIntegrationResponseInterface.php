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

use Trans\Customer\Api\Data\CustomerIntegrationDataInterface;
/**
 * @api
 */
interface CustomerIntegrationResponseInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MESSAGE = 'message';
    const DATA = 'datas';

//    const STATUS_ERROR_GENERAL = 400;
//    const STATUS_SUCCESS = 200;
    /**
     * Get api result message
     * 
     * @return string[]
     */
    public function getMessage();

    /**
     * Set api result message
     * 
     * @param string[] $message
     * @return void
     */
    public function setMessage($message);

    /**
     * @return \Trans\Customer\Api\Data\CustomerIntegrationDataInterface[]
     */
    public function getDatas();


    /**
     * @param \Trans\Customer\Api\Data\CustomerIntegrationDataInterface[]
     * @return void
     */
    public function setDatas($data);


   
}