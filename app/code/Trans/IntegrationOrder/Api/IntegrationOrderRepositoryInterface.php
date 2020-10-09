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

namespace Trans\IntegrationOrder\Api;

use Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;

interface IntegrationOrderRepositoryInterface {

	/**
	 * Save data.
	 *
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface $data
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationOrderInterface $data);

	/**
	 * Retrieve data by id
	 *
	 * @param int $dataId
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($dataId);

	/**
	 * Load Data By Order Id
	 *
	 * @param string $orderId
	 * @return string
	 */
	public function loadDataByOrderId($orderId);

}