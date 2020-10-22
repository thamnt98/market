<?php

namespace SM\MobileApi\Helper;

use Amasty\Label\Helper\Config;
use Amasty\Label\Model\LabelsFactory;
use Amasty\Label\Model\ResourceModel\Index;
use Amasty\Label\Model\ResourceModel\Labels;
use Magento\Bundle\Model\Product\Type;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use SM\Catalog\Helper\Delivery;
use SM\Catalog\Helper\StorePickup;
use SM\MobileApi\Model\Data\Catalog\Product\Bundle\Options;
use SM\MobileApi\Model\Data\Catalog\Product\Bundle\OptionsFactory;
use SM\MobileApi\Model\Data\Catalog\Product\Bundle\ProductItems;
use SM\MobileApi\Model\Data\Catalog\Product\Bundle\ProductItemsFactory;
use SM\MobileApi\Model\Data\Catalog\Product\DeliveryIntoFactory;
use SM\MobileApi\Model\Data\Catalog\Product\StoreInfoFactory;
use SM\MobileApi\Model\Product\Image;

/**
 * Class BundleProduct
 *
 * @package SM\MobileApi\Helper
 */
class BundleProduct extends \SM\MobileApi\Helper\Product\Common
{
    /**
     * @var OptionsFactory
     */
    protected $optionsFactory;
    /**
     * @var ProductItemsFactory
     */
    protected $bundleItemsFactory;
    /**
     * @var Configurable
     */
    protected $helperConfigurable;
    /**
     * @var Image
     */
    protected $productHelperImage;
    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @var \SM\GTM\Block\Product\ListProduct
     */
    protected $productGtm;

    /**
     * @var \SM\MobileApi\Model\Data\GTM\GTMFactory
     */
    protected $gtmDataFactory;

    /**
     * BundleProduct constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManagerInterface
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateInterface $stockState
     * @param Index $labelIndex
     * @param Config $config
     * @param LabelsFactory $labelsFactory
     * @param Labels $labelsResource
     * @param TimezoneInterface $timezone
     * @param StoreManagerInterface $storeManager
     * @param StorePickup $helperStorePickup
     * @param StoreInfoFactory $storeInfoFactory
     * @param Delivery $helperDelivery
     * @param DeliveryIntoFactory $deliveryIntoFactory
     * @param OptionsFactory $optionsFactory
     * @param ProductItemsFactory $bundleItemsFactory
     * @param Configurable $helperConfigurable
     * @param Image $productHelperImage
     * @param Emulation $appEmulation
     * @param \SM\GTM\Block\Product\ListProduct $productGtm
     * @param \SM\MobileApi\Model\Data\GTM\GTMFactory $gtmDataFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManagerInterface,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        Index $labelIndex,
        Config $config,
        LabelsFactory $labelsFactory,
        Labels $labelsResource,
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager,
        StorePickup $helperStorePickup,
        StoreInfoFactory $storeInfoFactory,
        Delivery $helperDelivery,
        DeliveryIntoFactory $deliveryIntoFactory,
        OptionsFactory $optionsFactory,
        ProductItemsFactory $bundleItemsFactory,
        Configurable $helperConfigurable,
        Image $productHelperImage,
        Emulation $appEmulation,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\MobileApi\Model\Data\GTM\GTMFactory $gtmDataFactory
    ) {
        $this->optionsFactory = $optionsFactory;
        $this->bundleItemsFactory = $bundleItemsFactory;
        $this->helperConfigurable = $helperConfigurable;
        $this->productHelperImage = $productHelperImage;
        $this->appEmulation = $appEmulation;
        $this->gtmDataFactory = $gtmDataFactory;
        $this->productGtm = $productGtm;
        parent::__construct(
            $context,
            $objectManagerInterface,
            $stockRegistry,
            $stockState,
            $labelIndex,
            $config,
            $labelsFactory,
            $labelsResource,
            $timezone,
            $storeManager,
            $helperStorePickup,
            $storeInfoFactory,
            $helperDelivery,
            $deliveryIntoFactory
        );
    }

    /**
     * @param $product
     * @return array
     */
    public function getBundleProductItems($product)
    {
        if (!$product || !$product->getId()) {
            return [];
        }
        if ($product->getTypeId() != Type::TYPE_CODE) {
            return [];
        }
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter(
            $product->getStoreId(),
            $product
        );
        $optionCollection = $productTypeInstance->getOptionsCollection($product);
        $selectionCollection = $productTypeInstance->getSelectionsCollection(
            $productTypeInstance->getOptionsIds($product),
            $product
        );
        $options = $optionCollection->appendSelections($selectionCollection, true);
        $data = [];
        if (count($options)) {
            foreach ($options as $option) {
                /** @var Options $optionsFactory */
                $optionsFactory = $this->optionsFactory->create();
                $optionsFactory->setData($option->getData());
                $productItem = [];
                foreach ($option->getSelections() as $product) {
                    /* @var ProductItems $productData */
                    $productData = $this->bundleItemsFactory->create();
                    $productData->setData($product->getData());
                    $productData->setImage($this->getMainImage($product));
                    $this->helperConfigurable->setProductData($productData, $product);
                    if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                        $productData->setConfigurableAttributes($this->helperConfigurable->getConfigurableAttributes($product));
                    }
                    $gtmData = $this->getGTMData($product);
                    $productData->setGtmData($gtmData);

                    $productItem[] = $productData;
                }
                $optionsFactory->setSelections($productItem);
                $data[] = $optionsFactory;
            }
        }
        return $data;
    }
    /**
     * Get default product image on listing page
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws NoSuchEntityException
     * @throws FileSystemException
     */
    public function getMainImage($product)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $mainImage = $this->productHelperImage->getMainImage($product);
        $this->appEmulation->stopEnvironmentEmulation();

        return $mainImage;
    }

    public function getGTMData($product)
    {
        $data = $this->productGtm->getGtm($product);
        $data = \Zend_Json_Decoder::decode($data);
        /** @var \SM\MobileApi\Api\Data\GTM\GTMInterface $gtmData */
        $gtmData = $this->gtmDataFactory->create();
        $gtmData
            ->setProductName($data['name'])
            ->setProductId($data['id'])
            ->setProductPrice($data['price'])
            ->setProductBrand($data['brand'])
            ->setProductCategory($data['category'])
            ->setProductSize($data['product_size'])
            ->setProductVolume($data['product_volume'])
            ->setProductWeight($data['product_weight'])
            ->setProductVariant($data['variant'])
            ->setDiscountPrice($data['salePrice'])
            ->setProductList($data['list'])
            ->setProductRating($data['rating'])
            ->setInitialPrice($data['initialPrice'])
            ->setDiscountRate($data['discountRate'])
            ->setProductType($product->getTypeId());
        if ($data['salePrice'] && $data['salePrice'] > 0) {
            $gtmData->setProductOnSale(__('Yes'));
        } else {
            $gtmData->setProductOnSale(__('Not on sale'));
        }

        return $gtmData;
    }
}
