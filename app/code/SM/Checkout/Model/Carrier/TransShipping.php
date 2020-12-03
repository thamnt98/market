<?php

namespace SM\Checkout\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;

class TransShipping extends AbstractCarrier implements CarrierInterface
{
    protected $isSpo = false;
    protected $postCode;
    protected $districtId;

    /**
     * @var int
     */
    protected $customerId = 0;

    /**
     * @var string
     */
    protected $_code = 'transshipping';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \SM\Checkout\Model\Split
     */
    protected $split;

    /**
     * @var \SM\Checkout\Helper\Config
     */
    protected $helperConfig;

    /**
     * TransShipping constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \SM\Checkout\Model\Split $split
     * @param \SM\Checkout\Helper\Config $helperConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \SM\Checkout\Model\Split $split,
        \SM\Checkout\Helper\Config $helperConfig,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->serializer = $serializer;
        $this->regionFactory = $regionFactory;
        $this->addressRepository = $addressRepository;
        $this->split = $split;
        $this->helperConfig = $helperConfig;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active') || $request->getPreShippingMethod() == 'transshipping_transshipping') {
            return false;
        }
        $shippingMethodList = $this->getShippingMethodList($request);
        if (!$shippingMethodList) {
            return false;
        }
        return $this->appearShippingMethod($shippingMethodList);
    }

    /**
     * @param $shippingMethodList
     * @return bool|Result
     */
    public function appearShippingMethod($shippingMethodList)
    {
        $listMethodName = $this->split->getListMethodName();
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();
        if (empty($shippingMethodList)) {
            return false;
        }
        foreach ($shippingMethodList as $code => $fee) {
            if (!isset($listMethodName[$code])) {
                continue;
            }

            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code . $code);
            $method->setMethodTitle($listMethodName[$code]);

            $method->setPrice($fee);
            $method->setCost($fee);

            $result->append($method);
        }
        return $result;
    }

    /**
     * @param RateRequest $request
     * @return array|bool
     */
    public function getShippingMethodList($request)
    {
        $shippingMethodList = [];
        // get data from quote address
        $data = $this->prepareOrderMultiAllocation($request);
        if (!$data
            || $data[0]['destination']['district'] == ''
            || !$this->split->checkShippingPostCode($this->postCode)
            || $data[0]['destination']['latitude'] == 0
            || $data[0]['destination']['longitude'] == 0
        ) {
            return false;
        }
        $response = $this->split->getOarResponse($data);
        if (!is_array($response) || isset($response['error']) || !isset($response['content'])) {
            return false;
        }
        foreach ($response['content'] as $data) {
            foreach ($data['data']['shipping_list'] as $shipping) {
                if (
                    isset($shipping['courier']['reason']) && $shipping['courier']['fee']['base'] == 0
                ) {
                    continue;
                }
                $shippingMethodList[$shipping['service_type']] = $shipping['courier']['fee']['total'];
            }
            break;
        }
        return $shippingMethodList;
    }

    /**
     * @param $request
     * @return array[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function prepareOrderMultiAllocation($request)
    {
        $quote = $request->getQuote();
        $items = [];
        $customerAddressId = $request->getCustomerAddressId();
        $cityId = $request->getDestCity();
        $city = $this->split->getCityName($cityId);
        $regionId = $request->getDestRegionId();
        $province = $this->split->getProvince($regionId);
        $parent = [];
        $all = [];
        $totalPrice = 0;
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $parent[$item->getParentItemId()][] = $item;
            }
            $all[$item->getId()] = $item;
        }
        foreach ($request->getAllItems() as $item) {
            $itemId = ($item->getQuoteItemId()) ? $item->getQuoteItemId() : $item->getId();
            if (isset($parent[$itemId])) {
                $rowTotal = (int)$all[$itemId]->getRowTotal();
                $totalPrice += $rowTotal;
                foreach ($parent[$itemId] as $childItem) {
                    $product = $childItem->getProduct();
                    $ownCourier = $product->getData('own_courier');
                    if ($ownCourier) {
                        $ownCourier = true;
                    } else {
                        $ownCourier = false;
                    }
                    $sku = $childItem->getSku();
                    $childQty = (int)$childItem->getQty() * (int)$item->getQty();
                    $price = ((int)$childItem->getPrice() != 0) ? (int)$childItem->getPrice() : (int)$childItem->getProduct()->getPrice();
                    if (isset($items[$sku])) {
                        $items[$sku]['quantity'] += (int)$childItem->getQty() * (int)$item->getQty();
                    } else {
                        $items[$sku] = [
                            "sku" => $sku,
                            'sku_basic' => $sku,
                            "quantity" => $childQty,
                            'price' => $price,
                            'weight' => (int)$childItem->getWeight(),
                            'is_spo' => false,
                            'is_own_courier' => $ownCourier
                        ];
                    }
                    if ($rowTotal == 0) {
                        $rowTotal = $price * $childQty;
                        $totalPrice += $rowTotal;
                    }
                }
            } elseif (isset($all[$itemId]) && !$all[$itemId]->getParentItemId()) {
                $itemsTrue = $all[$itemId];
                $rowTotal = (int)$itemsTrue->getRowTotal();
                $totalPrice += $rowTotal;
                $product = $itemsTrue->getProduct();
                $ownCourier = $product->getData('own_courier');
                if ($ownCourier) {
                    $ownCourier = true;
                } else {
                    $ownCourier = false;
                }
                $sku = $itemsTrue->getSku();
                $price = ((int)$itemsTrue->getPrice() != 0) ? (int)$itemsTrue->getPrice() : (int)$itemsTrue->getProduct()->getPrice();
                if (isset($items[$sku])) {
                    $items[$sku]['quantity'] += (int)$itemsTrue->getQty();
                } else {
                    $items[$sku] = [
                        "sku" => $sku,
                        'sku_basic' => $sku,
                        "quantity" => (int)$itemsTrue->getQty(),
                        'price' => $price,
                        'weight' => (int)$itemsTrue->getWeight(),
                        'is_spo' => false,
                        'is_own_courier' => $ownCourier
                    ];
                }
                if ($rowTotal == 0) {
                    $rowTotal = $price * (int)$itemsTrue->getQty();
                    $totalPrice += $rowTotal;
                }
            }
        }
        try {
            $address = $this->addressRepository->getById($customerAddressId);
            $this->postCode = $request->getDestPostcode();
            $this->districtId = $address->getCustomAttribute('district') ? $address->getCustomAttribute('district')->getValue() : '';
            $district = $this->split->getDistrictName($this->districtId);
            $lat = $address->getCustomAttribute('latitude') ? $address->getCustomAttribute('latitude')->getValue() : 0;
            $long = $address->getCustomAttribute('longitude') ? $address->getCustomAttribute('longitude')->getValue() : 0;
            $this->customerId = $address->getCustomerId();
        } catch (\Exception $e) {
            $lat = 0;
            $long = 0;
            $district = '';
        }
        $resetItems = [];
        foreach ($items as $item) {
            $resetItems[] = $item;
        }
        $data = [
            "order_id" => $this->customerId,
            "merchant_code" => $this->split->getMerchantCode(),
            "destination" => [
                "address" => $request->getDestStreet(),
                "province" => $province,
                "city" => $city,
                "district" => $district,
                "postcode" => $request->getDestPostcode(),
                "latitude" => (float)$lat,
                "longitude" => (float)$long,
            ],
            "items" => $resetItems,
            "total_weight" => (int)$request->getPackageWeight(),
            "total_price" => (int)$totalPrice,
            "total_qty" => (int)$request->getPackageQty()
        ];
        if ((bool)$this->helperConfig->isEnableOarLog()) {
            $data["quote_address_id"] = $quote->getId() . '-' . $request->getQuoteAddressId();
        }
        return [$data];
    }
}
