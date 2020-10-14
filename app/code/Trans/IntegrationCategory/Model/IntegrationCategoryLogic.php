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

namespace Trans\IntegrationCategory\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface;
use Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterfaceFactory;
use Trans\IntegrationCategory\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCategory\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCategory\Api\IntegrationCategoryLogicInterface;
use Trans\IntegrationCategory\Api\IntegrationCategoryRepositoryInterface;
use Trans\IntegrationCategory\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCategory\Api\IntegrationJobRepositoryInterface;
use Trans\Integration\Helper\Curl;
use Trans\Integration\Helper\Validation;

class IntegrationCategoryLogic implements IntegrationCategoryLogicInterface {

	/**
	 * @var Curl
	 */
	protected $curl;

	/**
	 * @var CategoryRepositoryInterface
	 */
	protected $categoryRepositoryInterface;

	/**
	 * @var IntegrationDataValueRepositoryInterface
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * @var IntegrationJobRepositoryInterface
	 */
	protected $integrationJobRepositoryInterface;

	/**
	 * @var IntegrationCategoryRepositoryInterface
	 */
	protected $integrationCategoryRepositoryInterface;

	/**
	 * @var IntegrationCategoryInterfaceFactory
	 */
	protected $integrationCategoryInterfaceFactory;

	/**
	 * @var StoreManagerInterface
	 */
	protected $storeManagerInterface;

	/**
	 * @var Validation
	 */
	protected $validation;

	/**
	 * @var CategoryFactory
	 */
	protected $categoryFactory;

	/**
	 * @var CategoryInterfaceFactory
	 */
	protected $categoryInterfaceFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resource;

	/**
	 * @var Registry
	 */
	protected $registry;

	/**
	 * @var string
	 */
	protected $result;

	/**
	 * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param IntegrationCategoryRepositoryInterface $integrationCategoryRepositoryInterface
	 * @param CategoryRepositoryInterface $categoryRepositoryInterface,
	 * @param StoreManagerInterface $storeManagerInterface
	 * @param IntegrationCategoryInterfaceFactory $integrationCategoryInterfaceFactory
	 * @param Curl $curl
	 * @param Validation $validation
	 * @param CategoryFactory $categoryFactory
	 * @param CategoryInterfaceFactory $categoryInterfaceFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\App\ResourceConnection $resource
	 * @param Registry $registry
	 */
	public function __construct
	(
		IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
		IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
		IntegrationCategoryRepositoryInterface $integrationCategoryRepositoryInterface,
		CategoryRepositoryInterface $categoryRepositoryInterface,
		StoreManagerInterface $storeManagerInterface,
		IntegrationCategoryInterfaceFactory $integrationCategoryInterfaceFactory,
		Curl $curl,
		Validation $validation,
		CategoryFactory $categoryFactory,
		CategoryInterfaceFactory $categoryInterfaceFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ResourceConnection $resource,
		Registry $registry
	) {
		$this->curl                                    = $curl;
		$this->categoryRepositoryInterface             = $categoryRepositoryInterface;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
		$this->integrationJobRepositoryInterface       = $integrationJobRepositoryInterface;
		$this->integrationCategoryRepositoryInterface  = $integrationCategoryRepositoryInterface;
		$this->integrationCategoryInterfaceFactory     = $integrationCategoryInterfaceFactory;
		$this->storeManagerInterface                   = $storeManagerInterface;
		$this->validation                              = $validation;
		$this->categoryFactory                         = $categoryFactory;
		$this->categoryInterfaceFactory                = $categoryInterfaceFactory;
		$this->_storeManager                           = $storeManager;
		$this->_resource                               = $resource;

		$registry->register('isSecureArea', true);
	}

	/**
	 * Save Data Category
	 *
	 * @param array $datas
	 * @return true
	 */
	public function saveCategory($datas) {
		$dataCategory          = [];
		$resultFromSaveMagento = [];
		$result                = [];
		$checkUpdate           = [];
		

		$checkData = array_filter($datas->getData());
		if (empty($checkData)) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}

		$jobId    = $datas->getFirstItem()->getJbId();
		$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
		$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
		$this->integrationJobRepositoryInterface->save($dataJobs);

