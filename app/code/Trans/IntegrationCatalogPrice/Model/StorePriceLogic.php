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

namespace Trans\IntegrationCatalogPrice\Model;


use Trans\Integration\Logger\Logger;

use Trans\IntegrationCatalogPrice\Api\StorePriceLogicInterface;
use Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface;
use Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterfaceFactory;
use Trans\IntegrationCatalogPrice\Api\StorePriceRepositoryInterface;
use Trans\IntegrationCatalogPrice\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalogPrice\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCatalogPrice\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalogPrice\Api\Data\IntegrationDataValueInterface;
use Trans\Integration\Helper\Validation;
use Trans\Integration\Helper\AttributeOption;
use Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface;
use Trans\Core\Helper\Data as CoreHelper;
Use Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationProductAttributeInterface;

use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\CouldNotSaveException;

class StorePriceLogic implements StorePriceLogicInterface {

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var CoreHelper
	 */
	protected $coreHelper;

	/**
	 * @var StorePriceRepositoryInterface
	 */
	protected $storePriceRepositoryInterface;
	
	/**
	 * @var storePriceInterfaceFactory
	 */
	protected $storePriceInterfaceFactory;

	/**
	 * @var \Trans\IntegrationCatalogPrice\Api\OnlinePriceRepositoryInterface
	 */
	protected $onlinePriceRepositoryInterface;

	/**
	 * @var \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterfaceFactory
	 */
	protected $onlinePriceInterfaceFactory;

	/**
	 * @var IntegrationJobRepositoryInterface
	 */
	protected $integrationJobRepositoryInterface;

	/**
	 * @var integrationDataValueRepositoryInterface
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * @var Validation
	 */
	protected $validation;

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute 
	 */
	protected $eavAttribute;

	/**
	 * @var ProductAttributeManagementInterface 
	 */
	protected $productAttributeManagement;	

	/**
	 * @var AttributeOption
	 */
	protected $attributeOptionHelper;

	/**
	 * @var \Magento\Eav\Model\Config
	 */
	protected $eavConfig;

		/**
	 * @var ProductAttributeInterfaceFactory 
	 */
	protected $productAttributeFactory;	

	/**
	 * @var ProductAttributeRepositoryInterface 
	 */
	protected $productAttributeRepository;

	/**
	 * @var IntegrationProductRepositoryInterface
	 */
	protected $integrationProductRepositoryInterface;

	/**
	 * @var ProductRepositoryInterface
	 */
	protected $productRepositoryInterface;

	/**
	 * @var IntegrationProductAttributeRepositoryInterface
	 */
	protected $integrationAttributeRepository;

	/**
	 * @var \Trans\IntegrationCatalogStock\Api\IntegrationStockInterface
	 */
	protected $integrationStock;

	/**
     * @var \Trans\IntegrationCatalogPrice\Helper\Data
     */
    protected $helperPrice;

