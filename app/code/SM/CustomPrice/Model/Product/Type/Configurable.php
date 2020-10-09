<?php


namespace SM\CustomPrice\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Model\Product\Type\Collection\SalableProcessor;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;

class Configurable extends \Magento\ConfigurableProduct\Model\Product\Type\Configurable
{
    /**
     * @var SearchCriteriaBuilder|null
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ProductAttributeRepositoryInterface|null
     */
    protected $productAttributeRepository;

    /**
     * @var Config|null
     */
    protected $catalogConfig;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Collection salable processor
     *
     * @var SalableProcessor
     */
    private $salableProcessor;

    /**
     * @var int|null
     */
    private $promoPrice;
    /**
     * @var int|null
     */
    private $basePrice;

    /**
     * Configurable constructor.
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttributeFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param Session $session
     * @param \Magento\Framework\Cache\FrontendInterface|null $cache
     * @param \Magento\Customer\Model\Session|null $customerSession
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param ProductInterfaceFactory|null $productFactory
     * @param SalableProcessor $salableProcessor
     * @param ProductAttributeRepositoryInterface|null $productAttributeRepository
     * @param SearchCriteriaBuilder|null $searchCriteriaBuilder
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttributeFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        Session $session,
        \Magento\Framework\Cache\FrontendInterface $cache = null,
        \Magento\Customer\Model\Session $customerSession = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        ProductInterfaceFactory $productFactory = null,
        SalableProcessor $salableProcessor,
        ProductAttributeRepositoryInterface $productAttributeRepository = null,
        SearchCriteriaBuilder $searchCriteriaBuilder = null
    ) {
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository,
            $typeConfigurableFactory,
            $eavAttributeFactory,
            $configurableAttributeFactory,
            $productCollectionFactory,
            $attributeCollectionFactory,
            $catalogProductTypeConfigurable,
            $scopeConfig,
            $extensionAttributesJoinProcessor,
            $cache,
            $customerSession,
            $serializer,
            $productFactory,
            $salableProcessor,
            $productAttributeRepository,
            $searchCriteriaBuilder
        );
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->customerSession = $session;
        $this->salableProcessor = $salableProcessor;

        if ($session->isLoggedIn()) {
            $this->promoPrice = $session->getOmniFinalPriceAttributeCode();
            $this->basePrice = $session->getOmniNormalPriceAttributeCode();
            $promoPrice = $eavConfig->getAttribute('catalog_product', $this->promoPrice);
            $basePrice = $eavConfig->getAttribute('catalog_product', $this->basePrice);
            if (!$promoPrice || !$promoPrice->getAttributeId()) {
                $this->promoPrice = null;
            }
            if (!$basePrice || !$basePrice->getAttributeId()) {
                $this->basePrice = null;
            }
        }
    }

    /**
     * Returns array of sub-products for specified configurable product
     * Result array contains all children for specified configurable product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $requiredAttributeIds Attributes to include in the select; one-dimensional array
     * @return ProductInterface[]
     */
    public function getUsedProducts($product, $requiredAttributeIds = null)
    {
        if (!$product->hasData($this->_usedProducts)) {
            $collection = $this->getConfiguredUsedProductCollection($product, false, $requiredAttributeIds);
            $usedProducts = array_values($collection->getItems());
            $product->setData($this->_usedProducts, $usedProducts);
        }

        return $product->getData($this->_usedProducts);
    }

    /**
     * Prepare collection for retrieving sub-products of specified configurable product
     * Retrieve related products collection with additional configuration
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $skipStockFilter
     * @param array $requiredAttributeIds Attributes to include in the select
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getConfiguredUsedProductCollection(
        \Magento\Catalog\Model\Product $product,
        $skipStockFilter = true,
        $requiredAttributeIds = null
    ) {
        $collection = $this->getUsedProductCollection($product);

        if ($skipStockFilter) {
            $collection->setFlag('has_stock_status_filter', true);
        }

        $attributesForSelect = $this->getAttributesForCollection($product);
        if ($requiredAttributeIds) {
            $this->searchCriteriaBuilder->addFilter('attribute_id', $requiredAttributeIds, 'in');
            $requiredAttributes = $this->productAttributeRepository
                ->getList($this->searchCriteriaBuilder->create())->getItems();
            $requiredAttributeCodes = [];
            foreach ($requiredAttributes as $requiredAttribute) {
                $requiredAttributeCodes[] = $requiredAttribute->getAttributeCode();
            }
            $attributesForSelect = array_unique(array_merge($attributesForSelect, $requiredAttributeCodes));
        }
        if (!empty($this->basePrice)) {
            $collection->addAttributeToSelect($this->basePrice);
        }
        if (!empty($this->promoPrice)) {
            $collection->addAttributeToSelect($this->promoPrice);
        }
        $collection
            ->addAttributeToSelect($attributesForSelect)
            ->addFilterByRequiredOptions()
            ->setStoreId($product->getStoreId());

        $collection->addMediaGalleryData();
        $collection->addTierPriceData();

        return $collection;
    }


    /**
     * Get Config instance
     * @return Config
     * @deprecated 100.1.0
     */
    protected function getCatalogConfig()
    {
        if (!$this->catalogConfig) {
            $this->catalogConfig = ObjectManager::getInstance()->get(Config::class);
        }
        return $this->catalogConfig;
    }

    /**
     * @return array
     */
    protected function getAttributesForCollection(\Magento\Catalog\Model\Product $product)
    {
        $productAttributes = $this->getCatalogConfig()->getProductAttributes();

        $requiredAttributes = [
            'name',
            'price',
            'weight',
            'image',
            'thumbnail',
            'status',
            'visibility',
            'media_gallery'
        ];

        $usedAttributes = array_map(
            function ($attr) {
                return $attr->getAttributeCode();
            },
            $this->getUsedProductAttributes($product)
        );

        return array_unique(array_merge($productAttributes, $requiredAttributes, $usedAttributes));
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool|mixed
     */
    public function isSalable($product)
    {
        $salable = $product->getStatus() == Status::STATUS_ENABLED;
        if ($salable && $product->hasData('is_salable')) {
            $salable = $product->getData('is_salable');
        }

        if ((bool)(int) $salable === false) {
            $collection = $this->getUsedProductCollection($product);
            $collection->addStoreFilter($this->getStoreFilter($product));
            $collection = $this->salableProcessor->process($collection);
            $salable = 0 !== $collection->getSize();
        }

        return $salable;
    }
}
