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
 * interface SprintPaymentFlagInterface
 */
interface SprintPaymentFlagInterface extends ExtensibleDataInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME          = 'sprint_payment_flag';
	const ID                  = 'id';
	const CURRENCY            = 'currency';
	const TRANSACTION_NO      = 'transaction_no';
	const TRANSACTION_AMOUNT  = 'transaction_amount';
	const TRANSACTION_DATE    = 'transaction_date';
	const CHANNEL_TYPE        = 'channel_type';
	const TRANSACTION_FEATURE = 'transaction_feature';
	const TRANSACTION_STATUS  = 'transaction_status';
	const TRANSACTION_MESSAGE = 'transaction_message';
	const CUSTOMER_ACCOUNT    = 'customer_account';
	const CARD_TOKEN          = 'card_token';
	const CARD_TOKEN_USE      = 'card_token_use';
	const CARD_NO             = 'card_no';
	const FLAG_TYPE           = 'flag_type';
	const INSERT_ID           = 'insert_date';
	const PAYMENT_REFF_ID     = 'payment_reff_id';
	const AUTH_CODE           = 'auth_code';
	const ADDITIONAL_DATA     = 'additional_data';
	const CREATED_AT          = 'created_at';
	const UPDATED_AT          = 'updated_at';


	const BANK_BIN_TABLE_NAME	= 'sprint_bank_bin';
	const BANK_ID				= 'bank_id';
	const BIN_TYPE_ID			= 'type_id';
	const BIN_TYPE_DB			= 1; // Type Debit
	const BIN_TYPE_CC			= 2; // Type Credit
	const BIN_CODE				= 'bin_code';



	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @param int $sprintId
	 * @return void
	 */
	public function setId($sprintId);

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
	public function getTransactionNo();

	/**
	 * @param string $transNo
	 * @return void
	 */
	public function setTransactionNo($transNo);

	/**
	 * @return string
	 */
	public function getTransactionAmount();

	/**
	 * @param string $amount
	 * @return void
	 */
	public function setTransactionAmount($amount);

	/**
	 * @return string
	 */
	public function getTransactionDate();

	/**
	 * @param string $date
	 * @return void
	 */
	public function setTransactionDate($date);

	/**
	 * @return string
	 */
	public function getChannelType();

	/**
	 * @param string $type
	 * @return void
	 */
	public function setChannelType($type);

	/**
	 * @return string
	 */
	public function getTransactionFeature();

	/**
	 * @param string $feature
	 * @return void
	 */
	public function setTransactionFeature($feature);

	/**
	 * @return string
	 */
	public function getTransactionStatus();

	/**
	 * @param string $status
	 * @return void
	 */
	public function setTransactionStatus($status);

	/**
	 * @return string
	 */
	public function getTransactionMessage();

	/**
	 * @param string $message
	 * @return void
	 */
	public function setTransactionMessage($message);

	/**
	 * @return string
	 */
	public function getCustomerAccount();

	/**
	 * @param string $account
	 * @return void
	 */
	public function setCustomerAccount($account);

	/**
	 * @return string
	 */
	public function getCardToken();

	/**
	 * @param string $token
	 * @return void
	 */
	public function setCardToken($token);

	/**
	 * @return string
	 */
	public function getCardTokenUse();

	/**
	 * @param string $string
	 * @return void
	 */
	public function setCardTokenUse($string);

	/**
	 * @return string
	 */
	public function getCardNo();

	/**
	 * @param string $cardNo
	 * @return void
	 */
	public function setCardNo($cardNo);

	/**
	 * @return string
	 */
	public function getFlagType();

	/**
	 * @param string $type
	 * @return void
	 */
	public function setFlagType($type);

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
	public function getPaymentReffId();

	/**
	 * @param string $reffId
	 * @return void
	 */
	public function setPaymentReffId($reffId);

	/**
	 * @return string
	 */
	public function getAuthCode();

	/**
	 * @param string $code
	 * @return void
	 */
	public function setAuthCode($code);

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
}
