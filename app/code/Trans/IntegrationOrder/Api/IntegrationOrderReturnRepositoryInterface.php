<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

use Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterface;

interface IntegrationOrderReturnRepositoryInterface {

	/**
	 * Save data.
	 *
	 * @param IntegrationOrderReturnInterface $integrationOrderReturnInterface
	 * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationOrderReturnInterface $integrationOrderReturnInterface);

	/**
	 * load data by sku and order id
	 * @param string $sku
	 * @param string $orderId
	 * @return array $resul
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataReturnBySku($sku, $orderId);

	/**
	 * load data by order id
	 * @param string $orderId
	 * @return array $resul
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataReturnByOrderid($orderId);

	/**
	 * load item by order id
	 * @param string $orderId
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadItemByOrderId($orderId);

	/**
	 * load attribute by attribute_code
	 * @param string $attributeCode
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeByCode($attributeCode);

	/**
	 * load rma item by rma_entity_id
	 * @param string $rmaEntityId
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadRmaItemByEntityId($rmaEntityId);

	/**
	 * load rma by order_increment_id
	 * @param string $rmaEntityId
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadRmaByOrderId($orderId);
}
