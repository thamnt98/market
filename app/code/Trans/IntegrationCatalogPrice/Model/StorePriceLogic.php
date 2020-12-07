<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>, Anan Fauzi <anan.fauzi@transdigital.co.id>
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

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterfaceFactory as ProductFactory;

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
     * @var ResourceConnection
     */
    protected $resourceConnection;
    
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    
    /**
     * 
     */
    protected $connection;
    
    /**
     * @var ProductFactory
     */
    protected $productFactory;

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
	 * @param ResourceConnection $resourceConnection
	 * @param ProductCollectionFactory $productCollectionFactory
	 * @param ProductFactory $productFactory
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
        ,ResourceConnection $resourceConnection
        ,ProductCollectionFactory $productCollectionFactory
        ,ProductFactory $productFactory
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
        $this->resourceConnection = $resourceConnection;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->connection = $this->resourceConnection->getConnection();
        $this->productFactory = $productFactory;

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
     * Get remap data value
     * 
     * @param array $data
     * @return array
     */
    public function getRemapDataValue($data)
    {
        $dataValue = [];
        foreach($data as $index => $value){
            $dataValue[] = json_decode($value['data_value'], true);
        }
        return $dataValue;
    }
    
    /**
     * Get source list
     *
     * @param array $data
     * @return $data
     */
    public function getSourceList($data)
    {
        $sourceList = [];
        foreach ($data as $index => $value) {
            if (isset($value['sku']) && $value['sku']) {
                if (in_array($value['store_code'], $sourceList) == false) {
                    $sourceList[] = $value['store_code'];
                }
            }
        }
        return $sourceList;
    }
    
    /**
     * Get new sources
     * 
     * @param array $sourcesList
     * @param array $sources
     * @return void
     */
    public function getNewSources($sourceList, $sources)
    {
        $result = [];
        if (empty($sources) == false) {
            $existSources = [];
            foreach ($sources as $index => $value) {
                $existSources[] = $value['source_code'];
            }
            foreach ($sourceList as $index => $value) {
                if (in_array($value, $existSources) == false) {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }
    
    /**
     * Save new sources
     * 
     * @param array $sources
     * @return void
     */
    public function saveNewSources($sources, $dataArray, $dataValue)
    {
        if (empty($sources) == false) {
            $failedSources = [];
            foreach($sources as $key => $value) {
                try {
                    $this->logger->info('Before add new store ' . date('d-M-Y H:i:s'));
                    $this->integrationStock->addNewSource($value);
                    $this->logger->info('After add new store ' . date('d-M-Y H:i:s'));
                } catch (\Exception $e) {
                    $failedSources[] = $value;
                }
            }
            if (empty($failedSources) == false) {
                foreach($dataArray as $index => $value) {
                    if (in_array($dataValue[$index]['store_code'], $failedSources)) {
                        $this->updateDataValueStatus(
                            $value['id'],
                            IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE,
                            "Error : Store " . $dataValue[$index]['store_code'] ." is Not  Available - "
                        );
                    }
                }
            }
        }
    }

    /**
     * Save Data To Magento & Mapping
     * @param @channel array
     * @param @data array
     * @throws NoSuchEntityException
     * @return mixed
     */
    public function remapData($jobs=[], $data=[])
    {   
        if(!$jobs->getFirstItem()->getId()){
            throw new NoSuchEntityException(__('Error Jobs Datas doesn\'t exist'));
        }
        
        $jobId = $jobs->getFirstItem()->getId();
        $this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
        
        $dataProduct = [];
        
        try {
            if($data) {
                $dataArray = $data->getData();
                $dataValue = $this->getRemapDataValue($dataArray);
                $sourceList = $this->getSourceList($dataValue);
                $sources = $this->connection->fetchAll($this->storePriceRepositoryInterface->getInventoryStoreListQuery($sourceList));
                $newSources = $this->getNewSources($sourceList, $sources);
                if (empty($newSources) == false) {
                    $this->saveNewSources($newSources, $dataArray, $dataValue);
                }
                foreach($dataArray as $index => $value)
                {
                    $dataProduct[$dataValue[$index]['sku']][$index] = $dataValue[$index];
                    $dataProduct[$dataValue[$index]['sku']][$index]['data_id'] = $value['id'];
                }
            }
        } catch (\Exception $exception) {
            $this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL,$exception->getMessage());
            throw new StateException(__("Error Validate SKU - ".$exception->getMessage()));
        }
        
        return $dataProduct;
    }
    
    /**
     * Get product by multiple sku
     */
    public function getProductByMultipleSku($skuList)
    {
        $result = [];
        if(empty($skuList) == false) {
            $this->logger->info('Before get product ' . date('d-M-Y H:i:s'));
            $collection = $this->productCollectionFactory->create()->addFieldToFilter('sku',['in'=>$skuList]);
            $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['entity_id','sku']);
            $result = $collection->getItems();
            $this->logger->info('After get product ' . date('d-M-Y H:i:s'));
        }
        return $result;
    }
    
    /**
     * get base price on sku
     */
    public function getBasePriceOnSku($productMappingData, $sku)
    {
        $result = 0;
        if (isset($productMappingData[$sku]['price'])) {
            foreach($productMappingData[$sku]['price'] as $index => $value)
            {
                if(strpos($index, 'base_price') !== false) {
                    if((int)$value){
                        $result = (int)$productMappingData[$sku]['price'][$index];
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * Is promo price on sku set
     */
    public function isPromoPriceOnSkuSet($productMappingData, $sku)
    {
        $result = false;
        if (isset($productMappingData[$sku]['price'])) {
            foreach($productMappingData[$sku]['price'] as $index => $value)
            {
                if(strpos($index, 'promo_price') !== false) {
                    if((int)$value){
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }
    
    /** Get promo price on sku
     *
     */
    public function getPromoPriceOnSku($productMappingData, $sku)
    {
        $result = 0;
        if (isset($productMappingData[$sku]['price'])) {
            foreach($productMappingData[$sku]['price'] as $index => $value)
            {
                if(strpos($index, 'promo_price') !== false) {
                    if((int)$value){
                        $result = (int)$productMappingData[$sku]['price'][$index];
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * Get default price on sku
     */
    public function getDefaultPriceOnSku($productMappingData, $sku)
    {
        $result = 0;
        if (isset($productMappingData[$sku]['price'])) {
            foreach($productMappingData[$sku]['price'] as $index => $value)
            {
                if(strpos($index, 'default_price') !== false) {
                    if((int)$value){
                        $result = (int)$productMappingData[$sku]['price'][$index];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Save Product
     */
    public function save($jobs = [], $dataProduct = [])
    {
        if(!$jobId = $jobs->getFirstItem()->getId()) {
            $message = 'Error Jobs Datas doesn\'t exist';
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
            throw new NoSuchEntityException(__($message));
        }
        
        if(empty($dataProduct)){
            $message = "Theres No SKU Key Available";
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
            throw new StateException(__($message));
        }
        
        $msgError=[];
        //var_dump($dataProduct);die();
        $skuList = [];
        $dataValueList = [];
        foreach ($dataProduct as $sku => $data) {
            $skuList[] = $sku;
            foreach($data as $key => $val) {
                $dataValueList[] = $val;
            }
        }
        
        $productMappingData=[];
        foreach($dataValueList as $index => $value) {
            $this->logger->info('Before validate store price ' . date('d-M-Y H:i:s'));
            $productMappingData[$value['sku']]['price'] = $this->validateStorePrice($this->validateParams($value));
            $this->logger->info('After validate store price ' . date('d-M-Y H:i:s'));
            $this->logger->info('Before save multi price ' . date('d-M-Y H:i:s'));
            $this->saveMultiPrice($this->validateParams($value));
            $this->logger->info('After save multi price ' . date('d-M-Y H:i:s'));
            $this->logger->info("SKU Catalog Price Updated --->".print_r($value['sku'],true));
        }

        $productInterfaces = $this->getProductByMultipleSku($skuList);
        $resource = $this->productFactory->create()->getResource();
        foreach($productInterfaces as $index => $product) {
            if($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
                $product->addData(['base_price_in_kg' => '']);
                $resource->saveAttribute($product, 'base_price_in_kg');
                $product->addData(['promo_price_in_kg' => '']);
                $resource->saveAttribute($product, 'promo_price_in_kg');
                $weight = $product->getWeight();
                $isOwnCourier = strtolower($product->getData('own_courier'));
                $soldIn = strtolower($product->getData('sold_in'));
                $isPriceInKg = 0;
                if(($isOwnCourier == 'iya' || $isOwnCourier == 1) && $soldIn == 'kg') {
                    $isPriceInKg = 1;
                    $priceInKg = $this->getBasePriceOnSku($productMappingData, $product->getSku());
                    $priceKgAttr = 'base_price_in_kg';
                    if ($this->isPromoPriceOnSkuSet($productMappingData, $product->getSku())) {
                        $priceKgAttr = 'promo_price_in_kg';
                        $promoPrice = $this->getPromoPriceOnSku($productMappingData, $product->getSku());
                        $priceInKg = ($promoPrice)? $promoPrice : $priceInKg;
                    }
                    $priceValue = $weight * ($priceInKg/1000);
                    if($priceInKg) {
                        $product->addData([$priceKgAttr => $priceInKg]);
                        $resource->saveAttribute($product, $priceKgAttr);
                    }
                }
                $product->addData(['price_in_kg' => $isPriceInKg]);
                //$resource->saveAttribute($product, 'price_in_kg');
                $product->setPrice($this->getDefaultPriceOnSku($productMappingData, $product->getSku()));
                //$resource->saveAttribute($product, 'price');
            }
        }
        
        $msg = NULL;
        $msgCheck = array_filter($msgError);
        $status = IntegrationJobInterface::STATUS_COMPLETE;
        if(!empty($msgCheck)){
            $msgError = array_unique($msgError);
            $msg = "Success with Error : ".implode("",$msgError);
        }
        $this->updateJobStatus($jobId,$status,$msg);
        return $productMappingData;
        
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
			$qry= $this->storePriceRepositoryInterface->loadQueryBySkuNStore($param['sku'],$param['store_code']);
			$qry = $this->connection->fetchRow($qry);
            $query = $this->storePriceInterfaceFactory->create();
            if($qry){
				$query->setId($qry['id']);
			}
			$query->setStoreAttrCode($param['store_code']);
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