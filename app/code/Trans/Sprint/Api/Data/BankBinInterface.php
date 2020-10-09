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
interface BankBinInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const DEFAULT_PREFIX	= 'trans_sprint';
	const DEFAULT_EVENT 	= 'trans_sprint_bank_bin';
	const TABLE_NAME		= 'sprint_bank_bin';
	const ID				= 'id';
	const BANK_ID			= 'bank_id';
	const TYPE_ID			= 'type_id';
	const BIN_CODE			= 'bin_code';
	const BIN_TYPE_DB		= 1;
	const BIN_TYPE_CC		= 2;
	const BIN_TYPE_DBCC		= 3; 
	
	const CREATED_AT        = 'created_at';
	const UPDATED_AT        = 'updated_at';
	

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
	 * @return int
	 */
	public function getBankId();

	/**
	 * @param int $bankId
	 * @return void
	 */
	public function setBankId($bankId);

	/**
	 * @return int
	 */
	public function getTypeId();

	/**
	 * @param int $typeId
	 * @return void
	 */
	public function setTypeId($typeId);

	/**
	 * @return string
	 */
	public function getBinCode();

	/**
	 * @param string $binCode
	 * @return void
	 */
	public function setBinCode($binCode);

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
