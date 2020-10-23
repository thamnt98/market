<?php
declare(strict_types=1);

namespace SM\Checkout\Model;

use Magento\Catalog\Model\Product;
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
     * MultiShippingMobile constructor.
     * @param \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory $gtmCart
     * @param \SM\GTM\Block\Product\ListProduct $productGtm
     * @param \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory
     * @param BasketFactory $basketFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\SalesRule\Model\Coupon $couponModel
     * @param \SM\MyVoucher\Model\RuleRepository $ruleRepository
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $quoteItemRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository
     * @param \SM\Checkout\Helper\Config $checkoutHelperConfig
     * @param MultiShippingHandle $multiShippingHandle
     * @param Checkout\Type\Multishipping $multiShipping
     * @param Split $split
     * @param MsiFullFill $msiFullFill
     * @param \SM\Checkout\Api\Data\Checkout\CheckoutDataInterfaceFactory $checkoutDataInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterfaceFactory $itemInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\ShippingMethodInterfaceFactory $shippingMethodInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterfaceFactory $itemAdditionalInfoInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterfaceFactory $deliveryInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterfaceFactory $additionalInfoInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterfaceFactory $storePickUpInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\ConfigInterfaceFactory $configInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterfaceFactory $pickUpConfigInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterfaceFactory $deliveryConfigInterfaceFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param Api\PaymentMethods $paymentMethods
     * @param \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterfaceFactory $searchStoreResponseFactory
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterfaceFactory $productOptionsInterfaceFactory
     * @param \SM\Checkout\Api\VoucherInterface $voucherInterface
     * @param \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterfaceFactory $voucherFactoryInterface
     * @param \SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory $installationInterfaceFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterfaceFactory $digitalInterfaceFactory
     * @param \Magento\Framework\Registry $registry
     * @param Price $price
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \SM\MobileApi\Api\Data\GTM\GTMCartInterfaceFactory $gtmCart,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory,
        BasketFactory $basketFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\SalesRule\Model\Coupon $couponModel,
        \SM\MyVoucher\Model\RuleRepository $ruleRepository,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Api\CartItemRepositoryInterface $quoteItemRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
        \SM\Checkout\Helper\Config $checkoutHelperConfig,
        \SM\Checkout\Model\MultiShippingHandle $multiShippingHandle,
        \SM\Checkout\Model\Checkout\Type\Multishipping $multiShipping,
        \SM\Checkout\Model\Split $split,
        \SM\Checkout\Model\MsiFullFill $msiFullFill,
        \SM\Checkout\Api\Data\Checkout\CheckoutDataInterfaceFactory $checkoutDataInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterfaceFactory $itemInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\ShippingMethodInterfaceFactory $shippingMethodInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterfaceFactory $itemAdditionalInfoInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterfaceFactory $deliveryInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterfaceFactory $additionalInfoInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterfaceFactory $storePickUpInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\ConfigInterfaceFactory $configInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Config\StorePickUpInterfaceFactory $pickUpConfigInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\Config\DeliveryInterfaceFactory $deliveryConfigInterfaceFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \SM\Checkout\Model\Api\PaymentMethods $paymentMethods,
        \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterfaceFactory $searchStoreResponseFactory,
        \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterfaceFactory $productOptionsInterfaceFactory,
        \SM\Checkout\Api\VoucherInterface $voucherInterface,
        \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterfaceFactory $voucherFactoryInterface,
        \SM\Checkout\Api\Data\CartItem\InstallationInterfaceFactory $installationInterfaceFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterfaceFactory $digitalInterfaceFactory,
        \Magento\Framework\Registry $registry,
        \SM\Checkout\Model\Price $price,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \SM\Checkout\Helper\DeliveryType $deliveryType,
        \SM\FreshProductApi\Helper\Fresh $fresh,
        \SM\Checkout\Api\Data\Checkout\SupportShippingInterfaceFactory $supportShippingInterfaceFactory,
        \SM\Help\Model\QuestionRepository $questionRepository,
        \SM\Help\Api\Data\QuestionInterfaceFactory $questionFactory
    ) {
        $this->questionRepository = $questionRepository;
        $this->questionFactory = $questionFactory;
        $this->fresh = $fresh;
        $this->configurationPool = $configurationPool;
        $this->appEmulation = $appEmulation;
        $this->customerFactory = $customerFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->addressRepository = $addressRepository;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->imageHelper = $imageHelper;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->checkoutHelperConfig = $checkoutHelperConfig;
        $this->multiShippingHandle = $multiShippingHandle;
        $this->multiShipping = $multiShipping;
        $this->split = $split;
        $this->msiFullFill = $msiFullFill;
        $this->checkoutDataInterfaceFactory = $checkoutDataInterfaceFactory;
        $this->itemInterfaceFactory = $itemInterfaceFactory;
        $this->shippingMethodInterfaceFactory = $shippingMethodInterfaceFactory;
        $this->previewOrderInterfaceFactory = $previewOrderInterfaceFactory;
        $this->itemAdditionalInfoInterfaceFactory = $itemAdditionalInfoInterfaceFactory;
        $this->deliveryInterfaceFactory = $deliveryInterfaceFactory;
        $this->additionalInfoInterfaceFactory = $additionalInfoInterfaceFactory;
        $this->storePickUpInterfaceFactory = $storePickUpInterfaceFactory;
        $this->configInterfaceFactory = $configInterfaceFactory;
        $this->pickUpConfigInterfaceFactory = $pickUpConfigInterfaceFactory;
        $this->deliveryConfigInterfaceFactory = $deliveryConfigInterfaceFactory;
        $this->quoteRepository = $quoteRepository;
        $this->paymentMethods = $paymentMethods;
        $this->searchStoreResponseFactory = $searchStoreResponseFactory;
        $this->productOptionsInterfaceFactory = $productOptionsInterfaceFactory;
        $this->voucherInterface = $voucherInterface;
        $this->voucherFactoryInterface = $voucherFactoryInterface;
        $this->installationFactory = $installationInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->digitalInterfaceFactory = $digitalInterfaceFactory;
        $this->registry = $registry;
        $this->couponModel = $couponModel;
        $this->ruleRepository = $ruleRepository;
        $this->date = $date;
        $this->basketCollectionFactory = $basketCollectionFactory;
        $this->basketFactory = $basketFactory;
        $this->price = $price;
        $this->productGtm = $productGtm;
        $this->priceCurrency = $priceCurrency;
        $this->gtmCart = $gtmCart;
        $this->deliveryType = $deliveryType;
        $this->supportShippingInterfaceFactory = $supportShippingInterfaceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function initCheckout($customerId, $cartId)
    {
        if ($this->registry->registry("remove_cart_item")) {
            $this->registry->unregister("remove_cart_item");
        }
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->getIsVirtual()) {
            return $this->handleCheckoutDigital($cartId, $customerId);
        }
        $customer = $this->customerFactory->create()->load($customerId);
        $quote = $this->quoteRepository->get($cartId);
        $checkoutSession = $this->multiShipping;
        $checkoutSession->setQuote($quote);
        $checkoutSession->setCustomer($customer->getDataModel());
        $defaultShippingAddress = $customer->getDefaultShippingAddress();
        $defaultShippingAddressId = $defaultShippingAddress->getId();
        $isAddressComplete = ($defaultShippingAddress->getStreetFull() == 'N/A'
            && $defaultShippingAddress->getPostcode() == '*****') ? false : true;
        $skuList = [];
        $child = [];
        $items = [];
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $child[$item->getParentItemId()] = $item->getSku();
            }
        }
        $weightUnit = $this->checkoutHelperConfig->getWeightUnit();
        $storeId = $quote->getStoreId();
        $currencySymbol = $this->checkoutHelperConfig->getCurrencySymbol();
        $quoteItems = $this->quoteItemRepository->getList($quote->getId());
        $defaultShippingMethod = "transshipping_transshipping1";
        foreach ($quoteItems as $index => $quoteItem) {
            $sku = (isset($child[$quoteItem->getItemId()])) ? $child[$quoteItem->getItemId()] : $quoteItem->getSku();
            $skuList[$sku] = $quoteItem->getQty();
            $items[$quoteItem->getItemId()] = [
                "shipping_method" => $defaultShippingMethod,
                "shipping_address" => $defaultShippingAddressId,
                "qty" => $quoteItem->getQty(),
                "disable" => false,
                "delivery" => [
                    "date" => "",
                    "time" => ""
                ]
            ];
            $this->requestItems[$quoteItem->getId()] = $this->buildRequestItemsInit($quoteItem, $this->getFormattedOptionValue($quoteItem), $storeId, $weightUnit, $currencySymbol, $defaultShippingMethod, $defaultShippingAddressId, $quote);
        }

        $storePickUp = $this->storePickUpInterfaceFactory->create();
        $additionalInfo = $this->additionalInfoInterfaceFactory->create()->setStorePickUp($storePickUp);
        $skuList = $this->getCartSkuList($quote);
        $isStoreFulFill = (empty($this->msiFullFill->getMsiFullFill($skuList))) ? false : true;
        $addressSelectedId = [$defaultShippingAddressId];
        $addressesList = $this->getAddressSelectedList($customerId, $addressSelectedId);

        return $this->handleCheckoutData(
            $cartId,
            $items,
            $additionalInfo,
            $customer,
            $checkoutSession,
            $isAddressComplete,
            $isStoreFulFill,
            $addressesList,
            $this->paymentMethods->getMethodsAvailable($quote, $customerId),
            true,
            true
        );
    }

    /**
     * @param $quoteItem
     * @param $itemOption
     * @param $storeId
     * @param $weightUnit
     * @param $currencySymbol
     * @param $shippingMethod
     * @param $shippingAddress
     * @param $quote
     * @return \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface
     * @throws \Zend_Json_Exception
     */
    protected function buildRequestItemsInit($quoteItem, $itemOption, $storeId, $weightUnit, $currencySymbol, $shippingMethod, $shippingAddress, $quote)
    {
        $quoteItemModel = $this->itemInterfaceFactory->create();
        $quoteItemId = $quoteItem->getId();
        $product = $quoteItem->getProduct();
        $regularPrice = $this->price->getRegularPrice($product);
        $productType = $product->getTypeId();
        $quoteItemModel->setItemId($quoteItemId);
        $quoteItemModel->setSku($product->getData('sku'));
        $quoteItemModel->setName($quoteItem->getName());
        $quoteItemModel->setProductType($productType);
        $quoteItemModel->setProductOption($itemOption);
        $quoteItemModel->setUrl($product->getProductUrl());
        $weight = $quoteItem->getQty() * $product->getWeight();
        $quoteItemModel->setWeight(round($weight, 2));
        $quoteItemModel->setWeightUnit($weightUnit);
        $quoteItemModel->setQty($quoteItem->getQty());
        $quoteItemModel->setThumbnail($this->getImageUrl($product, $storeId));
        $quoteItemModel->setRowTotal($quoteItem->getRowTotal());
        $quoteItemModel->setCurrencySymbol($currencySymbol);
        $quoteItemModel->setShippingAddressId($shippingAddress);
        $quoteItemModel->setShippingMethodSelected($shippingMethod);
        $quoteItemModel->setFreshProduct($this->fresh->populateObject($product));

        $quoteItemModel->setBaseRowTotalByLocation($regularPrice * $quoteItem->getQty());

        $shippingMethodList = [];
        foreach ($this->split->getListMethodFakeName() as $value => $label) {
            $shippingMethodModel = $this->shippingMethodInterfaceFactory->create();
            $shippingMethodModel->setValue('transshipping_transshipping' . $value)->setLabel($label);
            $shippingMethodModel->setDisabled(true);
            $shippingMethodList[] = $shippingMethodModel;
        }
        $quoteItemModel->setShippingMethod($shippingMethodList);
        $quoteItemModel->setDisable(false);

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
        $voucher = $quote->getApplyVoucher();
        if ($voucher != null && $voucher != '') {
            $model->setApplyVoucher(__('Yes'));
            $model->setVoucherId($voucher);
        } else {
            $model->setApplyVoucher(__('No'));
            $model->setVoucherId('');
        }
        $quoteItemModel->setGtmData($model);

        return $quoteItemModel;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @return \SM\Checkout\Api\Data\CartItem\InstallationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getInstallationProduct($item)
    {
        $installationInfo = $this->installationFactory->create();
        $buyRequest = $item->getBuyRequest();
        $allowInstallation = $item->getProduct()->getData(\SM\Installation\Helper\Data::PRODUCT_ATTRIBUTE);
        if ($allowInstallation == null || $allowInstallation == "") {
            $allowInstallation = false;
        }
        $installationFee = 0;
        $isInstallationFee = 0;
        $installationNote = '';
        if ($buyRequest) {
            $installationService = $buyRequest->getData(\SM\Installation\Helper\Data::QUOTE_OPTION_KEY);
            if ($installationService) {
                $installationFee = isset($installationService['installation_fee']) ? $installationService['installation_fee'] : 0;
                $isInstallationFee = isset($installationService['is_installation']) ? $installationService['is_installation'] : 0;
                $installationNote = isset($installationService['installation_note']) ? $installationService['installation_note'] : '';
            }
        }
        $installationInfo->setAllowInstallation($allowInstallation);
        $installationInfo->setInstallationFee($installationFee);
        $installationInfo->setIsInstallation($isInstallationFee);
        $installationInfo->setInstallationNote($installationNote);
        return $installationInfo;
    }
    /**
     * {@inheritdoc}
     */
    public function getStorePickUpSourceFullFill($customerId, $cartId)
    {
        $interface = $this->searchStoreResponseFactory->create();
        $customer = $this->customerFactory->create()->load($customerId);
        $quote = $this->quoteRepository->get($cartId);
        $skuList = $this->getCartSkuList($quote);
        $sourceList = $this->msiFullFill->getMsiFullFill($skuList);
        if (empty($sourceList)) {
            $interface->setCurrentStoreFulFill(false);
            return $interface;
        }
        $defaultShipping = $customer->getDefaultShippingAddress();
        $defaultLat = $defaultShipping->getCustomAttribute('latitude') ? $defaultShipping->getCustomAttribute('latitude')->getValue() : 0;
        $defaultLng = $defaultShipping->getCustomAttribute('longitude') ? $defaultShipping->getCustomAttribute('longitude')->getValue() : 0;
        if ($defaultLat != 0 && $defaultLng != 0) {
            $defaultShipping->setLatitude($defaultLat)->setLongitude($defaultLng);
        }
        $defaultShipping->setCity($this->split->getCityName($defaultShipping->getCity()));
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
        $format = $this->multiShippingHandle->itemsFormat($items);
        $items = $format['item_format'];
        $this->requestItems = $format['item_request'];
        $defaultShippingAddress = $customer->getDefaultShippingAddress();
        $isAddressComplete = ($defaultShippingAddress->getStreetFull() == 'N/A'
            && $defaultShippingAddress->getPostcode() == '*****') ? false : true;
        $skuList = $this->getCartSkuList($quote);
        $isStoreFulFill = (empty($this->msiFullFill->getMsiFullFill($skuList))) ? false : true;
        $addressSelectedId = $this->getAddressSelectedId($shippingAddress);
        $addressesList = $this->getAddressSelectedList($customerId, $addressSelectedId);

        return $this->handleCheckoutData(
            $cartId,
            $items,
            $additionalInfo,
            $customer,
            $checkoutSession,
            $isAddressComplete,
            $isStoreFulFill,
            $addressesList,
            $this->paymentMethods->getMethodsAvailable($quote, $customerId),
            true,
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
        $customerId,
        $cartId
    ) {
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

        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote, false);
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
            CheckoutDataInterface::SHOW_EACH_ITEMS => $this->multiShippingHandle->isShowEachItems($quote->getAllShippingAddresses())
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
            $isWarehouse = $product->getIsWarehouse();
            if ($isWarehouse == 1) {
                $skuList = [];
                break;
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
        $isStoreFulFill,
        $addressesList,
        $paymentMethodsAvailable = null,
        $collectVoucher = true,
        $init = false,
        $message = false
    ) {
        $dataHandle = $this->multiShippingHandle->handleData(
            $items,
            $this->multiShippingHandle->storePickUpFormat($additionalInfo),
            $customer,
            $checkoutSession
        );
        if ($init) {
            try {
                $this->voucherInterface->mobileApplyVoucher($cartId, '', true);
            } catch (\Exception $e) {
            }
        }
        if ($collectVoucher) {
            $voucherData = $this->getAllVoucherApply($cartId, $checkoutSession->getQuote());
        } else {
            $voucherData = ['cart_total' => null, 'voucher_data' => null];
        }
        $quote = $this->quoteRepository->get($checkoutSession->getQuote()->getId());
        $checkoutSession = $this->multiShipping;
        $checkoutSession->setQuote($quote);
        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($checkoutSession->getQuote(), false);
        $shippingMethodSelected = $previewOrderProcess['shipping_method_selected'];
        $quoteItems = $this->getQuoteItemsData($items, $shippingMethodSelected, $checkoutSession, $dataHandle);
        if (!$message) {
            $message = '';
            if (isset($dataHandle['error_stock']) && $dataHandle['error_stock']) {
                if (isset($dataHandle['error_stock_message']['out_stock']) && !empty($dataHandle['error_stock_message']['out_stock'])) {
                    $message = __('Unfortunately, some products are not allocated to your area. We have removed the products for you.');
                }
                if (isset($dataHandle['error_stock_message']['low_stock']) && !empty($dataHandle['error_stock_message']['low_stock'])) {
                    if ($message == '') {
                        $message = __('Unfortunately, some products are allocated in limited stock in your area. We have adjusted the quantity for you.');
                    } else {
                        $message = __('Unfortunately, some products are allocated in limited stock or not allocated in your area. We have adjusted the quantity or removed for you.');
                    }
                }
            } else {
                if ($this->removeItem && $this->updateItem) {
                    $message = __('Unfortunately, some products are allocated in limited stock or not allocated in your area. We have adjusted the quantity or removed for you.');
                } elseif ($this->removeItem) {
                    $message = __('Unfortunately, some products are not allocated to your area. We have removed the products for you.');
                } elseif ($this->updateItem) {
                    $message = __('Unfortunately, some products are allocated in limited stock in your area. We have adjusted the quantity for you.');
                }
            }
        }

        if ($dataHandle['error'] || $this->cartEmpty) {
            $checkoutError = true;
        } else {
            $checkoutError = false;
        }

        $customerId = $customer->getId();
        $rebuild = false;
        foreach ($quoteItems as $item) {
            $ShippingMethodSelected = $item->getShippingMethodSelected();
            if ($ShippingMethodSelected == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                continue;
            }
            $shippingMethodList = $item->getShippingMethod();
            $shippingMethodListEnable = [];
            foreach ($shippingMethodList as $shippingMethod) {
                if (!$shippingMethod->getDisabled()) {
                    $shippingMethodListEnable[] = $shippingMethod->getValue();
                }
            }
            if (empty($shippingMethodListEnable)) {
                $items[$item->getItemId()]['shipping_method'] = \SM\Checkout\Model\MultiShippingHandle::NOT_AVAILABLE;
            } elseif (!in_array($ShippingMethodSelected, $shippingMethodListEnable)) {
                $rebuild = true;
                $items[$item->getItemId()]['shipping_method'] = $shippingMethodListEnable[0];
            }
        }
        if ($rebuild) {
            return $this->handleCheckoutData(
                $cartId,
                $items,
                $additionalInfo,
                $customer,
                $checkoutSession,
                $isAddressComplete,
                $isStoreFulFill,
                $addressesList,
                $paymentMethodsAvailable,
                $collectVoucher,
                $init = false,
                $message
            );
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
            CheckoutDataInterface::IS_ERROR_CHECKOUT => $checkoutError,
            CheckoutDataInterface::PAYMENT_METHODS => $paymentMethodsAvailable,
            CheckoutDataInterface::VOUCHER => $voucherData['voucher_data'],
            CheckoutDataInterface::CURRENCY_SYMBOL => $this->getCurrencySymbol(),
            CheckoutDataInterface::DIGITAL_CHECKOUT => false,
            CheckoutDataInterface::DIGITAL_DETAIL => [],
            CheckoutDataInterface::BASKET_ID => $this->getBasketId($customerId),
            CheckoutDataInterface::BASKET_VALUE => $quote->getGrandTotal(),
            CheckoutDataInterface::BASKET_QTY => $quote->getItemsQty(),
            CheckoutDataInterface::SHOW_EACH_ITEMS => $this->multiShippingHandle->isShowEachItems($quote->getAllShippingAddresses())
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
        } catch (\Exception $e){
        }

        return $this->checkoutDataInterfaceFactory->create()->setData($data);
    }

    /**
     * @param $items
     * @param $shippingMethodSelected
     * @param $checkoutSession
     * @param $dataHandle
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getQuoteItemsData($items, $shippingMethodSelected, $checkoutSession, $dataHandle)
    {
        $response = [];
        $quoteItemData = [];
        $weightUnit = $this->checkoutHelperConfig->getWeightUnit();
        $currencySymbol = $this->getCurrencySymbol();
        $quote = $checkoutSession->getQuote();
        $quoteItems = $this->quoteItemRepository->getList($quote->getId());
        $itemOption = [];
        $basePriceByLocation = [];
        foreach ($quoteItems as $index => $quoteItem) {
            $itemOption[$quoteItem->getItemId()] = $this->getFormattedOptionValue($quoteItem);
            $basePriceByLocation[$quoteItem->getItemId()] = $quoteItem->getBasePriceByLocation();
        }
        $storeId = $quote->getStoreId();
        $addressId = $quote->getShippingAddress()->getId();
        foreach ($quote->getAllShippingAddresses() as $_address) {
            foreach ($_address->getAllVisibleItems() as $quoteItem) {
                if ($quoteItem instanceof \Magento\Quote\Model\Quote\Address\Item) {
                    $quoteItemId = $quoteItem->getQuoteItemId();
                    $quoteAddressId = $quoteItem->getQuoteAddressId();
                } else {
                    $quoteItemId = $quoteItem->getId();
                    $quoteAddressId = $addressId;
                }
                /** @var \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface $quoteItemModel */
                $quoteItemModel = $this->itemInterfaceFactory->create();
                $product = $quoteItem->getProduct();
                $regularPrice = $this->price->getRegularPrice($product);
                $productType = $product->getTypeId();
                $quoteItemModel->setItemId($quoteItemId);
                $quoteItemModel->setSku($product->getData('sku'));
                $quoteItemModel->setName($quoteItem->getName());
                $quoteItemModel->setProductType($productType);
                $quoteItemModel->setProductOption($itemOption[$quoteItemId]);
                $quoteItemModel->setUrl($product->getProductUrl());
                $weight = $quoteItem->getQty() * $product->getWeight();
                $quoteItemModel->setWeight(round($weight, 2));
                $quoteItemModel->setWeightUnit($weightUnit);
                $quoteItemModel->setQty($quoteItem->getQty());
                $quoteItemModel->setThumbnail($this->getImageUrl($product, $storeId));
                $quoteItemModel->setRowTotal($quoteItem->getRowTotal());
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
                $voucher = $quote->getApplyVoucher();
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
                $data = $shippingMethodSelected[$quoteAddressId];
                if ($data['shipping_method'] == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                    $quoteItemModel->setShippingAddressId(0);
                } else {
                    $quoteItemModel->setShippingAddressId($data['customer_address_id']);
                }
                $shippingMethod = ($data['shipping_method'] && $data['shipping_method'] != '')
                    ? $data['shipping_method'] : $items[$quoteItemId]['shipping_method'];
                if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::DC) {
                    $shippingMethod = \SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD;
                }
                if ($shippingMethod == \SM\Checkout\Model\MultiShippingHandle::TRANS_COURIER) {
                    $shippingMethod = \SM\Checkout\Model\MultiShippingHandle::SAME_DAY;
                }
                $quoteItemModel->setShippingMethodSelected($shippingMethod);
                $shippingMethodList = [];

                foreach ($this->split->getListMethodFakeName() as $value => $label) {
                    $shippingMethod = $this->shippingMethodInterfaceFactory->create();
                    $shippingMethod->setValue('transshipping_transshipping' . $value)->setLabel($label);
                    if ($data['shipping_method'] == \SM\Checkout\Model\MultiShippingHandle::DC) {
                        $data['shipping_method'] = \SM\Checkout\Model\MultiShippingHandle::DEFAULT_METHOD;
                    } elseif ($data['shipping_method'] == \SM\Checkout\Model\MultiShippingHandle::TRANS_COURIER) {
                        $data['shipping_method'] = \SM\Checkout\Model\MultiShippingHandle::SAME_DAY;
                    }
                    if ($data['shipping_method'] == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                        $shippingMethod->setDisabled(false);
                    } else {
                        if (in_array(
                            'transshipping_transshipping' . $value,
                            $dataHandle['data'][$quoteItemId]
                        )) {
                            $shippingMethod->setDisabled(false);
                        } else {
                            $shippingMethod->setDisabled(true);
                        }
                    }
                    $shippingMethodList[] = $shippingMethod;
                }

                $quoteItemModel->setShippingMethod($shippingMethodList);
                $deliveryData = $items[$quoteItemId]['delivery'];
                $delivery = $this->deliveryInterfaceFactory->create()
                    ->setDate($deliveryData['date'])
                    ->setTime($deliveryData['time']);

                $installationInfo = $this->getInstallationProduct($quoteItem);
                $additionalInfo = $this->itemAdditionalInfoInterfaceFactory->create();
                $additionalInfo->setDelivery($delivery);
                $additionalInfo->setInstallationInfo($installationInfo);
                $quoteItemModel->setAdditionalInfo($additionalInfo);
                $quoteItemModel->setDisable(false);
                if (isset($dataHandle['error_stock']) &&
                    isset($dataHandle['error_stock_message']['low_stock']) &&
                    in_array($quoteItemId, $dataHandle['error_stock_message']['low_stock'])
                ) {
                    $quoteItemModel->setMessage(__('Quantity has been adjusted.'));
                }
                $quoteItemData[$quoteItemId] = $quoteItemModel;
            }
        }
        if (isset($dataHandle['error_stock']) && isset($dataHandle['error_stock_message']['out_stock']) && !empty($dataHandle['error_stock_message']['out_stock'])) {
            $outStockList = $dataHandle['error_stock_message']['out_stock'];
        } else {
            $outStockList = [];
        }
        $cartItemsCount = count($quoteItems);
        if ($cartItemsCount == 0) {
            $this->cartEmpty = true;
        }
        foreach ($this->requestItems as $itemId => $item) {
            if ($item->getDisable()) {
                $response[] = $item;
            } elseif (in_array($itemId, $outStockList)) {
                $item->setDisable(true)->setMessage(__('Product has been removed.'));
                $response[] = $item;
            } elseif (isset($quoteItemData[$itemId])) {
                if ($item->getMessage() != '' && $quoteItemData[$itemId]->getMessage() == '') {
                    $updateItem = $quoteItemData[$itemId]->setMessage($item->getMessage());
                    $response[] = $updateItem;
                } elseif ($item->getQty() != $quoteItemData[$itemId]->getQty()) {
                    $this->updateItem = true;
                    $updateItem = $quoteItemData[$itemId]->setMessage(__('Quantity has been adjusted.'));
                    $response[] = $updateItem;
                } else {
                    $response[] = $quoteItemData[$itemId];
                }
                unset($quoteItemData[$itemId]);
            } else {
                $this->removeItem = true;
                $item->setDisable(true)->setMessage(__('Product has been removed.'));
                $response[] = $item;
            }
        }
        foreach ($quoteItemData as $item) {
            $response[] = $item;
        }
        return $response;
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
     * {@inheritdoc}
     */
    public function searchStore($cartId, $lat, $lng, $storePickupItems, $currentStoreCode)
    {
        $interface = $this->searchStoreResponseFactory->create();
        $skuList = [];
        $quote = $this->quoteRepository->get($cartId);
        $child = [];
        $fulFill = true;
        $destinationLatLng = $this->msiFullFill->addLatLngInterface($lat, $lng);
        $currentStore = $this->msiFullFill->getDistanceBetweenCurrentStoreAndAddressMobile($currentStoreCode, $destinationLatLng);
        $interface->setCurrentStore($currentStore);
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $child[$item->getParentItemId()] = $item->getSku();
            }
        }
        foreach ($quote->getAllVisibleItems() as $item) {
            if (in_array($item->getId(), $storePickupItems)) {
                $product = $item->getProduct();
                $isWarehouse = $product->getIsWarehouse();
                if ($isWarehouse == 1) {
                    $fulFill = false;
                    break;
                }
                $sku = (isset($child[$item->getItemId()])) ? $child[$item->getItemId()] : $item->getSku();
                $skuList[$sku] = $item->getQty();
            }
        }
        if (!$fulFill) {
            $interface->setCurrentStoreFulFill(false);
            return $interface;
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
    public function applyVoucher($shippingAddress, $items, $additionalInfo, $isStoreFulFill, $isSplitOrder, $isAddressComplete, $isErrorCheckout, $voucher, $currencySymbol, $digitalCheckout, $digitalDetail, $customerId, $cartId)
    {
        $requestItems = $items;
        $response = false;
        $customer = $this->customerFactory->create()->load($customerId);
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->getIsVirtual()) {
            $digitalCheckout = true;
        } else {
            $digitalCheckout = false;
            $digitalDetail = [];
        }
        if (!$digitalCheckout) {
            $format = $this->multiShippingHandle->itemsFormat($items);
            $items = $format['item_format'];
            $validateCartItems = $this->multiShippingHandle->validateItems($quote->getAllVisibleItems(), $items);
            $reload = $validateCartItems['reload'];
        } else {
            $reload = false;
        }

        $defaultShippingAddress = $customer->getDefaultShippingAddress();
        $isAddressComplete = ($defaultShippingAddress->getStreetFull() == 'N/A'
            && $defaultShippingAddress->getPostcode() == '*****') ? false : true;
        $skuList = $this->getCartSkuList($quote);
        $isStoreFulFill = (empty($this->msiFullFill->getMsiFullFill($skuList))) ? false : true;
        $addressSelectedId = $this->getAddressSelectedId($shippingAddress);
        $addressesList = $this->getAddressSelectedList($customerId, $addressSelectedId);
        if ($reload) {
            $this->requestItems = $format['item_request'];
            $checkoutSession = $this->multiShipping;
            $checkoutSession->setQuote($quote);
            $checkoutSession->setCustomer($customer->getDataModel());
            $response = $this->handleCheckoutData(
                $cartId,
                $items,
                $additionalInfo,
                $customer,
                $checkoutSession,
                $isAddressComplete,
                $isStoreFulFill,
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
            $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote, false);
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
                CheckoutDataInterface::SHOW_EACH_ITEMS => $this->multiShippingHandle->isShowEachItems($quote->getAllShippingAddresses())
            ];
            $response = $this->getCheckoutData($data);
        }
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function saveMobilePayment($paymentMethod, $customerId, $cartId)
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
                    $termInfo=$this->paymentHelper->getTermInfo($paymentMethod, $term, $quote);
                    $quote->setData('service_fee', ((int)$quote->getGrandTotal() * $termInfo['serviceFeeValue'])/100);
                }
            }
            $this->quoteRepository->save($quote);
            $isErrorCheckout = true;
        } catch (\Exception $e) {
            $isErrorCheckout = false;
        }
        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote, false);
        $data = [
            CheckoutDataInterface::SHIPPING_ADDRESS => [],
            CheckoutDataInterface::ITEMS => [],
            CheckoutDataInterface::ITEMS_MESSAGE => '',
            CheckoutDataInterface::ADDITIONAL_INFO => [],
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
            CheckoutDataInterface::SHOW_EACH_ITEMS => $this->multiShippingHandle->isShowEachItems($quote->getAllShippingAddresses())
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
     * @param $customerId
     * @return mixed
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function handleCheckoutDigital($cartId, $customerId)
    {
        $quote = $this->quoteRepository->getActive($cartId);
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
            CheckoutDataInterface::SHOW_EACH_ITEMS => false
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
