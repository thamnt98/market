<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 * 
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Api\Data;

interface OnlinePriceInterface {

	/**
	 * Constant for table name
	 */
	const DEFAULT_EVENT = 'trans_integration';
	const TABLE_NAME    = 'integration_catalog_online_price';

	/**
	 * Constant for field name
	 */
	const ID                   = 'id';
	const SKU                  = 'sku';
	const ONLINE_SELLING_PRICE = 'online_price';
	const CREATED_AT           = 'created_at';
    const UPDATED_AT           = 'updated_at';
    const IS_EXCLUSIVE         = 'is_exclusive';
    const MODIFIED_AT          = 'modified_at';
    const START_DATE           = 'start_date';
    const END_DATE             = 'end_date';
    const STAGING_ID           = 'staging_id';

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

	/**
	 * Get Is Exclusive
	 *
	 * @return string
	 */
	public function getIsExclusive();

	/**
	 * Set Is Exclusive
	 *
	 * @param string $isExclusive
	 * @return void
	 */
	public function setIsExclusive($isExclusive);

	/**
	 * Get Modified At
	 *
	 * @return string
	 */
	public function getModifiedAt();

	/**
	 * Set Modified At
	 *
	 * @param string $modifiedAt
	 * @return void
	 */
	public function setModifiedAt($modifiedAt);

	/**
	 * Get start date
	 *
	 * @return string
	 */
	public function getStartDate();

	/**
	 * Set start date
	 *
	 * @param string $startDate
	 * @return void
	 */
	public function setStartDate($startDate);

	/**
	 * Get end date
	 *
	 * @return string
	 */
	public function getEndDate();

	/**
	 * Set end date
	 *
	 * @param string $endDate
	 * @return void
	 */
	public function setEndDate($endDate);

	/**
	 * Get staging ID
	 *
	 * @return string
	 */
	public function getStagingId();

	/**
	 * Set staging ID
	 *
	 * @param string $stagingId
	 * @return void
	 */
	public function setStagingId($stagingId);
}