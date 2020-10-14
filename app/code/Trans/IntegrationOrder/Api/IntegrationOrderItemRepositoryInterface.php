<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

use Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface;

interface IntegrationOrderItemRepositoryInterface {
	/**
	 * Save data.
	 *
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface $data
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationOrderItemInterface $data);

	/**
	 * Retrieve data by id
	 *
	 * @param int $dataId
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($dataId);

	/**
	 * Retrieve data by order id
	 *
	 * @param string $orderId
	 * @return IntegrationOrderItemInterface
	 */
	public function getByOrderId($orderId);
}