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

namespace Trans\Integration\Api\Data;

/**
 * @api
 */
interface IntegrationChannelMethodInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'integration_channel_method';
	const ID = 'id';
	const CHANNEL_ID = 'ch_id';
	const TAG = 'tag';
	const DESCRIPTION = 'desc';
	const METHOD = 'method';
	const HEADERS = 'headers';
	const QUERY_PARAMS = 'query_params';
	const BODY = 'body';
	const PATH = 'path';
	const STATUS = 'status';
    const LIMIT = 'limit';
	const DATA_TYPE_JSON= 'LONGTEXT';

	/**
	 * Constant for Default Method
	 */
    const VAL_CH_ID = 1;
    const VAL_DESCRIPTION = 'Centralize API - Register Customer';
    const VAL_METHOD = 'POST';
    const VAL_HEADERS = '{}';
    const VAL_QUERY_PARAMS = '{}';
    const VAL_BODY = '{}';
    const VAL_PATH = '/ma/signup/customers/v1.0';
    const VAL_TAG = 'register-customers';
    const VAL_LIMIT = 100;

    /**
     * Addded Constant for PIM Method Category
     */
    const VAL_CH_ID_2 = 2;
	const VAL_DESCRIPTION_2 = 'PIM API';
	const VAL_METHOD_2 = 'GET';
	const VAL_HEADERS_2 = '{"dest":"tm.regulus"}';
	const VAL_QUERY_PARAMS_2 = '{}';
	const VAL_BODY_2 = '{}';
	const VAL_PATH_2 = '/category';
	const VAL_TAG_2 = 'category';
    const VAL_LIMIT_2 = 100;

    /**
     * Addded Constant for PIM Method Product
     */
    const VAL_CH_ID_3 = 2;
    const VAL_DESCRIPTION_3 = 'PIM API Product';
    const VAL_METHOD_3 = 'GET';
    const VAL_HEADERS_3 = '{"dest":"tm.regulus"}';
    const VAL_QUERY_PARAMS_3 = '{}';
    const VAL_BODY_3 = '{}';
    const VAL_PATH_3 = '/product';
    const VAL_TAG_3 = 'product';
    const VAL_LIMIT_3 = 100;

	/**
	 * Get id
	 * 
	 * @return int
	 */
	public function getId();

	/**
	 * Set id
	 * 
	 * @param string $idData
	 * @return void
	 */
	public function setId($idData);
	
	/**
	 * Get Channel Id
	 * 
	 * @return string
	 */
	public function getChId();

	/**
	 * Set Channel Id
	 * 
	 * @param string $chid
	 * @return void
	 */
	public function setChId($chid);

	/**
	 * Get Tags
	 * 
	 * @return string
	 */
	public function getTags();

	/**
	 * Set Tags
	 * 
	 * @param string $tags
	 * @return void
	 */
	public function setTags($tags);

	/**
	 * Get Description
	 * 
	 * @return string
	 */
	public function getDataDesc();

	/**
	 * Set Description
	 * 
	 * @param string $desc
	 * @return void
	 */
	public function setDataDesc($desc);

	/**
	 * Get Method
	 * 
	 * @return string
	 */
	public function getDataMethod();

	/**
	 * Set Method
	 * 
	 * @param string $method
	 * @return void
	 */
	public function setDataMethod($method);

	/**
	 * Get Headers
	 * 
	 * @return string
	 */
	public function getDataHeaders();

	/**
	 * Set Headers
	 * 
	 * @param string $headers
	 * @return void
	 */
	public function setDataHeaders($headers);

	/**
	 * Get Query Params
	 * 
	 * @return string
	 */
	public function getQueryParams();

	/**
	 * Set Query Params
	 * 
	 * @param string $queryParams
	 * @return void
	 */
	public function setQueryParams($queryParams);

	/**
	 * Get Body Params
	 * 
	 * @return string
	 */
	public function getDataBody();

	/**
	 * Set Body Params
	 * 
	 * @param string $body
	 * @return void
	 */
	public function setDataBody($body);

	/**
	 * Get Path
	 * 
	 * @return string
	 */
	public function getDataPath();

	/**
	 * Set Body Params
	 * 
	 * @param string $body
	 * @return void
	 */
	public function setDataPath($path);

    /**
     * Get Limit
     *
     * @return int
     */
    public function getLimits();

    /**
     * Set Limits
     *
     * @param int $body
     * @return void
     */
    public function setLimits($limit);

	/**
	 * Get Status
	 * 
	 * @return int
	 */
	public function getStatus();

	/**
	 * Set Status	
	 * 
	 * @param int $status
	 * @return void
	 */
	public function setStatus($status);

	/**
	 * Get created at
	 * 
	 * @return string
	 */
	public function getCreatedAt();

	/**
	 * Set created at
	 * 
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt);

	/**
	 * Get updated at
	 * 
	 * @return string
	 */
	public function getUpdatedAt();

	/**
	 * Set updated at
	 * 
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);

	/**
	 * Get created at
	 * 
	 * @return string
	 */
	public function getCreatedBy();

	/**
	 * Set created at
	 * 
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedBy($createdBy);

	/**
	 * Get updated at
	 * 
	 * @return string
	 */
	public function getUpdatedBy();

	/**
	 * Set updated at
	 * 
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedBy($updatedBy);

	
}