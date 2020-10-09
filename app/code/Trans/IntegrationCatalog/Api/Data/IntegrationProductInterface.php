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

namespace Trans\IntegrationCatalog\Api\Data;

/**
 * @api
 */
interface IntegrationProductInterface {

	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 *
	 */

	const DEFAULT_EVENT = 'trans_integration_catalog';
	const TABLE_NAME    = 'integration_catalog_product';

	/**
	 * Attribute type Code
	 */
	const ENTITY_TYPE_CODE = 'catalog_product';

	/**
	 * Constant for field name default id
	 */
	const ID = 'id';

	/**
	 * For Job Status Default Set
	 */
	const STATUS_JOB = 1;

	/**
	 * Constant for field name table integration_product
	 */
	const INTEGRATION_DATA_ID = 'integration_data_id';
	const MAGENTO_ENTITY_ID   = 'magento_entity_id';
	const PIM_ID              = 'pim_id';
	const ITEM_ID             = 'item_id';
	const MAGENTO_PARENT_ID   = 'magento_parent_id';
	const PIM_CATGORY_ID      = 'pim_catgory_id';
	const PIM_COLOR_ID        = 'pim_color_id';
	const PIM_SKU        	  = 'pim_sku';
	const ATTRIBUTE_LIST = 'attribute_list';
	/**
	 * New Constant for field name table integration_product
	 */
	const MAGENTO_CATEGORY_IDS	= 'magento_category_ids';
	// 0 is not configurable , 1 <= is configurable
	const STATUS_CONFIGURABLE	= 'status_configurable'; 
	const STATUS_CONFIGURABLE_NEED_CREATE	= 3;
	const STATUS_CONFIGURABLE_NEED_UPDATE	= 5;
	const STATUS_CONFIGURABLE_ON_PROGRESS 	= 7;
	const STATUS_CONFIGURABLE_UPDATED		= 10;// no need to create / update 
	const STATUS_CONFIGURABLE_FAIL_UPDATE	= 13;// create / update fail

	/**
	 * Constant for get data from PIM
	 */
	const ART          = 'art';
	
	const MAT          = 'mat';
	const COL_ID       = 'color_code';
	const COL          = 'col';
	const SIZE         = 'size';
	const BARCODE      = 'barcode';
	
	const GTYPE        = 'gtype';
	const DESCR        = 'descr';
	const BOX          = 'box';
	const PRODUK       = 'produk';
	const BAHAN        = 'bahan';
	const PEMELIHARAAN = 'pemeliharaan';
	const MADE_IN      = 'made_in';
	const BRANCH       = 'branch';

	const QTY          = 'qty';
	const BRAND        = 'brand_id';
	const SEASON       = 'season';
	const GROUP        = 'group';
	const CATEGORY     = 'category';
	
	const WEIGHT3      = 'weight3';
	const ACTIVE       = 'active';
	const CHANNEL      = 'channel';
	const GWP          = 'gwp';
	const NON_MERCH    = 'non_merch';
	const CONSUMABLE   = 'consumable';
	const HSCODE       = 'hscode';
	
	const COLOR        = 'color';
	const IMAGES       = 'images';
	const SELLING_UNIT = 'selling_unit';

	//New Const For Product Response
	const NAME			= 'product_name';
	const CTGID			= 'category_id';
	const SKU			= 'sku';
	const PRICE			= 'online_price';

	const WEIGHT		= 'weight';
	const HEIGHT		= 'height';
	const LENGTH		= 'length';
	const WIDTH			= 'width';

	const SHORT_DESC	= 'short_description';
	const LONG_DESC		= 'long_description';
	const ATTRIBUTES	= 'list_attributes';

	const PIM_CATEGORY_ID      = 'pim_category_id';

	const IMG_URL = 'image_url';
	const IMG_NAME = 'image_name';
	const IS_DEFAULT = 'is_default';
	const IS_ACTIVE = 'is_active';
	const DELETED = 'deleted';

	const PRODUCT_TYPE = 'product_type';
	const PRODUCT_TYPE_SIMPLE_LABEL = "simple";
	const PRODUCT_TYPE_SIMPLE_VALUE = 1;
	const PRODUCT_TYPE_DIGITAL_LABEL = "digital";
	const PRODUCT_TYPE_DIGITAL_VALUE = 3;

	const CATALOG_TYPE = 'catalog_type';
	const CATALOG_TYPE_SIMPLE_VALUE = 'simple';
	const CATALOG_TYPE_DIGITAL_VALUE = 'virtual';
	
	/**
	 * Contant for Product Price
	 */
	const NORMAL_SELLING_PRICE = 'normal_selling_price';

