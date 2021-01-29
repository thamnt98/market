<?php

namespace SM\Checkout\Model;

/**
 * Class MultiShippingHandle
 * @package SM\Checkout\Model
 */
class MultiShippingHandle
{
    const STORE_PICK_UP  = 'store_pickup_store_pickup';
    const NOT_SHIP  = 'transshipping_transshipping';
    const NOT_AVAILABLE  = 'transshipping_transshipping0';
    const DEFAULT_METHOD = 'transshipping_transshipping1';
    const SAME_DAY       = 'transshipping_transshipping2';
    const SCHEDULE       = 'transshipping_transshipping3';
    const NEXT_DAY       = 'transshipping_transshipping4';
    const DC             = 'transshipping_transshipping5';
    const TRANS_COURIER  = 'transshipping_transshipping6';

    protected $hasNormal   = false;
    protected $hasSpo      = false;
    protected $hasFresh    = false;
    protected $itemsUpdate = [];

    protected $addressEachItems = false;
    protected $inputItemsFormatAfterHandle;
    protected $outStockByMagento = [];
    protected $lowStockByMagento = [];
    protected $outStockByOar = [];
    protected $lowStockByOar = [];
    protected $mobile = false;
    protected $mobileItemsFormat = [];
    protected $initItems = [];
    protected $disablePickUp = false;
    protected $child = [];
    protected $skuList = [];

    /**
     * @var bool
     */
    protected $reload = false;

    /**
     * @var array
     */
    protected $reloadMessage = [];

    /**
     * @var bool
     */
    protected $orderIsSplit = false;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var Split
     */
    protected $split;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory
     */
    protected $previewOrderInterfaceFactory;

    /**
     * @var UpdateStockItem
     */
    protected $updateStockItem;

    /**
     * @var \SM\Checkout\Helper\Config
     */
    protected $helperConfig;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterfaceFactory
     */
    protected $itemInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterfaceFactory
     */
    protected $methodInterfaceFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\CollectionFactory
     */
    protected $addressCollectionFactory;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var \SM\FreshProductApi\Helper\Fresh
     */
    protected $fresh;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\ShippingMethodInterfaceFactory
     */
    protected $shippingMethodInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterfaceFactory
     */
    protected $itemInterfaceMobileFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \SM\GTM\Block\Product\ListProduct
     */
    protected $productGtm;

    /**
     * @var \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory
     */
    protected $gtmCart;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterfaceFactory
     */
    protected $deliveryInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory
     */
    protected $installationInterfaceFactory;

    /**
     * @var \SM\MobileApi\Model\Product\Common\Installation
     */
    protected $productInstallation;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterfaceFactory
     */
    protected $itemAdditionalInfoInterfaceFactory;

