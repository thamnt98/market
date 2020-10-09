<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use \Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderReturn as ResourceModel;

class IntegrationOrderReturn extends \Magento\Framework\Model\AbstractModel implements
IntegrationOrderReturnInterface {
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->getData(IntegrationOrderReturnInterface::ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setId($id) {
		return $this->setData(IntegrationOrderReturnInterface::ID, $id);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderId() {
		return $this->getData(IntegrationOrderReturnInterface::ORDER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderId($orderId) {
		return $this->setData(IntegrationOrderReturnInterface::ORDER_ID, $orderId);
	}

	/**
	 * @inheritdoc
	 */
	public function getReferenceNumber() {
		return $this->getData(IntegrationOrderReturnInterface::REFERENCE_NUMBER);
	}

	/**
	 * @inheritdoc
	 */
	public function setReferenceNumber($referenceNumber) {
		return $this->setData(IntegrationOrderReturnInterface::REFERENCE_NUMBER, $referenceNumber);
	}

	/**
	 * @inheritdoc
	 */
	public function getSku() {
		return $this->getData(IntegrationOrderReturnInterface::SKU);
	}

	/**
	 * @inheritdoc
	 */
	public function setSku($sku) {
		return $this->setData(IntegrationOrderReturnInterface::SKU, $sku);
	}

	/**
	 * @inheritdoc
	 */
	public function getStore() {
		return $this->getData(IntegrationOrderReturnInterface::STORE);
	}

	/**
	 * @inheritdoc
	 */
	public function setStore($store) {
		return $this->setData(IntegrationOrderReturnInterface::STORE, $store);
	}

	/**
	 * @inheritdoc
	 */
	public function getQtyInitiated() {
		return $this->getData(IntegrationOrderReturnInterface::QTY_INITIATED);
	}

	/**
	 * @inheritdoc
	 */
	public function setQtyInitiated($qtyinitiated) {
		return $this->setData(IntegrationOrderReturnInterface::QTY_INITIATED, $qtyinitiated);
	}

	/**
	 * @inheritdoc
	 */
	public function getQtyInprogress() {
		return $this->getData(IntegrationOrderReturnInterface::QTY_INPROGRESS);
	}

	/**
	 * @inheritdoc
	 */
	public function setQtyInprogress($qtyinprogress) {
		return $this->setData(IntegrationOrderReturnInterface::QTY_INPROGRESS, $qtyinprogress);
	}

	/**
	 * @inheritdoc
	 */
	public function getQtyApproved() {
		return $this->getData(IntegrationOrderReturnInterface::QTY_APPROVED);
	}

	/**
	 * @inheritdoc
	 */
	public function setQtyApproved($qtyapproved) {
		return $this->setData(IntegrationOrderReturnInterface::QTY_APPROVED, $qtyapproved);
	}

	/**
	 * @inheritdoc
	 */
	public function getQtyRejected() {
		return $this->getData(IntegrationOrderReturnInterface::QTY_REJECTED);
	}

	/**
	 * @inheritdoc
	 */
	public function setQtyRejected($qtyrejected) {
		return $this->setData(IntegrationOrderReturnInterface::QTY_REJECTED, $qtyrejected);
	}

	/**
	 * @inheritdoc
	 */
	public function getReturnReason() {
		return $this->getData(IntegrationOrderReturnInterface::RETURN_REASON);
	}

	/**
	 * @inheritdoc
	 */
	public function setReturnReason($returnReason) {
		return $this->setData(IntegrationOrderReturnInterface::RETURN_REASON, $returnReason);
	}

	/**
	 * @inheritdoc
	 */
	public function getItemCondition() {
		return $this->getData(IntegrationOrderReturnInterface::ITEM_CONDITION);
	}

	/**
	 * @inheritdoc
	 */
	public function setItemCondition($itemCondition) {
		return $this->setData(IntegrationOrderReturnInterface::ITEM_CONDITION, $itemCondition);
	}

	/**
	 * @inheritdoc
	 */
	public function getResolution() {
		return $this->getData(IntegrationOrderReturnInterface::RESOLUTION);
	}

	/**
	 * @inheritdoc
	 */
	public function setResolution($resolution) {
		return $this->setData(IntegrationOrderReturnInterface::RESOLUTION, $resolution);
	}

	/**
	 * @inheritdoc
	 */
	public function getStatus() {
		return $this->getData(IntegrationOrderReturnInterface::STATUS);
	}

	/**
	 * @inheritdoc
	 */
	public function setStatus($status) {
		return $this->setData(IntegrationOrderReturnInterface::STATUS, $status);
	}

	/**
	 * @inheritdoc
	 */
	public function getCreatedAt() {
		return $this->getData(IntegrationOrderReturnInterface::CREATED_AT);
	}

	/**
	 * @inheritdoc
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(IntegrationOrderReturnInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @inheritdoc
	 */
	public function getUpdatedAt() {
		return $this->getData(IntegrationOrderReturnInterface::UPDATED_AT);
	}

	/**
	 * @inheritdoc
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(IntegrationOrderReturnInterface::UPDATED_AT, $updatedAt);
	}
}