	/**
	 * Product Message Cron
	 */
	const MSG_PRODUCT_IS_NULL = "Error Update Product, Atribute Data Size is 0 or Null";
	const MSG_PRODUCT_DELETED = "Product has ben deleted by Update";

	/**
	 * Product Attribute Type
	 */
	const FRONTEND_INPUT_TYPE_SELECT = "select";
	const FRONTEND_INPUT_TYPE_TEXT   = "text";

	/**
	 * Product Type
	 */
	const PRODUCT_TYPE_SIMPLE       = "simple";
	const PRODUCT_TYPE_CONFIGURABLE = "configurable";
	const PRODUCT_TYPE_VIRTUAL      = "virtual";

	/**
	 * Product Visibility
	 */
	const VISIBILITY_NOT_VISIBLE = 1;
	const VISIBILITY_IN_CATALOG  = 2;
	const VISIBILITY_IN_SEARCH   = 3;
	const VISIBILITY_BOTH        = 4;

	/**
	* Product Status values
	*/
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;

	/**
	 * Constant For Product Attribute
	 */
	const IS_HTML_ALLOWED_ON_FRONT = true;
	const IS_USED_IN_GRID 		   = true;
	const IS_VISIBLE_IN_GRID 	   = true;
	const IS_FILTERABLE_IN_GRID    = true;
	const IS_SEARCHABLE            = true;
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

	const INPUT_TYPE_BACKEND_FORMAT_PRICE = "static";
	const INPUT_TYPE_FRONTEND_FORMAT_PRICE = "select";
	const SELLING_UNIT_CODE		= "selling_unit";

	/**
	 * get id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Get Entity Id
	 *
	 * @param int
	 */
	public function getMagentoEntityId();

	/**
	 * Set Entity Id
	 *
	 * @param int $entityId
	 * @return void
	 */
	public function setMagentoEntityId($entityId);

	/**
	 * Get Pim Id
	 *
	 * @param mixed
	 */
	public function getPimId();

	/**
	 * Set Pim Id
	 *
	 * @param mixed $pimId
	 * @return void
	 */
	public function setPimId($pimId);

	/**
	 * Get Pim SKU
	 *
	 * @param string
	 */
	public function getPimSku();

	/**
	 * Set Pim SKU
	 *
	 * @param mixed $sku
	 * @return void
	 */
	public function setPimSku($sku);

	/**
	 * Get Integration Data Id
	 *
	 * @param mixed
	 */
	public function getIntegrationDataId();

	/**
	 * Set Integration Data Id
	 *
	 * @param mixed $integrationDataId
	 * @return void
	 */
	public function setIntegrationDataId($integrationDataId);

	/**
	 * Get PIM Item Id
	 *
	 * @param mixed
	 */
	public function getItemId();

	/**
	 * Set PIM Item Id
	 *
	 * @param mixed $itemId
	 * @return void
	 */
	public function setItemId($itemId);

	/**
	 * Get Magento Parent Id
	 *
	 * @param mixed
	 */
	public function getMagentoParentId();

	/**
	 * Set Magento Parent Id
	 *
	 * @param mixed $magentoParentId
	 * @return void
	 */
	public function setMagentoParentId($magentoParentId);

	/**
	 * Get Pim Category Id
	 *
	 * @param mixed
	 */
	public function getPimCategoryId();

	/**
	 * Set Pim Category Id
	 *
	 * @param mixed $catId
	 * @return void
	 */
	public function setPimCategoryId($catId);

	/**
	 * Get Magento Category Id
	 *
	 * @param mixed
	 */
	public function getMagentoCategoryIds();

	/**
	 * Set Magento Category Id
	 *
	 * @param mixed $catId
	 * @return void
	 */
	public function setMagentoCategoryIds($catId);

	/**
	 * Get Magento Category Id
	 *
	 * @param mixed
	 */
	public function getStatusConfigurable();

	/**
	 * Set Magento Category Id
	 *
	 * @param mixed $catId
	 * @return void
	 */
	public function setStatusConfigurable($catId);

	/**
	 * Get Product TYpe Id
	 *
	 * @param mixed
	 */
	public function getProductType();

	/**
	 * Set Product TYpe Id
	 *
	 * @param mixed $typeId
	 * @return void
	 */
	public function setProductType($typeId);

	/**
	 * Get Product attribute list
	 *
	 * @param string
	 */
	public function getAttributeList();

	/**
	 * Set Product attribute list
	 *
	 * @param string $json
	 * @return void
	 */
	public function setAttributeList($json);

}