	/**
	 * @param Logger $Logger
	 * @param StorePriceRepositoryInterface $storePriceRepositoryInterface
	 * @param StorePriceInterfaceFactory $StorePriceInterfaceFactory
	 * @param \Trans\IntegrationCatalogPrice\Api\OnlinePriceRepositoryInterface $onlinePriceRepositoryInterface
	 * @param \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterfaceFactory $onlinePriceInterfaceFactory
	 * @param IntegrationDataValueRepositoryInterface $IntegrationDataValueRepositoryInterface
	 * @param Validation $validation
	 * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
	 * @param ProductAttributeManagementInterface $productAttributeManagement
	 * @param AttributeOption $attributeOptionHelper
	 * @param \Magento\Eav\Model\Config $eavConfig
	 * @param ProductAttributeInterfaceFactory $productAttributeFactory
	 * @param ProductAttributeRepositoryInterface $productAttributeRepository
	 * @param IntegrationProductRepositoryInterface $productRepository
	 * @param CoreHelper $coreHelper
	 * @param ProductRepositoryInterface $productRepositoryInterface
	 * @param IntegrationProductAttributeRepositoryInterface productRepositoryInterface$integrationAttributeRepository
	 * @param \Trans\IntegrationCatalogStock\Api\IntegrationStockInterface $integrationStock
	 * @param \Trans\IntegrationCatalogPrice\Helper\Data $helperPrice
	 */
	public function __construct
	(
		Logger $logger
		,StorePriceRepositoryInterface $storePriceRepositoryInterface
		,StorePriceInterfaceFactory $storePriceInterfaceFactory
		,\Trans\IntegrationCatalogPrice\Api\OnlinePriceRepositoryInterface $onlinePriceRepositoryInterface
		,\Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterfaceFactory $onlinePriceInterfaceFactory
		,IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
		,Validation $validation
		,IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
		,\Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
		,ProductAttributeManagementInterface $productAttributeManagement
		,AttributeOption $attributeOptionHelper
		,EavConfig $eavConfig
		,ProductAttributeInterfaceFactory $productAttributeFactory
		,ProductAttributeRepositoryInterface $productAttributeRepository
		,IntegrationProductRepositoryInterface $integrationProductRepositoryInterface
		,CoreHelper $coreHelper
		,ProductRepositoryInterface $productRepositoryInterface
		,IntegrationProductAttributeRepositoryInterface $integrationAttributeRepository
		,\Trans\IntegrationCatalogStock\Api\IntegrationStockInterface $integrationStock
		,\Trans\IntegrationCatalogPrice\Helper\Data $helperPrice
	) {
		$this->logger                           		= $logger;
		$this->storePriceRepositoryInterface			= $storePriceRepositoryInterface;
		$this->storePriceInterfaceFactory				= $storePriceInterfaceFactory;
		$this->onlinePriceRepositoryInterface			= $onlinePriceRepositoryInterface;
		$this->onlinePriceInterfaceFactory				= $onlinePriceInterfaceFactory;
		$this->integrationDataValueRepositoryInterface	= $integrationDataValueRepositoryInterface;
		$this->validation 								= $validation;
		$this->integrationJobRepositoryInterface 		= $integrationJobRepositoryInterface;
		$this->eavAttribute		 					   	= $eavAttribute;
		$this->productAttributeManagement 			   	= $productAttributeManagement;
		$this->attributeOptionHelper				    = $attributeOptionHelper;
		$this->eavConfig                                = $eavConfig;
		$this->productAttributeFactory 				    = $productAttributeFactory;
		$this->productAttributeRepository				= $productAttributeRepository;
		$this->integrationProductRepositoryInterface	= $integrationProductRepositoryInterface;
		$this->coreHelper								= $coreHelper;
		$this->productRepositoryInterface				= $productRepositoryInterface;
		$this->integrationAttributeRepository			= $integrationAttributeRepository;
		$this->attrGroupGeneralInfoId				   	= IntegrationProductAttributeInterface::ATTRIBUTE_SET_ID;
		$this->attrGroupProductDetailId				   	= $this->integrationAttributeRepository->getAttributeGroupId(IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT);	
		$this->integrationStock                         = $integrationStock;
		$this->updateRepositoryInterface                = $helperPrice->getUpdateRepositoryInterface();
        $this->updateInterfaceFactory                   = $helperPrice->getUpdateInterfaceFactory();
		$this->versionManager                           = $helperPrice->getVersionManagerFactory();
		$this->productStaging                           = $helperPrice->getProductStagingInterface();

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_price.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
	 * Prepare Data Job
	 * @param array $channel
	 * @return mixed
	 * @throws NoSuchEntityException
	 * @throws StateException
	 */
	public function prepareData($jobs = []) {
		if (empty($jobs)) {
			throw new StateException(__(
				'Parameter Channel are empty !'
			));
		}
	
		$jobId     = $jobs->getFirstItem()->getId();
		$jobStatus = $jobs->getFirstItem()->getStatus();
		$status    = StorePriceInterface::STATUS_JOB_DATA;

		$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
		if (!$result) {
			throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
		}

		return $result;
	}

	/**
	 * Save Data To Magento & Mapping
	 * @param @channel array
	 * @param @data array
	 * @throws NoSuchEntityException
	 * @return mixed
	 */
	public function remapData($jobs=[],$data=[]){
		
		$i= 0 ;
		$dataValue = [];
		$params=[];

		if(!$jobs->getFirstItem()->getId()){
			throw new NoSuchEntityException(__('Error Jobs Datas doesn\'t exist'));
		}
		$jobId = $jobs->getFirstItem()->getId();
		$this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
		
		$attributeCode=[];
		$attributeId=[];
		
		$dataProduct = [];
		$storeQuery = [];
		
		try {
			if($data) {

				foreach($data->getData() as $n => $row){
					$dataValue[$i] = json_decode($row['data_value'], true);
					$value = $dataValue[$i];
					
					if(isset($value['sku'])){
						// Check Store Exist
						$this->logger->info('Before get inventory store ' . date('d-M-Y H:i:s'));
						$storeQuery[$i] = $this->storePriceRepositoryInterface->getInventoryStore($value['store_code']);
						$this->logger->info('After get inventory store ' . date('d-M-Y H:i:s'));

						if (!$storeQuery[$i]) {
							try {
								$this->logger->info('Before add new store ' . date('d-M-Y H:i:s'));
								$this->integrationStock->addNewSource($value['store_code']);
								$storeQuery[$i] = $this->storePriceRepositoryInterface->getInventoryStore($value['store_code']);
								$this->logger->info('After add new store ' . date('d-M-Y H:i:s'));
							} catch (CouldNotSaveException $e) {
								$this->updateDataValueStatus($row['id'],IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE,"Error : Store " . $value['store_code']." is Not  Available - ");
							}
						}

						if ($storeQuery[$i]) {
							$dataProduct[$value['sku']][$i] = $value;
							$dataProduct[$value['sku']][$i]['data_id'] = $row['id'];
						}
					}
					
					$i++;
				
				}
			} else {
				throw new StateException(
					__("No data found.")
				);
			}

		} catch (\Exception $exception) {
			$this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL,$exception->getMessage());
			throw new StateException(
				__("Error Validate SKU - ".$exception->getMessage())
			);
		}

		return $dataProduct;
	}

