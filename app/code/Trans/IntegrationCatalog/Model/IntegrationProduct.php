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

namespace Trans\IntegrationCatalog\Model;

use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct as ResourceModel;

class IntegrationProduct extends \Magento\Framework\Model\AbstractModel implements IntegrationProductInterface {

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->_getData(IntegrationProductInterface::ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setId($id) {
		$this->setData(IntegrationProductInterface::ID, $id);
	}

	/**
	 * @inheritdoc
	 */
	public function getMagentoEntityId() {
		return $this->_getData(IntegrationProductInterface::MAGENTO_ENTITY_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setMagentoEntityId($entityId) {
		$this->setData(IntegrationProductInterface::MAGENTO_ENTITY_ID, $entityId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimId() {
		return $this->_getData(IntegrationProductInterface::PIM_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimId($pimId) {
		$this->setData(IntegrationProductInterface::PIM_ID, $pimId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimSku() {
		return $this->_getData(IntegrationProductInterface::PIM_SKU);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimSku($sku) {
		$this->setData(IntegrationProductInterface::PIM_SKU, $sku);
	}

	/**
	 * @inheritdoc
	 */
	public function getIntegrationDataId() {
		return $this->_getData(IntegrationProductInterface::INTEGRATION_DATA_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setIntegrationDataId($integrationDataId) {
		$this->setData(IntegrationProductInterface::INTEGRATION_DATA_ID, $integrationDataId);
	}

	/**
	 * @inheritdoc
	 */
	public function getItemId() {
		return $this->_getData(IntegrationProductInterface::ITEM_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setItemId($itemId) {
		$this->setData(IntegrationProductInterface::ITEM_ID, $itemId);
	}

	/**
	 * @inheritdoc
	 */
	public function getMagentoParentId() {
		return $this->_getData(IntegrationProductInterface::MAGENTO_PARENT_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setMagentoParentId($magentoParentId) {
		$this->setData(IntegrationProductInterface::MAGENTO_PARENT_ID, $magentoParentId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCategoryId() {
		return $this->_getData(IntegrationProductInterface::PIM_CATEGORY_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCategoryId($catId) {
		$this->setData(IntegrationProductInterface::PIM_CATEGORY_ID, $catId);
	}

	/**
	 * @inheritdoc
	 */
	public function getMagentoCategoryIds() {
		return $this->_getData(IntegrationProductInterface::MAGENTO_CATEGORY_IDS);
	}

	/**
	 * @inheritdoc
	 */
	public function setMagentoCategoryIds($catId) {
		$this->setData(IntegrationProductInterface::MAGENTO_CATEGORY_IDS, $catId);
	}

	/**
	 * @inheritdoc
	 */
	public function getStatusConfigurable() {
		return $this->_getData(IntegrationProductInterface::STATUS_CONFIGURABLE);
	}

	/**
	 * @inheritdoc
	 */
	public function setStatusConfigurable($status) {
		$this->setData(IntegrationProductInterface::STATUS_CONFIGURABLE, $status);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductType() {
		return $this->_getData(IntegrationProductInterface::PRODUCT_TYPE);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductType($typeId) {
		$this->setData(IntegrationProductInterface::PRODUCT_TYPE, $typeId);
	}

	/**
	 * @inheritdoc
	 */
	public function getAttributeList() {
		return $this->_getData(IntegrationProductInterface::ATTRIBUTE_LIST);
	}

	/**
	 * @inheritdoc
	 */
	public function setAttributeList($json) {
		$this->setData(IntegrationProductInterface::ATTRIBUTE_LIST, $json);
	}	
}
