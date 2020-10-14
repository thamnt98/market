<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api\Data;

/**
 * @api
 */
interface IntegrationDataValueInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME = 'integration_catalog_data';
	const ID         = 'id';
	const JOB_ID     = 'jb_id'; // job id
	const DATA_VALUE = 'data_value'; // Data format json
	const MESSAGE    = "message"; // message response
	const STATUS     = 'status'; // waiting , progress , close

	const STATUS_DATA_FAIL_UPDATE           = 5;
	const STATUS_DATA_FAIL_UPDATE_MAPPING   = 7;
	const STATUS_DATA_FAIL_UPDATE_CONFIGURE = 9;
	const STATUS_DATA_FAIL_DELETE           = 3;
	const STATUS_DATA_SUCCESS               = 50;

	const IS_DELETE_TRUE  = 1; // must be delete img
	const IS_DELETE_FALSE = 0;

	/**
	 * Constant for attribute
	 */

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
	 * Get Job Id
	 *
	 * @return string
	 */
	public function getJbId();

	/**
	 * Set Job Id
	 *
	 * @param string $jbid
	 * @return void
	 */
	public function setJbId($jbid);

	/**
	 * Get Message
	 *
	 * @return string
	 */
	public function getMessages();

	/**
	 * Set Message
	 *
	 * @param string $msg
	 * @return void
	 */
	public function setMessages($msg);

	/**
	 * Get Data Value (JSON)
	 *
	 * @return string
	 */
	public function getDataValue();

	/**
	 * Set  Data Value (JSON)
	 *
	 * @param string $dataValue
	 * @return void
	 */
	public function setDataValue($dataValue);

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

}