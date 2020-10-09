<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CTCORP DIGITAL. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api\Data;

/**
 * @api
 */
interface IntegrationOrderAllocationRuleInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */

	/**
	 * Constant for table name
	 */
	const TABLE_NAME = 'integration_oms_oar';

	/**
	 * Constant for field table
	 */
	const OAR_IDS             = 'oar_id';
	const QUOTE_ID            = 'quote_id';
	const ADDRESS_ID          = 'address_id';
	const CUSTOMER_ID         = 'customer_id';
	const OAR_CUSTOMER_ID     = 'oar_customer_id';
	const OAR_ORDER_ID        = 'oar_order_id';
	const STORE_CODE          = 'store_code';
	const ORDER_ID            = 'order_id';
	const REFERENCE_NUMBER    = 'reference_number';
	const ITEMS               = 'items';
	const IS_SPO              = 'is_spo';
	const IS_OWN_COURIER      = 'is_own_courier';
	const CODE_NAME           = 'code_name';
	const WAREHOUSE_SOURCE    = 'warehouse_source';
	const SPO_DETAIL          = 'spo_detail';
	const OAR_ORIGIN_ORDER_ID = 'oar_origin_order_id';

	/**
	 * Get OAR Id
	 *
	 * @return int
	 */
	public function getOarIds();

	/**
	 * Set OAR Id
	 *
	 * @param int $oarIds
	 * @return int
	 */
	public function setOarIds($oarIds);

	/**
	 * Get Quote Id
	 *
	 * @return int
	 */
	public function getQuoteId();

	/**
	 * Set Quote Id
	 *
	 * @param int $quoteId
	 * @return mixed
	 */
	public function setQuoteId($quoteId);

	/**
	 * Get Address Id
	 *
	 * @return int
	 */
	public function getAddressId();

	/**
	 * Set Address Id
	 *
	 * @param int $addressId
	 * @return mixed
	 */
	public function setAddressId($addressId);

	/**
	 * Get Customer Id
	 *
	 * @return int
	 */
	public function getCustomerId();

	/**
	 * Set Customer Id
	 *
	 * @param int $customerId
	 * @return mixed
	 */
	public function setCustomerId($customerId);

	/**
	 * Get Oar Customer Id
	 *
	 * @return string
	 */
	public function getOarCustomerId();

	/**
	 * Set Oar Customer Id
	 *
	 * @param string $oarCustomerId
	 * @return mixed
	 */
	public function setOarCustomerId($oarCustomerId);

	/**
	 * Get Oar Order Id
	 *
	 * @return string
	 */
	public function getOarOrderId();

	/**
	 * Set Oar Order Id
	 *
	 * @param string $oarOrderId
	 * @return mixed
	 */
	public function setOarOrderId($oarOrderId);

	/**
	 * Get Store Code
	 *
	 * @return int
	 */
	public function getStoreCode();

	/**
	 * Set Store Code
	 *
	 * @param int $storeCode
	 * @return mixed
	 */
	public function setStoreCode($storeCode);

	/**
	 * Get Order Id
	 *
	 * @return string
	 */
	public function getOrderId();

	/**
	 * Set Order Id
	 *
	 * @param string $orderId
	 * @return mixed
	 */
	public function setOrderId($orderId);

	/**
	 * Get Reference Number
	 *
	 * @return string
	 */
	public function getReferenceNumber();

	/**
	 * Set Reference Number
	 *
	 * @param string $referenceNumber
	 * @return mixed
	 */
	public function setReferenceNumber($referenceNumber);

	/**
	 * Get Addresses Data
	 *
	 * @return string
	 */
	public function getAddresses();

	/**
	 * Set Addresses Data array to Get Data Order Allocation Rule from OMS
	 *
	 * @return string $addresses
	 */
	public function setAddresses($addresses);

	/**
	 * Get Items Data
	 *
	 * @return mixed
	 */
	public function getItems();

	/**
	 * Set Items Data array
	 *
	 * @return mixed
	 */
	public function setItems($items);

	/**
	 * Get Is Spo
	 *
	 * @return bool
	 */
	public function getIsSpo();

	/**
	 * Set Is Spo
	 *
	 * @param bool $isSpo
	 * @return bool
	 */
	public function setIsSpo($isSpo);

	/**
	 * Get Is Own Courier
	 *
	 * @return bool
	 */
	public function getIsOwnCourier();

	/**
	 * Set Is Own Courier
	 *
	 * @param bool $isOwnCourier
	 * @return bool
	 */
	public function setIsOwnCourier($isOwnCourier);

	/**
	 * Get Code Name
	 *
	 * @return string
	 */
	public function getCodeName();

	/**
	 * Set Code Name
	 *
	 * @param string $codeName
	 * @return string
	 */
	public function setCodeName($codeName);

	/**
	 * Get Warehouse Source
	 *
	 * @return string
	 */
	public function getWarehouseSource();

	/**
	 * Set Warehouse Source
	 *
	 * @param string $warehouseSource
	 * @return string
	 */
	public function setWarehouseSource($warehouseSource);

	/**
	 * Get Spo Detail
	 *
	 * @return string
	 */
	public function getSpoDetail();

	/**
	 * Set Spo Detail
	 *
	 * @param string $spoDetail
	 * @return string
	 */
	public function setSpoDetail($spoDetail);

	/**
	 * Get Oar Origin Order Id
	 *
	 * @return string
	 */
	public function getOarOriginOrderId();

	/**
	 * Set Oar Origin Order Id
	 *
	 * @param string $oarOriginId
	 * @return string
	 */
	public function setOarOriginOrderId($oarOriginId);
}