    /**
     * @var \Magento\Catalog\Helper\Product\ConfigurationPool
     */
    protected $configurationPool;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterfaceFactory
     */
    protected $productOptionsInterfaceFactory;
    /**
     * MultiShippingHandle constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param Split $split
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory
     * @param UpdateStockItem $updateStockItem
     * @param \SM\Checkout\Helper\Config $helperConfig
     * @param \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterfaceFactory $itemInterfaceFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterfaceFactory $methodInterfaceFactory
     * @param \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory
     * @param Price $price
     * @param \SM\FreshProductApi\Helper\Fresh $fresh
     * @param \SM\Checkout\Api\Data\Checkout\ShippingMethodInterfaceFactory $shippingMethodInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterfaceFactory $itemInterfaceMobileFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\GTM\Block\Product\ListProduct $productGtm
     * @param \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory $gtmCart
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterfaceFactory $deliveryInterfaceFactory
     * @param \SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory $installationInterfaceFactory
     * @param \SM\MobileApi\Model\Product\Common\Installation $productInstallation
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterfaceFactory $itemAdditionalInfoInterfaceFactory
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterfaceFactory $productOptionsInterfaceFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \SM\Checkout\Model\Split $split,
        \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory,
        \SM\Checkout\Model\UpdateStockItem $updateStockItem,
        \SM\Checkout\Helper\Config $helperConfig,
        \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterfaceFactory $itemInterfaceFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterfaceFactory $methodInterfaceFactory,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory  $addressCollectionFactory,
        \SM\Checkout\Model\Price $price,
        \SM\FreshProductApi\Helper\Fresh $fresh,
        \SM\Checkout\Api\Data\Checkout\ShippingMethodInterfaceFactory $shippingMethodInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterfaceFactory $itemInterfaceMobileFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory $gtmCart,
        \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterfaceFactory $deliveryInterfaceFactory,
        \SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory $installationInterfaceFactory,
        \SM\MobileApi\Model\Product\Common\Installation $productInstallation,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Helper\Image $imageHelper,
        \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterfaceFactory $itemAdditionalInfoInterfaceFactory,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterfaceFactory $productOptionsInterfaceFactory
    ) {
        $this->timezone                     = $timezone;
        $this->sourceRepository             = $sourceRepository;
        $this->split                        = $split;
        $this->previewOrderInterfaceFactory = $previewOrderInterfaceFactory;
        $this->updateStockItem              = $updateStockItem;
        $this->helperConfig              = $helperConfig;
        $this->itemInterfaceFactory = $itemInterfaceFactory;
        $this->methodInterfaceFactory = $methodInterfaceFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->price = $price;
        $this->fresh = $fresh;
        $this->shippingMethodInterfaceFactory = $shippingMethodInterfaceFactory;
        $this->itemInterfaceMobileFactory = $itemInterfaceMobileFactory;
        $this->productRepository = $productRepository;
        $this->productGtm = $productGtm;
        $this->gtmCart = $gtmCart;
        $this->deliveryInterfaceFactory = $deliveryInterfaceFactory;
        $this->installationInterfaceFactory = $installationInterfaceFactory;
        $this->productInstallation = $productInstallation;
        $this->appEmulation = $appEmulation;
        $this->imageHelper = $imageHelper;
        $this->itemAdditionalInfoInterfaceFactory = $itemAdditionalInfoInterfaceFactory;
        $this->configurationPool = $configurationPool;
        $this->productOptionsInterfaceFactory = $productOptionsInterfaceFactory;
    }

    /**
     * @param $items
     * @param $storePickUp
     * @param $customer
     * @param $checkoutSession
     * @param false $mobile
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function handleData($items, $storePickUp, $customer, $checkoutSession, $mobile = false)
    {
        $this->mobile = $mobile;
        $data           = ['reload' => false, 'error' => false, 'data' => [], 'split' => false, 'out_stock' => false, 'low_stock' => false];
        $splitOrderData = $this->getSplitOrderData($items, $storePickUp, $customer, $checkoutSession);
        $data['reload'] = $splitOrderData['reload'];
        if ($data['reload']) {
            return $data;
        }
        $data['error']  = $splitOrderData['error'];
        $data['split']  = $this->orderIsSplit;
        $message = 'SM\Checkout\Model\MultiShippingHandle. Thoi gian tap quote address - quoteID ' . $checkoutSession->getQuote()->getId() . ': ';
        $dateStart = microtime(true); // log_time
        $this->reBuildQuoteAddress($splitOrderData['data'], $checkoutSession);
        $dateEnd = microtime(true); // log_time
        $this->writeTimeLog($dateEnd, $dateStart, $message);
        $addressShippingMethod = [];
        $itemsValidMethod    = [];
        $showEachItems = false;
        $shippingListFromQuoteAddress = [];
        $validMethodCodeWithQuoteAddress = [];
        $i = 0;
        foreach ($checkoutSession->getQuote()->getAllShippingAddresses() as $_address) {
            $preShippingMethod = $_address->getPreShippingMethod();
            if ($preShippingMethod == self::NOT_SHIP || $preShippingMethod == self::NOT_AVAILABLE) {
                $preShippingMethod = '';
            }
            $addressShippingMethod[$_address->getId()] = $preShippingMethod;
            if ($preShippingMethod != self::STORE_PICK_UP) {
                $i++;
                $_shippingRateGroups                       = $_address->getGroupedAllShippingRates();
                $shippingMethodList                        = [];
                $shippingMethodListFake                    = [];
                $validMethodList = [];
                if ($_shippingRateGroups) {
                    foreach ($_shippingRateGroups as $code => $_rates) {
                        if ($code == 'transshipping') {
                            foreach ($_rates as $_rate) {
                                $shippingMethodList[] = $_rate->getCode();
                                if ($_rate->getCode() == self::DC) {
                                    $rateCode = self::DEFAULT_METHOD;
                                } elseif ($_rate->getCode() == self::TRANS_COURIER) {
                                    $rateCode = self::SAME_DAY;
                                } else {
                                    $rateCode = $_rate->getCode();
                                }
                                if ($this->mobile) {
                                    $validMethodList[$rateCode] = $rateCode;
                                } else {
                                    $validMethodList[$rateCode] = $this->methodInterfaceFactory->create()->setMethodCode($rateCode);
                                }
                                if (!in_array($rateCode, $shippingMethodListFake)) {
                                    $shippingMethodListFake[] = $rateCode;
                                }
                            }
                        }
                    }
                }
                if ($i == 1) {
                    $validMethodCodeWithQuoteAddress = $shippingMethodListFake;
                } else {
                    if (!empty(array_diff($validMethodCodeWithQuoteAddress, $shippingMethodListFake)) || !empty(array_diff($shippingMethodListFake, $validMethodCodeWithQuoteAddress))) {
                        $showEachItems = true;
                    }
                }

                asort($validMethodList);
                $validMethodList = array_values($validMethodList);
                asort($shippingMethodList);
                $shippingMethodList = array_values($shippingMethodList);
                if (!in_array($preShippingMethod, $shippingMethodList) && !empty($shippingMethodList)) {
                    if ($shippingMethodList[0] == self::DEFAULT_METHOD && in_array(self::DC, $shippingMethodList)) {
                        $preShippingMethod = self::DC;
                    } elseif ($shippingMethodList[0] == self::SAME_DAY && in_array(self::TRANS_COURIER, $shippingMethodList)) {
                        $preShippingMethod = self::TRANS_COURIER;
                    } else {
                        $preShippingMethod = $shippingMethodList[0];
                    }
                    $addressShippingMethod[$_address->getId()] = $preShippingMethod;
                } elseif (empty($shippingMethodList)) {
                    $preShippingMethod = '';
                    $addressShippingMethod[$_address->getId()] = $preShippingMethod;
                }
                if (empty($shippingMethodListFake)) {
                    $shippingMethodListFake[] = self::NOT_AVAILABLE;
                    if ($this->mobile) {
                        $validMethodList[] = self::NOT_AVAILABLE;
                    } else {
                        $validMethodList[] = $this->methodInterfaceFactory->create()->setMethodCode(self::NOT_AVAILABLE);
                    }
                }
                foreach ($_address->getAllVisibleItems() as $item) {
                    if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                        $quoteItemId = $item->getQuoteItemId();
                    } else {
                        $quoteItemId = $item->getId();
                    }
                    if ($this->mobile) {
                        $itemsValidMethod[$quoteItemId] = $validMethodList;
                    } else {
                        $itemsValidMethod[$quoteItemId] = $this->itemInterfaceFactory->create()->setItemId($quoteItemId)->setValidMethod($validMethodList);
                    }
                    if (!in_array($preShippingMethod, $shippingMethodList)) {
                        $data['error'] = true;
                        $preShippingMethod = self::NOT_AVAILABLE;
                    }

                    if (!in_array($preShippingMethod, $shippingListFromQuoteAddress)) {
                        $shippingListFromQuoteAddress[] = $preShippingMethod;
                    }
                }
            } else {
                if ($this->mobile) {
                    foreach ($_address->getAllVisibleItems() as $item) {
                        if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                            $quoteItemId = $item->getQuoteItemId();
                        } else {
                            $quoteItemId = $item->getId();
                        }
                        if (isset($this->mobileItemsFormat[$quoteItemId])) {
                            foreach ($this->mobileItemsFormat[$quoteItemId]->getShippingMethod() as $method) {
                                if (!$method->getDisabled()) {
                                    $itemsValidMethod[$quoteItemId][] = $method->getValue();
                                }
                            }
                        } else {
                            $itemsValidMethod[$quoteItemId] = [
                                self::DEFAULT_METHOD,
                                self::SAME_DAY,
                                self::SCHEDULE,
                                self::NEXT_DAY
                            ];
                        }
                    }
                }
            }
        }
        if (count($shippingListFromQuoteAddress) > 1) {
            $showEachItems = true;
        }
        $data['show_each_items'] = $showEachItems;
        $data['data'] = $itemsValidMethod;
        $data['addressEachItems']    = $this->addressEachItems;
        if (!empty($this->outStockByOar) || !empty($this->outStockByMagento)) {
            $data['out_stock'] = true;
        }
        if (!empty($this->lowStockByOar) || !empty($this->lowStockByMagento)) {
            $data['low_stock'] = true;
        }
        foreach ($checkoutSession->getQuote()->getAllItems() as $item) {
            $item->unsetData('product_option');
        }
        $billing = $checkoutSession->getCustomer()->getDefaultBilling();
        $checkoutSession->setQuoteCustomerBillingAddress($billing);
        $message = 'SM\Checkout\Model\MultiShippingHandle. Thoi gian set shipping method - quoteID ' . $checkoutSession->getQuote()->getId() . ': ';
        $dateStart = microtime(true); // log_time
        $checkoutSession->setShippingMethods($addressShippingMethod);
        $dateEnd = microtime(true); // log_time
        $this->writeTimeLog($dateEnd, $dateStart, $message);
        $data['mobile-items-format'] = $this->mobileItemsFormat;
        $data['child-items'] = $this->child;
        $data['default-shipping-address'] = $billing;
        return $data;
    }

    /**
     * @param $additionalInfo
     * @return array[]
     */
    public function storePickUpFormat($additionalInfo)
    {
        $storePickUp = $additionalInfo->getStorePickUp();
        return [
            "store_pick_up" => [
                "store_code" => ($storePickUp->getStore()) ? $storePickUp->getStore()->getSourceCode() : "",
                "date" => $storePickUp->getDate(),
                "time" => $storePickUp->getTime(),
            ],
        ];
    }

    /**
     * @param $items
     * @return array[]
     */
    public function itemsFormat($items)
    {
        $itemsFormat  = [];
        $requestItems = [];
        $addressSelect = [];
        foreach ($items as $item) {
            $requestItems[$item->getItemId()] = $item;
            if ($item->getDisable()) {
                continue;
            }
            $format['shipping_method']  = $item->getShippingMethodSelected();
            $format['shipping_address'] = ($format['shipping_method'] == self::STORE_PICK_UP) ? 0 : $item->getShippingAddressId();
            $format['qty']              = $item->getQty();
            $additionalInfo             = $item->getAdditionalInfo()->getDelivery();
            $format['delivery']         = [
                'date' => $additionalInfo->getDate(),
                'time' => $additionalInfo->getTime(),
            ];
            $itemsFormat[$item->getItemId()] = $format;
            if ($format['shipping_address'] != 0 && !in_array($format['shipping_address'], $addressSelect)) {
                $addressSelect[] = $item->getShippingAddressId();
            }
        }
        return ['item_format' => $itemsFormat, 'item_request' => $requestItems, 'shipping_list' => $addressSelect];
    }

