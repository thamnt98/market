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

namespace Trans\IntegrationCategory\Api\Data;

/**
 * @api
 */
interface IntegrationCategoryInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */

	/**
	 * Constant for table name
	 */
	const DEFAULT_EVENT = 'trans_integration';
	const TABLE_NAME    = 'integration_category';

	/**
	 * Constant for field name default id
	 */
	const ID = 'id';

	/**
	 * Constant for field name from magento table
	 */
	const MAGENTO_ENTITY_ID = 'magento_entity_id';
	const MAGENTO_PARENT_ID = 'magento_parent_id';

	/**
	 * Constant for field name from table data
	 */
	const PIM_ASSIGNED_USER_ID    = 'pim_assigned_user_id';
	const PIM_CATEGORY_PARENT_ID  = 'pim_category_parent_id';
	const PIM_CATEGORY_ROUTE      = 'pim_category_route';
	const PIM_CATEGORY_ROUTE_NAME = 'pim_category_route_name';
	const PIM_CODE                = 'pim_code';
	const PIM_CREATED_AT          = 'pim_created_at';
	const PIM_CREATED_BY_ID       = 'pim_created_by_id';
	const PIM_DELETED             = 'pim_deleted';
	const PIM_DESCRIPTION         = 'pim_description';
	const PIM_DESCRIPTION_EN_US   = 'pim_description_en_us';
	const PIM_DESCRIPTION_ID_ID   = 'pim_description_id_id';
	const PIM_ID                  = 'pim_id';
	const PIM_IMAGE_NAME          = 'pim_image_name';
	const PIM_IS_ACTIVE           = 'pim_is_active';
	const PIM_MODIFIED_AT         = 'pim_modified_at';
	const PIM_MODIFIED_BY_ID      = 'pim_modified_by_id';
	const PIM_NAME                = 'pim_name';
	const PIM_NAME_EN_US          = 'pim_name_en_us';
	const PIM_NAME_ID_ID          = 'pim_name_id_id';
	const PIM_OWNER_USER_ID       = 'pim_owner_user_id';
	const UPDATED_AT              = 'updated_at';
	const STATUS                  = 'status';

	/**
	 * Constant for get data from PIM
	 */
	const ASSIGNED_USER_ID    = 'assigned_user_id';
	const CATEGORY_PARENT_ID  = 'parent_id';
	const CATEGORY_ROUTE      = 'category_route';
	const CATEGORY_ROUTE_NAME = 'category_route_name';
	const CODE                = 'code';
	const CREATED_AT          = 'created_at';
	const CREATED_BY_ID       = 'created_by_id';
	const DELETED             = 'deleted';
	const DESCRIPTION         = 'description';
	const DESCRIPTION_EN_US   = 'description_en_us';
	const DESCRIPTION_ID_ID   = 'description_id_id';
	// const ID                  = 'id';
	const IMAGE_NAME     = 'image_name';
	const IS_ACTIVE      = 'is_active';
	const MODIFIED_AT    = 'modified_at';
	const MODIFIED_BY_ID = 'modified_by_id';
	const NAME           = 'category_name';
	const NAME_EN_US     = 'name_en_us';
	const NAME_ID_ID     = 'name_id_id';
	const OWNER_USER_ID  = 'owner_user_id';

	//Set default for menu
	const INCLUDE_IN_MENU            = true;
	const DEFAULT_CATEGORY_PARENT_ID = 2;

	//For Job Status Default Set
	const STATUS_JOB = 1;

	/**
	 * Constant for attribute
	 */
	const DEFAULT_MD_ID        = 1;
	const STATUS_WAITING       = 1; // waiting to get data from API
	const STATUS_PROGRESS_GET  = 10; // On progress save data from api
	const STATUS_PROGRESS_SYNC = 11; // On Progress save / sync data original magento table entity
	const STATUS_PROGRESS_POST = 12; // On Progress post data to

	const STATUS_PROGRESS_FAIL = 20; // Fail

	const STATUS_READY             = 30; // Data ready for sync to original magento table entity
	const STATUS_PROGRESS_CATEGORY = 31; // On Progres Sync job data category to magento table

	const STATUS_COMPLETE = 50; // Data successfully saved sync

	const STATUS_CANCEL = 60; // cancel
	const STATUS_CLOSE  = 61; // close

	/**
	 * Message
	 */

	const MESSAGE_SAVED               = 'saved';
	const MESSAGE_DELETED             = 'deleted';
	const MSG_CATEGORY_IS_NULL        = "Error Update Category";
	const MESSAGE_CATEGORY_IS_DELETED = 'Category Successfully deleted!';
	const MESSAGE_CATEGORY_IS_NOT_EXIST = 'Category Is not Exist!';
	const MESSAGE_CATEGORY_CREATED 	= 'Category Successfully Created!';
	const MESSAGE_CATEGORY_UPDATED 	= 'Category Successfully Updated!';
	

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
	 * Get Parent Id
	 *
	 * @param int
	 */
	public function getMagentoParentId();

	/**
	 * Set Parent Id
	 *
	 * @param int $parentId
	 * @return void
	 */
	public function setMagentoParentId($parentId);

	/**
	 * Get Assigned User Id
	 *
	 * @param int
	 */
	public function getPimAssignedUserId();

	/**
	 * Set Assigned User Id
	 *
	 * @param int $assignedUserId
	 * @return void
	 */
	public function setPimAssignedUserId($assignedUserId);

	/**
	 * Get Category Parent Id
	 *
	 * @param string
	 */
	public function getPimCategoryParentId();

	/**
	 * Set Category Parent Id
	 *
	 * @param string $categoryParentId
	 * @return void
	 */
	public function setPimCategoryParentId($categoryParentId);

	/**
	 * Get Category Route
	 *
	 * @param string
	 */
	public function getPimCategoryRoute();

	/**
	 * Set Category Route
	 *
	 * @param string $categoryRoute
	 * @return void
	 */
	public function setPimCategoryRoute($categoryRoute);

	/**
	 * Get Category Name
	 *
	 * @param string
	 */
	public function getPimCategoryRouteName();

	/**
	 * Set Category Name
	 *
	 * @param string $categoryRouteName
	 * @return void
	 */
	public function setPimCategoryRouteName($categoryRouteName);

	/**
	 * Get Code
	 *
	 * @param int
	 */
	public function getPimCode();

	/**
	 * Set Code
	 *
	 * @param int $code
	 * @return void
	 */
	public function setPimCode($code);

	/**
	 * Get Created At
	 *
	 * @param string
	 */
	public function getPimCreatedAt();

	/**
	 * Set Created At
	 *
	 * @param string $createdAt
	 * @return void
	 */
	public function setPimCreatedAt($createdAt);

	/**
	 * Get Created By Id
	 *
	 * @param int
	 */
	public function getPimCreatedById();

	/**
	 * Set Created By Id
	 *
	 * @param int $createdById
	 * @return void
	 */
	public function setPimCreatedById($createdById);

	/**
	 * Get Deleted
	 *
	 * @param int
	 */
	public function getPimDeleted();

	/**
	 * Set Deleted
	 *
	 * @param int $deleted
	 * @return void
	 */
	public function setPimDeleted($deleted);

	/**
	 * Get Description
	 *
	 * @param string
	 */
	public function getPimDescription();

	/**
	 * Set Description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setPimDescription($description);

	/**
	 * Get Description En Us
	 *
	 * @param string
	 */
	public function getPimDescriptionEnUs();

	/**
	 * Set Description En Us
	 *
	 * @param string $descriptionEnUs
	 * @return void
	 */
	public function setPimDescriptionEnUs($descriptionEnUs);

	/**
	 * Get Description Id Id
	 *
	 * @param string
	 */
	public function getPimDescriptionIdId();

	/**
	 * Set Description Id Id
	 *
	 * @param string $descriptionIdId
	 * @return void
	 */
	public function setPimDescriptionIdId($descriptionIdId);

	/**
	 * Get Id
	 *
	 * @param string
	 */
	public function getPimId();

	/**
	 * Set Id
	 *
	 * @param string $pimId
	 * @return void
	 */
	public function setPimId($pimId);

	/**
	 * Get Image Name
	 *
	 * @param string
	 */
	public function getPimImageName();

	/**
	 * Set Image Name
	 *
	 * @param string $imageName
	 * @return void
	 */
	public function setPimImageName($imageName);

	/**
	 * Get Is Active
	 *
	 * @param string
	 */
	public function getPimIsActive();

	/**
	 * Set Is Active
	 *
	 * @param string $isActive
	 * @return void
	 */
	public function setPimIsActive($isActive);

	/**
	 * Get Modified At
	 *
	 * @param mixed
	 */
	public function getPimModifiedAt();

	/**
	 * Set Modified At
	 *
	 * @param mixed $modifiedAt
	 * @return void
	 */
	public function setPimModifiedAt($modifiedAt);

	/**
	 * Get Modified By Id
	 *
	 * @param int
	 */
	public function getPimModifiedById();

	/**
	 * Set Modified By Id
	 *
	 * @param int $modifiedById
	 * @return void
	 */
	public function setPimModifiedById($modifiedById);

	/**
	 * Get Name
	 *
	 * @param string
	 */
	public function getPimName();

	/**
	 * Set Name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setPimName($name);

	/**
	 * Get Name En Us
	 *
	 * @param string
	 */
	public function getPimNameEnUs();

	/**
	 * Set Name En Us
	 *
	 * @param string $nameEnUs
	 * @return void
	 */
	public function setPimNameEnUs($nameEnUs);

	/**
	 * Get Name Id Id
	 *
	 * @param string
	 */
	public function getPimNameIdId();

	/**
	 * Set Name Id Id
	 *
	 * @param string $nameIdId
	 * @return void
	 */
	public function setPimNameIdId($nameIdId);

	/**
	 * Get Get Owner User Id
	 *
	 * @param int
	 */
	public function getPimOwnerUserId();

	/**
	 * Set Get Owner User Id
	 *
	 * @param int $ownerUserId
	 * @return void
	 */
	public function setPimOwnerUserId($ownerUserId);

	/**
	 * Get Status
	 *
	 * @param int
	 */
	public function getStatus();

	/**
	 * Set Status
	 *
	 * @param int $status
	 * @return void
	 */
	public function setStatus($status);
}