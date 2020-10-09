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

namespace Trans\Sprint\Model;

use Trans\Sprint\Api\Data\SprintRefundInterface;
use Trans\Sprint\Model\ResourceModel\SprintRefund as ResourceModel;

/**
 * Class SprintResponse
 *
 * @SuppressWarnings(PHPMD)
 */
class SprintRefund extends \Magento\Framework\Model\AbstractModel implements SprintRefundInterface {

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = SprintRefundInterface::DEFAULT_PREFIX;

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->getData(SprintRefundInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(SprintRefundInterface::ID, $id);
	}

	/**
	 * @return string
	 */
	public function getChannelId() {
		return $this->getData(SprintRefundInterface::CHANNEL_ID);
	}

	/**
	 * @param string $hannelId
	 * @return void
	 */
	public function setChannelId($hannelId) {
		return $this->setData(SprintRefundInterface::CHANNEL_ID, $hannelId);
	}

	/**
	 * @return string
	 */
	public function getTransactionNo() {
		return $this->getData(SprintRefundInterface::TRANSACTION_NO);
	}

	/**
	 * @param string $transactionNo
	 * @return void
	 */
	public function setTransactionNo($transactionNo) {
		return $this->setData(SprintRefundInterface::TRANSACTION_NO, $transactionNo);
	}

	/**
	 * @return decimal
	 */
	public function getTransactionAmount() {
		return $this->getData(SprintRefundInterface::TRANSACTION_AMOUNT);
	}

	/**
	 * @param decimal $transactionAmount
	 * @return void
	 */
	public function setTransactionAmount($transactionAmount) {
		return $this->setData(SprintRefundInterface::TRANSACTION_AMOUNT, $transactionAmount);
	}

	/**
	 * @return string
	 */
	public function getAcquirerApprovalCode() {
		return $this->getData(SprintRefundInterface::ACQUIRER_APPROVAL_CODE);
	}

	/**
	 * @param string $acquirerApprovalCode
	 * @return void
	 */
	public function setAcquirerApprovalCode($acquirerApprovalCode) {
		return $this->setData(SprintRefundInterface::ACQUIRER_APPROVAL_CODE, $acquirerApprovalCode);
	}

	/**
	 * @return string
	 */
	public function getAquirerResponseCode() {
		return $this->getData(SprintRefundInterface::AQUIRER_RESPONSE_CODE);
	}

	/**
	 * @param string $aquirerResponseCode
	 * @return void
	 */
	public function setAquirerResponseCode($aquirerResponseCode) {
		return $this->setData(SprintRefundInterface::AQUIRER_RESPONSE_CODE, $aquirerResponseCode);
	}

	/**
	 * @return string
	 */
	public function getTransactionStatus() {
		return $this->getData(SprintRefundInterface::TRANSACTION_STATUS);
	}

	/**
	 * @param string $transactionStatus
	 * @return void
	 */
	public function setTransactionStatus($transactionStatus) {
		return $this->setData(SprintRefundInterface::TRANSACTION_STATUS, $transactionStatus);
	}

	/**
	 * @return string
	 */
	public function getTransactionMessage() {
		return $this->getData(SprintRefundInterface::TRANSACTION_MESSAGE);
	}

	/**
	 * @param string $transactionMessage
	 * @return void
	 */
	public function setTransactionMessage($transactionMessage) {
		return $this->setData(SprintRefundInterface::TRANSACTION_MESSAGE, $transactionMessage);
	}

	/**
	 * @return string
	 */
	public function getTransactionType() {
		return $this->getData(SprintRefundInterface::TRANSACTION_TYPE);
	}

	/**
	 * @param string $transactionType
	 * @return void
	 */
	public function setTransactionType($transactionType) {
		return $this->setData(SprintRefundInterface::TRANSACTION_TYPE, $transactionType);
	}

	/**
	 * @return string
	 */
	public function getTransactionReffId() {
		return $this->getData(SprintRefundInterface::TRANSACTION_REFF_ID);
	}

	/**
	 * @param string $transactionReffId
	 * @return void
	 */
	public function setTransactionReffId($transactionReffId) {
		return $this->setData(SprintRefundInterface::TRANSACTION_REFF_ID, $transactionReffId);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(SprintRefundInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(SprintRefundInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(SprintRefundInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(SprintRefundInterface::UPDATED_AT, $updatedAt);
	}

}
