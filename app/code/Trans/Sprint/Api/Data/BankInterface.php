<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Api\Data;

/**
 * interface BankInterface
 */
interface BankInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const DEFAULT_PREFIX	= 'trans_sprint';
	const DEFAULT_EVENT 	= 'trans_sprint_bank';
	const TABLE_NAME		= 'sprint_bank';
	const ID				= 'id';
	const NAME				= 'name';
	const CODE				= 'code';
	const LABEL				= 'label';
	const BIN_LIST			= 'bin_list';

	const CREATED_AT         = 'created_at';
	const UPDATED_AT         = 'updated_at';
	

	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getCode();

	/**
	 * @param string $code
	 * @return void
	 */
	public function setCode($code);

	/**
	 * @return string
	 */
	public function getLabel();

	/**
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label);

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
