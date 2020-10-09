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
interface IntegrationOrderPaymentInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */

	/**
	 * Constant for table name
	 */
	const DEFAULT_EVENT = 'trans_integration';
	const TABLE_NAME    = 'integration_oms_order_payment';

	/**
	 * Constant for field table
	 */
	const OMS_ID_ORDER_PAYMENT   = 'oms_id_order_payment';
	const REFERENCE_NUMBER       = 'reference_number';
	const ORDER_ID               = 'order_id';
	const PAYMENT_REF_NUMBER_1   = 'payment_ref_number_1';
	const PAYMENT_REF_NUMBER_2   = 'payment_ref_number_2';
	const ORDER_PAID_DATE_TIME   = 'order_paid_date_time';
	const CREATE_ORDER_DATE_TIME = 'create_order_date_time';
	const SPLIT_PAYMENT          = 'split_payment';
	const PAYMENT_TYPE_1         = 'payment_type_1';
	const PAYMENT_TYPE_2         = 'payment_type_2';
	const AMOUNT_OF_PAYMENT_1    = 'amount_of_payment_1';
	const AMOUNT_OF_PAYMENT_2    = 'amount_of_payment_2';
	const TOTAL_AMOUNT_PAID      = 'total_amount_paid';
	const PAYMENT_STATUS         = 'payment_status';

	/**
	 * Get Oms Id Order Payment
	 *
	 * @return int
	 */
	public function getOmsIdOrderPayment();

	/**
	 * Set Oms Id Order Payment
	 *
	 * @param int $omsIdOrderPayment
	 * @return void
	 */
	public function setOmsIdOrderPayment($omsIdOrderPayment);

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
	 * @return void
	 */
	public function setReferenceNumber($referenceNumber);

	/**
	 * Get Order Id
	 *
	 * @param string
	 */
	public function getOrderId();

	/**
	 * Set Order Id
	 *
	 * @param string $orderId
	 * @return void
	 */
	public function setOrderId($orderId);

	/**
	 * Get Payment Ref Number 1
	 *
	 * @param string
	 */
	public function getPaymentRefNumber1();

	/**
	 * Set Payment Ref Number 1
	 *
	 * @param string $paymentRefNumber1
	 * @return void
	 */
	public function setPaymentRefNumber1($paymentRefNumber1);

	/**
	 * Get Payment Ref Number 2
	 *
	 * @param string
	 */
	public function getPaymentRefNumber2();

	/**
	 * Set Payment Ref Number 2
	 *
	 * @param string $paymentRefNumber2
	 * @return void
	 */
	public function setPaymentRefNumber2($paymentRefNumber2);

	/**
	 * Get Order Paid Date Time
	 *
	 * @return string
	 */
	public function getOrderPaidDateTime();

	/**
	 * Set Order Paid Date Time
	 *
	 * @param string $orderPaidDateTime
	 * @return string
	 */
	public function setOrderPaidDateTime($orderPaidDateTime);

	/**
	 * Get Create Order Date Time
	 *
	 * @return string
	 */
	public function getCreateOrderDateTime();

	/**
	 * Set Create Order Date Time
	 *
	 * @param string $createOrderDateTime
	 * @return string
	 */
	public function setCreateOrderDateTime($createOrderDateTime);

	/**
	 * Get Split Payment
	 *
	 * @param boolean
	 */
	public function getSplitPayment();

	/**
	 * Set Split Payment
	 *
	 * @param boolean $splitPayment
	 * @return void
	 */
	public function setSplitPayment($splitPayment);

	/**
	 * Get Payment Type 1
	 *
	 * @param string
	 */
	public function getPaymentType1();

	/**
	 * Set Payment Type 1
	 *
	 * @param string $paymentType1
	 * @return void
	 */
	public function setPaymentType1($paymentType1);

	/**
	 * Get Payment Type 2
	 *
	 * @param string
	 */
	public function getPaymentType2();

	/**
	 * Set Payment Type 2
	 *
	 * @param string $paymentType2
	 * @return void
	 */
	public function setPaymentType2($paymentType2);

	/**
	 * Get Amount of Payment 1
	 *
	 * @param float
	 */
	public function getAmountOfPayment1();

	/**
	 * Set Amount of Payment 1
	 *
	 * @param float $amountOfPayment1
	 * @return void
	 */
	public function setAmountOfPayment1($amountOfPayment1);

	/**
	 * Get Amount of Payment 2
	 *
	 * @param float
	 */
	public function getAmountOfPayment2();

	/**
	 * Set Amount of Payment 2
	 *
	 * @param float $amountOfPayment2
	 * @return void
	 */
	public function setAmountOfPayment2($amountOfPayment2);

	/**
	 * Get Total Amount Paid
	 *
	 * @param float
	 */
	public function getTotalAmountPaid();

	/**
	 * Set Total Amount Paid
	 *
	 * @param float $totalAmountPaid
	 * @return void
	 */
	public function setTotalAmountPaid($totalAmountPaid);

	/**
	 * Get Payment Status
	 *
	 * @param string
	 */
	public function getPaymentStatus();

	/**
	 * Set Payment Status
	 *
	 * @param string $paymentStatus
	 * @return void
	 */
	public function setPaymentStatus($paymentStatus);
}