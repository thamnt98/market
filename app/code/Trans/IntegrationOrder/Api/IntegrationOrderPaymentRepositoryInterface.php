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

use Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface;

interface IntegrationOrderPaymentRepositoryInterface {

	/**
	 * Save data.
	 *
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface $data
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationOrderPaymentInterface $data);

	/**
	 * Retrieve data by id
	 *
	 * @param int $dataId
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($dataId);

	/**
	 * Retrieve data by reference number
	 *
	 * @param string $refNumber
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataByReferenceNumber($refNumber);
}
