<?php
declare(strict_types=1);

namespace SM\Checkout\Model;

use Magento\Payment\Model\Method\AbstractMethod;
use SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterfaceFactory;
use SM\Checkout\Model\Payment\Authorization;
use SM\DigitalProduct\Api\Data\DigitalInterface;
use SM\GTM\Model\BasketFactory;

/**
 * @api
 */
class MultiShipping implements \SM\Checkout\Api\MultiShippingInterface
{
    const CHECK_USE_FOR_COUNTRY = 'country';

    const CHECK_USE_FOR_CURRENCY = 'currency';

    const CHECK_ORDER_TOTAL_MIN_MAX = 'total';

    const CHECK_ZERO_TOTAL = 'zero_total';

    /**
     * @var array
     */
    protected $quoteItem = [];

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelperData;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var Checkout\Type\Multishipping
     */
    protected $multiShipping;

    /**
     * @var MsiFullFill
     */
    protected $msiFullFill;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var MultiShippingHandle
     */
    protected $multiShippingHandle;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\EstimateShippingInterfaceFactory
     */
    protected $estimateShippingInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterfaceFactory
     */
    protected $itemInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterfaceFactory
     */
    protected $methodInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrderInterfaceFactory
     */
    protected $previewOrderInterfaceFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $digitalInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterfaceFactory
     */
    protected $sourceStoreInterfaceFactory;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\SearchStoreInterfaceFactory
     */
    protected $searchStoreInterfaceFactory;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $blockRepository;
    /**
     * @var \SM\Checkout\Api\Data\Checkout\PlaceOrderInterfaceFactory
     */
    protected $responsePlaceOrderFactory;
    /**
     * @var \SM\Checkout\Api\Data\Checkout\PlaceOrder\PaymentInterfaceFactory
     */
    protected $responsePaymentFactory;

    /**
     * @var \SM\Checkout\Api\VoucherInterface
     */
    protected $voucherInterface;
    /**
     * @var \SM\Checkout\Helper\Config
     */
    protected $helperConfig;
    /**
     * @var BankInterfaceFactory
     */
    protected $bankInterfaceFactory;

    /**
     * @var \SM\Checkout\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    protected $productGtm;

    protected $gtmCheckout;

    protected $productRepository;

    /**
     * @var \SM\GTM\Model\ResourceModel\Basket\CollectionFactory
     */
    protected $basketCollectionFactory;

    /**
     * @var BasketFactory
     */
    protected $basketFactory;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Quote\Api\Data\PaymentInterfaceFactory
     */
    protected $corePaymentInterfaceFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

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
     * @var \SM\Checkout\Api\Data\CheckoutWeb\PaymentMethodInterface
     */
    protected $paymentMethodFactory;

