<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 * @modify	 J.P <jaka.pondan@ctcorpdigital.com>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model;

use \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface;
use \Trans\IntegrationCatalogPrice\Model\ResourceModel\StorePrice as ResourceModel;

class StorePrice extends \Magento\Framework\Model\AbstractModel implements
StorePriceInterface {

	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->_getData(StorePriceInterface::ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setId($id) {
		$this->setData(StorePriceInterface::ID, $id);
	}

	/**
	 * @inheritdoc
	 */
	public function getSourceCode() {
		return $this->_getData(StorePriceInterface::SOURCE_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setSourceCode($sourceCode) {
		$this->setData(StorePriceInterface::SOURCE_CODE, $sourceCode);
	}

	/**
	 * @inheritdoc
	 */
	public function getSku() {
		return $this->_getData(StorePriceInterface::SKU);
	}

	/**
	 * @inheritdoc
	 */
	public function setSku($sku) {
		$this->setData(StorePriceInterface::SKU, $sku);
	}
	
	/**
	 * @inheritdoc
	 */
	public function getStatus() {
		return $this->_getData(StorePriceInterface::STATUS);
	}

	/**
	 * @inheritdoc
	 */
	public function setStatus($status) {
		$this->setData(StorePriceInterface::STATUS, $status);
	}

	/**
	 * @inheritdoc
	 */
	public function getNormalSellingPrice() {
		return $this->_getData(StorePriceInterface::NORMAL_SELLING_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setNormalSellingPrice($price) {
		$this->setData(StorePriceInterface::NORMAL_SELLING_PRICE, $price);
	}

	/**
	 * @inheritdoc
	 */
	public function getPromoSellingPrice() {
		return $this->_getData(StorePriceInterface::PROMO_SELLING_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPromoSellingPrice($price) {
		$this->setData(StorePriceInterface::PROMO_SELLING_PRICE, $price);
	}

	/**
	 * @inheritdoc
	 */
	public function getOnlinePrice() {
		return $this->_getData(StorePriceInterface::ONLINE_SELLING_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setOnlinePrice($price) {
		$this->setData(StorePriceInterface::ONLINE_SELLING_PRICE, $price);
	}

	/**
	 * @inheritdoc
	 */
	public function getNormalPurchasePrice() {
		return $this->_getData(StorePriceInterface::NORMAL_PURCHASE_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setNormalPurchasePrice($price) {
		$this->setData(StorePriceInterface::NORMAL_PURCHASE_PRICE, $price);
	}

	/**
	 * @inheritdoc
	 */
	public function getPromoPurchasePrice() {
		return $this->_getData(StorePriceInterface::PROMO_PURCHASE_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPromoPurchasePrice($price) {
		$this->setData(StorePriceInterface::PROMO_PURCHASE_PRICE, $price);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCode() {
		return $this->_getData(StorePriceInterface::PIM_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCode($code) {
		$this->setData(StorePriceInterface::PIM_CODE, $code);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimCompanyCode() {
		return $this->_getData(StorePriceInterface::PIM_COMPANY_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimCompanyCode($code) {
		$this->setData(StorePriceInterface::PIM_COMPANY_CODE, $code);
	}

	/**
	 * @inheritdoc
	 */
	public function getPimProductId() {
		return $this->_getData(StorePriceInterface::PIM_PRODUCT_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPimProductId($id) {
		$this->setData(StorePriceInterface::PIM_PRODUCT_ID, $id);
	}

	/**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(StorePriceInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(StorePriceInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(StorePriceInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(StorePriceInterface::UPDATED_AT, $updatedAt);
    }
}