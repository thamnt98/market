<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/23/20
 * Time: 5:55 PM
 */

namespace SM\Checkout\Model;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use SM\Checkout\Model\Split;

/**
 * Class Onepage
 * @package SM\Checkout\Plugin\Checkout\Block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CheckoutProviderHandle
{
    const STORE_PICK_UP  = 'store_pickup_store_pickup';
    const NOT_AVAILABLE  = 'transshipping_transshipping0';
    const DEFAULT_METHOD = 'transshipping_transshipping1';
    const SAME_DAY       = 'transshipping_transshipping2';
    const SCHEDULE       = 'transshipping_transshipping3';
    const NEXT_DAY       = 'transshipping_transshipping4';
    const DC             = 'transshipping_transshipping5';
    const TRANS_COURIER  = 'transshipping_transshipping6';

    protected $defaultLat;
    protected $defaultLng;
    protected $paymentFail = false;
    protected $defaultAddressId;
    protected $customer;
    protected $currentQuoteItems = [];
    protected $preSelectSingleShippingMethod = \SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD;
    protected $cartUpdate = false;
    protected $isVirtual = false;
    protected $notFulFillMessage;
    protected $currentItemsListId = [];
    protected $isAddressEachItem = false;
    protected $fulFill = true;
    protected $quote = false;
    protected $allChildItems = [];
    protected $allParentItems = [];
    protected $skuListForPickUp = [];
    protected $itemNotIsSpo = [];

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \SM\Checkout\Helper\DeliveryType
     */
    protected $deliveryType;

    /**
     * @var \SM\Checkout\Helper\Config
     */
    protected $helperConfig;

    /**
     * @var \Magento\Payment\Api\PaymentMethodListInterface
     */
    protected $paymentMethodList;

    /**
     * @var \Magento\Payment\Model\Method\InstanceFactory
     */
    protected $instanceFactory;

    /**
     * @var \Magento\Payment\Model\Checks\SpecificationFactory
     */
    protected $methodSpecificationFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    protected $merger;

    /**
     * @var \Trans\AllowLocation\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $currency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SM\Checkout\Model\MsiFullFill
     */
    protected $msiFullFill;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Split
     */
    protected $split;
    /**
     * @var \SM\MyVoucher\Api\RuleRepositoryInterface
     */
    protected $ruleRepositoryInterface;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \SM\MyVoucher\Helper\Data
     */
    protected $voucherHelper;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Onepage constructor.
     * @param \SM\MyVoucher\Helper\Data $voucherHelper
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \SM\Checkout\Helper\DeliveryType $deliveryType
     * @param \SM\Checkout\Helper\Config $helperConfig
     * @param \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList
     * @param \Magento\Payment\Model\Method\InstanceFactory $instanceFactory
     * @param \Magento\Payment\Model\Checks\SpecificationFactory $specificationFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $currency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Checkout\Model\MsiFullFill $msiFullFill
     * @param Cart $cart
     * @param Split $split
     * @param \SM\MyVoucher\Api\RuleRepositoryInterface $ruleRepositoryInterface
     * @param AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \SM\MyVoucher\Helper\Data $voucherHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \SM\Checkout\Helper\DeliveryType $deliveryType,
        \SM\Checkout\Helper\Config $helperConfig,
        \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList,
        \Magento\Payment\Model\Method\InstanceFactory $instanceFactory,
        \Magento\Payment\Model\Checks\SpecificationFactory $specificationFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Checkout\Model\MsiFullFill $msiFullFill,
        Cart $cart,
        Split $split,
        \SM\MyVoucher\Api\RuleRepositoryInterface $ruleRepositoryInterface,
        AddressRepositoryInterface $addressRepository,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->serializer = $serializer;
        $this->assetRepo = $assetRepo;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->deliveryType = $deliveryType;
        $this->helperConfig = $helperConfig;
        $this->paymentMethodList = $paymentMethodList;
        $this->instanceFactory = $instanceFactory;
        $this->methodSpecificationFactory = $specificationFactory;
        $this->quoteRepository = $quoteRepository;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
        $this->msiFullFill = $msiFullFill;
        $this->cart = $cart;
        $this->split = $split;
        $this->ruleRepositoryInterface = $ruleRepositoryInterface;
        $this->addressRepository = $addressRepository;
        $this->voucherHelper = $voucherHelper;
        $this->request = $request;
    }

    /**
     * @param \SM\Checkout\Block\Onepage $subject
     * @param $result
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle()
    {
        $this->handleQuoteItems();
        $data['msi'] = $this->getSourcesList();
        $data['delivery_type'] = $this->deliveryType->getDeliveryType();
        try {
            $imageUrl = $this->assetRepo->getUrlWithParams('images/info.svg', []);
        } catch (\Exception $e) {
            $imageUrl = '';
        }
        $data['viewFileUrl'] = $imageUrl;
        $data['limitAddress'] = (int)$this->helperConfig->getAddressLimit();
        $data['defaultShippingAddressId'] = $this->getDefaultShippingAddressId();
        $data['paymentMethods'] = $this->getPaymentMethods();
        $data['dateTime'] = $this->helperConfig->getDateTimeConfig();
        $data['canShowVoucher'] = $this->canShowVoucher();
        $data['weightUnit'] = $this->helperConfig->getWeightUnit();
        $data['voucher_list'] = $this->getVoucherList();
        $data['symbol'] = $this->currency->getCurrency()->getCurrencySymbol();
        $data['sortSource'] = $this->getSortSource();
        $data['fulFill'] = $this->fulFill;
        $data['notFulFillMessage'] = $this->notFulFillMessage;
        $data['latlng'] = ['lat' => $this->defaultLat, 'lng' => $this->defaultLng];
        $data['address_complete'] = $this->isAddressComplete();
        $preSelectQuoteAddress = $this->getQuoteAddressPreSelect($data['sortSource']);
        $data['pre_select_items'] = $preSelectQuoteAddress['pre_select_items'];
        $data['pre_select_address'] = $preSelectQuoteAddress['pre_select_address'];
        $data['pre_select_single_method'] = $this->preSelectSingleShippingMethod;
        $data['pre_select_order_shipping_type'] = $preSelectQuoteAddress['pre_select_order_shipping_type'];
        $preSelectAfterAddNewAddress = $this->getPreSelectAfterAddNewAddress($data['pre_select_address'], $data['pre_select_order_shipping_type']);
        if (!empty($preSelectAfterAddNewAddress)) {
            $data['pre_select_popup'] = $preSelectAfterAddNewAddress;
        }
        $data['payment_fail'] = ($this->cartUpdate) ? false : $this->paymentFail;
        $data['is_virtual'] = $this->isVirtual;
        $data['apiKey'] = $this->helperConfig->getSecretKey();
        if ($this->isVirtual) {
            $this->checkoutSession->setDigital(true);
        }
        if ($this->helperConfig->isActiveFulfillmentStore()) {
            $data['shipping_support'] = __('Transmart now delivers only to Jabodetabek area.');
            $data['shipping_address_notify'] = __('Please change your delivery address to %1.', '<strong>Jabodetabek area</strong>');
        }
        $data['is_address_each_items'] = $this->isAddressEachItem;
        $data['currentItemsListId'] = implode(",", $this->currentItemsListId);
        $data['itemNotIsSpo'] = array_values($this->itemNotIsSpo);
        return $data;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentMethods()
    {
        $quote = $this->getQuote();
        $this->paymentFail = $quote->getPaymentFailureTime();
        $this->isVirtual = $quote->getIsVirtual();
        $store = $quote ? $quote->getStoreId() : null;
        $methods = [];
        $logoUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        foreach ($this->paymentMethodList->getActiveList($store) as $method) {
            $methodInstance = $this->instanceFactory->create($method);
            if ($method->getTitle() && $methodInstance->isAvailable($quote) && $this->canUseMethod($methodInstance, $quote)) {
                $description = $this->helperConfig->getPaymentDescription($method->getCode());
                $tooltipDescription = $this->helperConfig->getPaymentTooltipDescription($method->getCode());
                $logo = "logo/paymentmethod/" . $this->helperConfig->getPaymentLogo($method->getCode());
                $methods[] = [
                    'code'  => $method->getCode(),
                    'title' => $method->getTitle(),
                    'storeId'   => $method->getStoreId(),
                    'isActive'  => $method->getIsActive(),
                    'description'   => $description,
                    'tooltip_description'   => $tooltipDescription,
                    'logo'      => $logoUrl . $logo
                ];
            }
        }
        return $methods;
    }

    /**
     * @param $method
     * @param $quote
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function canUseMethod($method, $quote)
    {
        $checks = [
            AbstractMethod::CHECK_USE_FOR_COUNTRY,
            AbstractMethod::CHECK_USE_FOR_CURRENCY,
            AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
            AbstractMethod::CHECK_ZERO_TOTAL
        ];

        return $this->methodSpecificationFactory->create($checks)->isApplicable(
            $method,
            $quote
        );
    }
    /**
     * Get All source list
     *
     * @return SourceInterface[]|null
     */
    protected function getSourcesList()
    {
        $data = [];
        $searchCriteria = $this->searchCriteriaBuilder->create();
        try {
            $sourceData = $this->sourceRepository->getList($searchCriteria);
            if ($sourceData->getTotalCount()) {
                foreach ($sourceData->getItems() as $item) {
                    $data[$item->getSourceCode()] = $item->getData();
                }
            }
        } catch (\Exception $e) {
            return [];
        }
        return $data;
    }

    /**
     * @return int|mixed
     */
    protected function getDefaultShippingAddressId()
    {
        if (!$this->defaultAddressId) {
            $this->defaultAddressId = $this->getCustomer()->getDefaultShippingAddress()->getId();
        }
        return $this->defaultAddressId;
    }

    /**
     * @return bool
     */
    public function canShowVoucher()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getVoucherList()
    {
        $data = [];
        $customerId = $this->getCustomer()->getId();
        $vouchers = $this->ruleRepositoryInterface->getVoucherByCustomer($customerId);
        try {
            $quote = $this->checkoutSession->getQuote();
        } catch (\Exception $e) {
            return [];
        }

        foreach ($vouchers as $voucher) {
            if (!$this->voucherHelper->validateRule($quote, $voucher->getId()) ||
                !$voucher->getAvailable()
            ) {
                continue;
            }

            $data[] = [
                'id'              => $voucher->getId(),
                'name'            => $voucher->getName(),
                'description'     => $voucher->getDescription() ? $voucher->getDescription() : $voucher->getName(),
                'discount_amount' => $voucher->getDiscountAmount(),
                'discount_type'   => $voucher->getDiscountType(),
                'how_to_use'      => $voucher->getHowToUse(),
                'term_condition'  => $voucher->getTermCondition(),
                'image'           => $this->voucherHelper->getVoucherImage($voucher),
                'use_left'        => $voucher->getUseLeft(),
                'available'       => $voucher->getAvailable(),
                'code'            => $voucher->getCode(),
                'from_date'       => $voucher->getFromDate(),
                'to_date'         => $voucher->getToDate(),
                'discount_text'   => $voucher->getDiscountText(),
                'discount_note'   => $voucher->getDiscountNote(),
                'area'            => $voucher->getArea(),
                'expired_txt'     => $this->voucherHelper->getToDateTxt($voucher),
                'time_left_txt'   => $this->voucherHelper->getTimeLeftTxt($voucher)
            ];
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getSortSource()
    {
        $defaultShipping = $this->getCustomer()->getDefaultShippingAddress();
        $this->defaultLat = $defaultShipping->getCustomAttribute('latitude') ? $defaultShipping->getCustomAttribute('latitude')->getValue() : 0;
        $this->defaultLng = $defaultShipping->getCustomAttribute('longitude') ? $defaultShipping->getCustomAttribute('longitude')->getValue() : 0;
        if (empty($this->skuListForPickUp)) {
            $this->fulFill = false;
            $this->notFulFillMessage = __('Sorry, pick-up method is not applicable for this order. Shop conveniently with our delivery.');
            return [];
        }
        $sourceList = $this->msiFullFill->getMsiFullFill($this->skuListForPickUp);
        if (empty($sourceList)) {
            $this->fulFill = false;
            $this->notFulFillMessage = __('Sorry, pick-up method is not applicable for this order. Shop conveniently with our delivery.');
            return [];
        }
        if ($this->defaultLat != 0 && $this->defaultLng != 0) {
            $defaultShipping->setLatitude($this->defaultLat)->setLongitude($this->defaultLng);
        }
        $defaultShipping->setCity($this->split->getCityName($defaultShipping->getCity()));
        return $this->msiFullFill->sortSourceByDistance($sourceList, $defaultShipping);
    }

    /**
     * @return bool
     */
    protected function isAddressComplete()
    {
        $defaultShipping = $this->getCustomer()->getDefaultShippingAddress();
        return ($defaultShipping->getStreetFull() == 'N/A' && $defaultShipping->getPostcode() == '*****') ? false : true;
    }

    /**
     * @param $sortSource
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getQuoteAddressPreSelect($sortSource)
    {
        $preSelect = true;
        $allCustomerAddresses = $this->getCustomerAddresses();
        $preSelectItems = [];
        if ($this->checkoutSession->getPreShippingType()) {
            if (empty($sortSource)) {
                $preSelectShippingType = '0';
            } else {
                $preSelectShippingType = $this->checkoutSession->getPreShippingType();
            }
        } else {
            $preSelectShippingType = '0';
        }
        if ($this->checkoutSession->getPreAddress()) {
            $preSelectAddressIds = $this->checkoutSession->getPreAddress();
        } else {
            $preSelectAddressIds = [];
        }
        foreach ($preSelectAddressIds as $key => $address) {
            if (!in_array($address, $allCustomerAddresses)) {
                unset($preSelectAddressIds[$key]);
            }
        }
        $quote = $this->getQuote();
        $allShippingQuoteAddress = $quote->getAllShippingAddresses();
        $countShippingQuoteAddress = count($allShippingQuoteAddress);
        if ($countShippingQuoteAddress > 1) {
            $this->preSelectSingleShippingMethod = \SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD;
        }
        foreach ($allShippingQuoteAddress as $address) {
            if (!$address->getShippingMethod() || $address->getShippingMethod() == '') {
                $address->setShippingMethod(\SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD);
            }
            if ($countShippingQuoteAddress == 1 && $address->getShippingMethod() != \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                $this->preSelectSingleShippingMethod = $address->getShippingMethod();
            }
            if ($this->preSelectSingleShippingMethod == self::DC) {
                $this->preSelectSingleShippingMethod = self::DEFAULT_METHOD;
            } elseif ($this->preSelectSingleShippingMethod == self::TRANS_COURIER) {
                $this->preSelectSingleShippingMethod = self::SAME_DAY;
            }
            $items = $this->getItemPreSelect($address, $allCustomerAddresses);
            $preSelectItems = $preSelectItems + $items;
        }

        if (!empty($this->currentQuoteItems)) {
            $preSelectItems = $this->addPreSelectToNewItems($preSelectItems);
        }
        if (empty($preSelectAddressIds)) {
            $preSelectAddressIds = [$this->getDefaultShippingAddressId()];
        }
        if (count($preSelectItems) == 1) {
            $preSelectData = reset($preSelectItems);
            if ($preSelectData['shipping_method'] == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                $preSelectShippingType = '1';
            } else {
                $preSelectShippingType = '0';
            }
            if (count($preSelectAddressIds) > 1) {
                $preSelectAddressIds = [$preSelectData['address']];
            }
        }

        if (($preSelect || $this->cartUpdate) && !$this->paymentFail) {
            $this->removeOldQuoteAddress($quote);
        }
        return ['pre_select_items' => $preSelectItems, 'pre_select_address' => $preSelectAddressIds, 'pre_select_order_shipping_type' => $preSelectShippingType];
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function removeOldQuoteAddress($quote)
    {
        return;
        $defaultShipping = $quote->getCustomer()->getDefaultShipping();
        $defaultCustomerAddress = $this->addressRepository->getById($defaultShipping);
        foreach ($quote->getAllShippingAddresses() as $address) {
            $quote->removeAddress($address->getId());
        }
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(false)->importCustomerAddressData($defaultCustomerAddress);
        $quote->getPayment()->setMethod(null);
        $quote->setData('service_fee', 0);
        $this->quoteRepository->save($quote);
    }

    /**
     * @param $quoteAddress
     * @param $allCustomerAddresses
     * @return array
     */
    protected function getItemPreSelect($quoteAddress, $allCustomerAddresses)
    {
        $items = [];
        $shippingMethod = $quoteAddress->getShippingMethod();
        if ($shippingMethod == self::DC) {
            $shippingMethod = self::DEFAULT_METHOD;
        } elseif ($shippingMethod == self::TRANS_COURIER) {
            $shippingMethod = self::SAME_DAY;
        }
        foreach ($quoteAddress->getAllVisibleItems() as $item) {
            if (!isset($this->currentQuoteItems[$item->getQuoteItemId()]) || $this->currentQuoteItems[$item->getQuoteItemId()]->getQty() != $item->getQty()) {
                $this->cartUpdate = true;
                continue;
            }
            unset($this->currentQuoteItems[$item->getQuoteItemId()]);
            $customerAddressId = $quoteAddress->getCustomerAddressId();
            if (!in_array($customerAddressId, $allCustomerAddresses)) {
                $customerAddressId = $this->getDefaultShippingAddressId();
            }
            if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                $type = '1';
            } else {
                $type = '0';
            }
            if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::SCHEDULE) {
                $isSchedule = true;
            } else {
                $isSchedule = false;
            }
            $items[$item->getQuoteItemId()] = [
                'address' => $customerAddressId,
                'shipping_method' => $shippingMethod,
                'type' => $type,
                'isSchedule' => $isSchedule
            ];
        }
        return $items;
    }

    /**
     * @param $preSelectItems
     * @return array
     */
    protected function addPreSelectToNewItems($preSelectItems)
    {
        $items = [];
        if (!empty($preSelectItems)) {
            $preSelectData = reset($preSelectItems);
        } else {
            $preSelectData = [
                'address' => $this->defaultAddressId,
                'shipping_method' => \SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD,
                'type' => '0',
                'isSchedule' => false
            ];
        }
        foreach ($this->currentQuoteItems as $itemId => $item) {
            $items[$itemId] = $preSelectData;
        }
        return $items + $preSelectItems;
    }

    /**
     * @return array
     */
    protected function getCustomerAddresses()
    {
        return $this->getCustomer()->getAddressCollection()->getAllIds();
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        if (!$this->customer) {
            $this->customer = $this->customerSession->getCustomer();
        }
        return $this->customer;
    }

    /**
     * @param $preSelectAddressList
     * @param $preSelectShippingType
     * @return array
     */
    protected function getPreSelectAfterAddNewAddress($preSelectAddressList, $preSelectShippingType)
    {
        $data = [];
        if ($preSelectShippingType != '1') {
            $currentAddressId = $this->checkoutSession->getCurrentAddressId();
            $currentAction = $this->checkoutSession->getCurrentAction();
            if (in_array($currentAddressId, $preSelectAddressList) && in_array($currentAction, $this->actionList())) {
                $data['current_address_id'] = $currentAddressId;
                $data['current_action'] = $currentAction;
            }
        }
        $this->checkoutSession->unsCurrentAddressId();
        $this->checkoutSession->unsCurrentAction();
        return $data;
    }

    /**
     * @return string[]
     */
    protected function actionList()
    {
        return ['add', 'change'];
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function handleQuoteItems()
    {
        $allChildItemsId = [];
        $quote = $this->getQuote();
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $this->allChildItems[$item->getParentItemId()][] = $item;
                $allChildItemsId[$item->getParentItemId()][] = $item->getId();
            }
        }
        $totalWeight = 0;
        $totalQty = 0;
        $totalPrice = 0;
        $orderToSendOar = ['items' => []];
        $allItemsSkuList = [];
        $allParentItemsId = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $this->currentQuoteItems[$item->getId()] = $item;
            $this->currentItemsListId[] = $item->getId();
            $rowTotal = (int) $item->getRowTotal();
            $totalPrice += $rowTotal;
            if (isset($allChildItems[$item->getItemId()])) {
                foreach ($this->allChildItems[$item->getItemId()] as $itemData) {
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
        $spoSku = $this->buildOarDataToCheckIsSpo($orderToSendOar);
        $allItemsSkuListCheckSpo = $allItemsSkuList;
        $allParentItemsIsSpo = $allParentItemsId;
        foreach ($allItemsSkuListCheckSpo as $itemId => $itemData) {
            if (!isset($allItemsSkuList[$itemId])) {
                continue;
            }
            $itemSku = $itemData['sku'];
            if (!in_array($itemSku, $spoSku)) {
                $parentItemId = $allItemsSkuList[$itemId]['parent_id'];
                unset($allParentItemsIsSpo[$parentItemId]);
                if (isset($allChildItemsId[$parentItemId])) {
                    foreach ($allChildItemsId[$parentItemId] as $childItemId) {
                        unset($allItemsSkuList[$childItemId]);
                    }
                }
            }
        }
        foreach ($allItemsSkuList as $item) {
            if (isset($this->skuListForPickUp[$item['sku']])) {
                $this->skuListForPickUp[$item['sku']] += $item['qty'];
            } else {
                $this->skuListForPickUp[$item['sku']] = $item['qty'];
            }
        }
        if (empty($allParentItemsIsSpo)) {
            $this->fulFill = false;
            $this->notFulFillMessage = __('Sorry, pick-up method is not applicable for this order. Shop conveniently with our delivery.');
        } elseif (count($allParentItemsId) != count($allParentItemsIsSpo)) {
            $this->fulFill = false;
            $this->notFulFillMessage = __('Sorry, some items are not available for pick-up. We have more delivery options for you, try them out!');
        }
        $this->itemNotIsSpo = $allParentItemsIsSpo;
    }

    /**
     * @param $orderToSendOar
     * @param $checkIsSpo
     * @return array
     */
    protected function buildOarDataToCheckIsSpo($orderToSendOar)
    {
        $orderToSendOar['order_id'] = $this->getCustomer()->getId();
        $orderToSendOar['merchant_code'] = $this->split->getMerchantCode();
        $defaultAddress = $this->getCustomer()->getDefaultShippingAddress();
        try {
            $regionId = $defaultAddress->getRegionId();
            $province = $this->split->getProvince($regionId);
            $district = $defaultAddress->getCustomAttribute('district') ? $defaultAddress->getCustomAttribute('district')->getValue() : '';
            $district = $this->split->getDistrictName($district);
            $lat = $defaultAddress->getCustomAttribute('latitude') ? $defaultAddress->getCustomAttribute('latitude')->getValue() : 0;
            $long = $defaultAddress->getCustomAttribute('longitude') ? $defaultAddress->getCustomAttribute('longitude')->getValue() : 0;
            $city = $this->split->getCityName($defaultAddress->getCity());
        } catch (\Exception $e) {
            return [];
        }
        $orderToSendOar['destination'] = [
            "address" => $defaultAddress->getStreetFull(),
            "province" => $province,
            "city" => $city,
            "district" => $district,
            "postcode" => $defaultAddress->getPostcode(),
            "latitude" => (float)$lat,
            "longitude" => (float)$long
        ];
        $orderToSendOar['items'] = array_values($orderToSendOar['items']);
        $orderToSendOar['quote_address_id'] = 'spo';
        $response = $this->split->getOarResponse([$orderToSendOar]);
        if (!is_array($response) || isset($response['error']) || !isset($response['content'])) {
            return [];
        }
        $spo = [];
        foreach ($response['content'] as $data) {
            if (!isset($data['data']) || !isset($data['data']['is_spo']) || $data['data']['is_spo'] || !isset($data['data']['items'])) {
                continue;
            }
            foreach ($data['data']['items'] as $item) {
                $spo[] = $item['sku'];
            }
        }
        return $spo;
    }
}
