<?php
declare(strict_types=1);

namespace SM\Checkout\Model;

use Magento\Framework\Webapi\Exception as HttpException;
use SM\Checkout\Api\Data\Checkout\CheckoutDataInterface;
use SM\DigitalProduct\Api\Data\DigitalInterface;
use SM\GTM\Model\BasketFactory;

/**
 * @api
 */
class MultiShippingMobile implements \SM\Checkout\Api\MultiShippingMobileInterface
{
    const CHECK_USE_FOR_COUNTRY = 'country';

    const CHECK_USE_FOR_CURRENCY = 'currency';

    const CHECK_ORDER_TOTAL_MIN_MAX = 'total';

    const CHECK_ZERO_TOTAL = 'zero_total';

    protected $supportShippingData;
    protected $disablePickUp = false;
    protected $itemSelectShippingAddressId = [];
    protected $errorCheckout = false;
    protected $errorCheckoutByAddress = false;
    protected $rebuild = false;
    protected $notSpoList = false;
    /**
     * @var bool
     */
    protected $updateItem = false;

    /**
     * @var bool
     */
    protected $removeItem = false;

    /**
     * @var bool
     */
    protected $cartEmpty = false;

    /**
     * @var string
     */
    protected $currencySymbol = '';

    /**
     * @var array
     */
    protected $itemOption = [];

    /**
     * @var array
     */
    protected $requestItems = [];

    /**
     * @var \Magento\Catalog\Helper\Product\ConfigurationPool
     */
    protected $configurationPool;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Quote\Api\CartItemRepositoryInterface
     */
    protected $quoteItemRepository;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;

    /**
     * @var \SM\Checkout\Helper\Config
     */
    protected $checkoutHelperConfig;

    /**
     * @var MultiShippingHandle
     */
    protected $multiShippingHandle;

    /**
     * @var Checkout\Type\Multishipping
     */
    protected $multiShipping;

    /**
     * @var Split
     */
    protected $split;

    /**
     * @var MsiFullFill
     */
    protected $msiFullFill;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\CheckoutDataInterfaceFactory
     */
    protected $checkoutDataInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterfaceFactory
     */
    protected $itemInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\ShippingMethodInterfaceFactory
     */
    protected $shippingMethodInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory
     */
    protected $previewOrderInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterfaceFactory
     */
    protected $itemAdditionalInfoInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterfaceFactory
     */
    protected $deliveryInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterfaceFactory
     */
    protected $additionalInfoInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterfaceFactory
     */
    protected $storePickUpInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\ConfigInterfaceFactory
     */
    protected $configInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterfaceFactory
     */
    protected $pickUpConfigInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterfaceFactory
     */
    protected $deliveryConfigInterfaceFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Api\PaymentMethods
     */
    private $paymentMethods;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterfaceFactory
     */
    protected $searchStoreResponseFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterfaceFactory
     */
    protected $productOptionsInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory
     */
    protected $installationFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    private $couponModel;
    /**
     * @var \SM\MyVoucher\Model\RuleRepository
     */
    private $ruleRepository;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;
    /**
     * @var \SM\GTM\Model\ResourceModel\Basket\CollectionFactory
     */
    private $basketCollectionFactory;
    /**
     * @var BasketFactory
     */
    private $basketFactory;

    /**
     * @var \SM\Checkout\Api\VoucherInterface
     */
    protected $voucherInterface;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterfaceFactory
     */
    protected $voucherFactoryInterface;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterfaceFactory
     */
    protected $digitalInterfaceFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var \SM\GTM\Block\Product\ListProduct
     */
    private $productGtm;
    /**
     * @var \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory
     */
    private $gtmCart;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \SM\Checkout\Helper\DeliveryType
     */
    protected $deliveryType;

    /**
     * @var \SM\FreshProductApi\Helper\Fresh
     */
    protected $fresh;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\SupportShippingInterfaceFactory
     */
    protected $supportShippingInterfaceFactory;

    /**
     * @var \SM\Help\Model\QuestionRepository
     */
    private $questionRepository;

    /**
     * @var \SM\Help\Api\Data\QuestionInterfaceFactory
     */
    private $questionFactory;

    /**
     * @var \SM\MobileApi\Model\Product\Common\Installation
     */
    public $productInstallation;