    /**
     * MultiShipping constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Checkout\Helper\Data $checkoutHelperData
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param Checkout\Type\Multishipping $multiShipping
     * @param MsiFullFill $msiFullFill
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param MultiShippingHandle $multiShippingHandle
     * @param \SM\Checkout\Api\Data\CheckoutWeb\EstimateShippingInterfaceFactory $estimateShippingInterfaceFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterfaceFactory $itemInterfaceFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterfaceFactory $methodInterfaceFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterfaceFactory $digitalInterfaceFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterfaceFactory $sourceStoreInterfaceFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\SearchStoreInterfaceFactory $searchStoreInterfaceFactory
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param Authorization $authorization
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
     * @param \SM\Checkout\Api\Data\Checkout\PlaceOrderInterfaceFactory $placeOrderInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\PlaceOrder\PaymentInterfaceFactory $paymentInterfaceFactory
     * @param \SM\Checkout\Api\VoucherInterface $voucherInterface
     * @param \SM\Checkout\Helper\Config $helperConfig
     * @param BankInterfaceFactory $bankInterfaceFactory
     * @param \SM\Checkout\Helper\Payment $paymentHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \SM\GTM\Block\Product\ListProduct $productGtm
     * @param \SM\MobileApi\Api\Data\GTM\GTMCheckoutInterfaceFactory $gtmCheckout
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory
     * @param BasketFactory $basketFactory
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Api\Data\PaymentInterfaceFactory $corePaymentInterfaceFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList
     * @param \Magento\Payment\Model\Method\InstanceFactory $instanceFactory
     * @param \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\PaymentMethodInterfaceFactory $paymentMethodFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Helper\Data $checkoutHelperData,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \SM\Checkout\Model\Checkout\Type\Multishipping $multiShipping,
        \SM\Checkout\Model\MsiFullFill $msiFullFill,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \SM\Checkout\Model\MultiShippingHandle $multiShippingHandle,
        \SM\Checkout\Api\Data\CheckoutWeb\EstimateShippingInterfaceFactory $estimateShippingInterfaceFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterfaceFactory $itemInterfaceFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterfaceFactory $methodInterfaceFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrderInterfaceFactory $previewOrderInterfaceFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterfaceFactory $digitalInterfaceFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterfaceFactory $sourceStoreInterfaceFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\SearchStoreInterfaceFactory $searchStoreInterfaceFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        Authorization $authorization,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \SM\Checkout\Api\Data\Checkout\PlaceOrderInterfaceFactory $placeOrderInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\PlaceOrder\PaymentInterfaceFactory $paymentInterfaceFactory,
        \SM\Checkout\Api\VoucherInterface $voucherInterface,
        \SM\Checkout\Helper\Config $helperConfig,
        BankInterfaceFactory $bankInterfaceFactory,
        \SM\Checkout\Helper\Payment $paymentHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\MobileApi\Api\Data\GTM\GTMCheckoutInterfaceFactory $gtmCheckout,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory,
        BasketFactory $basketFactory,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\Data\PaymentInterfaceFactory $corePaymentInterfaceFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList,
        \Magento\Payment\Model\Method\InstanceFactory $instanceFactory,
        \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\PaymentMethodInterfaceFactory $paymentMethodFactory
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->cartManagement = $cartManagement;
        $this->quoteFactory = $quoteFactory;
        $this->checkoutHelperData = $checkoutHelperData;
        $this->urlInterface = $urlInterface;
        $this->sourceRepository = $sourceRepository;
        $this->multiShipping = $multiShipping;
        $this->msiFullFill = $msiFullFill;
        $this->orderRepository = $orderRepository;
        $this->multiShippingHandle = $multiShippingHandle;
        $this->estimateShippingInterfaceFactory = $estimateShippingInterfaceFactory;
        $this->itemInterfaceFactory = $itemInterfaceFactory;
        $this->methodInterfaceFactory = $methodInterfaceFactory;
        $this->previewOrderInterfaceFactory = $previewOrderInterfaceFactory;
        $this->messageManager = $messageManager;
        $this->digitalInterfaceFactory = $digitalInterfaceFactory;
        $this->sourceStoreInterfaceFactory = $sourceStoreInterfaceFactory;
        $this->searchStoreInterfaceFactory = $searchStoreInterfaceFactory;
        $this->quoteRepository = $quoteRepository;
        $this->authorization = $authorization;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->blockRepository = $blockRepository;
        $this->responsePlaceOrderFactory = $placeOrderInterfaceFactory;
        $this->responsePaymentFactory = $paymentInterfaceFactory;
        $this->voucherInterface = $voucherInterface;
        $this->helperConfig = $helperConfig;
        $this->bankInterfaceFactory = $bankInterfaceFactory;
        $this->paymentHelper = $paymentHelper;
        $this->dateTime = $dateTime;
        $this->priceCurrency = $priceCurrency;
        $this->productGtm = $productGtm;
        $this->gtmCheckout = $gtmCheckout;
        $this->productRepository = $productRepository;
        $this->basketCollectionFactory = $basketCollectionFactory;
        $this->basketFactory = $basketFactory;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->corePaymentInterfaceFactory = $corePaymentInterfaceFactory;
        $this->storeManager = $storeManager;
        $this->paymentMethodList = $paymentMethodList;
        $this->instanceFactory = $instanceFactory;
        $this->methodSpecificationFactory = $methodSpecificationFactory;
        $this->paymentMethodFactory = $paymentMethodFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function saveShippingItems($items, $additionalInfo, $type, $address)
    {
        $this->checkoutSession->setPreShippingType($type)->setPreAddress(explode(',', $address));
        $storePickUp = $additionalInfo->getStorePickUp();
        $checkoutSession = $this->multiShipping;
        $customer = $this->customerSession->getCustomer();
        $dataHandle = $this->multiShippingHandle->handleData(
            $items,
            $storePickUp,
            $customer,
            $checkoutSession
        );
        return $this->estimateHandle($dataHandle);
    }

    /**
     * @param $dataHandle
     * @return \SM\Checkout\Api\Data\CheckoutWeb\EstimateShippingInterface
     */
    protected function estimateHandle($dataHandle)
    {
        if ($dataHandle['reload']) {
            $this->messageManager->addWarning(__("Some products are updated."));
            return $this->getEstimateResponse(true, $dataHandle, '');
        }
        $message = $this->multiShippingHandle->handleMessage($dataHandle);
        return $this->getEstimateResponse(false, $dataHandle, $message);
    }

