<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Model;

use SM\Checkout\Model\MultiShipping;
use Trans\Mepay\Helper\Data as MainHelper;

class TransmartMultishipping extends MultiShipping
{
    /**
     * @var \SM\GTM\Model\ResourceModel\Basket\CollectionFactory
     */
    private $basketCollectionFactory;
    /**
     * @var BasketFactory
     */
    private $basketFactory;

    /**
     * @inheritdoc
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
        \SM\Checkout\Model\Payment\Authorization $authorization,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \SM\Checkout\Api\Data\Checkout\PlaceOrderInterfaceFactory $placeOrderInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\PlaceOrder\PaymentInterfaceFactory $paymentInterfaceFactory,
        \SM\Checkout\Api\VoucherInterface $voucherInterface,
        \SM\Checkout\Helper\Config $helperConfig,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterfaceFactory $bankInterfaceFactory,
        \SM\Checkout\Helper\Payment $paymentHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\MobileApi\Api\Data\GTM\GTMCheckoutInterfaceFactory $gtmCheckout,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\MobileApi\Api\Data\GTM\BasketInterfaceFactory $basketInterfaceFactory,
        \SM\GTM\Model\ResourceModel\Basket\CollectionFactory $basketCollectionFactory,
        \SM\GTM\Model\BasketFactory $basketFactory,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\Data\PaymentInterfaceFactory $corePaymentInterfaceFactory
    ) {
        $this->basketCollectionFactory = $basketCollectionFactory;
        $this->basketFactory = $basketFactory;
        parent::__construct(
          $customerSession,
          $checkoutSession,
          $cartManagement,
          $quoteFactory,
          $checkoutHelperData,
          $urlInterface,
          $sourceRepository,
          $multiShipping,
          $msiFullFill,
          $orderRepository,
          $multiShippingHandle,
          $estimateShippingInterfaceFactory,
          $itemInterfaceFactory,
          $methodInterfaceFactory,
          $previewOrderInterfaceFactory,
          $messageManager,
          $digitalInterfaceFactory,
          $sourceStoreInterfaceFactory,
          $searchStoreInterfaceFactory,
          $quoteRepository,
          $authorization,
          $orderCollectionFactory,
          $blockRepository,
          $placeOrderInterfaceFactory,
          $paymentInterfaceFactory,
          $voucherInterface,
          $helperConfig,
          $bankInterfaceFactory,
          $paymentHelper,
          $dateTime,
          $priceCurrency,
          $productGtm,
          $gtmCheckout,
          $productRepository,
          $basketInterfaceFactory,
          $basketCollectionFactory,
          $basketFactory,
          $paymentMethodManagement,
          $corePaymentInterfaceFactory
        );
    }

    /**
     * @inheritdoc
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

        //bank mega payment processing
        if (MainHelper::isMegaMethod($mainOrder->getPayment()->getMethod())) {
          return $this->processByBankMegaPayment($cartId, $response, $mainOrder, $quote);
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
     * Process bank mega payment
     * @param $cartId
     * @param $response
     * @param $mainOrder
     * @param $quote
     * @return $response
     */
    public function processByBankMegaPayment($cartId, $response, $mainOrder, $quote)
    {
        $suborderCollection = $this->orderCollectionFactory->create()->addAttributeToSelect('*')
            ->addFieldToFilter('parent_order', $mainOrder->getId());
        $suborderIds = $suborderCollection->getAllIds();
        $orderIds = $mainOrder->getId() . ',' . implode(",", $suborderIds);
        $response->setOrderIds($orderIds);
        $paymentMethod = $mainOrder->getPayment()->getMethod();
        $txnData = $mainOrder->getPayment()->getAdditionalInformation();
        $txnRaws = (isset($txnData['raw_details_info']))? $txnData['raw_details_info'] : [];
        $payment = $this->responsePaymentFactory->create()
            ->setPaymentMethod($paymentMethod ?? '')
            ->setStatus($txnRaws['status'] ?? '')
            ->setMessage($txnRaws['referenceId'] ?? '')
            ->setRedirectUrl($txnRaws['urls']['checkout'] ?? '');

        $error = false;
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
}
