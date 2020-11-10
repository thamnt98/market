<?php

namespace SM\Checkout\Model;

/**
 * Class SendOMS
 * @package SM\Checkout\Model
 */
class SendOMS
{
    /**
     * @var Checkout\Type\Multishipping
     */
    protected $multiShipping;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Trans\IntegrationOrder\Model\IntegrationOrderFactory
     */
    protected $integrationOrder;
    /**
     * @var \Trans\IntegrationOrder\Api\OmsIntegrationInterface
     */
    protected $omsIntegration;

    /**
     * @var \Trans\MasterPayment\Model\MasterPaymentFactory
     */
    protected $masterPaymentFactory;
    /**
     * @var \Trans\LocationCoverage\Model\DistrictFactory
     */
    protected $districtFactory;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \SM\Checkout\Model\Payment
     */
    protected $payment;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Trans\LocationCoverage\Model\CityFactory
     */
    protected $cityFactory;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * SendOMS constructor.
     * @param Checkout\Type\Multishipping $multiShipping
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Trans\IntegrationOrder\Model\IntegrationOrderFactory $integrationOrder
     * @param \Trans\IntegrationOrder\Api\OmsIntegrationInterface $omsIntegration
     * @param \Trans\MasterPayment\Model\MasterPaymentFactory $masterPaymentFactory
     * @param \Trans\LocationCoverage\Model\DistrictFactory $districtFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Payment $payment
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Trans\LocationCoverage\Model\CityFactory $cityFactory
     * @param Price $price
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \SM\Checkout\Model\Checkout\Type\Multishipping $multiShipping,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Trans\IntegrationOrder\Model\IntegrationOrderFactory $integrationOrder,
        \Trans\IntegrationOrder\Api\OmsIntegrationInterface $omsIntegration,
        \Trans\MasterPayment\Model\MasterPaymentFactory $masterPaymentFactory,
        \Trans\LocationCoverage\Model\DistrictFactory $districtFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \SM\Checkout\Model\Payment $payment,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Trans\LocationCoverage\Model\CityFactory $cityFactory,
        \SM\Checkout\Model\Price $price,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->multiShipping = $multiShipping;
        $this->orderFactory = $orderFactory;
        $this->integrationOrder = $integrationOrder;
        $this->omsIntegration = $omsIntegration;
        $this->masterPaymentFactory = $masterPaymentFactory;
        $this->districtFactory = $districtFactory;
        $this->cart = $cart;
        $this->quoteFactory = $quoteFactory;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        $this->customerFactory = $customerFactory;
        $this->payment = $payment;
        $this->regionFactory = $regionFactory;
        $this->cityFactory = $cityFactory;
        $this->price = $price;
        $this->serializer = $serializer;
        $this->eventManager = $eventManager;
        $this->timezone = $timezone;
    }

    /**
     * @param array $suborderIds
     * @param $mainOrder
     * @throws \Exception
     */
    public function processOrderOms($suborderIds, $mainOrder)
    {
        try {
            $data = [];
            /**
             * suborder
             */
            foreach ($suborderIds as $id) {
                $order = $this->orderFactory->create()->load($id);
                $data[] = $this->prepareData($order, $mainOrder);
            }
            $this->omsIntegration->createOrderOms($data);

            return;
        } catch (\Exception $e) {
            $this->payment->paymentFailed($mainOrder, true);
            throw new \Exception(__($e->getMessage()));
        }
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDistrictName($id)
    {
        $district = $this->districtFactory->create()->load($id);
        if ($district->getId()) {
            return $district->getDistrict();
        }
        return '';
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order $mainOrder
     * @return \Trans\IntegrationOrder\Model\IntegrationOrder
     */
    public function prepareData($order, $mainOrder)
    {
        /**
         * @var \Magento\Sales\Model\Order\Item $item
         */
        $items = [];
        foreach ($order->getAllItems() as $item) {
            if (!$item->getParentItemId() && ($item->getProductType() == 'configurable' || $item->getProductType() == 'bundle')) {
                continue;
            }
            $product = $item->getProduct();
            $regularPrice = $this->price->getRegularPrice($product);
            $qty = $item->getQtyOrdered();
            $weight = $item->getWeight();
            $price = $item->getPrice();
            $oriPrice = $regularPrice; // regular_price
            $sellPrice = $price; // final_price
            $discPrice = $item->getDiscountAmount(); // total_discount
            $subTotal = $sellPrice * $qty;
            $padPrice = $subTotal - $discPrice; // sub_total - total_discount
            $itemData = [
                'sku_basic' => '',
                'sku' => $item->getSku(),
                'quantity' => (int)$qty,
                //'name' => $item->getName(),
                'ori_price' => (int)$oriPrice,
                'sell_price' => (int)$sellPrice,
                'disc_price' => (int)$discPrice,
                'paid_price' => (int)$padPrice,
                'sub_total' => (int)$subTotal,
                'total_weight' => (int)$weight * (int)$qty,
                'weight' => (int)$weight,
                'coupon_code' => '', // pass
                'coupon_val' => 0,
                'is_warehouse' => $product->getIsWarehouse(),
            ];

            $this->eventManager->dispatch(
                'send_oms_prepare_item_data_after',
                ['itemData' => $itemData]
            );
            $items[] = $itemData;
        }
        $billingData = [
            ['street' => (string)$order->getBillingAddress()->getStreet()[0]]
        ];
        /**
         * @var \Magento\Sales\Model\Order\Address $shippingData
         */
        $shippingData = $order->getShippingAddress();
        $customerId = $order->getCustomerId();
        /**
         * @var \Magento\Customer\Model\Customer $customer
         */
        $customer = $this->customerFactory->create()->load($customerId);
        $districtData = '';
        $latitude = 0;
        $longitude = 0;
        /**
         * @var \Magento\Customer\Model\Address $singleAddress
         */
        foreach ($customer->getAddresses() as $singleAddress) {
            if ($singleAddress->getCustomAttribute('district') != null) {
                $districtData = $singleAddress->getCustomAttribute('district');
            }
            if ($singleAddress->getCustomAttribute('latitude') != null) {
                $latitude = $singleAddress->getCustomAttribute('latitude')->getValue();
            }
            if ($singleAddress->getCustomAttribute('longitude') != null) {
                $longitude = $singleAddress->getCustomAttribute('longitude')->getValue();
            }
        }
        $districtId = $districtData ? $districtData->getValue() : '';
        $district = $this->getDistrictName($districtId);

        $shipping = [
            [
                'street' => (string)$shippingData->getStreet()[0],
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'province' => (string)$this->getProvince($shippingData->getRegionId()),
                'city' => (string)$this->getCityName($shippingData->getCity()),
                'district' => (string)$district,
                'zip_code' => (string)$shippingData->getPostcode(),
            ]
        ];

        $paymentData = $mainOrder->getPayment();
        $masterList = $this->masterPaymentFactory->create()
            ->getCollection();
        $paymentCode = [];
        /**
         * @var \Trans\MasterPayment\Model\MasterPayment $master
         */
        foreach ($masterList as $master) {
            $paymentCode[$master->getPaymentMethod()] = $master->getPaymentId();
        }

        $masterId = (isset($paymentCode[$paymentData->getMethod()])) ? $paymentCode[$paymentData->getMethod()] : 0;
        $payment = [
            [
                'master_payment_id1' => (int)$masterId,
                'pay_ref_number1' => $mainOrder->getReferencePaymentNumber(),
                'amount' => (int)$paymentData->getAmountOrdered(),
                'split_payment' => 0,
                'currency' => 1

            ]
        ];
        $storeCode = ($order->getShippingMethod() == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) ? $order->getStorePickUp() : $order->getSplitStoreCode();
        $interface = $this->integrationOrder->create();
        $interface->setReferenceNumber($mainOrder->getReferenceNumber());
        $interface->setAccountEmail($order->getCustomerEmail());
        $interface->setAccountName($order->getCustomerName());
        $interface->setAccountPhoneNumber($shippingData->getTelephone());
        $interface->setReceiverName($order->getCustomerName());
        $interface->setReceiverPhone($shippingData->getTelephone());
        $interface->setFlagSpo(1);
        $interface->setStoreCode($storeCode);
        $interface->setOrderId($order->getReferenceOrderId());
        $interface->setQuoteId($order->getQuoteId());
        $interface->setAddressId($shippingData->getId());
        $interface->setPromotionType($order->getAppliedRuleIds());
        $interface->setPromotionValue((int)$order->getDiscountAmount());
        $interface->setOrderItems($items);
        $interface->setBilling($billingData);
        $interface->setShipping($shipping);
        $interface->setPayment($payment);

        $shippingMethod = $order->getShippingMethod();
        if (strpos($shippingMethod, 'transshipping_transshipping') !== false) {
            $logisticType = str_replace("transshipping_transshipping", "", $shippingMethod);
            $logisticType = (int)$logisticType;
            $orderType = 2;
        } else {
            $logisticType = 0;
            $orderType = 1;
        }
        $referenceNumber = $order->getReferenceNumber();
        $referenceNumber = explode("-", $referenceNumber);
        if (strtolower($referenceNumber[0]) == 'web') {
            $sourceOrder = 2;
        } else {
            $sourceOrder = 3;
        }
        $shippingFee = (int)$order->getShippingInclTax();
        $interface->setOrderType($orderType);
        $interface->setOrderSource($sourceOrder);
        $interface->setCourier($logisticType);
        $interface->setShippingFee($shippingFee);

        $oarData = $order->getOarData();
        try {
            $oarData = $this->serializer->unserialize($oarData);
        } catch (\Exception $e) {
            $oarData = [];
        }

        $orderOriginId = isset($oarData['order_id_origin']) ? (int) $oarData['order_id_origin'] : 0;
        $codeName = '';
        if (isset($oarData['spo_detail']) && $oarData['spo_detail']) {
            $spoDetail = json_encode($oarData['spo_detail']);
            if (isset($spoDetail['code_name'])) {
                $codeName = $spoDetail['code_name'];
            }
        } elseif ($this->byPassWareHouse()) {
            $spoDetail = $this->getSampleWareHouse();
            $codeName = $spoDetail['code_name'];
            $spoDetail = json_encode($spoDetail);
        } else {
            $spoDetail = json_encode('');
        }
        $isSpo = isset($oarData['is_spo']) ? (int) $oarData['is_spo'] : 0;
        $isOwnCourier = isset($oarData['is_own_courier']) ? (int) $oarData['is_own_courier'] : 0;
        $warehouseSource = isset($oarData['warehouse_source']) ? (string) $oarData['warehouse_source'] : '';
        $warehouseCode = (isset($oarData['warehouse']) && isset($oarData['warehouse']['store_code'])) ? (string) $oarData['warehouse']['store_code'] : '';
        $interface->setOrderOriginId($orderOriginId);
        $interface->setSpoDetail($spoDetail);
        $interface->setIsSpo($isSpo);
        $interface->setIsOwnCourier($isOwnCourier);
        $interface->setWarehouseSource($warehouseSource);
        $interface->setCodeName($codeName);
        $interface->setWarehouseCode($warehouseCode);

        $createAt = $order->getCreatedAt();
        $time = $this->timezone->date($order->getCreatedAt())->format('H:i:s');
        $date = $this->timezone->date($order->getCreatedAt())->format('Y-m-d');
        $timeslot = '';
        if ($logisticType == 1 || $logisticType == 4) {
            if (strtotime($time) > strtotime('13:30:00')) {
                $date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
            }
            $time = '17:00:00';
            $timeslot = $date . ' ' . $time;
        } elseif ($logisticType == 2) {
            if (strtotime($time) < strtotime('08:00:00')) {
                $time = '09:00:00';
            } elseif (strtotime($time) <= strtotime('15:30:00')) {
                $time = date('H:i:s', strtotime("+2 hour", strtotime($time)));
            } else {
                $time = '09:00:00';
                $date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
            }
            $timeslot = $date . ' ' . $time;
        } elseif ($logisticType == 3 || $shippingMethod == 'store_pickup_store_pickup') {
            if ($logisticType == 3) {
                $time = $order->getTime();
                $date = $this->timezone->date($order->getDate())->format('Y-m-d');
            } else {
                $time = $order->getStorePickUpDelivery();
                $date = $this->timezone->date($order->getStorePickUpTime())->format('Y-m-d');
            }
            $timeArray = explode("-", $time);
            $timeFrom = $timeArray[0];
            if (strpos($timeFrom, 'AM') !== false) {
                $timeFrom = str_replace("AM", "", $timeFrom);
                $timeFrom = preg_replace('/\s+/', '', $timeFrom);
                $timeFrom .= ':00';
                $timeFrom = date('H:i:s', strtotime("-1 hour", strtotime($timeFrom)));
            } else {
                $timeFrom = str_replace("PM", "", $timeFrom);
                $timeFrom = preg_replace('/\s+/', '', $timeFrom);
                $timeFrom .= ':00';
                $timeFrom = date('H:i:s', strtotime("+11 hour", strtotime($timeFrom)));
            }
            $timeslot = $date . ' ' . $timeFrom;
        }
        $interface->setTimeSlot($timeslot);
        return $interface;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getProvince($id)
    {
        $region = $this->regionFactory->create()->load($id);
        if ($region->getId()) {
            return $region->getName();
        }
        return '';
    }

    /**
     * @param int $id
     * @return string
     */
    public function getCityName($id)
    {
        $city = $this->cityFactory->create()->load($id);
        if ($city->getId()) {
            return $city->getCity();
        }
        return '';
    }

    /**
     * move item inactive to new quote
     * @param $firstOrder
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignInactiveItemsToNewQuote($firstOrder)
    {
        $quoteId = $firstOrder->getQuoteId();
        $customerId = $firstOrder->getCustomerId();
        try {
            $newQuote = $this->multiShipping->getQuote();
            if (empty($newQuote) || empty($newQuote->getId())) {
                throw new \Exception();
            }
            $newQuoteId = $this->multiShipping->getQuote()->getId();
        } catch (\Exception $e) {
            $this->cartManagement->createEmptyCartForCustomer($customerId);
            $newQuote = $this->cartManagement->getCartForCustomer($customerId);
            $newQuoteId = $newQuote->getId();
        }

        /**
         * @var \Magento\Quote\Model\Quote $quote
         */
        $quote = $this->quoteFactory->create()->load($quoteId);
        /**
         * @var \Magento\Quote\Model\Quote\Item $item
         */
        if ($quote->getIsVirtual()) {
            foreach ($quote->getItemsCollection() as $item) {
                if (!$item->getIsVirtual()) {
                    $item->setQuote($newQuote);
                    $item->setQuoteId($newQuoteId);
                    $item->save();
                }
            }
        } else {
            foreach ($quote->getItemsCollection() as $item) {
                if ($item->getIsActive() === null || $item->getIsActive() == 0) {
                    $item->setQuote($newQuote);
                    $item->setQuoteId($newQuoteId);
                    $item->save();
                }
            }
        }
        $newQuote->setTotalsCollectedFlag(false)->collectTotals();
        $this->cartRepository->save($newQuote);
    }

    /**
     * @return array
     */
    protected function getSampleWareHouse()
    {
        return [
            "store_code" => "DC001",
            "store_name" => "DC Tangerang Center",
            "store_address" => "Banten, Tanggerang, Cikokol, 15117",
            "email" => "customer_support@transmart.co.id",
            "province" => "Banten",
            "phone" => "628123456789",
            "city" => "Kota Tangerang",
            "district" => "Tangerang",
            "store_zipcode" => "15117",
            "latitude" => -6.207323,
            "longitude" => 106.6294,
            "code_name" => "ARI",
            "distance_radius" => 12
        ];
    }

    /**
     * @return bool
     */
    protected function byPassWareHouse()
    {
        return false;
    }
}
