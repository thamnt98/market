<?php

namespace SM\Checkout\Plugin\TransSprint\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use SM\Email\Model\Email\Sender as EmailSender;
use Trans\Sprint\Helper\Config;

class PaymentSendEmail
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var string
     */
    protected $paymentStatusCode;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var \SM\Checkout\Helper\Email
     */
    protected $emailHelper;

    /**
     * PaymentNotify constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param EmailSender $emailSender
     * @param Renderer $addressRenderer
     * @param PriceHelper $priceHelper
     * @param \SM\Checkout\Helper\Email $emailHelper
     * @param \Magento\Framework\Logger\Monolog|null $logger
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        EmailSender $emailSender,
        Renderer $addressRenderer,
        PriceHelper $priceHelper,
        \SM\Checkout\Helper\Email $emailHelper,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->priceHelper = $priceHelper;
        $this->addressRenderer = $addressRenderer;
        $this->emailHelper = $emailHelper;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->logger = $logger;
        $this->emailSender = $emailSender;
        $this->paymentStatusCode = null;
    }

    public function afterProcessingNotify(
        \Trans\Sprint\Api\PaymentNotifyInterface $subject,
        $result,
        $postData
    ) {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $coll */
        $coll = $this->orderCollectionFactory->create();
        $coll->addFieldToFilter('reference_number', $postData['transactionNo']);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $coll->getFirstItem();
        if (empty($postData['transactionStatus']) || !$order->getId() || !$order->getPayment()) {
            $this->logError("Can't send email", $postData);

            return $result;
        } else {
            $this->paymentStatusCode = $postData['transactionStatus'];
        }

        try {
            $this->sendEmail($order);
            $this->logInfo('Send Email Success', $postData);
        } catch (\Exception $e) {
            $this->logError(
                "Send Email error:\n\t" . $e->getMessage(),
                ['order_id' => $order->getId(), 'post_data' => $postData]
            );
        }

        return $result;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function sendEmail($order)
    {
        switch ($this->paymentStatusCode) {
            case Config::PAYMENT_FLAG_DECLINED_04: // Payment Expired
                return $this->createExpired($order);
            case Config::PAYMENT_FLAG_DECLINED_05: // Payment Reject by Bank
                return $this->createBankReject($order);
            case Config::PAYMENT_FLAG_DECLINED_01: // Payment System Failed
            case Config::PAYMENT_FLAG_DECLINED_02:
            case Config::PAYMENT_FLAG_DECLINED_03:
            case Config::PAYMENT_FLAG_DECLINED_06:
                return $this->createSystemFailed($order);
            case Config::PAYMENT_FLAG_SUCCESS_CODE: // Payment Success
            case Config::TRANSACTION_STATUS_SUCCESS:
                return $this->createSuccess($order);
            default:
                return true;
        }
    }

    /**
     * @param string $message
     * @param array  $params
     */
    protected function logError($message, $params)
    {
        if ($this->logger) {
            $this->logger->error($message, $params);
        }
    }

    /**
     * @param string $message
     * @param array  $params
     */
    protected function logInfo($message, $params)
    {
        if ($this->logger) {
            $this->logger->info($message, $params);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $templateId
     * @param $sender
     * @param array $templateVars
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function sendMailParam($order, $templateId, $sender, $templateVars = [])
    {
        $email = $order->getCustomerEmail();
        $name = $order->getCustomerName();
        $this->emailSender->send(
            $templateId,
            $sender,
            $email,
            $name,
            $templateVars,
            (int) $order->getStoreId()
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function createSuccess($order)
    {
        $templateId = $this->emailHelper->getPaymentSuccessPhysicalTemplateId();
        if (!$templateId) {
            throw new LocalizedException(__('Template does not exist'));
        }

        $sender = $this->emailHelper->getPaymentSuccessPhysicalSender();

        $templateVars = [
            "order" => $order,
            "order_total" => $this->getPrice($order->getGrandTotal()),
            "delivery_method" => $this->getDeliveryMethod($order->getShippingMethod(), $order->getShippingDescription()),
            "formattedShippingAddress" => $this->getFormattedShippingAddress($order)
        ];

        $this->sendMailParam($order, $templateId, $sender, $templateVars);
        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function createExpired($order)
    {
        $templateId = $this->emailHelper->getExpiredTemplateId();
        if (!$templateId) {
            throw new LocalizedException(__('Template does not exist'));
        }

        $sender = $this->emailHelper->getExpiredSender();
        $templateVars = [
            'order' => $order
        ];

        $this->sendMailParam($order, $templateId, $sender, $templateVars);
        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function createBankReject($order)
    {
        $templateId = $this->emailHelper->getBankRejectTemplateId();
        if (!$templateId) {
            throw new LocalizedException(__('Template does not exist'));
        }

        $sender = $this->emailHelper->getBankRejectSender();
        $templateVars = [
            'order' => $order
        ];

        $this->sendMailParam($order, $templateId, $sender, $templateVars);
        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function createSystemFailed($order)
    {
        $templateId = $this->emailHelper->getSystemFailedTemplateId();
        if (!$templateId) {
            throw new LocalizedException(__('Template does not exist'));
        }

        $sender = $this->emailHelper->getSystemFailedSender();
        $templateVars = [
            'order' => $order
        ];

        $this->sendMailParam($order, $templateId, $sender, $templateVars);
        return true;
    }

    /**
     * @param string $shippingMethod
     * @param string $shippingDescription
     * @return \Magento\Framework\Phrase|mixed|string
     */
    private function getDeliveryMethod($shippingMethod, $shippingDescription)
    {
        if ($shippingMethod == "store_pickup_store_pickup") {
            return __("Pick Up in Store");
        } else {
            $shippingDescription = explode(" - ", $shippingDescription);
            if (isset($shippingDescription[1])) {
                return $shippingDescription[1];
            }
        }
        return __("Not available");
    }

    /**
     * Render shipping address into html.
     *
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @param $price
     * @return string
     */
    public function getPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
