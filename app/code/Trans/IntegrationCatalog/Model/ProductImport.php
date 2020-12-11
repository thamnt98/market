<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Link;
use Magento\CatalogImportExport\Model\Import\Product\ImageTypeProcessor;
use Magento\CatalogImportExport\Model\Import\Product\MediaGalleryProcessor;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\CatalogImportExport\Model\StockItemImporterInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Store\Model\Store;
use Magento\CatalogImportExport\Model\Import\Product;

use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;

/**
 * Import entity product model
 *
 * @api
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @since 100.0.2
 */
class ProductImport extends \Magento\CatalogImportExport\Model\Import\Product
{
    const CONFIG_KEY_PRODUCT_TYPES = 'global/importexport/import_product_types';

    /**
     * Size of bunch - part of products to save in one step.
     */
    const BUNCH_SIZE = 20;

    /**
     * Size of bunch to delete attributes of products in one step.
     */
    const ATTRIBUTE_DELETE_BUNCH = 1000;

    /**
     * Pseudo multi line separator in one cell.
     *
     * Can be used as custom option value delimiter or in configurable fields cells.
     */
    const PSEUDO_MULTI_LINE_SEPARATOR = '|';

    /**
     * Symbol between Name and Value between Pairs.
     */
    const PAIR_NAME_VALUE_SEPARATOR = '=';

    /**
     * Value that means all entities (e.g. websites, groups etc.)
     */
    const VALUE_ALL = 'all';

    /**
     * Data row scopes.
     */
    const SCOPE_DEFAULT = 1;

    const SCOPE_WEBSITE = 2;

    const SCOPE_STORE = 0;

    const SCOPE_NULL = -1;

    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */

    /**
     * Column product store.
     */
    const COL_STORE = '_store';

    /**
     * Column product store view code.
     */
    const COL_STORE_VIEW_CODE = 'store_view_code';

    /**
     * Column website.
     */
    const COL_WEBSITE = 'website_code';

    /**
     * Column product attribute set.
     */
    const COL_ATTR_SET = '_attribute_set';

    /**
     * Column product type.
     */
    const COL_TYPE = 'product_type';

    /**
     * Column product category.
     */
    const COL_CATEGORY = 'categories';

    /**
     * Column product visibility.
     */
    const COL_VISIBILITY = 'visibility';

    /**
     * Column product sku.
     */
    const COL_SKU = 'sku';

    /**
     * Column product name.
     */
    // const COL_NAME = 'name';
    const COL_NAME = 'product_name';

    /**
     * Column product website.
     */
    const COL_PRODUCT_WEBSITES = '_product_websites';

    /**
     * Attribute code for media gallery.
     */
    const MEDIA_GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Column media image.
     */
    const COL_MEDIA_IMAGE = '_media_image';

    /**
     * Inventory use config label.
     */
    const INVENTORY_USE_CONFIG = 'Use Config';

    /**
     * Prefix for inventory use config.
     */
    const INVENTORY_USE_CONFIG_PREFIX = 'use_config_';

    /**
     * Url key attribute code
     */
    const URL_KEY = 'url_key';

    /**
     * @var array
     * @since 100.0.3
     */
    protected $rowNumbers = [];

    /**
     * Product entity link field
     *
     * @var string
     */
    private $productEntityLinkField;

    /**
     * Product entity identifier field
     *
     * @var string
     */
    private $productEntityIdentifierField;

    /**
     * Escaped separator value for regular expression.
     * The value is based on PSEUDO_MULTI_LINE_SEPARATOR constant.
     * @var string
     */
    private $multiLineSeparatorForRegexp;

    /**
     * Container for filesystem object.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Catalog config.
     *
     * @var CatalogConfig
     */
    private $catalogConfig;

    /**
     * Stock Item Importer
     *
     * @var StockItemImporterInterface
     */
    private $stockItemImporter;

    /**
     * @var ImageTypeProcessor
     */
    private $imageTypeProcessor;

    /**
     * Provide ability to process and save images during import.
     *
     * @var MediaGalleryProcessor
     */
    private $mediaProcessor;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollection;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Trans\Integration\Helper\AttributeOption
     */
    private $attributeOption;

    /**
     * @var \Trans\IntegrationEntity\Model\IntegrationProductAttributeRepository
     */
    private $attributeSet;

    /**
     * @var \Trans\IntegrationCatalog\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Brand\Api\BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var \Trans\Brand\Api\Data\BrandInterfaceFactory
     */
    protected $brandFactory;

