<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Api;

interface IntegrationStoreInterface {
	
	//For Job Status Default Set
	const STATUS_JOB = 1;
	const MSG_DATA_STORE_ANY_NULL = 'Error Save Store, Location Code or Name Field Null';
	
	/**
	 * Constant for field name from table data
	 */
	const IMS_LOCATION_ID   			 = 'location_id';
	const IMS_LOCATION_CODE 			 = 'location_code';
	const IMS_NAME 	        			 = 'name';
	const IMS_ENABLED      			     = 'enabled';
	const IMS_DESCRIPTION  			     = 'description';		
	const IMS_LATITUDE    			     = 'latitude';
	const IMS_LONGTITUDE  			     = 'longitude';	
	const IMS_COUNTRY_ID  			     = 'country_id';	
	const IMS_PROVINCE_ID 			     = 'province_id';
	const IMS_PROVINCE   	  		     = 'province';
	const IMS_CITY_ID     			     = 'city_id';	
	const IMS_CITY      			     = 'city';	
	const IMS_DISTRICT_ID			     = 'district_id';
	const IMS_DISTRICT   			     = 'district';	
	const IMS_ADDRESS   			     = 'address';
	const IMS_ZIPCODE     			     = 'zipcode';
	const IMS_CONTACT_NAME 			     = 'contact_name';
	const IMS_EMAIL   			         = 'email';
	const IMS_PHONE     			     = 'phone';
	const IMS_FAX        			     = 'fax';
	const IMS_USE_DEFAULT_CARRIER_CONFIG = 'use_default_carrier';	
	const IMS_HOUR_OPEN    			     = 'hour_open';
	const IMS_HOUR_CLOSE 			     = 'hour_close';

	/**
	 * Save data
	 *
	 * @param mixed $data
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveStore($datas);

	/**
	 * Prepare Data
	 *
	 * @param mixed $channel
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareData($channel);
}