    /**
     * @param $reload
     * @param $dataHandle
     * @param $message
     * @return \SM\Checkout\Api\Data\CheckoutWeb\EstimateShippingInterface
     */
    protected function getEstimateResponse($reload, $dataHandle, $message)
    {
        return $this->estimateShippingInterfaceFactory->create()
            ->setReload($reload)
            ->setItemsValidMethod($dataHandle['data'])
            ->setError($dataHandle['error'])
            ->setIsSplitOrder($dataHandle['split'])
            ->setStockMessage($message)
            ->setShowEachItems($dataHandle['show_each_items']);
    }

    /**
     * @param $cartId
     * @return int|string
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function placeOrderMobile($cartId)
    {
        $response = $this->responsePlaceOrderFactory->create();
        $this->checkoutSession->setArea(\SM\Checkout\Helper\OrderReferenceNumber::AREA_APP);
        try {
            $quote = $this->quoteRepository->get($cartId);
            $paymentMethod = $quote->getPayment()->getMethod();
            $payment = $this->corePaymentInterfaceFactory->create()->setMethod($paymentMethod);
            $this->paymentMethodManagement->set($cartId, $payment);
            $orderId = $this->cartManagement->placeOrder($cartId);
            $mainOrder = $this->orderRepository->get($orderId);
        } catch (\Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
            return $response;
        }
        $pg = $this->authorization->sendOrderToPaymentGateway($orderId);
        $suborderCollection = $this->orderCollectionFactory->create()->addAttributeToSelect('*')
            ->addFieldToFilter('parent_order', $orderId);
        $suborderIds = $suborderCollection->getAllIds();
        $orderIds = $orderId . ',' . implode(",", $suborderIds);
        $response->setOrderIds($orderIds);
        $paymentMethod = $mainOrder->getPayment()->getMethod();
        $payment = $this->responsePaymentFactory->create()
            ->setPaymentMethod($paymentMethod ?? '')
            ->setStatus($pg['insertStatus'] ?? '')
            ->setMessage($pg['insertMessage'] ?? '')
            ->setRedirectUrl($pg['redirectURL'] ?? '');
        $bank = $this->bankInterfaceFactory->create()
            ->setLogo($this->paymentHelper->getLogoPayment($paymentMethod, true))
            ->setTitle($mainOrder->getPayment()->getMethodInstance()->getTitle());

        if ($this->paymentHelper->isVirtualAccount($paymentMethod) && !empty($pg['account_number'])) {
            $bank->setMinimumAmount($this->paymentHelper->getMinimumAmountVA());
            $expiredTime = $pg['expired_time'] ?? null;
            if (!empty($expiredTime)) {
                $utc = strtotime($expiredTime) - $this->dateTime->getGmtOffset();
                $expiredTime = date('Y-m-d H:i:s', $utc);
            }
            $payment->setAccountNumber($pg['account_number'] ?? '')
                ->setExpiredTime($expiredTime)
                ->setHowToPayObjects($this->paymentHelper->getBlockHowToPay($paymentMethod, true))
                ->setReferenceNumber($pg['reference_number'])
                ->setTotalAmount($pg['total_amount'])
                ->setRelateUrl($this->paymentHelper->getWebsiteBanking($paymentMethod));
        }
        $payment->setBank($bank);
        $error = false;
        if (!empty($pg['insertStatus']) && $pg['insertStatus'] != '00') {
            $error = true;
        }
        $response->setError($error)
            ->setPayment($payment);

        $response = $this->getGTMData($suborderCollection, $mainOrder, $response);

        $response->setBasketValue($mainOrder->getGrandTotal());
        $customerId = $mainOrder->getCustomerId();
        if ($customerId) {
            $basket = $this->basketCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
            if (!$basket->getData()) {
                $basket = $this->basketFactory->create();
                $basket->setData('customer_id', $customerId);
                $basket->save();
            }
            $response->setBasketID($basket->getId() ?? null);
        }
        $response->setTransactionId($orderId ?? 'Not Available');
        $response->setTotalPayment($mainOrder->getGrandTotal());
        $response->setPaymentMethod($paymentMethod ?? '');
        $response->setBankType($mainOrder->getPayment()->getMethodInstance()->getTitle());
        $response->setShippingMethod($mainOrder->getShippingMethod());

        return $response;
    }

    /**
     * @param $suborderCollection
     * @param $mainOrder
     * @param $response
     * @return mixed
     */
    public function getGTMData($suborderCollection, $mainOrder, $response)
    {
        $gtmData = [];
        $totalQty = 0;
        $shippingDateTime = ['date' => '', 'time' => ''];
        foreach ($suborderCollection as $subOrder) {
            $shippingMethod = $subOrder->getShippingMethod();
            foreach ($subOrder->getAllItems() as $item) {
                $item->setData('shipping_method', $shippingMethod);
                $data = $this->getGTM($item, $mainOrder);
                $gtmData[] = $data;
                $totalQty += $item->getQtyOrdered();
            }
            if ($shippingDateTime['date'] != '' && $shippingMethod == \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
                $shippingDateTime['date'] = $subOrder->getStorePickUpDate();
                $shippingDateTime['time'] = $subOrder->getStorePickUpTime();
            } elseif ($shippingDateTime['date'] != '' && $shippingMethod == \SM\Checkout\Model\MultiShippingHandle::SCHEDULE) {
                $shippingDateTime['date'] = $subOrder->getDate();
                $shippingDateTime['time'] = $subOrder->getTime();
            }
        }
        $response->setBasketQty($totalQty);
        $response->setGtmData($gtmData);
        $response->setShippingDate(($shippingDateTime['date'] && $shippingDateTime['date'] != '') ? $shippingDateTime['date'] : 'Not Available');
        $response->setShippingTime(($shippingDateTime['time'] && $shippingDateTime['time'] != '') ? $shippingDateTime['time'] : 'Not Available');
        return $response;
    }