    /**
     * @param $items
     * @param $storePickUp
     * @param $customer
     * @param $checkoutSession
     * @return array
     */
    protected function getSplitOrderData($items, $storePickUp, $customer, $checkoutSession)
    {
        $message = 'SM\Checkout\Model\MultiShippingHandle. Thoi gian xu ly check out stock cho quoteID ' . $checkoutSession->getQuote()->getId() . ': ';
        $dateStart = microtime(true); // log_time
        $data                   = ['reload' => false, 'error' => false, 'data' => [], 'split' => false];
        $defaultShippingAddress = $customer->getDefaultShippingAddress()->getId();
        $quoteItemIdSku         = [];
        $order                  = [];
        $addressIds             = [];
        $quote                  = $checkoutSession->getQuote();
        $quoteId = $quote->getId();
        /** @var \Magento\Quote\Model\Quote\Item[] $allItems */
        $allItems         = $quote->getAllItems();
        $storePickupOrder = [];
        $listMethod       = $this->split->getListMethodName();
        $currentParentItems = [];
        foreach ($allItems as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $this->child[$item->getParentItemId()][] = $item;
                $allChildItemsId[$item->getParentItemId()][] = $item->getId();
            } else {
                $currentParentItems[$item->getId()] = $item->getId();
                $product = $item->getProduct();
                $isInOfStock = $this->updateStockItem->isOutStock($product->getId());
                if (!$isInOfStock) {
                    $this->outStockByMagento[] = $item->getId();
                } else {
                    switch ($product->getTypeId()) {
                        case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                            $maxQty = $this->updateStockItem->getConfigItemStock($item);
                            break;
                        case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                            $maxQty = $this->updateStockItem->getBundleItemStock($item);
                            break;
                        default:
                            $maxQty = $this->updateStockItem->getItemStock($product->getId());
                    }

                    if ($item->getQty() > $maxQty) {
                        $this->lowStockByMagento[$item->getId()] = $maxQty;
                    }
                }
            }
            $quoteItemIdSku[$item->getId()] = $item;
        }
        $dateEnd = microtime(true); // log_time
        $this->writeTimeLog($dateEnd, $dateStart, $message);
        foreach ($items as $item) {
            $quoteItemId = $item->getItemId();
            $shippingMethodSelected = $item->getShippingMethodSelected();
            $addressIdSelected = $item->getShippingAddressId();
            if ($this->mobile) {
                $this->mobileItemsFormat[$quoteItemId] = $item;
            }
            if (isset($currentParentItems[$quoteItemId])) {
                unset($currentParentItems[$quoteItemId]);
            }
            if (!isset($quoteItemIdSku[$quoteItemId]) || in_array($quoteItemId, $this->outStockByMagento)) {
                continue;
            }
            if ($shippingMethodSelected == self::STORE_PICK_UP) {
                if (!in_array($quoteItemId, $this->outStockByMagento)) {
                    if ($this->mobile) {
                        $storeCode = ($storePickUp->getStore()) ? $storePickUp->getStore()->getSourceCode() : "";
                    } else {
                        $storeCode = ($storePickUp->getStore()) ? $storePickUp->getStore() : "";
                    }
                    $storePickupOrder[][$quoteItemId] = [
                        'qty' => (isset($lowStockByMagento[$quoteItemId])) ? $lowStockByMagento[$quoteItemId] : $quoteItemIdSku[$quoteItemId]->getQty(),
                        'address' => $defaultShippingAddress,
                        'shipping_method' => $shippingMethodSelected,
                        'store_pickup' => $storeCode,
                        'split_store_code' => 0,
                    ];
                }
            } else {
                if (!in_array($addressIdSelected, $addressIds)) {
                    $addressIds[] = $addressIdSelected;
                }
                if (isset($order[$addressIdSelected])) {
                    if (isset($order[$addressIdSelected][$shippingMethodSelected])) {
                        $order[$addressIdSelected][$shippingMethodSelected][$quoteItemId] = (isset($this->lowStockByMagento[$quoteItemId])) ? $this->lowStockByMagento[$quoteItemId] : $quoteItemIdSku[$quoteItemId]->getQty();
                    } else {
                        $order[$addressIdSelected][$shippingMethodSelected] = [
                            $quoteItemId => (isset($this->lowStockByMagento[$quoteItemId])) ? $this->lowStockByMagento[$quoteItemId] : $quoteItemIdSku[$quoteItemId]->getQty(),
                        ];
                    }
                } else {
                    $order[$addressIdSelected] = [
                        $shippingMethodSelected => [
                            $quoteItemId => (isset($this->lowStockByMagento[$quoteItemId])) ? $this->lowStockByMagento[$quoteItemId] : $quoteItemIdSku[$quoteItemId]->getQty(),
                        ],
                    ];
                }
                $serviceType = str_replace("transshipping_transshipping", "", $shippingMethodSelected);
                if (!isset($listMethod[$serviceType]) && $serviceType != self::NOT_SHIP && $serviceType != 0) {
                    $data['error'] = true;
                }
                $this->inputItemsFormatAfterHandle[$quoteItemId] = [
                    'shipping_method' => $shippingMethodSelected,
                    'shipping_address' => $addressIdSelected,
                    'qty' => (isset($this->lowStockByMagento[$quoteItemId])) ? $this->lowStockByMagento[$quoteItemId] : $quoteItemIdSku[$quoteItemId]->getQty(),
                    'child' => [],
                    'sku' => $quoteItemIdSku[$quoteItemId]->getSku()
                ];
                if (isset($this->child[$quoteItemId])) {
                    $this->inputItemsFormatAfterHandle[$quoteItemId]['child']   = $this->child[$quoteItemId];
                }
            }
        }

