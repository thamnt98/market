<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

use Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface;

interface IntegrationOrderStatusRepositoryInterface {

	/**
	 * Save data.
	 *
	 * @param IntegrationOrderStatusInterface $integrationOrderStatusInterface
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationOrderStatusInterface $integrationOrderStatusInterface);

	/**
	 * Save History data.
	 *
	 * @param IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveHistory(IntegrationOrderHistoryInterface $integrationOrderHistoryInterface);

	/**
	 * Save Item data.
	 *
	 * @param IntegrationOrderItemInterface $integrationOrderItemInterface
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveItem(IntegrationOrderItemInterface $integrationOrderItemInterface);

	/**
	 * Retrieve data by id
	 *
	 * @param int $statusId
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($statusId);

	/**
	 * Load Status by Status and Action
	 * @param string $status
	 * @param string $action
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface
	 */
	public function loadByIdNonSubAction($status, $action);

	/**
	 * Load Status by Status, Action and Sub Action
	 * @param string $status
	 * @param string $action
	 * @param string $subAction
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface
	 */
	public function loadByIdSubAction($status, $action, $subAction);

	/**
	 * Load Status by Air Way Bill
	 * @param string $awb
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface
	 */
	public function loadByAWB($awb);

	/**
	 * Able to Save Order Item to Order Item Table by Order Id
	 * @param string $orderId
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface
	 */
	public function loadByOrderId($orderId);

	/**
	 * Able to Save Order to History Table by Order Id
	 * @param string $orderIds
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface
	 */
	public function loadByOrderIds($orderIds);

	/**
	 * Able to Get Data From Sales Order by Increment Id
	 * @param string $orderId
	 * @return string
	 */
	public function loadDataByRefOrderId($orderId);

	/**
	 * Able to Get Data From Sales Order Status History by Parent Order Id
	 * @param string $parentId
	 * @return string
	 */
	public function loadDataByParentOrderId($parentId);

	/**
	 * Able to Get Data OMS Status Table by Status Number
	 * @param string $feStatusNo
	 * @return string
	 */
	public function loadDataByFeStatusNo($feStatusNo);

	/**
	 * Able to Get Data Items From Sales Order Item Table
	 * @param string $orderId
	 * @return string
	 */
	public function loadItemByOrderIds($orderId);
}
