<?php

namespace Trans\MobileApi\Model;

use Magento\Bundle\Helper\Catalog\Product\Configuration as BundleConfiguration;
use Magento\Catalog\Helper\Image as MagentoImageHelper;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\CatalogEvent\Model\Event as SaleEvent;
use Magento\Checkout\Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Exception as HttpException;
use Magento\Quote\Api\Data\CartItemExtensionInterfaceFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory;
use SM\Checkout\Api\Data\CartItemDataInterfaceFactory;
use SM\Checkout\Model\Cart\Item\Data\OptionListFactory;
use SM\FreshProductApi\Helper\Fresh;
use SM\GTM\Model\BasketFactory;
use SM\Installation\Block\Form as Installation;
use SM\MobileApi\Model\Quote\Item\Stock;
use SM\MobileApi\Model\Cart as SMCart;
use Trans\MobileApi\Model\Cart\SortData;

class TransCart extends SMCart
{

    private $categoryEventList;

    private $productFactory;

    private $productRepository;

    private $customerSession;

    private $historyFactory;

    private $cartItemDataFactory;

    private $cartItemExFactory;

    private $helperConfiguration;

    private $itemResolver;

    private $urlBuilder;

    private $installationFactory;

    private $bundleConfiguration;

    private $basketCollectionFactory;

    private $basketFactory;

    private $basketInterfaceFactory;


    /**
     * @var \Magento\Quote\Api\Data\CartInterface
     */
    private $currentQuote;

    private $sortData;

    public function __construct(
        \SM\MobileApi\Api\Data\GTM\BasketInterfaceFactory $basketInterfaceFactory,
        \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory,
        BasketFactory $basketFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Helper\Cart $cart,
        \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \SM\FlashSale\Model\HistoryFactory $historyFactory,
        \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $cartItemOptionsProcessor,
        \Magento\Framework\App\Request\Http $request,
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
        \SM\MobileApi\Api\Data\CartItemInterfaceFactory $cartItemFactory,
        \SM\Bundle\Helper\Data $apiBundleHelper,
        \Magento\Framework\Registry $registry,
        \SM\Checkout\ViewModel\CartItem $cartItem,
        \SM\MobileApi\Model\Authorization\TokenUserContext $tokenUserContext,
        CustomerRepositoryInterface $customer,
        Stock $productStock,
        \SM\Checkout\Model\Data\CartMessageFactory $cartMessageFactory,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\Checkout\Model\Price $pricehelper,
        \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory $gtmCart,
        Fresh $fresh,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \SM\ShoppingList\Helper\Data $shoppingListHelper,
	    SortData $sortData
    ) {
        $this->categoryEventList        = $categoryEventList;
        $this->productFactory           = $productFactory;
        $this->productRepository        = $productRepository;
        $this->customerSession          = $customerSession;
        $this->historyFactory           = $historyFactory;
        $this->cartItemExFactory        = $cartItemExFactory;
        $this->cartItemDataFactory      = $cartItemDataFactory;
        $this->installationFactory      = $installationFactory;
        $this->helperConfiguration      = $helperConfiguration;
        $this->bundleConfiguration      = $bundleConfiguration;
        $this->itemResolver             = $itemResolver;
        $this->urlBuilder               = $urlBuilder;
        $this->basketCollectionFactory  = $basketCollectionFactory;
        $this->basketFactory            = $basketFactory;
        $this->basketInterfaceFactory   = $basketInterfaceFactory;
        $this->sortData = $sortData;

        parent::__construct(
            $basketInterfaceFactory,
            $basketCollectionFactory,
            $basketFactory,
            $quoteRepository,
            $productFactory,
            $cart,
            $categoryEventList,
            $productRepository,
            $customerSession,
            $historyFactory,
            $cartItemOptionsProcessor,
            $request,
            $cartItemExFactory,
            $cartItemDataFactory,
            $installationFactory,
            $helperConfiguration,
            $bundleConfiguration,
            $itemResolver,
            $urlBuilder,
            $magentoImageHelper,
            $appEmulation,
            $storeInterface,
            $optionListFactory,
            $swatchHelper,
            $cartItemFactory,
            $apiBundleHelper,
            $registry,
            $cartItem,
            $tokenUserContext,
            $customer,
            $productStock,
            $cartMessageFactory,
            $productGtm,
            $pricehelper,
            $gtmCart,
            $fresh,
            $quoteManagement,
            $addressRepository,
            $shoppingListHelper,
            $updateCart
        );
    }

