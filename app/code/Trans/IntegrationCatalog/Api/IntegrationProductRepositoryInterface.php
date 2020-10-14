<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;

interface IntegrationProductRepositoryInterface {

	/**
	 * Retrieve data by id
	 *
	 * @param int $id
	 * @return \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id);

	/**
	 * Retrieve data by entity id
	 *
	 * @param int $entityId
	 * @return \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByEntityId($entityId);

	/**
	 * Save Data
	 *
	 * @param \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface $data
	 * @return \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationProductInterface $data);

	/**
	 * Delete data.
	 *
	 * @param \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface $data
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(IntegrationProductInterface $data);

	/**
	 * Load Integration Product by Product Parent Id.
	 *
	 * @param mixed $ProductParentId
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */

	public function loadDataByProductParentId($productParentId);

	/**
	 * Load Integration Product by PIM Id.
	 *
	 * @param mixed $pimId
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataByPimId($pimId);

	/**
	 * Load Integration Product by ItemId.
	 *
	 * @param mixed $itemId
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataByItemId($itemId);

	/**
	 * Load Integration Product by sku.
	 *
	 * @param mixed $sku
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataByPimSku($sku);

	/**
	 * Load Integration Product Configurable by Multi Status Configurable.
	 * @param array $status
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadSkuConfigurableByMultiStatus($status=[]);


	

	

}