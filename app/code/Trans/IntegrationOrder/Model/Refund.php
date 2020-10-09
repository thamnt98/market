<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use \Trans\IntegrationOrder\Api\Data\RefundInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\Refund as ResourceModel;

class Refund extends \Magento\Framework\Model\AbstractModel implements
RefundInterface {
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getRefundId() {
		return $this->getData(RefundInterface::REFUND_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setRefundId($refundId) {
		return $this->setData(RefundInterface::REFUND_ID, $refundId);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderRefNumber() {
		return $this->getData(RefundInterface::ORDER_REF_NUMBER);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderRefNumber($orderRefNumber) {

		return $this->setData(RefundInterface::ORDER_REF_NUMBER, $orderRefNumber);
	}

	/**
	 * @inheritdoc
	 */
	public function getRefundTrxNumber() {
		return $this->getData(RefundInterface::REFUND_TRX_NUMBER);
	}

	/**
	 * @inheritdoc
	 */
	public function setRefundTrxNumber($refundTrxNumber) {

		return $this->setData(RefundInterface::REFUND_TRX_NUMBER, $refundTrxNumber);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderId() {
		return $this->getData(RefundInterface::ORDER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderId($orderId) {
		return $this->setData(RefundInterface::ORDER_ID, $orderId);
	}

	/**
	 * @inheritdoc
	 */
	public function getSku() {
		return $this->getData(RefundInterface::SKU);
	}

	/**
	 * @inheritdoc
	 */
	public function setSku($sku) {
		return $this->setData(RefundInterface::SKU, $sku);
	}

	/**
	 * @inheritdoc
	 */
	public function getRefundQty() {
		return $this->getData(RefundInterface::QTY_REFUND);
	}

	/**
	 * @inheritdoc
	 */
	public function setRefundQty($refundQty) {
		return $this->setData(RefundInterface::QTY_REFUND, $refundQty);
	}

	/**
	 * @inheritdoc
	 */
	public function getAmountRefundSku() {
		return $this->getData(RefundInterface::AMOUNT_REFUND_SKU);
	}

	/**
	 * @inheritdoc
	 */
	public function setAmountRefundSku($amountRefundSku) {
		return $this->setData(RefundInterface::AMOUNT_REFUND_SKU, $amountRefundSku);
	}

	/**
	 * @inheritdoc
	 */
	public function getAmountRefundOrder() {
		return $this->getData(RefundInterface::AMOUNT_REFUND_ORDER);
	}

	/**
	 * @inheritdoc
	 */
	public function setAmountRefundOrder($amountRefundOrder) {
		return $this->setData(RefundInterface::AMOUNT_REFUND_ORDER, $amountRefundOrder);
	}

	/**
	 * @inheritdoc
	 */
	public function getAmountTotalOrder() {
		return $this->getData(RefundInterface::AMOUNT_ORDER);
	}

	/**
	 * @inheritdoc
	 */
	public function setAmountTotalOrder($amountTotalOrder) {
		return $this->setData(RefundInterface::AMOUNT_ORDER, $amountTotalOrder);
	}

	/**
	 * @inheritdoc
	 */
	public function getAmountReferenceNumber() {
		return $this->getData(RefundInterface::AMOUNT_REF_NUMBER);
	}

	/**
	 * @inheritdoc
	 */
	public function setAmountReferenceNumber($amountReferenceNumber) {
		return $this->setData(RefundInterface::AMOUNT_REF_NUMBER, $amountReferenceNumber);
	}
}
