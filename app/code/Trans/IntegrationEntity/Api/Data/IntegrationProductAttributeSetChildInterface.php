<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   HaDi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * https://ctcorpdigital.com/
 */

namespace Trans\IntegrationEntity\Api\Data;

/**
 * @api
 */
interface IntegrationProductAttributeSetChildInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME   		= 'integration_product_attribute_set_child';
	const ID           		= 'id';
	const PIM_ID           	= 'pim_id';
	const CODE              = 'code';  
	const DELETED_ATTRIBUTE_LIST = 'deleted_attribute_list'; 
	const CREATED_AT 		= 'created_at';
	const UPDATED_AT 		= 'updated_at';
	const STATUS      		= 'status'; 

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Set id
	 *
	 * @param string $id
	 * @return void
	 */
	public function setId($id);

	/**
	 * Get pim id
	 *
	 * @return int
	 */
	public function getPimId();

	/**
	 * Set pim id
	 *
	 * @param string $pimId
	 * @return void
	 */
	public function setPimId($pimId);

	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode();

	/**
	 * Set code
	 *
	 * @param string $code
	 * @return void
	 */
	public function setCode($code);

	/**
	 * Get deleted_attribute_list
	 *
	 * @return string
	 */
	public function getDeletedAttributeList();

	/**
	 * Set deleted_attribute_list
	 *
	 * @param string $deletedAttributeList
	 * @return void
	 */
	public function setDeletedAttributeList($deletedAttributeList);

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