<?php
/**
 * Class Search
 * @package SM\Checkout\Plugin\Quote
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Plugin\Api\Quote\Model;

use Magento\Bundle\Helper\Catalog\Product\Configuration as BundleConfiguration;
use Magento\Catalog\Helper\Image as MagentoImageHelper;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Quote\Api\Data\CartItemExtensionInterfaceFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory;
use SM\Checkout\Api\Data\CartItemDataInterfaceFactory;
use SM\Checkout\Model\Cart\Item\Data\OptionListFactory;
use SM\FreshProductApi\Helper\Fresh;
use SM\MobileApi\Model\Quote\Item\Stock;

class Quote
{
    const PRODUCT_IMAGE_PATH = 'catalog/product';

    /**
     * @var CartItemDataInterfaceFactory
     */
    private $cartItemDataFactory;

    /**
     * @var CartItemExtensionInterfaceFactory
     */
    private $cartItemExFactory;

    /**
     * @var Configuration
     */
    private $helperConfiguration;

    /** @var ItemResolverInterface */
    private $itemResolver;

    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    private $item;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var InstallationInterfaceFactory
     */
    private $installationFactory;

    /**
     * @var BundleConfiguration
     */
    private $bundleConfiguration;

    /**
     * @var MagentoImageHelper
     */
    protected $magentoImageHelper;

    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @var StoreManagerInterface
     */
    protected $storeInterface;

    /**
     * @var OptionListFactory
     */
    protected $optionListFactory;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \SM\Bundle\Helper\Data
     */
    protected $apiBundleHelper;

    /**
     * @var Stock
     */
    protected $productStock;

    /**
     * @var \SM\Checkout\ViewModel\CartItem
     */
    protected $cartItem;

    protected $productGtm;

    protected $gtmCart;

    protected $fresh;

    /**
     * Quote constructor.
     * @param \SM\Checkout\Helper\DigitalProduct $digitalHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param CartItemExtensionInterfaceFactory $cartItemExFactory
     * @param CartItemDataInterfaceFactory $cartItemDataFactory
     * @param InstallationInterfaceFactory $installationFactory
     * @param Configuration $helperConfiguration
     * @param BundleConfiguration $bundleConfiguration
     * @param ItemResolverInterface $itemResolver
     * @param UrlInterface $urlBuilder
     * @param MagentoImageHelper $magentoImageHelper
     * @param Emulation $appEmulation
     * @param StoreManagerInterface $storeInterface
     * @param OptionListFactory $optionListFactory
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\Bundle\Helper\Data $apiBundleHelper
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     * @param Stock $productStock
     */

    public function __construct(
        CartItemExtensionInterfaceFactory $cartItemExFactory,
        CartItemDataInterfaceFactory $cartItemDataFactory,
        InstallationInterfaceFactory $installationFactory,
        Configuration $helperConfiguration,
        BundleConfiguration $bundleConfiguration,
        ItemResolverInterface $itemResolver,
        UrlInterface $urlBuilder,
        MagentoImageHelper $magentoImageHelper,
        Emulation $appEmulation,
        StoreManagerInterface $storeInterface,
        OptionListFactory $optionListFactory,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\Bundle\Helper\Data $apiBundleHelper,
        Stock $productStock,
        \SM\Checkout\ViewModel\CartItem $cartItem,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory $gtmCart,
        Fresh $fresh
    ) {
        $this->cartItemExFactory = $cartItemExFactory;
        $this->cartItemDataFactory = $cartItemDataFactory;
        $this->installationFactory = $installationFactory;
        $this->helperConfiguration = $helperConfiguration;
        $this->bundleConfiguration = $bundleConfiguration;
        $this->itemResolver = $itemResolver;
        $this->urlBuilder = $urlBuilder;
        $this->magentoImageHelper = $magentoImageHelper;
        $this->appEmulation = $appEmulation;
        $this->storeInterface = $storeInterface;
        $this->optionListFactory = $optionListFactory;
        $this->swatchHelper = $swatchHelper;
        $this->productRepository = $productRepository;
        $this->apiBundleHelper = $apiBundleHelper;
        $this->productStock = $productStock;
        $this->cartItem = $cartItem;
        $this->productGtm = $productGtm;
        $this->gtmCart = $gtmCart;
        $this->fresh = $fresh;
    }

    /**
     * Add condition getIsActive from item with select/unselect item on cart
     * Add Item Data for mobile
     * Get array of all items what can be display directly
     *
     * @param \Magento\Quote\Model\Quote $subject
     * @param callable $proceed
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function aroundGetAllVisibleItems(
        \Magento\Quote\Model\Quote $subject,
        callable $proceed
    ) {
        return $proceed();
        $items = [];
        $this->quote = $subject;
        /**
         * @var \Magento\Quote\Model\Quote\Item $item
         */
        foreach ($subject->getItemsCollection() as $item) {
            if (!$item->getId() || $item->getIsActive() === null) {
                $item->setIsActive(1);
            }

            if ($this->itemIsActive($item) && !$item->getParentItemId() && !$item->getParentItem()) {
                $this->setItem($item);
                $this->addDataToItem();
                $items[] = $this->getItem();
            }
        }

        return $items;
    }

    /**
     * Add additional Data To Cart Item
     */
    private function addDataToItem()
    {
        /**
         * @var \SM\Checkout\Model\Cart\Item\Data $itemData
         * @var \Magento\Quote\Api\Data\CartItemExtensionInterface
         * @var \SM\Checkout\Model\Cart\Item\ProductInfo $productInfo
         */
        $item = $this->getItem();
        $extensionAttributes = $item->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->cartItemExFactory->create();
        }

        $itemData = $this->cartItemDataFactory->create();
        $buyRequest = $item->getBuyRequest();
        if ($item->getProductType() == 'bundle') {
            $optionData = [];
            foreach ($this->apiBundleHelper->getBundleOptions($item) as $option) {
                $configAttributeFactory = $this->optionListFactory->create();
                $configAttributeFactory->setLabel($option['label']);
                $configAttributeFactory->setValue($option['value']);
                $configAttributeFactory->setProductName($option['product_name']);
                $optionData[] = $configAttributeFactory;
            }
            $itemData->setOptionList($optionData);
        } elseif ($item->getProductType() == 'configurable') {
            $configOptionList = $this->helperConfiguration->getOptions($item);
            $configData = $item->getBuyRequest()->getData('super_attribute');
            foreach ($configOptionList as &$config) {
                if (strtolower($config["label"]) == "color" && isset($configData[$config["option_id"]])) {
                    $attr = $item->getProduct()->getResource()->getAttribute('color');
                    if ($attr->usesSource()) {
                        $optionText = $attr->getSource()->getOptionText($configData[$config["option_id"]]);
                        $config["option_color_text"] = $optionText;
                    }
                    $hashcodeData = $this->swatchHelper->getSwatchesByOptionsId([$configData[$config["option_id"]]]);
                    if (isset($hashcodeData[$configData[$config["option_id"]]]['value'])) {
                        $config["value"] = $hashcodeData[$configData[$config["option_id"]]]['value'];
                    }

                    $config["option_value"] = $configData[$config["option_id"]];
                }
            }
            $itemData->setConfigOptionList($this->convertOptionListToObject($configOptionList));
        } else {
            $configOptionList = $this->helperConfiguration->getOptions($item);
            $itemData->setConfigOptionList($this->convertOptionListToObject($configOptionList));
        }
        $itemData->setInstallationData($this->getInstallation($buyRequest));
        $itemData->setProductLabel($this->getProductLabel($item->getId(), $buyRequest));
        $itemData->setImageUrl($this->getImage());
        $itemData->setProductSku($item->getProduct()->getData('sku'));
        $itemData->setProductId($item->getProduct()->getId());
        $itemData->setIsChecked($this->itemIsActive($item));
        $itemData->setSalableQuantity($this->productStock->getStock($item));
        $itemData->setDiscountPercent($this->cartItem->getDiscountPercent($item));
        $itemData->setOriginalPrice($this->getItemPrice($item));
        $itemData->setGtmData($this->getGTMData($item->getProduct(),$item,$this->quote));

        $freshProductData = $this->fresh->populateObject($item->getProduct());
        $extensionAttributes->setFreshProduct($freshProductData);
        $extensionAttributes->setItemData($itemData);
    }

    /**
     * @param $product
     * @return \SM\MobileApi\Api\Data\GTM\GTMCartInterface
     */
    protected function getGTMData($product,$item,$quote)
    {
        $product = $this->productRepository->getById($product->getId());
        $model = $this->gtmCart->create();
        $data = $this->productGtm->getGtm($product);
        $data = \Zend_Json_Decoder::decode($data);
        $model->setProductName($data['name']);
        $model->setProductId($data['id']);
        $model->setProductPrice($data['price']);
        $model->setProductBrand($data['brand']);
        $model->setProductCategory($data['category']);
        $model->setProductSize($data['product_size']);
        $model->setProductVolume($data['product_volume']);
        $model->setProductWeight($data['product_weight']);
        $model->setProductVariant($data['variant']);
        $model->setDiscountPrice($data['initialPrice'] - $data['price']);
        $model->setProductList($data['list']);
        $model->setInitialPrice($data['initialPrice']);
        $model->setDiscountRate($data['discountRate']);
        $model->setProductType($product->getTypeId());
        $model->setProductRating($data['rating']);
        $model->setProductBundle($data['productBundle']);
        $model->setSalePrice($data['initialPrice'] - $data['price']);
        $model->setProductQty($item->getQty());
        if($data['salePrice'] && $data['salePrice'] > 0) {
            $model->setProductOnSale(__('Yes'));
        }else{
            $model->setProductOnSale(__('Not on sale'));
        }
        $voucher = $quote->getApplyVoucher();
        if($voucher != null && $voucher != ''){
            $model->setApplyVoucher(__('Yes'));
            $model->setVoucherId($voucher);
        }else{
            $model->setApplyVoucher(__('No'));
            $model->setVoucherId('');
        }

        return $model;
    }

    /**
     * Response for api return array string and need to clarify attribute so we need convert to object
     * @param $configOptionList
     * @return \SM\Checkout\Api\Data\CartItem\OptionListInterface[]
     */
    protected function convertOptionListToObject($configOptionList)
    {
        $data = [];

        foreach ($configOptionList as $option) {
            $configAttributeFactory = $this->optionListFactory->create();
            $optionId = isset($option['option_id'])? $option['option_id'] : null;
            $optionValue = isset($option['option_value'])? $option['option_value'] : null;
            $configAttributeFactory->setLabel($option['label']);
            $configAttributeFactory->setValue($option['value']);
            $configAttributeFactory->setOptionId($optionId);
            $configAttributeFactory->setOptionValue($optionValue);
            if (isset($option['option_color_text'])) {
                $configAttributeFactory->setOptionColorText($option['option_color_text']);
            }
            $data[] = $configAttributeFactory;
        }

        return $data;
    }

    /**
     * Get product original price in cart
     * @param $item
     * @return float
     */
    private function getItemPrice($item){
        if($item->getProduct()->getTypeId() == 'bundle'){
            return $item->getBasePriceByLocation() * 1;
        }else{
            return $item->getProduct()->getPrice() * 1;
        }
    }

    /**
     * @param $productId
     * @param $buyRequest
     * @return array|string[]
     */
    private function getProductLabel($productId, $buyRequest)
    {
        if (isset($buyRequest['product-cat-label-' . $productId])) {
            return ['product_label' => $buyRequest['product-cat-label-' . $productId]];
        }

        return ['product_label' => ''];
    }

    /**
     * Retrieve product image
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getImage()
    {
        $mediaUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . self::PRODUCT_IMAGE_PATH;
        $storeId            = $this->storeInterface->getStore()->getId();
        $image = $this->getProductForThumbnail()->getThumbnail();

        if ($image == 'no_selection' || $image == null) {
            $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
            $placeHolderImage   = $this->magentoImageHelper->getDefaultPlaceholderUrl('thumbnail');
            $this->appEmulation->stopEnvironmentEmulation();

            return $placeHolderImage;
        }

        return $mediaUrl . $image;
    }

    /**
     * Identify the product from which thumbnail should be taken.
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @codeCoverageIgnore
     */
    private function getProductForThumbnail()
    {
        return $this->itemResolver->getFinalProduct($this->getItem());
    }

    /**
     * @param $buyRequest
     * @return mixed
     */
    private function getInstallation($buyRequest)
    {
        /**
         * @var \SM\Checkout\Model\Cart\Item\Data\Installation $installation
         */
        $installation = $this->installationFactory->create();
        $data = $buyRequest->getData(\SM\Installation\Helper\Data::QUOTE_OPTION_KEY);

        $item = $this->getItem();
        $product = $this->productRepository->getById($item->getProduct()->getId());
        $allowInstallation = $product->getData(\SM\Installation\Helper\Data::PRODUCT_ATTRIBUTE);
        if ($allowInstallation == null || $allowInstallation == "") {
            $allowInstallation = false;
        }
        $data["allow_installation"] = $allowInstallation;

        return  $installation->setObjectData($data);
    }

    /**
     * @return \Magento\Quote\Model\Quote\Item
     */
    private function getItem()
    {
        return $this->item;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    private function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @param $item
     * @return bool
     */
    protected function itemIsActive($item)
    {
        if (!$item->isDeleted() && $item->getIsActive()) {
            return true;
        }
        return false;
    }
}
