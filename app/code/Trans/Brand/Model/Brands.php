<?php
/**
 * @category Trans
 * @package  Trans_Brand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Brand\Model;

use \Trans\Brand\Api\Data\BrandInterface;
use \Trans\Brand\Model\ResourceModel\Brand as ResourceModel;

class Brands extends \Magento\Framework\Model\AbstractModel implements BrandInterface {

	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->getData(BrandInterface::BRAND_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setId($setId) {
		return $this->setData(BrandInterface::BRAND_ID, $setId);
	}

	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return $this->getData(BrandInterface::TITLE);
	}

	/**
	 * @inheritdoc
	 */
	public function setTitle($title) {
		return $this->setData(BrandInterface::TITLE, $title);
	}

	/**
	 * @inheritdoc
	 */
	public function getDescription() {
		return $this->getData(BrandInterface::DESCRIPTION);
	}

	/**
	 * @inheritdoc
	 */
	public function setDescription($description) {
		return $this->setData(BrandInterface::DESCRIPTION, $description);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetaTitle() {
		return $this->getData(BrandInterface::META_TITLE);
	}

	/**
	 * @inheritdoc
	 */
	public function setMetaTitle($metaTitle) {
		return $this->setData(BrandInterface::META_TITLE, $metaTitle);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetaKeywords() {
		return $this->getData(BrandInterface::META_KEYWORDS);
	}

	/**
	 * @inheritdoc
	 */
	public function setMetaKeywords($metaKeywords) {
		return $this->setData(BrandInterface::META_KEYWORDS, $metaKeywords);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetaDescription() {
		return $this->getData(BrandInterface::META_DESCRIPTION);
	}

	/**
	 * @inheritdoc
	 */
	public function setMetaDescription($metaDescription) {
		return $this->setData(BrandInterface::META_DESCRIPTION, $metaDescription);
	}

	/**
	 * @inheritdoc
	 */
	public function getUrlKey() {
		return $this->getData(BrandInterface::URL_KEY);
	}

	/**
	 * @inheritdoc
	 */
	public function setUrlKey($urlKey) {
		return $this->setData(BrandInterface::URL_KEY, $urlKey);
	}

	/**
	 * @inheritdoc
	 */
	public function getPosition() {
		return $this->getData(BrandInterface::POSITION);
	}

	/**
	 * @inheritdoc
	 */
	public function setPosition($position) {
		return $this->setData(BrandInterface::POSITION, $position);
	}

	/**
	 * @inheritdoc
	 */
	public function getImage() {
		return $this->getData(BrandInterface::IMAGE);
	}

	/**
	 * @inheritdoc
	 */
	public function setImage($image) {
		return $this->setData(BrandInterface::IMAGE, $image);
	}

	/**
	 * @inheritdoc
	 */
	public function getBannerImage() {
		return $this->getData(BrandInterface::BANNER_IMAGE);
	}

	/**
	 * @inheritdoc
	 */
	public function setBannerImage($bannerImage) {
		return $this->setData(BrandInterface::BANNER_IMAGE, $bannerImage);
	}

	/**
	 * @inheritdoc
	 */
	public function getStatus() {
		return $this->getData(BrandInterface::STATUS);
	}

	/**
	 * @inheritdoc
	 */
	public function setStatus($status) {
		return $this->setData(BrandInterface::STATUS, $status);
	}

	/**
	 * @inheritdoc
	 */
	public function setIsActive($isActive) {
		return $this->setData(BrandInterface::IS_ACTIVE, $isActive);
	}
}