    /**
     * @var \SM\Checkout\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * MultiShippingMobile constructor.
     * @param \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory
     * @param BasketFactory $basketFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\SalesRule\Model\Coupon $couponModel
     * @param \SM\MyVoucher\Model\RuleRepository $ruleRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository
     * @param \SM\Checkout\Helper\Config $checkoutHelperConfig
     * @param MultiShippingHandle $multiShippingHandle
     * @param Checkout\Type\Multishipping $multiShipping
     * @param Split $split
     * @param MsiFullFill $msiFullFill
     * @param \SM\Checkout\Api\Data\Checkout\CheckoutDataInterfaceFactory $checkoutDataInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterfaceFactory $additionalInfoInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterfaceFactory $storePickUpInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\ConfigInterfaceFactory $configInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterfaceFactory $pickUpConfigInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterfaceFactory $deliveryConfigInterfaceFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param Api\PaymentMethods $paymentMethods
     * @param \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterfaceFactory $searchStoreResponseFactory
     * @param \SM\Checkout\Api\VoucherInterface $voucherInterface
     * @param \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterfaceFactory $voucherFactoryInterface
     * @param \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterfaceFactory $digitalInterfaceFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \SM\Checkout\Helper\DeliveryType $deliveryType
     * @param \SM\Checkout\Api\Data\Checkout\SupportShippingInterfaceFactory $supportShippingInterfaceFactory
     * @param \SM\Help\Model\QuestionRepository $questionRepository
     * @param \SM\Checkout\Helper\Payment $paymentHelper
     */
    public function __construct(
        \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory,
        BasketFactory $basketFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\SalesRule\Model\Coupon $couponModel,
        \SM\MyVoucher\Model\RuleRepository $ruleRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
        \SM\Checkout\Helper\Config $checkoutHelperConfig,
        \SM\Checkout\Model\MultiShippingHandle $multiShippingHandle,
        \SM\Checkout\Model\Checkout\Type\Multishipping $multiShipping,
        \SM\Checkout\Model\Split $split,
        \SM\Checkout\Model\MsiFullFill $msiFullFill,
        \SM\Checkout\Api\Data\Checkout\CheckoutDataInterfaceFactory $checkoutDataInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterfaceFactory $additionalInfoInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterfaceFactory $storePickUpInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\ConfigInterfaceFactory $configInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterfaceFactory $pickUpConfigInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterfaceFactory $deliveryConfigInterfaceFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \SM\Checkout\Model\Api\PaymentMethods $paymentMethods,
        \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterfaceFactory $searchStoreResponseFactory,
        \SM\Checkout\Api\VoucherInterface $voucherInterface,
        \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterfaceFactory $voucherFactoryInterface,
        \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterfaceFactory $digitalInterfaceFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \SM\Checkout\Helper\DeliveryType $deliveryType,
        \SM\Checkout\Api\Data\Checkout\SupportShippingInterfaceFactory $supportShippingInterfaceFactory,
        \SM\Help\Model\QuestionRepository $questionRepository,
        \SM\Checkout\Helper\Payment $paymentHelper
    ) {
        $this->questionRepository = $questionRepository;
        $this->customerFactory = $customerFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->addressRepository = $addressRepository;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->checkoutHelperConfig = $checkoutHelperConfig;
        $this->multiShippingHandle = $multiShippingHandle;
        $this->multiShipping = $multiShipping;
        $this->split = $split;
        $this->msiFullFill = $msiFullFill;
        $this->checkoutDataInterfaceFactory = $checkoutDataInterfaceFactory;
        $this->additionalInfoInterfaceFactory = $additionalInfoInterfaceFactory;
        $this->storePickUpInterfaceFactory = $storePickUpInterfaceFactory;
        $this->configInterfaceFactory = $configInterfaceFactory;
        $this->pickUpConfigInterfaceFactory = $pickUpConfigInterfaceFactory;
        $this->deliveryConfigInterfaceFactory = $deliveryConfigInterfaceFactory;
        $this->quoteRepository = $quoteRepository;
        $this->paymentMethods = $paymentMethods;
        $this->searchStoreResponseFactory = $searchStoreResponseFactory;
        $this->voucherInterface = $voucherInterface;
        $this->voucherFactoryInterface = $voucherFactoryInterface;
        $this->digitalInterfaceFactory = $digitalInterfaceFactory;
        $this->registry = $registry;
        $this->couponModel = $couponModel;
        $this->ruleRepository = $ruleRepository;
        $this->date = $date;
        $this->basketCollectionFactory = $basketCollectionFactory;
        $this->basketFactory = $basketFactory;
        $this->priceCurrency = $priceCurrency;
        $this->deliveryType = $deliveryType;
        $this->supportShippingInterfaceFactory = $supportShippingInterfaceFactory;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function initCheckout($customerId, $cartId)
    {
        if ($this->registry->registry("remove_cart_item")) {
            $this->registry->unregister("remove_cart_item");
        }
        try {
            /** @var \Magento\Quote\Api\Data\CartInterface $quote */
            $quote = $this->quoteRepository->get($cartId);
        } catch (\Exception $e) {
            throw new HttpException(__('Your cart is empty. Pls add more item!'), 0, HttpException::HTTP_NOT_FOUND);
        }
        if ($quote->getIsVirtual()) {
            return $this->handleCheckoutDigital($cartId, $quote, $customerId);
        }
        $allVisibleItems = $quote->getAllVisibleItems();
        if (empty($allVisibleItems)) {
            throw new HttpException(__('There are not any products to checkout. Pls add more item!'), 0, HttpException::HTTP_NOT_FOUND);
        }

        $customer = $this->customerFactory->create()->load($customerId);
        $checkoutSession = $this->multiShipping;
        $checkoutSession->setQuote($quote);
        $checkoutSession->setCustomer($customer->getDataModel());
        $defaultShippingAddress = $customer->getDefaultShippingAddress();
        if ($defaultShippingAddress) {
            $defaultShippingAddressId = $defaultShippingAddress->getId();
            $isAddressComplete = ($defaultShippingAddress->getStreetFull() == 'N/A'
                && $defaultShippingAddress->getPostcode() == '*****') ? false : true;
            $defaultShippingMethod = \SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD;
        } else {
            // For case when customer init checkout with no default address
            $defaultShippingAddressId = 0;
            $isAddressComplete = false;
            $defaultShippingMethod = \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP;
        }
        $weightUnit = $this->checkoutHelperConfig->getWeightUnit();
        $storeId = $quote->getStoreId();
        $currencySymbol = $this->checkoutHelperConfig->getCurrencySymbol();
        $storePickUp = $this->storePickUpInterfaceFactory->create();
        $additionalInfo = $this->additionalInfoInterfaceFactory->create()->setStorePickUp($storePickUp);
        $addressSelectedId = [$defaultShippingAddressId];
        $this->itemSelectShippingAddressId = $addressSelectedId;
        $addressesList = $this->getAddressSelectedList($customerId, $addressSelectedId);
        $orderToSendOar['order_id'] = $quote->getCustomerId();
        $orderToSendOar['merchant_code'] = $this->split->getMerchantCode();

        if ($defaultShippingAddress) {
            $this->getSkuListForPickUp($quote, $defaultShippingAddress);
        } else {
            $this->notSpoList = [];
        }

        foreach ($allVisibleItems as $quoteItem) {
            $this->buildItemsInit($quoteItem, $quote, $defaultShippingMethod, $weightUnit, $currencySymbol, $storeId, $defaultShippingAddressId);
        }

        return $this->handleCheckoutData(
            $cartId,
            $this->requestItems,
            $additionalInfo,
            $customer,
            $checkoutSession,
            $isAddressComplete,
            $addressesList,
            $this->paymentMethods->getMethodsAvailable($quote, $customerId),
            true
        );
    }

    /**
     * @param $quote
     * @param $defaultShippingAddress
     */
    public function getSkuListForPickUp($quote, $defaultShippingAddress)
    {
        $orderToSendOar['order_id'] = (string)$quote->getCustomerId();
        $orderToSendOar['merchant_code'] = $this->split->getMerchantCode();
        $orderToSendOar['quote_address_id'] = 'spo';
        try {
            $regionId = $defaultShippingAddress->getRegionId();
            $province = $this->split->getProvince($regionId);
            $district = $defaultShippingAddress->getCustomAttribute('district') ? $defaultShippingAddress->getCustomAttribute('district')->getValue() : '';
            $district = $this->split->getDistrictName($district);
            $lat = $defaultShippingAddress->getCustomAttribute('latitude') ? $defaultShippingAddress->getCustomAttribute('latitude')->getValue() : 0;
            $long = $defaultShippingAddress->getCustomAttribute('longitude') ? $defaultShippingAddress->getCustomAttribute('longitude')->getValue() : 0;
            $city = $this->split->getCityName($defaultShippingAddress->getCity());
            $orderToSendOar['destination'] = [
                "address" => $defaultShippingAddress->getStreetFull(),
                "province" => $province,
                "city" => $city,
                "district" => $district,
                "postcode" => $defaultShippingAddress->getPostcode(),
                "latitude" => (float)$lat,
                "longitude" => (float)$long
            ];
            $this->notSpoList = $this->multiShippingHandle->getSkuListForPickUp($quote, $orderToSendOar, true);
        } catch (\Exception $e) {
            $this->notSpoList = [];
        }
    }
    /**
     * @param $quoteItem
     * @param $quote
     * @param $defaultShippingMethod
     * @param $weightUnit
     * @param $currencySymbol
     * @param $storeId
     * @param $defaultShippingAddressId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    protected function buildItemsInit($quoteItem, $quote, $defaultShippingMethod, $weightUnit, $currencySymbol, $storeId, $defaultShippingAddressId)
    {
        $this->requestItems[$quoteItem->getId()] = $this->multiShippingHandle->buildQuoteItemForMobile(
            $this->notSpoList,
            $quoteItem,
            $defaultShippingMethod,
            [],
            $weightUnit,
            $currencySymbol,
            $storeId,
            $defaultShippingAddressId,
            $quote->getApplyVoucher(),
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStorePickUpSourceFullFill($customerId, $cartId)
    {
        $interface = $this->searchStoreResponseFactory->create();
        $customer = $this->customerFactory->create()->load($customerId);
        $quote = $this->quoteRepository->get($cartId);
        $defaultShipping = $customer->getDefaultShippingAddress();
        $orderToSendOar['order_id'] = (string)$customerId;
        $orderToSendOar['merchant_code'] = $this->split->getMerchantCode();
        try {
            $regionId = $defaultShipping->getRegionId();
            $province = $this->split->getProvince($regionId);
            $district = $defaultShipping->getCustomAttribute('district') ? $defaultShipping->getCustomAttribute('district')->getValue() : '';
            $district = $this->split->getDistrictName($district);
            $lat = $defaultShipping->getCustomAttribute('latitude') ? $defaultShipping->getCustomAttribute('latitude')->getValue() : 0;
            $long = $defaultShipping->getCustomAttribute('longitude') ? $defaultShipping->getCustomAttribute('longitude')->getValue() : 0;
            if ($lat != 0 && $long != 0) {
                $defaultShipping->setLatitude($lat)->setLongitude($long);
            }
            $city = $this->split->getCityName($defaultShipping->getCity());
            $defaultShipping->setCity($city);
        } catch (\Exception $e) {
            return [];
        }
        $orderToSendOar['destination'] = [
            "address" => $defaultShipping->getStreetFull(),
            "province" => $province,
            "city" => $city,
            "district" => $district,
            "postcode" => $defaultShipping->getPostcode(),
            "latitude" => (float)$lat,
            "longitude" => (float)$long
        ];
        $orderToSendOar['quote_address_id'] = 'spo';
        $skuList = $this->multiShippingHandle->getSkuListForPickUp($quote, $orderToSendOar, false);
        $sourceList = $this->msiFullFill->getMsiFullFill($skuList);
        if (empty($sourceList)) {
            $interface->setCurrentStoreFulFill(false);
            return $interface;
        }
        $storeList = $this->msiFullFill->sortSourceByDistanceMobile($sourceList, $defaultShipping);
        $interface->setStoreList($storeList['store_list']);
        if (isset($storeList['store_list'][0])) {
            $interface->setCurrentStore($storeList['store_list'][0]);
            $interface->setCurrentStoreFulFill($storeList['current_store_fulfill']);
        }
        return $interface;
    }

    /**
     * {@inheritdoc}
     */
    public function saveShippingItems($shippingAddress, $items, $additionalInfo, $customerId, $cartId)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $quote = $this->quoteRepository->get($cartId);
        $checkoutSession = $this->multiShipping;
        $checkoutSession->setQuote($quote);
        $checkoutSession->setCustomer($customer->getDataModel());
        $defaultShippingAddress = $customer->getDefaultShippingAddress();
        $isAddressComplete = ($defaultShippingAddress->getStreetFull() == 'N/A'
            && $defaultShippingAddress->getPostcode() == '*****') ? false : true;
        $addressSelectedId = $this->getAddressSelectedId($shippingAddress);
        $addressesList = $this->getAddressSelectedList($customerId, $addressSelectedId);

        return $this->handleCheckoutData(
            $cartId,
            $items,
            $additionalInfo,
            $customer,
            $checkoutSession,
            $isAddressComplete,
            $addressesList,
            $this->paymentMethods->getMethodsAvailable($quote, $customerId),
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function previewOrder(
        $shippingAddress,
        $items,
        $additionalInfo,
        $isStoreFulFill,
        $isSplitOrder,
        $isAddressComplete,
        $isErrorCheckout,
        $voucher,
        $showEachItems,
        $disablePickUp,
        $customerId,
        $cartId
    ) {
        $this->disablePickUp = $disablePickUp;
        $customer = $this->customerFactory->create()->load($customerId);
        $quote = $this->quoteRepository->get($cartId);
        $checkoutSession = $this->multiShipping;
        $checkoutSession->setQuote($quote);
        $checkoutSession->setCustomer($customer->getDataModel());
        $format = $this->multiShippingHandle->itemsFormat($items);
        $itemsFormat = $format['item_format'];
        $deliveryDateTime = [];
        foreach ($itemsFormat as $quoteItemId => $item) {
            if ($item['shipping_method'] == \SM\Checkout\Model\MultiShippingHandle::SCHEDULE) {
                $deliveryDateTime[$item['shipping_address']] = $item['delivery'];
            }
        }
        $additionalInfoFormat = $this->multiShippingHandle->storePickUpFormat($additionalInfo);
        $storeDateTime = $additionalInfoFormat['store_pick_up'];
        $this->multiShippingHandle->handlePreviewOrder($deliveryDateTime, $storeDateTime, $quote);

        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote, false, $this->notSpoList);
        $addressSelectedId = $this->getAddressSelectedId($shippingAddress);
        $quote->collectTotals();
        $this->quoteRepository->save($quote);
        $data = [
            CheckoutDataInterface::SHIPPING_ADDRESS => $this->getAddressSelectedList($customerId, $addressSelectedId),
            CheckoutDataInterface::ITEMS => $items,
            CheckoutDataInterface::ITEMS_MESSAGE => '',
            CheckoutDataInterface::ADDITIONAL_INFO => $additionalInfo,
            CheckoutDataInterface::PREVIEW_ORDER => $previewOrderProcess['preview_order'],
            CheckoutDataInterface::CHECKOUT_TOTAL => $this->cartTotalRepository->get($cartId),
            CheckoutDataInterface::IS_STORE_FULFILL => $isStoreFulFill,
            CheckoutDataInterface::IS_SPLIT_ORDER => ($this->checkoutHelperConfig->showOrderSummary()) ? true : $isSplitOrder,
            CheckoutDataInterface::IS_ADDRESS_COMPLETE => $isAddressComplete,
            CheckoutDataInterface::IS_ERROR_CHECKOUT => $isErrorCheckout,
            CheckoutDataInterface::PAYMENT_METHODS => $this->paymentMethods->getMethodsAvailable($quote, $customerId),
            CheckoutDataInterface::VOUCHER => $voucher,
            CheckoutDataInterface::CURRENCY_SYMBOL => $this->getCurrencySymbol(),
            CheckoutDataInterface::DIGITAL_CHECKOUT => ($quote->getIsVirtual()) ? true : false,
            CheckoutDataInterface::DIGITAL_DETAIL => ($quote->getIsVirtual()) ? $this->getDigitalDetail($quote) : [],
            CheckoutDataInterface::BASKET_ID => $this->getBasketId($customerId),
            CheckoutDataInterface::BASKET_VALUE => $quote->getGrandTotal(),
            CheckoutDataInterface::BASKET_QTY => $quote->getItemsQty(),
            CheckoutDataInterface::SHOW_EACH_ITEMS => $showEachItems,
            CheckoutDataInterface::DISABLE_PICK_UP => $this->disablePickUp
        ];

        return $this->getCheckoutData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTimeConfig()
    {
        $storePickUpConfig = $this->checkoutHelperConfig->getDateTimeConfig();
        $storePickUpConfigObject = $this->pickUpConfigInterfaceFactory->create()
            ->setDateLimit($storePickUpConfig['date_limit'])
            ->setTodayStartTime($storePickUpConfig['today_time_start'])
            ->setNextStartTime($storePickUpConfig['nextday_time_start'])
            ->setEndTime($storePickUpConfig['time_end']);
        $DeliveryConfig = $this->checkoutHelperConfig->getDeliveryDateTimeConfig();
        $deliveryConfigObject = $this->deliveryConfigInterfaceFactory->create()
            ->setFromDate($DeliveryConfig['from_date'])
            ->setToDate($DeliveryConfig['to_date'])
            ->setStartTime($DeliveryConfig['time_start'])
            ->setEndTime($DeliveryConfig['time_end']);
        return $this->configInterfaceFactory->create()
            ->setStorePickUpDateTime($storePickUpConfigObject)
            ->setDeliveryDateTime($deliveryConfigObject);
    }

    /**
     * @param $quote
     * @return array
     */
    protected function getCartSkuList($quote)
    {
        $skuList = [];
        $child = [];
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $child[$item->getParentItemId()] = $item->getSku();
            }
        }
        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $isWarehouse = (bool)$product->getIsWarehouse();
            if ($isWarehouse) {
                continue;
            }
            $sku = (isset($child[$item->getItemId()])) ? $child[$item->getItemId()] : $item->getSku();
            $skuList[$sku] = $item->getQty();
        }
        return $skuList;
    }

    /**
     * @param $cartId
     * @param $items
     * @param $additionalInfo
     * @param $customer
     * @param $checkoutSession
     * @param $isAddressComplete
     * @param $isStoreFulFill
     * @param $addressesList
     * @param null $paymentMethodsAvailable
     * @param bool $collectVoucher
     * @param false $init
     * @param false $message
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function handleCheckoutData(
        $cartId,
        $items,
        $additionalInfo,
        $customer,
        $checkoutSession,
        $isAddressComplete,
        $addressesList,
        $paymentMethodsAvailable = null,
        $collectVoucher = true
    ) {
        $dataHandle = $this->multiShippingHandle->handleData(
            $items,
            $additionalInfo->getStorePickUp(),
            $customer,
            $checkoutSession,
            true
        );
        $quote = $checkoutSession->getQuote();
        if ($collectVoucher) {
            $voucherData = $this->getAllVoucherApply($cartId, $quote);
        } else {
            $voucherData = ['cart_total' => null, 'voucher_data' => null];
        }
        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($checkoutSession->getQuote(), false, $this->notSpoList, $dataHandle['data'], $dataHandle['mobile-items-format'], $dataHandle['child-items'], $dataHandle['default-shipping-address']);
        $quoteItems = $previewOrderProcess['quote_item_data'];
        if (empty($quoteItems)) {
            $this->cartEmpty = true;
        }
        $this->disablePickUp = $previewOrderProcess['disable_store_pickup'];
        $skuList = $previewOrderProcess['sku-list'];
        $isStoreFulFill = (empty($this->msiFullFill->getMsiFullFill($skuList))) ? false : true;
        foreach ($previewOrderProcess['out_stock'] as $itemId => $itemOutStock) {
            $itemOutStock->setDisable(true)->setMessage(__('Product has been removed.'));
            $quoteItems[$itemId] = $itemOutStock;
        }
        asort($quoteItems);
        $quoteItems = array_values($quoteItems);
        $message = $this->multiShippingHandle->handleMessage($dataHandle);

        if ($dataHandle['error'] || $this->cartEmpty || $this->errorCheckoutByAddress) {
            $this->errorCheckout = true;
        }
        $customerId = $customer->getId();
        if ($this->errorCheckoutByAddress) {
            $this->errorCheckout = true;
        }
        $data = [
            CheckoutDataInterface::SHIPPING_ADDRESS => $addressesList,
            CheckoutDataInterface::ITEMS => $quoteItems,
            CheckoutDataInterface::ITEMS_MESSAGE => $message,
            CheckoutDataInterface::ADDITIONAL_INFO => $additionalInfo,
            CheckoutDataInterface::PREVIEW_ORDER => $previewOrderProcess['preview_order'],
            CheckoutDataInterface::CHECKOUT_TOTAL => $voucherData['cart_total'],
            CheckoutDataInterface::IS_STORE_FULFILL => $isStoreFulFill,
            CheckoutDataInterface::IS_SPLIT_ORDER => $dataHandle['split'],
            CheckoutDataInterface::IS_ADDRESS_COMPLETE => $isAddressComplete,
            CheckoutDataInterface::IS_ERROR_CHECKOUT => $this->errorCheckout,
            CheckoutDataInterface::PAYMENT_METHODS => $paymentMethodsAvailable,
            CheckoutDataInterface::VOUCHER => $voucherData['voucher_data'],
            CheckoutDataInterface::CURRENCY_SYMBOL => $this->getCurrencySymbol(),
            CheckoutDataInterface::DIGITAL_CHECKOUT => false,
            CheckoutDataInterface::DIGITAL_DETAIL => [],
            CheckoutDataInterface::BASKET_ID => $this->getBasketId($customerId),
            CheckoutDataInterface::BASKET_VALUE => $quote->getGrandTotal(),
            CheckoutDataInterface::BASKET_QTY => $quote->getItemsQty(),
            CheckoutDataInterface::SHOW_EACH_ITEMS => $dataHandle['show_each_items'],
            CheckoutDataInterface::DISABLE_PICK_UP => $this->disablePickUp
        ];
        return $this->getCheckoutData($data);
    }

    /**
     * @param $shippingAddress
     * @return array
     */
    protected function getAddressSelectedId($shippingAddress)
    {
        $addressId = [];
        foreach ($shippingAddress as $address) {
            $addressId[] = $address->getId();
        }
        return $addressId;
    }

    /**
     * @param $customerId
     * @param $addressSelectedId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAddressSelectedList($customerId, $addressSelectedId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('parent_id', $customerId)
            ->addFilter('entity_id', $addressSelectedId, 'in')
            ->create();

        $addressRepository = $this->addressRepository->getList($searchCriteria);
        $addressList = $addressRepository->getItems();
        $this->supportShippingData = $this->supportShippingInterfaceFactory->create()->setUse(false)
            ->setMessage("")
            ->setAddressMessage("")
            ->setAddressSupport("");
        if ($this->checkoutHelperConfig->isActiveFulfillmentStore()) {
            $this->supportShippingData->setUse(true)
                ->setMessage(__('Transmart now delivers only to Jabodetabek area.'))
                ->setAddressMessage(__('Please change your delivery address to the Jabodetabek area.'));
            $addressListSupport = [];

            foreach ($addressList as $address) {
                if ($address->getPostcode() && $address->getPostcode() != '') {
                    $checkPostCodeSupport =  $this->multiShippingHandle->checkShippingPostCode($address->getPostcode());
                    if ($checkPostCodeSupport) {
                        $addressListSupport[] = $address->getId();
                    }
                }
            }
            if (!empty(array_diff($this->itemSelectShippingAddressId, $addressListSupport))) {
                $this->errorCheckoutByAddress = true;
            }
            $this->supportShippingData->setAddressSupport(implode(",", $addressListSupport));
        }
        return $addressList;
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCheckoutData($data)
    {
        if ($this->deliveryType->canShowPickupInStore()) {
            $data[CheckoutDataInterface::USE_STORE_PICK_UP] = true;
        } else {
            $data[CheckoutDataInterface::USE_STORE_PICK_UP] = false;
        }
        $data[CheckoutDataInterface::SUPPORT_SHIPPING] = $this->supportShippingData;

        /** @var \SM\Help\Model\Question $question */
        try {
            $question = $this->questionRepository->getById($this->checkoutHelperConfig->getTermAndConditionFaq());
            if ($question->getStatus()) {
                $data[CheckoutDataInterface::TERM_AND_CONDITION] = $question;
            }
        } catch (\Exception $e) {
        }

        return $this->checkoutDataInterfaceFactory->create()->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function searchStore($cartId, $lat, $lng, $storePickupItems, $currentStoreCode)
    {
        $interface = $this->searchStoreResponseFactory->create();
        $skuList = [];
        $quote = $this->quoteRepository->get($cartId);
        $child = [];
        $destinationLatLng = $this->msiFullFill->addLatLngInterface($lat, $lng);
        $currentStore = $this->msiFullFill->getDistanceBetweenCurrentStoreAndAddressMobile($currentStoreCode, $destinationLatLng);
        $interface->setCurrentStore($currentStore);
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $child[$item->getParentItemId()][] = $item;
            }
        }
        foreach ($quote->getAllVisibleItems() as $item) {
            if (in_array($item->getId(), $storePickupItems)) {
                if (isset($child[$item->getItemId()])) {
                    foreach ($child[$item->getItemId()] as $childItem) {
                        if (isset($skuList[$childItem->getSku()])) {
                            $skuList[$childItem->getSku()] += (int)$childItem->getQty() * (int)$item->getQty();
                        } else {
                            $skuList[$childItem->getSku()] = (int)$childItem->getQty() * (int)$item->getQty();
                        }
                    }
                } else {
                    if (isset($skuList[$item->getSku()])) {
                        $skuList[$item->getSku()] += (int)$item->getQty();
                    } else {
                        $skuList[$item->getSku()] = (int)$item->getQty();
                    }
                }
            }
        }
        $sourceList = $this->msiFullFill->getMsiFullFill($skuList);
        if (empty($sourceList)) {
            $interface->setCurrentStoreFulFill(false);
            return $interface;
        }
        $storeList = $this->msiFullFill->sortSourceByDistanceMobile($sourceList, $destinationLatLng, $currentStoreCode, true);
        $interface->setStoreList($storeList['store_list']);
        $interface->setCurrentStoreFulFill($storeList['current_store_fulfill']);
        return $interface;
    }

    /**
     * {@inheritdoc}
     */
    public function applyVoucher($shippingAddress, $items, $additionalInfo, $isStoreFulFill, $isSplitOrder, $isAddressComplete, $isErrorCheckout, $voucher, $currencySymbol, $digitalCheckout, $digitalDetail, $showEachItems, $disablePickUp, $customerId, $cartId)
    {
        $this->disablePickUp = $disablePickUp;
        $requestItems = $items;
        $response = false;
        $customer = $this->customerFactory->create()->load($customerId);
        $quote = $this->quoteRepository->get($cartId);
        if ($quote->getIsVirtual()) {
            $digitalCheckout = true;
        } else {
            $digitalCheckout = false;
            $digitalDetail = [];
        }
        if (!$digitalCheckout) {
            $format = $this->multiShippingHandle->itemsFormat($items);
            $itemsFormat = $format['item_format'];
            $validateCartItems = $this->multiShippingHandle->validateItems($quote->getAllVisibleItems(), $itemsFormat);
            $reload = $validateCartItems['reload'];
        } else {
            $reload = false;
        }
        $defaultShippingAddress = $customer->getDefaultShippingAddress();
        $isAddressComplete = ($defaultShippingAddress->getStreetFull() == 'N/A'
            && $defaultShippingAddress->getPostcode() == '*****') ? false : true;
        $addressSelectedId = $this->getAddressSelectedId($shippingAddress);
        $addressesList = $this->getAddressSelectedList($customerId, $addressSelectedId);
        if ($reload) {
            $storeId = $quote->getStoreId();
            $defaultShippingMethod = \SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD;
            $weightUnit = $this->checkoutHelperConfig->getWeightUnit();
            $defaultShippingAddressId = $defaultShippingAddress->getId();
            $this->getSkuListForPickUp($quote, $defaultShippingAddress);
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                $itemId = $quoteItem->getId();
                if (isset($itemsFormat[$itemId])) {
                    $shippingMethod = $itemsFormat[$itemId]['shipping_method'];
                    $shippingAddressId = $itemsFormat[$itemId]['shipping_address'];
                } else {
                    $shippingMethod = $defaultShippingMethod;
                    $shippingAddressId = isset($addressSelectedId[0]) ? $addressSelectedId[0] : $defaultShippingAddressId;
                }
                $this->buildItemsInit($quoteItem, $quote, $shippingMethod, $weightUnit, $currencySymbol, $storeId, $shippingAddressId);
            }
            $checkoutSession = $this->multiShipping;
            $checkoutSession->setQuote($quote);
            $checkoutSession->setCustomer($customer->getDataModel());
            $response = $this->handleCheckoutData(
                $cartId,
                $this->requestItems,
                $additionalInfo,
                $customer,
                $checkoutSession,
                $isAddressComplete,
                $addressesList,
                $this->paymentMethods->getMethodsAvailable($quote, $customerId),
                false
            );
        }
        try {
            $voucherList = [];
            foreach ($voucher as $voucherDetail) {
                $voucherList[] = $voucherDetail->getVoucher();
            }
            $this->voucherInterface->mobileApplyVoucher($cartId, implode(",", $voucherList));
        } catch (\Exception $e) {
            $e->getMessage();
        }
        $voucherData = $this->getAllVoucherApply($cartId, $quote);
        if ($response) {
            $response->setCheckoutTotal($voucherData['cart_total'])->setVoucher($voucherData['voucher_data']);
        } else {
            $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote, false, $this->notSpoList);
            $data = [
                CheckoutDataInterface::SHIPPING_ADDRESS => $addressesList,
                CheckoutDataInterface::ITEMS => $requestItems,
                CheckoutDataInterface::ITEMS_MESSAGE => '',
                CheckoutDataInterface::ADDITIONAL_INFO => $additionalInfo,
                CheckoutDataInterface::PREVIEW_ORDER => $previewOrderProcess['preview_order'],
                CheckoutDataInterface::CHECKOUT_TOTAL => $voucherData['cart_total'],
                CheckoutDataInterface::IS_STORE_FULFILL => $isStoreFulFill,
                CheckoutDataInterface::IS_SPLIT_ORDER => $isSplitOrder,
                CheckoutDataInterface::IS_ADDRESS_COMPLETE => $isAddressComplete,
                CheckoutDataInterface::IS_ERROR_CHECKOUT => $isErrorCheckout,
                CheckoutDataInterface::PAYMENT_METHODS => $this->paymentMethods->getMethodsAvailable($quote, $customerId),
                CheckoutDataInterface::VOUCHER => $voucherData['voucher_data'],
                CheckoutDataInterface::CURRENCY_SYMBOL => $currencySymbol,
                CheckoutDataInterface::DIGITAL_CHECKOUT => $digitalCheckout,
                CheckoutDataInterface::DIGITAL_DETAIL => $digitalDetail,
                CheckoutDataInterface::BASKET_ID => $this->getBasketId($customerId),
                CheckoutDataInterface::BASKET_VALUE => $quote->getGrandTotal(),
                CheckoutDataInterface::BASKET_QTY => $quote->getItemsQty(),
                CheckoutDataInterface::SHOW_EACH_ITEMS => $showEachItems,
                CheckoutDataInterface::DISABLE_PICK_UP => $this->disablePickUp
            ];
            $response = $this->getCheckoutData($data);
        }
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function saveMobilePayment($paymentMethod, $term = null, $customerId, $cartId)
    {
        $payment = [
            'method' => $paymentMethod,
            'check' => [
                self::CHECK_USE_FOR_COUNTRY,
                self::CHECK_USE_FOR_CURRENCY,
                self::CHECK_ORDER_TOTAL_MIN_MAX,
                self::CHECK_ZERO_TOTAL,
            ]
        ];
        $quote = $this->quoteRepository->getActive($cartId);
        $digitalOrder = false;
        if ($quote->getIsVirtual()) {
            $digitalOrder = true;
        }
        try {
            $quote->getPayment()->importData($payment);
            // shipping totals may be affected by payment method
            if (!$quote->isVirtual() && $quote->getShippingAddress()) {
                foreach ($quote->getAllShippingAddresses() as $shippingAddress) {
                    $shippingAddress->setCollectShippingRates(true);
                }
                $quote->setTotalsCollectedFlag(false)->collectTotals();
                if (!empty($term)) {
                    $quote->setSprintTermChannelid($term);
                    $termInfo = $this->paymentHelper->getTermInfo($paymentMethod, $term, $quote);
                    $quote->setData('service_fee', ((int)$quote->getGrandTotal() * $termInfo['serviceFeeValue'])/100);
                } else {
                    $quote->setData('service_fee', 0);
                }
            }
            $this->quoteRepository->save($quote);
            $isErrorCheckout = false;
        } catch (\Exception $e) {
            $isErrorCheckout = true;
        }
        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote, false, $this->notSpoList);
        $data = [
            CheckoutDataInterface::SHIPPING_ADDRESS => [],
            CheckoutDataInterface::ITEMS => [],
            CheckoutDataInterface::ITEMS_MESSAGE => '',
            CheckoutDataInterface::ADDITIONAL_INFO => null,
            CheckoutDataInterface::PREVIEW_ORDER => $previewOrderProcess['preview_order'],
            CheckoutDataInterface::CHECKOUT_TOTAL => $this->cartTotalRepository->get($cartId),
            CheckoutDataInterface::IS_STORE_FULFILL => true,
            CheckoutDataInterface::IS_SPLIT_ORDER => false,
            CheckoutDataInterface::IS_ADDRESS_COMPLETE => true,
            CheckoutDataInterface::IS_ERROR_CHECKOUT => $isErrorCheckout,
            CheckoutDataInterface::PAYMENT_METHODS => [],
            CheckoutDataInterface::VOUCHER => [],
            CheckoutDataInterface::CURRENCY_SYMBOL => $this->getCurrencySymbol(),
            CheckoutDataInterface::DIGITAL_CHECKOUT => $digitalOrder,
            CheckoutDataInterface::DIGITAL_DETAIL => [],
            CheckoutDataInterface::BASKET_ID => $this->getBasketId($customerId),
            CheckoutDataInterface::BASKET_VALUE => $quote->getGrandTotal(),
            CheckoutDataInterface::BASKET_QTY => $quote->getItemsQty(),
            CheckoutDataInterface::SHOW_EACH_ITEMS => false,
            CheckoutDataInterface::DISABLE_PICK_UP => $this->disablePickUp
        ];
        return $this->getCheckoutData($data);
    }

    /**
     * @param $cartId
     * @param $quote
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getAllVoucherApply($cartId, $quote)
    {
        $customerId = $quote->getData('customer_id');
        $basketId = 0;
        if ($customerId) {
            $basket = $this->basketCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
            if (!$basket->getData()) {
                $basket = $this->basketFactory->create();
                $basket->setData('customer_id', $customerId);
                $basket->save();
            }
            $basketId = $basket->getId();
        }
        $basketValue = $quote->getData('grand_total');
        $basketQty = $quote->getData('items_qty');
        $voucherData = [];
        $cartTotal = $this->cartTotalRepository->get($cartId);
        $amRule = $cartTotal->getExtensionAttributes()->getAmruleDiscountBreakdown();
        $voucherApplyPass = [];
        $date = $this->date->gmtDate();
        if ($amRule) {
            foreach ($amRule as $rule) {
                if ($rule->getCode()) {
                    $coupon = $this->couponModel->loadByCode($rule->getCode());
                    if ($coupon) {
                        $dataCoupon = $this->getVoucher($coupon, $quote);
                        if ($dataCoupon['to_date']) {
                            $voucherValidation = "Valid Until " . $dataCoupon['to_date'];
                            $voucherStatus = $this->dateDiffInDays($date, $dataCoupon['to_date']) . " days";
                        } else {
                            $voucherValidation = "Not available";
                            $voucherStatus = "Expired";
                        }
                        $voucherData[] = $this->voucherFactoryInterface->create()
                            ->setVoucher($rule->getCode())
                            ->setApply(true)
                            ->setAmount($rule->getRuleAmount())
                            ->setBasketValue($basketValue)
                            ->setBasketId($basketId)
                            ->setBasketQuantity($basketQty)
                            ->setVoucherId($dataCoupon['coupon_id'])
                            ->setVoucherName($dataCoupon['name'])
                            ->setVoucherDescription($dataCoupon['description'])
                            ->setVoucherValidation($voucherValidation)
                            ->setVoucherStatus($voucherStatus);
                    }
                    $voucherApplyPass[] = $rule->getCode();
                }
            }
        }
        $allApplyVoucher = $quote->getApplyVoucher();
        if ($allApplyVoucher && $allApplyVoucher != '') {
            foreach (explode(",", $allApplyVoucher) as $voucherString) {
                if (in_array($voucherString, $voucherApplyPass)) {
                    continue;
                }
                $coupon = $this->couponModel->loadByCode($voucherString);
                if ($coupon) {
                    $dataCoupon = $this->getVoucher($coupon, $quote);
                    if ($dataCoupon['to_date']) {
                        $voucherValidation = "Valid Until " . $dataCoupon['to_date'];
                        $voucherStatus = $this->dateDiffInDays($date, $dataCoupon['to_date']) . " days";
                    } else {
                        $voucherValidation = "Not available";
                        $voucherStatus = "Expired";
                    }
                    $voucherData[] = $this->voucherFactoryInterface->create()
                        ->setVoucher($voucherString)
                        ->setApply(false)
                        ->setAmount('')
                        ->setBasketValue($basketValue)
                        ->setBasketId($basketId)
                        ->setBasketQuantity($basketQty)
                        ->setVoucherId($dataCoupon['coupon_id'])
                        ->setVoucherName($dataCoupon['name'])
                        ->setVoucherDescription($dataCoupon['description'])
                        ->setVoucherValidation($voucherValidation)
                        ->setVoucherStatus($voucherStatus);
                }
            }
        }
        return ['voucher_data' => $voucherData, 'cart_total' => $cartTotal];
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCurrencySymbol()
    {
        if ($this->currencySymbol == '') {
            $this->currencySymbol = trim($this->checkoutHelperConfig->getCurrencySymbol());
        }
        return $this->currencySymbol;
    }

    /**
     * @param $cartId
     * @param $quote
     * @param $customerId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function handleCheckoutDigital($cartId, $quote, $customerId)
    {
        $defaultShipping = $quote->getCustomer()->getDefaultShipping();
        $defaultCustomerAddress = $this->addressRepository->getById($defaultShipping);
        foreach ($quote->getAllShippingAddresses() as $address) {
            $quote->removeAddress($address->getId());
        }
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->importCustomerAddressData($defaultCustomerAddress);
        $this->quoteRepository->save($quote);

        try {
            $this->voucherInterface->mobileApplyVoucher($cartId, '', true);
        } catch (\Exception $e) {
        }
        $storePickUp = $this->storePickUpInterfaceFactory->create();
        $additionalInfo = $this->additionalInfoInterfaceFactory->create()->setStorePickUp($storePickUp);

        $voucherData = $this->getAllVoucherApply($cartId, $quote);
        $digitalDetail = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $options = $item->getBuyRequest()->toArray();
            $product =  $item->getProduct();
            foreach ($options['digital'] as $code => $option) {
                $digitalDetail[] = $this->digitalInterfaceFactory->create()
                    ->setLabel($this->getDigitalLabel($code))
                    ->setValue($this->formatValue($code, $option, $product));
            }
        }
        $data = [
            CheckoutDataInterface::SHIPPING_ADDRESS => [],
            CheckoutDataInterface::ITEMS => [],
            CheckoutDataInterface::ITEMS_MESSAGE => '',
            CheckoutDataInterface::ADDITIONAL_INFO => $additionalInfo,
            CheckoutDataInterface::PREVIEW_ORDER => [],
            CheckoutDataInterface::CHECKOUT_TOTAL => $voucherData['cart_total'],
            CheckoutDataInterface::IS_STORE_FULFILL => true,
            CheckoutDataInterface::IS_SPLIT_ORDER => false,
            CheckoutDataInterface::IS_ADDRESS_COMPLETE => true,
            CheckoutDataInterface::IS_ERROR_CHECKOUT => false,
            CheckoutDataInterface::PAYMENT_METHODS => $this->paymentMethods->getMethodsAvailable($quote, $customerId),
            CheckoutDataInterface::VOUCHER => $voucherData['voucher_data'],
            CheckoutDataInterface::CURRENCY_SYMBOL => $this->getCurrencySymbol(),
            CheckoutDataInterface::DIGITAL_CHECKOUT => true,
            CheckoutDataInterface::DIGITAL_DETAIL => $this->getDigitalDetail($quote),
            CheckoutDataInterface::SHOW_EACH_ITEMS => false,
            CheckoutDataInterface::DISABLE_PICK_UP => $this->disablePickUp
        ];

        return $this->getCheckoutData($data);
    }

    /**
     * @param string $code
     * @return string
     */
    protected function getDigitalLabel($code)
    {
        $label = '';
        $i = 0;
        foreach (explode('_', $code) as $nam) {
            $i++;
            if ($i > 1) {
                $label .= ' ';
            }
            if (strtolower($nam) == 'id') {
                $nam = "ID";
            }
            $label .= $nam;
        }

        return ucwords(str_replace("and", "&", $label));
    }

    /**
     * @param $code
     * @param $option
     * @param $product
     * @return string
     */
    private function formatValue($code, $option, $product)
    {
        if (in_array($code, DigitalInterface::DIGITAL_PRICE_FIELDS)) {
            return $this->priceCurrency->convertAndFormat($option, false);
        }

        if ($code == DigitalInterface::SERVICE_TYPE) {
            return $this->getServiceType($product);
        }

        return $option;
    }

    /**
     * @param $product
     * @return string
     */
    private function getServiceType($product)
    {
        $category = $product->getCategoryCollection()->addAttributeToSelect('name')->getFirstItem();
        return $category->getName();
    }

    /*
    * @param string $date1
    * @param string $date2
    * @return float|int
    */
    public function dateDiffInDays($date1, $date2)
    {
        if (!is_string($date1) || !is_string($date2)) {
            return 0;
        }
        // Calculating the difference in timestamps
        $diff = strtotime($date2) - strtotime($date1);

        // 1 day = 24 hours
        // 24 * 60 * 60 = 86400 seconds
        return abs(round($diff / 86400));
    }

    /**
     * @param $quote
     * @return array
     */
    public function getDigitalDetail($quote)
    {
        $digitalDetail = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $options = $item->getBuyRequest()->toArray();
            $product = $item->getProduct();
            foreach ($options['digital'] as $code => $option) {
                $digitalDetail[] = $this->digitalInterfaceFactory->create()
                    ->setLabel($this->getDigitalLabel($code))
                    ->setValue($this->formatValue($code, $option, $product));
            }
        }
        return $digitalDetail;
    }

    public function getBasketId($customerId)
    {
        $basket = $this->basketCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
        if (!$basket->getData()) {
            $basket = $this->basketFactory->create();
            $basket->setData('customer_id', $customerId);
            $basket->save();
        }
        return $basket->getId();
    }

    /**
     * @param $coupon
     * @param $quote
     * @return \SM\MyVoucher\Api\Data\RuleDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getVoucher($coupon, $quote)
    {
        $collection = $this->ruleRepository->getVoucherCollection($quote->getCustomer());
        $collection->getSelect()->where('main_table.rule_id = ?', $coupon->getRuleId());

        /** @var \Magento\SalesRule\Model\Coupon $voucher */
        $voucher = $collection->getFirstItem();
        if ($voucher) {
            $dataCoupon = $this->ruleRepository->prepareRuleData($voucher);
        } else {
            $dataCoupon = $this->ruleRepository->prepareRuleData($coupon);
        }

        return $dataCoupon;
    }
}
