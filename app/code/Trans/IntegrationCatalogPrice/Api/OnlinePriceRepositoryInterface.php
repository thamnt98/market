<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Api;

use Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface;

interface OnlinePriceRepositoryInterface {

	/**
	 * Retrieve data by id
	 *
	 * @param int $id
	 * @return \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id);

	/**
	 * Save Data
	 *
	 * @param \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface $data
	 * @return \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */

	public function save(OnlinePriceInterface $data);

	/**
	 * Delete data.
	 *
	 * @param \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface $data
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(OnlinePriceInterface $data);

	/**
	 * Load Integration Product by sku.
	 *
	 * @param mixed $sku
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataBySku($sku);

}