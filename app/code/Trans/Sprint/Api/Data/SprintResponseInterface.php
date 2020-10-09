<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * interface SprintResponseInterface
 */
interface SprintResponseInterface extends ExtensibleDataInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME       = 'sprint_response';
	const ID               = 'id';
	const STORE_ID         = 'store_id';
	const QUOTE_ID         = 'quote_id';
	const CHANNEL_ID       = 'channel_id';
	const TRANSACTION_NO   = 'transaction_no';
	const CURRENCY         = 'currency';
	const INSERT_STATUS    = 'insert_status';
	const INSERT_MESSAGE   = 'insert_message';
	const INSERT_ID        = 'insert_id';
	const REDIRECT_URL     = 'redirect_url';
	const REDIRECT_DATA    = 'redirect_data';
	const ADDITIONAL_DATA  = 'additional_data';
	const PAYMENT_METHOD   = 'payment_method';
	const CUSTOMER_ACCOUNT = 'customer_account';
	const FLAG             = 'flag';
	const INSERT_DATE      = 'insert_date';
	const EXPIRE_DATE      = 'expire_date';
	const CREATED_AT       = 'created_at';
	const UPDATED_AT       = 'updated_at';

	// response status
	const INVALID_CODE = '01';
	const DOUBLE_PAYMENT_CODE = '02';
	const CANCEL_BY_ADMIN_CODE = '05';
	const EXPIRED_CODE = '04';
	const SUCCESS_CODE = '00';
	const INVALID_CHANNELID_MESSAGE = 'Invalid channelId';
	const INVALID_TRANSACTIONNO_MESSAGE = 'Invalid transactionNo';
	const INVALID_TRANSACTIONAMOUNT_MESSAGE = 'Invalid Transaction Amount';
	const INVALID_INSERTID_MESSAGE = 'Invalid insertId';
	const INVALID_AUTHCODE_MESSAGE = 'Invalid AuthCode';
	const INVALID_CURRENCY_MESSAGE = 'Invalid Currency';
	const INVALID_TRANSACTIONSTATUS_MESSAGE = 'Invalid Transaction Status';
	const INVALID_CUSTOMERACCOUNT_MESSAGE = 'Invalid VA Number';
	const SUCCESS_TRANSACTION_MESSAGE = 'Success';
	const DOUBLE_PAYMENT_MESSAGE = 'Transaction has been paid';
	const CANCEL_BY_ADMIN_MESSAGE = 'Transaction has been canceled';
	const EXPIRED_MESSAGE = 'Transaction has been expired';

	//status order
	const IN_PROCESS_ORDER = 'in_process';
	const CANCELED_ORDER = 'canceled';
	
	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($sprintId);

	/**
	 * @return string
	 */
	public function getStoreId();

	/**
	 * @param string $storeId
	 * @return void
	 */
	public function setStoreId($storeId);

	/**
	 * @return string
	 */
	public function getQuoteId();

	/**
	 * @param string $quoteId
	 * @return void
	 */
	public function setQuoteId($quoteId);

	/**
	 * @return string
	 */
	public function getTransactionNo();

	/**
	 * @param string $transNo
	 * @return void
	 */
	public function setTransactionNo($transNo);

	/**
	 * @return string
	 */
	public function getCurrency();

	/**
	 * @param string $currency
	 * @return void
	 */
	public function setCurrency($currency);

	/**
	 * @return string
	 */
	public function getInsertStatus();

	/**
	 * @param string $insertStatus
	 * @return void
	 */
	public function setInsertStatus($insertStatus);

	/**
	 * @return string
	 */
	public function getInsertMessage();

	/**
	 * @param string $insertMessage
	 * @return void
	 */
	public function setInsertMessage($insertMessage);

	/**
	 * @return string
	 */
	public function getInsertId();

	/**
	 * @param string $insertId
	 * @return void
	 */
	public function setInsertId($insertId);

	/**
	 * @return string
	 */
	public function getRedirectUrl();

	/**
	 * @param string $redirectUrl
	 * @return void
	 */
	public function setRedirectUrl($redirectUrl);

	/**
	 * @return string
	 */
	public function getRedirectData();

	/**
	 * @param string $redirectData
	 * @return void
	 */
	public function setRedirectData($redirectData);

	/**
	 * @return string
	 */
	public function getAdditionalData();

	/**
	 * @param string $additional
	 * @return void
	 */
	public function setAdditionalData($additional);

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

	/**
	 * @return string
	 */
	public function getPaymentMethod();

	/**
	 * @param string $method
	 * @return void
	 */
	public function setPaymentMethod($method);

	/**
	 * @return string
	 */
	public function getFlag();

	/**
	 * @param string $flag
	 * @return void
	 */
	public function setFlag($flag);

	/**
	 * @return string
	 */
	public function getInsertDate();

	/**
	 * @param string $date
	 * @return void
	 */
	public function setInsertData($date);

	/**
	 * @return string
	 */
	public function getExpireDate();

	/**
	 * @param string $date
	 * @return void
	 */
	public function setExpireData($date);

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
	public function getCustomerAccount();

	/**
	 * @param string $customerAccount
	 * @return void
	 */
	public function setCustomerAccount($customerAccount);
}
