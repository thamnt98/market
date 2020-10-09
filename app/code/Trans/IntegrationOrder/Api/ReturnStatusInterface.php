<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.co.id>
 * @author   Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

/**
 * @api
 * Interface ReturnStatusInterface
 */
interface ReturnStatusInterface {

	/**
	 * send Return data to OMS
	 * @param  string $orderId
	 * @param  string $returnStore
	 * @param  mixed $orderItems
	 * @param  string $comment
	 * @param  int $reason
	 * @return mixed
	 */
	public function sendReturn($orderId, $returnStore, $orderItems, $comment, $reason);

	/**
	 * From Oms initiate update return to magento
	 * @param  string $returnId
	 * @param  string $returnStore
	 * @param  int $status
	 * @param  int $action
	 * @param  mixed $orderItems
	 * @return mixed
	 */
	public function returnInitiate($returnId, $returnStore, $status, $action, $orderItems);

	/**
	 * Updated return from in-progress to approved or rejected
	 * @param  string $returnId
	 * @param  int $status
	 * @param  int $action
	 * @param  int $subAction
	 * @param  mixed $orderItems
	 * @return mixed
	 */
	public function returnProgress($returnId, $status, $action, $subAction, $orderItems);

	/**
	 * Cancel Return From OMS
	 * @param  string $returnId
	 * @param  int $status
	 * @param  int $action
	 * @param  int $subAction
	 * @return mixed
	 */
	public function returnCancel($returnId, $status, $action, $subAction);
}
