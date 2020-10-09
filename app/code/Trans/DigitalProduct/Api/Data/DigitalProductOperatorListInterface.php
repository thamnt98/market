<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Api\Data;

/**
 * @api
 */
interface DigitalProductOperatorListInterface {

	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 *
	 */

	const DEFAULT_EVENT = 'trans_digitalproduct_operatorlist';
	const TABLE_NAME    = 'digitalproduct_operator_list';

	/**
	 * Constant Field name
	 */
	const ID            = 'id';
	const BRAND_ID      = 'brand_id';
	const OPERATOR_NAME = 'operator_name';
	const SERVICE_NAME  = 'service_name';
	const PREFIX_NUMBER = 'prefix_number';
	const UPDATED_AT    = 'updated_at';
	const CREATED_AT    = 'created_at';

	/**
	 * get id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Get Brand Id
	 *
	 * @param int
	 */
	public function getBrandId();

	/**
	 * Set Brand Id
	 *
	 * @param int $entityId
	 * @return void
	 */
	public function setBrandId($brandId);

	/**
	 * Get Operator Name
	 *
	 * @param string
	 */
	public function getOperatorName();

	/**
	 * Set Operator Name
	 *
	 * @param string $entityId
	 * @return void
	 */
	public function setOperatorName($operatorName);

	/**
	 * Get Service Name
	 *
	 * @param string
	 */
	public function getServiceName();

	/**
	 * Set Service Name
	 *
	 * @param string $entityId
	 * @return void
	 */
	public function setServiceName($serviceName);

	/**
	 * Get Prefix Number
	 *
	 * @param string
	 */
	public function getPrefixNumber();

	/**
	 * Set Prefix Number
	 *
	 * @param string $entityId
	 * @return void
	 */
	public function setPrefixNumber($prefixNumber);

	/**
	 * @return string
	 */
	public function getCreatedAt();

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt);

	/**
	 * @return string
	 */
	public function getUpdatedAt();

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);
}