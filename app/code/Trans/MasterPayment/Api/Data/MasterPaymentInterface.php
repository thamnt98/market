<?php
/**
 * @category Trans
 * @package  trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Api\Data;

/**
 * interface MasterPaymentCodeinterface
 */
interface MasterPaymentInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const DEFAULT_PREFIX = 'master_payment';
	const DEFAULT_EVENT  = 'trans_master_payment';
	const TABLE_NAME     = 'master_payment';

	const ID             = 'id';
	const PAYMENT_ID     = 'payment_id';
	const PAYMENT_TITLE  = 'payment_title';
	const PAYMENT_METHOD = 'payment_method';
	const PAYMENT_TERMS  = 'payment_terms';
	const CREATED_AT     = 'created_at';
	const UPDATED_AT     = 'updated_at';

	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id);

	/**
	 * @return string
	 */
	public function getPaymentId();

	/**
	 * @param string $paymentId
	 * @return void
	 */
	public function setPaymentId($paymentId);

	/**
	 * @return string
	 */
	public function getPaymentTitle();

	/**
	 * @param string $paymentTitle
	 * @return void
	 */
	public function setPaymentTitle($paymentTitle);

	/**
	 * @return string
	 */
	public function getPaymentMethod();

	/**
	 * @param string $paymentMethod
	 * @return void
	 */
	public function setPaymentMethod($paymentMethod);

	/**
	 * @return string
	 */
	public function getPaymentTerms();

	/**
	 * @param string $paymentTerms
	 * @return void
	 */
	public function setPaymentTerms($paymentTerms);

	/**
	 * @return datetime
	 */
	public function getCreatedAt();

	/**
	 * @param datetime $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt);

	/**
	 * @return datetime
	 */
	public function getUpdatedAt();

	/**
	 * @param datetime $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);

}
