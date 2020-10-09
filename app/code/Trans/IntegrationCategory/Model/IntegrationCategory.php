<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCategory
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCategory\Model;

use \Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface;
use \Trans\IntegrationCategory\Model\ResourceModel\IntegrationCategory as ResourceModel;

class IntegrationCategory extends \Magento\Framework\Model\AbstractModel implements
\Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface {

	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->_getData(IntegrationCategoryInterface::ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setId($id) {
		$this->setData(IntegrationCategoryInterface::ID, $id);
	}

	/**
	 * @inheritdoc
	 */
	public function getMagentoEntityId() {
		return $this->_getData(IntegrationCategoryInterface::MAGENTO_ENTITY_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setMagentoEntityId($entityId) {
		$this->setData(IntegrationCategoryInterface::MAGENTO_ENTITY_ID, $entityId);
	}

	/**
	 * @inheritdoc
	 */
	public function getMagentoParentId() {
		return $this->_getData(IntegrationCategoryInterface::MAGENTO_PARENT_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setMagentoParentId($parentId) {
		$this->setData(IntegrationCategoryInterface::MAGENTO_PARENT_ID, $parentId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimAssignedUserId() {
		return $this->_getData(IntegrationCategoryInterface::PIM_ASSIGNED_USER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimAssignedUserId($assignedUserId) {
		$this->setData(IntegrationCategoryInterface::PIM_ASSIGNED_USER_ID, $assignedUserId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCategoryParentId() {
		return $this->_getData(IntegrationCategoryInterface::PIM_CATEGORY_PARENT_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCategoryParentId($categoryParentId) {
		$this->setData(IntegrationCategoryInterface::PIM_CATEGORY_PARENT_ID, $categoryParentId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCategoryRoute() {
		return $this->_getData(IntegrationCategoryInterface::PIM_CATEGORY_ROUTE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCategoryRoute($categoryRoute) {
		$this->setData(IntegrationCategoryInterface::PIM_CATEGORY_ROUTE, $categoryRoute);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCategoryRouteName() {
		return $this->_getData(IntegrationCategoryInterface::PIM_CATEGORY_ROUTE_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCategoryRouteName($categoryRouteName) {
		$this->setData(IntegrationCategoryInterface::PIM_CATEGORY_ROUTE_NAME, $categoryRouteName);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCode() {
		return $this->_getData(IntegrationCategoryInterface::PIM_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCode($code) {
		$this->setData(IntegrationCategoryInterface::PIM_CODE, $code);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCreatedAt() {
		return $this->_getData(IntegrationCategoryInterface::PIM_CREATED_AT);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCreatedAt($createdAt) {
		$this->setData(IntegrationCategoryInterface::PIM_CREATED_AT, $createdAt);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCreatedById() {
		return $this->_getData(IntegrationCategoryInterface::PIM_CREATED_BY_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCreatedById($createdById) {
		$this->setData(IntegrationCategoryInterface::PIM_CREATED_BY_ID, $createdById);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimDeleted() {
		return $this->_getData(IntegrationCategoryInterface::PIM_DELETED);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimDeleted($deleted) {
		$this->setData(IntegrationCategoryInterface::PIM_DELETED, $deleted);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimDescription() {
		return $this->_getData(IntegrationCategoryInterface::PIM_DESCRIPTION);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimDescription($description) {
		$this->setData(IntegrationCategoryInterface::PIM_DESCRIPTION, $description);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimDescriptionEnUs() {
		return $this->_getData(IntegrationCategoryInterface::PIM_DESCRIPTION_EN_US);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimDescriptionEnUs($descriptionEnUs) {
		$this->setData(IntegrationCategoryInterface::PIM_DESCRIPTION_EN_US, $descriptionEnUs);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimDescriptionIdId() {
		return $this->_getData(IntegrationCategoryInterface::PIM_DESCRIPTION_ID_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimDescriptionIdId($descriptionIdId) {
		$this->setData(IntegrationCategoryInterface::PIM_DESCRIPTION_ID_ID, $descriptionIdId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimId() {
		return $this->_getData(IntegrationCategoryInterface::PIM_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimId($pimId) {
		$this->setData(IntegrationCategoryInterface::PIM_ID, $pimId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimImageName() {
		return $this->_getData(IntegrationCategoryInterface::PIM_IMAGE_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimImageName($imageName) {
		$this->setData(IntegrationCategoryInterface::PIM_IMAGE_NAME, $imageName);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimIsActive() {
		return $this->_getData(IntegrationCategoryInterface::PIM_IS_ACTIVE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimIsActive($isActive) {
		$this->setData(IntegrationCategoryInterface::PIM_IS_ACTIVE, $isActive);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimModifiedAt() {
		return $this->_getData(IntegrationCategoryInterface::PIM_MODIFIED_AT);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimModifiedAt($modifiedAt) {
		$this->setData(IntegrationCategoryInterface::PIM_MODIFIED_AT, $modifiedAt);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimModifiedById() {
		return $this->_getData(IntegrationCategoryInterface::PIM_MODIFIED_BY_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimModifiedById($modifiedById) {
		$this->setData(IntegrationCategoryInterface::PIM_MODIFIED_BY_ID, $modifiedById);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimName() {
		return $this->_getData(IntegrationCategoryInterface::PIM_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimName($name) {
		$this->setData(IntegrationCategoryInterface::PIM_NAME, $name);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimNameEnUs() {
		return $this->_getData(IntegrationCategoryInterface::PIM_NAME_EN_US);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimNameEnUs($nameEnUs) {
		$this->setData(IntegrationCategoryInterface::PIM_NAME_EN_US, $nameEnUs);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimNameIdId() {
		return $this->_getData(IntegrationCategoryInterface::PIM_NAME_ID_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimNameIdId($nameIdId) {
		$this->setData(IntegrationCategoryInterface::PIM_NAME_ID_ID, $nameIdId);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimOwnerUserId() {
		return $this->_getData(IntegrationCategoryInterface::PIM_OWNER_USER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimOwnerUserId($ownerUserId) {
		$this->setData(IntegrationCategoryInterface::PIM_OWNER_USER_ID, $ownerUserId);
	}

	/**
	 * @inheritdoc
	 */
	public function getCreatedAt() {
		return $this->_getData(IntegrationCategoryInterface::CREATED_AT);
	}

	/**
	 * @inheritdoc
	 */
	public function setCreatedAt($createdAt) {
		$this->setData(IntegrationCategoryInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @inheritdoc
	 */
	public function getUpdatedAt() {
		return $this->_getData(IntegrationCategoryInterface::UPDATED_AT);
	}

	/**
	 * @inheritdoc
	 */
	public function setUpdatedAt($updatedAt) {
		$this->setData(IntegrationCategoryInterface::UPDATED_AT, $updatedAt);
	}

	/**
	 * @inheritdoc
	 */
	public function getStatus() {
		return $this->_getData(IntegrationCategoryInterface::STATUS);
	}

	/**
	 * @inheritdoc
	 */
	public function setStatus($status) {
		$this->setData(IntegrationCategoryInterface::STATUS, $status);
	}

}