	/**
	 * Save Product
	 */
	public function save($jobs = [], $dataProduct = [])
	{
		$jobId = $jobs->getFirstItem()->getId();
		if(!$jobs->getFirstItem()->getId()){
			$message = 'Error Jobs Datas doesn\'t exist';
			$this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
			throw new NoSuchEntityException(__($message));
		}

		$jobId = $jobs->getFirstItem()->getId();

		$checkDataProduct = array_filter($dataProduct);

		if(empty($checkDataProduct)){
			$message = "Theres No SKU Key Available";
			$this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
			throw new StateException(
				__($message)
			);
		}
		$query = [];
		$productIds=[];
		$productMapingData=[];
		$index=0;
		$check = [];
		$productInterface=[];
		$msgError=[];
		foreach ($dataProduct as $sku => $data) {
			if (empty($sku)) {
				continue;
			}
			// $query[$index] = $this->integrationProductRepositoryInterface->loadDataByPimSku($sku);
			// if (is_null($query[$index])) {
			// 	if (isset($dataProduct[$sku])) {
			// 		foreach($dataProduct[$sku] as $failData) {
			// 			$this->updateDataValueStatus($failData['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE_MAPPING, "SKU Mapping not exist--->".print_r($sku,true));
			// 		}
			// 	}
			// 	$this->logger->info("SKU Mapping not exist--->".print_r($sku,true));
			// 	continue;
			// }
			
			// $productIds[$index] = $query[$index]->getMagentoEntityId();
			try {
				$this->logger->info('Before get product ' . date('d-M-Y H:i:s'));
				$productInterface[$index] = $this->productRepositoryInterface->get($sku);
				if($productInterface[$index] instanceof \Magento\Catalog\Api\Data\ProductInterface) {
					$product = $productInterface[$index];

					$weight = $product->getWeight();
					$isOwnCourier = strtolower($product->getData('own_courier'));
					$soldIn = strtolower($product->getData('sold_in'));
				}
				$this->logger->info('After get product ' . date('d-M-Y H:i:s'));
			} catch (\Exception $exception) {
				foreach ($data as $valueX) {
					$msgError[] = $exception->getMessage();
					$this->updateDataValueStatus($valueX['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE_MAPPING, $exception->getMessage());
					$this->logger->info("Error Save Mapping".__FUNCTION__." : ".$exception->getMessage());
					
				}
				continue;
			}

			$indexO = 0;
			$defaultPrice=0;
			
			try{
				foreach ($data as $value) {
					try {
						$this->logger->info('Before validate store price ' . date('d-M-Y H:i:s'));
						$productMapingData[$sku][$indexO] = $this->validateStorePrice($this->validateParams($value));
						$this->logger->info('After validate store price ' . date('d-M-Y H:i:s'));
					} catch (StateException $e) {
						$msgError[] = $e->getMessage();
						$this->updateDataValueStatus($value['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE, $e->getMessage());
						$this->logger->info("Error Save Attribute Magento ".__FUNCTION__." : ".$e->getMessage());
						continue;
					}
					
					$indexW = 0;
			
					$product->addData(['base_price_in_kg' => '']);
					$product->getResource()->saveAttribute($product, 'base_price_in_kg');

					$product->addData(['promo_price_in_kg' => '']);
					$product->getResource()->saveAttribute($product, 'promo_price_in_kg');

					foreach ($productMapingData[$sku][$indexO] as $priceType => $priceValue) {
						$productMapingData[$sku][$indexO][$indexW]['code'] = $priceType;
						$productMapingData[$sku][$indexO][$indexW]['price'] = $priceValue;

						if(($isOwnCourier == 'iya' || $isOwnCourier == 1) && $soldIn == 'kg') {
							$priceInKg = $priceValue;
							$priceValue = $weight * ($priceInKg/1000);

							$priceKgAttr = 'base_price_in_kg';
							if (strpos($priceType, 'promo_price') !== false) {
								$priceKgAttr = 'promo_price_in_kg';
							}

							if($priceInKg) {
								$product->addData([$priceKgAttr => $priceInKg]);
								$product->getResource()->saveAttribute($product, $priceKgAttr);
							}
						}

						if($priceType == 'default_price'){
							// $productInterface[$index]->setPrice($priceValue);
							$product->setPrice($priceValue);
							$product->getResource()->saveAttribute($product, 'price');

							$defaultPrice = $priceValue;
						} else {
							// $productInterface[$index]->addData(array($priceType => $priceValue));
							$product->addData(array($priceType => $priceValue));
							$product->getResource()->saveAttribute($product, $priceType);
						}
						$indexW++;
					}

					$isPriceInKg = 0;
					if(($isOwnCourier == 'iya' || $isOwnCourier == 1) && $soldIn == 'kg') {
						$isPriceInKg = 1;
					}
					
					$product->addData(['price_in_kg' => $isPriceInKg]);
					$product->getResource()->saveAttribute($product, 'price_in_kg');
					
					$indexO++;
					
					// try {
					// 	$this->logger->info('Before load data by sku ' . date('d-M-Y H:i:s'));
					// 	$querySkuOnline = $this->onlinePriceRepositoryInterface->loadDataBySku($value['sku']);
					// 	$this->logger->info('After load data by sku ' . date('d-M-Y H:i:s'));
					// 	$value['custom_status'] = false;
					// 	if ($value['is_exclusive'] == 1) {
					// 		if ($querySkuOnline) {
					// 			$value['custom_status'] = true;
					// 			if ($querySkuOnline->getIsExclusive() == 1) {
					// 				// update custom table
					// 				$this->logger->info('Before save online price custom ' . date('d-M-Y H:i:s'));
					// 				$this->saveOnlinePriceCustom($this->validateParams($value));
					// 				$this->logger->info('After save online price custom ' . date('d-M-Y H:i:s'));
					// 			}
					// 			if ($querySkuOnline->getIsExclusive() == 0) {
					// 				// delete staging content
					// 				$this->logger->info('Before delete staging update ' . date('d-M-Y H:i:s'));
					// 				$deleteStagingUpdate = $this->updateRepositoryInterface->get($querySkuOnline->getStagingId()); 
					// 				$this->updateRepositoryInterface->delete($deleteStagingUpdate);
					// 				$this->logger->info('After delete staging update ' . date('d-M-Y H:i:s'));

					// 				// update custom table
					// 				$this->logger->info('Before update save online price custom ' . date('d-M-Y H:i:s'));
					// 				$this->saveOnlinePriceCustom($this->validateParams($value));
					// 				$this->logger->info('After save online price custom ' . date('d-M-Y H:i:s'));
					// 			}
					// 		}
					// 		else {
					// 			$this->logger->info('Before save online price custom ' . date('d-M-Y H:i:s'));
					// 			$this->saveOnlinePriceCustom($this->validateParams($value));
					// 			$this->logger->info('After save online price custom ' . date('d-M-Y H:i:s'));
					// 		}
					// 	}

					// 	if ($value['is_exclusive'] == 0) {
					// 		if ($querySkuOnline) {
					// 			if (empty($querySkuOnline->getStagingId())) {
					// 				$this->logger->info('Before save online price custom ' . date('d-M-Y H:i:s'));
					// 				$scheduleF = $this->saveStagingUpdate($this->validateParams($value));
					// 				$value['staging_id'] = $scheduleF;
					// 				$this->logger->info('After save online price custom ' . date('d-M-Y H:i:s'));
					// 			}
					// 		}
					// 		else {
					// 			$this->logger->info('Before save staging update ' . date('d-M-Y H:i:s'));
					// 			$scheduleF = $this->saveStagingUpdate($this->validateParams($value));
					// 			$value['staging_id'] = $scheduleF;
					// 			$this->logger->info('After save staging update ' . date('d-M-Y H:i:s'));

					// 			$this->logger->info('Before save online price custom ' . date('d-M-Y H:i:s'));
					// 			$this->saveOnlinePriceCustom($this->validateParams($value));
					// 			$this->logger->info('After save online price custom ' . date('d-M-Y H:i:s'));
					// 		}
					// 	}
					// }
					// catch (\Exception $exception) {
					// 	$this->updateDataValueStatus($value['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE_MAPPING, $exception->getMessage());
					// 	// continue; //void if catch error
					// }
			
					try{
						$this->logger->info('Before save multi price ' . date('d-M-Y H:i:s'));
						$this->saveMultiPrice($this->validateParams($value));
						$this->logger->info('After save multi price ' . date('d-M-Y H:i:s'));
					} catch (\Exception $exception) {
						$msgError[] =$exception->getMessage();
						$this->updateDataValueStatus($value['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE_MAPPING, $exception->getMessage());
						$this->logger->info("Error Save Mapping".__FUNCTION__." : ".$exception->getMessage());
						continue;
					}

					$this->logger->info("SKU Catalog Price Updated --->".print_r($sku,true));
					$this->updateDataValueStatus($value['data_id'], IntegrationDataValueInterface::STATUS_DATA_SUCCESS, NULL);
				}
				
				// Save Product Price
				// $this->productRepositoryInterface->save($productInterface[$index]);
				// $this->productRepositoryInterface->save($product);
				// $product->save();
			} catch (\Exception $exception) {
				$msgError[] =$exception->getMessage();
				$this->updateDataValueStatus($value['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE, $exception->getMessage());
				$this->logger->info("Error Save Magento ".__FUNCTION__." : ".$exception->getMessage());
				continue;
			}
					
			$index++;
		}
		// Set Msg to Job
		$msg = NULL;
		$msgCheck = array_filter($msgError);
		$status = IntegrationJobInterface::STATUS_COMPLETE;
		if(!empty($msgCheck)){
			$msgError = array_unique($msgError);
			$msg = "Success with Error : ".implode("",$msgError);
			// $status = IntegrationJobInterface::STATUS_COMPLETE_WITH_ERROR;
		}
		$this->updateJobStatus($jobId,$status,$msg);
		return $productMapingData;
	}

	/**
     * Save to staging update
     * @param $param mixed
     * @return $data mixed
     * @throw logger error
     */
    protected function saveStagingUpdate($param)
    {
        try {
            $schedule = $this->updateInterfaceFactory->create();
            $schedule->setName($param['sku']);
            $schedule->setDescription($param['sku']); 
            $schedule->setStartTime($param['start_date']);
            $schedule->setEndTime($param['end_date']);
            $schedule->setIsCampaign(false);
            $stagingRepo = $this->updateRepositoryInterface->save($schedule);
            $this->versionManager->setCurrentVersionId($stagingRepo->getId());

            // save promo price by schedule
            $dataStoreCode = $stagingRepo->getId();

            $productS = $this->productRepositoryInterface->get($param['sku']);
            $productS->setPrice(50);
            $result = $this->productStaging->schedule($productS, $dataStoreCode);
            
        } catch (\Exception $e) {
            // $this->logger->error("<=End staging_update" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $dataStoreCode;
    }

	/**
	 * Validate Params
	 * 
	 * @param $param mixed
	 * @return $result mixed
	 */
	protected function validateParams($param){
		$result = [];
		try {
			$result['sku'] = $this->validation->validateArray(StorePriceInterface::SKU, $param);
			$result['store_code'] = $this->validation->validateArray(StorePriceInterface::SOURCE_CODE, $param);
			$result['normal_sell_price'] = $this->validation->validateArray(StorePriceInterface::NORMAL_SELLING_PRICE, $param);
			$result['online_sell_price'] = $this->validation->validateArray(StorePriceInterface::ONLINE_SELLING_PRICE, $param);
			$result['promo_sell_price'] = $this->validation->validateArray(StorePriceInterface::PROMO_SELLING_PRICE, $param);
			$result['store_attr_code'] = $result['store_code'];

			$result['pim_id'] = $this->validation->validateArray(StorePriceInterface::ID, $param);
			$result['pim_code'] = $this->validation->validateArray(StorePriceInterface::CODE, $param);
			$result['pim_product_id'] = $this->validation->validateArray(StorePriceInterface::PRODUCT_ID, $param);
			$result['pim_company_code'] = $this->validation->validateArray(StorePriceInterface::COMPANY_CODE, $param);

			$result['normal_purchase_price'] = $this->validation->validateArray('normal_purchase_price', $param);
			$result['promo_purchase_price'] = $this->validation->validateArray('promo_purchase_price', $param);

			$result['is_exclusive'] = $this->validation->validateArray('is_exclusive', $param);
			$result['start_date'] = $this->validation->validateArray('start_date', $param);
			$result['end_date'] = $this->validation->validateArray('end_date', $param);
			$result['modified_at'] = $this->validation->validateArray('modified_at', $param);

			$result['staging_id'] = $this->validation->validateArray('staging_id', $param);
			$result['custom_status'] = $this->validation->validateArray('custom_status', $param);
			// $result['store_attr_code'] = $this->createAttributeCode($result);

		} catch (\Exception $exception) {
			// $this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL,$exception->getMessage());
			throw new StateException(
				__("Error Save SKU - ".$exception->getMessage())
			);
		}
		
		return $result;
	}

	/**
	 * save custom table online price
	 *
	 * @param mixed $param
	 * @return bolean
	 */
	protected function saveOnlinePriceCustom($param) {
		try {
			
			$query = $this->onlinePriceInterfaceFactory->create();

			if ($param['custom_status']) {
				$query = $this->onlinePriceRepositoryInterface->loadDataBySku($param['sku']);
			}

			$query->setSku($param['sku']);
			$query->setOnlinePrice($param['online_sell_price']);
			$query->setModifiedAt($param['modified_at']);
			$query->setIsExclusive($param['is_exclusive']);

			if ($param['is_exclusive'] == 0) {
				$query->setStartDate($param['start_date']);
				$query->setEndDate($param['end_date']);
				$query->setStagingId($param['staging_id']);
			}
			$this->onlinePriceRepositoryInterface->save($query);
		} catch (\Exception $exception) {
			// $this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL,$exception->getMessage());
			throw new StateException(
				__($exception->getMessage())
			);
		}
	}

	/**
	 * Function for change product category reflect
	 *
	 * @param int $productId
	 * @param ProductInterface $product
	 * @param array $dataProduct
	 * @return bolean
	 */
	protected function saveMultiPrice($param) {
		try {
			$query = $this->storePriceRepositoryInterface->loadDataBySkuNStore($param['sku'],$param['store_code']);
			
			if(!$query){
				$query = $this->storePriceInterfaceFactory->create();
				$query->setStoreAttrCode($param['store_code']);
			}
			
			$query->setSourceCode($param['store_code']);		
			$query->setSku($param['sku']);
			$query->setPimId($param['pim_id']);
			$query->setPimCode($param['pim_code']);
			$query->setPimProductId($param['pim_product_id']);
			$query->setPimCompanyCode($param['pim_company_code']);

			$query->setNormalSellingPrice($param['normal_sell_price']);
			$query->setPromoSellingPrice($param['promo_sell_price']);
			$query->setOnlinePrice($param['online_sell_price']);

			$query->setNormalPurchasePrice($param['normal_purchase_price']);
			$query->setPromoPurchasePrice($param['promo_purchase_price']);
			$query->setStatus(1);
		
			$this->storePriceRepositoryInterface->save($query);
		} catch (\Exception $exception) {
			// $this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL,$exception->getMessage());
			throw new StateException(
				__($exception->getMessage())
			);
		}
	}

	/**
	 * Base Price & Promo Logic Validate & Create Attribute
	 * @param mixed $param
	 * @return array $result 
	 */
	protected function validateStorePrice($param){
		try {
			$sku = $param['sku'];
			$storeCode = strtolower($param['store_attr_code']);
			$basePriceCode = StorePriceLogicInterface::PRODUCT_ATTR_BASE_PRICE . $storeCode;
			$basePriceCodeId = $this->saveAttributeProduct($basePriceCode, $sku);

			$promoPriceCode = StorePriceLogicInterface::PRODUCT_ATTR_PROMO_PRICE . $storeCode;
			$promoPriceCodeId = $this->saveAttributeProduct($promoPriceCode, $sku);
		} catch (\Exception $exception) {
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}

		$result = [];
		
		try {
			$array['normal'] = (int)$param['normal_sell_price'];	
			$array['online'] = (int)$param['online_sell_price'];
			$array['promo'] = (int)$param['promo_sell_price'];
			$arrayPrice = array_map('intval',$array);

			$maxPrice = max($arrayPrice);

			$result["default_price"] = $maxPrice;
			$result[$basePriceCode] = $maxPrice;
			$result[$promoPriceCode] = 0;
			$check = 0;

			if ($arrayPrice['online'] != 0 && $arrayPrice['normal'] != 0) {
				$result[$basePriceCode] = $arrayPrice['online'];
				$result[$promoPriceCode] = 0;
				
				if($arrayPrice['normal'] > $arrayPrice['online']) {
					$result[$basePriceCode] = $arrayPrice['normal'];
					$result[$promoPriceCode] = $arrayPrice['online'];
				}
			}

			if ($arrayPrice['online'] == 0 && ($arrayPrice['normal'] != 0 && $arrayPrice['promo'] != 0)) {
				$result[$basePriceCode] = $arrayPrice['normal'];
				$result[$promoPriceCode] = 0;

				if ($arrayPrice['normal'] > $arrayPrice['promo']) {
					$result[$basePriceCode] = $arrayPrice['normal'];
					$result[$promoPriceCode] = $arrayPrice['promo'];
				}
			}

			if ($arrayPrice['normal'] == 0 && $arrayPrice['online'] != 0) {
				$result[$basePriceCode] = $arrayPrice['online'];
				$result[$promoPriceCode] = 0;				
			}

			if ($arrayPrice['online'] == 0 && $arrayPrice['normal'] == 0) {
				$result[$basePriceCode] = $arrayPrice['promo'];
				$result[$promoPriceCode] = 0;
			}
			
		} catch (\Exception $exception) {
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}
		
		return $result;
	}
	
	/** 
	 * Update Jobs Status
	 * @param $jobId int
	 * @param $status int
	 * @param $msg string
	 * @throw new StateException
	 */
	protected function updateJobStatus($jobId,$status=0,$msg=""){
		
		if(empty($jobId)){
			throw new NoSuchEntityException(__('Jobs ID doesn\'t exist'));
		}
		try {
			$jobs = $this->integrationJobRepositoryInterface->getById($jobId);
			$jobs->setStatus($status);
			if(!empty($msg)){
				$jobs->setMessage($msg);
				
			}
			$this->integrationJobRepositoryInterface->save($jobs);
		} catch (\Exception $exception) {
			throw new StateException(
				__('Cannot Update Job Status! - '.$exception->getMessage())
			);
		}
	}

	/** 
	 * Update Data Value Status
	 * @param $jobId int
	 * @param $status int
	 * @param $msg string
	 * @throw new StateException
	 */
	protected function updateDataValueStatus($dataId,$status=0,$msg=""){
		
		if(empty($dataId)){
			throw new NoSuchEntityException(__('Data ID doesn\'t exist'));
		}
		try {
			$query = $this->integrationDataValueRepositoryInterface->getById($dataId);
			$query->setStatus($status);
			if(!empty($msg)){
				$query->setMessage($msg);
				
			}
			$this->integrationDataValueRepositoryInterface->save($query);
		} catch (\Exception $exception) {
			throw new StateException(
				__('Cannot Update Data Value Status! - '.$exception->getMessage())
			);
		}
	}

	/**
	 * Create Store Attribute Code
	 * @return $storeAttrCode 
	 */
	protected function createAttributeCode($param=""){
		try {
			if(!isset($param['store_code'])||empty($param['store_code'])){
				return null;
			}

			$query = $this->storePriceRepositoryInterface->loadDataByStoreCode($param['store_code']);

			if(empty($query)){
				return $this->coreHelper->genRandAttrCode(StorePriceLogicInterface::STORE_ATTR_CODE_COUNT_CHAR);
			
			}
			$storeCode = $query->getStoreAttrCode();
		} catch (\Exception $exception) {
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}
		
		return $storeCode ;
	}

	/**
	 * Function for change product category reflect
	 *
	 * @param int $productId
	 * @param ProductInterface $product$normalSellPrice
	 * @param array $dataProduct
	 *
	 * @return bolean
	 */

	public function checkMultiPriceExist($sku ,$store_code) {
		try {
			$collection = $this->storePriceInterfaceFactory->create()->getCollection();
			$collection->addFieldToFilter(StorePriceInterface::SOURCE_CODE, $store_code);
			$collection->addFieldToFilter(StorePriceInterface::SKU, $sku);

			$result = NULL;
			if ($collection->getFirstItem()) {
				$result = $collection->getFirstItem()->getId();
			}
			
		} catch (\Exception $exception) {
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}
		return $result;
	}

	/**
     * Check Attribute Id Exist
     * @param string $attributeCode
     * @param string $sku
     * @return mixed
     */
	protected function saveAttributeProduct($attributeCode, $sku) {
		$attributeId 		= NULL;
		try {
			$attributeData = $this->eavAttribute->getCollection()->addFieldToFilter('attribute_code', $attributeCode);
			
			if ($attributeData->getSize() > 0) { 
				foreach($attributeData as $attributeDatas)
				{ 
					$attributeId = $attributeDatas['attribute_id'];
				}
			} else {
				$this->createAttributeProduct($attributeCode, $sku);
			}
		} catch (\Exception $exception) {
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}	

        return $attributeId;
	}

	/**
	 * Create New Attribute
	 * 
	 * @param $attributeCode string
	 * @param $sku string
	 * @return
	 */
	protected function createAttributeProduct($attributeCode="", $sku){
		try {
			
			$frontentInput    = StorePriceLogicInterface::INPUT_TYPE_FRONTEND_FORMAT_PRICE;
			$backendInput     = StorePriceLogicInterface::INPUT_TYPE_BACKEND_FORMAT_PRICE;
			
			$attributeValue = $this->productAttributeFactory->create();
			
			
			$attributeValue->setPosition(StorePriceLogicInterface::POSITION);
			$attributeValue->setApplyTo(StorePriceLogicInterface::APPLY_TO);
			$attributeValue->setIsVisible(StorePriceLogicInterface::IS_VISIBLE);
			
			$attributeValue->setScope(StorePriceLogicInterface::SCOPE);
			$attributeValue->setAttributeCode($attributeCode);
			$attributeValue->setFrontendInput($frontentInput);
			$attributeValue->setEntityTypeId(StorePriceLogicInterface::ENTITY_TYPE_ID);
			$attributeValue->setIsRequired(StorePriceLogicInterface::IS_REQUIRED);
			$attributeValue->setIsUserDefined(StorePriceLogicInterface::IS_USER_DEFINED);
			$attributeValue->setDefaultFrontendLabel($attributeCode);
			$attributeValue->setBackendType($backendInput);
			$attributeValue->setDefaultValue(0);
			$attributeValue->setIsUnique(StorePriceLogicInterface::IS_UNIQUE);

			// Smart OSC Required
			$attributeValue->setIsSearchable(StorePriceLogicInterface::IS_SEARCHBLE);
			$attributeValue->setIsFilterable(StorePriceLogicInterface::IS_FILTERABLE);
			$attributeValue->setIsComparable(StorePriceLogicInterface::IS_COMPARABLE);
			$attributeValue->setIsHtmlAllowedOnFront(StorePriceLogicInterface::IS_HTML_ALLOWED_ON_FRONT);
			$attributeValue->setIsVisibleOnFront(StorePriceLogicInterface::IS_VISIBLE_ON_FRONT);
			$attributeValue->setIsFilterableInSearch(StorePriceLogicInterface::IS_FILTERABLE_IN_SEARCH);
			$attributeValue->setUsedInProductListing(StorePriceLogicInterface::USED_IN_PRODUCT_LISTING);
			$attributeValue->setUsedForSortBy(StorePriceLogicInterface::USED_FOR_SORT_BY);
			$attributeValue->setIsVisibleInAdvancedSearch(StorePriceLogicInterface::IS_VISIBLE_IN_ADVANCED_SEARCH);
			$attributeValue->setIsWysiwygEnabled(StorePriceLogicInterface::IS_WYSIWYG_ENABLED);
			$attributeValue->setIsUsedForPromoRules(StorePriceLogicInterface::IS_USED_FOR_PROMO_RULES);
			// $attributeValue->setIsRequiredInAdminStore(StorePriceLogicInterface::IS_USED_FOR_PROMO_RULES);
			$attributeValue->setIsUsedInGrid(StorePriceLogicInterface::IS_USED_IN_GRID);
			$attributeValue->setIsVisibleInGrid(StorePriceLogicInterface::IS_VISIBLE_IN_GRID);
			$attributeValue->setIsFilterableInGrid(StorePriceLogicInterface::IS_FILTERABLE_IN_GRID);
			// $attributeValue->setIsPagebuilderEnable();
			
			$attributeValue->setIsUsedForPriceRules(StorePriceLogicInterface::IS_USED_FOR_PRICE_RULES);
			
			$this->productAttributeRepository->save($attributeValue);
			
			//Set Attribute to Attribute Set (Default)
			$this->productAttributeManagement->assign($this->attrGroupGeneralInfoId, $this->attrGroupProductDetailId, $attributeCode, StorePriceLogicInterface::SORT_ORDER);

			//Set Attribute to Attribute Set (attribute set base on SKU)
			$attributeSetId = $this->getAttributeSetId($sku);
			$this->logger->info($sku . ' = ' . $attributeSetId);
			if ($attributeSetId != $this->attrGroupGeneralInfoId) {
				try {
					$attrGroup = $this->integrationAttributeRepository->getAttributeGroupIdBySet(IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT, $attributeSetId);
					$this->productAttributeManagement->assign($attributeSetId, $attrGroup, $attributeCode, StorePriceLogicInterface::SORT_ORDER);

					$this->logger->info($sku . ' Assign attribute ' . $attributeCode . ' to attibute set ' . $attributeSetId . ' SUCCESS');
				} catch (\Exception $e) {
					$this->logger->info($e->getMessage());					
					$this->logger->info($sku . ' Assign attribute ' . $attributeCode . ' to attibute set ' . $attributeSetId . ' FAILED');
				}
			}
				
		} catch (\Exception $exception) {
			
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}	
	}

	/**
	 * get attribute set id
	 * 
	 * @param $sku string
	 * @return
	 */
	protected function getAttributeSetId($sku){
		try {
			$product = $this->productRepositoryInterface->get($sku);
			return $product->getAttributeSetId();
		} catch (\Exception $exception) {
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}	
	}
}