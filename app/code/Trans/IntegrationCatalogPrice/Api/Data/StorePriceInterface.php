<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 * @modify	 J.P <jaka.pondan@ctcorpdigital.com>
 * 
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Api\Data;

interface StorePriceInterface {

	/**
	 * Constant for table name
	 */
	const DEFAULT_EVENT = 'trans_integration';
	const TABLE_NAME    = 'integration_catalog_store_price';

	/**
	 * Constant for field name
	 */
	const ID                   = 'id';
	const SOURCE_CODE          = 'store_code';
	const STORE_ATTR_CODE      = 'store_attr_code';
	const SKU                  = 'sku';
	const NORMAL_SELLING_PRICE = 'normal_selling_price';
	const PROMO_SELLING_PRICE  = 'promo_selling_price';
	// const SOURCE_CODE          = 'source_code';
	// const PRICE                = 'price';
	const DROP_ONLINE_SELLING_PRICE = 'online_selling_price';

	const STATUS               = 'status';
	const DELETED              = 'is_deleted';
	const STATUS_JOB_DATA	   = 1;

	//Update Column #1

	// Response Param
	const ONLINE_SELLING_PRICE = 'online_price';
	const NORMAL_PURCHASE_PRICE = "normal_purchase_price";
	const PROMO_PURCHASE_PRICE = "promo_purchase_price";

	const CODE = "code";
	const COMPANY_CODE = "company_code";
	
	const PRODUCT_ID = "product_id";
	const PIM_ID = "pim_id";
	const PIM_CODE = "pim_code";
	const PIM_COMPANY_CODE = "pim_company_code";
	const PIM_PRODUCT_ID = "pim_product_id";
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

	/**
	 * get id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Set Id
	 *
	 * @param int $id
	 * @return void
	 */
	public function setId($id);

	/**
	 * Get Store Code
	 *
	 * @return string
	 */
	public function getSourceCode();

	/**
	 * Set Store Code
	 *
	 * @param string $sourceCode
	 * @return void
	 */
	public function setSourceCode($sourceCode);

	/**
	 * Get Sku
	 *
	 * @return string
	 */
	public function getSku();

	/**
	 * Set Sku
	 *
	 * @param string $sku
	 * @return void
	 */
	public function setSku($sku);


	/**
	 * Get Status
	 *
	 * @return int
	 */
	public function getStatus();

	/**
	 * Set Status
	 *
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status);

	/**
	 * Get Normal Selling Price
	 *
	 * @return float
	 */
	public function getNormalSellingPrice();

	/**
	 * Set Normal Selling Price
	 *
	 * @param float $price
	 * @return void
	 */
	public function setNormalSellingPrice($price);

	/**
	 * Get Promo Selling Price
	 *
	 * @return float
	 */
	public function getPromoSellingPrice();

	/**
	 * Set Promo Selling Price
	 *
	 * @param float $price
	 * @return void
	 */
	public function setPromoSellingPrice($price);

	/**
	 * Get Online Price
	 *
	 * @return float
	 */
	public function getOnlinePrice();

	/**
	 * Set Online Price
	 *
	 * @param float $price
	 * @return void
	 */
	public function setOnlinePrice($price);


	/**
	 * Get Normal Purchase Price
	 *
	 * @return float
	 */
	public function getNormalPurchasePrice();

	/**
	 * Set Normal Purchase Price
	 *
	 * @param float $price
	 * @return void
	 */
	public function setNormalPurchasePrice($price);

	/**
	 * Get Promo Purchase Price
	 *
	 * @return float
	 */
	public function getPromoPurchasePrice();

	/**
	 * Set Promo Purchase Price
	 *
	 * @param float $price
	 * @return void
	 */
	public function setPromoPurchasePrice($price);

	/**
	 * Get PIM CODE
	 *
	 * @return string
	 */
	public function getPimCode();

	/**
	 * Set PIM CODE
	 *
	 * @param string $code
	 * @return void
	 */
	public function setPimCode($code);

	/**
	 * Get PIM Company CODE
	 *
	 * @return string
	 */
	public function getPimCompanyCode();

	/**
	 * Set PIM Company CODE
	 *
	 * @param string $code
	 * @return void
	 */
	public function setPimCompanyCode($code);

	/**
	 * Get PIM Product Id
	 *
	 * @return string
	 */
	public function getPimProductId();

	/**
	 * Set PIM Product Id
	 *
	 * @param string $id
	 * @return void
	 */
	public function setPimProductId($id);

	/**
	 * Get created at
	 *
	 * @return string
	 */
	public function getCreatedAt();

	/**
	 * Set created at
	 *
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt);

	/**
	 * Get updated at
	 *
	 * @return string
	 */
	public function getUpdatedAt();

	/**
	 * Set updated at
	 *
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);


}