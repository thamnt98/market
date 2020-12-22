<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Api;

interface IntegrationStockInterface {
	
	//For Job Status Default Set
	const STATUS_JOB = 1;
	const MSG_DATA_STOCK_NULL = 'Error Saving Stock, There is an Attribute Field that has an empty value.';
	const MSG_NEW_STORE       = 'Add a new store, because the store does not exist.'; 
	const MSG_NO_STORE		  = 'Store does not exist.';
	/**
	 * Constant for field name from table data
	 */
	const IMS_PRODUCT_SKU    = 'product_sku';
	const IMS_LOCATION_CODE  = 'location_code';
	const IMS_QUANTITY 	     = 'quantity';
	const IMS_STATUS         = 1;

	const MAX_TRY_HIT		 = 3;

	/**
	 * Save data
	 *
	 * @param mixed $data
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveStock($datas);

	/**
	 * Prepare Data
	 *
	 * @param mixed $channel
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareData($channel);

	/**
     * Add New Store
     * @param string $locationCode
     * @return mixed
     */
	public function addNewSource($locationCode);

	/**
	 * @param mixed $channel
	 * @return array
	 */
	public function prepareStockDataUsingRawQuery($channel);

    /**
     * @param array $channel
     * @param array $data
     * @return int
     */
	public function insertStockDataUsingRawQuery($channel, $data);
	
}