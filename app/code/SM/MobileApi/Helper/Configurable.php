<?php
namespace SM\MobileApi\Helper;

/**
 * Class Configurable
 *
 * @package SM\MobileApi\Helper\Product
 */
class Configurable extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_configurableAttributeData;

    /**
     * @var \Magento\ConfigurableProduct\Helper\Data
     */
    protected $_configurableHelper;

    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\Configurable\AttributeFactory
     */
    protected $_configurableAttribute;

    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\Configurable\AttributeOptionFactory
     */
    protected $_configurableAttributeOption;

    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\Configurable\ProductFactory
     */
    protected $_product;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * @var \SM\MobileApi\Helper\Price
     */
    protected $_apiPriceHelper;

    /**
     * @var \SM\MobileApi\Helper\Product\Common
     */
    protected $_commonHelper;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $_swatchHelper;

    /**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    protected $sourceItemsBySku;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var \SM\MobileApi\Model\Product\Image
     */
    private $image;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\ConfigurableProduct\Helper\Data $configurableHelper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \SM\MobileApi\Helper\Price $apiPriceHelper,
        \SM\MobileApi\Model\Data\Catalog\Product\Configurable\AttributeFactory $attributeFactory,
        \SM\MobileApi\Model\Data\Catalog\Product\Configurable\AttributeOptionFactory $attributeOptionFactory,
        \SM\MobileApi\Model\Data\Catalog\Product\Configurable\ProductFactory $productFactory,
        \SM\MobileApi\Helper\Product\Common $commonHelper,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
        \Magento\Eav\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \SM\MobileApi\Model\Product\Image $image
    ) {
        $this->_configurableAttributeData   = $configurableAttributeData;
        $this->_configurableHelper          = $configurableHelper;
        $this->_productHelper               = $productHelper;
        $this->_configurableAttribute       = $attributeFactory;
        $this->_configurableAttributeOption = $attributeOptionFactory;
        $this->_product                     = $productFactory;
        $this->_stockState                  = $stockState;
        $this->_stockRegistry               = $stockRegistry;
        $this->_apiPriceHelper              = $apiPriceHelper;
        $this->_commonHelper                = $commonHelper;
        $this->_swatchHelper                = $swatchHelper;
        $this->sourceItemsBySku             = $sourceItemsBySku;
        $this->config                       = $config;
        $this->storeManager                 = $storeManager;
        $this->appEmulation                 = $appEmulation;
        $this->image                        = $image;

        parent::__construct($context);
    }

    /**
     * Return product configurable data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfigurableAttributes($product, &$productInfo)
    {
        if (!$product || !$product->getId()) {
            return null;
        }
        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return null;
        }

        $dataModel = $this->_configurableAttributeData;
        $allowProducts =  $this->_getAllowProducts($product, $productInfo);
        $options = $this->_getOptions($product, $allowProducts);
        $attributesData = $dataModel->getAttributesData($product, $options);
        if (!isset($attributesData['attributes'])) {
            return null;
        }

        $data = [];
        $swatchData = (isset($options['option_id'])) ? $this->_swatchHelper->getSwatchesByOptionsId($options['option_id']) : [];
        foreach ($attributesData['attributes'] as $attribute) {
            $attributeData = $this->_configurableAttribute->create();
            $attributeData->setId($attribute['id']);
            $attributeData->setCode($attribute['code']);
            $attributeData->setLabel($attribute['label']);
            $type = $this->config->getAttribute('catalog_product', $attribute['code'])
                ->getFrontend()->getAttribute()->getSwatchInputType();
            $attributeData->setInputType($this->getInputType($type));
            if (is_array($attribute['options'])) {
                $options = [];
                foreach ($attribute['options'] as $option) {
                    $attributeOptionData = $this->_configurableAttributeOption->create();
                    $attributeOptionData->setId($option['id']);
                    $attributeOptionData->setLabel($option['label']);
                    $hexCode = $this->getColorByOptionId($swatchData, $option);
                    $attributeOptionData->setHexColorCode(
                        ($attribute['code'] == 'color' && isset($hexCode['color'])) ? $hexCode['color'] : null
                    );
                    $attributeOptionData->setImage(
                        ($attribute['code'] == 'color' && isset($hexCode['image'])) ? $hexCode['image'] : null
                    );
                    if (is_array($option['products'])) {
                        $products = [];
                        foreach ($option['products'] as $product) {
                            /* @var \Magento\Catalog\Model\Product $product */
                            $productData = $this->_product->create();
                            $this->setProductData($productData, $product);
                            $products[] = $productData;
                        }

                        $attributeOptionData->setProducts($products);
                    }

                    $options[] = $attributeOptionData;
                }

                $attributeData->setOptions($options);
            }

            $data[] = $attributeData;
        }

        return $data;
    }

    /**
     * Get Input Type
     *
     * @param $type
     * @return string
     */
    protected function getInputType($type)
    {
        switch ($type) {
            case 'visual':
                return \Magento\Swatches\Model\Swatch::SWATCH_TYPE_VISUAL_ATTRIBUTE_FRONTEND_INPUT;

            case 'text':
                return \Magento\Swatches\Model\Swatch::SWATCH_TYPE_TEXTUAL_ATTRIBUTE_FRONTEND_INPUT;

            default:
                return \Magento\Swatches\Model\Swatch::SWATCH_INPUT_TYPE_DROPDOWN;
        }
    }

    /**
     * @param  \SM\MobileApi\Api\Data\Catalog\Product\Configurable\ProductInterface $productData
     * @param  \Magento\Catalog\Model\Product $product
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setProductData($productData, $product)
    {
        $productData->setId($product->getId());
        $productData->setSku($product->getSku());
        $productData->setStock($this->getStockConfigurable($product));
        $productData->setIsSaleable($this->isProductSalable($product));
        $productData->setIsAvailable($product->isAvailable());
        $productData->setThumbnailImage($this->getThumbnailImage($product));
        $stockItem = $this->_stockRegistry->getStockItem($product->getId());
        if ($stockItem) {
            $productData->setBackorders($stockItem->getBackorders());
        }

        $finalPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE);
        $productData->setFinalPrice($this->_apiPriceHelper->formatPrice($finalPrice->getAmount()->getValue()));
        $regularPrice = $product->getPriceInfo()->getPrices()
            ->get(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getAmount()->getValue();
        $productData->setPrice($this->_apiPriceHelper->formatPrice($regularPrice));
        $productData->setProductLabel($this->_commonHelper->getProductLabel($product));
    }

    /**
     * Get Allowed Products
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function _getAllowProducts($product, $productInfo)
    {
        $products = [];
        $skipSaleableCheck = $this->_productHelper->getSkipSaleableCheck();
        $allProducts = $product->getTypeInstance()->getUsedProducts($product, null);
        foreach ($allProducts as $product) {
            /* @var \Magento\Catalog\Model\Product $product */
            if ($product->isSaleable() || $skipSaleableCheck) {
                $products[] = $product;
                $this->setParentsImages($product, $productInfo);
            }
        }

        return $products;
    }

    /**
     * Get Hex Color Code
     *
     * @param $swatchData
     * @param $option
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getColorByOptionId($swatchData, $option)
    {
        $hexColorCode = null;

        foreach ($swatchData as $swatch) {
            if ($option['id'] == $swatch['option_id']) {
                $hexColorCode = $swatch['value'];
                break;
            }
        }
        $text = str_replace(' ', '', $hexColorCode);
        $result = [];
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (isset($hexColorCode[0]) && $hexColorCode[0] == '#') {
            $result['color'] =  $text;
        } elseif (!empty($text)) {
            $result['image'] = $mediaUrl . \Magento\Swatches\Helper\Media::SWATCH_MEDIA_PATH . $text;
        }
        return $result;
    }

    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    protected function _getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            foreach ($this->_getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                $options['option_id'][] = $attributeValue;
                $options[$productAttributeId][$attributeValue][] = $product;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }

        return $options;
    }

    /**
     * Get allowed attributes
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function _getAllowAttributes($product)
    {
        return $product->getTypeInstance()->getConfigurableAttributes($product);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return float|int|null
     */
    protected function getStockConfigurable($product)
    {
        $sources = $this->sourceItemsBySku->execute($product->getSku());
        $stockChild = 0;
        if (!empty($sources)) {
            foreach ($sources as $source) {
                if ($source->getStatus() && $source->getQuantity()) {
                    $stockChild = $stockChild + $source->getQuantity();
                }
            }
        }
        return $stockChild;
    }

    /**
     * Check product is salable or not, don't rely on $product->getIsSalable()
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    protected function isProductSalable($product)
    {
        if ($product) {
            if ($this->_commonHelper->isProductAllowedBackOrder($product)) {
                //if product is allowed back order => always return true
                return true;
            } else {
                if ($this->_commonHelper->isProductEnabled($product) && $this->getStockConfigurable($product) > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    protected function getThumbnailImage($product)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $thumbnailImage = $this->_productHelper->getThumbnailUrl($product);
        $this->appEmulation->stopEnvironmentEmulation();

        return $thumbnailImage;
    }

    private function setParentsImages($product, &$productInfo)
    {
        $productInfo->setGallery(array_merge($productInfo->getGallery(), $this->image->getGalleryInfo($product)));
    }
}
