<?php

namespace SM\Checkout\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PricingData;
use Magento\Quote\Model\QuoteFactory;
use SM\Checkout\Api\PaymentInterface;
use Trans\Sprint\Helper\Config;

class Payment implements PaymentInterface
{
    /**
     * @var Session
     */
    protected $sessionCheckout;
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var Config
     */
    protected $configHelper;
    /**
     * @var PricingData
     */
    protected $pricingData;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var \SM\Checkout\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Payment constructor.
     * @param QuoteFactory $quoteFactory
     * @param Session $sessionCheckout
     * @param Config $configHelper
     * @param PricingData $pricingData
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \SM\Checkout\Helper\Payment $paymentHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        Session $sessionCheckout,
        Config $configHelper,
        PricingData $pricingData,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \SM\Checkout\Helper\Payment $paymentHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
        $this->quoteFactory = $quoteFactory;
        $this->sessionCheckout = $sessionCheckout;
        $this->configHelper = $configHelper;
        $this->pricingData = $pricingData;
        $this->jsonHelper = $json;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param string $customerId
     * @param string $paymentMethod
     * @param null $quote
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInstalmentTerm($customerId, $paymentMethod, $quote = null)
    {
        return $this->paymentHelper->getInstalmentTerm($paymentMethod, $quote);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param false $cancelOrder
     */
    public function paymentFailed(\Magento\Sales\Model\Order $order, $cancelOrder = false)
    {
        if ($cancelOrder) {
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
            try {
                $order->save();
            } catch (\Exception $e) {
            }

        }
        $this->cancelSubOrders($order);
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        if (!empty($quote)) {
            $quote->setIsActive(1);
            $quote->setPaymentFailureTime((int)$quote->getPaymentFailureTime() + 1);
            $quote->save();
        }

        $this->messageManager->addErrorMessage(__("Sorry, we couldn't process your payment. Please select another payment method."));
    }

    /**
     * @param $order
     */
    protected function cancelSubOrders($order)
    {
        $suborders = $this->orderCollectionFactory->create()->addAttributeToSelect('entity_id')
            ->addFieldToFilter('parent_order', $order->getId());
        foreach ($suborders as $sub) {
            $sub->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            $sub->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
            try {
                $sub->save();
            } catch (\Exception $e) {
            }
        }
    }
}
