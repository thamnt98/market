<?php
/**
 * @category Trans
 * @package  trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Api\Data;

/**
 * interface MasterPaymentCodeinterface
 */
interface MasterPaymentMatrixAdjustmentInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const DEFAULT_PREFIX = 'master_payment';
	const DEFAULT_EVENT  = 'trans_master_payment_matrix_adjustment';
	const TABLE_NAME     = 'master_payment_matrix_adjustment';

	const ID             = 'id';
	const TRANSACTION_NO = 'transaction_no';
	const PAID_AMOUNT    = 'paid_amount';
	const REFUND_AMOUNT  = 'refund_amount';
	const STATUS         = 'status';
	const MESSAGE        = 'message';
	const CREATED_AT     = 'created_at';
	const UPDATED_AT     = 'updated_at';

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
	public function getTransactionNo();

	/**
	 * @param string $transactionNo
	 * @return void
	 */
	public function setTransactionNo($transactionNo);

	/**
	 * @return string
	 */
	public function getPaidAmount();

	/**
	 * @param string $paidAmount
	 * @return void
	 */
	public function setPaidAmount($paidAmount);

	/**
	 * @return string
	 */
	public function getRefundAmount();

	/**
	 * @param string $refundAmount
	 * @return void
	 */
	public function setRefundAmount($refundAmount);

	/**
	 * @return string
	 */
	public function getStatus();

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status);

	/**
	 * @return string
	 */
	public function getMessage();

	/**
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message);

	/**
	 * @return datetime
	 */
	public function getCreatedAt();

	/**
	 * @param datetime $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt);

	/**
	 * @return datetime
	 */
	public function getUpdatedAt();

	/**
	 * @param datetime $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);

}
