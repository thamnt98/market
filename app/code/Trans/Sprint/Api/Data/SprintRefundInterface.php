<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Api\Data;

/**
 * interface SprintRefundInterface
 */
interface SprintRefundInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const DEFAULT_PREFIX = 'trans_sprint';
	const DEFAULT_EVENT  = 'trans_sprint_refund';
	const TABLE_NAME     = 'sprint_refund';

	const ID                     = 'id';
	const CHANNEL_ID             = 'channel_id';
	const TRANSACTION_NO         = 'transaction_no';
	const TRANSACTION_AMOUNT     = 'transaction_amount';
	const ACQUIRER_APPROVAL_CODE = 'acquirer_approval_code';
	const ACQUIRER_RESPONSE_CODE = 'acquirer_response_code';
	const TRANSACTION_STATUS     = 'transaction_status';
	const TRANSACTION_MESSAGE    = 'transaction_message';
	const TRANSACTION_TYPE       = 'transaction_type';
	const TRANSACTION_REFF_ID    = 'transaction_reff_id';
	const CREATED_AT             = 'created_at';
	const UPDATED_AT             = 'updated_at';

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
	public function getChannelId();

	/**
	 * @param string $channelId
	 * @return void
	 */
	public function setChannelId($channelId);

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
	public function getTransactionAmount();

	/**
	 * @param string $transactionAmount
	 * @return void
	 */
	public function setTransactionAmount($transactionAmount);

	/**
	 * @return string
	 */
	public function getAcquirerApprovalCode();

	/**
	 * @param string $acquirerApprovalCode
	 * @return void
	 */
	public function setAcquirerApprovalCode($acquirerApprovalCode);

	/**
	 * @return string
	 */
	public function getAquirerResponseCode();

	/**
	 * @param string $acquirerResponseCode
	 * @return void
	 */
	public function setAquirerResponseCode($acquirerResponseCode);

	/**
	 * @return string
	 */
	public function getTransactionStatus();

	/**
	 * @param string $transactionStatus
	 * @return void
	 */
	public function setTransactionStatus($transactionStatus);

	/**
	 * @return string
	 */
	public function getTransactionMessage();

	/**
	 * @param string $transactionMessage
	 * @return void
	 */
	public function setTransactionMessage($transactionMessage);

	/**
	 * @return string
	 */
	public function getTransactionType();

	/**
	 * @param string $transactionType
	 * @return void
	 */
	public function setTransactionType($transactionType);

	/**
	 * @return string
	 */
	public function getTransactionReffId();

	/**
	 * @param string $transactionReffId
	 * @return void
	 */
	public function setTransactionReffId($transactionReffId);

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
