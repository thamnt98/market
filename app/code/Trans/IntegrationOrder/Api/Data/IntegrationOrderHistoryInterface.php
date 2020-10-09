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
interface IntegrationOrderHistoryInterface {

	/**
	 * Constant for table name
	 */
	const TABLE_NAME = 'integration_oms_order_history';

	/**
	 * Constant for field table
	 */
	const HISTORY_ID       = 'history_id';
	const REFERENCE_NUMBER = 'reference_number';
	const ORDER_ID         = 'order_id';
	const AWB_NUMBER       = 'awb_number';
	const LOGISTIC_COURIER = 'logistic_courier';
	const FE_STATUS_NO     = 'fe_status_no';
	const FE_SUB_STATUS_NO = 'fe_sub_status_no';
	const UPDATED_AT       = 'updated_at';

	/**
	 * Get Order History Id
	 *
	 * @return int
	 */
	public function getHistoryId();

	/**
	 * Set Order History Id
	 *
	 * @param int $historyId
	 * @return mixed
	 */
	public function setHistoryId($historyId);

	/**
	 * Get Reference Number
	 *
	 * @param string
	 */
	public function getReferenceNumber();

	/**
	 * Set Reference Number
	 *
	 * @param string $refNumber
	 * @return mixed
	 */
	public function setReferenceNumber($refNumber);

	/**
	 * Get Order Id
	 *
	 * @param string
	 */
	public function getOrderId();

	/**
	 * Set Order Id
	 *
	 * @param string $orderId
	 * @return mixed
	 */
	public function setOrderId($orderId);

	/**
	 * Get AWB Number
	 *
	 * @param string
	 */
	public function getAwbNumber();

	/**
	 * Set AWB Number
	 *
	 * @param string $awbNumber
	 * @return mixed
	 */
	public function setAwbNumber($awbNumber);

	/**
	 * Get Logistic Courier Number
	 *
	 * @param int
	 */
	public function getLogCourierNo();

	/**
	 * Set Logistic Courier Number
	 *
	 * @param int $logCourierNo
	 * @return mixed
	 */
	public function setLogCourierNo($logCourierNo);

	/**
	 * Get Frontend Status Number
	 *
	 * @param string
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
	 * Get Frontend Sub Status Number
	 *
	 * @param string
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
	 * Get Updated At
	 *
	 * @param datetime
	 */
	public function getUpdatedAt();

	/**
	 * Set Updated At
	 *
	 * @param datetime $updatedAt
	 * @return mixed
	 */
	public function setUpdatedAt($updatedAt);
}
