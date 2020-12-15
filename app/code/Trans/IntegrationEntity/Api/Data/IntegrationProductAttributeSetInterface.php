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
interface IntegrationProductAttributeSetInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME   		= 'integration_product_attribute_set';
	const ID           		= 'id';
	const PIM_ID           	= 'pim_id';
	const NAME    	        = 'name';
	const CODE              = 'code'; 
	const ATTRIBUTE_SET_ID  = 'attribute_set_id'; 
	const ATTRIBUTE_SET_GROUP  = 'attribute_set_group'; 
	const DELETED   	    = 'deleted'; 
	const DELETED_ATTRIBUTE_LIST = 'deleted_attribute_list'; 
	const CREATED_AT 		= 'created_at';
	const UPDATED_AT 		= 'updated_at';
	const STATUS      		= 'status'; 

	const ATTRIBUTE_GROUP_CODE  = 'attribute_group_code'; 
	const ATTRIBUTE_GROUP_CODE_DATA  = 'product-details'; 
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
	 * Get Name
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
	 * Get attribute_set_id
	 *
	 * @return string
	 */
	public function getAttributeSetId();

	/**
	 * Set attribute_set_id
	 *
	 * @param string $attributeSetId
	 * @return void
	 */
	public function setAttributeSetId($attributeSetId);

	/**
	 * Get attribute_set_group
	 *
	 * @return string
	 */
	public function getAttributeSetGroup();

	/**
	 * Set attribute_set_group
	 *
	 * @param string $attributeSetGroup
	 * @return void
	 */
	public function setAttributeSetGroup($attributeSetGroup);

	/**
	 * Get deleted
	 *
	 * @return string
	 */
	public function getDeleted();

	/**
	 * Set deleted
	 *
	 * @param string $deleted
	 * @return void
	 */
	public function setDeleted($deleted);

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