<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CTCORP DIGITAL. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api\Data;

/**
 * @api
 */
interface IntegrationOrderReturnInterface {

	/**
	 * Constant for table name
	 */
	const TABLE_NAME = 'integration_oms_return';

	/**
	 * Constant for field table
	 */
	const ID               = 'id';
	const REFERENCE_NUMBER = 'reference_number';
	const ORDER_ID         = 'order_id';
	const STORE            = 'store';
	const SKU              = 'sku';
	const QTY_INITIATED    = 'qty_initiated';
	const QTY_INPROGRESS   = 'qty_inprogress';
	const QTY_APPROVED     = 'qty_approved';
	const QTY_REJECTED     = 'qty_rejected';
	const RETURN_REASON    = 'return_reason';
	const ITEM_CONDITION   = 'item_condition';
	const RESOLUTION       = 'resolution';
	const STATUS           = 'status';
	const CREATED_AT       = 'created_at';
	const UPDATED_AT       = 'updated_at';

	const RESOLUTION_DATA = 'voucher';

	/**
	 * status magento
	 */
	const RETURN_INITIATED  = 'return_initiated';
	const RETURN_INPROGRESS = 'return_inprogress';
	const RETURN_APPROVED   = 'return_approved';
	const RETURN_REJECTED   = 'return_rejected';

	/**
	 * status oms
	 */
	const STATUS_PROGRESS              = 6;
	const ACTION_PROGRESS              = 7;
	const STATUS_APPROVED_AND_REJECTED = 7;
	const ACTION_APPROVED_AND_REJECTED = 8;
	const SUBACTION_APPROVED           = 1;
	const SUBACTION_REJECTED           = 2;
	const STATUS_CANCEL                = 6;
	const ACTION_CANCEL                = 8;
	const SUBACTION_CANCEL             = 3;

	/**
	 * status condition / resolution
	 */
	const STATUS_ITEM_CONDITION = 'Damaged';
	const STATUS_RESOLUTION     = 'Exchange';

	/**
	 * status
	 */

	/**
	 * Get Id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Set Id
	 *
	 * @param int $id
	 * @return int
	 */
	public function setId($id);

	/**
	 * Get order Id
	 *
	 * @return string
	 */
	public function getOrderId();

	/**
	 * Set order Id
	 *
	 * @param string $orderId
	 * @return string
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
	 * @return string
	 */
	public function setReferenceNumber($referenceNumber);

	/**
	 * Get store
	 *
	 * @return string
	 */
	public function getStore();

	/**
	 * Set store
	 *
	 * @param string $store
	 * @return string
	 */
	public function setStore($store);

	/**
	 * Get sku
	 *
	 * @return string
	 */
	public function getSku();

	/**
	 * Set sku
	 *
	 * @param string $sku
	 * @return string
	 */
	public function setSku($sku);

	/**
	 * Get qty initiated
	 *
	 * @return string
	 */
	public function getQtyInitiated();

	/**
	 * Set qty initiated
	 *
	 * @param string $qtyinitiated
	 * @return string
	 */
	public function setQtyInitiated($qtyinitiated);

	/**
	 * Get qty inprogress
	 *
	 * @return string
	 */
	public function getQtyInprogress();

	/**
	 * Set qty inprogress
	 *
	 * @param string $qtyinprogress
	 * @return string
	 */
	public function setQtyInprogress($qtyinprogress);

	/**
	 * Get qty approved
	 *
	 * @return string
	 */
	public function getQtyApproved();

	/**
	 * Set qty approved
	 *
	 * @param string $qtyapproved
	 * @return string
	 */
	public function setQtyApproved($qtyapproved);

	/**
	 * Get qty rejected
	 *
	 * @return string
	 */
	public function getQtyRejected();

	/**
	 * Set qty rejected
	 *
	 * @param string $qtyrejected
	 * @return string
	 */
	public function setQtyRejected($qtyrejected);

	/**
	 * Get return reason
	 *
	 * @return string
	 */
	public function getReturnReason();

	/**
	 * Set return reason
	 *
	 * @param string $returnReason
	 * @return string
	 */
	public function setReturnReason($returnReason);

	/**
	 * Get item condition
	 *
	 * @return string
	 */
	public function getItemCondition();

	/**
	 * Set item condition
	 *
	 * @param string $itemCondition
	 * @return string
	 */
	public function setItemCondition($itemCondition);

	/**
	 * Get resolution
	 *
	 * @return string
	 */
	public function getResolution();

	/**
	 * Set resolution
	 *
	 * @param string $resolution
	 * @return string
	 */
	public function setResolution($resolution);

	/**
	 * Get return Status
	 *
	 * @return string
	 */
	public function getStatus();

	/**
	 * Set return Status
	 *
	 * @param string $status
	 * @return string
	 */
	public function setStatus($status);

	/**
	 * Get created at
	 *
	 * @return string
	 */
	public function getCreatedAt();

	/**
	 * Set created at
	 *
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt);

	/**
	 * Get updated at
	 *
	 * @return string
	 */
	public function getUpdatedAt();

	/**
	 * Set updated at
	 *
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);
}
