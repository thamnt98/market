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

namespace Trans\IntegrationCatalogPrice\Api;

interface StorePriceLogicInterface 
{
	//For Job Status Default Set
	const STATUS_JOB = 1;
	const MSG_DATA_ATTRIBUTE_NULL  = 'Error Save Attribute, Any Attribute Field Null';
	const MSG_ATTRIBUTE_CODE_EXIST = 'Error Save Attribute, Attribute Already Exist';	
	const MSG_ATTRIBUTE_DELETED    = 'Deleted Attribute';
	const MSG_ATTRIBUTE_UPDATE     = 'Update Attribute';
	/**
	 * Constant for field name from table data
	 */
	const PIM_ASSIGNED_USER_ID = 'assigned_user_id';
	const PIM_ATTRIBUTE_TYPE   = 'attribute_type';
	const PIM_CREATED_AT	   = 'created_at';
	const PIM_CREATED_BY_ID    = 'created_by_id';
	const PIM_CODE			   = 'code';
	const PIM_DELETED		   = 'deleted';
	const PIM_ID               = 'id';
	const PIM_LABEL			   = 'label';
	const PIM_MODIFIED_AT      = 'modified_at';
	const PIM_MODIFIED_BY_ID   = 'modified_by_id';
	const PIM_OPTION		   = 'option';
	const PIM_OWNER_USER_ID	   = 'owner_user_id';
	const PIM_DEVAULT_VALUE	   = 'default_value';
	
	const IS_HTML_ALLOWED_ON_FRONT = true;
	
	
	
	const POSITION				   = 0;
	const APPLY_TO				   = array("simple", "virtual", "configurable");
	const IS_VISIBLE 			   = true;
	const SCOPE					   = 'global';
	const ENTITY_TYPE_ID		   = 4;
	const IS_REQUIRED 			   = false;
	const IS_USER_DEFINED 		   = true;
	const IS_UNIQUE				   = 0;

	const ATTRIBUTE_SET_ID		   = 4;
	const ATTRIBUTE_GROUP_ID	   = 19;
	const SORT_ORDER			   = 100;

	const INPUT_TYPE_BACKEND_FORMAT_PRICE = "decimal";
	const INPUT_TYPE_FRONTEND_FORMAT_PRICE = "price";

	const PRODUCT_ATTR_BASE_PRICE 	= "base_price_";
	const PRODUCT_ATTR_PROMO_PRICE  = "promo_price_";

	/**
	 * Attribute type Code
	 */
	const ENTITY_TYPE_CODE = 'catalog_product';
	const STORE_ATTR_CODE_COUNT_CHAR = 7;

	/**
	 * New Update Configuration Attribute FOR Multi Price Only
	 */
	const IS_SEARCHBLE						= true;
	const IS_FILTERABLE 					= false;
	const IS_COMPARABLE						= false;
	const IS_VISIBLE_ON_FRONT 				= false;
	const IS_FILTERABLE_IN_SEARCH			= false;
	const USED_IN_PRODUCT_LISTING			= 0;
	const USED_FOR_SORT_BY 					= 0;
	const IS_VISIBLE_IN_ADVANCED_SEARCH		= 1;
	const IS_WYSIWYG_ENABLED				= false;
	const IS_USED_FOR_PROMO_RULES			= false;
	const IS_USED_IN_GRID 		   			= false;
	const IS_VISIBLE_IN_GRID 	   			= false;
	const IS_FILTERABLE_IN_GRID    			= false;
	const IS_USED_FOR_PRICE_RULES 			= false;

	/**
	 * Prepare Data
	 *
	 * @param mixed $jobs
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareData($jobs);

	/**
	 * Save store Price
	 * @param mixed $jobs
	 * @param mixed $data Product Remap
	 * @return \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */	
	public function remapData($jobs,$data);
	
	/**
	 * Save store Price
	 * @param mixed $jobs
	 * @param mixed $data
	 * @return \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save($jobs,$data);

	

}