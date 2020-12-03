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

    protected $inputItemsFormat;
    protected $inputItemsFormatAfterHandle;
    protected $outStockByMagento = [];
    protected $lowStockByMagento = [];
    protected $outStockByOar = [];
    protected $lowStockByOar = [];
    protected $mobile = false;
    protected $mobileItemsFormat = [];

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
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory  $addressCollectionFactory
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
        $this->reBuildQuoteAddress($splitOrderData['data'], $checkoutSession);
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
                    $preShippingMethod = $shippingMethodList[0];
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
        $checkoutSession->setShippingMethods($addressShippingMethod);
        $data['mobile-items-format'] = $this->mobileItemsFormat;
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
                "store_code" => ($storePickUp->getStore()) ? $storePickUp->getStore() : "",
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
            $format['shipping_address'] = $item->getShippingAddressId();
            $format['qty']              = $item->getQty();
            $additionalInfo             = $item->getAdditionalInfo()->getDelivery();
            $format['delivery']         = [
                'date' => $additionalInfo->getDate(),
                'time' => $additionalInfo->getTime(),
            ];
            $itemsFormat[$item->getItemId()] = $format;
            if (!in_array($item->getShippingAddressId(), $addressSelect)) {
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
        $child            = [];
        $listMethod       = $this->split->getListMethodName();
        $currentParentItems = [];
        foreach ($allItems as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $child[$item->getParentItemId()][] = $item;
            } else {
                $currentParentItems[$item->getId()] = $item->getId();
                $product = $item->getProduct();
                $isOutOfStock = $this->updateStockItem->isOutStock($product->getId());
                if (!$isOutOfStock) {
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
        foreach ($items as $item) {
            $quoteItemId = $item->getItemId();
            $shippingMethodSelected = $item->getShippingMethodSelected();
            $addressIdSelected = $item->getShippingAddressId();
            if ($this->mobile) {
                if ($item->getDisable()) {
                    continue;
                }
                if ($item->getAdditionalInfo()) {
                    $additionalInfo = $item->getAdditionalInfo()->getDelivery();
                    $this->mobileItemsFormat[$quoteItemId] = [
                        'shipping_method' => $shippingMethodSelected,
                        'shipping_address' => $addressIdSelected,
                        'qty' => $item->getQty(),
                        'delivery' => [
                            'date' => $additionalInfo->getDate(),
                            'time' => $additionalInfo->getTime()
                        ]
                    ];
                } else {
                    $this->mobileItemsFormat[$quoteItemId] = [
                        'shipping_method' => $shippingMethodSelected,
                        'shipping_address' => $addressIdSelected,
                        'qty' => $item->getQty(),
                        'delivery' => [
                            'date' => null,
                            'time' => null
                        ]
                    ];
                }
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
                if (isset($child[$quoteItemId])) {
                    $this->inputItemsFormatAfterHandle[$quoteItemId]['child']   = $child[$quoteItemId];
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
                        if (isset($child[$quoteItemId])) {
                            // parent quote item id
                            foreach ($child[$quoteItemId] as $itemData) {
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
            $splitOrder = $this->split->getOarResponse($dataSendToOar);
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
                        if (!in_array($itemData['shipping_method'], $shippingList) && !empty($shippingList)) {
                            $itemData['shipping_method'] = $shippingList[0];
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
                        if (!in_array($itemAddToQuoteAddress['shipping_method'], $shippingList) && !empty($shippingList)) {
                            $itemAddToQuoteAddress['shipping_method'] = $shippingList[0];
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
     * @throws \Exception
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
                $this->saveStorePickUpDateTime($address, $date, $time);
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
     */
    protected function saveStorePickUpDateTime($address, $date, $time)
    {
        $address->setStorePickUpTime($date)->setStorePickUpDelivery($time)->save();
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
     * @return array[]
     */
    public function getPreviewOrderData($quote, $web = true)
    {
        $previewOrderData       = [];
        $shippingMethodSelected = [];
        $shippingAddress        = $quote->getAllShippingAddresses();
        foreach ($shippingAddress as $address) {
            $shippingMethodSelected[$address->getId()] = [
                'shipping_method' => $address->getShippingMethod(),
                'customer_address_id' => $address->getCustomerAddressId(),
            ];

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
                $shippingMethodServiceType = str_replace("transshipping_transshipping", "", $shippingMethod);
                $shippingMethodTitle       = (isset($listMethod[$shippingMethodServiceType])) ? $listMethod[$shippingMethodServiceType] : __('Shipping Method not available');
                $addressId                 = $address->getCustomerAddressId();
                if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::SCHEDULE) {
                    $date = $address->getDate();
                    $time = $address->getTime();
                }
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
            }
            $previewOrder->setItems($itemList);
            $previewOrder->setItemTotal($itemTotal);
            $previewOrderData[] = $previewOrder;
        }
        return ['preview_order' => $previewOrderData, 'shipping_method_selected' => $shippingMethodSelected];
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
}
