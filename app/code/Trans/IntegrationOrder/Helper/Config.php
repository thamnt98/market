<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * Constant config path
	 * Order Magento
	 */
	const OMS_BASE_URL    = 'integrationorder/oms_integration/oms_base_url';
	const OMS_DEST        = 'integrationorder/oms_integration/oms_header_dest';
	const OMS_MERCHANT_ID = 'integrationorder/oms_integration/oms_merchant_id';
	const OMS_LOGIN_API   = 'integrationorder/oms_integration/oms_login_path';
	const OMS_ORDER_API   = 'integrationorder/oms_integration/oms_order_path';
	const OMS_OAR_API     = 'integrationorder/oms_integration/oms_oar_path';
	const OMS_PAYMENT_API = 'integrationorder/oms_integration/oms_payment_path';
	const OMS_RETURN_API  = 'integrationorder/oms_integration/oms_return_path';
	const OMS_TOKEN       = 'integrationorder/oms_integration/oms_token';

	/* Update Status Order */
	const IN_PROCESS_STATUS                = 'integrationorder/update_order_status_oms/in_process_status/in_process_status_order';
	const IN_DELIVERY_STATUS               = 'integrationorder/update_order_status_oms/in_delivery_status/in_delivery_status_order';
	const DELIVERED_STATUS                 = 'integrationorder/update_order_status_oms/delivered_status/delivered_status_order';
	const ORDER_CANCELED_STATUS            = 'integrationorder/update_order_status_oms/order_canceled_status/order_canceled_status_order';
	const PICKUP_BY_CUSTOMER_STATUS        = 'integrationorder/update_order_status_oms/pickup_by_customer_status/pickup_by_customer_status_order';
	const IN_TRANSIT_STATUS                = 'integrationorder/update_order_status_oms/in_transit_status/in_transit_status_order';
	const IN_PROCESS_WAITING_PICKUP_STATUS = 'integrationorder/update_order_status_oms/in_process_waiting_pickup_status/in_process_waiting_pickup_status_order';
	const FAILED_DELIVERY_STATUS           = 'integrationorder/update_order_status_oms/failed_delivery_status/failed_delivery_status_order';

	/* NUMBER STATUS ORDER */
	const NUMBER_STATUS_IN_PROCESS         = 'integrationorder/update_order_status_oms/in_process_status/number_status_in_process';
	const NUMBER_STATUS_IN_DELIVERY        = 'integrationorder/update_order_status_oms/in_delivery_status/number_status_in_delivery';
	const NUMBER_STATUS_DELIVERED          = 'integrationorder/update_order_status_oms/delivered_status/number_status_delivered';
	const NUMBER_STATUS_ORDER_CANCELED     = 'integrationorder/update_order_status_oms/order_canceled_status/number_status_order_canceled';
	const NUMBER_PICKUP_BY_CUSTOMER        = 'integrationorder/update_order_status_oms/pickup_by_customer_status/number_pickup_by_customer';
	const NUMBER_IN_TRANSIT                = 'integrationorder/update_order_status_oms/in_transit_status/number_in_transit';
	const NUMBER_IN_PROCESS_WAITING_PICKUP = 'integrationorder/update_order_status_oms/in_process_waiting_pickup_status/number_in_process_waiting_pickup';
	const NUMBER_FAILED_DELIVERY           = 'integrationorder/update_order_status_oms/failed_delivery_status/number_failed_delivery';

	/**
	 * Get config value by path
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function getConfigValue($path) {
		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
	}

	/**
	 * get oms base url
	 *
	 * @return string
	 */
	public function getOmsBaseUrl() {
		return $this->getConfigValue(self::OMS_BASE_URL);
	}

	/**
	 * get oms header destination
	 *
	 * @return string
	 */
	public function getOmsDest() {
		return $this->getConfigValue(self::OMS_DEST);
	}

	/**
	 * get oms token
	 *
	 * @return string
	 */
	public function getOmsToken() {
		return $this->getConfigValue(self::OMS_TOKEN);
	}

	/**
	 * get oms merchant id
	 *
	 * @return string
	 */
	public function getOmsMerchantId() {
		return $this->getConfigValue(self::OMS_MERCHANT_ID);
	}

	/**
	 * get oms login api
	 *
	 * @return string
	 */
	public function getOmsLoginApi() {
		return $this->getConfigValue(self::OMS_LOGIN_API);
	}

	/**
	 * get oms order api
	 *
	 * @return string
	 */
	public function getOmsOrderApi() {
		return $this->getConfigValue(self::OMS_ORDER_API);
	}

	/**
	 * get oms oar api
	 *
	 * @return string
	 */
	public function getOmsOarApi() {
		return $this->getConfigValue(self::OMS_OAR_API);
	}

	/**
	 * get oms payment status api
	 *
	 * @return string
	 */
	public function getOmsPaymentStatusApi() {
		return $this->getConfigValue(self::OMS_PAYMENT_API);
	}

	/**
	 * Get data In Process Status
	 *
	 * @return string
	 */
	public function getInProcessOrderStatus() {
		return $this->getConfigValue(self::IN_PROCESS_STATUS);
	}

	/**
	 * Get data In Delivery Status
	 *
	 * @return string
	 */
	public function getInDeliveryOrderStatus() {
		return $this->getConfigValue(self::IN_DELIVERY_STATUS);
	}

	/**
	 * Get data Delivered Status
	 *
	 * @return string
	 */
	public function getDeliveredOrderStatus() {
		return $this->getConfigValue(self::DELIVERED_STATUS);
	}

	/**
	 * Get data Order Canceled Status
	 *
	 * @return string
	 */
	public function getOrderCanceledStatus() {
		return $this->getConfigValue(self::ORDER_CANCELED_STATUS);
	}

	/**
	 * Get data Order Ready For Pickup By Customer status
	 *
	 * @return string
	 */
	public function getPickupByCustomerStatus() {
		return $this->getConfigValue(self::PICKUP_BY_CUSTOMER_STATUS);
	}

	/**
	 * Get data Order In Transit status
	 *
	 * @return string
	 */
	public function getInTransitStatus() {
		return $this->getConfigValue(self::IN_TRANSIT_STATUS);
	}

	/**
	 * Get data Order In Process - Waiting Pick Up By Customer status
	 *
	 * @return string
	 */
	public function getInProcessWaitingPickupStatus() {
		return $this->getConfigValue(self::IN_PROCESS_WAITING_PICKUP_STATUS);
	}

	/**
	 * Get data Order Failed Delivery status
	 *
	 * @return string
	 */
	public function getFailedDeliveryStatus() {
		return $this->getConfigValue(self::FAILED_DELIVERY_STATUS);
	}

	/**
	 * Get number In Process status from table OMS Order Status (11)
	 *
	 * @return string
	 */
	public function getNumberInProcess() {
		return $this->getConfigValue(self::NUMBER_STATUS_IN_PROCESS);
	}

	/**
	 * Get number In Delivery status from table OMS Order Status
	 *
	 * @return string
	 */
	public function getNumberInDelivery() {
		return $this->getConfigValue(self::NUMBER_STATUS_IN_DELIVERY);
	}

	/**
	 * Get number Delivered status from table OMS Order Status
	 *
	 * @return string
	 */
	public function getNumberDelivered() {
		return $this->getConfigValue(self::NUMBER_STATUS_DELIVERED);
	}

	/**
	 * Get number Order Canceled status from table OMS Order Status
	 *
	 * @return string
	 */
	public function getNumberOrderCanceled() {
		return $this->getConfigValue(self::NUMBER_STATUS_ORDER_CANCELED);
	}

	/**
	 * Get number Ready For Pickup By Customer status from table OMS Order Status
	 *
	 * @return string
	 */
	public function getNumberPickUpByCustomer() {
		return $this->getConfigValue(self::NUMBER_PICKUP_BY_CUSTOMER);
	}

	/**
	 * Get number In Transit Status
	 *
	 * @return string
	 */
	public function getNumberInTransit() {
		return $this->getConfigValue(self::NUMBER_IN_TRANSIT);
	}

	/**
	 * Get number In Process - Waiting Pick Up By Customer Status
	 *
	 * @return string
	 */
	public function getNumberInProcessWaitingPickup() {
		return $this->getConfigValue(self::NUMBER_IN_PROCESS_WAITING_PICKUP);
	}

	/**
	 * Get number Failed Delivery Status
	 *
	 * @return string
	 */
	public function getNumberFailedDelivery() {
		return $this->getConfigValue(self::NUMBER_FAILED_DELIVERY);
	}

	/**
	 * Get API Url Return
	 * @return string
	 */
	public function getOmsReturnApi() {
		return $this->getConfigValue(self::OMS_RETURN_API);
	}
}
