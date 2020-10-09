<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CTCORP DIGITAL. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api\Data;

/**
 * @api
 */
interface IntegrationOrderStatusInterface {

	/**
	 * Constant for table name
	 */
	const TABLE_NAME = 'integration_oms_status';

	/**
	 * Constant for field table
	 */
	const STATUS_ID          = 'status_id';
	const OMS_STATUS_NO      = 'status';
	const OMS_ACTION_NO      = 'action';
	const OMS_SUB_ACTION_NO  = 'sub_action';
	const FE_STATUS_NO       = 'fe_status_no';
	const FE_STATUS          = 'fe_status';
	const FE_SUB_STATUS_NO   = 'fe_sub_status_no';
	const FE_SUB_STATUS      = 'fe_sub_status';
	const OMS_PAYMENT_STATUS = 'oms_payment_status';
	const PG_STATUS_NO       = 'pg_status_no';

	/**
	 * Get Status Shipping Id
	 *
	 * @return int
	 */
	public function getStatusId();

	/**
	 * Set Status Shipping Id
	 *
	 * @param int $statusId
	 * @return int
	 */
	public function setStatusId($statusId);

	/**
	 * Get OMS Status Number
	 *
	 * @param int
	 * @return int
	 */
	public function getStatusOms();

	/**
	 * Set OMS Status Number
	 *
	 * @param int $omsStatusNo
	 * @return int
	 */
	public function setStatusOms($omsStatusNo);

	/**
	 * Get OMS Action Number
	 *
	 * @param int
	 * @return int
	 */
	public function getActionOms();

	/**
	 * Set OMS Action Number
	 *
	 * @param int $omsActionNo
	 * @return int
	 */
	public function setActionOms($omsActionNo);

	/**
	 * Get OMS Sub Action Number
	 *
	 * @param int
	 * @return int
	 */
	public function getSubActionOms();

	/**
	 * Set OMS Sub Action Number
	 *
	 * @param int $omsSubActionNo
	 * @return int
	 */
	public function setSubActionOms($omsSubActionNo);

	/**
	 * Get Frontend Status Number
	 *
	 * @param string
	 * @return mixed
	 */
	public function getFeStatusNo();

	/**
	 * Set Frontend Status Number
	 *
	 * @param string $feStatusNo
	 * @return mixed
	 */
	public function setFeStatusNo($feStatusNo);

	/**
	 * Get Frontend Status
	 *
	 * @param string
	 * @return mixed
	 */
	public function getFeStatus();

	/**
	 * Set Frontend Status
	 *
	 * @param string $feStatus
	 * @return mixed
	 */
	public function setFeStatus($feStatus);

	/**
	 * Get Frontend Sub Status Number
	 *
	 * @param string
	 * @return mixed
	 */
	public function getFeSubStatusNo();

	/**
	 * Set Frontend Sub Status Number
	 *
	 * @param string $feSubStatusNo
	 * @return mixed
	 */
	public function setFeSubStatusNo($feSubStatusNo);

	/**
	 * Get Frontend Sub Status
	 *
	 * @param string
	 * @return mixed
	 */
	public function getFeSubStatus();

	/**
	 * Set Frontend Sub Status
	 *
	 * @param string $feSubStatus
	 * @return mixed
	 */
	public function setFeSubStatus($feSubStatus);
	/**
	 * Get OMS Payment Status
	 *
	 * @param string
	 * @return mixed
	 */
	public function getOmsPaymentStatus();

	/**
	 * Set OMS Payment Status
	 *
	 * @param string $omsPaymentStatus
	 * @return mixed
	 */
	public function setOmsPaymentStatus($omsPaymentStatus);

	/**
	 * Get Payment Gateway Status Number
	 *
	 * @param string
	 * @return mixed
	 */
	public function getPgStatusNo();

	/**
	 * Set Payment Gateway Status Number
	 *
	 * @param string $pgStatusNo
	 * @return mixed
	 */
	public function setPgStatusNo($pgStatusNo);
}