        /**
     * @inheritdoc
     * @throws \Magento\Framework\Webapi\Exception
     * @throws CouldNotSaveException
     */
    public function getItems($checkStock = true)
    {
        $customerId = $this->tokenUserContext->getUserId();
        if (!$customerId || $customerId == 0) {
            return $this->cartItemFactory->create();
        }

        $output             = [];
        $cartMessageFactory = [];
        $isRemoveProduct    = false;
        $isAdjustQty        = false;

        /** @var  \Magento\Quote\Model\Quote $quote */
        if (empty($this->currentQuote)) {
            $this->getCartIdForCustomer($customerId);
            $quote = $this->currentQuote = $this->quote;
        } else {
            $quote = $this->currentQuote;
        }

        $totalQty   = 0;
        $removeMultiShippingFlag = false;
        if ($quote->isMultipleShippingAddresses() || $quote->getIsMultiShipping()) {
            foreach ($quote->getAllShippingAddresses() as $address) {
                $quote->removeAddress($address->getId());
            }
            $shippingAddress = $quote->getShippingAddress();
            $defaultShipping = $quote->getCustomer()->getDefaultShipping();
            if ($defaultShipping) {
                $defaultCustomerAddress = $this->addressRepository->getById($defaultShipping);
                $shippingAddress->importCustomerAddressData($defaultCustomerAddress);
            }
            $quote->setIsMultiShipping(0);
            $extensionAttributes = $quote->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                $extensionAttributes->setShippingAssignments([]);
            }
            $removeMultiShippingFlag = true;
            //$this->quoteRepository->save($quote);
        }
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getItemsCollection() as $item) {
            if (!$this->itemIsActive($item)) {
                continue;
            }

            $totalQty += $item->getQty();

            $cartMessageFactory = $this->cartMessageFactory->create();

            if ($checkStock) {
                $availableStock = $this->productStock->getStock($item); //get saleable quantity

                //Adjust stock of product or remove product if product out of stock
                if ($availableStock <= 0) {
                    $quote->removeItem($item->getItemId());
                    if (!$this->registry->registry("remove_cart_item")) {
                        $this->registry->register("remove_cart_item", true);
                    }
                    $isRemoveProduct = true;
                    $message = "One of the product is no longer available and we remove it from your cart";
                    $cartMessageFactory->setMessage($message);
                    $cartMessageFactory->setMessageType(MessageInterface::TYPE_WARNING);
                    continue;
                } else {
                    if ($item->getQty() > $availableStock) {
                        $item->setQty($availableStock);
                        $isAdjustQty         = true;
                        $extensionAttributes = $item->getExtensionAttributes();
                        $cartMessageFactory->setMessage(__('The quantity has been adjusted due to stock limitation.'));
                        $cartMessageFactory->setMessageType(MessageInterface::TYPE_WARNING);
                        $extensionAttributes->setCartMessage($cartMessageFactory);
                        $item->setExtensionAttributes($extensionAttributes);
                    }
                }
            } else {
                $availableStock = 0;
            }
            //Add addition inform
            $this->addDataToItem($quote, $item, $checkStock, $availableStock);
            $freshProductData = $this->fresh->populateObject($item->getProduct());
            $extension = $item->getExtensionAttributes();
            $extension->setFreshProduct($freshProductData);
            $item->setExtensionAttributes($extension);

            //Apply Custom option
            $item = $this->cartItemOptionsProcessor->addProductOptions($item->getProductType(), $item);
            $output[] = $this->cartItemOptionsProcessor->applyCustomOptions($item);
        }

        if ($isRemoveProduct || $isAdjustQty || $removeMultiShippingFlag) {
            $quote->setTotalsCollectedFlag(true);
            $this->quoteRepository->save($quote);
        }
        if ($this->registry->registry("remove_cart_item")) {
            $this->registry->unregister("remove_cart_item");
        }
        //Return result
        $cartItem = $this->cartItemFactory->create();
        $cartItem->setId($quote->getId());
        $cartItem->setItems($output);
        if ($isRemoveProduct) {
            $cartItem->setMessages($cartMessageFactory);
        }
        $cartItem->setBasketQty($totalQty);
        $cartItem->setBasketValue($quote->getGrandTotal());

        if ($customerId) {
            $basket = $this->basketCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
            if (!$basket->getData()) {
                $basket = $this->basketFactory->create();
                $basket->setData('customer_id', $customerId);
                $basket->save();
            }
            $cartItem->setBasketID($basket->getId() ?? null);
        }

        return $cartItem;
    }

    /**
     * @param $customer
     * @param $item
     */
    private function setOriginalPrice($customer, $item)
    {
        $attributeCode = $this->getOmniNormalPriceAttributeCode($customer);
        /** @var Item $item */
        $item          = ($item->getParentItem() ? $item->getParentItem() : $item);
        $product       =$this->productRepository->getById($item->getProductId());

        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            $option = $item->getProduct()->getTypeInstance()->getOrderOptions($item->getProduct());
            $product = $this->productRepository->get($option['simple_sku']);
        }
        $price         = $product->getData($attributeCode);
        if ($product->getTypeId() == 'bundle') {
            $productTypeInstance = $product->getTypeInstance();
            $productOption = $productTypeInstance
                ->getSelectionsCollection($productTypeInstance->getOptionsIds($product), $product)
                ->getItems();

            $option=$item->getProduct()->getTypeInstance()->getOrderOptions($product);

            $selectedProduct = $this->getSelectedProduct($productOption, $option);
            $price = 0;

            foreach ($selectedProduct as $p) {
                $price += $p->getPrice();
            }
        }

        try {
            $item->setBasePriceByLocation($price);
            $item->save();
        } catch (\Exception $exception) {
        }
    }

    
    /**
     * Add additional Data To Cart Item
     * @param $quote
     * @param $item
     */
    private function addDataToItem($quote, $item, $checkStock, $availableStock)
    {

        
        /**
         * @var \SM\Checkout\Model\Cart\Item\Data $itemData
         * @var \Magento\Quote\Api\Data\CartItemExtensionInterface
         * @var \SM\Checkout\Model\Cart\Item\ProductInfo $productInfo
         */
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
            $configOptionList = $this->sortData->sortCart($configOptionList);
            foreach ($configOptionList as $key => $config) {
                if (is_string($config["option_value"]) && $config["option_value"] == '') {
                    unset($configOptionList[$key]);
                }
            }
            
            
            $itemData->setConfigOptionList($this->convertOptionListToObject($configOptionList));
        } else {
            
            $configOptionList = $this->helperConfiguration->getOptions($item);
            $itemData->setConfigOptionList($this->convertOptionListToObject($configOptionList));
        }
        
        $itemData->setInstallationData($this->getInstallation($buyRequest, $item));
        $itemData->setProductLabel($this->getProductLabel($item->getId(), $buyRequest));
        $itemData->setImageUrl($this->getImage($item));
        $itemData->setProductSku($item->getProduct()->getData('sku'));
        $itemData->setProductId($item->getProduct()->getId());
        $itemData->setIsChecked($item->getIsActive());
        $itemData->setDiscountPercent($this->cartItem->getDiscountPercent($item));
        $itemData->setOriginalPrice($this->pricehelper->getRegularPrice($item->getProduct()));
        if (!$checkStock) {
            $availableStock = $this->productStock->getStock($item);
        }
        $itemData->setSalableQuantity($availableStock);
        $itemData->setGtmData($this->getGTMData($item->getProduct(), $item, $quote));

        $freshProductData = $this->fresh->populateObject($item->getProduct());
        $extensionAttributes->setFreshProduct($freshProductData);
        $extensionAttributes->setItemData($itemData);
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
     * @param int $item
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getImage($item)
    {
        $mediaUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . self::PRODUCT_IMAGE_PATH;
        $storeId            = $this->storeInterface->getStore()->getId();
        $image = $this->getProductForThumbnail($item)->getThumbnail();

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
     * @param int $item
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @codeCoverageIgnore
     */
    private function getProductForThumbnail($item)
    {
        return $this->itemResolver->getFinalProduct($item);
    }

    /**
     * @param $buyRequest
     * @param $item
     * @return mixed
     */
    private function getInstallation($buyRequest, $item)
    {
        /**
         * @var \SM\Checkout\Model\Cart\Item\Data\Installation $installation
         */
        $installation = $this->installationFactory->create();
        $data = $buyRequest->getData(\SM\Installation\Helper\Data::QUOTE_OPTION_KEY);

        $product = $this->productRepository->getById($item->getProduct()->getId());
        $allowInstallation = $product->getData(\SM\Installation\Helper\Data::PRODUCT_ATTRIBUTE);
        if ($allowInstallation == null || $allowInstallation == "") {
            $allowInstallation = false;
        }
        $data["allow_installation"] = $allowInstallation;

        return  $installation->setObjectData($data);
    }

}
