<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalogStock\Api\Data;
 
/**
 * @api
 */
interface IntegrationCheckStockResponseInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MESSAGE = 'message';
    const DATA = 'datas';
    const CODE = 'code';
    const STATUS_ERROR_FIELD_REQUIRED = 2;
    const STATUS_ERROR_GENERAL = 400;
    const STATUS_SUCCESS = 200;

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
     * Get api result data
     * 
     * @return mixed
     */
    public function getDatas();

    /**
     * Set api result message
     * 
     * @param mixed $data
     * @return void
     */
    public function setDatas($data);

    /**
     * Get api result code
     * 
     * @return int[]
     */
    public function getCode();

    /**
     * Set api result status
     * 
     * @param int[] $status
     * @return void
     */
    public function setCode($code);
}