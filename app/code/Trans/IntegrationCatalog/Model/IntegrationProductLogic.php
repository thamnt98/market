<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory as ProductOptionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Filesystem\Io\File;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterfaceFactory;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterfaceFactory;
use Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationProductLogicInterface;
use Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface;
use Trans\IntegrationCategory\Api\IntegrationCategoryRepositoryInterface;
use Trans\Integration\Helper\AttributeOption;
use Trans\Integration\Helper\Curl;
use Trans\Integration\Helper\Validation;
use Trans\Integration\Logger\Logger;

use Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationProductAttributeInterface;

use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;

class IntegrationProductLogic implements IntegrationProductLogicInterface {
	/**
	 * @var IntegrationJobRepositoryInterface
	 */
	protected $integrationJobRepositoryInterface;

	/**
	 * @var IntegrationDataValueRepositoryInterface
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * @var IntegrationCategoryRepositoryInterface
	 */
	protected $integrationCategoryRepositoryInterface;

	/**
	 * @var IntegrationProductRepositoryInterface
	 */
	protected $integrationProductRepositoryInterface;

	/**
	 * @var IntegrationProductInterfaceFactory
	 */
	protected $integrationProductInterfaceFactory;

	/**
	 * @var ProductRepositoryInterface
	 */
	protected $productRepositoryInterface;

	/**
	 * @var ProductInterfaceFactory
	 */
	protected $productInterfaceFactory;

	/**
	 * @var AttributeOption
	 */
	protected $attributeOption;

	/**
	 * @var Validation
	 */
	protected $validation;

	/**
	 * @var Curl
	 */
	protected $curl;

	/**
	 * @var ProductOptionFactory
	 */
	protected $productOptionFactory;

	/**
	 * @var ProductModel
	 */
	protected $productModel;

	/**
	 * @var DirectoryList
	 */
	protected $directoryList;

	/**
	 * @var File
	 */
	protected $file;

	/**
	 * @var array
	 */
	protected $result;

	/**
	 * @var CategoryLinkRepositoryInterface
	 */
	protected $categoryLinkRepositoryInterface;

	/**
	 * @var CategoryLinkManagementInterface
	 */
	protected $categoryLinkManagementInterface;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory
	 */
	protected $configurableAttribute;

	/**
	 * @var IntegrationProductAttributeRepositoryInterface
	 */
	protected $integrationAttributeRepository;

	/**
	 * @var Attribute Group For General Information
	 */
	protected $attrGroupGeneralInfoId;

	/**
	 * @var ProductAttributeInterfaceFactory 
	 */
	protected $productAttributeFactory;	

	/**
	 * @var ProductAttributeRepositoryInterface 
	 */
	protected $productAttributeRepository;

	/**
	 * @var ProductAttributeManagementInterface 
	 */
	protected $productAttributeManagement;	

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute 
	 */
	protected $eavAttribute;

	/**
	 * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute
	 */
	protected $attributeResource;

	/**
	 * @var \Trans\IntegrationCatalog\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Trans\IntegrationEntity\Model\IntegrationProductAttributeRepository
	 */
	protected $attributeSet;

	/**
	 * @var \Trans\Brand\Api\BrandRepositoryInterface
	 */
	protected $brandRepository;

	/**
	 * @var \Trans\Brand\Api\Data\BrandInterface
	 */
	protected $brandFactory;

	/**
	 * @param Logger $Logger
	 * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttribute
	 * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param IntegrationCategoryRepositoryInterface $integrationCategoryRepositoryInterface
	 * @param IntegrationProductRepositoryInterface $integrationProductRepositoryInterface
	 * @param IntegrationProductInterfaceFactory $integrationProductInterfaceFactory
	 * @param ProductRepositoryInterface $productRepositoryInterface
	 * @param ProductInterfaceFactory $productInterfaceFactory
	 * @param AttributeOption $attributeOption
	 * @param Validation $validation
	 * @param Curl $curl
	 * @param ProductOptionFactory $productOptionFactory
	 * @param ProductModel $productModel
	 * @param DirectoryList $directoryList
	 * @param File $file
	 * @param CategoryLinkRepositoryInterface $categoryLinkRepositoryInterface
	 * @param CategoryLinkManagementInterface $categoryLinkManagementInterface
	 * @param IntegrationProductAttributeRepositoryInterface
	 * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute $attributeResource
	 * @param \Trans\IntegrationCatalog\Helper\Data $dataHelper
	 * @param \Trans\IntegrationEntity\Model\IntegrationProductAttributeRepository $attributeSet
	 * @param \Trans\Brand\Api\BrandRepositoryInterface $brandRepository
	 * @param \Trans\Brand\Api\Data\BrandInterface $brandFactory
	 */
	public function __construct
	(
		Logger $logger,
		\Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttribute,
		IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
		IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
		IntegrationCategoryRepositoryInterface $integrationCategoryRepositoryInterface,
		IntegrationProductRepositoryInterface $integrationProductRepositoryInterface,
		IntegrationProductInterfaceFactory $integrationProductInterfaceFactory,
		ProductRepositoryInterface $productRepositoryInterface,
		ProductInterfaceFactory $productInterfaceFactory,
		AttributeOption $attributeOption,
		Validation $validation,
		Curl $curl,
		ProductOptionFactory $productOptionFactory,
		ProductModel $productModel,
		DirectoryList $directoryList,
		File $file,
		\Magento\Eav\Model\Config $eavConfig,
		CategoryLinkRepositoryInterface $categoryLinkRepositoryInterface,
		CategoryLinkManagementInterface $categoryLinkManagementInterface,
		IntegrationDataValueInterfaceFactory $integrationDataValueInterfaceFactory,
		IntegrationProductAttributeRepositoryInterface $integrationAttributeRepository,
		\Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
		ProductAttributeInterfaceFactory $productAttributeFactory,
		ProductAttributeRepositoryInterface $productAttributeRepository,
		ProductAttributeManagementInterface $productAttributeManagement,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute $attributeResource,
		\Trans\IntegrationCatalog\Helper\Data $dataHelper,
		\Trans\IntegrationEntity\Model\IntegrationProductAttributeRepository $attributeSet,
		\Trans\Brand\Api\BrandRepositoryInterface $brandRepository,
		\Trans\Brand\Api\Data\BrandInterfaceFactory $brandFactory
	) {
		$this->logger = $logger;
		$this->configurableAttribute = $configurableAttribute;
		$this->integrationJobRepositoryInterface = $integrationJobRepositoryInterface;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
		$this->integrationCategoryRepositoryInterface = $integrationCategoryRepositoryInterface;
		$this->integrationProductRepositoryInterface = $integrationProductRepositoryInterface;
		$this->integrationProductInterfaceFactory = $integrationProductInterfaceFactory;
		$this->productRepositoryInterface = $productRepositoryInterface;
		$this->productInterfaceFactory = $productInterfaceFactory;
		$this->attributeOption = $attributeOption;
		$this->attributeResource = $attributeResource;
		$this->validation = $validation;
		$this->curl = $curl;
		$this->productOptionFactory = $productOptionFactory;
		$this->productModel = $productModel;
		$this->directoryList = $directoryList;
		$this->file = $file;
		$this->eavConfig = $eavConfig;
		$this->categoryLinkRepositoryInterface = $categoryLinkRepositoryInterface;
		$this->categoryLinkManagementInterface = $categoryLinkManagementInterface;
		$this->integrationDataValueInterfaceFactory = $integrationDataValueInterfaceFactory;
		$this->integrationAttributeRepository = $integrationAttributeRepository;
		$this->dataHelper = $dataHelper;
		$this->attributeSet = $attributeSet;
		$this->brandRepository = $brandRepository;
		$this->brandFactory = $brandFactory;

		// $this->attrGroupGeneralInfoId = $this->integrationAttributeRepository->getAttributeGroupId(IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_GENERAL);
		$this->attrGroupGeneralInfoId = IntegrationProductInterface::ATTRIBUTE_SET_ID;
		$this->attrGroupProductDetailId = $this->integrationAttributeRepository->getAttributeGroupId(IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT);

		$this->eavAttribute = $eavAttribute;
		$this->productAttributeFactory = $productAttributeFactory;
		$this->productAttributeRepository = $productAttributeRepository;
		$this->productAttributeManagement = $productAttributeManagement;

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_product.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
	 * Update Job data
	 * @param object $datas
	 * @param int $status
	 * @param string $msg
	 * @throw error 
	 */
	protected function updateJobData($jobId=0,$status="" , $msg=""){
	
		if ($jobId<1) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}
		try{
			$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
			$dataJobs->setStatus($status);
			if(!empty($msg)){
				$dataJobs->setMessages($msg);
			}	
			$this->integrationJobRepositoryInterface->save($dataJobs);
		}catch (\Exception $exception) {
			$this->logger->info(__FUNCTION__."------ ERROR ".$exception->getMessage());
			throw new CouldNotSaveException(__("Error : Cannot Update Job data - ".$exception->getMessage()));
		}
	}

