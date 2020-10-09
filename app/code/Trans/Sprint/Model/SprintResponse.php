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

use Trans\Sprint\Api\Data\SprintResponseInterface;
use Trans\Sprint\Model\ResourceModel\SprintResponse as SprintResponseResourceModel;

/**
 * Class SprintResponse
 *
 * @SuppressWarnings(PHPMD)
 */
class SprintResponse extends \Magento\Framework\Model\AbstractModel implements SprintResponseInterface {
	/**
	 * cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'trans_sprint_response';

	/**
	 * cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'trans_sprint_response';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'trans_sprint_response';

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(SprintResponseResourceModel::class);
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
		return $this->getData(SprintResponseInterface::ID);
	}

	/**
	 * @param int $sprintId
	 * @return void
	 */
	public function setId($sprintId) {
		return $this->setData(SprintResponseInterface::ID, $sprintId);
	}

	/**
	 * @return string
	 */
	public function getStoreId() {
		return $this->getData(SprintResponseInterface::STORE_ID);
	}

	/**
	 * @param string $storeId
	 * @return void
	 */
	public function setStoreId($storeId) {
		return $this->setData(SprintResponseInterface::STORE_ID, $storeId);
	}

	/**
	 * @return string
	 */
	public function getQuoteId() {
		return $this->getData(SprintResponseInterface::QUOTE_ID);
	}

	/**
	 * @param string $quoteId
	 * @return void
	 */
	public function setQuoteId($quoteId) {
		return $this->setData(SprintResponseInterface::QUOTE_ID, $quoteId);
	}

	/**
	 * @return string
	 */
	public function getTransactionNo() {
		return $this->getData(SprintResponseInterface::TRANSACTION_NO);
	}

	/**
	 * @param string $transNo
	 * @return void
	 */
	public function setTransactionNo($transNo) {
		return $this->setData(SprintResponseInterface::TRANSACTION_NO, $transNo);
	}

	/**
	 * @return string
	 */
	public function getCurrency() {
		return $this->getData(SprintResponseInterface::CURRENCY);
	}

	/**
	 * @param string $currency
	 * @return void
	 */
	public function setCurrency($currency) {
		return $this->setData(SprintResponseInterface::CURRENCY, $currency);
	}

	/**
	 * @return string
	 */
	public function getInsertStatus() {
		return $this->getData(SprintResponseInterface::INSERT_STATUS);
	}

	/**
	 * @param string $insertStatus
	 * @return void
	 */
	public function setInsertStatus($insertStatus) {
		return $this->setData(SprintResponseInterface::INSERT_STATUS, $insertStatus);
	}

	/**
	 * @return string
	 */
	public function getInsertMessage() {
		return $this->getData(SprintResponseInterface::INSERT_MESSAGE);
	}

	/**
	 * @param string $insertMessage
	 * @return void
	 */
	public function setInsertMessage($insertMessage) {
		return $this->setData(SprintResponseInterface::INSERT_MESSAGE, $insertMessage);
	}

	/**
	 * @return string
	 */
	public function getInsertId() {
		return $this->getData(SprintResponseInterface::INSERT_ID);
	}

	/**
	 * @param string $insertId
	 * @return void
	 */
	public function setInsertId($insertId) {
		return $this->setData(SprintResponseInterface::INSERT_ID, $insertId);
	}

	/**
	 * @return string
	 */
	public function getRedirectUrl() {
		return $this->getData(SprintResponseInterface::REDIRECT_URL);
	}

	/**
	 * @param string $redirectUrl
	 * @return void
	 */
	public function setRedirectUrl($redirectUrl) {
		return $this->setData(SprintResponseInterface::REDIRECT_URL, $redirectUrl);
	}

	/**
	 * @return string
	 */
	public function getRedirectData() {
		return $this->getData(SprintResponseInterface::REDIRECT_DATA);
	}

	/**
	 * @param string $redirectData
	 * @return void
	 */
	public function setRedirectData($redirectData) {
		return $this->setData(SprintResponseInterface::REDIRECT_DATA, $redirectData);
	}

	/**
	 * @return string
	 */
	public function getAdditionalData() {
		return $this->getData(SprintResponseInterface::ADDITIONAL_DATA);
	}

	/**
	 * @param string $additional
	 * @return void
	 */
	public function setAdditionalData($additional) {
		return $this->setData(SprintResponseInterface::ADDITIONAL_DATA, $additional);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(SprintResponseInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(SprintResponseInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(SprintResponseInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(SprintResponseInterface::UPDATED_AT, $updatedAt);
	}

	/**
	 * @return string
	 */
	public function getPaymentMethod() {
		return $this->getData(SprintResponseInterface::PAYMENT_METHOD);
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function setPaymentMethod($method) {
		return $this->setData(SprintResponseInterface::PAYMENT_METHOD, $method);
	}

	/**
	 * @return string
	 */
	public function getFlag() {
		return $this->getData(SprintResponseInterface::FLAG);
	}

	/**
	 * @param string $flag
	 * @return void
	 */
	public function setFlag($flag) {
		return $this->setData(SprintResponseInterface::FLAG, $flag);
	}

	/**
	 * @return string
	 */
	public function getInsertDate() {
		return $this->getData(SprintResponseInterface::INSERT_DATE);
	}

	/**
	 * @param string $date
	 * @return void
	 */
	public function setInsertData($date) {
		return $this->setData(SprintResponseInterface::INSERT_DATE, $date);
	}

	/**
	 * @return string
	 */
	public function getExpireDate() {
		return $this->getData(SprintResponseInterface::EXPIRE_DATE);
	}

	/**
	 * @param string $date
	 * @return void
	 */
	public function setExpireData($date) {
		return $this->setData(SprintResponseInterface::EXPIRE_DATE, $date);
	}

	/**
	 * @return string
	 */
	public function getChannelId() {
		return $this->getData(SprintResponseInterface::CHANNEL_ID);
	}

	/**
	 * @param string $channelId
	 * @return void
	 */
	public function setChannelId($channelId) {
		return $this->setData(SprintResponseInterface::CHANNEL_ID, $channelId);
	}

	/**
	 * @return string
	 */
	public function getCustomerAccount() {
		return $this->getData(SprintResponseInterface::CUSTOMER_ACCOUNT);
	}

	/**
	 * @param string $customerAccount
	 * @return void
	 */
	public function setCustomerAccount($customerAccount) {
		return $this->setData(SprintResponseInterface::CUSTOMER_ACCOUNT, $customerAccount);
	}
}
