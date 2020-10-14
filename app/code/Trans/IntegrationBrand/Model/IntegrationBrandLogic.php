<?php
/**
 * @category Trans
 * @package  Trans_IntegrationBrand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 * @author   HaDi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationBrand\Model;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\StoreManagerInterface;
use Trans\Brand\Api\BrandRepositoryInterface;
use Trans\Brand\Api\Data\AmastyBrandInterface;
use Trans\Brand\Api\Data\AmastyBrandInterfaceFactory;
use Trans\Brand\Api\Data\BrandInterface;
use Trans\Brand\Api\Data\BrandInterfaceFactory;
use Trans\IntegrationBrand\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationBrand\Api\Data\IntegrationJobInterface;
use Trans\IntegrationBrand\Api\IntegrationBrandLogicInterface;
use Trans\IntegrationBrand\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationBrand\Api\IntegrationJobRepositoryInterface;
use Trans\Integration\Helper\Curl;
use Trans\Integration\Helper\Validation;
use Trans\IntegrationBrand\Helper\Eav\Attribute\Option as OptionHelper;
use \Trans\Integration\Logger\Logger;

class IntegrationBrandLogic implements IntegrationBrandLogicInterface {

	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

	/**
	 * @var Curl
	 */
	protected $curl;

	/**
	 * @var Validation
	 */
	protected $validation;

	/**
	 * @var AmastyBrandInterface
	 */
	protected $amastyBrandInterface;

	/**
	 * @var AmastyBrandInterfaceFactory
	 */
	protected $amastyBrandFactory;

	/**
	 * @var IntegrationJobRepositoryInterface
	 */
	protected $integrationJobRepositoryInterface;

	/**
	 * @var IntegrationDataValueRepositoryInterface
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * BrandFactory
	 *
	 * @var BrandRepositoryInterface
	 */
	protected $brandRepositoryInterface;

	/**
	 * BrandFactory
	 *
	 * @var BrandInterfaceFactory
	 */
	protected $brandInterfaceFactory;

	/**
	 * BrandFactory
	 *
	 * @var BrandInterfaceFactory
	 */
	protected $brandInterface;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \Trans\IntegrationBrand\Helper\Eav\Attribute\Option
	 */
	protected $optionHelper;

	/**
	 * @var \Magento\Eav\Setup\EavSetupFactory
	 */
	protected $eavSetupFactory;

	/**
	 * @param Logger $logger
	 * @param Curl $curl
	 * @param Validation $validation
	 * @param AmastyBrandInterface $amastyBrandInterface
	 * @param AmastyBrandInterfaceFactory $amastyBrandFactory
	 * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param IntegrationBrandInterfaceFactory $integrationBrandInterfaceFactory
	 * @param BrandRepositoryInterface $brandRepositoryInterface
	 * @param BrandInterfaceFactory $brandInterfaceFactory
	 * @param StoreManagerInterface $storeManager
	 * @param OptionHelper $optionHelper
	 * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
	 */
	public function __construct
	(
		Logger $logger
		, Curl $curl
		, Validation $validation
		, AmastyBrandInterfaceFactory $amastyBrandFactory
		, IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
		, IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
		, BrandInterfaceFactory $brandInterfaceFactory
		, StoreManagerInterface $storeManager
		, OptionHelper $optionHelper
		, \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory

	) {
		$this->logger                                  = $logger;
		$this->curl                                    = $curl;
		$this->validation                              = $validation;
		$this->amastyBrandFactory                      = $amastyBrandFactory;
		$this->integrationJobRepositoryInterface       = $integrationJobRepositoryInterface;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;

		$this->brandInterfaceFactory = $brandInterfaceFactory;

		$this->storeManager = $storeManager;
		$this->optionHelper = $optionHelper;
		$this->eavSetupFactory = $eavSetupFactory;

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_brand.log');
	    $logger = new \Zend\Log\Logger();
	    $this->logger = $logger->addWriter($writer);

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
		$jobs        = $channel['jobs'];
		$jobId       = $jobs->getFirstItem()->getId();
		$jobStatus   = $jobs->getFirstItem()->getStatus();
		$statusReady = IntegrationDataValueInterface::STATUS_DATA_READY;

		if ($jobStatus != IntegrationJobInterface::STATUS_READY) {
			throw new NoSuchEntityException(__('Data already updated Job = ' . $jobStatus));
		}
		$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $statusReady);
		if (!$result) {
			throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
		}

		return $result;
	}

	/**
	 * Save Data Brand
	 *
	 * @param array $datas
	 * @return true
	 */
	public function save($datas) {

		$checkData = array_filter($datas->getData());
		if (empty($checkData)) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}

		$jobId = $datas->getFirstItem()->getJbId();
		$this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);

		$i = 0;
		$j = 0;

		$allStores = $this->storeManager->getStores();
        // get attribute id
      	$attrCollection = $this->optionHelper->initAttribute($this->optionHelper::SHOPBY_BRAND_ATTRIBUTE_CODE);
      	$attrId = $attrCollection->getAttributeId();

		foreach ($datas as $data) {
			try {
		      	$dataEnv = $this->curl->jsonToArray($data->getDataValue());

		      	$option = array();
                $option['attribute_id'] = $attrId;

                $optionCollection = $this->optionHelper->getOptionValue($dataEnv['brand_name'], $attrId);
		      	$indexAttrValue = $dataEnv['brand_name'];

		      	if(!$optionCollection->getSize()) {
		          	$option['value']['name'][0] = $indexAttrValue;
		          	foreach($allStores as $store){
		            	$option['value']['name'][$store->getId()] = $indexAttrValue;
		          	}
		          	$eavSetup = $this->eavSetupFactory->create();
		          	$eavSetup->addAttributeOption($option);

		      	}

		    } catch (\Exception $e) {
		     	$this->logger->info($e->getMessage());
				continue;   
		    }

			try {
				foreach ($allStores as $key => $store) {
					$param = $this->validateDataValue($data, $store->getId());

					$msg = $this->saveDataValue($param);
					$data->setStoreId($store->getId());
					$this->saveStatusMessage($data, $msg, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
				}
			} catch (\Exception $exception) {
				$msg = __FUNCTION__ . " ERROR : " . $exception->getMessage();
				$this->saveStatusMessage($data, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
				continue;
			}
			$i++;

		}
		$this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_COMPLETE);

	}
	/**
	 * Save Brand Data Value
	 * @param instance Integration Data Value
	 *
	 */
	protected function saveDataValue($params) {
		try {
			$brand       = $this->getByPimId($params[BrandInterface::PIM_ID]);
			$brandAmasty = $this->getByAmastyPimId($params[AmastyBrandInterface::AMASTY_PIM_ID]);
			if ($params[BrandInterface::DELETED] == 1) {
				if (!empty($brand)) {
					$brand->delete();
					$msg = "Data Deleted By Update: " . $params[BrandInterface::PIM_ID];
					throw new StateException(__($msg));
				} else {
					$msg = "Data Already Deleted By Update: " . $params[BrandInterface::PIM_ID];
				}

				return $msg;
			}

			if (is_null($brand)) {
				$msg = "Successfully Create New Brand";

				$model = $this->brandInterfaceFactory->create();
				$model->setData($params);
				$model->save();

				

			} else {
				$msg = "Successfully Update Brand Id " . $brand->getId();
				$brand->setTitle($params[BrandInterface::TITLE]);
				$brand->setDescription($params[BrandInterface::DESCRIPTION]);
				$brand->setStatus($params[BrandInterface::STATUS]);
				$brand->save();

				
			}

			if (is_null($brandAmasty)) {
				$modelAmasty = $this->amastyBrandFactory->create();
				$modelAmasty->setData($params);
				$modelAmasty->save();
			} else {
				// Amasty Message Save
				$msgAmasty = "Successfully Update Brand Id " . $brandAmasty->getOptionSettingId();
				$brandAmasty->setTitle($params[OptionSettingInterface::TITLE]);
				$brandAmasty->setDescription($params[OptionSettingInterface::DESCRIPTION]);
				$brandAmasty->setFilterCode($params[OptionSettingInterface::FILTER_CODE]);
				$brandAmasty->setValue($params[OptionSettingInterface::VALUE]);
				$brandAmasty->setStoreId($params[OptionSettingInterface::STORE_ID]);
				$brandAmasty->save();
			}

		} catch (Exception $ex) {
			$msg = __FUNCTION__ . " Error : " . $ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}
		return $msg;

	}

	/**
	 *
	 */
	protected function getByPimId($id) {
		try {
			$result = NUll;
			if (empty($id)) {
				throw new StateException(__(
					'Parameter Id are empty !'
				));
			}
			$collection = $this->brandInterfaceFactory->create()->getCollection();
			$collection->addFieldToFilter(BrandInterface::PIM_ID, $id);

			if ($collection->getSize() > 0) {
				$result = $collection->getFirstItem();
			}
		} catch (Exception $ex) {
			$msg = __FUNCTION__ . " Error : " . $ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}
		return $result;
	}

	/**
	 * Amasty brand check by pim id
	 */
	public function getByAmastyPimId($id) {
		try {
			$result = NUll;
			if (empty($id)) {
				throw new StateException(__(
					'Parameter Id are empty !'
				));
			}
			$collection = $this->amastyBrandFactory->create()->getCollection();
			$collection->addFieldToFilter(AmastyBrandInterface::AMASTY_PIM_ID, $id);

			if ($collection->getSize() > 0) {
				$result = $collection->getFirstItem();
			}
		} catch (Exception $ex) {
			$msg = __FUNCTION__ . " Error : " . $ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}
		return $result;
	}

	/**
	 * Validate Product Data Value
	 * @param instance Integration Data Value
	 *
	 */
	protected function validateDataValue($integrationDataValue, $store = 0) {
		if (!$integrationDataValue->getId()) {
			throw new StateException(__("Theres No Data Value Id Exist"));
		}
		$data = $this->curl->jsonToArray($integrationDataValue->getDataValue());

		$result = NULL;
		try {
			$result[BrandInterface::PIM_ID]       = $this->validation->validateArray(BrandInterface::RESP_ID, $data);
			$result[BrandInterface::PIM_CODE]     = $this->validation->validateArray(BrandInterface::RESP_CODE, $data);
			$result[BrandInterface::COMPANY_CODE] = $this->validation->validateArray(BrandInterface::COMPANY_CODE, $data);
			$result[BrandInterface::TITLE]        = $this->validation->validateArray(BrandInterface::RESP_NAME, $data);
			$result[BrandInterface::DESCRIPTION]  = $this->validation->validateArray(BrandInterface::RESP_DESC, $data);

			// Amasty Field
			$result[AmastyBrandInterface::AMASTY_PIM_ID]   = $this->validation->validateArray(BrandInterface::RESP_ID, $data);
			$result[AmastyBrandInterface::AMASTY_PIM_CODE] = $this->validation->validateArray(BrandInterface::RESP_CODE, $data);
			$result[OptionSettingInterface::TITLE]         = $this->validation->validateArray(BrandInterface::RESP_NAME, $data);
			$result[OptionSettingInterface::DESCRIPTION]   = $this->validation->validateArray(BrandInterface::RESP_DESC, $data);

			$attrCollection = $this->optionHelper->initAttribute($this->optionHelper::SHOPBY_BRAND_ATTRIBUTE_CODE);
			$optionCollection = $this->optionHelper->getOptionValue($result[OptionSettingInterface::TITLE], $attrCollection->getAttributeId(), $store);

			$result[BrandInterface::DELETED]    = $this->validation->validateArray(BrandInterface::DELETED, $data);
			$result[BrandInterface::IS_ACTIVE]  = $this->validation->validateArray(BrandInterface::IS_ACTIVE, $data);
			$result[BrandInterface::IS_DEFAULT] = $this->validation->validateArray(BrandInterface::IS_DEFAULT, $data);

			$result[OptionSettingInterface::VALUE] = $optionCollection->getFirstItem()->getId();
			$result[OptionSettingInterface::FILTER_CODE] = $this->optionHelper::SHOPBY_BRAND_ATTRIBUTE_CODE;
			$result[OptionSettingInterface::STORE_ID] = $store;

			$result[BrandInterface::STATUS] = 1;

			if ($result[BrandInterface::IS_ACTIVE] == 0) {
				$result[BrandInterface::STATUS] = 0;
			}
			if ($result[BrandInterface::DELETED] == 1) {
				$result[BrandInterface::STATUS] = 0;
			}

		} catch (Exception $ex) {
			$msg = __FUNCTION__ . " Error : " . $ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}
		return $result;

	}

	/**
	 * Save Status and Message to Integration Data Value
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param string $message
	 * @param int $status
	 * @return $result
	 */
	protected function saveStatusMessage($data, $message, $status) {
		$data->setMessage($message);
		$data->setStatus($status);
		$this->integrationDataValueRepositoryInterface->save($data);
	}

	/**
	 * Update Jobs Status
	 * @param $jobId int
	 * @param $status int
	 * @param $msg string
	 * @throw new StateException
	 */
	protected function updateJobStatus($jobId, $status = 0, $msg = "") {

		if (empty($jobId)) {
			throw new NoSuchEntityException(__('Jobs ID doesn\'t exist'));
		}
		try {
			$jobs = $this->integrationJobRepositoryInterface->getById($jobId);
			$jobs->setStatus($status);
			if (!empty($msg)) {
				$jobs->setMessage($msg);

			}
			$this->integrationJobRepositoryInterface->save($jobs);
		} catch (\Exception $exception) {
			throw new StateException(
				__('Cannot Update Job Status! - ' . $exception->getMessage())
			);
		}
	}

	
}
