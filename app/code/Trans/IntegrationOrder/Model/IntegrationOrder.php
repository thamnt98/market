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

namespace Trans\IntegrationOrder\Model;

use \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrder as ResourceModel;

class IntegrationOrder extends \Magento\Framework\Model\AbstractModel implements
IntegrationOrderInterface {
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getOmsIdOrder() {
		return $this->getData(IntegrationOrderInterface::OMS_ID_ORDER);
	}

	/**
	 * @inheritdoc
	 */
	public function setOmsIdOrder($omsIdOrder) {
		return $this->setData(IntegrationOrderInterface::OMS_ID_ORDER, $omsIdOrder);
	}

	/**
	 * @inheritdoc
	 */
	public function getReferenceNumber() {
		return $this->getData(IntegrationOrderInterface::REFERENCE_NUMBER);
	}

	/**
	 * @inheritdoc
	 */
	public function setReferenceNumber($refNumber) {

		return $this->setData(IntegrationOrderInterface::REFERENCE_NUMBER, $refNumber);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderId() {
		return $this->getData(IntegrationOrderInterface::ORDER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderId($orderId) {

		return $this->setData(IntegrationOrderInterface::ORDER_ID, $orderId);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderStatus() {
		return $this->getData(IntegrationOrderInterface::ORDER_STATUS);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderStatus($orderStatus) {
		return $this->setData(IntegrationOrderInterface::ORDER_STATUS, $orderStatus);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderType() {
		return $this->getData(IntegrationOrderInterface::ORDER_TYPE);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderType($orderType) {
		return $this->setData(IntegrationOrderInterface::ORDER_TYPE, $orderType);
	}

	/**
	 * @inheritdoc
	 */
	public function getSourceOrder() {
		return $this->getData(IntegrationOrderInterface::ORDER_SOURCE);
	}

	/**
	 * @inheritdoc
	 */
	public function setSourceOrder($orderSource) {
		return $this->setData(IntegrationOrderInterface::ORDER_SOURCE, $orderSource);
	}

	/**
	 * @inheritdoc
	 */
	public function getSourceStore() {
		return $this->getData(IntegrationOrderInterface::SOURCE_STORE);
	}

	/**
	 * @inheritdoc
	 */
	public function setSourceStore($sourceStore) {
		return $this->setData(IntegrationOrderInterface::SOURCE_STORE, $sourceStore);
	}

	/**
	 * @inheritdoc
	 */
	public function getFulfillmentStore() {
		return $this->getData(IntegrationOrderInterface::FULFILLMENT_STORE);
	}

	/**
	 * @inheritdoc
	 */
	public function setFulfillmentStore($fulfillmentStore) {
		return $this->setData(IntegrationOrderInterface::FULFILLMENT_STORE, $fulfillmentStore);
	}

	/**
	 * @inheritdoc
	 */
	public function getTenderType() {
		return $this->getData(IntegrationOrderInterface::TENDER_TYPE);
	}

	/**
	 * @inheritdoc
	 */
	public function setTenderType($tenderType) {
		return $this->setData(IntegrationOrderInterface::TENDER_TYPE, $tenderType);
	}

	/**
	 * @inheritdoc
	 */
	public function getCourier() {
		return $this->getData(IntegrationOrderInterface::COURIER);
	}

	/**
	 * @inheritdoc
	 */
	public function setCourier($courier) {
		return $this->setData(IntegrationOrderInterface::COURIER, $courier);
	}

	/**
	 * @inheritdoc
	 */
	public function getShipmentType() {
		return $this->getData(IntegrationOrderInterface::SHIPMENT_TYPE);
	}

	/**
	 * @inheritdoc
	 */
	public function setShipmentType($shipmentType) {
		return $this->setData(IntegrationOrderInterface::SHIPMENT_TYPE, $shipmentType);
	}

	/**
	 * @inheritdoc
	 */
	public function getShipmentDate() {
		return $this->getData(IntegrationOrderInterface::SHIPMENT_DATE);
	}

	/**
	 * @inheritdoc
	 */
	public function setShipmentDate($shipmentDate) {
		return $this->setData(IntegrationOrderInterface::SHIPMENT_DATE, $shipmentDate);
	}

	/**
	 * @inheritdoc
	 */
	public function getMerchantId() {
		return $this->getData(IntegrationOrderInterface::MERCHANT_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setMerchantId($merchantId) {
		return $this->setData(IntegrationOrderInterface::MERCHANT_ID, $merchantId);
	}

	/**
	 * @inheritdoc
	 */
	public function getAccountName() {
		return $this->getData(IntegrationOrderInterface::ACCOUNT_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setAccountName($accountName) {
		return $this->setData(IntegrationOrderInterface::ACCOUNT_NAME, $accountName);
	}

	/**
	 * @inheritdoc
	 */
	public function getAccountPhoneNumber() {
		return $this->getData(IntegrationOrderInterface::ACCOUNT_PHONE_NUMBER);
	}

	/**
	 * @inheritdoc
	 */
	public function setAccountPhoneNumber($accountPhoneNumber) {
		return $this->setData(IntegrationOrderInterface::ACCOUNT_PHONE_NUMBER, $accountPhoneNumber);
	}

	/**
	 * @inheritdoc
	 */
	public function getAccountEmail() {
		return $this->getData(IntegrationOrderInterface::ACCOUNT_EMAIL);
	}

	/**
	 * @inheritdoc
	 */
	public function setAccountEmail($accountEmail) {
		return $this->setData(IntegrationOrderInterface::ACCOUNT_EMAIL, $accountEmail);
	}

	/**
	 * @inheritdoc
	 */
	public function getReceiverName() {
		return $this->getData(IntegrationOrderInterface::RECIPIENT_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setReceiverName($receiverName) {
		return $this->setData(IntegrationOrderInterface::RECIPIENT_NAME, $receiverName);
	}

	/**
	 * @inheritdoc
	 */
	public function getReceiverPhone() {
		return $this->getData(IntegrationOrderInterface::RECIPIENT_PHONE);
	}

	/**
	 * @inheritdoc
	 */
	public function setReceiverPhone($receiverPhone) {
		return $this->setData(IntegrationOrderInterface::RECIPIENT_PHONE, $receiverPhone);
	}

	/**
	 * @inheritdoc
	 */
	public function getReceiverEmail() {
		return $this->getData(IntegrationOrderInterface::RECIPIENT_EMAIL_ADDRESS);
	}

	/**
	 * @inheritdoc
	 */
	public function setReceiverEmail($receiverEmail) {
		return $this->setData(IntegrationOrderInterface::RECIPIENT_EMAIL_ADDRESS, $receiverEmail);
	}

	/**
	 * @inheritdoc
	 */
	public function getSourceChannel() {
		return $this->getData(IntegrationOrderInterface::SOURCE_CHANNEL);
	}

	/**
	 * @inheritdoc
	 */
	public function setSourceChannel($sourceChannel) {
		return $this->setData(IntegrationOrderInterface::SOURCE_CHANNEL, $sourceChannel);
	}

	/**
	 * @inheritdoc
	 */
	public function getShippingAddress() {
		return $this->getData(IntegrationOrderInterface::SHIPPING_ADDRESS);
	}

	/**
	 * @inheritdoc
	 */
	public function setShippingAddress($shippingAddress) {
		return $this->setData(IntegrationOrderInterface::SHIPPING_ADDRESS, $shippingAddress);
	}

	/**
	 * @inheritdoc
	 */
	public function getBillingAddress() {
		return $this->getData(IntegrationOrderInterface::BILLING_ADDRESS);
	}

	/**
	 * @inheritdoc
	 */
	public function setBillingAddress($billingAddress) {
		return $this->setData(IntegrationOrderInterface::BILLING_ADDRESS, $billingAddress);
	}

	/**
	 * @inheritdoc
	 */
	public function getProvince() {
		return $this->getData(IntegrationOrderInterface::PROVINCE);
	}

	/**
	 * @inheritdoc
	 */
	public function setProvince($province) {
		return $this->setData(IntegrationOrderInterface::PROVINCE, $province);
	}

	/**
	 * @inheritdoc
	 */
	public function getCity() {
		return $this->getData(IntegrationOrderInterface::CITY);
	}

	/**
	 * @inheritdoc
	 */
	public function setCity($city) {
		return $this->setData(IntegrationOrderInterface::CITY, $city);
	}

	/**
	 * @inheritdoc
	 */
	public function getDistrict() {
		return $this->getData(IntegrationOrderInterface::DISTRICT);
	}

	/**
	 * @inheritdoc
	 */
	public function setDistrict($district) {
		return $this->setData(IntegrationOrderInterface::DISTRICT, $district);
	}

	/**
	 * @inheritdoc
	 */
	public function getZipcode() {
		return $this->getData(IntegrationOrderInterface::ZIPCODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setZipcode($zipCode) {
		return $this->setData(IntegrationOrderInterface::ZIPCODE, $zipCode);
	}

	/**
	 * @inheritdoc
	 */
	public function getLongitude() {
		return $this->getData(IntegrationOrderInterface::LONGITUDE);
	}

	/**
	 * @inheritdoc
	 */
	public function setLongitude($longitude) {
		return $this->setData(IntegrationOrderInterface::LONGITUDE, $longitude);
	}

	/**
	 * @inheritdoc
	 */
	public function getLatitude() {
		return $this->getData(IntegrationOrderInterface::LATITUDE);
	}

	/**
	 * @inheritdoc
	 */
	public function setLatitude($latitude) {
		return $this->setData(IntegrationOrderInterface::LATITUDE, $latitude);
	}

	/**
	 * Get Order.
	 *
	 * @api
	 * @return string
	 */
	public function getOrder() {
		$this->order;
	}

	/**
	 * Set Order.
	 *
	 * @api
	 * @return string
	 */
	public function setOrder($order) {
		$this->order = $order;
	}

	/**
	 * Get items.
	 *
	 * @api
	 * @return mixed
	 */
	public function getOrderItems() {
		return $this->getData(IntegrationOrderInterface::ORDER_ITEMS);
	}

	/**
	 * Set items.
	 *
	 * @api
	 * @return mixed
	 */
	public function setOrderItems($orderItems) {
		return $this->setData(IntegrationOrderInterface::ORDER_ITEMS, $orderItems);
	}

	/**
	 * @inheritdoc
	 */
	public function setOarIds($oarIds) {
		return $this->setData(IntegrationOrderInterface::OAR_IDS, $oarIds);
	}

	/**
	 * @inheritdoc
	 */
	public function getQuoteId() {
		return $this->getData(IntegrationOrderInterface::QUOTE_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setQuoteId($quoteId) {
		return $this->setData(IntegrationOrderInterface::QUOTE_ID, $quoteId);
	}

	/**
	 * @inheritdoc
	 */
	public function getCustomerId() {
		return $this->getData(IntegrationOrderInterface::CUSTOMER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setCustomerId($customerId) {
		return $this->setData(IntegrationOrderInterface::CUSTOMER_ID, $customerId);
	}

	/**
	 * @inheritdoc
	 */
	public function getStoreCode() {
		return $this->getData(IntegrationOrderInterface::STORE_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setStoreCode($storeCode) {
		return $this->setData(IntegrationOrderInterface::STORE_CODE, $storeCode);
	}

	/**
	 * Get billing data.
	 *
	 * @api
	 * @return mixed
	 */
	public function getBilling() {
		return $this->getData(IntegrationOrderInterface::BILLING);
	}

	/**
	 * Set billing data.
	 *
	 * @api
	 * @return mixed
	 */
	public function setBilling($billing) {
		return $this->setData(IntegrationOrderInterface::BILLING, $billing);
	}

	/**
	 * Get shipping data.
	 *
	 * @api
	 * @return mixed
	 */
	public function getShipping() {
		return $this->getData(IntegrationOrderInterface::SHIPPING);
	}

	/**
	 * Set shipping data.
	 *
	 * @api
	 * @return mixed
	 */
	public function setShipping($shipping) {
		return $this->setData(IntegrationOrderInterface::SHIPPING, $shipping);
	}

	/**
	 * Get payment data.
	 *
	 * @api
	 * @return mixed
	 */
	public function getPayment() {
		return $this->getData(IntegrationOrderInterface::PAYMENT);
	}

	/**
	 * Set payment data.
	 *
	 * @api
	 * @return mixed
	 */
	public function setPayment($payment) {
		return $this->setData(IntegrationOrderInterface::PAYMENT, $payment);
	}

	/**
	 * Get Flag Spo data.
	 *
	 * @api
	 * @return int
	 */
	public function getFlagSpo() {
		return $this->getData(IntegrationOrderInterface::FLAG_SPO);
	}

	/**
	 * Set Flag Spo data.
	 *
	 * @api
	 * @return int
	 */
	public function setFlagSpo($flagSpo) {
		return $this->setData(IntegrationOrderInterface::FLAG_SPO, $flagSpo);
	}

	/**
	 * @inheritdoc
	 */
	public function getAddressId() {
		return $this->getData(IntegrationOrderInterface::ADDRESS_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setAddressId($addressId) {
		return $this->setData(IntegrationOrderInterface::ADDRESS_ID, $addressId);
	}

	/**
	 * @inheritdoc
	 */
	public function getShippingFee() {
		return $this->getData(IntegrationOrderInterface::SHIPPING_FEE);
	}

	/**
	 * @inheritdoc
	 */
	public function setShippingFee($shippingFee) {
		return $this->setData(IntegrationOrderInterface::SHIPPING_FEE, $shippingFee);
	}

	/**
	 * @inheritdoc
	 */
	public function getSpoDetail() {
		return $this->getData(IntegrationOrderInterface::SPO_DETAIL);
	}

	/**
	 * @inheritdoc
	 */
	public function setSpoDetail($spoDetail) {
		return $this->setData(IntegrationOrderInterface::SPO_DETAIL, $spoDetail);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderOriginId() {
		return $this->getData(IntegrationOrderInterface::ORDER_ORIGIN_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderOriginId($orderOriginId) {
		return $this->setData(IntegrationOrderInterface::ORDER_ORIGIN_ID, $orderOriginId);
	}

	/**
	 * @inheritdoc
	 */
	public function getIsSpo() {
		return $this->getData(IntegrationOrderInterface::IS_SPO);
	}

	/**
	 * @inheritdoc
	 */
	public function setIsSpo($isSpo) {
		return $this->setData(IntegrationOrderInterface::IS_SPO, $isSpo);
	}

	/**
	 * @inheritdoc
	 */
	public function getIsOwnCourier() {
		return $this->getData(IntegrationOrderInterface::IS_OWN_COURIER);
	}

	/**
	 * @inheritdoc
	 */
	public function setIsOwnCourier($isOwnCourier) {
		return $this->setData(IntegrationOrderInterface::IS_OWN_COURIER, $isOwnCourier);
	}

	/**
	 * @inheritdoc
	 */
	public function getWarehouseSource() {
		return $this->getData(IntegrationOrderInterface::WAREHOUSE_SOURCE);
	}

	/**
	 * @inheritdoc
	 */
	public function setWarehouseSource($warehouseSource) {
		return $this->setData(IntegrationOrderInterface::WAREHOUSE_SOURCE, $warehouseSource);
	}

	/**
	 * @inheritdoc
	 */
	public function getWarehouseCode() {
		return $this->getData(IntegrationOrderInterface::WAREHOUSE_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setWarehouseCode($warehouseCode) {
		return $this->setData(IntegrationOrderInterface::WAREHOUSE_CODE, $warehouseCode);
	}

	/**
	 * @inheritdoc
	 */
	public function getCodeName() {
		return $this->getData(IntegrationOrderInterface::CODE_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setCodeName($codeName) {
		return $this->setData(IntegrationOrderInterface::CODE_NAME, $codeName);
	}

	/**
	 * @inheritdoc
	 */
	public function getTimeSlot() {
		return $this->getData(IntegrationOrderInterface::TIME_SLOT);
	}

	/**
	 * @inheritdoc
	 */
	public function setTimeSlot($timeSlot) {
		return $this->setData(IntegrationOrderInterface::TIME_SLOT, $timeSlot);
	}

    /**
     * @inheritdoc
     */
    public function getGrandTotal() {
        return $this->getData(IntegrationOrderInterface::GRAND_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setGrandTotal($grandTotal) {
        return $this->setData(IntegrationOrderInterface::GRAND_TOTAL, $grandTotal);
    }

    /**
     * @inheritdoc
     */
    public function getLogisticCourierName() {
        return $this->getData(IntegrationOrderInterface::LOGISTIC_COURIER_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setLogisticCourierName($logisticCourierName) {
        return $this->setData(IntegrationOrderInterface::LOGISTIC_COURIER_NAME, $logisticCourierName);
    }

    /**
     * @inheritdoc
     */
    public function getLogisticCourier() {
        return $this->getData(IntegrationOrderInterface::LOGISTIC_COURIER);
    }

    /**
     * @inheritdoc
     */
    public function setLogisticCourier($logisticCourier) {
        return $this->setData(IntegrationOrderInterface::LOGISTIC_COURIER, $logisticCourier);
    }
}