		try {
			$exist = [];
			$i     = 0;
			foreach ($datas as $data) {
				$dataCategory[$i] = $this->curl->jsonToArray($data->getDataValue());
				// Check Existing Category
				$checkUpdate[$i]  = $this->integrationCategoryRepositoryInterface->loadDataByPimId($this->validation->validateArray(IntegrationCategoryInterface::ID, $dataCategory[$i]));
				$exist=$checkUpdate[$i]->getId();
				$isDeleted = $this->validation->validateArray(IntegrationCategoryInterface::DELETED, $dataCategory[$i]);
				$test = 1;
				if($exist<1){
					if ($isDeleted<1) {
						// Create Url Category
						$url[$i]          = preg_replace('#[^0-9a-z]+#i', '-', $this->validation->validateArray(IntegrationCategoryInterface::NAME, $dataCategory[$i]));
						$urlKey[$i]       = strtolower($url[$i]);
						$resultUrlKey[$i] = $this->categoryFactory->create()->loadByAttribute('url_key', $urlKey[$i]);

						// save to magento
						$resultFromSaveMagento[$i] = $this->saveDataToMagento($data, $dataCategory[$i]);
						
						if($resultFromSaveMagento[$i]){
							// save to mapping integration
							$this->saveDataToIntegrationCategory($data, $resultFromSaveMagento[$i], $dataCategory[$i]) ;
							$this->saveStatusMessage($data, IntegrationCategoryInterface::MESSAGE_CATEGORY_CREATED, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
						}
					}else{
						$this->saveStatusMessage($data, IntegrationCategoryInterface::MESSAGE_CATEGORY_IS_NOT_EXIST, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
					}
				}else{
					if ($isDeleted>0) {
						// Delete Category Magento
						$deleteCategory[$i] = $this->categoryInterfaceFactory->create()->load($checkUpdate[$i]->getMagentoEntityId())->delete();
						if ($deleteCategory[$i]) {
							// Delete Category Mapping
							$this->integrationCategoryInterfaceFactory->create()->load($checkUpdate[$i]->getId())->delete();
							// Update status category data is deleted
							$this->saveStatusMessage($data, IntegrationCategoryInterface::MESSAGE_CATEGORY_IS_DELETED, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
						}
					}else{
						// Update Data To Magento
						$resultFromUpdateMagento[$i] = $this->updateDataToMagento($data, $checkUpdate[$i], $dataCategory[$i]);
						if ($resultFromUpdateMagento[$i]) {
							// Update Data To Magento Mapping
							$resultUpdateDataToIntegrationCategory[$i] = $this->updateDataToIntegrationCategory($data, $resultFromUpdateMagento[$i], $dataCategory[$i], $checkUpdate[$i]);
							if ($resultUpdateDataToIntegrationCategory[$i]) {
								// Update Data To Magento Category Child
								$this->updateChildCategory($data, $resultUpdateDataToIntegrationCategory[$i], $dataCategory[$i]);
								$this->saveStatusMessage($data, IntegrationCategoryInterface::MESSAGE_CATEGORY_UPDATED, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
							}
						}
					}
				}
				$i++;	
				
			}

		} catch (\Exception $exception) {
			$dataJobs->setStatus(IntegrationJobInterface::STATUS_READY);
			$dataJobs->setMessage($exception->getMessage());
			$this->integrationJobRepositoryInterface->save($dataJobs);
		}

		$dataJobs->setStatus(IntegrationJobInterface::STATUS_COMPLETE);
		$this->integrationJobRepositoryInterface->save($dataJobs);
		return $result;
	}

	/**
	 * Get Data Value by Job Id
	 *
	 * @param IntegrationJobInterface $channel
	 * @return $result
	 */
	public function prepareData($channel = []) {
		if (empty($channel)) {
			throw new StateException(__(
				'Parameter Channel are empty !'
			));
		}
		$jobs      = $channel['jobs'];
		$jobId     = $jobs->getFirstItem()->getId();
		$jobStatus = $jobs->getFirstItem()->getStatus();
		$status    = IntegrationCategoryInterface::STATUS_JOB;

		if ($jobStatus != IntegrationJobInterface::STATUS_READY) {
			throw new NoSuchEntityException(__('Data already updated'));
		}
		$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
		if (!$result) {
			throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
		}

		return $result;
	}

	/**
	 * Save Data Category to Magento
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param array $dataCategory
	 * @return $result
	 */
	public function saveDataToMagento($data, $dataCategory) {
		try {
			$parentId = IntegrationCategoryInterface::DEFAULT_CATEGORY_PARENT_ID;
			
			$category = $this->categoryInterfaceFactory->create();
			if ($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory)) {
				$collection = $this->integrationCategoryRepositoryInterface->loadDataByCategoryParentId($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory));
				if ($collection->getSize()) {
					$parentId = $collection->getFirstItem()->getMagentoEntityId();
				}
			}
			
			$category->setParentId($parentId);

			$isActive = 0;
			if($this->validation->validateArray(IntegrationCategoryInterface::IS_ACTIVE, $dataCategory)){
				$isActive = $this->validation->validateArray(IntegrationCategoryInterface::IS_ACTIVE, $dataCategory);
			}
			$category->setIsActive($isActive);

			$category->setName($this->validation->validateArray(IntegrationCategoryInterface::NAME, $dataCategory));
			
			$category->setDescription($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION, $dataCategory));
			$category->setIncludeInMenu(IntegrationCategoryInterface::INCLUDE_IN_MENU);

			$result = $this->categoryRepositoryInterface->save($category);
		} catch (\Exception $exception) {
			$this->saveStatusMessage($data, $exception->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			return false;
		}
		return $result;
	}

	/**
	 * Save Data Category to Integration Category
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param CategoryInterface $resultFromSaveMagento
	 * @param array $dataCategory
	 * @return $result
	 */
	public function saveDataToIntegrationCategory($data, $resultFromSaveMagento, $dataCategory) {
		try {
			$integrationCategory = $this->integrationCategoryInterfaceFactory->create();
			$integrationCategory->setMagentoEntityId($resultFromSaveMagento->getEntityId());
			$integrationCategory->setMagentoParentId($resultFromSaveMagento->getParentId());
			$integrationCategory->setPimAssignedUserId($this->validation->validateArray(IntegrationCategoryInterface::ASSIGNED_USER_ID, $dataCategory));
			$parentId = IntegrationCategoryInterface::DEFAULT_CATEGORY_PARENT_ID;
			if($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory)){
				$parentId = $this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory);
				
			}
			$integrationCategory->setPimCategoryParentId($parentId);

			$integrationCategory->setPimCategoryRoute($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_ROUTE, $dataCategory));
			$integrationCategory->setPimCategoryRouteName($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_ROUTE_NAME, $dataCategory));
			$integrationCategory->setPimCode($this->validation->validateArray(IntegrationCategoryInterface::CODE, $dataCategory));
			$integrationCategory->setPimCreatedAt($this->validation->validateArray(IntegrationCategoryInterface::CREATED_AT, $dataCategory));
			$integrationCategory->setPimCreatedById($this->validation->validateArray(IntegrationCategoryInterface::CREATED_BY_ID, $dataCategory));
			$integrationCategory->setPimDeleted($this->validation->validateArray(IntegrationCategoryInterface::DELETED, $dataCategory));
			$integrationCategory->setPimDescription($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION, $dataCategory));
			$integrationCategory->setPimDescriptionEnUs($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION_EN_US, $dataCategory));
			$integrationCategory->setPimDescriptionIdId($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION_ID_ID, $dataCategory));
			$integrationCategory->setPimId($this->validation->validateArray(IntegrationCategoryInterface::ID, $dataCategory));
			$integrationCategory->setPimImageName($this->validation->validateArray(IntegrationCategoryInterface::IMAGE_NAME, $dataCategory));
			$integrationCategory->setPimIsActive($this->validation->validateArray(IntegrationCategoryInterface::IS_ACTIVE, $dataCategory));
			$integrationCategory->setPimModifiedAt($this->validation->validateArray(IntegrationCategoryInterface::MODIFIED_AT, $dataCategory));
			$integrationCategory->setPimModifiedById($this->validation->validateArray(IntegrationCategoryInterface::MODIFIED_BY_ID, $dataCategory));
			$integrationCategory->setPimName($this->validation->validateArray(IntegrationCategoryInterface::NAME, $dataCategory));
			$integrationCategory->setPimNameEnUs($this->validation->validateArray(IntegrationCategoryInterface::NAME_EN_US, $dataCategory));
			$integrationCategory->setPimNameIdId($this->validation->validateArray(IntegrationCategoryInterface::NAME_ID_ID, $dataCategory));
			$integrationCategory->setPimOwnerUserId($this->validation->validateArray(IntegrationCategoryInterface::OWNER_USER_ID, $dataCategory));
			$integrationCategory->setStatus(IntegrationJobInterface::STATUS_COMPLETE);

			$result = $this->integrationCategoryRepositoryInterface->save($integrationCategory);
		} catch (\Exception $exception) {
			$this->saveStatusMessage($data, $exception->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			return false;
		}
		return $result;
	}

	

	/**
	 * Update Data Category to Magento
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param IntegrationCategoryInterface $checkUpdate
	 * @param array $dataCategory
	 * @return $result
	 */
	public function updateDataToMagento($data, $checkUpdate, $dataCategory) {
		try {
			$storeId  = $this->storeManagerInterface->getStore()->getId();
			$entityId = $checkUpdate->getMagentoEntityId();
			$category = $this->categoryInterfaceFactory->create()->load($entityId);
			// Set Parent
			$parentId = IntegrationCategoryInterface::DEFAULT_CATEGORY_PARENT_ID;
			if ($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory)) {
				$parentId = $this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory);
				$collection = $this->integrationCategoryRepositoryInterface->loadDataByCategoryParentId($parentId);
				if ($collection->getSize()) {
					$parentId = $collection->getFirstItem()->getMagentoEntityId();	
				} 
			}
			$category->move($parentId, null);
			
			// Set active
			$isActive = 0;
			if($this->validation->validateArray(IntegrationCategoryInterface::IS_ACTIVE, $dataCategory)){
				$isActive = $this->validation->validateArray(IntegrationCategoryInterface::IS_ACTIVE, $dataCategory);		
			}
			$category->setIsActive($isActive);

			$category->setName($this->validation->validateArray(IntegrationCategoryInterface::NAME, $dataCategory));
			$category->setDescription($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION, $dataCategory));
			$category->setIncludeInMenu(IntegrationCategoryInterface::INCLUDE_IN_MENU);

			$result = $this->categoryRepositoryInterface->save($category);

		} catch (\Exception $exception) {
			$this->saveStatusMessage($data, $exception->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			return false;
		}
		return $result;
	}


	/**
	 * Update Data Category to Integration category
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param CategoryInterface $resultFromUpdateMagento
	 * @param IntegrationCategoryInterface $checkUpdate
	 * @param array $dataCategory
	 * @return $result
	 */
	public function updateDataToIntegrationCategory($data, $resultFromUpdateMagento, $dataCategory, $checkUpdate) {
		try {
			$integrationCategory = $this->integrationCategoryInterfaceFactory->create()->load($checkUpdate->getId());
			$integrationCategory->setMagentoParentId($resultFromUpdateMagento->getParentId());
			$integrationCategory->setPimAssignedUserId($this->validation->validateArray(IntegrationCategoryInterface::ASSIGNED_USER_ID, $dataCategory));
			$parentId = IntegrationCategoryInterface::DEFAULT_CATEGORY_PARENT_ID;
			if($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory)){
				$parentId = $this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_PARENT_ID, $dataCategory);
				
			}
			$integrationCategory->setPimCategoryParentId($parentId);
			
			$integrationCategory->setPimCategoryRoute($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_ROUTE, $dataCategory));
			$integrationCategory->setPimCategoryRouteName($this->validation->validateArray(IntegrationCategoryInterface::CATEGORY_ROUTE_NAME, $dataCategory));
			$integrationCategory->setPimCode($this->validation->validateArray(IntegrationCategoryInterface::CODE, $dataCategory));
			$integrationCategory->setPimCreatedAt($this->validation->validateArray(IntegrationCategoryInterface::CREATED_AT, $dataCategory));
			$integrationCategory->setPimCreatedById($this->validation->validateArray(IntegrationCategoryInterface::CREATED_BY_ID, $dataCategory));
			$integrationCategory->setPimDeleted($this->validation->validateArray(IntegrationCategoryInterface::DELETED, $dataCategory));
			$integrationCategory->setPimDescription($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION, $dataCategory));
			$integrationCategory->setPimDescriptionEnUs($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION_EN_US, $dataCategory));
			$integrationCategory->setPimDescriptionIdId($this->validation->validateArray(IntegrationCategoryInterface::DESCRIPTION_ID_ID, $dataCategory));
			$integrationCategory->setPimId($this->validation->validateArray(IntegrationCategoryInterface::ID, $dataCategory));
			$integrationCategory->setPimImageName($this->validation->validateArray(IntegrationCategoryInterface::IMAGE_NAME, $dataCategory));
			$integrationCategory->setPimIsActive($this->validation->validateArray(IntegrationCategoryInterface::IS_ACTIVE, $dataCategory));
			$integrationCategory->setPimModifiedAt($this->validation->validateArray(IntegrationCategoryInterface::MODIFIED_AT, $dataCategory));
			$integrationCategory->setPimModifiedById($this->validation->validateArray(IntegrationCategoryInterface::MODIFIED_BY_ID, $dataCategory));
			$integrationCategory->setPimName($this->validation->validateArray(IntegrationCategoryInterface::NAME, $dataCategory));
			$integrationCategory->setPimNameEnUs($this->validation->validateArray(IntegrationCategoryInterface::NAME_EN_US, $dataCategory));
			$integrationCategory->setPimNameIdId($this->validation->validateArray(IntegrationCategoryInterface::NAME_ID_ID, $dataCategory));
			$integrationCategory->setPimOwnerUserId($this->validation->validateArray(IntegrationCategoryInterface::OWNER_USER_ID, $dataCategory));
			$integrationCategory->setStatus(IntegrationJobInterface::STATUS_COMPLETE);

			$result = $this->integrationCategoryRepositoryInterface->save($integrationCategory);

		} catch (\Exception $exception) {
			$this->saveStatusMessage($data, $exception->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			return false;
		}

		return $result;
	}

	/**
	 * Update Parent Id, Name, Url Key, Path and Active to Child Category
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param IntegrationCategoryInterface $checkUpdate
	 * @param array $dataCategory
	 * @return $result
	 */
	public function updateChildCategory($data, $resultUpdateDataToIntegrationCategory, $dataCategory) {
		$i             = 0;
		$categoryChild = [];

		$resultMappings   = $this->integrationCategoryRepositoryInterface->loadDataByPimCategoryParentId($this->validation->validateArray(IntegrationCategoryInterface::ID, $dataCategory));
		$urlParent[$i]    = preg_replace('#[^0-9a-z]+#i', '-', $this->validation->validateArray(IntegrationCategoryInterface::NAME, $dataCategory));
		$urlKeyParent[$i] = strtolower($urlParent[$i]);
		if ($resultMappings) {
			foreach ($resultMappings as $key => $resultMapping) {

				$url[$i]    = preg_replace('#[^0-9a-z]+#i', '-', $resultMapping->getPimName());
				$urlKey[$i] = strtolower($url[$i]);

				$categoryChild[$i] = $this->categoryInterfaceFactory->create()->load($resultMapping->getMagentoEntityId());
				$categoryChild[$i]->move($resultUpdateDataToIntegrationCategory->getMagentoEntityId(), $resultMapping->getMagentoEntityId());
				$categoryChild[$i]->setName($resultMapping->getPimName());
				$categoryChild[$i]->setUrlKey($urlKey[$i]);
				$categoryChild[$i]->setPath($urlKeyParent[$i] . '/' . $urlKey[$i]);
				$categoryChild[$i]->setIsActive(1);

				$result = $this->categoryRepositoryInterface->save($categoryChild[$i]);
			}
		}
	}

	/**
	 * Save Status and Message to Integration Data Value
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param string $message
	 * @param int $status
	 * @return $result
	 */
	public function saveStatusMessage($data, $message, $status) {
		$data->setMessage($message);
		$data->setStatus($status);
		$this->integrationDataValueRepositoryInterface->save($data);
	}
}