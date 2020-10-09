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

namespace Trans\IntegrationOrder\Api\Data;

/**
 * @api
 */
interface RefundInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */

	/**
	 * Constant for table name
	 */
	const DEFAULT_EVENT = 'trans_integration';
	const REFUND_TABLE  = 'integration_oms_refund';

	/**
	 * Constant for field table
	 */
	const REFUND_ID           = 'refund_id';
	const ORDER_REF_NUMBER    = 'order_ref_number';
	const REFUND_TRX_NUMBER   = 'refund_trx_number';
	const ORDER_ID            = 'order_id';
	const SKU                 = 'sku';
	const QTY_REFUND          = 'qty_refund'; // based qty allocated
	const AMOUNT_REFUND_SKU   = 'amount_refund_sku'; // amount refund sku per order
	const AMOUNT_REFUND_ORDER = 'amount_refund_order'; // based order_id per reference_number
	const AMOUNT_ORDER        = 'amount_gt_order'; // based grand_total per order_id
	const AMOUNT_REF_NUMBER   = 'amount_ref_number';

	/**
	 * Constant for Refund Indenity
	 */
	const CAPTURE        = 'CAPTURE';
	const REFUND         = 'REFUND';
	const PREFIX_REFUND  = 'RF';
	const PREFIX_CAPTURE = 'CAPTURE';

	/**
	 * Get Refund Id
	 *
	 * @return int
	 */
	public function getRefundId();

	/**
	 * Set Refund Id
	 *
	 * @param int $refundId
	 * @return void
	 */
	public function setRefundId($refundId);

	/**
	 * Get Order Reference Number
	 *
	 * @return string
	 */
	public function getOrderRefNumber();

	/**
	 * Set Order Reference Number
	 *
	 * @param string $orderRefNumber
	 * @return string
	 */
	public function setOrderRefNumber($orderRefNumber);

	/**
	 * Get Refund Transaction Number
	 *
	 * @return string
	 */
	public function getRefundTrxNumber();

	/**
	 * Set Refund Transaction Number
	 *
	 * @param string $refundTrxNumber
	 * @return string
	 */
	public function setRefundTrxNumber($refundTrxNumber);

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
	 * @return string
	 */
	public function setOrderId($orderId);

	/**
	 * Get SKU
	 *
	 * @return string
	 */
	public function getSKU();

	/**
	 * Set SKU
	 *
	 * @param string $sku
	 * @return string
	 */
	public function setSKU($sku);

	/**
	 * Get Refund Qty
	 *
	 * @return int
	 */
	public function getRefundQty();

	/**
	 * Set Refund Qty
	 *
	 * @param int $refundQty
	 * @return int
	 */
	public function setRefundQty($refundQty);

	/**
	 * Get Amount Refund Per Sku
	 *
	 * @return int
	 */
	public function getAmountRefundSku();

	/**
	 * Set Amount Refund Per Sku
	 *
	 * @param int $amountRefundSku
	 * @return int
	 */
	public function setAmountRefundSku($amountRefundSku);

	/**
	 * Get Amount Refund Per Order
	 *
	 * @return int
	 */
	public function getAmountRefundOrder();

	/**
	 * Set Amount Refund Per Order
	 *
	 * @param int $amountRefundOrder
	 * @return int
	 */
	public function setAmountRefundOrder($amountRefundOrder);

	/**
	 * Get Amount Total Per Order
	 *
	 * @return int
	 */
	public function getAmountTotalOrder();

	/**
	 * Set Amount Total Per Order
	 *
	 * @param int $amountTotalOrder
	 * @return int
	 */
	public function setAmountTotalOrder($amountTotalOrder);

	/**
	 * Get Amount Per Reference Number
	 *
	 * @return int
	 */
	public function getAmountReferenceNumber();

	/**
	 * Set Amount Per Reference Number
	 *
	 * @param int $amountReferenceNumber
	 * @return int
	 */
	public function setAmountReferenceNumber($amountReferenceNumber);
}
