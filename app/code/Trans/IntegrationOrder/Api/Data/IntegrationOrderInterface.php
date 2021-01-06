<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api\Data;

/**
 * @api
 */
interface IntegrationOrderInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */

	/**
	 * Constant for table name
	 */
	const TABLE_NAME = 'integration_oms_order';

	/**
	 * Constant for field table
	 */
	const OMS_ID_ORDER            = 'oms_id_order';
	const REFERENCE_NUMBER        = 'reference_number';
	const ORDER_ID                = 'order_id';
	const ORDER_STATUS            = 'order_status';
	const ORDER_TYPE              = 'order_type';
	const ORDER_SOURCE            = 'order_source';
	const SOURCE_STORE            = 'source_store';
	const FULFILLMENT_STORE       = 'fulfillment_store';
	const TENDER_TYPE             = 'tender_type';
	const COURIER                 = 'courier';
	const SHIPMENT_TYPE           = 'shipment_type';
	const SHIPMENT_DATE           = 'shipment_date'; // time slot
	const MERCHANT_ID             = 'merchant_id';
	const ORDER_CREATED_DATE      = 'order_created_date';
	const ACCOUNT_NAME            = 'customer_name';
	const ACCOUNT_PHONE_NUMBER    = 'customer_phone_number';
	const ACCOUNT_EMAIL           = 'customer_email';
	const SOURCE_CHANNEL          = 'source_channel';
	const RECIPIENT_NAME          = 'receiver_name';
	const RECIPIENT_PHONE         = 'receiver_phone';
	const RECIPIENT_EMAIL_ADDRESS = 'recipient_email_address';
	const SHIPPING_ADDRESS        = 'shipping_address';
	const BILLING_ADDRESS         = 'billing_address';
	const PROVINCE                = 'province';
	const CITY                    = 'city';
	const DISTRICT                = 'district';
	const ZIPCODE                 = 'zipcode';
	const LONGITUDE               = 'longitude';
	const LATITUDE                = 'latitude';

	// Custom Data Parameter
	const ORDER_ITEMS  = 'order_items';
	const CUSTOMER_ID  = 'customer_id';
	const QUOTE_ID     = 'quote_id';
	const STORE_CODE   = 'store_code';
	const BILLING      = 'billing';
	const SHIPPING     = 'shipping';
	const PAYMENT      = 'payment';
	const FLAG_SPO     = 'flag_spo'; // 1 = Non Warehouse, 2 = Warehouse
	const ADDRESS_ID   = 'address_id'; //
	const SHIPPING_FEE = 'shipping_fee'; //

	// Custom Data Field
	const SOURCE_CHANNELS = 1;
	const SPLIT_PAYMENTS  = 0;

	// Smart OSC custom
	const SPO_DETAIL       = 'spo_detail';
	const ORDER_ORIGIN_ID  = 'order_origin_id';
	const IS_SPO           = 'is_spo';
	const IS_OWN_COURIER   = 'is_own_courier';
	const WAREHOUSE_SOURCE = 'warehouse_source';
	const WAREHOUSE_CODE   = 'warehouse_code';
	const CODE_NAME        = 'code_name';
	const TIME_SLOT        = 'time_slot';
    const GRAND_TOTAL      = 'grand_total';
    const LOGISTIC_COURIER_NAME = 'logistic_courier_name';
    const LOGISTIC_COURIER = 'LOGISTIC_COURIER';

	/**
	 * Get Oms Order Id
	 *
	 * @return int
	 */
	public function getOmsIdOrder();

	/**
	 * Set Oms Order Id
	 *
	 * @param int $omsIdOrder
	 * @return mixed
	 */
	public function setOmsIdOrder($omsIdOrder);

	/**
	 * Get Reference Number
	 *
	 * @return string
	 */
	public function getReferenceNumber();

	/**
	 * Set Reference Number
	 *
	 * @param string $refNumber
	 * @return mixed
	 */
	public function setReferenceNumber($refNumber);

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
	 * Get Order Status
	 *
	 * @return string
	 */
	public function getOrderStatus();

	/**
	 * Set Order Status
	 *
	 * @param string $orderStatus
	 * @return mixed
	 */
	public function setOrderStatus($orderStatus);

	/**
	 * Get Order Type
	 *
	 * @return int
	 */
	public function getOrderType();

	/**
	 * Set Order Type
	 *
	 * @param string $orderType
	 * @return mixed
	 */

	public function setOrderType($orderType);

	/**
	 * Get Source Order
	 *
	 * @return string
	 */
	public function getSourceOrder();

	/**
	 * Set Source Order
	 *
	 * @param string $sourceOrder
	 * @return mixed
	 */
	public function setSourceOrder($sourceOrder);

	/**
	 * Get Source Store
	 *
	 * @return string
	 */
	public function getSourceStore();

	/**
	 * Set Source Store
	 *
	 * @param string $sourceStore
	 * @return mixed
	 */
	public function setSourceStore($sourceStore);

	/**
	 * Get Fulfillment Store
	 *
	 * @return string
	 */
	public function getFulfillmentStore();

	/**
	 * Set Fulfillment Store
	 *
	 * @param string $fulfillmentStore
	 * @return mixed
	 */
	public function setFulfillmentStore($fulfillmentStore);

	/**
	 * Get Tender Type
	 *
	 * @return string
	 */
	public function getTenderType();

	/**
	 * Set Tender Type
	 *
	 * @param string $tenderType
	 * @return mixed
	 */
	public function setTenderType($tenderType);

	/**
	 * Get Courier
	 *
	 * @return string
	 */
	public function getCourier();

	/**
	 * Set Courier
	 *
	 * @param string $courier
	 * @return mixed
	 */
	public function setCourier($courier);

	/**
	 * Get Shipment Type
	 *
	 * @return string
	 */
	public function getShipmentType();

	/**
	 * Set Shipment Type
	 *
	 * @param string $shipmentType
	 * @return mixed
	 */
	public function setShipmentType($shipmentType);

	/**
	 * Get Shipment Date
	 *
	 * @return string
	 */
	public function getShipmentDate();

	/**
	 * Set Shipment Date
	 *
	 * @param datetime $shipmentDate
	 * @return string
	 */
	public function setShipmentDate($shipmentDate);

	/**
	 * Get Merchant Id
	 *
	 * @return string
	 */
	public function getMerchantId();

	/**
	 * Set Merchant Id
	 *
	 * @param string $merchantId
	 * @return mixed
	 */
	public function setMerchantId($merchantId);

	/**
	 * Get Customer Name
	 *
	 * @return string
	 */
	public function getAccountName();

	/**
	 * Set Customer Name
	 *
	 * @param string $accountName
	 * @return mixed
	 */
	public function setAccountName($accountName);

	/**
	 * Get Customer Phone Number
	 *
	 * @return string
	 */
	public function getAccountPhoneNumber();

	/**
	 * Set Customer Phone Number
	 *
	 * @param string $accountPhoneNumber
	 * @return mixed
	 */
	public function setAccountPhoneNumber($accountPhoneNumber);

	/**
	 * Get Customer Email
	 *
	 * @return string
	 */
	public function getAccountEmail();

	/**
	 * Set Customer Email
	 *
	 * @param string $accountEmail
	 * @return mixed
	 */
	public function setAccountEmail($accountEmail);

	/**
	 * Get Receiver Name
	 *
	 * @return string
	 */
	public function getReceiverName();

	/**
	 * Set Customer Name
	 *
	 * @param string $receiverName
	 * @return mixed
	 */
	public function setReceiverName($receiverName);

	/**
	 * Get Receiver Phone Number
	 *
	 * @return string
	 */
	public function getReceiverPhone();

	/**
	 * Set Receiver Phone Number
	 *
	 * @param string $receiverPhone
	 * @return mixed
	 */
	public function setReceiverPhone($receiverPhone);

	/**
	 * Get Receiver Email
	 *
	 * @return string
	 */
	public function getReceiverEmail();

	/**
	 * Set Receiver Email
	 *
	 * @param string $receiverEmail
	 * @return mixed
	 */
	public function setReceiverEmail($receiverEmail);

	/**
	 * Get Source Channel
	 *
	 * @return string
	 */
	public function getSourceChannel();

	/**
	 * Set Source Channel
	 *
	 * @param int $sourceChannel
	 * @return mixed
	 */
	public function setSourceChannel($sourceChannel);

	/**
	 * Get Shipping Address
	 *
	 * @return string
	 */
	public function getShippingAddress();

	/**
	 * Set Shipping Address
	 *
	 * @param string $shippingAddress
	 * @return mixed
	 */
	public function setShippingAddress($shippingAddress);

	/**
	 * Get Billing Address
	 *
	 * @return string
	 */
	public function getBillingAddress();

	/**
	 * Set Billing Address
	 *
	 * @param string $billingAddress
	 * @return mixed
	 */
	public function setBillingAddress($billingAddress);

	/**
	 * Get Province
	 *
	 * @return string
	 */
	public function getProvince();

	/**
	 * Set Province
	 *
	 * @param string $province
	 * @return mixed
	 */
	public function setProvince($province);

	/**
	 * Get City
	 *
	 * @return string
	 */
	public function getCity();

	/**
	 * Set City
	 *
	 * @param string $city
	 * @return mixed
	 */
	public function setCity($city);

	/**
	 * Get District
	 *
	 * @return string
	 */
	public function getDistrict();

	/**
	 * Set District
	 *
	 * @param string $district
	 * @return mixed
	 */
	public function setDistrict($district);

	/**
	 * Get Zipcode
	 *
	 * @return string
	 */
	public function getZipcode();

	/**
	 * Set Zipcode
	 *
	 * @param string $zipCode
	 * @return mixed
	 */
	public function setZipcode($zipCode);

	/**
	 * Get Longitude
	 *
	 * @return string
	 */
	public function getLongitude();

	/**
	 * Set Longitude
	 *
	 * @param string $longitude
	 * @return mixed
	 */
	public function setLongitude($longitude);

	/**
	 * Get Latitude
	 *
	 * @return string
	 */
	public function getLatitude();

	/**
	 * Set Latitude
	 *
	 * @param string $latitude
	 * @return mixed
	 */
	public function setLatitude($latitude);

	/**
	 * Get Order Data
	 *
	 * @return string
	 */
	public function getOrder();

	/**
	 * Set Order Data array for create order to OMS
	 *
	 * @return string $order
	 */
	public function setOrder($order);

	/**
	 * Get Items Data
	 *
	 * @return mixed
	 */
	public function getOrderItems();

	/**
	 * Set Items Data array
	 *
	 * @return mixed
	 */
	public function setOrderItems($orderItems);

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
	 * Get Store Code Id
	 *
	 * @return string
	 */
	public function getStoreCode();

	/**
	 * Set Store Code Id
	 *
	 * @param string $quoteId
	 * @return mixed
	 */
	public function setStoreCode($quoteId);

	/**
	 * Get Billing Data
	 *
	 * @return mixed
	 */
	public function getBilling();

	/**
	 * Set Billing Data array
	 *
	 * @return mixed
	 */
	public function setBilling($billing);

	/**
	 * Get Shipping Data
	 *
	 * @return mixed
	 */
	public function getShipping();

	/**
	 * Set Shipping Data array
	 *
	 * @return mixed
	 */
	public function setShipping($shipping);

	/**
	 * Get Payment Data
	 *
	 * @return mixed
	 */
	public function getPayment();

	/**
	 * Set Payment Data array
	 *
	 * @return mixed
	 */
	public function setPayment($payment);

	/**
	 * Get Flag Spo
	 *
	 * @return int
	 */
	public function getFlagSpo();

	/**
	 * Set Flag Spo
	 *
	 * @param int $flagSpo
	 * @return mixed
	 */
	public function setFlagSpo($flagSpo);

	/**
	 * Get Address Id
	 *
	 * @return string
	 */
	public function getAddressId();

	/**
	 * Set Address Id
	 *
	 * @param bool $addressId
	 * @return string
	 */
	public function setAddressId($addressId);

	/**
	 * Get Shipping Fee
	 *
	 * @return int
	 */
	public function getShippingFee();

	/**
	 * Set Shipping Fee
	 *
	 * @param int $addressId
	 * @return int
	 */
	public function setShippingFee($shippingFee);

	/**
	 * @return string
	 */
	public function getSpoDetail();

	/**
	 * @param string $spoDetail
	 * @return string
	 */
	public function setSpoDetail($spoDetail);

	/**
	 * @return int
	 */
	public function getOrderOriginId();

	/**
	 * @param int $orderOriginId
	 * @return int
	 */
	public function setOrderOriginId($orderOriginId);

	/**
	 * @return int
	 */
	public function getIsSpo();

	/**
	 * @param int $isSpo
	 * @return int
	 */
	public function setIsSpo($isSpo);

	/**
	 * @return int
	 */
	public function getIsOwnCourier();

	/**
	 * @param int $isOwnCourier
	 * @return int
	 */
	public function setIsOwnCourier($isOwnCourier);

	/**
	 * @return string
	 */
	public function getWarehouseSource();

	/**
	 * @param string $warehouseSource
	 * @return string
	 */
	public function setWarehouseSource($warehouseSource);

	/**
	 * @return string
	 */
	public function getWarehouseCode();

	/**
	 * @param string $warehouseCode
	 * @return string
	 */
	public function setWarehouseCode($warehouseCode);

	/**
	 * @return string
	 */
	public function getCodeName();

	/**
	 * @param string $codeName
	 * @return string
	 */
	public function setCodeName($codeName);

	/**
	 * @return string
	 */
	public function getTimeSlot();

	/**
	 * @param datetime $timeSlot
	 * @return string
	 */
	public function setTimeSlot($timeSlot);

    /**
     * @return int
     */
    public function getGrandTotal();

    /**
     * @param int $grandTotal
     * @return int
     */
    public function setGrandTotal($grandTotal);

    /**
     * @return string
     */
    public function getLogisticCourierName();

    /**
     * @param string $logisticCourierName
     * @return string
     */
    public function setLogisticCourierName($logisticCourierName);

    /**
     * @return int
     */
    public function getLogisticCourier();

    /**
     * @param int $logisticCourier
     * @return int
     */
    public function setLogisticCourier($logisticCourier);
}