        if (!empty($currentParentItems)) {
            return $data;
        }
        $dataSendToOar = [];
        if (!empty($addressIds)) {
            $addressCollection = $this->addressCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('entity_id', ['in' => $addressIds]);
            $addressData       = $this->split->getAddressData($addressCollection);
            $postCode = [];
            if ($this->helperConfig->isActiveFulfillmentStore()) {
                foreach ($addressData as $addressId => $address) {
                    $postCode[$addressId] = $address['postcode'];
                }
                $enableShipping = $this->split->checkShippingPostCodeList($postCode);
            }
            //$customerId        = $customer->getId();
            $merchantCode      = $this->getMerchantCode();
            $i = 1;
            foreach ($order as $addressId => $rateItem) {
                $addressPostCode = '';
                if ($this->helperConfig->isActiveFulfillmentStore() && isset($postCode[$addressId])) {
                    $addressPostCode = $postCode[$addressId];
                }
                foreach ($rateItem as $serviceType => $item) {
                    $totalWeight                        = 0;
                    $totalQty                           = 0;
                    $totalPrice                         = 0;
                    $orderToSendOar                     = [];
                    if ((bool)$this->helperConfig->isEnableOarLog()) {
                        $orderToSendOar['quote_address_id'] = $quoteId;
                    }
                    //$orderToSendOar['order_id']         = $customerId;
                    $orderToSendOar['merchant_code']    = $merchantCode;
                    $orderToSendOar['destination']      = $addressData[$addressId];
                    foreach ($item as $quoteItemId => $qty) {
                        if ($this->helperConfig->isActiveFulfillmentStore()) {
                            if (!in_array($addressPostCode, $enableShipping)) {
                                $storePickupOrder[][$quoteItemId] = [
                                    'qty' => $qty,
                                    'address' => $addressId,
                                    'shipping_method' => self::NOT_SHIP,
                                    'store_pickup' => '',
                                    'split_store_code' => 0,
                                ];
                                continue;
                            }
                        }
                        $itemData    = $quoteItemIdSku[$quoteItemId];
                        $rowTotal    = (int) $itemData->getRowTotal();
                        $totalPrice += $rowTotal;
                        if (isset($this->child[$quoteItemId])) {
                            // parent quote item id
                            foreach ($this->child[$quoteItemId] as $itemData) {
                                $product    = $itemData->getProduct();
                                $ownCourier = (bool)$product->getData('own_courier');
                                $sku      = $itemData->getSku();
                                $childQty = (int) $qty * (int) $itemData->getQty();
                                $price    = ((int) $itemData->getPrice() != 0) ? (int) $itemData->getPrice() : (int) $product->getFinalPrice();
                                if (isset($orderToSendOar['items'][$sku])) {
                                    $orderToSendOar['items'][$sku]['quantity'] += $childQty;
                                } else {
                                    $orderToSendOar['items'][$sku] = [
                                        'sku' => $sku,
                                        'sku_basic' => $sku,
                                        'quantity' => $childQty,
                                        'price' => $price,
                                        'weight' => (int) $itemData->getWeight(),
                                        'is_spo' => false,
                                        'is_own_courier' => $ownCourier,
                                    ];
                                }
                                $totalWeight += $childQty * (int) $itemData->getWeight();
                                $totalQty += $childQty;
                                if ($rowTotal == 0) {
                                    $rowTotal = $price * $childQty;
                                    $totalPrice += $rowTotal;
                                }
                            }
                        } else {
                            $product    = $itemData->getProduct();
                            $ownCourier = (bool)$product->getData('own_courier');
                            // sing quote item id
                            $sku   = $itemData->getSku();
                            $price = ((int) $itemData->getPrice() != 0) ? (int) $itemData->getPrice() : (int) $product->getFinalPrice();
                            if (isset($orderToSendOar['items'][$sku])) {
                                $orderToSendOar['items'][$sku]['quantity'] += (int) $qty;
                            } else {
                                $orderToSendOar['items'][$sku] = [
                                    'sku' => $sku,
                                    'sku_basic' => $sku,
                                    'quantity' => (int) $qty,
                                    'price' => $price,
                                    'weight' => (int) $itemData->getWeight(),
                                    'is_spo' => false,
                                    'is_own_courier' => $ownCourier,
                                ];
                            }

                            $totalWeight += (int) $itemData->getWeight();
                            $totalQty += (int) $qty;
                            if ($rowTotal == 0) {
                                $rowTotal = $price * (int) $qty;
                                $totalPrice += $rowTotal;
                            }
                        }
                    }
                    if ($this->helperConfig->isActiveFulfillmentStore()) {
                        if (!in_array($addressPostCode, $enableShipping)) {
                            continue;
                        }
                    }
                    $orderToSendOar["total_weight"] = (int) $totalWeight;
                    $orderToSendOar["total_price"]  = (int) $totalPrice;
                    $orderToSendOar["total_qty"]    = (int) $totalQty;
                    if ($orderToSendOar['destination']['district'] != ''
                        && $orderToSendOar['destination']['latitude'] != 0
                        && $orderToSendOar['destination']['longitude'] != 0
                    ) {
                        $orderToSendOar['items']           = array_values($orderToSendOar['items']);
                        $orderToSendOar['order_id'] = (string)$i;
                        $dataSendToOar[] = $orderToSendOar;
                        $i++;
                    }
                }
            }
            $message = 'SM\Checkout\Model\MultiShippingHandle. Thoi gian xu ly lay data send to OAR de split order - quoteID ' . $checkoutSession->getQuote()->getId() . ': ';
            $dateEnd = microtime(true); // log_time
            $this->writeTimeLog($dateEnd, $dateStart, $message);

            $message = 'SM\Checkout\Model\MultiShippingHandle. Thoi gian OAR tra ve de split order - quoteID ' . $checkoutSession->getQuote()->getId() . ': ';
            $dateStart = microtime(true); // log_time
            $splitOrder = $this->split->getOarResponse($dataSendToOar);
            $dateEnd = microtime(true); // log_time
            $this->writeTimeLog($dateEnd, $dateStart, $message);

            if (is_array($splitOrder)
                && !isset($splitOrder['error'])
                && isset($splitOrder['content'])
            ) {
                $data['data'] = $this->convertOarOrder($splitOrder);
            }
        }
        $data['data'] = array_merge($data['data'], $storePickupOrder);
        return $data;
    }

    /**
     * @param $splitOrder
     * @return array
     */
    public function convertOarOrder($splitOrder)
    {
        $ship                = [];
        $splitOrderWithSku   = [];
        $addressIdList       = [];
        $oarOrder = [];
        foreach ($splitOrder['content'] as $contentData) {
            if (!$contentData['status']) {
                continue;
            }
            $orderDetail = $contentData['data'];
            if (!isset($orderDetail['store']) || !isset($orderDetail['store']['store_code'])) {
                continue;
            }
            if (!isset($oarOrder[$orderDetail['order_id_origin']])) {
                $oarOrder[$orderDetail['order_id_origin']] = 1;
            } else {
                $this->orderIsSplit = true;
            }

            $storeCode           = $orderDetail['store']['store_code'];
            $orderItems          = $orderDetail['items'];
            $orderShippingMethod = $this->getShippingListFromOrderSplit($orderDetail['shipping_list']);
            asort($orderShippingMethod);
            $orderShippingMethod = array_values($orderShippingMethod);
            foreach ($orderItems as $item) {
                if (isset($item['sku']) && isset($item['quantity']) && (int) $item['quantity'] > 0) {
                    if (isset($splitOrderWithSku[$item['sku']])) {
                        $splitOrderWithSku[$item['sku']]['quantity_allocated'] += (int) $item['quantity'];
                    } else {
                        $splitOrderWithSku[$item['sku']] = [
                            'store' => $storeCode,
                            'quantity_allocated' => (int) $item['quantity'],
                            'shipping_list' => $orderShippingMethod,
                            'oar_data' => [
                                'order_id' => $orderDetail['order_id'],
                                'order_id_origin' => $orderDetail['order_id_origin'],
                                'store_code' => $storeCode,
                                'is_spo' => $orderDetail['is_spo'],
                                'is_own_courier' => $orderDetail['is_own_courier'],
                                'warehouse_source' => $orderDetail['warehouse_source'],
                                'warehouse' => $orderDetail['warehouse'],
                                'store_name' => $orderDetail['store']['store_name'],
                                'spo_detail' => $orderDetail['warehouse'],
                                'store' => $orderDetail['store'],
                                'shipping_list' => $orderDetail['shipping_list']
                            ],
                        ];
                    }
                }
            }
        }
        foreach ($this->inputItemsFormatAfterHandle as $quoteItemId => $itemData) {
            if (empty($itemData['child'])) {
                // single
                if (!isset($splitOrderWithSku[$itemData['sku']])) {
                    // out stock from oar
                    $this->outStockByOar[] = $quoteItemId;
                    unset($this->inputItemsFormatAfterHandle[$quoteItemId]);
                } else {
                    $itemFromOAR = $splitOrderWithSku[$itemData['sku']];
                    $qty         = 0;
                    if ((int) $itemData['qty'] <= (int) $itemFromOAR['quantity_allocated']) {
                        $qty = (int) $itemData['qty'];
                    } elseif ((int) $itemFromOAR['quantity_allocated'] > 0) {
                        $this->lowStockByOar[] = $quoteItemId;
                        $qty = (int) $itemFromOAR['quantity_allocated'];
                    }
                    if ($qty > 0) {
                        $shippingList = $itemFromOAR['shipping_list'];
                        if (!in_array($itemData['shipping_method'], $shippingList) && !empty($shippingList)) {
                            $itemData['shipping_method'] = $shippingList[0];
                        }
                        if ($this->countArray($shippingList) == 1 && in_array(self::DC, $shippingList)) {
                            // is_spo
                            $this->hasSpo = true;
                            if ($itemData['shipping_method'] == self::DEFAULT_METHOD) {
                                $itemData['shipping_method'] = self::DC;
                            }
                        } elseif ($this->countArray($shippingList) <= 2 && in_array(self::TRANS_COURIER, $shippingList)) {
                            // own_courier
                            $this->hasFresh = true;
                            if ($itemData['shipping_method'] == self::SAME_DAY) {
                                $itemData['shipping_method'] = self::TRANS_COURIER;
                            }
                        } elseif ($this->countArray($shippingList) == 1 && in_array(self::SAME_DAY, $shippingList)) {
                            $this->hasFresh = true;
                        } else {
                            $this->hasNormal = true;
                        }

                        $itemAddToQuoteAddress = [
                            'qty' => $qty,
                            'address' => $itemData['shipping_address'],
                            'shipping_method' => $itemData['shipping_method'],
                            'split_store_code' => $itemFromOAR['store'],
                            'oar_data' => $itemFromOAR['oar_data'],
                        ];
                        if (!in_array($itemData['shipping_address'], $addressIdList)) {
                            $addressIdList[] = $itemData['shipping_address'];
                        }
                        $this->inputItemsFormatAfterHandle[$quoteItemId]['shipping_method'] = $itemAddToQuoteAddress['shipping_method'];
                        $ship[][$quoteItemId]                               = $itemAddToQuoteAddress;
                        $itemsResponseOar[$quoteItemId]                     = $itemAddToQuoteAddress;
                        $splitOrderWithSku                                  = $this->rePareQtyFromOAR([$itemData['sku'] => $qty], $splitOrderWithSku);
                    }
                }
            } else {
                //parent
                $itemAddToQuoteAddress = [
                    'qty' => (int) $itemData['qty'],
                    'address' => $itemData['shipping_address'],
                    'shipping_method' => $itemData['shipping_method'],
                    'split_store_code' => 0,
                ];
                if (!in_array($itemData['shipping_address'], $addressIdList)) {
                    $addressIdList[] = $itemData['shipping_address'];
                }
                $useQtyFromOar   = [];
                $outStockFromOar = false;
                foreach ($itemData['child'] as $childItem) {
                    if (!isset($splitOrderWithSku[$childItem->getSku()])
                        || ($itemAddToQuoteAddress['split_store_code'] != 0 && $itemAddToQuoteAddress['split_store_code'] != $splitOrderWithSku[$childItem->getSku()]['store'])
                    ) {
                        // out stock from oar
                        $this->outStockByOar[] = $quoteItemId;
                        $outStockFromOar = true;
                        break;
                    } else {
                        $childSku     = $childItem->getSku();
                        $itemFromOAR  = $splitOrderWithSku[$childSku];
                        $shippingList = $itemFromOAR['shipping_list'];
                        if (!in_array($itemAddToQuoteAddress['shipping_method'], $shippingList) && !empty($shippingList)) {
                            $itemAddToQuoteAddress['shipping_method'] = $shippingList[0];
                        }
                        if ($this->countArray($shippingList) == 1 && in_array(self::DC, $shippingList)) {
                            // is_spo
                            $this->hasSpo = true;
                            if ($itemAddToQuoteAddress['shipping_method'] == self::DEFAULT_METHOD) {
                                $itemAddToQuoteAddress['shipping_method'] = self::DC;
                            }
                        } elseif ($this->countArray($shippingList) <= 2 && in_array(self::TRANS_COURIER, $shippingList)) {
                            // own_courier
                            $this->hasFresh = true;
                            if ($itemAddToQuoteAddress['shipping_method'] == self::SAME_DAY) {
                                $itemAddToQuoteAddress['shipping_method'] = self::TRANS_COURIER;
                            }
                        } elseif ($this->countArray($shippingList) == 1 && in_array(self::SAME_DAY, $shippingList)) {
                            $this->hasFresh = true;
                        } else {
                            $this->hasNormal = true;
                        }
                        $childItemTotalQty = (int) $childItem->getQty() * $itemAddToQuoteAddress['qty'];
                        if ($childItemTotalQty <= (int) $itemFromOAR['quantity_allocated']) {
                            $useQtyFromOar[$childSku]                  = $itemData['qty'];
                            $itemAddToQuoteAddress['split_store_code'] = $itemFromOAR['store'];
                            $itemAddToQuoteAddress['oar_data']         = $itemFromOAR['oar_data'];
                        } elseif ((int) $itemFromOAR['quantity_allocated'] > 0) {
                            $qty = (int) $itemFromOAR['quantity_allocated'] / (int) $childItem->getQty();
                            $qty = round($qty);
                            if ($qty == 0) {
                                // out stock from oar
                                $this->outStockByOar[] = $quoteItemId;
                                $outStockFromOar = true;
                                break;
                            } else {
                                $this->lowStockByOar[] = $quoteItemId;
                                $itemAddToQuoteAddress['qty']              = $qty;
                                $itemAddToQuoteAddress['split_store_code'] = $itemFromOAR['store'];
                                $itemAddToQuoteAddress['oar_data']         = $itemFromOAR['oar_data'];
                            }
                        } else {
                            // out stock from oar
                            $this->outStockByOar[] = $quoteItemId;
                            $outStockFromOar = true;
                            break;
                        }
                    }
                }
                if (!$outStockFromOar) {
                    $splitOrderWithSku                                  = $this->rePareQtyFromOAR($useQtyFromOar, $splitOrderWithSku);
                    $itemsResponseOar[$quoteItemId]                     = $itemAddToQuoteAddress;
                    $ship[][$quoteItemId]                               = $itemAddToQuoteAddress;
                    $this->inputItemsFormatAfterHandle[$quoteItemId]['shipping_method'] = $itemAddToQuoteAddress['shipping_method'];
                } else {
                    unset($this->inputItemsFormatAfterHandle[$quoteItemId]);
                }
            }
        }
        if (count($addressIdList) == 1
            && (($this->hasNormal && $this->hasFresh) || ($this->hasNormal && $this->hasSpo) || ($this->hasFresh && $this->hasSpo))
        ) {
            $this->addressEachItems = true;
        }
        return $ship;
    }

    /**
     * @param $useQtyFromOar
     * @param $splitOrderWithSku
     * @return mixed
     */
    protected function rePareQtyFromOAR($useQtyFromOar, $splitOrderWithSku)
    {
        foreach ($useQtyFromOar as $sku => $qty) {
            $splitOrderWithSku[$sku]['quantity_allocated'] = $splitOrderWithSku[$sku]['quantity_allocated'] - $qty;
        }
        return $splitOrderWithSku;
    }

    /**
     * @param $items
     * @param $itemsResponseOar
     */
    protected function checkoutOarStock($items, $itemsResponseOar)
    {
        foreach ($items as $itemId => $item) {
            if ($item['shipping_method'] == self::STORE_PICK_UP) {
                continue;
            }
            if (!isset($itemsResponseOar[$itemId])) {
                $this->reloadMessage['out_stock'][] = $itemId;
                $this->reload                       = true;
                return;
            } elseif ($item['qty'] != $itemsResponseOar[$itemId]['qty']) {
                $this->reloadMessage['low_stock'][] = $itemId;
                $this->reload                       = true;
            }
        }
    }

    /**
     * @param $array
     * @return int|void
     */
    protected function getCountArray($array)
    {
        return count($array);
    }

    /**
     * @param $orderShippingMethod
     * @return array
     */
    protected function getShippingListFromOrderSplit($orderShippingMethod)
    {
        $methodList = [];
        foreach ($orderShippingMethod as $method) {
            if (
                isset($method['courier']['reason']) && $method['courier']['fee']['base'] == 0
            ) {
                continue;
            }
            $methodList[$method['service_type']] = 'transshipping_transshipping' . $method['service_type'];
        }
        return $methodList;
    }

    /**
     * @param $checkoutSession
     * @param $ship
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function reBuildQuoteAddress($ship, $checkoutSession)
    {
        $checkoutSession->setCollectRatesFlag(true);
        $checkoutSession->setShippingItemsInformation($ship);
    }

    /**
     * @return string
     */
    protected function getMerchantCode()
    {
        return $this->split->getMerchantCode();
    }

    /**
     * @param $deliveryDateTime
     * @param $storeDateTime
     * @param $quote
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function handlePreviewOrder($deliveryDateTime, $storeDateTime, $quote)
    {
        $reload          = false;
        $shippingAddress = $quote->getAllShippingAddresses();
        foreach ($shippingAddress as $address) {
            $date           = $time           = null;
            $shippingMethod = $address->getShippingMethod();
            if (!$shippingMethod || $shippingMethod == '') {
                $reload = true;
                break;
            } elseif ($shippingMethod == self::STORE_PICK_UP) {
                $date = $this->timezone->convertConfigTimeToUtc($storeDateTime['date']);
                $time = $storeDateTime['time'];
                $store = $storeDateTime['store_code'];
                $this->saveStorePickUpDateTime($address, $date, $time, $store);
            } else {
                if ($shippingMethod == self::SCHEDULE) {
                    $dateTime = $deliveryDateTime[$address->getCustomerAddressId()];
                    $date     = $this->timezone->convertConfigTimeToUtc($dateTime['date']);
                    $time     = $dateTime['time'];
                    $this->saveScheduleDateTime($address, $date, $time);
                }
            }
        }
        return $reload;
    }

    /**
     * @param $address
     * @param $date
     * @param $time
     */
    protected function saveScheduleDateTime($address, $date, $time)
    {
        $address->setDate($date)->setTime($time)->save();
    }

    /**
     * @param $address
     * @param $date
     * @param $time
     * @param $store
     */
    protected function saveStorePickUpDateTime($address, $date, $time, $store)
    {
        $address->setStorePickUpTime($date)->setStorePickUpDelivery($time)->setStorePickUp($store)->save();
    }

    /**
     * @param $product
     * @return mixed
     */
    protected function getProductOptions($product)
    {
        return $product->getTypeInstance(true)->getOrderOptions($product);
    }

    /**
     * @param $quote
     * @param bool $web
     * @param false $notSpoList
     * @param array $invalidShippingList
     * @param array $items
     * @param array $childItems
     * @param false $defaultShipping
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    public function getPreviewOrderData($quote, $web = true, $notSpoList = false, $invalidShippingList = [], $items = [], $childItems = [], $defaultShipping = false)
    {
        $this->initItems = $items;
        $quoteItemData = [];
        $previewOrderData       = [];
        $shippingAddress        = $quote->getAllShippingAddresses();
        if (!$web && !empty($invalidShippingList)) {
            $weightUnit = $this->helperConfig->getWeightUnit();
            $currencySymbol = trim($this->helperConfig->getCurrencySymbol());
            $storeId = $quote->getStoreId();
            $voucher = $quote->getApplyVoucher();
        }
        if (!$web && !$notSpoList) {
            $orderToSendOar['order_id'] = $quote->getCustomerId();
            $orderToSendOar['merchant_code'] = $this->split->getMerchantCode();
            try {
                $regionId = $defaultShipping->getRegionId();
                $province = $this->split->getProvince($regionId);
                $district = $defaultShipping->getCustomAttribute('district') ? $defaultShipping->getCustomAttribute('district')->getValue() : '';
                $district = $this->split->getDistrictName($district);
                $lat = $defaultShipping->getCustomAttribute('latitude') ? $defaultShipping->getCustomAttribute('latitude')->getValue() : 0;
                $long = $defaultShipping->getCustomAttribute('longitude') ? $defaultShipping->getCustomAttribute('longitude')->getValue() : 0;
                $city = $this->split->getCityName($defaultShipping->getCity());
                $orderToSendOar['destination'] = [
                    "address" => $defaultShipping->getStreetFull(),
                    "province" => $province,
                    "city" => $city,
                    "district" => $district,
                    "postcode" => $defaultShipping->getPostcode(),
                    "latitude" => (float)$lat,
                    "longitude" => (float)$long
                ];
                $notSpoList = $this->getSkuListForPickUp($quote, $orderToSendOar, true);
            } catch (\Exception $e) {
                $notSpoList = [];
            }
        } else {
            $notSpoList = [];
        }

        foreach ($shippingAddress as $address) {
            $date           = $time           = "";
            $previewOrder   = $this->previewOrderInterfaceFactory->create();
            $shippingMethod = $address->getShippingMethod();
            $previewOrder->setShippingFeeNotDiscount(0);
            if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                $title               = __('Pick Up in Store');
                $shippingMethodTitle = __("Store Pick Up");
                $addressId           = 0;
                $date                = $address->getStorePickUpTime();
                $time                = $address->getStorePickUpDelivery();
            } else {
                $title                     = __('Delivery Address');
                $listMethod                = $this->split->getListMethodName();
                if ($shippingMethod == self::TRANS_COURIER) {
                    $shippingMethod = self::SAME_DAY;
                } elseif ($shippingMethod == self::DC) {
                    $shippingMethod = self::DEFAULT_METHOD;
                } elseif ($shippingMethod == self::SCHEDULE) {
                    $date = $address->getDate();
                    $time = $address->getTime();
                }
                $shippingMethodServiceType = str_replace("transshipping_transshipping", "", $shippingMethod);
                if (isset($listMethod[$shippingMethodServiceType])) {
                    $shippingMethodTitle = $listMethod[$shippingMethodServiceType];
                } else {
                    $shippingMethodTitle = __('Shipping Method not available');
                    $shippingMethod = self::NOT_AVAILABLE;
                }
                $addressId                 = $address->getCustomerAddressId();
                if ($address->getFreeShipping() == 1) {
                    $previewOrder->setShippingFeeNotDiscount($address->getShippingDiscountAmount());
                    $previewOrder->setShippingFee($address->getShippingInclTax());
                } else {
                    $shippingFee = (int) $address->getShippingInclTax() - (int) $address->getShippingDiscountAmount();
                    $previewOrder->setShippingFeeNotDiscount($address->getShippingInclTax());
                    $previewOrder->setShippingFee($shippingFee);
                }
            }
            $previewOrder->setTitle($title);
            $previewOrder->setShippingMethod($shippingMethod);
            $previewOrder->setShippingMethodTitle($shippingMethodTitle);
            $previewOrder->setAddressId($addressId);
            if ($date != '') {
                $date = $this->timezone->formatDate($date);
                $date = date('d M Y', strtotime($date));
            }
            $previewOrder->setDate($date);
            $previewOrder->setTime($time);
            $itemList  = [];
            $itemTotal = 0;
            foreach ($address->getAllVisibleItems() as $item) {
                if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                    $quoteItemId = $item->getQuoteItemId();
                } else {
                    $quoteItemId = $item->getId();
                }
                $itemList[] = $quoteItemId;
                $itemTotal  = $itemTotal + (int) $item->getQty();
                if (!$web && !empty($invalidShippingList)) {
                    $quoteItemData[$quoteItemId] = $this->buildQuoteItemForMobile($notSpoList, $item, $shippingMethod, $invalidShippingList, $weightUnit, $currencySymbol, $storeId, $addressId, $voucher, false, $childItems);
                }
            }
            $previewOrder->setItems($itemList);
            $previewOrder->setItemTotal($itemTotal);
            $previewOrderData[] = $previewOrder;
        }
        return ['preview_order' => $previewOrderData, 'quote_item_data' => $quoteItemData, 'out_stock' => $this->initItems, 'disable_store_pickup' => $this->disablePickUp, 'sku-list' => $this->skuList];
    }

    /**
     * @param $quoteItems
     * @param $items
     * @return array
     */
    public function validateItems($quoteItems, $items)
    {
        $reload         = false;
        $quoteItemsName = [];
        foreach ($quoteItems as $item) {
            if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                $itemId = $item->getQuoteItemId();
            } else {
                $itemId = $item->getId();
            }
            $quoteItemsName[$itemId] = $item->getName();
            $qty                     = (int) $item->getQty();
            if (!isset($items[$itemId]) || $items[$itemId]['qty'] != $qty) {
                $reload = true;
                break;
            } else {
                unset($items[$itemId]);
            }
        }
        if (!empty($items)) {
            $reload = true;
        }
        return ['reload' => $reload, 'quote_items_name' => $quoteItemsName];
    }

    /**
     * @param $allShippingAddress
     * @param $itemsValidMethod
     * @param false $web
     * @return bool
     */
    public function isShowEachItems($allShippingAddress, $itemsValidMethod, $web = false)
    {
        if (count($allShippingAddress) == 1) {
            return false;
        }
        $showEachItems                = false;
        $shippingListFromQuoteAddress = [];
        foreach ($allShippingAddress as $_address) {
            $shippingMethod = $_address->getShippingMethod();
            if (!$shippingMethod) {
                $shippingMethod = 'not_set';
            }
            if ($shippingMethod == self::DC) {
                $shippingMethod = self::DEFAULT_METHOD;
            }
            if ($shippingMethod == self::TRANS_COURIER) {
                $shippingMethod = self::SAME_DAY;
            }
            if (!in_array($shippingMethod, $shippingListFromQuoteAddress)) {
                $shippingListFromQuoteAddress[] = $shippingMethod;
            }
        }
        if (count($shippingListFromQuoteAddress) > 1) {
            $showEachItems = true;
        } else {
            $validMethodCode = 0;
            $i               = 0;
            if ($web) {
                foreach ($itemsValidMethod as $item) {
                    $i++;
                    if ($i == 1) {
                        $validMethodCode = $this->countArray($item->getValidMethod());
                    } else {
                        if ($validMethodCode != $this->countArray($item->getValidMethod())) {
                            $showEachItems = true;
                            break;
                        }
                    }
                }
            } else {
                foreach ($itemsValidMethod as $item) {
                    $i++;
                    $validMethod = 0;
                    foreach ($item->getShippingMethod() as $method) {
                        if ($method->getValue() != self::NOT_AVAILABLE && !$method->getDisabled()) {
                            $validMethod++;
                        }
                    }
                    if ($i == 1) {
                        $validMethodCode = $validMethod;
                    } else {
                        if ($validMethodCode != $validMethod) {
                            $showEachItems = true;
                            break;
                        }
                    }
                }
            }
        }
        return $showEachItems;
    }

    /**
     * @param $array
     * @return int|void
     */
    protected function countArray($array)
    {
        return count($array);
    }

    /**
     * @param $dataHandle
     * @return \Magento\Framework\Phrase|string
     */
    public function handleMessage($dataHandle)
    {
        $message = '';
        if ($dataHandle['out_stock'] && $dataHandle['low_stock']) {
            $message = __('Unfortunately, some products are allocated in limited stock or not allocated in your area. We have adjusted the quantity or removed for you.');
        } elseif ($dataHandle['out_stock']) {
            $message = __('Unfortunately, some products are not allocated to your area. We have removed the products for you.');
        } elseif ($dataHandle['low_stock']) {
            $message = __('Unfortunately, some products are allocated in limited stock in your area. We have adjusted the quantity for you.');
        }
        return $message;
    }

    /**
     * @param $postCode
     * @return bool
     */
    public function checkShippingPostCode($postCode)
    {
        return $this->split->checkShippingPostCode($postCode);
    }

    /**
     * @param $notSpoList
     * @param $quoteItem
     * @param $shippingMethod
     * @param $invalidShippingList
     * @param $weightUnit
     * @param $currencySymbol
     * @param $storeId
     * @param $addressId
     * @param $voucher
     * @param false $init
     * @param array $childItems
     * @return mixed|\SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    public function buildQuoteItemForMobile($notSpoList, $quoteItem, $shippingMethod, $invalidShippingList, $weightUnit, $currencySymbol, $storeId, $addressId, $voucher, $init = false, $childItems = [])
    {
        if ($quoteItem instanceof \Magento\Quote\Model\Quote\Address\Item) {
            $quoteItemId = $quoteItem->getQuoteItemId();
        } else {
            $quoteItemId = $quoteItem->getId();
        }
        if (!$init) {
            if (isset($this->initItems[$quoteItemId])) {
                $product = $quoteItem->getProduct();
                if (!in_array($quoteItemId, $notSpoList)) {
                    $this->disablePickUp = true;
                } else {
                    if (isset($childItems[$quoteItemId])) {
                        $parentQty = $quoteItem->getQty();
                        foreach ($childItems[$quoteItemId] as $child) {
                            $childSku = $child->getProduct()->getSku();
                            $childQty = $parentQty * $child->getQty();
                            if (isset($this->skuList[$childSku])) {
                                $this->skuList[$childSku] += $childQty;
                            } else {
                                $this->skuList[$childSku] = $childQty;
                            }
                        }
                    } else {
                        $sku = $product->getSku();
                        if (isset($this->skuList[$sku])) {
                            $this->skuList[$sku] += $quoteItem->getQty();
                        } else {
                            $this->skuList[$sku] = $quoteItem->getQty();
                        }
                    }
                }
                $shippingMethodList = [];
                $quoteItemModel = $this->initItems[$quoteItemId];
                if (!$shippingMethod || $shippingMethod == '') {
                    $shippingMethod = self::NOT_AVAILABLE;
                }
                if ($shippingMethod == self::STORE_PICK_UP) {
                    $quoteItemModel->setShippingAddressId(0);
                }
                foreach ($this->split->getListMethodFakeName() as $value => $label) {
                    $shippingCode = 'transshipping_transshipping' . $value;
                    $shippingMethodObj = $this->shippingMethodInterfaceFactory->create();
                    $shippingMethodObj->setValue($shippingCode)->setLabel($label)->setDisabled(true);
                    if (in_array($shippingCode, $invalidShippingList[$quoteItemId])) {
                        $shippingMethodObj->setDisabled(false);
                    }
                    $shippingMethodList[] = $shippingMethodObj;
                }
                $quoteItemModel->setShippingMethodSelected($shippingMethod);
                $quoteItemModel->setShippingMethod($shippingMethodList);
                if ($quoteItem->getQty() < $quoteItemModel->getQty()) {
                    $quoteItemModel->setQty($quoteItem->getQty());
                    $quoteItemModel->setMessage(__('Quantity has been adjusted.'));
                }
                unset($this->initItems[$quoteItemId]);
                return $quoteItemModel;
            }
        }
        /** @var \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface $quoteItemModel */
        $quoteItemModel = $this->itemInterfaceMobileFactory->create();
        $product = $quoteItem->getProduct();
        $regularPrice = $this->price->getRegularPrice($product);
        $productType = $product->getTypeId();
        $quoteItemModel->setItemId($quoteItemId);
        $quoteItemModel->setSku($product->getData('sku'));
        $quoteItemModel->setName($quoteItem->getName());
        $quoteItemModel->setProductType($productType);
        $quoteItemModel->setProductOption($this->getFormattedOptionValue($quoteItem));
        $quoteItemModel->setUrl($product->getProductUrl());
        $weight = $quoteItem->getQty() * $product->getWeight();
        $quoteItemModel->setWeight(round($weight, 2));
        $quoteItemModel->setWeightUnit($weightUnit);
        $quoteItemModel->setQty($quoteItem->getQty());
        $quoteItemModel->setThumbnail($this->getImageUrl($product, $storeId));
        $quoteItemModel->setRowTotal($quoteItem->getRowTotal());
        $quoteItemModel->setDisableStorePickUp((!in_array($quoteItemId, $notSpoList))); // add spo
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
        $model->setProductQty($quoteItem->getQty());
        if ($data['salePrice'] && $data['salePrice'] > 0) {
            $model->setProductOnSale(__('Yes'));
        } else {
            $model->setProductOnSale(__('Not on sale'));
        }
        if ($voucher != null && $voucher != '') {
            $model->setApplyVoucher(__('Yes'));
            $model->setVoucherId($voucher);
        } else {
            $model->setApplyVoucher(__('No'));
            $model->setVoucherId('');
        }
        $quoteItemModel->setGtmData($model);
        $quoteItemModel->setBaseRowTotalByLocation($regularPrice * $quoteItem->getQty());
        $quoteItemModel->setCurrencySymbol($currencySymbol);
        $quoteItemModel->setFreshProduct($this->fresh->populateObject($product));
        $quoteItemModel->setShippingAddressId($addressId);
        $quoteItemModel->setShippingMethodSelected($shippingMethod);
        $quoteItemModel->setShippingMethod($this->getFullShippingList());
        $delivery = $this->deliveryInterfaceFactory->create()->setDate('')->setTime('');
        $installationInfo = $this->getInstallationProduct($quoteItem);
        $additionalInfo = $this->itemAdditionalInfoInterfaceFactory->create();
        $additionalInfo->setDelivery($delivery);
        $additionalInfo->setInstallationInfo($installationInfo);
        $quoteItemModel->setAdditionalInfo($additionalInfo);
        $quoteItemModel->setDisable(false);
        $quoteItemModel->setMessage('');
        return $quoteItemModel;
    }

    /**
     * @return array
     */
    protected function getFullShippingList()
    {
        $shippingMethodList = [];
        foreach ($this->split->getListMethodFakeName() as $value => $label) {
            $shippingMethodObj = $this->shippingMethodInterfaceFactory->create();
            $shippingMethodObj->setValue('transshipping_transshipping' . $value)->setLabel($label)->setDisabled(false);
            $shippingMethodList[] = $shippingMethodObj;
        }
        return $shippingMethodList;
    }

    /**
     * @param $product
     * @param $storeId
     * @return string
     */
    protected function getImageUrl($product, $storeId)
    {
        try {
            $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
            $imageUrl = $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
            $this->appEmulation->stopEnvironmentEmulation();
            return $imageUrl;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \SM\Checkout\Api\Data\CartItem\InstallationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getInstallationProduct($item)
    {
        $installationInfo    = $this->installationInterfaceFactory->create();
        $buyRequest          = $item->getOptionByCode('info_buyRequest');
        $allowInstallation   = $item->getProduct()->getData(\SM\Installation\Helper\Data::PRODUCT_ATTRIBUTE);
        $installationTooltip = $this->productInstallation->getTooltipMessage();
        $installationFee     = 0;
        $isInstallationFee   = 0;
        $installationNote    = '';

        if ($allowInstallation == null || $allowInstallation == "") {
            $allowInstallation = false;
        }

        if ($buyRequest) {
            $installationService = json_decode(
                $buyRequest->getValue(),
                true
            )[\SM\Installation\Helper\Data::QUOTE_OPTION_KEY] ?? null;
            if ($installationService) {
                $installationFee = $installationService['installation_fee'] ?? 0;
                $isInstallationFee = $installationService['is_installation'] ?? 0;
                $installationNote = $installationService['installation_note'] ?? '';
            }
        }
        $installationInfo->setAllowInstallation($allowInstallation);
        $installationInfo->setInstallationFee($installationFee);
        $installationInfo->setIsInstallation($isInstallationFee);
        $installationInfo->setInstallationNote($installationNote);
        $installationInfo->setTooltipMessage($installationTooltip);
        return $installationInfo;
    }

    /**
     * @param $item
     * @return array
     */
    protected function getFormattedOptionValue($item)
    {
        $optionsData = [];
        $options = $this->configurationPool->getByProductType($item->getProductType())->getOptions($item);
        foreach ($options as $index => $optionValue) {
            /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
            $helper = $this->configurationPool->getByProductType('default');
            $option = $helper->getFormattedOptionValue($optionValue, []);
            $option = explode('<span>', $option['value']);
            $optionFormat = [];
            foreach ($option as $a) {
                if (strip_tags($a) != '') {
                    $value = strip_tags($a);
                    $value = explode(': ', $value);
                    $value = end($value);
                    $optionFormat[] = $value;
                }
            }
            $optionsData[$index] = $this->productOptionsInterfaceFactory->create()->setLabel($optionValue['label'])->setValue(implode(', ', $optionFormat));
        }
        return $optionsData;
    }

    /**
     * @param $dateEnd
     * @param $dateStart
     * @param $message
     */
    protected function writeTimeLog($dateEnd, $dateStart, $message)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout-log-time.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $timeDiff = round($dateEnd - $dateStart, 4);
        $logger->info($message . $timeDiff . 's');
    }

    /**
     * @param $quote
     * @param $orderToSendOar
     * @param $checkIsSpo
     * @return array
     */
    public function getSkuListForPickUp($quote, $orderToSendOar, $checkIsSpo)
    {
        $allChildItemsId = [];
        $allChildItems = [];
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $allChildItems[$item->getParentItemId()][] = $item;
                $allChildItemsId[$item->getParentItemId()][] = $item->getId();
            }
        }
        $totalWeight = 0;
        $totalQty = 0;
        $totalPrice = 0;
        $orderToSendOar['items'] = [];
        $allItemsSkuList = [];
        $allParentItemsId = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $rowTotal = (int) $item->getRowTotal();
            $totalPrice += $rowTotal;
            if (isset($allChildItems[$item->getItemId()])) {
                foreach ($allChildItems[$item->getItemId()] as $itemData) {
                    $product    = $itemData->getProduct();
                    $ownCourier = (bool)$product->getData('own_courier');
                    $sku      = $itemData->getSku();
                    $childQty = (int) $item->getQty() * (int) $itemData->getQty();
                    $price    = ((int) $itemData->getPrice() != 0) ? (int) $itemData->getPrice() : (int) $product->getFinalPrice();
                    if (isset($orderToSendOar['items'][$sku])) {
                        $orderToSendOar['items'][$sku]['quantity'] += $childQty;
                    } else {
                        $orderToSendOar['items'][$sku] = [
                            'sku' => $sku,
                            'sku_basic' => $sku,
                            'quantity' => $childQty,
                            'price' => $price,
                            'weight' => (int) $itemData->getWeight(),
                            'is_spo' => false,
                            'is_own_courier' => $ownCourier,
                        ];
                    }
                    $totalWeight += $childQty * (int) $itemData->getWeight();
                    $totalQty += $childQty;
                    if ($rowTotal == 0) {
                        $rowTotal = $price * $childQty;
                        $totalPrice += $rowTotal;
                    }
                    $allItemsSkuList[$itemData->getId()] = ['parent_id' => $item->getId(), 'sku' => $sku, 'qty' => (int) $item->getQty() * (int) $itemData->getQty()];
                }
            } else {
                $product    = $item->getProduct();
                $ownCourier = (bool)$product->getData('own_courier');
                // sing quote item id
                $sku   = $item->getSku();
                $price = ((int) $item->getPrice() != 0) ? (int) $item->getPrice() : (int) $product->getFinalPrice();
                if (isset($orderToSendOar['items'][$sku])) {
                    $orderToSendOar['items'][$sku]['quantity'] += (int) $item->getQty();
                } else {
                    $orderToSendOar['items'][$sku] = [
                        'sku' => $sku,
                        'sku_basic' => $sku,
                        'quantity' => (int) $item->getQty(),
                        'price' => $price,
                        'weight' => (int) $item->getWeight(),
                        'is_spo' => false,
                        'is_own_courier' => $ownCourier,
                    ];
                }

                $totalWeight += (int) $item->getWeight();
                $totalQty += (int) $item->getQty();
                if ($rowTotal == 0) {
                    $rowTotal = $price * (int) $item->getQty();
                    $totalPrice += $rowTotal;
                }
                $allItemsSkuList[$item->getId()] = ['parent_id' => $item->getId(), 'sku' => $sku, 'qty' => (int) $item->getQty()];
            }
            $allParentItemsId[$item->getId()] = $item->getId();
        }
        $orderToSendOar['total_weight'] = $totalWeight;
        $orderToSendOar['total_price'] = $totalPrice;
        $orderToSendOar['total_qty'] = $totalQty;
        $notSpoSku = $this->buildOarDataToCheckIsSpo($orderToSendOar);
        if (!$checkIsSpo) {
            $allItemsSkuListCheckSpo = $allItemsSkuList;
            $allParentItemsIsSpo = $allParentItemsId;
            foreach ($allItemsSkuListCheckSpo as $itemId => $itemData) {
                if (!in_array($itemId, $allItemsSkuList)) {
                    continue;
                }
                $itemSku = $itemData['sku'];
                if (!in_array($itemSku, $notSpoSku)) {
                    $parentItemId = $allItemsSkuList[$itemId]['parent_id'];
                    unset($allParentItemsIsSpo[$parentItemId]);
                    if (isset($allChildItemsId[$parentItemId])) {
                        foreach ($allChildItemsId[$parentItemId] as $childItemId) {
                            unset($allItemsSkuList[$childItemId]);
                        }
                    }
                }
            }
            $skuListForPickUp = [];
            foreach ($allItemsSkuList as $item) {
                if (isset($skuListForPickUp[$item['sku']])) {
                    $skuListForPickUp[$item['sku']] += $item['qty'];
                } else {
                    $skuListForPickUp[$item['sku']] = $item['qty'];
                }
            }
            return $skuListForPickUp;
        } else {
            $allItemsSkuListCheckSpo = $allItemsSkuList;
            $allParentItemsIsSpo = $allParentItemsId;
            foreach ($allItemsSkuListCheckSpo as $itemId => $itemData) {
                $itemSku = $itemData['sku'];
                if (!in_array($itemSku, $notSpoSku)) {
                    $parentItemId = $allItemsSkuList[$itemId]['parent_id'];
                    unset($allParentItemsIsSpo[$parentItemId]);
                }
            }
            if (empty($allParentItemsIsSpo)) {
                $this->disablePickUp = false;
                //$this->notFulFillMessage = __('Sorry, pick-up method is not applicable for this order. Shop conveniently with our delivery.');
            } elseif (count($allParentItemsId) != count($allParentItemsIsSpo)) {
                $this->disablePickUp = false;
                //$this->notFulFillMessage = __('Sorry, some items are not available for pick-up. We have more delivery options for you, try them out!');
            }
            return $allParentItemsIsSpo;
        }
    }

    /**
     * @param $orderToSendOar
     * @return array
     */
    public function buildOarDataToCheckIsSpo($orderToSendOar)
    {
        $orderToSendOar['items'] = array_values($orderToSendOar['items']);
        $response = $this->split->getOarResponse([$orderToSendOar]);
        if (!is_array($response) || isset($response['error']) || !isset($response['content'])) {
            return [];
        }
        $spo = [];
        foreach ($response['content'] as $data) {
            if (!isset($data['data'])) {
                continue;
            }
            if (!isset($data['data']['items'])) {
                continue;
            }
            foreach ($data['data']['items'] as $item) {
                if (!$item['is_spo']) {
                    $spo[] = $item['sku'];
                }
            }
        }
        return $spo;
    }
}
