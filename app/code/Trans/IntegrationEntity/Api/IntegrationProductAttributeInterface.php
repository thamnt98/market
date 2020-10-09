<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 * @modify   J.P <jaka.pondan@transdigital.co.id>
 * @modify   HaDi <ashadi.sejati@transdigital.co.id>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Api;

interface IntegrationProductAttributeInterface {
	
	//For Job Status Default Set
	const STATUS_JOB = 1;
	const MSG_DATA_ATTRIBUTE_NULL  = 'Error Save Attribute, Any Attribute Field Null';
	const MSG_ATTRIBUTE_CODE_EXIST = 'Error Save Attribute, Attribute Already Exist';	
	const MSG_ATTRIBUTE_DELETED    = 'Deleted Attribute';
	const MSG_ATTRIBUTE_UPDATE     = 'Update Attribute';
	const MSG_ATTRIBUTE_NEW     	= 'New Attribute';
	const MSG_ATTRIBUTE_SET    	= 'New Attribute set';
	const MSG_ATTRIBUTE_SET_NULL = 'Null Attribute set';
	/**
	 * Constant for field name from table data
	 */
	const PIM_ASSIGNED_USER_ID = 'assigned_user_id';
	const PIM_ATTRIBUTE_TYPE   = 'attribute_type_id';
	const PIM_CREATED_AT	   = 'created_at';
	const PIM_CREATED_BY_ID    = 'created_by_id';
	const PIM_CODE			   = 'code';
	const PIM_DELETED		   = 'deleted';
	const PIM_ID               = 'id';
	const PIM_LABEL			   = 'name';
	const PIM_MODIFIED_AT      = 'modified_at';
	const PIM_MODIFIED_BY_ID   = 'modified_by_id';
	const PIM_OPTION		   = 'options';
	const PIM_OWNER_USER_ID	   = 'owner_user_id';
	const PIM_DEVAULT_VALUE	   = 'default_value';
	
	const IS_HTML_ALLOWED_ON_FRONT = true;
	const IS_USED_IN_GRID 		   = true;
	const IS_VISIBLE_IN_GRID 	   = true;
	const IS_FILTERABLE_IN_GRID    = true;
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

	const ATTRIBUTE_GROUP_CODE_GENERAL	= "general-information"; // for replacement ATTRIBUTE_SET_ID
	const ATTRIBUTE_GROUP_CODE_PRODUCT	= "product-details"; // for replacement ATTRIBUTE_GROUP_ID


	// attribute set
	const PIM_ATTRIBUTE_LIST	   = 'attribute_list';

	/**
	 * Save data
	 *
	 * @param mixed $datas
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save($datas);

	/**
	 * Prepare Data
	 *
	 * @param mixed $channel
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareData($channel);

	/**
	 * Save attribute set data
	 *
	 * @param mixed $datas
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveAttributeSet($datas);
}