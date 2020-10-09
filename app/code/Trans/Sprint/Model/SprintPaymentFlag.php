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

namespace Trans\Sprint\Model;

use Trans\Sprint\Api\Data\SprintPaymentFlagInterface;
use Trans\Sprint\Model\ResourceModel\SprintPaymentFlag as SprintPaymentFlagResourceModel;

/**
 * Class SprintPaymentFlag
 *
 * @SuppressWarnings(PHPMD)
 */
class SprintPaymentFlag extends \Magento\Framework\Model\AbstractModel implements SprintPaymentFlagInterface {
	/**
	 * cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'trans_sprint_paymentflag';

	/**
	 * cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'trans_sprint_paymentflag';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'trans_sprint_paymentflag';

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(SprintPaymentFlagResourceModel::class);
	}

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];

		return $values;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->getData(SprintPaymentFlagInterface::ID);
	}

	/**
	 * @param int $sprintId
	 * @return void
	 */
	public function setId($sprintId) {
		return $this->setData(SprintPaymentFlagInterface::ID, $sprintId);
	}

	/**
	 * @return string
	 */
	public function getCurrency() {
		return $this->getData(SprintPaymentFlagInterface::CURRENCY);
	}

	/**
	 * @param string $currency
	 * @return void
	 */
	public function setCurrency($currency) {
		return $this->setData(SprintPaymentFlagInterface::CURRENCY, $currency);
	}

	/**
	 * @return string
	 */
	public function getTransactionNo() {
		return $this->getData(SprintPaymentFlagInterface::TRANSACTION_NO);
	}

	/**
	 * @param string $transNo
	 * @return void
	 */
	public function setTransactionNo($transNo) {
		return $this->setData(SprintPaymentFlagInterface::TRANSACTION_NO, $transNo);
	}

	/**
	 * @return string
	 */
	public function getTransactionAmount() {
		return $this->getData(SprintPaymentFlagInterface::TRANSACTION_AMOUNT);
	}

	/**
	 * @param string $amount
	 * @return void
	 */
	public function setTransactionAmount($amount) {
		return $this->setData(SprintPaymentFlagInterface::TRANSACTION_AMOUNT, $amount);
	}

	/**
	 * @return string
	 */
	public function getTransactionDate() {
		return $this->getData(SprintPaymentFlagInterface::TRANSACTION_DATE);
	}

	/**
	 * @param string $date
	 * @return void
	 */
	public function setTransactionDate($date) {
		return $this->setData(SprintPaymentFlagInterface::TRANSACTION_DATE, $date);
	}

	/**
	 * @return string
	 */
	public function getChannelType() {
		return $this->getData(SprintPaymentFlagInterface::CHANNEL_TYPE);
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function setChannelType($type) {
		return $this->setData(SprintPaymentFlagInterface::CHANNEL_TYPE, $type);
	}

	/**
	 * @return string
	 */
	public function getTransactionFeature() {
		return $this->getData(SprintPaymentFlagInterface::TRANSACTION_FEATURE);
	}

	/**
	 * @param string $feature
	 * @return void
	 */
	public function setTransactionFeature($feature) {
		return $this->setData(SprintPaymentFlagInterface::TRANSACTION_FEATURE, $feature);
	}

	/**
	 * @return string
	 */
	public function getTransactionStatus() {
		return $this->getData(SprintPaymentFlagInterface::TRANSACTION_STATUS);
	}

	/**
	 * @param string $status
	 * @return void
	 */
	public function setTransactionStatus($status) {
		return $this->setData(SprintPaymentFlagInterface::TRANSACTION_STATUS, $status);
	}

	/**
	 * @return string
	 */
	public function getTransactionMessage() {
		return $this->getData(SprintPaymentFlagInterface::TRANSACTION_MESSAGE);
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function setTransactionMessage($message) {
		return $this->setData(SprintPaymentFlagInterface::TRANSACTION_MESSAGE, $message);
	}

	/**
	 * @return string
	 */
	public function getCustomerAccount() {
		return $this->getData(SprintPaymentFlagInterface::CUSTOMER_ACCOUNT);
	}

	/**
	 * @param string $account
	 * @return void
	 */
	public function setCustomerAccount($account) {
		return $this->setData(SprintPaymentFlagInterface::CUSTOMER_ACCOUNT, $account);
	}

	/**
	 * @return string
	 */
	public function getCardToken() {
		return $this->getData(SprintPaymentFlagInterface::CARD_TOKEN);
	}

	/**
	 * @param string $token
	 * @return void
	 */
	public function setCardToken($token) {
		return $this->setData(SprintPaymentFlagInterface::CARD_TOKEN, $token);
	}

	/**
	 * @return string
	 */
	public function getCardTokenUse() {
		return $this->getData(SprintPaymentFlagInterface::CARD_TOKEN_USE);
	}

	/**
	 * @param string $string
	 * @return void
	 */
	public function setCardTokenUse($string) {
		return $this->setData(SprintPaymentFlagInterface::CARD_TOKEN_USE, $string);
	}
	/**
	 * @return string
	 */
	public function getCardNo() {
		return $this->getData(SprintPaymentFlagInterface::CARD_NO);
	}

	/**
	 * @param string $cardNo
	 * @return void
	 */
	public function setCardNo($cardNo) {
		return $this->setData(SprintPaymentFlagInterface::CARD_NO, $cardNo);
	}
	
	/**
	 * @return string
	 */
	public function getFlagType() {
		return $this->getData(SprintPaymentFlagInterface::FLAG_TYPE);
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function setFlagType($type) {
		return $this->setData(SprintPaymentFlagInterface::FLAG_TYPE, $type);
	}

	/**
	 * @return string
	 */
	public function getInsertId() {
		return $this->getData(SprintPaymentFlagInterface::INSERT_ID);
	}

	/**
	 * @param string $insertId
	 * @return void
	 */
	public function setInsertId($insertId) {
		return $this->setData(SprintPaymentFlagInterface::INSERT_ID, $insertId);
	}

	/**
	 * @return string
	 */
	public function getPaymentReffId() {
		return $this->getData(SprintPaymentFlagInterface::PAYMENT_REFF_ID);
	}

	/**
	 * @param string $reffId
	 * @return void
	 */
	public function setPaymentReffId($reffId) {
		return $this->setData(SprintPaymentFlagInterface::PAYMENT_REFF_ID, $reffId);
	}

	/**
	 * @return string
	 */
	public function getAuthCode() {
		return $this->getData(SprintPaymentFlagInterface::AUTH_CODE);
	}

	/**
	 * @param string $code
	 * @return void
	 */
	public function setAuthCode($code) {
		return $this->setData(SprintPaymentFlagInterface::AUTH_CODE, $code);
	}

	/**
	 * @return string
	 */
	public function getAdditionalData() {
		return $this->getData(SprintPaymentFlagInterface::ADDITIONAL_DATA);
	}

	/**
	 * @param string $additional
	 * @return void
	 */
	public function setAdditionalData($additional) {
		return $this->setData(SprintPaymentFlagInterface::ADDITIONAL_DATA, $additional);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(SprintPaymentFlagInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(SprintPaymentFlagInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(SprintPaymentFlagInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(SprintPaymentFlagInterface::UPDATED_AT, $updatedAt);
	}
}
