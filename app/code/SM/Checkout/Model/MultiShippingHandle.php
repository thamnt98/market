<?php

namespace SM\Checkout\Model;

/**
 * Class MultiShippingHandle
 * @package SM\Checkout\Model
 */
class MultiShippingHandle
{
    const STORE_PICK_UP = 'store_pickup_store_pickup';
    const SCHEDULE = 'transshipping_transshipping3';
    const DEFAULT_METHOD = 'transshipping_transshipping1';
    const SAME_DAY = 'transshipping_transshipping2';

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
        $this->updateStockItem->updateStock($checkoutSession->getQuote());
        $data = ['error' => false, 'data' => [], 'split' => false];
        $splitOrderData = $this->getSplitOrderData($items, $additionalInfo, $customer, $checkoutSession);
        $data['error'] = $splitOrderData['error'];
        $data['split'] = $this->orderIsSplit;
        //die();
        $this->reBuildQuoteAddress($splitOrderData['data'], $checkoutSession);
        //
        $addressShippingMethod = [];
        $itemShippingMethod = [];
        $itemShippingMethodMerge = [];
        $error = false;
        foreach ($checkoutSession->getQuote()->getAllShippingAddresses() as $_address) {
            $shippingMethod = $_address->getPreShippingMethod();
            if ($shippingMethod == self::STORE_PICK_UP) {
                $addressShippingMethod[$_address->getId()] = true;
                continue;
            } else {
                $addressShippingMethod[$_address->getId()] = true;
                $_shippingRateGroups = $_address->getGroupedAllShippingRates();
                $shippingMethodList = [];
                if ($_shippingRateGroups) {
                    foreach ($_shippingRateGroups as $code => $_rates) {
                        if ($code == 'transshipping') {
                            foreach ($_rates as $_rate) {
                                $shippingMethodList[] = $_rate->getCode();
                                if (!in_array($_rate->getCode(), $itemShippingMethodMerge)) {
                                    $itemShippingMethodMerge[] = $_rate->getCode();
                                }
                            }
                        }
                    }
                }
                foreach ($_address->getAllVisibleItems() as $item) {
                    if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                        $quoteItemId = $item->getQuoteItemId();
                    } else {
                        $quoteItemId = $item->getId();
                    }
                    $itemShippingMethod[$quoteItemId] = $shippingMethodList;
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
        foreach ($checkoutSession->getQuote()->getAllItems() as $item) {
            $item->unsetData('product_option');
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
     * @return array
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
                                $ownCourier = $itemData->getProduct()->getData('own_courier');
                                if ($ownCourier) {
                                    $ownCourier = 1;
                                } else {
                                    $ownCourier = 0;
                                }
                                $sku = $itemData->getSku();
                                $childQty = (int)$qty * (int)$itemData->getQty();
                                $price = ((int)$itemData->getPrice() != 0) ? (int)$itemData->getPrice() : (int)$itemData->getProduct()->getFinalPrice();
                                if (isset($orderToSendOar['items'][$sku])) {
                                    $orderToSendOar['items'][$sku]['quantity'] += $childQty;
                                } else {
                                    $orderToSendOar['items'][] = [
                                        'sku' => $sku,
                                        'sku_basic' => $sku,
                                        'quantity' => $childQty,
                                        'price' => $price,
                                        'weight' => (int)$itemData->getWeight(),
                                        'is_spo' => 0,
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
                            $ownCourier = $itemData->getProduct()->getData('own_courier');
                            if ($ownCourier) {
                                $ownCourier = 1;
                            } else {
                                $ownCourier = 0;
                            }
                            // sing quote item id
                            $sku = $itemData->getSku();
                            $price = ((int)$itemData->getPrice() != 0) ? (int)$itemData->getPrice() : (int)$itemData->getProduct()->getFinalPrice();
                            if (isset($orderToSendOar['items'][$sku])) {
                                $orderToSendOar['items'][$sku]['quantity'] += (int)$qty;
                            } else {
                                $orderToSendOar['items'][$sku] = [
                                    'sku' => $sku,
                                    'sku_basic' => $sku,
                                    'quantity' => (int)$qty,
                                    'price' => $price,
                                    'weight' => (int)$itemData->getWeight(),
                                    'is_spo' => 0,
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
        foreach ($splitOrder as $data) {
            foreach ($data as $addressId => $order) {
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
                    foreach ($orderItems as $item) {
                        if (isset($item['sku']) && isset($item['quantity'])) {
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
                        $itemAddToQuoteAddress = [
                            'qty' => $qty,
                            'address' => $itemData['shipping_address'],
                            'shipping_method' => $itemData['shipping_method'],
                            'split_store_code' => $itemFromOAR['store'],
                            'oar_data' => $itemFromOAR['oar_data']
                        ];
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
                }
            }
        }
        $this->checkoutOarStock($items, $itemsResponseOar);
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
        $storeCode = [];
        foreach ($items as $itemId => $item) {
            if ($item['shipping_method'] == self::STORE_PICK_UP) {
                continue;
            }
            if (!isset($itemsResponseOar[$itemId])) {
                $this->reloadMessage['out_stock'][] = $itemId;
                $this->reload = true;
            } elseif ($item['qty'] != $itemsResponseOar[$itemId]['qty']) {
                $this->reloadMessage['low_stock'][] = $itemId;
                $this->reload = true;
                if (!in_array($itemsResponseOar[$itemId]['split_store_code'], $storeCode)) {
                    $storeCode[] = $itemsResponseOar[$itemId]['split_store_code'];
                }
            }
        }
        if (count($storeCode) > 1) {
            $this->orderIsSplit = true;
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
            } else {
                if ($shippingMethod == self::SCHEDULE) {
                    $dateTime = $deliveryDateTime[$address->getCustomerAddressId()];
                    $date = $this->timezone->convertConfigTimeToUtc($dateTime['date']);
                    $time = $dateTime['time'];
                }
            }
            $this->saveAddressData($address, $date, $time);
        }
        return $reload;
    }

    /**
     * @param $address
     * @param $date
     * @param $time
     */
    protected function saveAddressData($address, $date, $time)
    {
        $address->setDate($date)->setTime($time)->save();
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
            $previewOrder->setFreeShipping(false);
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
                    $previewOrder->setFreeShipping(true);
                    $previewOrder->setShippingFeeNotDiscount($address->getShippingDiscountAmount());
                } else {
                    $previewOrder->setShippingFeeNotDiscount($address->getShippingInclTax());
                }
            }
            $previewOrder->setTitle($title);
            $previewOrder->setShippingMethod($shippingMethod);
            $previewOrder->setShippingMethodTitle($shippingMethodTitle);
            $previewOrder->setShippingFee($address->getShippingInclTax());
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
}