	/**
	 * @param array $channel
	 * @return mixed
	 * @throws NoSuchEntityException
	 * @throws StateException
	 */
	public function prepareData($channel = []) {
		if (empty($channel)) {
			throw new StateException(__(
				'Parameter Channel are empty !'
			));
		}
		try{
			$jobs      = $channel['jobs'];
			$jobId     = $jobs->getFirstItem()->getId();
			$jobStatus = $jobs->getFirstItem()->getStatus();
			
			$status    = IntegrationProductInterface::STATUS_JOB;
			
			$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
			if ($result->getSize() < 1) {
				throw new NoSuchEntityException(__('Result Data Value Are Empty!!'));
			}
			
		} catch (\Exception $exception) {
			$this->logger->info(__FUNCTION__."------ ERROR ".$exception->getMessage());
			throw new StateException(__($exception->getMessage()));
		}

		return $result;
	}

	/**
	 * @param Object Data Value Product
	 */
	public function saveProduct($datas) {
		
		$checkData = array_filter($datas->getData());
		if (empty($checkData)) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}
		
		$jobId    = $datas->getFirstItem()->getJbId();
		$this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);

		try {
			$i = 0;
			$objStatusMessage 	= [];
			$dataProduct		= [];
			$saveMagento		= [];
			$deleted			= [];
			$resultDataConfigurable=[];

			foreach ($datas as $data) {
			
				$dataProduct[$i] = $this->validateProductDataValue($data);
				
				if(empty($dataProduct[$i]['sku']) || is_null($dataProduct[$i]['sku'])){
					$msg="Error , SKU ARE EMPTY !!";
					$this->logger->info($msg);
					$this->saveStatusMessage($data, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
					continue;
				}
				if(empty($dataProduct[$i]['pim_id']) || is_null($dataProduct[$i]['pim_id'])){
					$msg="Error , PIM ID ARE EMPTY !!";
					$this->logger->info($msg);
					$this->saveStatusMessage($data, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
					continue;
				}
				
				if ($dataProduct[$i]['is_deleted']<1) {
					
					try {
						$saveMagento[$i] = $this->saveDataToMagento($dataProduct[$i],$data);
					} catch (StateException $e) {
						$this->logger->info($e->getMessage());
						continue;
					} catch (\Exception $e) {
						$this->logger->info($e->getMessage());
						continue;
					}
					
					if($saveMagento[$i]['entity_id']>0){
						$integrationProductMap[$i] = $this->saveDataToIntegrationProduct($saveMagento[$i]['entity_id'],$dataProduct[$i],$data);
					
						if($saveMagento[$i]['is_configurable']>0){
							$this->createIntegrationProductMapConfigurable($dataProduct[$i],$data);
							$this->logger->info("Created configurable ".print_r($dataProduct[$i]['sku'],true));
						}else{
							$this->logger->info("Theres no configurable ".print_r($dataProduct[$i]['sku'],true));
						}
					}
				}else{
					$this->deleteProduct($dataProduct[$i],$data);
				
				}
				$this->saveStatusMessage($data,NULL,IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
				// return $dataProduct[$i];
				$i++;
				
			}
		} catch (\Exception $exception) {
			$msg = __FUNCTION__." ERROR : ".$exception->getMessage();
			$this->logger->info($msg);
			$this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL);
			
			throw new StateException(__($exception->getMessage()));
		}

		$this->updateJobData($jobId,IntegrationJobInterface::STATUS_COMPLETE);
		return true;
	}

	/**
	 * Validate Product Data Value
	 * @param instance Integration Data Value
	 * 
	 */
	protected function validateProductDataValue($integrationDataValue){
		if(!$integrationDataValue->getId()){
			throw new StateException(__("Theres No Data Value Id Exist"));
		}
		$dataProduct       = $this->curl->jsonToArray($integrationDataValue->getDataValue());
		// $integrationDataId[$i] = $data->getId();
		$result = NULL;
		try{
			$result['is_deleted'] 	 	= $this->validation->validateArray(IntegrationProductInterface::DELETED, $dataProduct);
			$result['is_active'] 	 	= $this->validation->validateArray(IntegrationProductInterface::IS_ACTIVE, $dataProduct);

			$result['sku'] 				= $this->validation->validateArray(IntegrationProductInterface::SKU, $dataProduct);
			$result['product_name']		= $this->validation->validateArray(IntegrationProductInterface::NAME, $dataProduct);
			$result['category_id']		= $this->validation->validateArray(IntegrationProductInterface::CTGID, $dataProduct);
			$result['price']	 		= $this->validation->validateArray(IntegrationProductInterface::PRICE, $dataProduct);

			$result['weight']	 		= $this->validation->validateArray(IntegrationProductInterface::WEIGHT, $dataProduct);
			$result['height']	 		= $this->validation->validateArray(IntegrationProductInterface::HEIGHT, $dataProduct);
			$result['length']	 		= $this->validation->validateArray(IntegrationProductInterface::LENGTH, $dataProduct);
			$result['width']	 		= $this->validation->validateArray(IntegrationProductInterface::WIDTH, $dataProduct);
			

			$result['active']	 		= $this->validation->validateArray(IntegrationProductInterface::IS_ACTIVE, $dataProduct);
			$result['short_desc']		= $this->validation->validateArray(IntegrationProductInterface::SHORT_DESC, $dataProduct);
			$result['long_desc']		= $this->validation->validateArray(IntegrationProductInterface::LONG_DESC, $dataProduct);
			$result['data_attributes']	= $this->validation->validateArray(IntegrationProductInterface::ATTRIBUTES, $dataProduct);

			$result['pim_id'] 			= $this->validation->validateArray(IntegrationProductInterface::ID, $dataProduct);
			
			$result[IntegrationProductInterface::COMPANY_CODE] = $this->validation->validateArray(IntegrationProductInterface::COMPANY_CODE, $dataProduct);

			$result[IntegrationProductInterface::PRODUCT_TYPE]	= IntegrationProductInterface::PRODUCT_TYPE_SIMPLE_VALUE;
			if(isset($dataProduct[IntegrationProductInterface::PRODUCT_TYPE]) && (strtolower($dataProduct[IntegrationProductInterface::PRODUCT_TYPE])==IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_LABEL)){
				$result[IntegrationProductInterface::PRODUCT_TYPE]	=IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_VALUE;
			}

			$result[IntegrationProductInterface::CATALOG_TYPE]	=IntegrationProductInterface::CATALOG_TYPE_SIMPLE_VALUE;
			if(!empty($result[IntegrationProductInterface::PRODUCT_TYPE]) && ($result[IntegrationProductInterface::PRODUCT_TYPE]==IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_VALUE)){
				$result[IntegrationProductInterface::CATALOG_TYPE]	=IntegrationProductInterface::CATALOG_TYPE_DIGITAL_VALUE;
			}
			

			// get magento category id
			$result['m_category_id'] = $this->getCategoryId($result['category_id']);

			// Item Id
			$result['item_id'] = $this->getItemId($result['sku'] );

			//BARCODE
			$result['barcode'] = $this->validation->validateArray(IntegrationProductInterface::BARCODE, $dataProduct);

			//BRAND
			$result[IntegrationProductInterface::BRAND] = $this->validation->validateArray(IntegrationProductInterface::BRAND, $dataProduct);
			$result[IntegrationProductInterface::BRAND_CODE] = $this->validation->validateArray(IntegrationProductInterface::BRAND_CODE, $dataProduct);
			$result[IntegrationProductInterface::BRAND_NAME] = $this->validation->validateArray(IntegrationProductInterface::BRAND_NAME, $dataProduct);

		} catch (Exception $ex) {
			$msg = __FUNCTION__." Validate Data : ".$ex->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			throw new StateException(__($msg));
		}
		
		return $result;

	}

	/**
	 * @param $dataProduct
	 * @param $integrationDataValue
	 * @return \Magento\Catalog\Api\Data\ProductInterface
	 * @throws CouldNotSaveException
	 */
	protected function saveDataToMagento($dataProduct, $integrationDataValue) 
	{
		$isConfigurable = 0;	
		try {
			// Check SKU
			$productMapingData = $this->integrationProductRepositoryInterface->loadDataByPimSku($dataProduct['sku']);
			$productId = null;
			$update = false;

			if ($productMapingData) {
				$productId = $productMapingData->getMagentoEntityId();
			}
		
			if($productId) {
				try {
					$product = $this->productRepositoryInterface->getById($productId);
					$update = true;
				} catch (NoSuchEntityException $e) {
					$product = $this->productInterfaceFactory->create();
				}
			} else {
				// Create New
				$product = $this->productInterfaceFactory->create();
			}

			if(!$update) {
				// Set sku
				$product->setSku($dataProduct['sku']); 
				// Set Url
				$product->setUrlKey(
					$this->changeUrlKeyChildProduct(
						$dataProduct['product_name'], 
						$dataProduct['sku']
					)
				);
				// Base Price
				$product->setPrice($dataProduct['price']);
			}
		} catch (Exception $ex) {
			$msg = __FUNCTION__." Validate UPDATE/INSERT : ".$ex->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			throw new StateException(__($msg));
		}
		

		try {
			
			$checkCat = array_filter($dataProduct['m_category_id']);
			if(!empty($checkCat)){
				$product->setCategoryIds($dataProduct['m_category_id']);
			}
			
			$product->setName($dataProduct['product_name']); // Name of Product

			$attributeSet = $this->prepareAttributeSet($dataProduct);
			
			$product->setAttributeSetId($attributeSet); // Attribute set id

			// weight of product
			if(!empty($dataProduct['weight'])){
				$product->setWeight($dataProduct['weight']);
			}
			// height of product
			if(!empty($dataProduct['height'])){
				$product->setProductHeight($dataProduct['height']);
			}
			// length of product
			if(!empty($dataProduct['length'])){
				$product->setProductLength($dataProduct['length']);
			}
			// width of product
			if(!empty($dataProduct['width'])){
				$product->setProductWidth($dataProduct['width']);
			}

			$visibility = IntegrationProductInterface::VISIBILITY_BOTH; // visibility of product (catalog, search)
			if ($this->integrationProductRepositoryInterface->checkPosibilityConfigurable($dataProduct['sku'])) {
				$isConfigurable = 1;
				$visibility = IntegrationProductInterface::VISIBILITY_NOT_VISIBLE; // Not visible
			}
			
			$product->setVisibility($visibility); // Not visible
			
			$product->setTaxClassId(0); // Tax class id
			if(isset($dataProduct['product_type']) && !empty($dataProduct['product_type'])){
				$this->logger->info($dataProduct['sku']." ----".strtoupper($dataProduct['product_type'])." ---");
				$product->setTypeId($dataProduct['product_type']); // type of product (simple/virtual/downloadable/configurable)
			}

			$product->setStockData(
				array(
					'use_config_manage_stock' => 0,
					'manage_stock'            => 1,
					'is_in_stock'             => 1,
					'qty'                     => 1,
				)
			);

			$status = IntegrationProductInterface::STATUS_DISABLED;
			if ((int) $dataProduct['is_active'] == 1) {
				$status = IntegrationProductInterface::STATUS_ENABLED;
			}
			
			$product->setStatus($status);
						
			if(!empty($dataProduct['short_desc'])){
				$product->setShortDescription($dataProduct['short_desc']);
			}
			if(!empty($dataProduct['long_desc'])){
				$product->setDescription($dataProduct['long_desc']);
			}

			$product->setDescription($dataProduct['long_desc']);

			// Set ProductType
			$this->logger->info($dataProduct['sku']." ---- Catalog = ".strtoupper($dataProduct[IntegrationProductInterface::CATALOG_TYPE])." ---");
			$product->setTypeId($dataProduct[IntegrationProductInterface::CATALOG_TYPE]);
			$this->logger->info($dataProduct['sku']." ---- Product = ".strtoupper($dataProduct[IntegrationProductInterface::PRODUCT_TYPE])." ---");
			$product->setProductType($dataProduct[IntegrationProductInterface::PRODUCT_TYPE]);

			// Create Attribute Item Code
			$dataAttributes = $this->addAttributeSellingForConfigurable($dataProduct);
			
			// Set Attributes
			if(isset($dataAttributes['attributes']) && is_array($dataAttributes['attributes'])){
				$checkAttr  = array_filter($dataAttributes['attributes']);
				if (!empty($checkAttr)) {
					foreach ($dataAttributes['attributes'] as $rowAttr) {
						if(isset($rowAttr['attribute_code']) && !empty($rowAttr['attribute_code'])){
							if($rowAttr['attribute_code']!=IntegrationProductInterface::PRODUCT_TYPE){
								$this->saveAttributeDataByType($product,$rowAttr['attribute_code'],$rowAttr['attribute_value']);
							}
						}
					}
				}
			}

			// BARCODE
			if(isset($dataProduct['barcode'])) {
				try {
					$this->saveAttributeBarcode(IntegrationProductInterface::BARCODE, $dataProduct['barcode']);
					$product->setData(IntegrationProductInterface::BARCODE, $dataProduct['barcode']);
				} catch (\Exception $e) {
					$this->logger->info('Error create/set barcode. SKU = ' . $product->getSku() . ' Barcode = ' . $dataProduct['barcode'] . ' Msg: ' . $e->getMessage());
				} catch (StateException $e) {
					$this->logger->info('Error create/set barcode. SKU = ' . $product->getSku() . ' Barcode = ' . $dataProduct['barcode'] . ' Msg: ' . $e->getMessage());
				}
			}

			// BRAND
			if(isset($dataProduct[IntegrationProductInterface::BRAND])) {
				$brandAttributeCode = $this->dataHelper->getBrandAttributeCode();
				try {
					$brandData = $this->brandRepository->getByPimId($dataProduct[IntegrationProductInterface::BRAND]);
					$this->saveAttributeDataByType($product, $brandAttributeCode, $brandData->getTitle());
				} catch (NoSuchEntityException $e) {
					$brand = $this->brandFactory->create();
					$brand->setPimId($dataProduct[IntegrationProductInterface::BRAND]);
					$brand->setCode($dataProduct[IntegrationProductInterface::BRAND_CODE]);
					$brand->setTitle($dataProduct[IntegrationProductInterface::BRAND_NAME]);
					$brand->setData('company_code', $dataProduct[IntegrationProductInterface::COMPANY_CODE]);

					try {
						$brandData = $this->brandRepository->save($brand);
						$this->logger->info('Create trans_brand data. PIM_ID = ' . $dataProduct[IntegrationProductInterface::BRAND]);
						$this->saveAttributeDataByType($product, $brandAttributeCode, $brandData->getTitle());
					} catch (CouldNotSaveException $e) {
						$this->logger->info('Error create/set brand. SKU = ' . $product->getSku() . ' brand = ' . $dataProduct[IntegrationProductInterface::BRAND] . ' Msg: ' . $e->getMessage());
					}
				} catch (\Exception $e) {
					$this->logger->info('Error create/set brand. SKU = ' . $product->getSku() . ' brand = ' . $dataProduct[IntegrationProductInterface::BRAND] . ' Msg: ' . $e->getMessage());
				}
			}

			// Save Product
			$product = $this->productRepositoryInterface->save($product);

			if($visibility && isset($dataProduct['sku']) && $dataProduct['sku']) {
				$this->integrationProductRepositoryInterface->changeProductVisibility($dataProduct['sku'], $visibility);
			}

			//Code for change product category
			$this->removeAndAssignProductCategory($productId, $product, $dataProduct);
		
		} catch (CouldNotSaveException $exception) {
			$msg = __FUNCTION__." On Save : ".$exception->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue,$msg , IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			throw new StateException(__($msg));
		} catch (\Exception $exception) {
			$msg = __FUNCTION__." On Save : ".$exception->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue,$msg , IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
			throw new StateException(__($msg));
		} catch (\RuntimeException $exception) {
			$msg = __FUNCTION__." On Save Runtime : ".$exception->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE_CONFIGURE);
			throw new StateException(__($msg));
		}

		return $result = [
			"is_configurable" => $isConfigurable,
			"entity_id" => $product->getEntityId()
		];
	}

	/**
	 * prepare attribute set data
	 *
	 * @param array $productData
	 * @return int
	 */
	protected function prepareAttributeSet(array $productData, $type = 'simple')
	{
		$default = $this->attrGroupGeneralInfoId;
		$result = $default;

		switch ($type) {
			case 'simple':
				if(isset($productData['category_id']) && $productData['category_id']) {
					foreach($productData['category_id'] as $pimId) {
						try {
							$attributeSet = $this->attributeSet->getAttributeSetIdByPimId($pimId);
							if($attributeSet) {
								$result = (int)$attributeSet;
								break;
							}
						} catch (StateException $e) {
							continue;
						} catch (\Exception $e) {
							continue;
						}
					}
				}
				break;
			
			case 'configurable':
				if(isset($productData['magento_entity_ids']) && $productData['magento_entity_ids']) {
					$childs = array_values($productData['magento_entity_ids']);
					foreach($childs as $child) {
						try {
							$product = $this->productRepositoryInterface->getById($child);
						} catch (NoSuchEntityException $e) {
							$product = null;
							continue;
						}

						if($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
							if((int)$product->getAttributeSetId() != $default) {
								if($product->getAttributeSetId()) {
									$result = (int)$product->getAttributeSetId();
									break;
								}
							}
						}
					}
				}
				break;
		}

		return $result;
	}

	/**
	 * 
	 */
	public function checkConfigurable($sku=""){
		$result['is_configurable']=0;
		$result['query_integration_product'] =NULL;
		$result['sku']=$sku;
		$result['sku_configurable']=NULL;
		if(empty($sku)){
			$msg = __FUNCTION__." Error SKU are empty!";
			$this->logger->info($msg);
			return $result;

		}
		$result['sku']=$sku;
		$skuConfigurable 	= $this->getItemId($sku);
		if(!$skuConfigurable){
			$msg2 = __FUNCTION__." Error Item Id Empty!";
			$this->logger->info($msg2);
			return $result;
		}
		$result['sku_configurable']=$skuConfigurable;
		// $check = $this->integrationProductRepositoryInterface->checkPosibilityConfigurable($dataProduct['sku']);
		// var_dump($check);
		$queryIntegrationProduct = $this->integrationProductRepositoryInterface->loadDataByItemId($skuConfigurable);
		if ($queryIntegrationProduct->getSize() > 0) {
			$countProduct = 0;
			foreach($queryIntegrationProduct as $row){
				if($row->getPimSku()!=$sku){
					$countProduct++;
					break;
				}
			}
			if($countProduct>0){
				$result['is_configurable']=1;
				$result['query_integration_product'] = $queryIntegrationProduct ;
			}
		
		}
		return $result;
	}

	/**
	 * Add Selling SKU Product
	 * @param string $sku
	 * @param array $dataAttributes
	 */
	protected function addAttributeSellingForConfigurable($dataProduct)
	{	
		$dataAttributes = $dataProduct['data_attributes'];
		try {
			if ($dataProduct[IntegrationProductInterface::PRODUCT_TYPE] != IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_LABEL) {
				$sellingUnit = substr($dataProduct['sku'], -3); //get 3 last char from SKU 
				$attributeCode = IntegrationProductInterface::SELLING_UNIT_CODE;
				$attributeId = $this->saveAttributeProduct($attributeCode);

				$dataAttributes[] = [
					"attribute_code" => $attributeCode,
					"attribute_value" => ltrim($sellingUnit, '0')
				];
			}
		} catch(Exception $ex) {
			$msg = __FUNCTION__." Add attribute Error : ".$ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}

		$result['attributes'] = $dataAttributes;
		$this->logger->info(__FUNCTION__." ATTRS :". json_encode($dataAttributes));
		
		return $result;
	}

	/**
	 * Function for change product category reflect
	 *
	 * @param int $productId
	 * @param ProductInterface $product
	 * @param array $dataProduct
	 * @return boolean
	 */
	protected function removeAndAssignProductCategory($productId = NULL, ProductInterface $product, $dataProduct = []) {
		try{
			$assignNewcategory = false;
			$categoryIds = $dataProduct['m_category_id'];
			
			if(!is_array($categoryIds)){
				return false;
			}
			
			if ($productId) {
				if ($product->getCategoryIds()) {
					$i  = 0;
					foreach ($product->getCategoryIds() as $catId) {
						if($catId && !in_array($catId, $categoryIds)){
							try {
								$removecategory = $this->categoryLinkRepositoryInterface->deleteByIds($catId, $product->getSku());
							} catch (NoSuchEntityException $e) {
								$this->logger->info(__FUNCTION__. "------ remove category by id. Category ID " . $catId . " doesnt exists");
								continue;
							}
						}
						$i++;
					}
				} 
				$assignNewcategory = $this->categoryLinkManagementInterface->assignProductToCategories($product->getSku(), $categoryIds);
			}
		} catch (\Exception $exception) {
			$this->logger->info(__FUNCTION__."------ ERROR ".$exception->getMessage());
			throw new StateException(__($exception->getMessage()));
		}

		return $assignNewcategory;
	}

	/**
	 * Get Category Id by Pim Id
	 * @param string $categoryPimId
	 * @return int $result
	 */
	protected function getCategoryId($categoryPimIds) {
		try{
			$result = [];
			if ($categoryPimIds) {
				foreach ($categoryPimIds as $categoryPimId) {
					$categoryCollection = $this->integrationCategoryRepositoryInterface->loadDataByPimId($categoryPimId);
					$categoryId         = $categoryCollection->getMagentoEntityId();
					if ($categoryId) {
						$result[] = $categoryId;
					}
				}
			}
		} catch (\Exception $exception) {
			$this->logger->info(__FUNCTION__."------ ERROR ".$exception->getMessage());
			throw new StateException(__($exception->getMessage()));
		}
		return $result;
	}

	/**
	 * Save Status and Message to Integration Data Value
	 *
	 * @param IntegrationDataValueInterface $objStatusMessage
	 * @param string $message
	 * @param int $status
	 */
	protected function saveStatusMessage($objStatusMessage, $message, $status) {
		$objStatusMessage->setMessage($message);
		$objStatusMessage->setStatus($status);
		$this->integrationDataValueRepositoryInterface->save($objStatusMessage);
	}

	/**
	 * @param string $sku
	 * @return string
	 */
	protected function getItemId($sku) {
		$itemId = NULL;
		if ($sku) {
			$itemId = substr(str_replace(' ', '', $sku), 0, 8);
		}
		return $itemId;
	}
	/**
     * Check Attribute Id Exist
     * @param string $attributeCode
     * @return mixed
     */
	protected function saveAttributeProduct($attributeCode) {
		$attributeId 		= NULL;
		try {
			$attributeData = $this->eavAttribute->getCollection()->addFieldToFilter('attribute_code', $attributeCode);
			
			if($attributeData->getSize() > 0){ 
				foreach($attributeData as $attributeDatas)
				{ 
					$attributeId = $attributeDatas['attribute_id'];
				}
			}else{
				
				$this->createAttributeProduct($attributeCode);
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
	 * @return
	 */
	protected function createAttributeProduct($attributeCode=""){
		try {
			
			$frontentInput    = IntegrationProductInterface::INPUT_TYPE_FRONTEND_FORMAT_PRICE;
			$backendInput     = IntegrationProductInterface::INPUT_TYPE_BACKEND_FORMAT_PRICE;
			
			$attributeValue = $this->productAttributeFactory->create();
			
			$attributeValue->setIsHtmlAllowedOnFront(IntegrationProductInterface::IS_HTML_ALLOWED_ON_FRONT);
			$attributeValue->setIsUsedInGrid(IntegrationProductInterface::IS_USED_IN_GRID);
			$attributeValue->setIsVisibleInGrid(IntegrationProductInterface::IS_VISIBLE_IN_GRID);
			$attributeValue->setIsFilterableInGrid(IntegrationProductInterface::IS_FILTERABLE_IN_GRID);
			$attributeValue->setPosition(IntegrationProductInterface::POSITION);
			$attributeValue->setApplyTo(IntegrationProductInterface::APPLY_TO);
			$attributeValue->setIsVisible(IntegrationProductInterface::IS_VISIBLE);
			
			$attributeValue->setScope(IntegrationProductInterface::SCOPE);
			$attributeValue->setAttributeCode($attributeCode);
			$attributeValue->setFrontendInput($frontentInput);
			$attributeValue->setEntityTypeId(IntegrationProductInterface::ENTITY_TYPE_ID);
			$attributeValue->setIsRequired(IntegrationProductInterface::IS_REQUIRED);
			$attributeValue->setIsUserDefined(IntegrationProductInterface::IS_USER_DEFINED);
			$attributeValue->setDefaultFrontendLabel($attributeCode);
			$attributeValue->setBackendType($backendInput);
			$attributeValue->setDefaultValue(0);
			$attributeValue->setIsUnique(IntegrationProductInterface::IS_UNIQUE);
			
			$this->productAttributeRepository->save($attributeValue);
			
			//Set Attribute to Attribute Set (Default)
			$this->productAttributeManagement->assign($this->attrGroupGeneralInfoId, $this->attrGroupProductDetailId, $attributeCode, IntegrationProductInterface::SORT_ORDER);
				
		} catch (\Exception $exception) {
			
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}	
	}

    /**
     * @param ProductInterface $product
     * @param string $attributeCode
     * @param array $dataProduct
     * @return boolean
     */
    protected function saveAttributeDataByType($product, $attributeCode, $attributeValue)
    {
    	$attributeValue = ucwords($attributeValue);

        if ($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            try{
                $attributeData = $this->eavConfig->getAttribute(IntegrationProductInterface::ENTITY_TYPE_CODE, $attributeCode);
                if ($attributeData->getId()) {
                    if ($attributeData->getAdditionalData() || $attributeData->getFrontendInput() == IntegrationProductInterface::FRONTEND_INPUT_TYPE_SELECT) {
                    	$attrVal = $this->attributeOption->createOrGetId(
                                $attributeCode, 
                                $attributeValue
                            );

                    	$currentVal = $product->getAttributeText($attributeCode);

                    	$attrVal = $attributeData->getBackendType() == 'int' ? (int) $attrVal : $attrVal;

                    	if($attributeData->getBackendType() == 'int' && $attrVal == 0) {
                    		$attrVal = NULL;
                    	}

                    	if($attributeCode == 'color') {
                            $product->setColor($attrVal);
                        } else {
                            $product->setData(
                                $attributeCode, 
                                $attrVal
                            );
                        }
                    } else {
                    	$attributeValue = $attributeData->getBackendType() == 'int' ? (int) $attributeValue : $attributeValue;
                    	$product->setData(
                            $attributeCode, 
                            $attributeValue
                        );
                    }
                }
            } catch (\Exception $exception) {
                $this->logger->info(__FUNCTION__."------ ERROR ".$exception->getMessage());
                throw new StateException(__($exception->getMessage()));
            }

            return $product;
        };
    }

	/**
	 * @param $dataProductMagento
	 * @param $dataProduct
	 * @param $integrationDataValue
	 * @return mixed
	 * @throws CouldNotSaveException
	 */
	protected function saveDataToIntegrationProduct($magentoProductId, $dataProduct, $integrationDataValue) {
		try {
			$integrationProduct = $this->integrationProductRepositoryInterface->loadDataByPimSku(
				$dataProduct['sku']
			);
			
			if ($integrationProduct) {
				
			}else{
				$integrationProduct = $this->integrationProductInterfaceFactory->create();

			}
			$integrationProduct->setPimId($dataProduct['pim_id']);
			$integrationProduct->setPimSku($dataProduct['sku']);
			
			$integrationProduct->setMagentoEntityId($magentoProductId);
			$integrationProduct->setItemId($dataProduct['item_id']);

			if($dataProduct['data_attributes']) {
				$integrationProduct->setAttributeList(json_encode($dataProduct['data_attributes']));
			}

			$categoryIds = $dataProduct['category_id'];
			if(is_array($categoryIds)){
				$jsonCat = json_encode($categoryIds);
				$integrationProduct->setPimCategoryId($jsonCat);
			}

			$mCategoryIds = $dataProduct['m_category_id'];
			if(is_array($mCategoryIds)){
				$jsonMCat = json_encode($mCategoryIds);
				$integrationProduct->setMagentoCategoryIds($jsonMCat);
			}

			$productType = IntegrationProductInterface::PRODUCT_TYPE_SIMPLE_VALUE;
			if($dataProduct[IntegrationProductInterface::PRODUCT_TYPE] == IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_LABEL){
				$productType = IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_VALUE;
			}
			$integrationProduct->setProductType($productType);
			
			$result = $this->integrationProductRepositoryInterface->save($integrationProduct);

		} catch (\Exception $ex) {
			$msg = __FUNCTION__." Save Integration Catalog Map : ".$ex->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE_MAPPING);
			throw new StateException(__($msg));
		}
		return $result ;

	}

	/**
	 * Save Product COnfigurable to Integration Mapping Data Product
	 */
	protected function createIntegrationProductMapConfigurable($dataProduct,$integrationDataValue) {
		
		try {
			$integrationProduct = $this->integrationProductRepositoryInterface->loadDataByPimSku(
				$dataProduct['item_id']
			);
			
			if ($integrationProduct) {
			
				if($integrationProduct->getStatusConfigurable()<1){
					return "IS NOT CONFIGURABLE";
				}
				if($integrationProduct->getStatusConfigurable()==integrationProductInterface::STATUS_CONFIGURABLE_UPDATED){
					$integrationProduct->setStatusConfigurable(integrationProductInterface::STATUS_CONFIGURABLE_NEED_UPDATE);
				}
				$integrationProduct->setStatusConfigurable(integrationProductInterface::STATUS_CONFIGURABLE_NEED_UPDATE);
				
			} else {
				$integrationProduct = $this->integrationProductInterfaceFactory->create();
				$integrationProduct->setPimSku($dataProduct['item_id']);
				$integrationProduct->setStatusConfigurable(integrationProductInterface::STATUS_CONFIGURABLE_NEED_CREATE);
			}
			$integrationProduct->setIntegrationDataId($integrationDataValue->getId());

			$categoryIds = $dataProduct['category_id'];
			if(is_array($categoryIds)){
				$jsonCat = json_encode($categoryIds);
				$integrationProduct->setPimCategoryId($jsonCat);
			}
			
			$result = $this->integrationProductRepositoryInterface->save($integrationProduct);

		} catch (\Exception $ex) {
			$msg = __FUNCTION__." Save Integration Catalog Map Create Configurable : ".$ex->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE_MAPPING);
			throw new StateException(__($msg));
		}
		return "Success Create / Update Attribute";
	}

	/**
	 * DELETE PRODUCT
	 */
	protected function deleteProduct($dataProduct=[],$integrationDataValue=[]){
		try {
			$productMapingData = $this->integrationProductRepositoryInterface->loadDataByPimSku($dataProduct['sku']);
			
			if ($productMapingData) {
				$this->productRepositoryInterface->deleteById($dataProduct['sku']);
				$this->integrationProductRepositoryInterface->delete($productMapingData);
				// $product       = $this->productRepositoryInterface->getById($productMapingData->getMagentoEntityId());
				// $productDelete = $this->productModel->delete($product);
			
			}

		} catch (\Exception $ex) {
			$msg = __FUNCTION__." Error on Delete : ".$ex->getMessage();
			$this->logger->info($msg);
			$this->saveStatusMessage($integrationDataValue, $msg, IntegrationDataValueInterface::STATUS_DATA_FAIL_DELETE);
			return false;
		}
		$this->logger->info("DELETED PRODUCT ".print_r($dataProduct[$i]['sku'],true));
	}


	/**
	 * Prepare Data Configurable
	 * @return array $result
	 */
	public function prepareDataConfigurable(){
		$result = [];
		$status = [
			IntegrationProductInterface::STATUS_CONFIGURABLE_NEED_CREATE, 
			IntegrationProductInterface::STATUS_CONFIGURABLE_NEED_UPDATE
		];
		try{
			$query  = $this->integrationProductRepositoryInterface->loadSkuConfigurableByMultiStatus($status);

			if($query->getSize() > 0){
				$i = 0;
				$queryDataValue = [];
				$dataValue = [];
				
				foreach($query as $row) {
					$result[$i]['sku'] = $row->getPimSku();
					$result[$i]['map_id'] = $row->getId();
					$result[$i]['product_configurable']	= $row;
					$queryDataValue[$i] = $this->integrationDataValueRepositoryInterface->getById($row->getIntegrationDataId());
					
					if ($queryDataValue[$i]) {
						$dataValue[$i] = json_decode($queryDataValue[$i]->getDataValue());
						$result[$i]['last_simple_product_data']=$dataValue[$i];
						$result[$i]['simple_products'] = $this->getSimpleProductBySkuConfigurable($row->getPimSku());
					}
					
					$i++;
				}
			}
		} catch (\Exception $ex) {
			$msg = __FUNCTION__." Error Preparing Data : ".$ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}
		
		return $result;
	}

	/**
	 * Get Simple Product By Sku Configurable
	 * @param string $sku
	 * @return array $result Integration Catalog Product Map
	 */
	protected function getSimpleProductBySkuConfigurable($skuConfigurable){
		$result=[];
		$queryIntegrationProduct = $this->integrationProductRepositoryInterface->loadDataByItemId($skuConfigurable);
		if ($queryIntegrationProduct->getSize() > 0) {
			$i=0;
			foreach($queryIntegrationProduct as $row){
				$result[$i]=$row;
				$i++;
			}
		}
		return $result;
	}

	/**
	 * Save Data Configurable
	 */
	public function saveDataConfigurable($dataProductConfigurable=[]){

		if(!is_array($dataProductConfigurable)){
			$this->logger->info(__FUNCTION__."---- NOT ARRAY");
		}
		$check = array_filter($dataProductConfigurable);
		if(empty($check)){
			$this->logger->info(__FUNCTION__."---- Data EMPTY");
		}
		$checkSimple = [];
		$i=0;
		
		$simpleProductDatas = [];
		$configureProduct	= [];
		try{
			foreach($dataProductConfigurable as $row){
				try {
					$this->logger->info(__FUNCTION__."---- Product SKU " . $row['product_configurable']->getPimSku() . " | map_id = " . $row['product_configurable']->getId());

					$checkSimple[$i]=array_filter($row['simple_products']);
					if(!empty($row['simple_products'])){
						$this->logger->info("-- Set Status On Progress");
						$this->updateMapByConfigureProduct($row['product_configurable'],IntegrationProductInterface::STATUS_CONFIGURABLE_ON_PROGRESS);

						$this->logger->info("-- Update Simple");
						$simpleProductData[$i]=$this->updateSimpleProductDataByConfigurable($row['simple_products']);

						$this->logger->info("-- Save Configure");

						$configureProduct[$i] = $this->saveConfigurableProductData($row['product_configurable']->getPimSku(),$simpleProductData[$i]);

						$this->logger->info("-- Update Catalog Product Mapping Configure");
						$this->saveMapByConfigureProduct($configureProduct[$i],$row['product_configurable']);

						$this->logger->info("-- Update Catalog Product Mapping Simple");
						$this->updateMapBySimpleProduct($configureProduct[$i],$row['simple_products']);
						
					}
					$i++;
				} catch (\Exception $ex) {
					$index = 0;
					foreach($row['simple_products'] as $data){
						try {
							if($data->getMagentoEntityId()){
								$magentoId = $data->getMagentoEntityId();
								$productData[$index] = $this->productRepositoryInterface->getById($magentoId);
								$productIndex = $productData[$index];
								$sku = $productIndex->getSku();
								$visibility = IntegrationProductInterface::VISIBILITY_BOTH;
								$this->integrationProductRepositoryInterface->changeProductVisibility($sku, $visibility);
							}
						} catch (\Exception $e) {
							$this->logger->info($e->getMessage());
							continue;
						}
					}
					$msg = __FUNCTION__." Error Saving Data : ".$ex->getMessage();
					$this->updateMapByConfigureProduct($row['product_configurable'],IntegrationProductInterface::STATUS_CONFIGURABLE_FAIL_UPDATE);
					$this->logger->info($msg);
					continue;
				}
			}
		} catch (\Exception $ex) {
			$msg = __FUNCTION__." Error Saving Data : ".$ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}

	}

	/**
	 * Generate Category Ids All Simple Product
	 */
	protected function generateCategoryIds($paramCategoryIds=[],$getCategoryIds=[]){
		if(is_array($paramCategoryIds)){
			$check = array_filter($getCategoryIds);
			if(!empty($check)){
			
				$paramCategoryIds = array_merge($paramCategoryIds,$check);
			}
		}else{
			$paramCategoryIds=$getCategoryIds;
		}
		return $paramCategoryIds;
	}

	/**
	 * Update Simple Product Data By Configurable
	 */
	protected function updateSimpleProductDataByConfigurable($dataSimpleProducts=[]){
		$result = [];
		$i=0;
		$productData = [];
		$saveProductData=[];
		
		$productDataAttr = [];
		$attributeOptionValues=[];
		$attributeValueData=[];
		$attrCode="";
		$attrId="";
		$magentoIds=[];
		$productName ="";
		$isActive=0;
		$categoryIds=[];
		$getAttributeDatas =[];
		try{
			foreach($dataSimpleProducts as $row){
				try {
					if($row->getMagentoEntityId() > 0){
						$magentoIds[$i] = $row->getMagentoEntityId();
						$skus[$i] = $row->getPimSku();
						$productData[$i] = $this->productRepositoryInterface->getById($row->getMagentoEntityId());
						$productData[$i]->setVisibility(IntegrationProductInterface::VISIBILITY_NOT_VISIBLE);
						$saveProductData[$i] = $this->productRepositoryInterface->save($productData[$i]);

						$productDataAttr[$i] = $saveProductData[$i]->getResource()->getAttribute(IntegrationProductInterface::SELLING_UNIT_CODE);
						
						$attributeOptionValues[$i] = $productDataAttr[$i]->getSource()->getOptionText(
							$saveProductData[$i]->getData(IntegrationProductInterface::SELLING_UNIT_CODE)
						);
						$attributeValueData[$i] = [
							'label'        => $productDataAttr[$i]->getAttributeCode(),
							'attribute_id' => $productDataAttr[$i]->getId(),
							'value_index'  => $attributeOptionValues[$i]
						];

						$getAttributeDatas[$i]      = [
							'attribute_code'   => $productDataAttr[$i]->getAttributeCode(),
							'attribute_value'  => $attributeOptionValues[$i]
						];

						$attrCode=$productDataAttr[$i]->getAttributeCode();
						$attrId=$productDataAttr[$i]->getId();
						$productName=$saveProductData[$i]->getName();

						if($saveProductData[$i]->getStatus() == 1){
							$isActive++;
						}
						
						$categoryIds=$this->generateCategoryIds($categoryIds,$saveProductData[$i]->getCategoryIds());
						
						// Get All Custom Attributes
						// foreach ( $saveProductData[$i]->getCustomAttributes() as $attribute) {  
						// 	$this->logger->info(__FUNCTION__."--".print_r($attribute->getAttributeCode(),true));
							
						// }
					}
					$i++;
				} catch (\Exception $e) {
					continue;
				}
			}
			$result = [
				"product_map_data" => $dataSimpleProducts,
				"magento_entity_ids" => $magentoIds,
				"skus" => $skus,
				"attr_value_index" => $attributeValueData,
				"attr_code" => $attrCode,
				"attr_id" => $attrId,
				"default_product_name" => $productName,
				"simple_active" => $isActive,
				"simple_category_ids" => array_unique($categoryIds),
				"simple_data_attr" => $getAttributeDatas
			];
		} catch (\Exception $ex) {
			$msg = __FUNCTION__." Error : ".$ex->getMessage();
			$this->logger->info($msg);
			throw new StateException(__($msg));
		}

		return $result;
	}

	/**
	 * Create / Update Configurable Data product
	 */
	protected function saveConfigurableProductData($sku="",$simpleProducts=[])
	{
		$productConfigurableId = $this->productModel->getIdBySku($sku);
		$attributeSetId = $this->prepareAttributeSet($simpleProducts, 'configurable');
		
		try {
			$product = $this->productRepositoryInterface->get($sku);
			$this->logger->info(__FUNCTION__."---- Update Product");
		} catch (NoSuchEntityException $e) {
			$product = $this->productInterfaceFactory->create();
			$product->setSku($sku);
			$product->setUrlKey($this->changeUrlKeyChildProduct($simpleProducts['default_product_name'] , $sku));
			$this->logger->info(__FUNCTION__."---- Create New Product");
		}
		
		$product->setAttributeSetId($attributeSetId);
		$product->setTypeId(IntegrationProductInterface::PRODUCT_TYPE_CONFIGURABLE);

		$attributeIds = [];
		$rejectedAttr = [];
		$swatch = [];
		$associatedProductIds = [];
		$attrs = $this->getAttributeOption($simpleProducts);

		if($attrs) {
			$units = [];
			foreach ($attrs as $key => $dataAttr) {
				$productId = $key;
				$attributes = $dataAttr;
				// var_dump($attributes);
				try {
					$simpleProduct = $this->productRepositoryInterface->getById($productId);
					$associatedProductIds[] = $productId;

					if(!empty($attributes)) {
						foreach ($attributes as $attr) {
							$attribute = $product->getResource()->getAttribute($attr);
							$configurableAttributesData[0] = [
								'attribute_id' => $attribute->getId(),
								'code' => $attribute->getAttributeCode(),
								'label' => $attribute->getStoreLabel(),
								'position' => '0',
								'values' => $simpleProducts['attr_value_index']
							];

							if ($simpleProduct && $simpleProduct instanceof \Magento\Catalog\Api\Data\ProductInterface) {
		                        $productDataAttr = $simpleProduct->getResource()->getAttribute($attr);
		                        if($productDataAttr) {
		                        	if($simpleProduct->getCustomAttribute($attr)) {
		                        		$attrValue = $simpleProduct->getCustomAttribute($attr)->getValue();
		                        	} else {
		                        		continue;
		                        	}
		                        	
		                        	$attrLabel = $simpleProduct->getResource()->getAttribute($attr)->getFrontend()->getValue($simpleProduct);
		                        	if(!$attrLabel || $attrLabel == 'default') {
			                        	if(!in_array($productDataAttr->getId(), $rejectedAttr)) {
				                        	$rejectedAttr[] = $productDataAttr->getId();
				                        }
			                        	continue;
			                        }
		                        	
			                        if(!in_array($productDataAttr->getId(), $attributeIds)) {
		                            	$attributeIds[] = $productDataAttr->getId();
		                            	$swatch[] = $productDataAttr->getId();
		                        	}

		                            $attributeData['label'] = $attrLabel;
		                            $attributeData['attribute_id'] = $productDataAttr->getId();
		                            $attributeData['value_index'] = $attrValue;
		                            $attributeData['is_percent'] = 0;
		                            $attributeData['pricing_value'] = $simpleProduct->getFinalPrice();

		                            $configurableProductsData[$productId][] = $attributeData;
		                        }
		                    }
						}
					}

					//set selling as option
					$productDataAttr = $simpleProduct->getResource()->getAttribute($simpleProducts['attr_code']);
	                $sku = $simpleProduct->getSku();
	                $sellingUnit = substr($sku, -3);
	                if($productDataAttr) {

	                	if(in_array($sellingUnit, $units) && empty($swatch)) {
	                		throw new \Exception("Selling unit duplicate, configurable product create process cancelled.");
	                	}

	                	if(!in_array($productDataAttr->getId(), $attributeIds)) {
	                    	$attributeIds[] = $productDataAttr->getId();
	                	}

	                    $attributeData['label'] = $sellingUnit;
	                    $attributeData['attribute_id'] = $productDataAttr->getId();
	                    $attributeData['value_index'] = $simpleProduct->getCustomAttribute($simpleProducts['attr_code'])->getValue();
	                    $attributeData['is_percent'] = 0;
	                    $attributeData['pricing_value'] = $simpleProduct->getFinalPrice();

	                    $configurableProductsData[$productId][] = $attributeData;
	                    $units[] = $sellingUnit;
	                }
				} catch (NoSuchEntityException $e) {
					continue;
				}
			}
		}
		
		if($product->getId()) {
			$this->removeSuperAttr($product);
		}

		if(!empty($attributeIds)) {
			$product->getTypeInstance()->setUsedProductAttributeIds($attributeIds, $product);
		}

		$configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
        $product->setCanSaveConfigurableAttributes(true);
        $product->setConfigurableAttributesData($configurableAttributesData);
        
        if(!empty($configurableProductsData)) {
            $product->setConfigurableProductsData($configurableProductsData);
        }

        $product->setAffectConfigurableProductAttributes(4);
        $this->configurableAttribute->create()->setUsedProductAttributeIds($attributeIds, $product);
        $product->setNewVariationsAttributeSetId(4); // Setting Attribute Set Id
        $product->setAssociatedProductIds($associatedProductIds);// Setting Associated Products

		// $attribute = $product->getResource()->getAttribute($simpleProducts['attr_code']);
		// $configurableAttributesData[0] = [
		// 	'attribute_id' => $attribute->getId(),
		// 	'code' => $attribute->getAttributeCode(),
		// 	'label' => $attribute->getStoreLabel(),
		// 	'position' => '0',
		// 	'values' => $simpleProducts['attr_value_index']
		// ];

		// $configurableOptions = $this->productOptionFactory->create($configurableAttributesData);
		// $extensionConfigurableAttributes = $product->getExtensionAttributes();
		// $extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
		// $product->setExtensionAttributes($extensionConfigurableAttributes);

		// if (isset($simpleProducts['magento_entity_ids']) && !empty($simpleProducts['magento_entity_ids'])) {
		// 	$extensionConfigurableAttributes->setConfigurableProductLinks($simpleProducts['magento_entity_ids']);
		// }

		$configurableActive = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
		
		if (isset($simpleProducts['simple_active']) && ($simpleProducts['simple_active'] > 0)) {
			$configurableActive = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
		}

		
		$product->setWebsiteIds([1]);
		$product->setName($simpleProducts['default_product_name']);
		$product->setCategoryIds($simpleProducts['simple_category_ids']);
		$product->setVisibility(IntegrationProductInterface::VISIBILITY_BOTH);
		$product->setStatus($configurableActive);
		$product->setStockData(
			[
				'use_config_manage_stock' => 1,
				'is_in_stock' => 1,
			]
		);

		try {
			//Save Product Configurable
			$productSave = $this->productRepositoryInterface->save($product);
			
			//Code for change product category
			$dataProduct['m_category_id'] = $simpleProducts['simple_category_ids'];
			$this->removeAndAssignProductCategory($product->getId(), $productSave, $dataProduct);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}

		return $productSave;
	}

	/**
	 * get super attribute
	 *
	 * @param array $data
	 * @return array
	 */
	protected function getAttributeOption(array $data)
	{
		$attrData = [];;
		$swatch = [];
		if($data['product_map_data']) {
			foreach($data['product_map_data'] as $row) {
				$attributeList = $row->getAttributeList();
				$attributeList = json_decode($attributeList, true);

				foreach ($attributeList as $attr) {
					$code = $attr['attribute_code'];
					if ($this->dataHelper->isSwatchAttr($code) && !in_array($code, $swatch)) {
						$attrData[$row->getMagentoEntityId()][] = $code;
						$swatch[(int) $row->getMagentoEntityId()][] = $code;
					}
				}

				if(empty($swatch)) {
					$swatch = [(int)  $row->getMagentoEntityId() => []];
				}
			}
		}
		
		return $swatch;
	}

	/**
	 * Remove super attribute from current parent product
	 */
	protected function removeSuperAttr($product)
	{
		if($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
			$conn = $this->attributeResource->getConnection();
			$tableName = $conn->getTableName('catalog_product_super_attribute');
			$rowId = $product->getRowId();
			$whereConditions = [
			    $conn->quoteInto('product_id = ?', $rowId),
			];

			try {
				$deleteRows = $conn->delete($tableName, $whereConditions);
				$this->logger->info(__FUNCTION__ . " Success execute delete table where product_id = " . $rowId);
			} catch (\Exception $e) {
				$this->logger->info(__FUNCTION__ . " Error execute delete table where product_id = " . $rowId);
			}
		}
	}

	protected function updateMapBySimpleProduct($configureProduct,$simpleProductMap){
		if($configureProduct->getId()){
			foreach($simpleProductMap as $productMap){
				if($productMap->getMagentoEntityId()){
					$productMap->setMagentoParentId($configureProduct->getId());
					$this->integrationProductRepositoryInterface->save($productMap);
				}
			}
		}
	}

	/**
	 * Update & Save data when success IntegrationProductInterface
	 */
	protected function saveMapByConfigureProduct($magentoConfigureProduct=[],$configureProductMap=[]){
		if($magentoConfigureProduct->getId()){
			$configureProductMap->setMagentoEntityId($magentoConfigureProduct->getId());
		}
		if($magentoConfigureProduct->getCategoryIds()){
			$configureProductMap->setMagentoCategoryIds(json_encode($magentoConfigureProduct->getCategoryIds()));
		}
			
		$configureProductMap->setStatusConfigurable(IntegrationProductInterface::STATUS_CONFIGURABLE_UPDATED);
		$this->integrationProductRepositoryInterface->save($configureProductMap);
	}

	/**
	 * Update with dynamic status IntegrationProductInterface
	 */
	protected function updateMapByConfigureProduct($configureProductMap=[],$status=""){
			
		$configureProductMap->setStatusConfigurable($status);
		$this->integrationProductRepositoryInterface->save($configureProductMap);
	}

	/**
	 * Update URL
	 */
	public function changeUrlKeyChildProduct($name, $extraForUrlKey) {
		$result = "";
		if ($extraForUrlKey) {
			$result = str_replace(' ', '-', strtolower($name)) . '-' . $extraForUrlKey;
		} else {
			$result = str_replace(' ', '-', strtolower($name));
		}

		return $result;
	}

	/**
     * create or Check Attribute barcode
     * @param string $attributeCode
     * @param string $attributeValue
     * @return mixed
     */
	protected function saveAttributeBarcode($attributeCode, $attributeValue) {
		try {
			$attributeData = $this->eavAttribute->getCollection()->addFieldToFilter('attribute_code', $attributeCode);
			
			if($attributeData->getSize() <= 0) { 
				$this->createAttributeBarcode($attributeCode);
			}

		} catch (\Exception $exception) {
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}
	}

	/**
	 * Create New Attribute
	 * 
	 * @param $attributeCode string
	 * @return
	 */
	protected function createAttributeBarcode($attributeCode=""){
		try {
			
			$frontentInput    = IntegrationProductInterface::FRONTEND_INPUT_TYPE_TEXT;
			
			$attributeValue = $this->productAttributeFactory->create();
			
			$attributeValue->setIsHtmlAllowedOnFront(IntegrationProductInterface::IS_HTML_ALLOWED_ON_FRONT);
			$attributeValue->setIsUsedInGrid(IntegrationProductInterface::IS_USED_IN_GRID);
			$attributeValue->setIsVisibleInGrid(IntegrationProductInterface::IS_VISIBLE_IN_GRID);
			$attributeValue->setIsFilterableInGrid(IntegrationProductInterface::IS_FILTERABLE_IN_GRID);
			$attributeValue->setPosition(IntegrationProductInterface::POSITION);
			$attributeValue->setApplyTo(IntegrationProductInterface::APPLY_TO);
			$attributeValue->setIsVisible(IntegrationProductInterface::IS_VISIBLE);
			$attributeValue->setIsSearchable(IntegrationProductInterface::IS_SEARCHABLE);
			$attributeValue->setScope(IntegrationProductInterface::SCOPE);
			$attributeValue->setAttributeCode($attributeCode);
			$attributeValue->setFrontendInput($frontentInput);
			$attributeValue->setEntityTypeId(IntegrationProductInterface::ENTITY_TYPE_ID);
			$attributeValue->setIsRequired(IntegrationProductInterface::IS_REQUIRED);
			$attributeValue->setIsUserDefined(IntegrationProductInterface::IS_USER_DEFINED);
			$attributeValue->setDefaultFrontendLabel($attributeCode);
			$attributeValue->setIsUnique(IntegrationProductInterface::IS_UNIQUE);
			
			$this->productAttributeRepository->save($attributeValue);
			
			//Set Attribute to Attribute Set (Default)
			$this->productAttributeManagement->assign($this->attrGroupGeneralInfoId, $this->attrGroupProductDetailId, $attributeCode, IntegrationProductInterface::SORT_ORDER);
				
		} catch (\Exception $exception) {
			
			throw new StateException(
				__(__FUNCTION__." - ".$exception->getMessage())
			);
		}	
	}



}