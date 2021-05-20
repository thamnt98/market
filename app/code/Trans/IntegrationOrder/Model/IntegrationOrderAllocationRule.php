<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT CORP DIGITAL. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule as ResourceModel;

class IntegrationOrderAllocationRule extends \Magento\Framework\Model\AbstractModel implements
IntegrationOrderAllocationRuleInterface {
	protected $isWarehouse;
	protected $items;

	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getOarIds() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::OAR_IDS);
	}

	/**
	 * @inheritdoc
	 */
	public function setOarIds($oarIds) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::OAR_IDS, $oarIds);
	}

	/**
	 * @inheritdoc
	 */
	public function getQuoteId() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::QUOTE_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setQuoteId($quoteId) {

		return $this->setData(IntegrationOrderAllocationRuleInterface::QUOTE_ID, $quoteId);
	}

	/**
	 * @inheritdoc
	 */
	public function getAddressId() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::ADDRESS_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setAddressId($addressId) {

		return $this->setData(IntegrationOrderAllocationRuleInterface::ADDRESS_ID, $addressId);
	}

	/**
	 * @inheritdoc
	 */
	public function getCustomerId() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::CUSTOMER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setCustomerId($customerId) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::CUSTOMER_ID, $customerId);
	}

	/**
	 * @inheritdoc
	 */
	public function getOarOrderId() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::OAR_ORDER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOarOrderId($oarOrderId) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::OAR_ORDER_ID, $oarOrderId);
	}

	/**
	 * @inheritdoc
	 */
	public function getOarCustomerId() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::OAR_CUSTOMER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOarCustomerId($oarCustomerId) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::OAR_CUSTOMER_ID, $oarCustomerId);
	}

	/**
	 * @inheritdoc
	 */
	public function getStoreCode() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::STORE_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setStoreCode($storeCode) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::STORE_CODE, $storeCode);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderId() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::ORDER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderId($orderId) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::ORDER_ID, $orderId);
	}

	/**
	 * @inheritdoc
	 */
	public function getReferenceNumber() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::REFERENCE_NUMBER);
	}

	/**
	 * @inheritdoc
	 */
	public function setReferenceNumber($referenceNumber) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::REFERENCE_NUMBER, $referenceNumber);
	}

	/**
	 * Get addresses.
	 *
	 * @api
	 * @return string
	 */
	public function getAddresses() {
		$this->address;
	}

	/**
	 * Set addresses.
	 *
	 * @api
	 * @return string
	 */
	public function setAddresses($addresses) {
		$this->address = $addresses;
	}

	/**
	 * Get items.
	 *
	 * @api
	 * @return mixed
	 */
	public function getItems() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::ITEMS);
	}

	/**
	 * Set items.
	 *
	 * @api
	 * @return mixed
	 */
	public function setItems($items) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::ITEMS, $items);
	}

	/**
	 * Get is spo.
	 *
	 * @api
	 * @return bool
	 */
	public function getIsSpo() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::IS_SPO);
	}

	/**
	 * Set is spo.
	 *
	 * @api
	 * @param  $isSpo
	 * @return bool
	 */
	public function setIsSpo($isSpo) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::IS_SPO, $isSpo);
	}

	/**
	 * Get is own courier / is fresh.
	 *
	 * @api
	 * @return bool
	 */
	public function getIsOwnCourier() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::IS_OWN_COURIER);
	}

	/**
	 * Set is own courier.
	 *
	 * @api
	 * @param  $isOwnCourier
	 * @return bool
	 */
	public function setIsOwnCourier($isOwnCourier) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::IS_OWN_COURIER, $isOwnCourier);
	}

	/**
	 * Get Code Name
	 *
	 * @api
	 * @return string
	 */
	public function getCodeName() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::IS_OWN_COURIER);
	}

	/**
	 * Set Code Name
	 *
	 * @api
	 * @param  $codeName
	 * @return string
	 */
	public function setCodeName($codeName) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::CODE_NAME, $codeName);
	}

	/**
	 * Get Warehouse Source
	 *
	 * @api
	 * @return string
	 */
	public function getWarehouseSource() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::WAREHOUSE_SOURCE);
	}

	/**
	 * Set Warehouse Source
	 *
	 * @api
	 * @param  $warehouseSource
	 * @return string
	 */
	public function setWarehouseSource($warehouseSource) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::WAREHOUSE_SOURCE, $warehouseSource);
	}

	/**
	 * Get Spo Detail
	 *
	 * @api
	 * @return string
	 */
	public function getSpoDetail() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::SPO_DETAIL);
	}

	/**
	 * Set Spo Detail
	 *
	 * @api
	 * @param  $spoDetail
	 * @return string
	 */
	public function setSpoDetail($spoDetail) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::SPO_DETAIL, $spoDetail);
	}

	/**
	 * Get Oar Origin Order Id
	 *
	 * @api
	 * @return string
	 */
	public function getOarOriginOrderId() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::OAR_ORIGIN_ORDER_ID);
	}

	/**
	 * Set Oar Origin Order Id
	 *
	 * @api
	 * @param  $oarOriginId
	 * @return string
	 */
	public function setOarOriginOrderId($oarOriginId) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::OAR_ORIGIN_ORDER_ID, $oarOriginId);
	}

	/**
	 * Get is price promo.
	 *
	 * @api
	 * @return bool
	 */
	public function getIsPricePromo() {
		return $this->getData(IntegrationOrderAllocationRuleInterface::IS_PRICE_PROMO);
	}

	/**
	 * Set is price promo.
	 *
	 * @api
	 * @param  $isPricePromo
	 * @return bool
	 */
	public function setIsPricePromo($isPricePromo) {
		return $this->setData(IntegrationOrderAllocationRuleInterface::IS_PRICE_PROMO, $isPricePromo);
	}
}
