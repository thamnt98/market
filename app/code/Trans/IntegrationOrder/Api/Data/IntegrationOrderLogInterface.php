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
interface IntegrationOrderLogInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */

	/**
	 * Constant for table name
	 */
	const DEFAULT_EVENT = 'trans_integration';
	const TABLE_NAME    = 'integration_oms_order_log';

	/**
	 * Constant for field table
	 * Date time data type
	 */
	const OMS_ID_ORDER_LOG = 'oms_id_order_log';
	const REFERENCE_NUMBER = 'reference_number';
	const PAYMENT_PENDING  = 'payment_pending';
	const IN_PROCESS       = 'in_process';
	const READY_TO_DELIVER = 'ready_to_deliver';
	const READY_TO_PICKUP  = 'ready_to_pickup';
	const OUT_OF_DELIVERY  = 'out_of_delivery'; //in delivery
	const IN_TRANSIT       = 'in_transit'; //in delivery
	const GOODS_ACCEPTANCE = 'good_acceptance';

	/**
	 * Get Reference Number
	 *
	 * @param string
	 */
	public function getReferenceNumber();

	/**
	 * Set Reference Number
	 *
	 * @param string $referenceNumber
	 * @return string
	 */
	public function setReferenceNumber($referenceNumber);

	/**
	 * Get Payment Pending
	 *
	 * @return string
	 */
	public function getPaymentPending();

	/**
	 * Set Payment Pending
	 *
	 * @param string $paymentPending
	 * @return string
	 */
	public function setPaymentPending($paymentPending);

	/**
	 * Get In Process
	 *
	 * @return string
	 */
	public function getInProcess();

	/**
	 * Set In Process
	 *
	 * @param float $amount
	 * @return string
	 */
	public function setInProcess($inProcess);

	/**
	 * Get Ready To Deliver
	 *
	 * @return string
	 */
	public function getReadyToDeliver();

	/**
	 * Set Ready To Deliver
	 *
	 * @param string $readyToDeliver
	 * @return string
	 */
	public function setReadyToDeliver($readyToDeliver);

	/**
	 * Get Ready To Pickup
	 *
	 * @return string
	 */
	public function getReadyToPickup();

	/**
	 * Set Ready To Pickup
	 *
	 * @param string $readyToPickup
	 * @return string
	 */
	public function setReadyToPickup($readyToPickup);

	/**
	 * Get Out Of Delivery
	 *
	 * @return string
	 */
	public function getOutOfDelivery();

	/**
	 * Set Out Of Delivery
	 *
	 * @param string $outOfDelivery
	 * @return string
	 */
	public function setOutOfDelivery($outOfDelivery);

	/**
	 * Get In Transit
	 *
	 * @return string
	 */
	public function getInTransit();

	/**
	 * Set In Transit
	 *
	 * @param string $outOfDelivery
	 * @return string
	 */
	public function setInTransit($outOfDelivery);

	/**
	 * Get Goods Acceptance
	 *
	 * @return string
	 */
	public function getGoodsAcceptance();

	/**
	 * Set Goods Acceptance
	 *
	 * @param string $goodsAcceptance
	 * @return string
	 */
	public function setGoodsAcceptance($goodsAcceptance);
}