    /**
     * @var \Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface
     */
    private $integrationProductRepository;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\ImportExport\Model\Import\Config $importConfig
     * @param Proxy\Product\ResourceModelFactory $resourceFactory
     * @param Product\OptionFactory $optionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory
     * @param Product\Type\Factory $productTypeFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory
     * @param Proxy\ProductFactory $proxyProdFactory
     * @param UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac
     * @param DateTime\TimezoneInterface $localeDate
     * @param DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param Product\StoreResolver $storeResolver
     * @param Product\SkuProcessor $skuProcessor
     * @param Product\CategoryProcessor $categoryProcessor
     * @param Product\Validator $validator
     * @param ObjectRelationProcessor $objectRelationProcessor
     * @param TransactionManagerInterface $transactionManager
     * @param Product\TaxClassProcessor $taxClassProcessor
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\Url $productUrl
     * @param \Trans\IntegrationEntity\Model\IntegrationProductAttributeRepository $attributeSet
     * @param array $data
     * @param array $dateAttrCodes
     * @param CatalogConfig $catalogConfig
     * @param ImageTypeProcessor $imageTypeProcessor
     * @param MediaGalleryProcessor $mediaProcessor
     * @param StockItemImporterInterface|null $stockItemImporter
     * @param DateTimeFactory $dateTimeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface|null $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Trans\IntegrationCatalog\Helper\Data $dataHelper
     * @param \Trans\Integration\Helper\AttributeOption $attributeOption
     * @param \Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface $integrationProductRepository
     * @param \Trans\Brand\Api\BrandRepositoryInterface $brandRepository
     * @param \Trans\Brand\Api\Data\BrandInterfaceFactory $brandFactory
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory,
        \Magento\CatalogImportExport\Model\Import\Product\OptionFactory $optionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory,
        \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory,
        \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory,
        \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory,
        \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        DateTime $dateTime,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        Product\StoreResolver $storeResolver,
        Product\SkuProcessor $skuProcessor,
        Product\CategoryProcessor $categoryProcessor,
        Product\Validator $validator,
        ObjectRelationProcessor $objectRelationProcessor,
        TransactionManagerInterface $transactionManager,
        Product\TaxClassProcessor $taxClassProcessor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Trans\IntegrationEntity\Model\IntegrationProductAttributeRepository $attributeSet,
        \Trans\Integration\Helper\AttributeOption $attributeOption,
        \Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface $integrationProductRepository,
        \Trans\IntegrationCatalog\Helper\Data $dataHelper,
        \Trans\Brand\Api\BrandRepositoryInterface $brandRepository,
        \Trans\Brand\Api\Data\BrandInterfaceFactory $brandFactory,
        array $data = [],
        array $dateAttrCodes = [],
        CatalogConfig $catalogConfig = null,
        ImageTypeProcessor $imageTypeProcessor = null,
        MediaGalleryProcessor $mediaProcessor = null,
        StockItemImporterInterface $stockItemImporter = null,
        DateTimeFactory $dateTimeFactory = null,
        ProductRepositoryInterface $productRepository = null
    ) {

        parent::__construct($jsonHelper, $importExportData, $importData, $config, $resource, $resourceHelper, $string, $errorAggregator, $eventManager, $stockRegistry, $stockConfiguration, $stockStateProvider, $catalogData, $importConfig, $resourceFactory, $optionFactory, $setColFactory, $productTypeFactory, $linkFactory, $proxyProdFactory, $uploaderFactory, $filesystem, $stockResItemFac, $localeDate, $dateTime, $logger, $indexerRegistry, $storeResolver, $skuProcessor, $categoryProcessor, $validator, $objectRelationProcessor, $transactionManager, $taxClassProcessor, $scopeConfig, $productUrl, $data, $dateAttrCodes, $catalogConfig, $imageTypeProcessor, $mediaProcessor, $stockItemImporter, $dateTimeFactory, $productRepository
        );

        $this->catalogConfig = $catalogConfig ?: ObjectManager::getInstance()->get(CatalogConfig::class);
        $this->productRepository = $productRepository ?? ObjectManager::getInstance()->get(ProductRepositoryInterface::class);
        $this->attributeSet = $attributeSet;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->attributeOption = $attributeOption;
        $this->categoryCollection = $categoryCollection;
        $this->attributeRepository = $attributeRepository;
        $this->integrationProductRepository = $integrationProductRepository;
        $this->dataHelper = $dataHelper;
        $this->brandRepository = $brandRepository;
        $this->brandFactory = $brandFactory;

        $this->attrGroupGeneralInfoCode = 'Default';
        $this->attrGroupGeneralInfoId = IntegrationProductInterface::ATTRIBUTE_SET_ID;
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_product.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
    }

    /**
     * Check one attribute. Can be overridden in child.
     *
     * @param string $attrCode Attribute code
     * @param array $attrParams Attribute params
     * @param array $rowData Row data
     * @param int $rowNum
     * @return bool
     */
    public function isAttributeValid($attrCode, array $attrParams, array $rowData, $rowNum)
    {
        if (!$this->validator->isAttributeValid($attrCode, $attrParams, $rowData)) {
            foreach ($this->validator->getMessages() as $message) {
                $this->skipRow($rowNum, $message, ProcessingError::ERROR_LEVEL_NOT_CRITICAL, $attrCode);
            }
            return false;
        }
        return true;
    }

    /**
     * Multiple value separator getter.
     *
     * @return string
     */
    public function getMultipleValueSeparator()
    {
        if (!empty($this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR])) {
            return $this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR];
        }
        return Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR;
    }

    /**
     * Return empty attribute value constant
     *
     * @return string
     * @since 101.0.0
     */
    public function getEmptyAttributeValueConstant()
    {
        if (!empty($this->_parameters[Import::FIELD_EMPTY_ATTRIBUTE_VALUE_CONSTANT])) {
            return $this->_parameters[Import::FIELD_EMPTY_ATTRIBUTE_VALUE_CONSTANT];
        }
        return Import::DEFAULT_EMPTY_ATTRIBUTE_VALUE_CONSTANT;
    }

    /**
     * Retrieve instance of product custom options import entity
     *
     * @return \Magento\CatalogImportExport\Model\Import\Product\Option
     */
    public function getOptionEntity()
    {
        return $this->_optionEntity;
    }

    /**
     * Retrieve id of media gallery attribute.
     *
     * @return int
     */
    public function getMediaGalleryAttributeId()
    {
        if (!$this->_mediaGalleryAttributeId) {
            /** @var $resource \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModel */
            $resource = $this->_resourceFactory->create();
            $this->_mediaGalleryAttributeId = $resource->getAttribute(self::MEDIA_GALLERY_ATTRIBUTE_CODE)->getId();
        }
        return $this->_mediaGalleryAttributeId;
    }

    /**
     * Retrieve product type by name.
     *
     * @param string $name
     * @return Product\Type\AbstractType
     */
    public function retrieveProductTypeByName($name)
    {
        if (isset($this->_productTypeModels[$name])) {
            return $this->_productTypeModels[$name];
        }
        return null;
    }

    /**
     * Set import parameters
     *
     * @param array $params
     * @return $this
     */
    public function setParameters(array $params)
    {
        parent::setParameters($params);
        $this->getOptionEntity()->setParameters($params);

        return $this;
    }

    /**
     * Delete products for replacement.
     *
     * @return $this
     */
    public function deleteProductsForReplacement()
    {
        $this->setParameters(
            array_merge(
                $this->getParameters(),
                ['behavior' => Import::BEHAVIOR_DELETE]
            )
        );
        $this->_deleteProducts();

        return $this;
    }

    /**
     * Delete products.
     *
     * @return $this
     * @throws \Exception
     */
    protected function _deleteProducts()
    {
        $productEntityTable = $this->_resourceFactory->create()->getEntityTable();

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $idsToDelete = [];

            foreach ($bunch as $rowNum => $rowData) {
                if ($this->validateRow($rowData, $rowNum) && self::SCOPE_DEFAULT == $this->getRowScope($rowData)) {
                    $idsToDelete[] = $this->getExistingSku($rowData[self::COL_SKU])['entity_id'];
                }
            }
            if ($idsToDelete) {
                $this->countItemsDeleted += count($idsToDelete);
                $this->transactionManager->start($this->_connection);
                try {
                    $this->objectRelationProcessor->delete(
                        $this->transactionManager,
                        $this->_connection,
                        $productEntityTable,
                        $this->_connection->quoteInto('entity_id IN (?)', $idsToDelete),
                        ['entity_id' => $idsToDelete]
                    );
                    $this->_eventManager->dispatch(
                        'catalog_product_import_bunch_delete_commit_before',
                        [
                            'adapter' => $this,
                            'bunch' => $bunch,
                            'ids_to_delete' => $idsToDelete,
                        ]
                    );
                    $this->transactionManager->commit();
                } catch (\Exception $e) {
                    $this->transactionManager->rollBack();
                    throw $e;
                }
                $this->_eventManager->dispatch(
                    'catalog_product_import_bunch_delete_after',
                    ['adapter' => $this, 'bunch' => $bunch]
                );
            }
        }
        return $this;
    }

    /**
     * Create Product entity from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _importData()
    {
        $this->_validatedRows = null;
        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->_deleteProducts();
        } elseif (Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->_replaceFlag = true;
            $this->_replaceProducts();
        } else {
            $this->_saveProductsData();
        }
        $this->_eventManager->dispatch('catalog_product_import_finish_before', ['adapter' => $this]);
        return true;
    }

    /**
     * Replace imported products.
     *
     * @return $this
     */
    protected function _replaceProducts()
    {
        $this->deleteProductsForReplacement();
        $this->_oldSku = $this->skuProcessor->reloadOldSkus()->getOldSkus();
        $this->_validatedRows = null;
        $this->setParameters(
            array_merge(
                $this->getParameters(),
                ['behavior' => Import::BEHAVIOR_APPEND]
            )
        );
        $this->_saveProductsData();

        return $this;
    }

    /**
     * Save products data.
     *
     * @return $this
     */
    protected function _saveProductsData()
    {
        $this->_saveProducts();
        foreach ($this->_productTypeModels as $productTypeModel) {
            $productTypeModel->saveData();
        }
        $this->_saveLinks();
        $this->_saveStockItem();
        if ($this->_replaceFlag) {
            $this->getOptionEntity()->clearProductsSkuToId();
        }
        $this->getOptionEntity()->importData();

        return $this;
    }

    /**
     * Initialize attribute sets code-to-id pairs.
     *
     * @return $this
     */
    protected function _initAttributeSets()
    {
        foreach ($this->_setColFactory->create()->setEntityTypeFilter($this->_entityTypeId) as $attributeSet) {
            $this->_attrSetNameToId[$attributeSet->getAttributeSetName()] = $attributeSet->getId();
            $this->_attrSetIdToName[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
        }
        return $this;
    }

    /**
     * Initialize existent product SKUs.
     *
     * @return $this
     */
    protected function _initSkus()
    {
        $this->skuProcessor->setTypeModels($this->_productTypeModels);
        $this->_oldSku = $this->skuProcessor->reloadOldSkus()->getOldSkus();
        return $this;
    }

    /**
     * Initialize image array keys.
     *
     * @return $this
     */
    private function initImagesArrayKeys()
    {
        $this->_imagesArrayKeys = $this->imageTypeProcessor->getImageTypes();
        return $this;
    }

    /**
     * Initialize product type models.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initTypeModels()
    {
        $productTypes = $this->_importConfig->getEntityTypes($this->getEntityTypeCode());
        foreach ($productTypes as $productTypeName => $productTypeConfig) {
            $params = [$this, $productTypeName];
            if (!($model = $this->_productTypeFactory->create($productTypeConfig['model'], ['params' => $params]))
            ) {
                throw new LocalizedException(
                    __('Entity type model \'%1\' is not found', $productTypeConfig['model'])
                );
            }
            if (!$model instanceof \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType) {
                throw new LocalizedException(
                    __(
                        'Entity type model must be an instance of '
                        . \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType::class
                    )
                );
            }
            if ($model->isSuitable()) {
                $this->_productTypeModels[$productTypeName] = $model;
            }
            // phpcs:disable Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $this->_fieldsMap = array_merge($this->_fieldsMap, $model->getCustomFieldsMapping());
            $this->_specialAttributes = array_merge($this->_specialAttributes, $model->getParticularAttributes());
            // phpcs:enable 
        }
        $this->_initErrorTemplates();
        // remove doubles
        $this->_specialAttributes = array_unique($this->_specialAttributes);

        return $this;
    }

    /**
     * Initialize Product error templates
     */
    protected function _initErrorTemplates()
    {
        foreach ($this->_messageTemplates as $errorCode => $template) {
            $this->addMessageTemplate($errorCode, $template);
        }
    }

    /**
     * Set valid attribute set and product type to rows.
     *
     * Set valid attribute set and product type to rows with all
     * scopes to ensure that existing products doesn't changed.
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareRowForDb(array $rowData)
    {
        $rowData = $this->_customFieldsMapping($rowData);

        $rowData = parent::_prepareRowForDb($rowData);

        static $lastSku = null;

        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            return $rowData;
        }

        $lastSku = $rowData[self::COL_SKU];

        if ($this->isSkuExist($lastSku)) {
            $newSku = $this->skuProcessor->getNewSku($lastSku);
            $rowData[self::COL_ATTR_SET] = $newSku['attr_set_code'];
            $rowData[self::COL_TYPE] = $newSku['type_id'];
        }

        return $rowData;
    }

    /**
     * Gather and save information about product links.
     *
     * Must be called after ALL products saving done.
     *
     * @return $this
     */
    protected function _saveLinks()
    {
        /** @var Link $resource */
        $resource = $this->_linkFactory->create();
        $mainTable = $resource->getMainTable();
        $positionAttrId = [];
        $nextLinkId = $this->_resourceHelper->getNextAutoincrement($mainTable);

        // pre-load 'position' attributes ID for each link type once
        foreach ($this->_linkNameToId as $linkId) {
            $select = $this->_connection->select()->from(
                $resource->getTable('catalog_product_link_attribute'),
                ['id' => 'product_link_attribute_id']
            )->where(
                'link_type_id = :link_id AND product_link_attribute_code = :position'
            );
            $bind = [':link_id' => $linkId, ':position' => 'position'];
            $positionAttrId[$linkId] = $this->_connection->fetchOne($select, $bind);
        }
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $this->processLinkBunches($bunch, $resource, $nextLinkId, $positionAttrId);
        }
        return $this;
    }

    /**
     * Save product attributes.
     *
     * @param array $attributesData
     * @return $this
     */
    protected function _saveProductAttributes(array $attributesData)
    {
        $linkField = $this->getProductEntityLinkField();
        foreach ($attributesData as $tableName => $skuData) {
            $tableData = [];
            foreach ($skuData as $sku => $attributes) {
                $linkId = $this->_oldSku[strtolower($sku)][$linkField];
                foreach ($attributes as $attributeId => $storeValues) {
                    foreach ($storeValues as $storeId => $storeValue) {
                        $tableData[] = [
                            $linkField => $linkId,
                            'attribute_id' => $attributeId,
                            'store_id' => $storeId,
                            'value' => $storeValue,
                        ];
                    }
                }
            }
            $this->_connection->insertOnDuplicate($tableName, $tableData, ['value']);
        }

        return $this;
    }

    /**
     * Save product categories.
     *
     * @param array $categoriesData
     * @return $this
     */
    protected function _saveProductCategories(array $categoriesData)
    {
        static $tableName = null;

        if (!$tableName) {
            $tableName = $this->_resourceFactory->create()->getProductCategoryTable();
        }
        if ($categoriesData) {
            $categoriesIn = [];
            $delProductId = [];

            foreach ($categoriesData as $delSku => $categories) {
                $productId = $this->skuProcessor->getNewSku($delSku)['entity_id'];
                $delProductId[] = $productId;

                if($this->checkSequenceProduct($productId)) {
                    foreach (array_keys($categories) as $categoryId) {
                        $categoriesIn[] = ['product_id' => $productId, 'category_id' => $categoryId, 'position' => 0];
                    }
                }
            }
            if (Import::BEHAVIOR_APPEND != $this->getBehavior()) {
                $this->_connection->delete(
                    $tableName,
                    $this->_connection->quoteInto('product_id IN (?)', $delProductId)
                );
            }
            if ($categoriesIn) {
                $this->_connection->insertOnDuplicate($tableName, $categoriesIn, ['product_id', 'category_id']);
            }
        }
        return $this;
    }

    /**
     * Update and insert data in entity table.
     *
     * @param array $entityRowsIn Row for insert
     * @param array $entityRowsUp Row for update
     * @return $this
     * @since 100.1.0
     */
    public function saveProductEntity(array $entityRowsIn, array $entityRowsUp)
    {
        $this->logger->info('Start ' . __FUNCTION__ . ' ' . date('H:i:s'));
        static $entityTable = null;
        $this->countItemsCreated += count($entityRowsIn);
        $this->countItemsUpdated += count($entityRowsUp);

        if (!$entityTable) {
            $entityTable = $this->_resourceFactory->create()->getEntityTable();
        }
        if ($entityRowsUp) {
            $this->_connection->insertOnDuplicate($entityTable, $entityRowsUp, ['updated_at', 'attribute_set_id']);
        }
        if ($entityRowsIn) {
            $this->_connection->insertMultiple($entityTable, $entityRowsIn);

            $select = $this->_connection->select()->from(
                $entityTable,
                array_merge($this->getNewSkuFieldsForSelect(), $this->getOldSkuFieldsForSelect())
            )->where(
                $this->_connection->quoteInto('sku IN (?)', array_keys($entityRowsIn))
            );
            $newProducts = $this->_connection->fetchAll($select);
            foreach ($newProducts as $data) {
                $sku = $data['sku'];
                unset($data['sku']);
                foreach ($data as $key => $value) {
                    $this->skuProcessor->setNewSkuData($sku, $key, $value);
                }
            }

            $this->updateOldSku($newProducts);
        }

        $this->logger->info('End ' . __FUNCTION__ . ' ' . date('H:i:s'));

        return $this;
    }

    /**
     * Return additional data, needed to select.
     *
     * @return array
     */
    private function getOldSkuFieldsForSelect()
    {
        return ['type_id', 'attribute_set_id'];
    }

    /**
     * Adds newly created products to _oldSku
     *
     * @param array $newProducts
     * @return void
     */
    private function updateOldSku(array $newProducts)
    {
        $oldSkus = [];
        foreach ($newProducts as $info) {
            $typeId = $info['type_id'];
            $sku = strtolower($info['sku']);
            $oldSkus[$sku] = [
                'type_id' => $typeId,
                'attr_set_id' => $info['attribute_set_id'],
                $this->getProductIdentifierField() => $info[$this->getProductIdentifierField()],
                'supported_type' => isset($this->_productTypeModels[$typeId]),
                $this->getProductEntityLinkField() => $info[$this->getProductEntityLinkField()],
            ];
        }

        $this->_oldSku = array_replace($this->_oldSku, $oldSkus);
    }

    /**
     * Get new SKU fields for select
     *
     * @return array
     */
    private function getNewSkuFieldsForSelect()
    {
        $fields = ['sku', $this->getProductEntityLinkField()];
        if ($this->getProductEntityLinkField() != $this->getProductIdentifierField()) {
            $fields[] = $this->getProductIdentifierField();
        }
        return $fields;
    }

    /**
     * Init media gallery resources
     *
     * @return void
     * @since 100.0.4
     * @deprecated 100.2.3
     */
    protected function initMediaGalleryResources()
    {
        if (null == $this->mediaGalleryTableName) {
            $this->productEntityTableName = $this->getResource()->getTable('catalog_product_entity');
            $this->mediaGalleryTableName = $this->getResource()->getTable('catalog_product_entity_media_gallery');
            $this->mediaGalleryValueTableName = $this->getResource()->getTable(
                'catalog_product_entity_media_gallery_value'
            );
            $this->mediaGalleryEntityToValueTableName = $this->getResource()->getTable(
                'catalog_product_entity_media_gallery_value_to_entity'
            );
        }
    }

    /**
     * Get existing images for current bunch
     *
     * @param array $bunch
     * @return array
     */
    protected function getExistingImages($bunch)
    {
        return $this->mediaProcessor->getExistingImages($bunch);
    }

    /**
     * Retrieve image from row.
     *
     * @param array $rowData
     * @return array
     */
    public function getImagesFromRow(array $rowData)
    {
        $images = [];
        $labels = [];
        foreach ($this->_imagesArrayKeys as $column) {
            if (!empty($rowData[$column])) {
                $images[$column] = array_unique(
                    array_map(
                        'trim',
                        explode($this->getMultipleValueSeparator(), $rowData[$column])
                    )
                );

                if (!empty($rowData[$column . '_label'])) {
                    $labels[$column] = $this->parseMultipleValues($rowData[$column . '_label']);

                    if (count($labels[$column]) > count($images[$column])) {
                        $labels[$column] = array_slice($labels[$column], 0, count($images[$column]));
                    }
                }
            }
        }

        return [$images, $labels];
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
            $result['is_deleted']       = $this->validation->validateArray(IntegrationProductInterface::DELETED, $dataProduct);
            $result['is_active']        = $this->validation->validateArray(IntegrationProductInterface::IS_ACTIVE, $dataProduct);

            $result['sku']              = $this->validation->validateArray(IntegrationProductInterface::SKU, $dataProduct);
            $result['product_name']     = $this->validation->validateArray(IntegrationProductInterface::NAME, $dataProduct);
            $result['category_id']      = $this->validation->validateArray(IntegrationProductInterface::CTGID, $dataProduct);
            $result['price']            = $this->validation->validateArray(IntegrationProductInterface::PRICE, $dataProduct);

            $result['weight']           = $this->validation->validateArray(IntegrationProductInterface::WEIGHT, $dataProduct);
            $result['height']           = $this->validation->validateArray(IntegrationProductInterface::HEIGHT, $dataProduct);
            $result['length']           = $this->validation->validateArray(IntegrationProductInterface::LENGTH, $dataProduct);
            $result['width']            = $this->validation->validateArray(IntegrationProductInterface::WIDTH, $dataProduct);
            

            $result['active']           = $this->validation->validateArray(IntegrationProductInterface::IS_ACTIVE, $dataProduct);
            $result['short_desc']       = $this->validation->validateArray(IntegrationProductInterface::SHORT_DESC, $dataProduct);
            $result['long_desc']        = $this->validation->validateArray(IntegrationProductInterface::LONG_DESC, $dataProduct);
            $result['data_attributes']  = $this->validation->validateArray(IntegrationProductInterface::ATTRIBUTES, $dataProduct);

            $result['pim_id']           = $this->validation->validateArray(IntegrationProductInterface::ID, $dataProduct);
            
            $result[IntegrationProductInterface::COMPANY_CODE] = $this->validation->validateArray(IntegrationProductInterface::COMPANY_CODE, $dataProduct);

            $result[IntegrationProductInterface::PRODUCT_TYPE]  = IntegrationProductInterface::PRODUCT_TYPE_SIMPLE_VALUE;
            if(isset($dataProduct[IntegrationProductInterface::PRODUCT_TYPE]) && (strtolower($dataProduct[IntegrationProductInterface::PRODUCT_TYPE])==IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_LABEL)){
                $result[IntegrationProductInterface::PRODUCT_TYPE]  =IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_VALUE;
            }

            $result[IntegrationProductInterface::CATALOG_TYPE]  =IntegrationProductInterface::CATALOG_TYPE_SIMPLE_VALUE;
            if(!empty($result[IntegrationProductInterface::PRODUCT_TYPE]) && ($result[IntegrationProductInterface::PRODUCT_TYPE]==IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_VALUE)){
                $result[IntegrationProductInterface::CATALOG_TYPE]  =IntegrationProductInterface::CATALOG_TYPE_DIGITAL_VALUE;
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
     * prepare brand data
     *
     * @param array $dataProduct
     * @return array|bool
     */
    protected function prepareBrand($dataProduct = [])
    {
        $brandVal = '';
        if(isset($dataProduct[IntegrationProductInterface::BRAND])) {
            $brandAttributeCode = $this->dataHelper->getBrandAttributeCode();
            try {
                $brandData = $this->brandRepository->getByPimId($dataProduct[IntegrationProductInterface::BRAND]);

                $brandVal = $brandData->getTitle();
                // $this->saveAttributeDataByType($product, $brandAttributeCode, $brandData->getTitle());
            } catch (NoSuchEntityException $e) {
                $brand = $this->brandFactory->create();
                $brand->setPimId($dataProduct[IntegrationProductInterface::BRAND]);
                $brand->setCode($dataProduct[IntegrationProductInterface::BRAND_CODE]);
                $brand->setTitle($dataProduct[IntegrationProductInterface::BRAND_NAME]);
                $brand->setData('company_code', $dataProduct[IntegrationProductInterface::COMPANY_CODE]);

                try {
                    $brandData = $this->brandRepository->save($brand);
                    $brandVal = $brandData->getTitle();
                    $this->logger->info('Create trans_brand data. PIM_ID = ' . $dataProduct[IntegrationProductInterface::BRAND]);
                    // $this->saveAttributeDataByType($product, $brandAttributeCode, $brandData->getTitle());
                } catch (CouldNotSaveException $e) {
                    $this->logger->info('Error create/set brand. SKU = ' . $product->getSku() . ' brand = ' . $dataProduct[IntegrationProductInterface::BRAND] . ' Msg: ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                $this->logger->info('Error create/set brand. SKU = ' . $product->getSku() . ' brand = ' . $dataProduct[IntegrationProductInterface::BRAND] . ' Msg: ' . $e->getMessage());
            }

            return ['attribute_code' => $brandAttributeCode, 'attribute_value' => $brandVal];
        }

        return false;
    }

    /**
     * Gather and save information about product entities.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @throws LocalizedException
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function syncProduct($bunch = null)
    {
        $websiteId = $this->storeManager->getDefaultStoreView()->getWebsiteId();
        $website = $this->storeManager->getWebsite($websiteId);
    
        $priceIsGlobal = $this->_catalogData->isPriceGlobal();
        $productLimit = null;
        $productsQty = null;
        $entityLinkField = $this->getProductEntityLinkField();

        $bunch = $bunch->getData();
        
        if ($bunch != false) {
            $entityRowsIn = [];
            $entityRowsUp = [];
            $attributes = [];
            $this->websitesCache = [];
            $this->categoriesCache = [];
            $tierPrices = [];
            $mediaGallery = [];
            $labelsForUpdate = [];
            $imagesForChangeVisibility = [];
            $uploadedImages = [];
            $previousType = null;
            $prevAttributeSet = null;
    
            foreach ($bunch as $rowNum => $rowData) {
                if(!isset($rowData['data_value_id'])) {
                    $dataValueId = $rowData['id'];
                }

                if(isset($rowData['data_value'])) {
                    $rowData = json_decode($rowData['data_value'], true);
                }

                if($dataValueId) {
                    $rowData['data_value_id'] = $dataValueId;
                }
                
                $rowData[self::URL_KEY] = null;

                // reset category processor's failed categories array
                $this->categoryProcessor->clearFailedCategories();

                $rowScope = $this->getRowScope($rowData);

                $urlKey = $this->getUrlKey($rowData);

                $attributeSet = $this->prepareAttributeSet($rowData);

                $rowData['name'] = $rowData['product_name'];
                $rowData['price'] = 0;
                $rowData['attribute_set_code'] = $attributeSet['attribute_set_code'];
                $rowData['_attribute_set'] = $attributeSet['attribute_set_code'];
                $rowData['website_id'] = $websiteId;
                $rowData['_product_websites'] = $website->getCode();
                $rowData['product_websites'] = $website->getCode();
                $rowData['status'] = $rowData['is_active'];

                $productBrand = $this->prepareBrand($rowData);

                if(isset($productBrand['attribute_value'])) {
                    $rowData[$productBrand['attribute_code']] = $productBrand['attribute_value'];
                }

                $visibility = 'Catalog, Search';
                if ($this->integrationProductRepository->checkPosibilityConfigurable($rowData['sku'])) {
                    $visibility = 'Not Visible Individually';
                }

                $rowData['visibility'] = $visibility;

                try {
                    $rowData['categories'] = $this->prepareCategories($rowData);
                } catch (\Exception $e) {
                    $this->logger->info($e->getMessage() . ' $sku ' . $rowData['sku']);
                    unset($bunch[$rowNum]);
                    continue;
                }

                $multiselect = [];
                if(isset($rowData['list_attributes'])) {

                    $sellingUnit = substr($rowData['sku'], -3); //get 3 last char from SKU 
                    $attributeCode = IntegrationProductInterface::SELLING_UNIT_CODE;
                    
                    $rowData['list_attributes'][] = [
                        "attribute_code" => $attributeCode,
                        "attribute_value" => ltrim($sellingUnit, '0')
                    ];

                    foreach ($rowData['list_attributes'] as $listVal) {
                        $checkAttribute = $this->config->getAttribute(IntegrationProductInterface::ENTITY_TYPE_CODE, $listVal['attribute_code']);

                        if($checkAttribute->getFrontendInput() != 'multiselect') {
                            $rowData[$listVal['attribute_code']] = $listVal['attribute_value'];
                        } else {
                            $attrVal = $this->saveAttributeDataByType($listVal['attribute_code'], strtolower($listVal['attribute_value']));

                            $data['code'] = $listVal['attribute_code'];
                            $data['value'] = $attrVal;

                            $multiselect[] = $data;

                            $this->logger->info('insert option ' . date('H:i:s'));
                            $rowData[$listVal['attribute_code']] = $attrVal;
                        }
                    }

                    // unset($rowData['list_attributes']);
                }

                if (!empty($rowData[self::URL_KEY])) {
                    // If url_key column and its value were in the CSV file
                    $rowData[self::URL_KEY] = $urlKey;
                } elseif ($this->isNeedToChangeUrlKey($rowData)) {
                    // If url_key column was empty or even not declared in the CSV file but by the rules it is need to
                    // be setteed. In case when url_key is generating from name column we have to ensure that the bunch
                    // of products will pass for the event with url_key column.
                    $bunch[$rowNum][self::URL_KEY] = $rowData[self::URL_KEY] = $urlKey;
                }

                $rowSku = $rowData[self::COL_SKU];

                if (null === $rowSku) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                if (self::SCOPE_STORE == $rowScope) {
                    // set necessary data from SCOPE_DEFAULT row
                    $rowData[self::COL_TYPE] = $this->skuProcessor->getNewSku($rowSku)['type_id'];
                    // $rowData['attribute_set_id'] = $this->skuProcessor->getNewSku($rowSku)['attr_set_id'];
                    $rowData['attribute_set_id'] = $attributeSet['attribute_set_id'];
                    $rowData[self::COL_ATTR_SET] = $attributeSet['attribute_set_code'];
                }

                $bunch[$rowNum] = $rowData;

                try {
                    // 1. Entity phase
                    if ($this->isSkuExist($rowSku)) {
                        // existing row
                        if (isset($rowData['attribute_set_code'])) {
                            $attributeSetId = $this->catalogConfig->getAttributeSetId(
                                $this->getEntityTypeId(),
                                $rowData['attribute_set_code']
                            );

                            // wrong attribute_set_code was received
                            if (!$attributeSetId) {
                                throw new LocalizedException(
                                    __(
                                        'Wrong attribute set code "%1", please correct it and try again.',
                                        $rowData['attribute_set_code']
                                    )
                                );
                            }
                        } else {
                            $attributeSetId = $this->skuProcessor->getNewSku($rowSku)['attr_set_id'];
                        }

                        $entityRowsUp[] = [
                            'updated_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                            'attribute_set_id' => $attributeSetId,
                            $entityLinkField => $this->getExistingSku($rowSku)[$entityLinkField]
                        ];
                    } else {
                        if (!$productLimit || $productsQty < $productLimit) {
                            $entityRowsIn[strtolower($rowSku)] = [
                                'attribute_set_id' => $attributeSet['attribute_set_id'],
                                'type_id' => strtolower($rowData['product_type']),
                                'sku' => $rowSku,
                                'has_options' => isset($rowData['has_options']) ? $rowData['has_options'] : 0,
                                'created_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                                'updated_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                            ];
                            
                            $productsQty++;
                        } else {
                            $rowSku = null;
                            // sign for child rows to be skipped
                            $this->getErrorAggregator()->addRowToSkip($rowNum);
                            continue;
                        }
                    }

                    if (!array_key_exists($rowSku, $this->websitesCache)) {
                        $this->websitesCache[$rowSku] = [];
                    }

                    // 2. Product-to-Website phase
                    if (!empty($rowData[self::COL_PRODUCT_WEBSITES])) {
                        $websiteCodes = explode($this->getMultipleValueSeparator(), $rowData[self::COL_PRODUCT_WEBSITES]);
                        foreach ($websiteCodes as $websiteCode) {
                            $websiteId = $this->storeResolver->getWebsiteCodeToId($websiteCode);
                            $this->websitesCache[$rowSku][$websiteId] = true;
                        }
                    } else {
                        $product = $this->retrieveProductBySku($rowSku);
                        if ($product) {
                            $websiteIds = $product->getWebsiteIds();
                            foreach ($websiteIds as $websiteId) {
                                $this->websitesCache[$rowSku][$websiteId] = true;
                            }
                        }
                    }

                    // 3. Categories phase
                    if (!array_key_exists($rowSku, $this->categoriesCache)) {
                        $this->categoriesCache[$rowSku] = [];
                    }
                    $rowData['rowNum'] = $rowNum;
                    $categoryIds = $this->processRowCategories($rowData);
                    foreach ($categoryIds as $id) {
                        $this->categoriesCache[$rowSku][$id] = true;
                    }
                    unset($rowData['rowNum']);

                    // 6. Attributes phase
                    $rowStore = (self::SCOPE_STORE == $rowScope)
                        ? $this->storeResolver->getStoreCodeToId($rowData[self::COL_STORE])
                        : 0;
                    $productType = isset($rowData[self::COL_TYPE]) ? strtolower($rowData[self::COL_TYPE]) : null;
                    if ($productType !== null) {
                        $previousType = $productType;
                    }
                    if (isset($rowData[self::COL_ATTR_SET])) {
                        $prevAttributeSet = $rowData[self::COL_ATTR_SET];
                    }
                    if (self::SCOPE_NULL == $rowScope) {
                        // for multiselect attributes only
                        if ($prevAttributeSet !== null) {
                            $rowData[self::COL_ATTR_SET] = $prevAttributeSet;
                        }
                        if ($productType === null && $previousType !== null) {
                            $productType = $previousType;
                        }
                        if ($productType === null) {
                            continue;
                        }
                    }

                    $productTypeModel = $this->_productTypeModels[$productType];

                    if(!empty($multiselect)) {
                        foreach($multiselect as $multi) {
                            $productTypeModel->addAttributeOption($multi['code'], strtolower($multi['value']), strtolower($multi['value']));
                        }
                    }

                    if (!empty($rowData['tax_class_name'])) {
                        $rowData['tax_class_id'] =
                            $this->taxClassProcessor->upsertTaxClass($rowData['tax_class_name'], $productTypeModel);
                    }

                    if ($this->getBehavior() == Import::BEHAVIOR_APPEND ||
                        empty($rowData[self::COL_SKU])
                    ) {
                        $rowData = $productTypeModel->clearEmptyData($rowData);
                    }

                    $rowData = $productTypeModel->prepareAttributesWithDefaultValueForSave(
                        $rowData,
                        !$this->isSkuExist($rowSku)
                    );
                    $product = $this->_proxyProdFactory->create(['data' => $rowData]);

                    foreach ($rowData as $attrCode => $attrValue) {
                        $attribute = $this->retrieveAttributeByCode($attrCode);

                        if ('multiselect' != $attribute->getFrontendInput() && self::SCOPE_NULL == $rowScope) {
                            // skip attribute processing for SCOPE_NULL rows
                            continue;
                        }
                        $attrId = $attribute->getId();
                        $backModel = $attribute->getBackendModel();
                        $attrTable = $attribute->getBackend()->getTable();
                        $storeIds = [0];

                        if ('datetime' == $attribute->getBackendType()
                            && (
                                in_array($attribute->getAttributeCode(), $this->dateAttrCodes)
                                || $attribute->getIsUserDefined()
                            )
                        ) {
                            $attrValue = $this->dateTime->formatDate($attrValue, false);
                        } elseif ('datetime' == $attribute->getBackendType() && strtotime($attrValue)) {
                            $attrValue = gmdate(
                                'Y-m-d H:i:s',
                                $this->_localeDate->date($attrValue)->getTimestamp()
                            );
                        } elseif ($backModel) {
                            $attribute->getBackend()->beforeSave($product);
                            $attrValue = $product->getData($attribute->getAttributeCode());
                        }
                        if (self::SCOPE_STORE == $rowScope) {
                            if (self::SCOPE_WEBSITE == $attribute->getIsGlobal()) {
                                // check website defaults already set
                                if (!isset($attributes[$attrTable][$rowSku][$attrId][$rowStore])) {
                                    $storeIds = $this->storeResolver->getStoreIdToWebsiteStoreIds($rowStore);
                                }
                            } elseif (self::SCOPE_STORE == $attribute->getIsGlobal()) {
                                $storeIds = [$rowStore];
                            }
                            if (!$this->isSkuExist($rowSku)) {
                                $storeIds[] = 0;
                            }
                        }
                        foreach ($storeIds as $storeId) {
                            if (!isset($attributes[$attrTable][$rowSku][$attrId][$storeId])) {
                                $attributes[$attrTable][$rowSku][$attrId][$storeId] = $attrValue;
                            }
                        }

                        // restore 'backend_model' to avoid 'default' setting
                        $attribute->setBackendModel($backModel);
                    }
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    continue;
                }
            }

            foreach ($bunch as $rowNum => $rowData) {
                if ($this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    unset($bunch[$rowNum]);
                }
            }

            $this->saveProductEntity(
                $entityRowsIn,
                $entityRowsUp
            )->_saveProductWebsites(
                $this->websitesCache
            )->_saveProductCategories(
                $this->categoriesCache
            )->_saveProductTierPrices(
                $tierPrices
            )->_saveMediaGallery(
                $mediaGallery
            )->_saveProductAttributes(
                $attributes
            )->updateMediaGalleryVisibility(
                $imagesForChangeVisibility
            )->updateMediaGalleryLabels(
                $labelsForUpdate
            );

            // $this->logger->info('$butnch = ' . json_encode($bunch));
            
            $this->_eventManager->dispatch(
                'catalog_product_import_bunch_save_after',
                ['adapter' => $this, 'bunch' => $bunch]
            );
        }

        return $this;
    }
    //phpcs:enable Generic.Metrics.NestingLevel

    /**
     * prepare attribute set data
     *
     * @param array $productData
     * @return array
     */
    protected function prepareAttributeSet(array $productData, $type = 'simple')
    {
        $defaultCode = $this->attrGroupGeneralInfoCode;
        $defaultId = $this->attrGroupGeneralInfoId;

        $result['attribute_set_code'] = $defaultCode;
        $result['attribute_set_id'] = $defaultId;

        switch ($type) {
            case 'simple':
                if(isset($productData['category_id']) && $productData['category_id']) {
                    foreach($productData['category_id'] as $pimId) {
                        try {
                            $attributeSetCode = $this->attributeSet->getAttributeSetCodeByPimId($pimId);
                            $attributeSetId = $this->attributeSet->getAttributeSetIdByPimId($pimId);
                            if($attributeSet) {
                                $result['attribute_set_id'] = $attributeSetId;
                                $result['attribute_set_code'] = $attributeSetCode;
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
                            $product = $this->productRepository->getById($child);
                        } catch (NoSuchEntityException $e) {
                            $product = null;
                            continue;
                        }

                        if($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
                            if($product->getAttributeSetId()) {
                                if((int)$product->getAttributeSetId() != $default) {
                                    $attributeSet = $this->attributeSet->getAttributeSetCodeByAttrSetId($product->getAttributeSetId());
                                    $result['attribute_set_code'] = $attributeSet;
                                    $result['attribute_set_id'] = $product->getAttributeSetId();
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
     * @param string $attributeCode
     * @param array $dataProduct
     * @return boolean
     */
    protected function saveAttributeDataByType($attributeCode, $attributeValue)
    {
        // $attributeValue = ucwords($attributeValue);

        try{
            $attributeData = $this->config->getAttribute(IntegrationProductInterface::ENTITY_TYPE_CODE, $attributeCode);
            if ($attributeData->getId()) {

                $frontendInputArr = [IntegrationProductInterface::FRONTEND_INPUT_TYPE_SELECT, IntegrationProductInterface::FRONTEND_INPUT_TYPE_MULTISELECT];
                if ($attributeData->getAdditionalData() || (in_array($attributeData->getFrontendInput(), $frontendInputArr))) {
                    $attrVal = $this->attributeOption->createOrGetId(
                            $attributeCode, 
                            $attributeValue
                        );

                    $attributeValue = $attributeData->getBackendType() == 'int' ? (int) $attrVal : $attrVal;

                    if($attributeData->getBackendType() == 'int' && $attributeValue == 0) {
                        $attributeValue = NULL;
                    }
                } else {
                    $attributeValue = $attributeData->getBackendType() == 'int' ? (int) $attributeValue : $attributeValue;
                }
            }
        } catch (\Exception $exception) {
            $this->logger->info(__FUNCTION__."------ ERROR ".$exception->getMessage());
        }

        return $attributeValue;
    }

    /**
     * Prepare Categories
     *
     * @param array @rowData
     * @return string
     */
    public function prepareCategories($rowData)
    {
        try {
            $pimCategoryIds = $rowData['category_id'];
            $categories = $this->categoryCollection($pimCategoryIds);
            
            $paths = [];
            $pathCategoriesArray = [];
            foreach($categories as $category) {
                try {
                    $path = $category->getPath();
                    $categoryIds = explode('/', $path);
                    
                    $pathCategories = $this->categoryCollection($categoryIds, false);
                    
                    $pathString = [];
                    if($pathCategories) {
                        foreach($pathCategories as $pathCtg) {
                            $pathString[] = $pathCtg->getName();
                        }
                    }
                    $pathStringArray = implode('/', $pathString);

                    $pathCategoriesArray[] = $pathStringArray;
                } catch (\Exception $ex) {
                    $this->logger->info('error ' . __FUNCTION__ . ' ' . $ex->getMessage() . '. Continue');
                    continue;
                }
            }

            return implode(',', $pathCategoriesArray);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' ' . __FUNCTION__ . ' $pimCategoryIds: ' . print_r($pimCategoryIds, true));
        }
    }

    /**
     * get category collection
     * 
     * @param array $pimIds
     * @param bool $usePimId
     */
    protected function categoryCollection($pimIds = [], $usePimId = true)
    {
        $this->logger->info('Start ' . __FUNCTION__ . ' ' . date('H:i:s'));
        $nameEavId = $this->getCategoryAttributeId('name');
        $result = false;

        if(is_array($pimIds) && !empty($pimIds)) {
            $string = implode('","', $pimIds);
        }

        $column = 'integration_category.pim_id';
        if(!$usePimId) {
            $column = 'integration_category.magento_entity_id';
        }

        if(!empty($string)) {
            $collection = $this->categoryCollection->create();
            $collection->getSelect()->join(
                ['integration_category'],
                'e.entity_id = integration_category.magento_entity_id',
                []
            )->columns('integration_category.magento_entity_id')
            ->where('' . $column . ' in ("' . $string . '")');
            $collection->getSelect()->join(
                ['catalog_category_entity_varchar'],
                'e.row_id = catalog_category_entity_varchar.row_id',
                []
            )->columns('catalog_category_entity_varchar.value as name')
            ->where('attribute_id = ' . $nameEavId);
            $collection->getSelect()->group('e.entity_id');

            // echo $collection->getSelect();

            if($collection->getSize()) {
                $result = $collection;
            }
        }

        $this->logger->info('End ' . __FUNCTION__ . ' ' . date('H:i:s'));

        return $result;
    }

    /**
     * get category attribute id
     *
     * @param string $code
     * @return int
     */
    public function getCategoryAttributeId($code)
    {
        $attribute = $this->attributeRepository->get(Category::ENTITY, $code);
        return $attribute->getAttributeId();
    }

    /**
     * Prepare array with image states (visible or hidden from product page)
     *
     * @param array $rowData
     * @return array
     */
    private function getImagesHiddenStates($rowData)
    {
        $statesArray = [];
        $mappingArray = [
            '_media_is_disabled' => '1'
        ];

        foreach ($mappingArray as $key => $value) {
            if (isset($rowData[$key]) && strlen(trim($rowData[$key]))) {
                $items = explode($this->getMultipleValueSeparator(), $rowData[$key]);

                foreach ($items as $item) {
                    $statesArray[$item] = $value;
                }
            }
        }

        return $statesArray;
    }

    /**
     * Resolve valid category ids from provided row data.
     *
     * @param array $rowData
     * @return array
     */
    protected function processRowCategories($rowData)
    {
        $categoriesString = empty($rowData[self::COL_CATEGORY]) ? '' : $rowData[self::COL_CATEGORY];
        $categoryIds = [];
        if (!empty($categoriesString)) {
            $categoryIds = $this->categoryProcessor->upsertCategories(
                $categoriesString,
                $this->getMultipleValueSeparator()
            );
            foreach ($this->categoryProcessor->getFailedCategories() as $error) {
                $this->errorAggregator->addError(
                    AbstractEntity::ERROR_CODE_CATEGORY_NOT_VALID,
                    ProcessingError::ERROR_LEVEL_NOT_CRITICAL,
                    $rowData['rowNum'],
                    self::COL_CATEGORY,
                    __('Category "%1" has not been created.', $error['category'])
                    . ' ' . $error['exception']->getMessage()
                );
            }
        } else {
            $product = $this->retrieveProductBySku($rowData['sku']);
            if ($product) {
                $categoryIds = $product->getCategoryIds();
            }
        }
        return $categoryIds;
    }

    /**
     * Get product websites.
     *
     * @param string $productSku
     * @return array
     */
    public function getProductWebsites($productSku)
    {
        return array_keys($this->websitesCache[$productSku]);
    }

    /**
     * Retrieve product categories.
     *
     * @param string $productSku
     * @return array
     */
    public function getProductCategories($productSku)
    {
        return array_keys($this->categoriesCache[$productSku]);
    }

    /**
     * Get store id by code.
     *
     * @param string $storeCode
     * @return array|int|null|string
     */
    public function getStoreIdByCode($storeCode)
    {
        if (empty($storeCode)) {
            return self::SCOPE_DEFAULT;
        }
        return $this->storeResolver->getStoreCodeToId($storeCode);
    }

    /**
     * Save product tier prices.
     *
     * @param array $tierPriceData
     * @return $this
     */
    protected function _saveProductTierPrices(array $tierPriceData)
    {
        static $tableName = null;

        if (!$tableName) {
            $tableName = $this->_resourceFactory->create()->getTable('catalog_product_entity_tier_price');
        }
        if ($tierPriceData) {
            $tierPriceIn = [];
            $delProductId = [];

            foreach ($tierPriceData as $delSku => $tierPriceRows) {
                $productId = $this->skuProcessor->getNewSku($delSku)[$this->getProductEntityLinkField()];
                $delProductId[] = $productId;

                foreach ($tierPriceRows as $row) {
                    $row[$this->getProductEntityLinkField()] = $productId;
                    $tierPriceIn[] = $row;
                }
            }
            if (Import::BEHAVIOR_APPEND != $this->getBehavior()) {
                $this->_connection->delete(
                    $tableName,
                    $this->_connection->quoteInto("{$this->getProductEntityLinkField()} IN (?)", $delProductId)
                );
            }
            if ($tierPriceIn) {
                $this->_connection->insertOnDuplicate($tableName, $tierPriceIn, ['value']);
            }
        }
        return $this;
    }

    /**
     * Returns an object for upload a media files
     *
     * @return \Magento\CatalogImportExport\Model\Import\Uploader
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploader()
    {
        if ($this->_fileUploader === null) {
            $this->_fileUploader = $this->_uploaderFactory->create();

            $this->_fileUploader->init();

            $dirConfig = DirectoryList::getDefaultConfig();
            $dirAddon = $dirConfig[DirectoryList::MEDIA][DirectoryList::PATH];

            if (!empty($this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR])) {
                $tmpPath = $this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR];
            } else {
                $tmpPath = $dirAddon . '/' . $this->_mediaDirectory->getRelativePath('import');
            }

            if (!$this->_fileUploader->setTmpDir($tmpPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not readable.', $tmpPath)
                );
            }
            $destinationDir = "catalog/product";
            $destinationPath = $dirAddon . '/' . $this->_mediaDirectory->getRelativePath($destinationDir);

            $this->_mediaDirectory->create($destinationPath);
            if (!$this->_fileUploader->setDestDir($destinationPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not writable.', $destinationPath)
                );
            }
        }
        return $this->_fileUploader;
    }

    /**
     * Retrieve uploader.
     *
     * @return Uploader
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUploader()
    {
        return $this->_getUploader();
    }

    /**
     * Uploading files into the "catalog/product" media folder.
     *
     * Return a new file name if the same file is already exists.
     *
     * @param string $fileName
     * @param bool $renameFileOff [optional] boolean to pass.
     * Default is false which will set not to rename the file after import.
     * @return string
     */
    protected function uploadMediaFiles($fileName, $renameFileOff = false)
    {
        try {
            $res = $this->_getUploader()->move($fileName, $renameFileOff);
            return $res['file'];
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return '';
        }
    }

    /**
     * Try to find file by it's path.
     *
     * @param string $fileName
     * @return string
     */
    private function getSystemFile($fileName)
    {
        $filePath = 'catalog' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR . $fileName;
        /** @var \Magento\Framework\Filesystem\Directory\ReadInterface $read */
        $read = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        return $read->isExist($filePath) && $read->isReadable($filePath) ? $fileName : '';
    }

    /**
     * Save product media gallery.
     *
     * @param array $mediaGalleryData
     * @return $this
     */
    protected function _saveMediaGallery(array $mediaGalleryData)
    {
        if (empty($mediaGalleryData)) {
            return $this;
        }
        $this->mediaProcessor->saveMediaGallery($mediaGalleryData);

        return $this;
    }

    /**
     * Save product websites.
     *
     * @param array $websiteData
     * @return $this
     */
    protected function _saveProductWebsites(array $websiteData)
    {
        static $tableName = null;

        if (!$tableName) {
            $tableName = $this->_resourceFactory->create()->getProductWebsiteTable();
        }
        if ($websiteData) {
            $websitesData = [];
            $delProductId = [];

            foreach ($websiteData as $delSku => $websites) {
                try {
                    $productId = $this->skuProcessor->getNewSku($delSku)['entity_id'];
                    $delProductId[] = $productId;

                    if($this->checkSequenceProduct($productId)) {
                        foreach (array_keys($websites) as $websiteId) {
                            $websitesData[] = ['product_id' => $productId, 'website_id' => $websiteId];
                        }
                    }
                } catch (\Exception $e) {
                    $this->logger->info(__FUNCTION__ . ' $delSku: ' . $delSku . '. $productId: ' . $productId . '. error: ' . $e->getMessage());
                    continue;
                }
            }
            if (Import::BEHAVIOR_APPEND != $this->getBehavior()) {
                $this->_connection->delete(
                    $tableName,
                    $this->_connection->quoteInto('product_id IN (?)', $delProductId)
                );
            }
            if ($websitesData) {
                try {
                    $this->_connection->insertOnDuplicate($tableName, $websitesData);
                } catch (\Exception $e) {
                    $this->logger->info(__FUNCTION__ . ' $websitesData: ' . print_r($websitesData, true) . '. error: ' . $e->getMessage());
                }
            }
        }
        return $this;
    }

    /**
     * check squence product id
     *
     * @param int $value
     * @return bool
     */
    protected function checkSequenceProduct($value)
    {
        $connection = $this->getConnection();
        $table = $connection->getTableName('sequence_product');

        $check = $connection->select()
            ->from(
                $table,
                ['sequence_value']
            )->where('sequence_value = ?', $value);

        $data = $connection->fetchRow($check);

        if($data) {
            return true;
        }
        
        return false;
    }

    /**
     * Stock item saving.
     *
     * @return $this
     */
    protected function _saveStockItem()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $stockData = [];
            $productIdsToReindex = [];
            // Format bunch to stock data rows
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->isRowAllowedToImport($rowData, $rowNum)) {
                    continue;
                }

                $row = [];
                $sku = $rowData[self::COL_SKU];
                if ($this->skuProcessor->getNewSku($sku) !== null) {
                    $row = $this->formatStockDataForRow($rowData);
                    $productIdsToReindex[] = $row['product_id'];
                }

                if (!isset($stockData[$sku])) {
                    $stockData[$sku] = $row;
                }
            }

            // Insert rows
            if (!empty($stockData)) {
                $this->stockItemImporter->import($stockData);
            }

            $this->reindexProducts($productIdsToReindex);
        }
        return $this;
    }

    /**
     * Initiate product reindex by product ids
     *
     * @param array $productIdsToReindex
     * @return void
     */
    private function reindexProducts($productIdsToReindex = [])
    {
        $indexer = $this->indexerRegistry->get('catalog_product_category');
        if (is_array($productIdsToReindex) && count($productIdsToReindex) > 0 && !$indexer->isScheduled()) {
            $indexer->reindexList($productIdsToReindex);
        }
    }

    /**
     * Retrieve attribute by code
     *
     * @param string $attrCode
     * @return mixed
     */
    public function retrieveAttributeByCode($attrCode)
    {
        /** @var string $attrCode */
        $attrCode = mb_strtolower($attrCode);

        if (!isset($this->_attributeCache[$attrCode])) {
            $this->_attributeCache[$attrCode] = $this->getResource()->getAttribute($attrCode);
        }

        return $this->_attributeCache[$attrCode];
    }

    /**
     * Attribute set ID-to-name pairs getter.
     *
     * @return array
     */
    public function getAttrSetIdToName()
    {
        return $this->_attrSetIdToName;
    }

    /**
     * DB connection getter.
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * EAV entity type code getter.
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'catalog_product';
    }

    /**
     * New products SKU data.
     *
     * Returns array of new products data with SKU as key. All SKU keys are in lowercase for avoiding creation of
     * new products with the same SKU in different letter cases.
     *
     * @param string $sku
     * @return array
     */
    public function getNewSku($sku = null)
    {
        return $this->skuProcessor->getNewSku($sku);
    }

    /**
     * Get next bunch of validated rows.
     *
     * @return array|null
     */
    public function getNextBunch()
    {
        return $this->_dataSourceModel->getNextBunch();
    }

    /**
     * Existing products SKU getter.
     *
     * Returns array of existing products data with SKU as key. All SKU keys are in lowercase for avoiding creation of
     * new products with the same SKU in different letter cases.
     *
     * @return array
     */
    public function getOldSku()
    {
        return $this->_oldSku;
    }

    /**
     * Retrieve Category Processor
     *
     * @return \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor
     */
    public function getCategoryProcessor()
    {
        return $this->categoryProcessor;
    }

    /**
     * Obtain scope of the row from row data.
     *
     * @param array $rowData
     * @return int
     */
    public function getRowScope($rowData = [])
    {
        return self::SCOPE_DEFAULT;
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Validate_Exception
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            // check that row is already validated
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;

        $rowScope = $this->getRowScope($rowData);
        $sku = $rowData[self::COL_SKU];

        // BEHAVIOR_DELETE and BEHAVIOR_REPLACE use specific validation logic
        if (Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            if (self::SCOPE_DEFAULT == $rowScope && !$this->isSkuExist($sku)) {
                $this->skipRow($rowNum, ValidatorInterface::ERROR_SKU_NOT_FOUND_FOR_DELETE);
                return false;
            }
        }
        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (self::SCOPE_DEFAULT == $rowScope && !$this->isSkuExist($sku)) {
                $this->skipRow($rowNum, ValidatorInterface::ERROR_SKU_NOT_FOUND_FOR_DELETE);
                return false;
            }
            return true;
        }

        // if product doesn't exist, need to throw critical error else all errors should be not critical.
        $errorLevel = $this->getValidationErrorLevel($sku);

        if (!$this->validator->isValid($rowData)) {
            foreach ($this->validator->getMessages() as $message) {
                $this->skipRow($rowNum, $message, $errorLevel, $this->validator->getInvalidAttribute());
            }
        }

        if (null === $sku) {
            $this->skipRow($rowNum, ValidatorInterface::ERROR_SKU_IS_EMPTY, $errorLevel);
        } elseif (false === $sku) {
            $this->skipRow($rowNum, ValidatorInterface::ERROR_ROW_IS_ORPHAN, $errorLevel);
        } elseif (self::SCOPE_STORE == $rowScope
            && !$this->storeResolver->getStoreCodeToId($rowData[self::COL_STORE])
        ) {
            $this->skipRow($rowNum, ValidatorInterface::ERROR_INVALID_STORE, $errorLevel);
        }

        // SKU is specified, row is SCOPE_DEFAULT, new product block begins
        $this->_processedEntitiesCount++;

        if ($this->isSkuExist($sku) && Import::BEHAVIOR_REPLACE !== $this->getBehavior()) {
            // can we get all necessary data from existent DB product?
            // check for supported type of existing product
            if (isset($this->_productTypeModels[$this->getExistingSku($sku)['type_id']])) {
                $this->skuProcessor->addNewSku(
                    $sku,
                    $this->prepareNewSkuData($sku)
                );
            } else {
                $this->skipRow($rowNum, ValidatorInterface::ERROR_TYPE_UNSUPPORTED, $errorLevel);
            }
        } else {
            // validate new product type and attribute set
            if (!isset($rowData[self::COL_TYPE], $this->_productTypeModels[$rowData[self::COL_TYPE]])) {
                $this->skipRow($rowNum, ValidatorInterface::ERROR_INVALID_TYPE, $errorLevel);
            } elseif (!isset($rowData[self::COL_ATTR_SET], $this->_attrSetNameToId[$rowData[self::COL_ATTR_SET]])
            ) {
                $this->skipRow($rowNum, ValidatorInterface::ERROR_INVALID_ATTR_SET, $errorLevel);
            } elseif ($this->skuProcessor->getNewSku($sku) === null) {
                $this->skuProcessor->addNewSku(
                    $sku,
                    [
                        'row_id' => null,
                        'entity_id' => null,
                        'type_id' => $rowData[self::COL_TYPE],
                        'attr_set_id' => $this->_attrSetNameToId[$rowData[self::COL_ATTR_SET]],
                        'attr_set_code' => $rowData[self::COL_ATTR_SET],
                    ]
                );
            }
        }

        if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
            $newSku = $this->skuProcessor->getNewSku($sku);
            // set attribute set code into row data for followed attribute validation in type model
            $rowData[self::COL_ATTR_SET] = $newSku['attr_set_code'];

            /** @var \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType $productTypeValidator */
            // isRowValid can add error to general errors pull if row is invalid
            $productTypeValidator = $this->_productTypeModels[$newSku['type_id']];
            $productTypeValidator->isRowValid(
                $rowData,
                $rowNum,
                !($this->isSkuExist($sku) && Import::BEHAVIOR_REPLACE !== $this->getBehavior())
            );
        }
        // validate custom options
        $this->getOptionEntity()->validateRow($rowData, $rowNum);

        if ($this->isNeedToValidateUrlKey($rowData)) {
            $urlKey = strtolower($this->getUrlKey($rowData));
            $storeCodes = empty($rowData[self::COL_STORE_VIEW_CODE])
                ? array_flip($this->storeResolver->getStoreCodeToId())
                : explode($this->getMultipleValueSeparator(), $rowData[self::COL_STORE_VIEW_CODE]);
            foreach ($storeCodes as $storeCode) {
                $storeId = $this->storeResolver->getStoreCodeToId($storeCode);
                $productUrlSuffix = $this->getProductUrlSuffix($storeId);
                $urlPath = $urlKey . $productUrlSuffix;
                if (empty($this->urlKeys[$storeId][$urlPath])
                    || ($this->urlKeys[$storeId][$urlPath] == $sku)
                ) {
                    $this->urlKeys[$storeId][$urlPath] = $sku;
                    $this->rowNumbers[$storeId][$urlPath] = $rowNum;
                } else {
                    $message = sprintf(
                        $this->retrieveMessageTemplate(ValidatorInterface::ERROR_DUPLICATE_URL_KEY),
                        $urlKey,
                        $this->urlKeys[$storeId][$urlPath]
                    );
                    $this->addRowError(
                        ValidatorInterface::ERROR_DUPLICATE_URL_KEY,
                        $rowNum,
                        $rowData[self::COL_NAME],
                        $message,
                        $errorLevel
                    )
                        ->getErrorAggregator()
                        ->addRowToSkip($rowNum);
                }
            }
        }

        if (!empty($rowData['new_from_date']) && !empty($rowData['new_to_date'])
        ) {
            $newFromTimestamp = strtotime($this->dateTime->formatDate($rowData['new_from_date'], false));
            $newToTimestamp = strtotime($this->dateTime->formatDate($rowData['new_to_date'], false));
            if ($newFromTimestamp > $newToTimestamp) {
                $this->skipRow(
                    $rowNum,
                    'invalidNewToDateValue',
                    $errorLevel,
                    $rowData['new_to_date']
                );
            }
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Check if need to validate url key.
     *
     * @param array $rowData
     * @return bool
     */
    private function isNeedToValidateUrlKey($rowData)
    {
        if (!empty($rowData[self::COL_SKU]) && empty($rowData[self::URL_KEY])
            && $this->getBehavior() === Import::BEHAVIOR_APPEND
            && $this->isSkuExist($rowData[self::COL_SKU])) {
            return false;
        }

        return (!empty($rowData[self::URL_KEY]) || !empty($rowData[self::COL_NAME]))
            && (empty($rowData[self::COL_VISIBILITY])
                || $rowData[self::COL_VISIBILITY]
                !== (string)Visibility::getOptionArray()[Visibility::VISIBILITY_NOT_VISIBLE]);
    }

    /**
     * Prepare new SKU data
     *
     * @param string $sku
     * @return array
     */
    private function prepareNewSkuData($sku)
    {
        $data = [];
        foreach ($this->getExistingSku($sku) as $key => $value) {
            $data[$key] = $value;
        }

        $data['attr_set_code'] = $this->_attrSetIdToName[$this->getExistingSku($sku)['attr_set_id']];

        return $data;
    }

    /**
     * Parse attributes names and values string to array.
     *
     * @param array $rowData
     *
     * @return array
     */
    private function _parseAdditionalAttributes($rowData)
    {
        if (empty($rowData['additional_attributes'])) {
            return $rowData;
        }
        $rowData = array_merge($rowData, $this->getAdditionalAttributes($rowData['additional_attributes']));
        return $rowData;
    }

    /**
     * Retrieves additional attributes in format:
     * [
     *      code1 => value1,
     *      code2 => value2,
     *      ...
     *      codeN => valueN
     * ]
     *
     * @param string $additionalAttributes Attributes data that will be parsed
     * @return array
     */
    private function getAdditionalAttributes($additionalAttributes)
    {
        return empty($this->_parameters[Import::FIELDS_ENCLOSURE])
            ? $this->parseAttributesWithoutWrappedValues($additionalAttributes)
            : $this->parseAttributesWithWrappedValues($additionalAttributes);
    }

    /**
     * Parses data and returns attributes in format:
     * [
     *      code1 => value1,
     *      code2 => value2,
     *      ...
     *      codeN => valueN
     * ]
     *
     * @param string $attributesData Attributes data that will be parsed. It keeps data in format:
     *      code=value,code2=value2...,codeN=valueN
     * @return array
     */
    private function parseAttributesWithoutWrappedValues($attributesData)
    {
        $attributeNameValuePairs = explode($this->getMultipleValueSeparator(), $attributesData);
        $preparedAttributes = [];
        $code = '';
        foreach ($attributeNameValuePairs as $attributeData) {
            //process case when attribute has ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR inside its value
            if (strpos($attributeData, self::PAIR_NAME_VALUE_SEPARATOR) === false) {
                if (!$code) {
                    continue;
                }
                $preparedAttributes[$code] .= $this->getMultipleValueSeparator() . $attributeData;
                continue;
            }
            list($code, $value) = explode(self::PAIR_NAME_VALUE_SEPARATOR, $attributeData, 2);
            $code = mb_strtolower($code);
            $preparedAttributes[$code] = $value;
        }
        return $preparedAttributes;
    }

    /**
     * Parses data and returns attributes in format:
     * [
     *      code1 => value1,
     *      code2 => value2,
     *      ...
     *      codeN => valueN
     * ]
     * All values have unescaped data except mupliselect attributes,
     * they should be parsed in additional method - parseMultiselectValues()
     *
     * @param string $attributesData Attributes data that will be parsed. It keeps data in format:
     *      code="value",code2="value2"...,codeN="valueN"
     *  where every value is wrapped in double quotes. Double quotes as part of value should be duplicated.
     *  E.g. attribute with code 'attr_code' has value 'my"value'. This data should be stored as attr_code="my""value"
     *
     * @return array
     */
    private function parseAttributesWithWrappedValues($attributesData)
    {
        $attributes = [];
        preg_match_all(
            '~((?:[a-zA-Z0-9_])+)="((?:[^"]|""|"' . $this->getMultiLineSeparatorForRegexp() . '")+)"+~',
            $attributesData,
            $matches
        );
        foreach ($matches[1] as $i => $attributeCode) {
            $attribute = $this->retrieveAttributeByCode($attributeCode);
            $value = 'multiselect' != $attribute->getFrontendInput()
                ? str_replace('""', '"', $matches[2][$i])
                : '"' . $matches[2][$i] . '"';
            $attributes[mb_strtolower($attributeCode)] = $value;
        }
        return $attributes;
    }

    /**
     * Parse values of multiselect attributes depends on "Fields Enclosure" parameter
     *
     * @param string $values
     * @param string $delimiter
     * @return array
     * @since 100.1.2
     */
    public function parseMultiselectValues($values, $delimiter = self::PSEUDO_MULTI_LINE_SEPARATOR)
    {
        if (empty($this->_parameters[Import::FIELDS_ENCLOSURE])) {
            return explode($delimiter, $values);
        }
        if (preg_match_all('~"((?:[^"]|"")*)"~', $values, $matches)) {
            return $values = array_map(
                function ($value) {
                    return str_replace('""', '"', $value);
                },
                $matches[1]
            );
        }
        return [$values];
    }

    /**
     * Retrieves escaped PSEUDO_MULTI_LINE_SEPARATOR if it is metacharacter for regular expression
     *
     * @return string
     */
    private function getMultiLineSeparatorForRegexp()
    {
        if (!$this->multiLineSeparatorForRegexp) {
            $this->multiLineSeparatorForRegexp = in_array(self::PSEUDO_MULTI_LINE_SEPARATOR, str_split('[\^$.|?*+(){}'))
                ? '\\' . self::PSEUDO_MULTI_LINE_SEPARATOR
                : self::PSEUDO_MULTI_LINE_SEPARATOR;
        }
        return $this->multiLineSeparatorForRegexp;
    }

    /**
     * Set values in use_config_ fields.
     *
     * @param array $rowData
     *
     * @return array
     */
    private function _setStockUseConfigFieldsValues($rowData)
    {
        $useConfigFields = [];
        foreach ($rowData as $key => $value) {
            $useConfigName = $key === StockItemInterface::ENABLE_QTY_INCREMENTS
                ? StockItemInterface::USE_CONFIG_ENABLE_QTY_INC
                : self::INVENTORY_USE_CONFIG_PREFIX . $key;

            if (isset($this->defaultStockData[$key])
                && isset($this->defaultStockData[$useConfigName])
                && !empty($value)
                && empty($rowData[$useConfigName])
            ) {
                $useConfigFields[$useConfigName] = ($value == self::INVENTORY_USE_CONFIG) ? 1 : 0;
            }
        }
        $rowData = array_merge($rowData, $useConfigFields);
        return $rowData;
    }

    /**
     * Custom fields mapping for changed purposes of fields and field names.
     *
     * @param array $rowData
     *
     * @return array
     */
    private function _customFieldsMapping($rowData)
    {
        foreach ($this->_fieldsMap as $systemFieldName => $fileFieldName) {
            if (array_key_exists($fileFieldName, $rowData)) {
                $rowData[$systemFieldName] = $rowData[$fileFieldName];
            }
        }

        $rowData = $this->_parseAdditionalAttributes($rowData);

        $rowData = $this->_setStockUseConfigFieldsValues($rowData);
        if (array_key_exists('status', $rowData)
            && $rowData['status'] != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        ) {
            if ($rowData['status'] == 'yes') {
                $rowData['status'] = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
            } elseif (!empty($rowData['status']) || $this->getRowScope($rowData) == self::SCOPE_DEFAULT) {
                $rowData['status'] = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
            }
        }
        return $rowData;
    }

    /**
     * Validate data rows and save bunches to DB
     *
     * @return $this|AbstractEntity
     */
    protected function _saveValidatedBunches()
    {
        $source = $this->_getSource();
        $source->rewind();

        while ($source->valid()) {
            try {
                $rowData = $source->current();
            } catch (\InvalidArgumentException $e) {
                $source->next();
                continue;
            }

            $rowData = $this->_customFieldsMapping($rowData);

            $this->validateRow($rowData, $source->key());

            $source->next();
        }
        $this->checkUrlKeyDuplicates();
        $this->getOptionEntity()->validateAmbiguousData();
        return parent::_saveValidatedBunches();
    }

    /**
     * Check that url_keys are not assigned to other products in DB
     *
     * @return void
     * @since 100.0.3
     */
    protected function checkUrlKeyDuplicates()
    {
        $resource = $this->getResource();
        foreach ($this->urlKeys as $storeId => $urlKeys) {
            $urlKeyDuplicates = $this->_connection->fetchAssoc(
                $this->_connection->select()->from(
                    ['url_rewrite' => $resource->getTable('url_rewrite')],
                    ['request_path', 'store_id']
                )->joinLeft(
                    ['cpe' => $resource->getTable('catalog_product_entity')],
                    "cpe.entity_id = url_rewrite.entity_id"
                )->where('request_path IN (?)', array_keys($urlKeys))
                    ->where('store_id IN (?)', $storeId)
                    ->where('cpe.sku not in (?)', array_values($urlKeys))
            );
            foreach ($urlKeyDuplicates as $entityData) {
                $rowNum = $this->rowNumbers[$entityData['store_id']][$entityData['request_path']];
                $message = sprintf(
                    $this->retrieveMessageTemplate(ValidatorInterface::ERROR_DUPLICATE_URL_KEY),
                    $entityData['request_path'],
                    $entityData['sku']
                );
                $this->addRowError(ValidatorInterface::ERROR_DUPLICATE_URL_KEY, $rowNum, 'url_key', $message);
            }
        }
    }

    /**
     * Retrieve product rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     * @since 100.0.3
     */
    protected function getProductUrlSuffix($storeId = null)
    {
        if (!isset($this->productUrlSuffix[$storeId])) {
            $this->productUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->productUrlSuffix[$storeId];
    }

    /**
     * Retrieve url key from provided row data.
     *
     * @param array $rowData
     * @return string
     *
     * @since 100.0.3
     */
    protected function getUrlKey($rowData)
    {
        if (!empty($rowData[self::URL_KEY])) {
            $urlKey = (string) $rowData[self::URL_KEY];
            return trim(strtolower($urlKey));
        }

        if (!empty($rowData[self::COL_NAME])
            && (array_key_exists(self::URL_KEY, $rowData) || !$this->isSkuExist($rowData[self::COL_SKU]))) {
            return $this->productUrl->formatUrlKey($rowData[self::COL_NAME]);
        }

        return '';
    }

    /**
     * Retrieve resource.
     *
     * @return Proxy\Product\ResourceModel
     *
     * @since 100.0.3
     */
    protected function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = $this->_resourceFactory->create();
        }
        return $this->_resource;
    }

    /**
     * Whether a url key is needed to be change.
     *
     * @param array $rowData
     * @return bool
     */
    private function isNeedToChangeUrlKey(array $rowData): bool
    {
        $urlKey = $this->getUrlKey($rowData);
        $productExists = $this->isSkuExist($rowData[self::COL_SKU]);
        $markedToEraseUrlKey = isset($rowData[self::URL_KEY]);
        // The product isn't new and the url key index wasn't marked for change.
        if (!$urlKey && $productExists && !$markedToEraseUrlKey) {
            // Seems there is no need to change the url key
            return false;
        }

        return true;
    }

    /**
     * Get product entity link field
     *
     * @return string
     */
    private function getProductEntityLinkField()
    {
        if (!$this->productEntityLinkField) {
            $this->productEntityLinkField = $this->getMetadataPool()
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getLinkField();
        }
        return $this->productEntityLinkField;
    }

    /**
     * Get product entity identifier field
     *
     * @return string
     */
    private function getProductIdentifierField()
    {
        if (!$this->productEntityIdentifierField) {
            $this->productEntityIdentifierField = $this->getMetadataPool()
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getIdentifierField();
        }
        return $this->productEntityIdentifierField;
    }

    /**
     * Update media gallery labels
     *
     * @param array $labels
     * @return void
     */
    private function updateMediaGalleryLabels(array $labels)
    {
        if (!empty($labels)) {
            $this->mediaProcessor->updateMediaGalleryLabels($labels);
        }
    }

    /**
     * Update 'disabled' field for media gallery entity
     *
     * @param array $images
     * @return $this
     */
    private function updateMediaGalleryVisibility(array $images)
    {
        if (!empty($images)) {
            $this->mediaProcessor->updateMediaGalleryVisibility($images);
        }

        return $this;
    }

    /**
     * Parse values from multiple attributes fields
     *
     * @param string $labelRow
     * @return array
     */
    private function parseMultipleValues($labelRow)
    {
        return $this->parseMultiselectValues(
            $labelRow,
            $this->getMultipleValueSeparator()
        );
    }

    /**
     * Check if product exists for specified SKU
     *
     * @param string $sku
     * @return bool
     */
    private function isSkuExist($sku)
    {
        $sku = strtolower($sku);
        return isset($this->_oldSku[$sku]);
    }

    /**
     * Get existing product data for specified SKU
     *
     * @param string $sku
     * @return array
     */
    private function getExistingSku($sku)
    {
        return $this->_oldSku[strtolower($sku)];
    }

    /**
     * Format row data to DB compatible values.
     *
     * @param array $rowData
     * @return array
     */
    private function formatStockDataForRow(array $rowData): array
    {
        $sku = $rowData[self::COL_SKU];
        $row['product_id'] = $this->skuProcessor->getNewSku($sku)['entity_id'];
        $row['website_id'] = $this->stockConfiguration->getDefaultScopeId();
        $row['stock_id'] = $this->stockRegistry->getStock($row['website_id'])->getStockId();

        $stockItemDo = $this->stockRegistry->getStockItem($row['product_id'], $row['website_id']);
        $existStockData = $stockItemDo->getData();

        if (isset($rowData['qty']) && $rowData['qty'] == 0 && !isset($rowData['is_in_stock'])) {
            $rowData['is_in_stock'] = 0;
        }

        $row = array_merge(
            $this->defaultStockData,
            array_intersect_key($existStockData, $this->defaultStockData),
            array_intersect_key($rowData, $this->defaultStockData),
            $row
        );

        if ($this->stockConfiguration->isQty($this->skuProcessor->getNewSku($sku)['type_id'])) {
            $stockItemDo->setData($row);
            $row['is_in_stock'] = $row['is_in_stock'] ?? $this->stockStateProvider->verifyStock($stockItemDo);
            if ($this->stockStateProvider->verifyNotification($stockItemDo)) {
                $date = $this->dateTimeFactory->create('now', new \DateTimeZone('UTC'));
                $row['low_stock_date'] = $date->format(DateTime::DATETIME_PHP_FORMAT);
            }
            $row['stock_status_changed_auto'] = (int)!$this->stockStateProvider->verifyStock($stockItemDo);
        } else {
            $row['qty'] = 0;
        }

        return $row;
    }

    /**
     * Retrieve product by sku.
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function retrieveProductBySku($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $product;
    }

    /**
     * Add row as skipped
     *
     * @param int $rowNum
     * @param string $errorCode Error code or simply column name
     * @param string $errorLevel error level
     * @param string|null $colName optional column name
     * @return $this
     */
    private function skipRow(
        $rowNum,
        string $errorCode,
        string $errorLevel = ProcessingError::ERROR_LEVEL_NOT_CRITICAL,
        $colName = null
    ): self {
        $this->addRowError($errorCode, $rowNum, $colName, null, $errorLevel);
        $this->getErrorAggregator()
            ->addRowToSkip($rowNum);
        return $this;
    }

    /**
     * Returns errorLevel for validation
     *
     * @param string $sku
     * @return string
     */
    private function getValidationErrorLevel($sku): string
    {
        return (!$this->isSkuExist($sku) && Import::BEHAVIOR_REPLACE !== $this->getBehavior())
            ? ProcessingError::ERROR_LEVEL_CRITICAL
            : ProcessingError::ERROR_LEVEL_NOT_CRITICAL;
    }

    /**
     * Processes link bunches
     *
     * @param array $bunch
     * @param Link $resource
     * @param int $nextLinkId
     * @param array $positionAttrId
     * @return void
     */
    private function processLinkBunches(
        array $bunch,
        Link $resource,
        int $nextLinkId,
        array $positionAttrId
    ): void {
        $productIds = [];
        $linkRows = [];
        $positionRows = [];

        $bunch = array_filter($bunch, [$this, 'isRowAllowedToImport'], ARRAY_FILTER_USE_BOTH);
        foreach ($bunch as $rowData) {
            $sku = $rowData[self::COL_SKU];
            $productId = $this->skuProcessor->getNewSku($sku)[$this->getProductEntityLinkField()];
            $productIds[] = $productId;
            $productLinkKeys = $this->fetchProductLinks($resource, $productId);
            $linkNameToId = array_filter(
                $this->_linkNameToId,
                function ($linkName) use ($rowData) {
                    return isset($rowData[$linkName . 'sku']);
                },
                ARRAY_FILTER_USE_KEY
            );
            foreach ($linkNameToId as $linkName => $linkId) {
                $linkSkus = explode($this->getMultipleValueSeparator(), $rowData[$linkName . 'sku']);
                $linkPositions = !empty($rowData[$linkName . 'position'])
                    ? explode($this->getMultipleValueSeparator(), $rowData[$linkName . 'position'])
                    : [];

                $linkSkus = array_filter(
                    $linkSkus,
                    function ($linkedSku) use ($sku) {
                        $linkedSku = trim($linkedSku);
                        return ($this->skuProcessor->getNewSku($linkedSku) !== null || $this->isSkuExist($linkedSku))
                            && strcasecmp($linkedSku, $sku) !== 0;
                    }
                );
                foreach ($linkSkus as $linkedKey => $linkedSku) {
                    $linkedId = $this->getProductLinkedId($linkedSku);
                    if ($linkedId == null) {
                        // Import file links to a SKU which is skipped for some reason, which leads to a "NULL"
                        // link causing fatal errors.
                        $formatStr = 'WARNING: Orphaned link skipped: From SKU %s (ID %d) to SKU %s, Link type id: %d';
                        $exception = new \Exception(sprintf($formatStr, $sku, $productId, $linkedSku, $linkId));
                        $this->_logger->critical($exception);
                        continue;
                    }
                    $linkKey = $this->composeLinkKey($productId, $linkedId, $linkId);
                    $productLinkKeys[$linkKey] = $productLinkKeys[$linkKey] ?? $nextLinkId;

                    $linkRows[$linkKey] = $linkRows[$linkKey] ?? [
                            'link_id' => $productLinkKeys[$linkKey],
                            'product_id' => $productId,
                            'linked_product_id' => $linkedId,
                            'link_type_id' => $linkId,
                        ];

                    if (!empty($linkPositions[$linkedKey])) {
                        $positionRows[] = [
                            'link_id' => $productLinkKeys[$linkKey],
                            'product_link_attribute_id' => $positionAttrId[$linkId],
                            'value' => $linkPositions[$linkedKey],
                        ];
                    }
                    $nextLinkId++;
                }
            }
        }
        $this->saveLinksData($resource, $productIds, $linkRows, $positionRows);
    }

    /**
     * Fetches Product Links
     *
     * @param Link $resource
     * @param int $productId
     * @return array
     */
    private function fetchProductLinks(Link $resource, int $productId) : array
    {
        $productLinkKeys = [];
        $select = $this->_connection->select()->from(
            $resource->getTable('catalog_product_link'),
            ['id' => 'link_id', 'linked_id' => 'linked_product_id', 'link_type_id' => 'link_type_id']
        )->where(
            'product_id = :product_id'
        );
        $bind = [':product_id' => $productId];
        foreach ($this->_connection->fetchAll($select, $bind) as $linkData) {
            $linkKey = $this->composeLinkKey($productId, $linkData['linked_id'], $linkData['link_type_id']);
            $productLinkKeys[$linkKey] = $linkData['id'];
        }

        return $productLinkKeys;
    }

    /**
     * Gets the Id of the Sku
     *
     * @param string $linkedSku
     * @return int|null
     */
    private function getProductLinkedId(string $linkedSku) : ?int
    {
        $linkedSku = trim($linkedSku);
        $newSku = $this->skuProcessor->getNewSku($linkedSku);
        $linkedId = !empty($newSku) ? $newSku['entity_id'] : $this->getExistingSku($linkedSku)['entity_id'];
        return $linkedId;
    }

    /**
     * Saves information about product links
     *
     * @param Link $resource
     * @param array $productIds
     * @param array $linkRows
     * @param array $positionRows
     * @throws LocalizedException
     */
    private function saveLinksData(Link $resource, array $productIds, array $linkRows, array $positionRows)
    {
        $mainTable = $resource->getMainTable();
        if (Import::BEHAVIOR_APPEND != $this->getBehavior() && $productIds) {
            $this->_connection->delete(
                $mainTable,
                $this->_connection->quoteInto('product_id IN (?)', array_unique($productIds))
            );
        }
        if ($linkRows) {
            $this->_connection->insertOnDuplicate($mainTable, $linkRows, ['link_id']);
        }
        if ($positionRows) {
            // process linked product positions
            $this->_connection->insertOnDuplicate(
                $resource->getAttributeTypeTable('int'),
                $positionRows,
                ['value']
            );
        }
    }

    /**
     * Composes the link key
     *
     * @param int $productId
     * @param int $linkedId
     * @param int $linkTypeId
     * @return string
     */
    private function composeLinkKey(int $productId, int $linkedId, int $linkTypeId) : string
    {
        return "{$productId}-{$linkedId}-{$linkTypeId}";
    }
}
