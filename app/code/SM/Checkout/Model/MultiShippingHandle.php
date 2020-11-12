<?php

namespace SM\Checkout\Model;

/**
 * Class MultiShippingHandle
 * @package SM\Checkout\Model
 */
class MultiShippingHandle
{
    const STORE_PICK_UP = 'store_pickup_store_pickup';
    const NOT_AVAILABLE = 'transshipping_transshipping0';
    const DEFAULT_METHOD = 'transshipping_transshipping1';
    const SAME_DAY = 'transshipping_transshipping2';
    const SCHEDULE = 'transshipping_transshipping3';
    const DC = 'transshipping_transshipping5';
    const TRANS_COURIER = 'transshipping_transshipping6';

    protected $hasNormal = false;
    protected $hasSpo = false;
    protected $hasFresh = false;
    protected $itemsUpdate = [];

    protected $addressEachItems = false;

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
     * MultiShippingHandle constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param Split $split
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory
     * @param UpdateStockItem $updateStockItem
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \SM\Checkout\Model\Split $split,
        \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory,
        \SM\Checkout\Model\UpdateStockItem $updateStockItem
    ) {
        $this->timezone = $timezone;
        $this->sourceRepository = $sourceRepository;
        $this->split = $split;
        $this->previewOrderInterfaceFactory = $previewOrderInterfaceFactory;
        $this->updateStockItem = $updateStockItem;
    }

    /**
     * @param $items
     * @param $additionalInfo
     * @param $customer
     * @param $checkoutSession
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function handleData($items, $additionalInfo, $customer, $checkoutSession)
    {
        $this->itemsUpdate = $items;
        $this->updateStockItem->updateStock($checkoutSession->getQuote());
        $data = ['error' => false, 'data' => [], 'split' => false];
        $splitOrderData = $this->getSplitOrderData($items, $additionalInfo, $customer, $checkoutSession);
        $data['error'] = $splitOrderData['error'];
        $data['split'] = $this->orderIsSplit;
        $this->reBuildQuoteAddress($splitOrderData['data'], $checkoutSession);
        if (!empty($this->itemsUpdate)) {
            $items = $this->itemsUpdate;
        }
        $addressShippingMethod = [];
        $itemShippingMethod = [];
        $error = false;
        foreach ($checkoutSession->getQuote()->getAllShippingAddresses() as $_address) {
            $preShippingMethod = $_address->getPreShippingMethod();
            if ($preShippingMethod == self::STORE_PICK_UP) {
                $addressShippingMethod[$_address->getId()] = true;
                continue;
            } else {
                $addressShippingMethod[$_address->getId()] = true;
                $_shippingRateGroups = $_address->getGroupedAllShippingRates();
                $shippingMethodList = [];
                $shippingMethodListFake = [];
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
                                if (!in_array($rateCode, $shippingMethodListFake)) {
                                    $shippingMethodListFake[] = $rateCode;
                                }
                            }
                        }
                    }
                }
                sort($shippingMethodListFake);
                foreach ($_address->getAllVisibleItems() as $item) {
                    if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                        $quoteItemId = $item->getQuoteItemId();
                    } else {
                        $quoteItemId = $item->getId();
                    }
                    if (empty($shippingMethodListFake)) {
                        $shippingMethodListFake[] = 'transshipping_transshipping0';
                    }
                    $itemShippingMethod[$quoteItemId] = $shippingMethodListFake;
                    if (!in_array($items[$quoteItemId]['shipping_method'], $shippingMethodList)) {
                        unset($addressShippingMethod[$_address->getId()]);
                        $error = true;
                    }
                }
            }
        }

        if ($error) {
            $data['error'] = true;
            $data['data'] = $itemShippingMethod;
        } else {
            // set shipping method to quote address
            $data['data'] = $itemShippingMethod;
        }
        //
        $data['error_stock'] = $this->reload;
        $data['error_stock_message'] = $this->reloadMessage;
        $data['addressEachItems'] = $this->addressEachItems;
        $data['current_items'] = [];
        foreach ($checkoutSession->getQuote()->getAllItems() as $item) {
            $item->unsetData('product_option');
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                continue;
            }
            if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                $quoteItemId = $item->getQuoteItemId();
            } else {
                $quoteItemId = $item->getId();
            }
            $data['current_items'][] = $quoteItemId;
        }
        $billing = $checkoutSession->getCustomer()->getDefaultBilling();
        $checkoutSession->setQuoteCustomerBillingAddress($billing);
        $checkoutSession->setShippingMethods($addressShippingMethod);
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
            ]
        ];
    }

    /**
     * @param $items
     * @return array[]
     */
    public function itemsFormat($items)
    {
        $itemsFormat = [];
        $requestItems = [];
        foreach ($items as $item) {
            $requestItems[$item->getItemId()] = $item;
            if ($item->getDisable()) {
                continue;
            }
            $format['shipping_method'] = $item->getShippingMethodSelected();
            $format['shipping_address'] = $item->getShippingAddressId();
            $format['qty'] = $item->getQty();
            $additionalInfo = $item->getAdditionalInfo()->getDelivery();
            $format['delivery'] = [
                'date' => $additionalInfo->getDate(),
                'time' => $additionalInfo->getTime()
            ];
            $itemsFormat[$item->getItemId()] = $format;
        }
        return ['item_format' => $itemsFormat, 'item_request' => $requestItems];
    }

    /**
     * @param $items
     * @param $additionalInfo
     * @param $customer
     * @param $checkoutSession
     * @return array|bool[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getSplitOrderData($items, $additionalInfo, $customer, $checkoutSession)
    {
        $data = ['error' => false, 'data' => [], 'validShippingMethod' => [], 'split' => false];
        $defaultShippingAddress = $customer->getDefaultShippingAddress()->getId();
        $splitOrderFromOar = [];
        $splitOrder = [];
        $quoteItemIdSku = [];
        $order = [];
        $addressIds = [];
        /** @var \Magento\Quote\Model\Quote\Item[] $allItems */
        $allItems = $checkoutSession->getQuote()->getAllItems();
        $storePickupOrder = [];
        $child = [];
        $listMethod = $this->split->getListMethodName();
        foreach ($allItems as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $child[$item->getParentItemId()][] = $item;
            }
            $quoteItemIdSku[$item->getId()] = $item;
        }

        $newItemsHasChild = [];
        $newItemsNoChild = [];
        foreach ($items as $quoteItemId => $itemData) {
            if (!isset($quoteItemIdSku[$quoteItemId])) {
                continue;
            }
            if ($itemData['shipping_method'] == self::STORE_PICK_UP) {
                $storePickupOrder[][$quoteItemId] = [
                    'qty' => $quoteItemIdSku[$quoteItemId]->getQty(),
                    'address' => $defaultShippingAddress,
                    'shipping_method' => $itemData['shipping_method'],
                    'store_pickup' => $additionalInfo['store_pick_up']['store_code'],
                    'split_store_code' => 0
                ];
            } else {
                if (!in_array($itemData['shipping_address'], $addressIds)) {
                    $addressIds[] = $itemData['shipping_address'];
                }
                $rateCode = $itemData['shipping_method'];
                if (isset($order[$itemData['shipping_address']])) {
                    if (isset($order[$itemData['shipping_address']][$rateCode])) {
                        $order[$itemData['shipping_address']][$rateCode][$quoteItemId] = $quoteItemIdSku[$quoteItemId]['qty'];
                    } else {
                        $order[$itemData['shipping_address']][$rateCode] = [
                            $quoteItemId => $quoteItemIdSku[$quoteItemId]['qty']
                        ];
                    }
                } else {
                    $order[$itemData['shipping_address']] = [
                        $rateCode => [
                            $quoteItemId => $quoteItemIdSku[$quoteItemId]['qty']
                        ]
                    ];
                }
                $serviceType = str_replace("transshipping_transshipping", "", $itemData['shipping_method']);
                if (!isset($listMethod[$serviceType])) {
                    $data['error'] = true;
                }
            }
            $items[$quoteItemId]['qty'] = $quoteItemIdSku[$quoteItemId]->getQty();
            $items[$quoteItemId]['child'] = [];
            $items[$quoteItemId]['sku'] = $quoteItemIdSku[$quoteItemId]->getSku();
            if (isset($child[$quoteItemId])) {
                $items[$quoteItemId]['child'] = $child[$quoteItemId];
                $newItemsHasChild[$quoteItemId] = $items[$quoteItemId];
            } else {
                $newItemsNoChild[$quoteItemId] = $items[$quoteItemId];
            }
        }
        $items = $newItemsHasChild + $newItemsNoChild;
        $errorOarSlit = [];
        if (!empty($addressIds)) {
            $addressCollection = $customer->getAddressesCollection()->addFieldToFilter('entity_id', ['in' => $addressIds]);
            $addressData = $this->split->getAddressData($addressCollection);
            $customerId = $customer->getId();
            $merchantCode = $this->getMerchantCode();
            $errorOar = [];
            $i = 0;
            foreach ($order as $addressId => $rateItem) {
                foreach ($rateItem as $serviceType => $item) {
                    $i++;
                    $totalWeight = 0;
                    $totalQty = 0;
                    $totalPrice = 0;
                    $itemsData = [];
                    $orderToSendOar = [];
                    $orderToSendOar['order_id'] = $customerId;
                    $orderToSendOar['merchant_code'] = $merchantCode;
                    $orderToSendOar['destination'] = $addressData[$addressId];
                    foreach ($item as $quoteItemId => $qty) {
                        $itemsData[] = $quoteItemId;
                        $itemData = $quoteItemIdSku[$quoteItemId];
                        $rowTotal = (int)$itemData->getRowTotal();
                        $totalPrice += $rowTotal;
                        if (isset($child[$quoteItemId])) {
                            // parent quote item id
                            foreach ($child[$quoteItemId] as $itemData) {
                                $product = $itemData->getProduct();
                                $ownCourier = $product->getData('own_courier');
                                if ($ownCourier) {
                                    $ownCourier = true;
                                } else {
                                    $ownCourier = false;
                                }
                                $sku = $itemData->getSku();
                                $childQty = (int)$qty * (int)$itemData->getQty();
                                $price = ((int)$itemData->getPrice() != 0) ? (int)$itemData->getPrice() : (int)$product->getFinalPrice();
                                if (isset($orderToSendOar['items'][$sku])) {
                                    $orderToSendOar['items'][$sku]['quantity'] += $childQty;
                                } else {
                                    $orderToSendOar['items'][] = [
                                        'sku' => $sku,
                                        'sku_basic' => $sku,
                                        'quantity' => $childQty,
                                        'price' => $price,
                                        'weight' => (int)$itemData->getWeight(),
                                        'is_spo' => false,
                                        'is_own_courier' => $ownCourier
                                    ];
                                }
                                $totalWeight += $childQty * (int)$itemData->getWeight();
                                $totalQty += $childQty;
                                if ($rowTotal == 0) {
                                    $rowTotal = $price * $childQty;
                                    $totalPrice += $rowTotal;
                                }
                            }
                        } else {
                            $product = $itemData->getProduct();
                            $ownCourier = $product->getData('own_courier');
                            if ($ownCourier) {
                                $ownCourier = true;
                            } else {
                                $ownCourier = false;
                            }
                            // sing quote item id
                            $sku = $itemData->getSku();
                            $price = ((int)$itemData->getPrice() != 0) ? (int)$itemData->getPrice() : (int)$product->getFinalPrice();
                            if (isset($orderToSendOar['items'][$sku])) {
                                $orderToSendOar['items'][$sku]['quantity'] += (int)$qty;
                            } else {
                                $orderToSendOar['items'][$sku] = [
                                    'sku' => $sku,
                                    'sku_basic' => $sku,
                                    'quantity' => (int)$qty,
                                    'price' => $price,
                                    'weight' => (int)$itemData->getWeight(),
                                    'is_spo' => false,
                                    'is_own_courier' => $ownCourier
                                ];
                            }

                            $totalWeight += (int)$itemData->getWeight();
                            $totalQty += (int)$qty;
                            if ($rowTotal == 0) {
                                $rowTotal = $price * (int)$qty;
                                $totalPrice += $rowTotal;
                            }
                        }
                    }
                    $orderToSendOar["total_weight"] = (int)$totalWeight;
                    $orderToSendOar["total_price"] = (int)$totalPrice;
                    $orderToSendOar["total_qty"] = (int)$totalQty;
                    if ($orderToSendOar['destination']['district'] == ''
                        || !$this->checkShippingPostCode($orderToSendOar['destination']['postcode'])
                        || $orderToSendOar['destination']['latitude'] == 0
                        || $orderToSendOar['destination']['longitude'] == 0
                    ) {
                        $splitOrder[$i][$addressId]['oar']['error'] = __('Address not validate.');
                    } else {
                        $resetItems = [];
                        foreach ($orderToSendOar['items'] as $it) {
                            $resetItems[] = $it;
                        }
                        $orderToSendOar['items'] = $resetItems;
                        $splitOrder[$i][$addressId]['oar'] = $this->split->getOarResponse([$orderToSendOar]);
                    }
                    if (isset($splitOrder[$i][$addressId]['oar']['error'])) {
                        unset($splitOrder[$i]);
                        foreach ($itemsData as $quoteItemId) {
                            $errorOar[$quoteItemId] = [];
                            $errorOarSlit[][$quoteItemId] = [
                                'qty' => $quoteItemIdSku[$quoteItemId]['qty'],
                                'address' => $items[$quoteItemId]['shipping_address'],
                                'shipping_method' => $items[$quoteItemId]['shipping_method'],
                                'split_store_code' => 0
                            ];
                            unset($items[$quoteItemId]);
                        }
                    }
                }
            }
            if (!empty($errorOar)) {
                $data['error'] = true;
                $data['data'] = $errorOarSlit;
            }
            if (!empty($splitOrder)) {
                $splitOrderFromOar = $this->convertOarOrder($splitOrder, $items);
                $data['validShippingMethod'] = $splitOrderFromOar['validShippingMethod'] + $errorOar;
                $data['data'] = array_merge($data['data'], $splitOrderFromOar['ship']);
            } else {
                $data['validShippingMethod'] = $errorOar;
            }
        }
        $data['data'] = array_merge($data['data'], $storePickupOrder);
        return $data;
    }

    /**
     * @param $splitOrder
     * @param $items
     * @return array[]
     */
    public function convertOarOrder($splitOrder, $items)
    {
        $itemsResponseOar = [];
        $ship = [];
        $validShippingMethod = [];
        $splitOrderWithSku = [];
        $addressIdList = [];
        foreach ($splitOrder as $data) {
            foreach ($data as $addressId => $order) {
                $i = 0;
                foreach ($order['oar']['content'] as $contentData) {
                    if (!$contentData['status']) {
                        continue;
                    }
                    $orderDetail = $contentData['data'];
                    if (!isset($orderDetail['store']) || !isset($orderDetail['store']['store_code'])) {
                        continue;
                    }
                    $storeCode = $orderDetail['store']['store_code'];
                    $orderItems = $orderDetail['items'];
                    $orderShippingMethod = $this->getShippingListFromOrderSplit($orderDetail['shipping_list']);
                    $hasInStockProduct = false;
                    foreach ($orderItems as $item) {
                        if (isset($item['sku']) && isset($item['quantity']) && (int)$item['quantity'] > 0) {
                            $hasInStockProduct = true;
                            if (isset($splitOrderWithSku[$item['sku']])) {
                                $splitOrderWithSku[$item['sku']]['quantity_allocated'] += (int)$item['quantity'];
                            } else {
                                $splitOrderWithSku[$item['sku']] = [
                                    'store' => $storeCode,
                                    'quantity_allocated' => (int)$item['quantity'],
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
                                        'spo_detail' => $orderDetail['warehouse']
                                    ]
                                ];
                            }
                        }
                    }
                    if ($hasInStockProduct) {
                        $i++;
                    }
                }
                if ($i > 1) {
                    $this->orderIsSplit = true;
                }
            }
        }
        foreach ($items as $quoteItemId => $itemData) {
            if ($itemData['shipping_method'] == self::STORE_PICK_UP) {
                unset($items[$quoteItemId]);
                continue;
            }
            if (empty($itemData['child'])) {
                // single
                if (!isset($splitOrderWithSku[$itemData['sku']])) {
                    // out stock from oar
                    //unset($items[$quoteItemId]);
                } else {
                    $itemFromOAR = $splitOrderWithSku[$itemData['sku']];
                    $qty = 0;
                    if ((int)$itemData['qty'] <= (int)$itemFromOAR['quantity_allocated']) {
                        $qty = (int)$itemData['qty'];
                    } elseif ((int)$itemFromOAR['quantity_allocated'] > 0) {
                        $qty = (int)$itemFromOAR['quantity_allocated'];
                    }
                    if ($qty > 0) {
                        $shippingList = $itemFromOAR['shipping_list'];
                        if (count($shippingList) == 1 && in_array(self::DC, $shippingList)) {
                            // is_spo
                            $this->hasSpo = true;
                            if ($itemData['shipping_method'] == self::DEFAULT_METHOD) {
                                $itemData['shipping_method'] = self::DC;
                            }
                        } elseif (count($shippingList) <= 2 && in_array(self::TRANS_COURIER, $shippingList)) {
                            // own_courier
                            $this->hasFresh = true;
                            if ($itemData['shipping_method'] == self::SAME_DAY) {
                                $itemData['shipping_method'] = self::TRANS_COURIER;
                            }
                        } elseif (count($shippingList) == 1 && in_array(self::SAME_DAY, $shippingList)) {
                            $this->hasFresh = true;
                        } else {
                            $this->hasNormal = true;
                        }
                        $itemAddToQuoteAddress = [
                            'qty' => $qty,
                            'address' => $itemData['shipping_address'],
                            'shipping_method' => $itemData['shipping_method'],
                            'split_store_code' => $itemFromOAR['store'],
                            'oar_data' => $itemFromOAR['oar_data']
                        ];
                        if (!in_array($itemData['shipping_address'], $addressIdList)) {
                            $addressIdList[] = $itemData['shipping_address'];
                        }
                        $this->itemsUpdate[$quoteItemId]['shipping_method'] = $itemData['shipping_method'];
                        $ship[][$quoteItemId] = $itemAddToQuoteAddress;
                        $itemsResponseOar[$quoteItemId] = $itemAddToQuoteAddress;
                        $splitOrderWithSku = $this->rePareQtyFromOAR([$itemData['sku'] => $qty], $splitOrderWithSku);
                    }
                }
            } else {
                //parent
                $itemAddToQuoteAddress = [
                    'qty' => (int)$itemData['qty'],
                    'address' => $itemData['shipping_address'],
                    'shipping_method' => $itemData['shipping_method'],
                    'split_store_code' => 0,
                ];
                if (!in_array($itemData['shipping_address'], $addressIdList)) {
                    $addressIdList[] = $itemData['shipping_address'];
                }
                $useQtyFromOar = [];
                $outStockFromOar = false;
                foreach ($itemData['child'] as $childItem) {
                    if (!isset($splitOrderWithSku[$childItem->getSku()])
                        || ($itemAddToQuoteAddress['split_store_code'] != 0 && $itemAddToQuoteAddress['split_store_code'] != $splitOrderWithSku[$childItem->getSku()]['store'])
                    ) {
                        // out stock from oar
                        $outStockFromOar = true;
                        //unset($items[$quoteItemId]);
                        break;
                    } else {
                        $childSku = $childItem->getSku();
                        $itemFromOAR = $splitOrderWithSku[$childSku];
                        $shippingList = $itemFromOAR['shipping_list'];
                        if (count($shippingList) == 1 && isset($shippingList[self::DC])) {
                            // is_spo
                            $this->hasSpo = true;
                            $itemData['shipping_method'] = self::DC;
                        } elseif (count($shippingList) <= 2 && isset($shippingList[self::TRANS_COURIER])) {
                            // own_courier
                            $this->hasFresh = true;
                            $itemData['shipping_method'] = self::TRANS_COURIER;
                        } elseif (count($shippingList) == 1 && in_array(self::SAME_DAY, $shippingList)) {
                            $this->hasFresh = true;
                        } else {
                            $this->hasNormal = true;
                        }
                        $childItemTotalQty = (int)$childItem->getQty() * $itemAddToQuoteAddress['qty'];
                        if ($childItemTotalQty <= (int)$itemFromOAR['quantity_allocated']) {
                            $useQtyFromOar[$childSku] = $itemData['qty'];
                            $itemAddToQuoteAddress['split_store_code'] = $itemFromOAR['store'];
                            $itemAddToQuoteAddress['oar_data'] = $itemFromOAR['oar_data'];
                        } elseif ((int)$itemFromOAR['quantity_allocated'] > 0) {
                            $qty = (int)$itemFromOAR['quantity_allocated'] / (int)$childItem->getQty();
                            $qty = round($qty);
                            if ($qty == 0) {
                                // out stock from oar
                                $outStockFromOar = true;
                                //unset($items[$quoteItemId]);
                                break;
                            } else {
                                $itemAddToQuoteAddress['qty'] = $qty;
                                $itemAddToQuoteAddress['split_store_code'] = $itemFromOAR['store'];
                                $itemAddToQuoteAddress['oar_data'] = $itemFromOAR['oar_data'];
                            }
                        } else {
                            // out stock from oar
                            $outStockFromOar = true;
                            //unset($items[$quoteItemId]);
                            break;
                        }
                    }
                }
                if (!$outStockFromOar) {
                    $splitOrderWithSku = $this->rePareQtyFromOAR($useQtyFromOar, $splitOrderWithSku);
                    $itemsResponseOar[$quoteItemId] = $itemAddToQuoteAddress;
                    $ship[][$quoteItemId] = $itemAddToQuoteAddress;
                    $this->itemsUpdate[$quoteItemId]['shipping_method'] = $itemAddToQuoteAddress['shipping_method'];
                }
            }
        }
        $this->checkoutOarStock($items, $itemsResponseOar);
        if (count($addressIdList) == 1
            && (($this->hasNormal && $this->hasFresh) || ($this->hasNormal && $this->hasSpo) || ($this->hasFresh && $this->hasSpo))
        ) {
            $this->orderIsSplit = true;
        }
        return ['ship' => $ship, 'validShippingMethod' => $validShippingMethod];
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
                $this->reload = true;
                return;
            } elseif ($item['qty'] != $itemsResponseOar[$itemId]['qty']) {
                $this->reloadMessage['low_stock'][] = $itemId;
                $this->reload = true;
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
            $methodList[] = 'transshipping_transshipping' . $method['service_type'];
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
        $reload = false;
        $shippingAddress = $quote->getAllShippingAddresses();
        foreach ($shippingAddress as $address) {
            $date = $time = null;
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
                    $date = $this->timezone->convertConfigTimeToUtc($dateTime['date']);
                    $time = $dateTime['time'];
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
        $previewOrderData = [];
        $shippingMethodSelected = [];
        $shippingAddress = $quote->getAllShippingAddresses();
        foreach ($shippingAddress as $address) {
            $shippingMethodSelected[$address->getId()] = [
                'shipping_method' => $address->getShippingMethod(),
                'customer_address_id' => $address->getCustomerAddressId()
            ];

            $date = $time = "";
            $previewOrder = $this->previewOrderInterfaceFactory->create();
            $shippingMethod = $address->getShippingMethod();
            $previewOrder->setShippingFeeNotDiscount(0);
            if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                $title = __('Pick Up in Store');
                $shippingMethodTitle = __("Store Pick Up");
                $addressId = 0;
                $date = $address->getDate();
                $time = $address->getTime();
            } else {
                $title = __('Delivery Address');
                $listMethod = $this->split->getListMethodName();
                $shippingMethodServiceType = str_replace("transshipping_transshipping", "", $shippingMethod);
                $shippingMethodTitle = (isset($listMethod[$shippingMethodServiceType])) ? $listMethod[$shippingMethodServiceType] : __('Shipping Method not available');
                $addressId = $address->getCustomerAddressId();
                if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::SCHEDULE) {
                    $date = $address->getDate();
                    $time = $address->getTime();
                }
                if ($address->getFreeShipping() == 1) {
                    $previewOrder->setShippingFeeNotDiscount($address->getShippingDiscountAmount());
                    $previewOrder->setShippingFee($address->getShippingInclTax());
                } else {
                    $shippingFee = (int)$address->getShippingInclTax() - (int)$address->getShippingDiscountAmount();
                    $previewOrder->setShippingFeeNotDiscount($address->getShippingInclTax());
                    $previewOrder->setShippingFee($shippingFee);
                }
            }
            $previewOrder->setTitle($title);
            $previewOrder->setShippingMethod($shippingMethod);
            $previewOrder->setShippingMethodTitle($shippingMethodTitle);
            $previewOrder->setAddressId($addressId);
            if (!$web && $date != '') {
                $date = date('d M Y', strtotime($date));
            }
            $previewOrder->setDate($date);
            $previewOrder->setTime($time);
            $itemList = [];
            $itemTotal = 0;
            foreach ($address->getAllVisibleItems() as $item) {
                if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                    $quoteItemId = $item->getQuoteItemId();
                } else {
                    $quoteItemId = $item->getId();
                }
                $itemList[] = $quoteItemId;
                $itemTotal = $itemTotal + (int)$item->getQty();
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
        $reload = false;
        $quoteItemsName = [];
        foreach ($quoteItems as $item) {
            if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                $itemId = $item->getQuoteItemId();
            } else {
                $itemId = $item->getId();
            }
            $quoteItemsName[$itemId] = $item->getName();
            $qty = (int)$item->getQty();
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
     * @param $postCode
     * @return bool
     */
    public function checkShippingPostCode($postCode)
    {
        return $this->split->checkShippingPostCode($postCode);
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
        $showEachItems = false;
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
            $i = 0;
            if ($web) {
                foreach ($itemsValidMethod as $item) {
                    $i++;
                    if ($i == 1) {
                        $validMethodCode = count($item->getValidMethod());
                    } else {
                        if ($validMethodCode != count($item->getValidMethod())) {
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
}
