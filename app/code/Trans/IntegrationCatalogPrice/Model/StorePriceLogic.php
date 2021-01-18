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
use Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationProductAttributeInterface;

use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterfaceFactory as ProductFactory;

/**
 * \Trans\IntegrationCatalogPrice\Model\StorePriceLogic
 */
class StorePriceLogic implements StorePriceLogicInterface
{
    /**
     * @var string
     */
    const DATA_LIST = 'data_list';

    /**
     * @var string
     */
    const SOURCE_LIST = 'source_list';

    /**
     * @var string
     */
    const SKU_LIST = 'sku_list';

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
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;
    
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

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
    public function __construct(
        Logger $logger,
        StorePriceRepositoryInterface $storePriceRepositoryInterface,
        StorePriceInterfaceFactory $storePriceInterfaceFactory,
        \Trans\IntegrationCatalogPrice\Api\OnlinePriceRepositoryInterface $onlinePriceRepositoryInterface,
        \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterfaceFactory $onlinePriceInterfaceFactory,
        IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
        Validation $validation,
        IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        ProductAttributeManagementInterface $productAttributeManagement,
        AttributeOption $attributeOptionHelper,
        EavConfig $eavConfig,
        ProductAttributeInterfaceFactory $productAttributeFactory,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        IntegrationProductRepositoryInterface $integrationProductRepositoryInterface,
        CoreHelper $coreHelper,
        ProductRepositoryInterface $productRepositoryInterface,
        IntegrationProductAttributeRepositoryInterface $integrationAttributeRepository,
        \Trans\IntegrationCatalogStock\Api\IntegrationStockInterface $integrationStock,
        \Trans\IntegrationCatalogPrice\Helper\Data $helperPrice,
        ResourceConnection $resourceConnection,
        ProductCollectionFactory $productCollectionFactory,
        ProductFactory $productFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->logger = $logger;
        $this->storePriceRepositoryInterface = $storePriceRepositoryInterface;
        $this->storePriceInterfaceFactory = $storePriceInterfaceFactory;
        $this->onlinePriceRepositoryInterface = $onlinePriceRepositoryInterface;
        $this->onlinePriceInterfaceFactory = $onlinePriceInterfaceFactory;
        $this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
        $this->validation = $validation;
        $this->integrationJobRepositoryInterface = $integrationJobRepositoryInterface;
        $this->eavAttribute = $eavAttribute;
        $this->productAttributeManagement = $productAttributeManagement;
        $this->attributeOptionHelper = $attributeOptionHelper;
        $this->eavConfig = $eavConfig;
        $this->productAttributeFactory = $productAttributeFactory;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->integrationProductRepositoryInterface = $integrationProductRepositoryInterface;
        $this->coreHelper = $coreHelper;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->integrationAttributeRepository = $integrationAttributeRepository;
        $this->attrGroupGeneralInfoId = IntegrationProductAttributeInterface::ATTRIBUTE_SET_ID;
        $this->attrGroupProductDetailId = $this->integrationAttributeRepository->getAttributeGroupId(
            IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT
        );
        $this->integrationStock = $integrationStock;
        $this->updateRepositoryInterface = $helperPrice->getUpdateRepositoryInterface();
        $this->updateInterfaceFactory = $helperPrice->getUpdateInterfaceFactory();
        $this->versionManager = $helperPrice->getVersionManagerFactory();
        $this->productStaging = $helperPrice->getProductStagingInterface();
        $this->resourceConnection = $resourceConnection;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->connection = $this->resourceConnection->getConnection();
        $this->productFactory = $productFactory;
        $this->indexerRegistry = $indexerRegistry;
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
    public function prepareData($jobs = [])
    {
        if (empty($jobs)) {
            throw new StateException(__(
                'Parameter Channel are empty !'
            ));
        }

        $jobId = $jobs->getFirstItem()->getId();
        $status = StorePriceInterface::STATUS_JOB_DATA;

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
    protected function getRemapDataValue($data)
    {
        $dataValue = [];
        foreach ($data as $index => $value) {
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
    protected function getSourceList($data)
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
    protected function getNewSources($sourceList, $sources)
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
    protected function saveNewSources($sources, $dataArray, $dataValue)
    {
        if (empty($sources) == false) {
            $failedSources = [];
            foreach ($sources as $key => $value) {
                try {
                    $this->logger->info('Before add new store ' . date('d-M-Y H:i:s'));
                    $this->integrationStock->addNewSource($value);
                    $this->logger->info('After add new store ' . date('d-M-Y H:i:s'));
                } catch (\Exception $e) {
                    $failedSources[] = $value;
                }
            }
            if (empty($failedSources) == false) {
                foreach ($dataArray as $index => $value) {
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
    public function remapData($jobs = [], $data = [])
    {
        if (!$jobs->getFirstItem()->getId()) {
            throw new NoSuchEntityException(__('Error Jobs Datas doesn\'t exist'));
        }

        $jobId = $jobs->getFirstItem()->getId();
        $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);

        $dataProduct = [];
        try {
            if ($data) {
                $dataArray = $data->getData();
                $dataValue = $this->getRemapDataValue($dataArray);
                $sourceList = $this->getSourceList($dataValue);
                $sources = $this->connection->fetchAll($this->storePriceRepositoryInterface->getInventoryStoreListQuery($sourceList));
                $newSources = $this->getNewSources($sourceList, $sources);
                if (empty($newSources) == false) {
                    $this->saveNewSources($newSources, $dataArray, $dataValue);
                }
                foreach ($dataArray as $index => $value) {
                    $dataProduct[$dataValue[$index]['sku']][$index] = $dataValue[$index];
                    $dataProduct[$dataValue[$index]['sku']][$index]['data_id'] = $value['id'];
                }
            }
        } catch (\Exception $exception) {
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $exception->getMessage());
            throw new StateException(__("Error Validate SKU - ".$exception->getMessage()));
        }

        return $dataProduct;
    }
    
    /**
     * Get product by multiple sku
     */
    protected function getProductByMultipleSku($skuList)
    {
        $result = [];
        if (empty($skuList) == false) {
            $this->logger->info('Before get product ' . date('d-M-Y H:i:s'));
            $collection = $this->productCollectionFactory->create()->addFieldToFilter('sku', ['in'=>$skuList]);
            $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['entity_id','sku','row_id']);
            $result = $collection->getItems();
            $this->logger->info('After get product ' . date('d-M-Y H:i:s'));
        }
        return $result;
    }

    /**
     * Get data list
     *
     * @param array $dataProduct
     * @return array
     */
    protected function getDataList($dataProduct)
    {
        $skuList = [];
        $dataValueList = [];
        $sourceList = [];
        foreach ($dataProduct as $sku => $data) {
            $skuList[] = $sku;
            foreach ($data as $key => $val) {
                $dataValueList[] = $val;
                if (in_array($val['store_code'], $sourceList) == false) {
                    $sourceList[] = $val['store_code'];
                }
            }
        }
        return [
            self::SKU_LIST => $skuList,
            self::SOURCE_LIST => $sourceList,
            self::DATA_LIST => $dataValueList
        ];
    }

    /**
     * Get decimal attribute
     *
     * @param array $dataList
     * @return array
     */
    protected function getDecimalAttribute($dataList)
    {
        $attributes = ['price', 'base_price_in_kg', 'promo_price_in_kg'];
        foreach ($dataList as $index => $value) {
            $sourceCode = $value['store_code'];
            $basePrice = StorePriceLogicInterface::PRODUCT_ATTR_BASE_PRICE . $sourceCode;
            if (\in_array($basePrice, $attributes) == false) {
                $attributes[] = $basePrice;
            }
            $promoPrice = StorePriceLogicInterface::PRODUCT_ATTR_PROMO_PRICE . $sourceCode;
            if (in_array($promoPrice, $attributes) == false) {
                $attributes[] = $promoPrice;
            }
        }
        return $attributes;
    }

    /**
     * Get int attribute
     *
     * @return array
     */
    protected function getIntAttribute()
    {
        return ['own_courier', 'price_in_kg', 'price_type', 'price_view'];
    }

    /**
     * Get list of existing sku
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $productInterfaces
     * @return array
     */
    protected function getExistingSkuList($productInterfaces)
    {
        $result = [];
        foreach ($productInterfaces as $index => $product) {
            $result[] = $product->getSku();
        }
        return $result;
    }

    /**
     * Get product decimal attribute
     *
     * @param array $dataProduct
     * @param array $productInterfaces
     * @param array $decimalAttributes
     * @return array
     */
    protected function getProductDecimalAttributes($dataList, $productInterfaces, $decimalAttributes)
    {
        $prices = [];
        foreach ($dataList as $key => $value) {
            $basePrice = '';
            $promoPrice = '';
            $data = \array_pop($value);
            foreach ($decimalAttributes as $idx => $res) {
                if (\strpos($res, $data['store_code']) !== false) {
                    if (\strpos($res, 'base') !== false) {
                        $basePrice = $res;
                    }
                    if (\strpos($res, 'promo') !== false) {
                        $promoPrice = $res;
                    }
                }
            }
            $prices[$data['sku']] = [
                'base_price' => $basePrice,
                'promo_price' => $promoPrice,
                'normal_selling_price' => $data['normal_selling_price'],
                'online_price' => $data['online_price'],
                'promo_selling_price' => $data['promo_selling_price']
            ];
        }
        return $prices;
    }

    /**
     * Decision price
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $baseAttrName
     * @param string $promoAttrName
     * @param int $normalPrice
     * @param int $onlinePrice
     * @param int $promoPrice
     * @return array
     */
    protected function priceDecision($product, $baseAttrName, $promoAttrName, $normalPrice, $onlinePrice, $promoPrice)
    {
        $result = [];
        if ($onlinePrice > 0) {
            if ($normalPrice > $onlinePrice) {
                $result[$baseAttrName] = $normalPrice;
                $result[$promoAttrName] = $onlinePrice;
            } else {
                $result[$baseAttrName] = $onlinePrice;
                $result[$promoAttrName] = 0;
            }
        } elseif ($normalPrice > 0) {
            if ($normalPrice > $promoPrice) {
                $result[$baseAttrName] = $normalPrice;
                $result[$promoAttrName] = $promoPrice;
            } else {
                $result[$baseAttrName] = $normalPrice;
                $result[$promoAttrName] = 0;
            }
        } else {
            $result[$baseAttrName] = $promoPrice;
            $result[$promoAttrName] = 0;
        }
        $arrayPrice = array_map('intval',[$normalPrice, $onlinePrice, $promoPrice]);
        $maxPrice = max($arrayPrice);
        $result['price'] = $maxPrice;
        $result['base_price_in_kg'] = '';
        $result['promo_price_in_kg'] = '';

        $isOwnCourier = strtolower($product->getData('own_courier'));
        $isFresh = strtolower($product->getData('is_fresh'));
        $weight = $product->getWeight();
        $soldIn = strtolower($product->getData('sold_in'));

        // if (($isOwnCourier == 'yes' || $isOwnCourier == 1) && $soldIn == 'kg') {
        //     $result['base_price_in_kg'] = $weight * ($result[$baseAttrName] / 1000);
        //     $result['promo_price_in_kg'] = $weight * ($result[$promoAttrName] / 1000);
        // }

        if (($isFresh == 'yes' || $isFresh == 1) && ($soldIn == 'kg' || $soldIn == 'pcs')) {
            $result['base_price_in_kg'] = $result[$baseAttrName];
            $result[$baseAttrName] = $weight * ($result[$baseAttrName] / 1000);

            $result['promo_price_in_kg'] = $result[$promoAttrName];
            $result[$promoAttrName] = $weight * ($result[$promoAttrName] / 1000);

            $arrayPriceFresh = array_map('intval', [$result[$baseAttrName], $result[$promoAttrName]]);
            $result['price'] = max($arrayPriceFresh);
        }
        return $result;
    }

    /**
     * FIlter data list by exist sku
     *
     * @param array $dataList
     * @param array $existSku
     * @return void
     */
    protected function filterDataListWithExistSku($dataList, $existSku)
    {
        $result = [];
        foreach ($dataList as $key => $value) {
            if (\in_array($value['sku'], $existSku)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Get multiprice data
     *
     * @param array $existSkuList
     * @param array $sourceList
     * @param array $dataValueList
     * @return array
     */
    public function getMultipriceData($existSkuList, $sourceList, $dataValueList)
    {
        $sql = $this->storePriceRepositoryInterface->loadQueryBySkuListNSourceList($existSkuList, $sourceList);
        $data = $this->connection->fetchAll($sql);
        return \array_merge($data, $dataValueList);
    }

    /**
     * Save Product
     */
    public function save($jobs = [], $dataProduct = [])
    {
        if (!$jobId = $jobs->getFirstItem()->getId()) {
            $message = 'Error Jobs Datas doesn\'t exist';
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
            throw new NoSuchEntityException(__($message));
        }

        if (empty($dataProduct)) {
            $message = "Theres No SKU Key Available";
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
            throw new StateException(__($message));
        }

        $msgError=[];

        //Get all required data
        $dataList = $this->getDataList($dataProduct);
        $productInterfaces = $this->getProductByMultipleSku($dataList['sku_list']);
        $existingSkuList = $this->getExistingSkuList($productInterfaces);
        $dataValueList = $this->filterDataListWithExistSku($dataList['data_list'], $existingSkuList);
        $decimalAttributes = $this->getDecimalAttribute($dataList['data_list']);
        //$intAttributes = $this->getIntAttribute();
        $productDecimalAttributes = $this->getProductDecimalAttributes(
            $dataProduct,
            $productInterfaces,
            $decimalAttributes
        );

        //Saving process
        try {
            $inputList = $this->prepareProductPricesInput($productInterfaces, $productDecimalAttributes);
            $this->saveProductPrices($inputList);
            $multiPrices = $this->getMultipriceData($existingSkuList, $dataList[self::SOURCE_LIST], $dataValueList);
            $productMappingData = $this->saveMultiPriceBulk($multiPrices);
            // $this->updateDataValueStatusBulk($multiPrices, IntegrationDataValueInterface::STATUS_DATA_SUCCESS, null);
            $this->updateDataValueStatusBulk($dataValueList, IntegrationDataValueInterface::STATUS_DATA_SUCCESS, null);
            $this->reindexByProductsIds($productInterfaces, ['catalog_product_attribute', 'catalogsearch_fulltext']);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage() . date('d-M-Y H:i:s'));
            throw $e;
        }

        $msg = null;
        $msgCheck = array_filter($msgError);
        $status = IntegrationJobInterface::STATUS_COMPLETE;
        if (!empty($msgCheck)) {
            $msgError = array_unique($msgError);
            $msg = "Success with Error : ".implode("", $msgError);
        }
        $this->updateJobStatus($jobId, $status, $msg);
        return $productMappingData;
    }

    /**
     * Reindex
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $productInterfaces
     * @param array $indexLists
     * @return void
     */
    protected function reindexByProductsIds($productInterfaces, $indexLists)
    {
        $productIds = [];
        foreach ($productInterfaces as $key => $value) {
            $productIds[] = $value->getId();
        }
        foreach ($indexLists as $indexList) {
            $categoryIndexer = $this->indexerRegistry->get($indexList);
            if (!$categoryIndexer->isScheduled()) {
                $categoryIndexer->reindexList(array_unique($productIds));
            }
        }
    }

    /**
     * Prepare Product Prices Input
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $productInterfaces
     * @param array $productDecimalAttributes
     * @return array
     */
    public function prepareProductPricesInput($productInterfaces, $productDecimalAttributes)
    {
        $inputList = [];
        $attributeIdMap = [];
        foreach ($productInterfaces as $index => $product) {
            $productPriceAttributes = [];
            $sku = $this->validateSku($product->getSku(), $productPriceAttributes);

            if(!isset($productDecimalAttributes[$sku])){
                continue;
            }

            $data = $productDecimalAttributes[$sku];
            $productPriceAttributes = $this->priceDecision(
                $product,
                $data['base_price'],
                $data['promo_price'],
                $data['normal_selling_price'],
                $data['online_price'],
                $data['promo_selling_price']
            );
            foreach ($productPriceAttributes as $key => $value) {
                if(!isset($attributeIdMap[$key])){
                    $attributeIdMap[$key] = $this->saveAttributeProduct($key, $sku);
                }
                $inputList[] = [
                    'attribute_id' => $attributeIdMap[$key],
                    'store_id' => 0,
                    'value' => $value,
                    'row_id' => $product->getRowId()
                ];
            }
        }
        return $inputList;
    }

    /**
     * Save product prices bulk
     *
     * @param [type] $inputList
     * @return void
     */
    public function saveProductPrices($inputList)
    {
        try {
            $this->logger->info('Before save bulk product price ' . date('d-M-Y H:i:s'));
            $tableName = $this->connection->getTableName('catalog_product_entity_decimal');
            $this->connection->insertOnDuplicate($tableName, $inputList, ['value']);
            $this->logger->info('After save bulk product price ' . date('d-M-Y H:i:s'));
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $this->logger->critical('Error occurred ' . $e->getMessage().' '.date('d-M-Y H:i:s'));
        }
    }

    /**
     * Validate Params
     *
     * @param $param mixed
     * @return $result mixed
     */
    protected function validateParams($param)
    {
        $result = [];
        try {
            $result['sku'] = $this->validation->validateArray(StorePriceInterface::SKU, $param);
            $result['store_code'] = $this->validation->validateArray(StorePriceInterface::SOURCE_CODE, $param);
            $result['normal_selling_price'] = $this->validation->validateArray(StorePriceInterface::NORMAL_SELLING_PRICE, $param);
            $result['online_price'] = $this->validation->validateArray(StorePriceInterface::ONLINE_SELLING_PRICE, $param);
            $result['promo_selling_price'] = $this->validation->validateArray(StorePriceInterface::PROMO_SELLING_PRICE, $param);
            //$result['store_attr_code'] = $result['store_code'];

            $result['pim_id'] = $this->validation->validateArray(StorePriceInterface::ID, $param);
            $result['pim_code'] = $this->validation->validateArray(StorePriceInterface::CODE, $param);
            $result['pim_product_id'] = $this->validation->validateArray(StorePriceInterface::PRODUCT_ID, $param);
            $result['pim_company_code'] = $this->validation->validateArray(StorePriceInterface::COMPANY_CODE, $param);

            $result['normal_purchase_price'] = $this->validation->validateArray('normal_purchase_price', $param);
            $result['promo_purchase_price'] = $this->validation->validateArray('promo_purchase_price', $param);

            //$result['is_exclusive'] = $this->validation->validateArray('is_exclusive', $param);
            //$result['start_date'] = $this->validation->validateArray('start_date', $param);
            //$result['end_date'] = $this->validation->validateArray('end_date', $param);
            //$result['modified_at'] = $this->validation->validateArray('modified_at', $param);

            //$result['staging_id'] = $this->validation->validateArray('staging_id', $param);
            //$result['custom_status'] = $this->validation->validateArray('custom_status', $param);
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
     * Function for change product category reflect (bulk)
     */
    public function saveMultiPriceBulk($dataList)
    {
        $result = array();
        try {
            $this->logger->info('Before save multi price ' . date('d-M-Y H:i:s'));
            foreach ($dataList as $key => $value) {
                $result[$key] = $this->validateParams($dataList[$key]);
                $result[$key]['status'] = 1;
            }
            $tableName = $this->connection->getTableName('integration_catalog_store_price');
            $this->connection->insertOnDuplicate($tableName, $result);
            $this->logger->info("SKU Catalog Price Updated ---> \n ".print_r($result, true)."\n");
            return $result;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $this->logger->critical('Error occurred ' . $e->getMessage().' '.date('d-M-Y H:i:s'));
        }
        return false;
    }

    /**
     * Update Jobs Status
     * @param $jobId int
     * @param $status int
     * @param $msg string
     * @throw new StateException
     */
    protected function updateJobStatus($jobId, $status = 0, $msg = "")
    {
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
            \var_dump($e->getMessage());
            throw new StateException(
                __('Cannot Update Job Status! - '.$exception->getMessage())
            );
        }
    }

    /**
     * Update Data Value Status (bulk)
     */
    public function updateDataValueStatusBulk($multiPrices, $status = 0, $msg = "")
    {
        try {
            $ids = [];
            foreach ($multiPrices as $key => $value) {
                $ids[] = ['id' => $value['data_id'], 'status' => $status];
            }
            $tableName = $this->connection->getTableName(IntegrationDataValueInterface::TABLE_NAME);
            $this->connection->insertOnDuplicate($tableName, $ids);
        } catch (\Exception $e) {
            \var_dump($e->getMessage());
            $this->logger->critical('Error occurred ' . $e->getMessage().' '.date('d-M-Y H:i:s'));
        }
    }

    /**
     * Update Data Value Status
     * @param $jobId int
     * @param $status int
     * @param $msg string
     * @throw new StateException
     */
    protected function updateDataValueStatus($dataId, $status = 0, $msg = "")
    {
        if (empty($dataId)) {
            throw new NoSuchEntityException(__('Data ID doesn\'t exist'));
        }
        try {
            $query = $this->integrationDataValueRepositoryInterface->getById($dataId);
            $query->setStatus($status);
            if (!empty($msg)) {
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
    protected function createAttributeCode($param = "")
    {
        try {
            if (!isset($param['store_code'])||empty($param['store_code'])) {
                return null;
            }

            $query = $this->storePriceRepositoryInterface->loadDataByStoreCode($param['store_code']);

            if (empty($query)) {
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

    public function checkMultiPriceExist($sku, $storeCode)
    {
        try {
            $collection = $this->storePriceInterfaceFactory->create()->getCollection();
            $collection->addFieldToFilter(StorePriceInterface::SOURCE_CODE, $storeCode);
            $collection->addFieldToFilter(StorePriceInterface::SKU, $sku);

            $result = null;
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
    protected function saveAttributeProduct($attributeCode, $sku)
    {
        $attributeId = null;
        try {
            $attributeData = $this->eavAttribute->getCollection()->addFieldToFilter('attribute_code', $attributeCode);
            if ($attributeData->getSize() > 0) {
                foreach ($attributeData as $attributeDatas) {
                    $attributeId = $attributeDatas->getAttributeId();
                }
            }
            if (!$attributeId) {
                $attributeId = $this->createAttributeProduct($sku, $attributeCode);
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
    protected function createAttributeProduct($sku, $attributeCode = "")
    {
        try {
            $frontentInput = StorePriceLogicInterface::INPUT_TYPE_FRONTEND_FORMAT_PRICE;
            $backendInput = StorePriceLogicInterface::INPUT_TYPE_BACKEND_FORMAT_PRICE;

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

            $attributeId = $this->productAttributeRepository->save($attributeValue);
            
            //Set Attribute to Attribute Set (Default)
            $this->productAttributeManagement->assign(
                $this->attrGroupGeneralInfoId, 
                $this->attrGroupProductDetailId, 
                $attributeCode, 
                StorePriceLogicInterface::SORT_ORDER
            );

            //Set Attribute to Attribute Set (attribute set base on SKU)
            $attributeSetId = $this->getAttributeSetId($sku);
            $this->logger->info($sku . ' = ' . $attributeSetId);
            if ($attributeSetId != $this->attrGroupGeneralInfoId) {
                try {
                    $attrGroup = $this->integrationAttributeRepository->getAttributeGroupIdBySet(
                        IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT,
                        $attributeSetId
                    );
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
        return $attributeId;
    }

    /**
     * get attribute set id
     *
     * @param $sku string
     * @return
     */
    protected function getAttributeSetId($sku)
    {
        try {
            $product = $this->productRepositoryInterface->get($sku);
            return $product->getAttributeSetId();
        } catch (\Exception $exception) {
            throw new StateException(
                __(__FUNCTION__." - ".$exception->getMessage())
            );
        }
    }

    protected function validateSku($sku, $data) {		
		$skuUpperCase = strtoupper($sku);
		if (isset($data[$skuUpperCase])) {
			return $skuUpperCase;
		}
		
		$skuLowerCase = strtoupper($sku);
		if (isset($data[$skuLowerCase])) {
			return $skuLowerCase;
		}

		return $sku;

	}
}
