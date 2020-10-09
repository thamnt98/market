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

interface IntegrationOrderHistoryRepositoryInterface {
	/**
	 * Save data.
	 *
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationOrderHistoryInterface $integrationOrderHistoryInterface);

	/**
	 * Delete data.
	 *
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(IntegrationOrderHistoryInterface $integrationOrderHistoryInterface);
}
