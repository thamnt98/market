<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: October, 08 2020
 * Time: 2:25 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Override\MagentoCatalog\Model;

class Product extends \Magento\Catalog\Model\Product
{
    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $helper;

    /**
     * Product constructor.
     *
     * @param \SM\Catalog\Helper\Data                                                   $helper
     * @param \Magento\Framework\Model\Context                                          $context
     * @param \Magento\Framework\Registry                                               $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory                         $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory                              $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface                                $storeManager
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface                  $metadataService
     * @param \Magento\Catalog\Model\Product\Url                                        $url
     * @param \Magento\Catalog\Model\Product\Link                                       $productLink
     * @param \Magento\Catalog\Model\Product\Configuration\Item\OptionFactory           $itemOptionFactory
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory              $stockItemFactory
     * @param \Magento\Catalog\Model\Product\OptionFactory                              $catalogProductOptionFactory
     * @param \Magento\Catalog\Model\Product\Visibility                                 $catalogProductVisibility
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status                    $catalogProductStatus
     * @param \Magento\Catalog\Model\Product\Media\Config                               $catalogProductMediaConfig
     * @param \Magento\Catalog\Model\Product\Type                                       $catalogProductType
     * @param \Magento\Framework\Module\Manager                                         $moduleManager
     * @param \Magento\Catalog\Helper\Product                                           $catalogProduct
     * @param \Magento\Catalog\Model\ResourceModel\Product                              $resource
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection                   $resourceCollection
     * @param \Magento\Framework\Data\CollectionFactory                                 $collectionFactory
     * @param \Magento\Framework\Filesystem                                             $filesystem
     * @param \Magento\Framework\Indexer\IndexerRegistry                                $indexerRegistry
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\Processor                     $productFlatIndexerProcessor
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor                    $productPriceIndexerProcessor
     * @param \Magento\Catalog\Model\Indexer\Product\Eav\Processor                      $productEavIndexerProcessor
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface                          $categoryRepository
     * @param \Magento\Catalog\Model\Product\Image\CacheFactory                         $imageCacheFactory
     * @param \Magento\Catalog\Model\ProductLink\CollectionProvider                     $entityCollectionProvider
     * @param \Magento\Catalog\Model\Product\LinkTypeProvider                           $linkTypeProvider
     * @param \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory                     $productLinkFactory
     * @param \Magento\Catalog\Api\Data\ProductLinkExtensionFactory                     $productLinkExtensionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Backend\Media\EntryConverterPool $mediaGalleryEntryConverterPool
     * @param \Magento\Framework\Api\DataObjectHelper                                   $dataObjectHelper
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface          $joinProcessor
     * @param array                                                                     $data
     * @param \Magento\Eav\Model\Config|null                                            $config
     * @param \Magento\Catalog\Model\FilterProductCustomAttribute|null                  $filterCustomAttribute
     */
    public function __construct(
        \SM\Catalog\Helper\Data $helper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataService,
        \Magento\Catalog\Model\Product\Url $url,
        \Magento\Catalog\Model\Product\Link $productLink,
        \Magento\Catalog\Model\Product\Configuration\Item\OptionFactory $itemOptionFactory,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory,
        \Magento\Catalog\Model\Product\OptionFactory $catalogProductOptionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $catalogProductStatus,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Catalog\Model\ResourceModel\Product $resource,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $resourceCollection,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\Catalog\Model\Indexer\Product\Eav\Processor $productEavIndexerProcessor,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Product\Image\CacheFactory $imageCacheFactory,
        \Magento\Catalog\Model\ProductLink\CollectionProvider $entityCollectionProvider,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkFactory,
        \Magento\Catalog\Api\Data\ProductLinkExtensionFactory $productLinkExtensionFactory,
        \Magento\Catalog\Model\Product\Attribute\Backend\Media\EntryConverterPool $mediaGalleryEntryConverterPool,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $joinProcessor,
        array $data = [],
        \Magento\Eav\Model\Config $config = null,
        \Magento\Catalog\Model\FilterProductCustomAttribute $filterCustomAttribute = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $storeManager,
            $metadataService,
            $url,
            $productLink,
            $itemOptionFactory,
            $stockItemFactory,
            $catalogProductOptionFactory,
            $catalogProductVisibility,
            $catalogProductStatus,
            $catalogProductMediaConfig,
            $catalogProductType,
            $moduleManager,
            $catalogProduct,
            $resource,
            $resourceCollection,
            $collectionFactory,
            $filesystem,
            $indexerRegistry,
            $productFlatIndexerProcessor,
            $productPriceIndexerProcessor,
            $productEavIndexerProcessor,
            $categoryRepository,
            $imageCacheFactory,
            $entityCollectionProvider,
            $linkTypeProvider,
            $productLinkFactory,
            $productLinkExtensionFactory,
            $mediaGalleryEntryConverterPool,
            $dataObjectHelper,
            $joinProcessor,
            $data,
            $config,
            $filterCustomAttribute
        );
        $this->helper = $helper;
    }

    public function getPriceInfo()
    {
        if ($this->getTypeId() !== \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $minProduct = $this->helper->getMinProduct($this);
        } else {
            $minProduct = $this;
        }

        if (!$this->_priceInfo) {
            $this->_priceInfo = $this->_catalogProductType->getPriceInfo($minProduct);
        }

        return $this->_priceInfo;
    }
}