    protected function getGTM($item, $order)
    {
        $product = $this->productRepository->getById($item->getProduct()->getId());
        if ($product->getId()) {
            $model = $this->gtmCheckout->create();
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
            $model->setProductQty($item->getQtyOrdered());
            if ($data['salePrice'] && $data['salePrice'] > 0) {
                $model->setProductOnSale(__('Yes'));
            } else {
                $model->setProductOnSale(__('Not on sale'));
            }
            $voucher = $order->getCouponCode();
            if ($voucher != null && $voucher != '') {
                $model->setApplyVoucher(__('Yes'));
                $model->setVoucherId($voucher);
            } else {
                $model->setApplyVoucher(__('No'));
                $model->setVoucherId('');
            }

            $model->setShippingMethod($item->getData('shipping_method'));

            return $model;
        } else {
            return null;
        }
    }

    /**
     * set payment method
     *
     * @param int $customerId
     * @param string $paymentMethod
     * @param int $serviceFee
     *
     * @return false|string
     */
    public function savePayment($customerId, $paymentMethod, $serviceFee = 0)
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

        $data = [
            'error' => true,
            'message' => '',
            'url' => $this->urlInterface->getUrl('transcheckout')
        ];

        $customerSession = $this->multiShipping->getCustomerSession();
        if ($customerSession->getId() != $customerId) {
            $data['message'] = __('Invalid customer!');
            return json_encode($data);
        }
        try {
            $this->multiShipping->setPaymentMethod($payment, $serviceFee);
            $data['message'] = __('Success');
            $data['error'] = false;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            $data['error'] = true;
            return json_encode($data);
        }
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function previewOrder($items, $storeDateTime, $deliveryDateTime, $isSplitOrder, $store, $updatePaymentMethod)
    {
        $itemsFormat = [];
        foreach ($items as $item) {
            $itemsFormat[$item->getItemId()] = [
                "shipping_method" => $item->getShippingMethodSelected(),
                "shipping_address" => $item->getShippingAddressId(),
                "qty" => $item->getQty()
            ];
        }
        $checkoutSession = $this->multiShipping;
        $validateCartItems = $this->multiShippingHandle->validateItems(
            $checkoutSession->getQuote()->getAllVisibleItems(),
            $itemsFormat
        );
        $reload = $validateCartItems['reload'];
        if ($reload) {
            $this->messageManager->addWarning(__("Some products are updated."));
            return $this->previewOrderInterfaceFactory->create()->setReload(true)->setOrder([])->setIsSplitOrder(false);
        }
        $storeDateTime = $this->storePickUpFormat($storeDateTime, $store);
        $deliveryDateTime = $this->formatDeliveryDateTime($deliveryDateTime);
        $quote = $checkoutSession->getQuote();
        $reload = $this->multiShippingHandle->handlePreviewOrder($deliveryDateTime, $storeDateTime, $quote);
        if ($reload) {
            $this->messageManager->addWarning(__("An error occurred, please checkout again!"));
            return $this->previewOrderInterfaceFactory->create()->setReload(true)->setOrder([])->setIsSplitOrder(false);
        }
        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote);
        if ($this->helperConfig->showOrderSummary()) {
            $isSplitOrder = true;
        }
        if (!$updatePaymentMethod) {
            $paymentMethodList = $this->getPaymentMethod();
        } else {
            $paymentMethodList = [];
        }
        return $this->previewOrderInterfaceFactory->create()->setReload(false)->setOrder($previewOrderProcess['preview_order'])->setIsSplitOrder($isSplitOrder)->setPaymentMethod($paymentMethodList);
    }

    /**
     * {@inheritdoc}
     */
    public function searchStore($lat, $lng, $storePickupItems, $currentStoreCode)
    {
        $response = $this->searchStoreInterfaceFactory->create();
        $data = [];
        $skuList = [];
        $quote = $this->multiShipping->getQuote();
        $child = [];
        $destinationLatLng = $this->msiFullFill->addLatLngInterface($lat, $lng);
        $distanceOfCurrentStore = $this->msiFullFill->getDistanceBetweenCurrentStoreAndAddress(
            $currentStoreCode,
            $destinationLatLng
        );
        if ($distanceOfCurrentStore) {
            $response->setCurrentStore($this->sourceStoreInterfaceFactory->create()->setSourceCode($currentStoreCode)->setDistance($distanceOfCurrentStore));
        }
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItemId() && $item->getParentItemId() != null) {
                $child[$item->getParentItemId()] = $item->getSku();
            }
        }
        foreach ($quote->getAllVisibleItems() as $item) {
            if (in_array($item->getId(), $storePickupItems)) {
                $product = $item->getProduct();
                $isWarehouse = (bool)$product->getIsWarehouse();
                if ($isWarehouse) {
                    continue;
                }
                $sku = (isset($child[$item->getItemId()])) ? $child[$item->getItemId()] : $item->getSku();
                $skuList[$sku] = $item->getQty();
            }
        }
        $sourceList = $this->msiFullFill->getMsiFullFill($skuList);
        if (empty($sourceList)) {
            return $response;
        }
        foreach ($this->msiFullFill->sortSourceByDistance($sourceList, $destinationLatLng, true) as $source) {
            $data[] = $this->sourceStoreInterfaceFactory->create()->setSourceCode($source['source_code'])->setDistance($source['distance']);
        }
        $response->setShortestStoreList($data);
        return $response;
    }

    /**
     * @param $storeDateTime
     * @param $store
     * @return array
     */
    public function storePickUpFormat($storeDateTime, $store)
    {
        return [
            "store_code" => $store,
            "date" => $storeDateTime->getDate(),
            "time" => $storeDateTime->getTime(),
        ];
    }

    /**
     * @param $deliveryDateTime
     * @return array
     */
    protected function formatDeliveryDateTime($deliveryDateTime)
    {
        $deliveryDateTimeFormat = [];
        foreach ($deliveryDateTime as $delivery) {
            $deliveryDateTimeFormat[$delivery->getAddress()] = [
                'date' => $delivery->getDate(),
                'time' => $delivery->getTime()
            ];
        }
        return $deliveryDateTimeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function resetPaymentFail()
    {
        $quote = $this->multiShipping->getQuote();
        $quote->setPaymentFailureTime(null)->save();
        $previewOrderProcess = $this->multiShippingHandle->getPreviewOrderData($quote);
        $isSplitOrder = false;
        return $this->previewOrderInterfaceFactory->create()->setOrder($previewOrderProcess['preview_order'])->setIsSplitOrder($isSplitOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function digitalDetail()
    {
        $data = [];
        foreach ($this->multiShipping->getQuote()->getAllVisibleItems() as $item) {
            $options = $item->getBuyRequest()->toArray();
            $product = $item->getProduct();
            foreach ($options['digital'] as $code => $option) {
                $data[] = $this->digitalInterfaceFactory->create()
                    ->setLabel($this->getDigitalLabel($code))
                    ->setValue($this->formatValue($code, $option, $product));
            }
        }
        return $data;
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

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentMethod()
    {
        $quote = $this->checkoutSession->getQuote();
        $store = $quote ? $quote->getStoreId() : null;
        $methods = [];
        $logoUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        foreach ($this->paymentMethodList->getActiveList($store) as $method) {
            $methodInstance = $this->instanceFactory->create($method);
            if ($method->getTitle() && $methodInstance->isAvailable($quote) && $this->canUseMethod($methodInstance, $quote)) {
                $description = $this->helperConfig->getPaymentDescription($method->getCode());
                $tooltipDescription = $this->helperConfig->getPaymentTooltipDescription($method->getCode());
                $logo = "logo/paymentmethod/" . $this->helperConfig->getPaymentLogo($method->getCode());
                $methodObj = $this->paymentMethodFactory->create();
                $methodObj->setTitle($method->getTitle());
                $methodObj->setStoreId($method->getStoreId());
                $methodObj->setIsActive($method->getIsActive());
                $methodObj->setDescription($description);
                $methodObj->setTooltipDescription($tooltipDescription);
                $methodObj->setLogo($logoUrl . $logo);
                $methodObj->setMethod($method->getCode());
                $methods[] = $methodObj;
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
}
