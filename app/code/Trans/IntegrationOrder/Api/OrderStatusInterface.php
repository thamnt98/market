<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

/**
 * Interface OrderStatusInterface
 */
interface OrderStatusInterface {
	/**
	 * Get Data Status without sub_action
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @return mixed
	 */
	public function statusNonSubAction($orderId, $status, $action);

	/**
	 * Get Data Status with sub_action
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @param int $subAction
	 * @return mixed
	 */
	public function statusWithSubAction($orderId, $status, $action, $subAction);

	/**
	 * Get Data Status with items
	 *
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @param int $subAction
	 * @param mixed $orderItems
	 * @return mixed[]
	 */
	public function statusOrderItems($orderId, $status, $action, $subAction, $orderItems);

	/**
	 * Get Data Status Update AWB
	 *
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @param string $logisticNumber
	 * @param string $logisticCourier
	 * @return mixed[]
	 */
	public function updateAWB($orderId, $status, $action, $logisticNumber, $logisticCourier);

	/**
	 * OMS able to check status payment in Magento
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @return mixed
	 */
	public function checkPaymentStatus($orderId, $status, $action);
}
