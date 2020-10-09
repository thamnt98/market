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

namespace Trans\IntegrationEntity\Api\Data;

/**
 * @api
 */
interface IntegrationProductAttributeTypeInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME   		= 'integration_product_attribute_type';
	const ID           		= 'id';
	const PIM_TYPE_ID    	= 'pim_type_id'; // TYPE ID FROM PIM
	const PIM_TYPE_CODE     = 'pim_type_code'; // Batch id are unique id base on chunk jobs
	const PIM_TYPE_NAME     = 'pim_type_name'; // PIM TYPE NAME
	const BACKEND_CODE   	= 'backend_type'; // Backend Type Code for magento attr
	const FRONTEND_CODE    	= 'frontend_input'; //  Frontend Input Code for magento attr
	const IS_SWATCH			= 'is_swatch';
	const CREATED_AT 		= 'created_at';
	const UPDATED_AT 		= 'updated_at';
	const STATUS      		= 'status'; // Active / No

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
	 * Get PIM_TYPE_ID
	 *
	 * @return string
	 */
	public function getPimTypeId();

	/**
	 * Set PIM_TYPE_ID
	 *
	 * @param string $id
	 * @return void
	 */
	public function setPimTypeId($id);

	/**
	 * Get PIM_TYPE_CODE
	 *
	 * @return string
	 */
	public function getPimTypeCode();

	/**
	 * Set PIM_TYPE_CODE
	 *
	 * @param string $code
	 * @return void
	 */
	public function setPimTypeCode($code);

	/**
	 * Get PIM_TYPE_Name
	 *
	 * @return string
	 */
	public function getPimTypeName();

	/**
	 * Set PIM_TYPE_Name
	 *
	 * @param string $code
	 * @return void
	 */
	public function setPimTypeName($name);

	/**
	 * Get BACKEND_CODE
	 *
	 * @return string
	 */
	public function getBackendType();

	/**
	 * Set BACKEND_CODE
	 *
	 * @param string $code
	 * @return void
	 */
	public function setBackendType($code);

	/**
	 * Get FRONTEND_CODE
	 *
	 * @return string
	 */
	public function getFrontendInput();

	/**
	 * Set FRONTEND_CODE
	 *
	 * @param string $code
	 * @return void
	 */
	public function setFrontendInput($code);

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
	 * Get Swatch
	 *
	 * @return int
	 */
	public function isSwatch();

	/**
	 * Set Swatch
	 *
	 * @param int $swatch
	 * @return void
	 */
	public function setSwatch($swatch);

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