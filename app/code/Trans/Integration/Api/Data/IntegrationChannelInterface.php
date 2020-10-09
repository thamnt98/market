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
interface IntegrationChannelInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const DEFAULT_EVENT = 'trans_integration';
	
    const TABLE_NAME = 'integration_channel';
    const ID = 'id';
    const NAME = 'name';
    const CODE = 'code';
    const URL = 'url';
    const ENV = 'env';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    
    /**
     * Channel Status
     */
    const CHANNEL_STATUS_ACTIVE = 1;
    const CHANNEL_STATUS_INACTIVE = 0;

    /**
     * Constant for Default Channel
     */
    const VAL_NAME = 'ccd';
    const VAL_URL = 'http://35.241.12.179/api/coreapi';
    const VAL_ENV = 'development';
    
    /**
     * Constant for General Data Value
     */
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const AUTHOR_BY_DEFAULT = 0;

    /**
     * Addded Constant for PIM Channel
     */
    const VAL_NAME_2 = 'pim';
    const VAL_URL_2 = 'http://34.67.195.219/v1';
	const VAL_ENV_2 = 'development';
	const CHANNEL_CODE_PIM = 'pim';
	
	
    
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
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string $name
     * @return void
     */
    public function setName($name);

    /**
     * Get Url
     *
     * @return string
     */
     public function getUrl();

    /**
	 * Set Url
	 * 
	 * @param string $url
	 * @return void
	 */
	public function setUrl($url);

	/**
	 * Get Environment
	 * 
	 * @return string
	 */
	public function getEnvironment();

	/**
	 * Set Env
	 * 
	 * @param string $env
	 * @return void
	 */
	public function setEnvironment($env);

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