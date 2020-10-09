<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Model;

use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface;
use Trans\MasterPayment\Model\ResourceModel\MasterPaymentMatrixAdjustment as ResourceModel;

/**
 * Class MasterPayment
 *
 * @SuppressWarnings(PHPMD)
 */
class MasterPaymentMatrixAdjustment extends \Magento\Framework\Model\AbstractModel implements MasterPaymentMatrixAdjustmentInterface {

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = MasterPaymentMatrixAdjustmentInterface::DEFAULT_PREFIX;

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
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::ID, $id);
	}

	/**
	 * @return string
	 */
	public function getTransactionNo() {
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::TRANSACTION_NO);
	}

	/**
	 * @param string $transactionNo
	 * @return void
	 */
	public function setTransactionNo($transactionNo) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::TRANSACTION_NO, $transactionNo);
	}

	/**
	 * @return string
	 */
	public function getPaidAmount() {
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::PAID_AMOUNT);
	}

	/**
	 * @param string $paidAmount
	 * @return void
	 */
	public function setPaidAmount($paidAmount) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::PAID_AMOUNT, $paidAmount);
	}

	/**
	 * @return string
	 */
	public function getRefundAmount() {
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::REFUND_AMOUNT);
	}

	/**
	 * @param string $refundAmount
	 * @return void
	 */
	public function setRefundAmount($refundAmount) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::REFUND_AMOUNT, $refundAmount);
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::STATUS);
	}

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::STATUS, $status);
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::MESSAGE);
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::MESSAGE, $message);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(MasterPaymentMatrixAdjustmentInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(MasterPaymentMatrixAdjustmentInterface::UPDATED_AT, $updatedAt);
	}

}
