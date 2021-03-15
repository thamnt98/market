<?php

namespace SM\MobileApi\Model;

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

class Cart implements \SM\MobileApi\Api\CartInterface
{
    const PRODUCT_IMAGE_PATH = 'catalog/product';

    protected $quote;

    protected $quoteRepository;

    protected $cart;

    protected $pricehelper;

    protected $categoryEventId = null;

    protected $event = null;

    protected $cartItemOptionsProcessor;

    protected $magentoImageHelper;

    protected $appEmulation;

    protected $storeInterface;

    protected $optionListFactory;

    protected $swatchHelper;

    protected $request;

    protected $cartItemFactory;

    protected $apiBundleHelper;

    protected $registry;

    protected $cartItem;

    protected $tokenUserContext;

    protected $customer;

    protected $productStock;

    protected $cartMessageFactory;

    protected $productGtm;

    protected $gtmCart;

    protected $fresh;

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

    public $quoteManagement;

    /**
     * @var \Magento\Quote\Api\Data\CartInterface
     */
    private $currentQuote;

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
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    ) {
        $this->fresh                    = $fresh;
        $this->quoteRepository          = $quoteRepository;
        $this->cart                     = $cart;
        $this->categoryEventList        = $categoryEventList;
        $this->productFactory           = $productFactory;
        $this->productRepository        = $productRepository;
        $this->customerSession          = $customerSession;
        $this->historyFactory           = $historyFactory;
        $this->cartItemOptionsProcessor = $cartItemOptionsProcessor;
        $this->request                  = $request;
        $this->cartItemExFactory        = $cartItemExFactory;
        $this->cartItemDataFactory      = $cartItemDataFactory;
        $this->installationFactory      = $installationFactory;
        $this->helperConfiguration      = $helperConfiguration;
        $this->bundleConfiguration      = $bundleConfiguration;
        $this->itemResolver             = $itemResolver;
        $this->urlBuilder               = $urlBuilder;
        $this->magentoImageHelper       = $magentoImageHelper;
        $this->appEmulation             = $appEmulation;
        $this->storeInterface           = $storeInterface;
        $this->optionListFactory        = $optionListFactory;
        $this->swatchHelper             = $swatchHelper;
        $this->cartItemFactory          = $cartItemFactory;
        $this->apiBundleHelper          = $apiBundleHelper;
        $this->registry                 = $registry;
        $this->basketCollectionFactory  = $basketCollectionFactory;
        $this->basketFactory            = $basketFactory;
        $this->cartItem                 = $cartItem;
        $this->tokenUserContext         = $tokenUserContext;
        $this->customer                 = $customer;
        $this->basketInterfaceFactory   = $basketInterfaceFactory;
        $this->productStock             = $productStock;
        $this->cartMessageFactory       = $cartMessageFactory;
        $this->productGtm               = $productGtm;
        $this->gtmCart                  = $gtmCart;
        $this->pricehelper              = $pricehelper;
        $this->quoteManagement          = $quoteManagement;
        $this->addressRepository = $addressRepository;
    }

    /**
     * Add product to cart
     * @return int $cartId
     * @return int $customerId
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $cartItem
     * @return bool
     * @throws Exception
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \Zend_Json_Exception
     */
    public function addToCart($cartId, $customerId, $cartItem)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $customer = $this->customer->getById($customerId);
        if ($this->registry->registry("remove_cart_item")) {
            $this->registry->unregister("remove_cart_item");
        }
        try {
            foreach ($cartItem as $item) {
                $item->setQuoteId($cartId);
                /** @var \Magento\Quote\Model\Quote $quote */
                $cartId = $item->getQuoteId();
                if (!$cartId) {
                    throw new InputException(
                        __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'quoteId'])
                    );
                }
                $notAdd = false;
                $this->_initializeEventsForQuoteItems($quote);
                $truePrice = 0;
                $currentProduct = $this->productRepository->get($item->getSku());
                $event = $this->event;

                if ($this->categoryEventId != null && array_search($this->categoryEventId, $currentProduct->getCategoryIds()) !== false) {
                    if ($event) {
                        if ($event->getStatus() !== \Magento\CatalogEvent\Model\Event::STATUS_OPEN) {
                            $item->setHasError(true)->setMessage(__('This product is no longer on sale.'));
                            $item->getQuote()->setHasError(
                                true
                            )->addMessage(
                                __('Some of these products can no longer be sold.')
                            );

                            $item->setHasError(false);
                            $item->getQuote()->setHasError(false);

                            $item->setCustomPrice($currentProduct->getPrice());
                            $item->setOriginalCustomPrice($currentProduct->getPrice());
                            $item->getProduct()->setIsSuperMode(true);
                            $item->setEventId(null);
                            $item->setEvent(null);
                            $item->save();
                        } else {
                            if ($currentProduct->getData('is_flashsale') &&
                                $currentProduct->getData('flashsale_qty') > 0 &&
                                $currentProduct->getData('flashsale_qty_per_customer') > 0) {
                                $history = $this->historyFactory->create();
                                $collection = $history->getCollection()
                                    ->addFieldToFilter('event_id', $event->getId())
                                    ->addFieldToFilter('item_id', $currentProduct->getId());

                                $itemTotalBuy = 0;
                                $itemCustomerBuy = 0;
                                foreach ($collection as $historyItem) {
                                    if ($customer->getId() == $historyItem->getData("customer_id")) {
                                        $itemCustomerBuy = $historyItem->getData('item_qty_purchase');
                                    }
                                    $itemTotalBuy += $historyItem->getData('item_qty_purchase');
                                }

                                $flashSaleLimit = $currentProduct->getData('flashsale_qty');
                                $flashSaleCustomerLimit = $currentProduct->getData('flashsale_qty_per_customer');

                                $qtyInCart = $this->checkProductInCart($quote, $currentProduct);

                                if ($qtyInCart != null) {
                                    $currentQty = $qtyInCart;
                                }

                                $qtyNow = $item->getQty();

                                $availableQty = $flashSaleLimit - $itemTotalBuy;
                                $availableCustomerQty = $flashSaleCustomerLimit - $itemCustomerBuy;

                                if ($availableQty > 0 && $availableCustomerQty > 0) {
                                    if ($qtyNow <= $availableQty) {
                                        if ($qtyNow > $availableCustomerQty) {
                                            $qtyNow = $availableCustomerQty;
                                            $message = __('You exceeded the maximum quantity of Surprise Deals product. The excess items have been removed & can be purchased later in normal price.
');
                                        }
                                    } else {
                                        $qtyNow = $availableQty;
                                        $message = __('You exceeded the maximum quantity of Surprise Deals product. The excess items have been removed & can be purchased later in normal price.
');
                                        if ($qtyNow > $availableCustomerQty) {
                                            $qtyNow = $availableCustomerQty;
                                        }
                                    }
                                    if ($currentProduct->getSpecialPrice()) {
                                        $price = $currentProduct->getSpecialPrice();
                                    } else {
                                        $price = $currentProduct->getPrice();
                                    }

                                    if (isset($currentQty)) {
                                        if ($availableCustomerQty < $availableQty) {
                                            $avaiQty = $availableCustomerQty;
                                        } else {
                                            $avaiQty = $availableQty;
                                        }
                                        $remainQty =  $avaiQty - $currentQty;
                                        if ($remainQty < $qtyNow) {
                                            $message = __('You exceeded the maximum quantity of Surprise Deals product. The excess items have been removed & can be purchased later in normal price.
');
                                            if ($remainQty > 0) {
                                                $qtyNow = $remainQty;
                                            } else {
                                                $notAdd = true;
                                            }
                                        }
                                    }
                                    $truePrice = $price;
                                    $item->setCustomPrice($price);
                                    $item->setOriginalCustomPrice($price);
                                    $item->setData($item::KEY_QTY, $qtyNow);
                                } else {
                                    $item->setEventId(null);
                                    if ($item->getParentItem()) {
                                        $item->getParentItem()->setEventId(null);
                                    }
                                    $item->setCustomPrice($currentProduct->getPrice());
                                    $item->setOriginalCustomPrice($currentProduct->getPrice());
                                    $truePrice = $currentProduct->getPrice();
                                }
                            } else {
                                $item->setEventId(null);
                            }
                        }
                    } else {
                        /*
                         * If quote item has event id but event was
                         * not assigned to it then we should set event id to
                         * null as event was removed already
                         */
                        $item->setEventId(null);
                    }
                }

                if ($notAdd == false) {
                    $quoteItems = $quote->getItems();
                    $quoteItems[] = $item;
                    $quote->setItems($quoteItems);
                    $this->quoteRepository->save($quote->setTotalsCollectedFlag(true));

                    if ($truePrice != 0) {
                        $quote->getLastAddedItem()->setCustomPrice($truePrice);
                        $quote->getLastAddedItem()->setOriginalCustomPrice($truePrice);
                        $quote->getLastAddedItem()->getProduct()->setIsSuperMode(true);
                        $quote->getLastAddedItem()->save();
                    }
                    if ($event->getId()) {
                        $quote->getLastAddedItem()->setEventId($event->getId());
                        $quote->getLastAddedItem()->setEvent($event);
                    }

                    $this->setOriginalPrice($customer, $quote->getLastAddedItem());

                    if ($item->getExtensionAttributes()->getItemData() !== null &&
                        $item->getExtensionAttributes()->getItemData()->getInstallationData() !== null) {
                        $installData = $item->getExtensionAttributes()->getItemData()->getInstallationData();
                        $isInstallation = $installData->getIsInstallation();
                        $installationNote = $installData->getInstallationNote();

                        $data = [
                            "installation_service" => [
                                Installation::USED_FIELD_NAME => $isInstallation,
                                Installation::NOTE_FIELD_NAME => trim($installationNote)
                            ]
                        ];
                        $option = $quote->getLastAddedItem()->getOptionByCode('info_buyRequest');
                        if ($option) {
                            $value = \Zend_Json_Decoder::decode($option->getValue());
                            $value = array_merge($value, $data);
                            $option->setValue(\Zend_Json_Encoder::encode($value, true));
                        } else {
                            $option = [
                                'code' => 'info_buyRequest',
                                'value' => \Zend_Json_Encoder::encode($data, true)
                            ];
                        }

                        $quote->getLastAddedItem()->addOption($option);
                        $quote->getLastAddedItem()->saveItemOptions();
                    }
                }

                if (isset($message) && $message != "") {
                    throw new \Magento\Framework\Webapi\Exception(
                        __($message),
                        444,
                        \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
                    );
                }
            }
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
        return true;
    }

    /**
     * @param $quote
     * @param $product
     * @return null
     */
    public function checkProductInCart($quote, $product)
    {
        foreach ($quote->getItemsCollection() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            if ($item->getProduct()->getSku() == $product->getSku()) {
                return $item->getQty();
            }
        }
        return null;
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
     * @param $productOption
     * @param $option
     * @return array
     */
    public function getSelectedProduct($productOption, $option)
    {
        $products = [];
        $optionSelected = [];
        $infoBuyRequest = $option['info_buyRequest'];
        $bundleOption = $infoBuyRequest['bundle_option'];
        foreach ($productOption as $optionId => $productOpt) {
            foreach ($bundleOption as $opt) {
                if (in_array($productOpt->getSelectionId(), $opt)) {
                    $optionSelected[] = $productOpt;
                    if ($productOpt->getTypeId() == Configurable::TYPE_CODE) {
                        if (!empty($infoBuyRequest['super_attribute'])) {
                            $supperAttributes = $infoBuyRequest['super_attribute'];

                            $attrOpt          = 0;
                            foreach ($bundleOption as $k => $v) {
                                if (in_array($productOpt->getSelectionId(), $v)) {
                                    $attrOpt = $k;
                                }
                            }
                            $attributes        = $productOpt->getTypeInstance()->getConfigurableAttributesAsArray($productOpt);
                            $attributeId       = head(array_keys($supperAttributes[$attrOpt][$productOpt->getSelectionId()]));
                            $attributeSelected = head(array_values($supperAttributes[$attrOpt][$productOpt->getSelectionId()]));
                            $attrbute_code     = $attributes[$attributeId]['attribute_code'];
                            $usedProduct       = $productOpt->getTypeInstance()->getUsedProducts($productOpt);
                            /** @var Product $product */
                            foreach ($usedProduct as $product) {
                                if ($product->getData($attrbute_code) == $attributeSelected) {
                                    $products[] = $product;
                                }
                            }
                        }
                    } else {
                        $products[] = $productOpt;
                    }
                }
            }
        }

        return $products;
    }

    /**
     * @param $customer
     * @return mixed
     */
    public function getOmniStoreId($customer)
    {
        if (!empty($customer)) {
            $omni_store_id = $customer->getCustomAttribute(\SM\CustomPrice\Model\Customer::OMNI_STORE_ID);
        }

        if (!empty($omni_store_id)) {
            return $omni_store_id->getValue();
        }

        return $omni_store_id;
    }

    /**
     * @return string
     */
    public function getOmniNormalPriceAttributeCode($customer)
    {
        $omni_store_id = $this->getOmniStoreId($customer);
        return \SM\CustomPrice\Model\Customer::PREFIX_OMNI_NORMAL_PRICE . $omni_store_id;
    }

    /**
     * @return \SM\MobileApi\Api\Data\GTM\BasketInterface
     */
    public function getCartCount()
    {
        $customerId = $this->tokenUserContext->getUserId();
        if (!$customerId || $customerId == 0) {
            $basketInterface = $this->basketInterfaceFactory->create();
            $basketInterface->setCartTotal(0);
            $basketInterface->setBasketQty(0);
            $basketInterface->setBasketValue(0);
            $basketInterface->setCartTotal(0);
            return $basketInterface;
        }
        $basketInterface = $this->basketInterfaceFactory->create();
        $total = 0;
        $this->getCartIdForCustomer($customerId);
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quote;
        /** @var  \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getItemsCollection() as $item) {
            if ($item->isDeleted() || !$item->getIsActive() || $item->getParentItemId() || $item->getParentItem()) {
                continue;
            }
            $total += $item->getQty();
        }
        $basketInterface->setCartTotal($total);
        $basketInterface->setBasketQty($total);
        $basketInterface->setBasketValue($quote->getSubtotalWithDiscount());
        $basketInterface->setCartTotal($total);
        if ($customerId) {
            $basket = $this->basketCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
            if (!$basket->getData()) {
                $basket = $this->basketFactory->create();
                $basket->setData('customer_id', $customerId);
                $basket->save();
            }
            $basketInterface->setBasketId($basket->getId());
        }
        return $basketInterface;
    }

    /**
     * @param int $cartId
     * @param int $id
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function removeCart($cartId, $id)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($id);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('The %1 Cart doesn\'t contain the %2 item.', $cartId, $id)
            );
        }
        try {
            if (!$this->registry->registry("remove_cart_item")) {
                $this->registry->register("remove_cart_item", true);
            }
            $quote->removeItem($id);
            $this->quoteRepository->save($quote);
            $this->registry->unregister("remove_cart_item");
        } catch (\Exception $e) {
            $this->registry->unregister("remove_cart_item");
            throw new CouldNotSaveException(__("The item couldn't be removed from the quote."));
        }

        return true;
    }

    protected function _initializeEventsForQuoteItems(\Magento\Quote\Model\Quote $quote)
    {
        if (!$quote->getEventInitialized()) {
            $quote->setEventInitialized(true);
            $event = $this->categoryEventList->getEventCollection()
                ->addFieldToFilter('status', SaleEvent::STATUS_OPEN)
                ->addVisibilityFilter()
                ->getFirstItem();
            $this->event = $event;
            $this->categoryEventId = $event->getCategoryId();
        }

        return $this;
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
            $this->quoteRepository->save($quote);
        }
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getItemsCollection() as $item) {
            if (!$this->itemIsActive($item)) {
                continue;
            }

            $totalQty += $item->getQty();

            //Add addition inform
            $this->addDataToItem($quote, $item);
            $freshProductData = $this->fresh->populateObject($item->getProduct());
            $extension = $item->getExtensionAttributes();
            $extension->setFreshProduct($freshProductData);
            $item->setExtensionAttributes($extension);

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
            }

            //Apply Custom option
            $item = $this->cartItemOptionsProcessor->addProductOptions($item->getProductType(), $item);
            $output[] = $this->cartItemOptionsProcessor->applyCustomOptions($item);
        }

        if ($isRemoveProduct || $isAdjustQty) {
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
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     */
    public function setQuote(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        $this->currentQuote = $quote;
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getQuote()
    {
        return $this->currentQuote;
    }

    /**
     * @param $item
     * @return bool
     */
    protected function itemIsActive($item)
    {
        if (!$item->isDeleted() && !$item->getParentItemId() && !$item->getParentItem() && !$item->getIsVirtual()) {
            return true;
        } elseif ($this->isCartUpdate() && !$item->isDeleted() && !$item->getIsActive()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function isCartUpdate()
    {
        $currentUrl = $this->request->getFullActionName();
        $listUrl = ['transcheckout_cart_update','checkout_cart_add','checkout_sidebar_UpdateItemQty','checkout_cart_updatePost'];
        if (in_array($currentUrl, $listUrl)) {
            return true;
        }
        return false;
    }

    /**
     * Add additional Data To Cart Item
     * @param $quote
     * @param $item
     */
    private function addDataToItem($quote, $item)
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
            //$configData = $item->getBuyRequest()->getData('super_attribute');
            foreach ($configOptionList as $key => $config) {
                if (is_string($config["option_value"]) && $config["option_value"] == '') {
                    unset($configOptionList[$key]);
                }
                /*if (strtolower($config["label"]) == "color" && isset($configData[$config["option_id"]])) {
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
                }*/
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
        $itemData->setSalableQuantity($this->productStock->getStock($item));
        $itemData->setGtmData($this->getGTMData($item->getProduct(), $item, $quote));

        $freshProductData = $this->fresh->populateObject($item->getProduct());
        $extensionAttributes->setFreshProduct($freshProductData);
        $extensionAttributes->setItemData($itemData);
    }

    /**
     * @param $product
     * @return \SM\MobileApi\Api\Data\GTM\GTMCartInterface
     */
    protected function getGTMData($product, $item, $quote)
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
        if ($data['salePrice'] && $data['salePrice'] > 0) {
            $model->setProductOnSale(__('Yes'));
        } else {
            $model->setProductOnSale(__('Not on sale'));
        }
        $voucher = $quote->getApplyVoucher();
        if ($voucher != null && $voucher != '') {
            $model->setApplyVoucher(__('Yes'));
            $model->setVoucherId($voucher);
        } else {
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
            $configAttributeFactory->setLabel($option['label']);
            $configAttributeFactory->setValue($option['value']);
            $configAttributeFactory->setOptionId($option['option_id']);
            $configAttributeFactory->setOptionValue($option['option_value']);
            if (isset($option['option_color_text'])) {
                $configAttributeFactory->setOptionColorText($option['option_color_text']);
            }
            $data[] = $configAttributeFactory;
        }

        return $data;
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

    /**
     * @inheritDoc
     */
    public function getCartIdForCustomer($customerId)
    {
        try {
            //Get active quote by customer id
            $quote = $this->quoteRepository->getActiveForCustomer($customerId);
            $this->quote = $quote;
        } catch (NoSuchEntityException $e) {
            //If quote is not active or customer don't have
            //We will create new quote for customer
            $quote = $this->quoteManagement->createEmptyCartForCustomer($customerId);
            $this->quote = $quote;
            return (int)$quote;
        } catch (CouldNotSaveException $e) {
            throw new CouldNotSaveException(__("The cart can't be created."));
        }

        return (int)$quote->getId();
    }
}
