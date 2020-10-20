<?php

namespace SM\Sales\Model\Order;

use Amasty\Rules\Model\DiscountBreakdownLine as VoucherDetailData;
use Amasty\Rules\Model\DiscountBreakdownLineFactory as VoucherDetailDataFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use SM\Checkout\Helper\Payment;
use SM\Sales\Api\Data\DigitalOrderDataInterface;
use SM\Sales\Api\Data\ParentOrderDataInterface;
use SM\Sales\Api\Data\ParentOrderDataInterfaceFactory;
use SM\Sales\Api\Data\PaymentInfoDataInterface;
use SM\Sales\Api\Data\PaymentInfoDataInterfaceFactory;
use SM\Sales\Api\Data\SubOrderDataInterface;
use Trans\Sprint\Api\Data\SprintResponseInterface;
use Trans\Sprint\Api\SprintResponseRepositoryInterface;
use Trans\Sprint\Model\SprintResponse;

/**
 * Class ParentOrder
 * @package SM\Sales\Model\Order
 */
class ParentOrder
{
    /**
     * @var ParentOrderDataInterfaceFactory
     */
    protected $parentOrderDataFactory;

    /**
     * @var PaymentInfoDataInterfaceFactory
     */
    protected $paymentInfoDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var SprintResponseRepositoryInterface
     */
    protected $sprintResponseRepository;

    /**
     * @var VoucherDetailDataFactory
     */
    protected $voucherDetailDataFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var Payment
     */
    protected $paymentHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    private $howToPay;

    private $logo;

    /**
     * ParentOrder constructor.
     * @param ParentOrderDataInterfaceFactory $parentOrderDataFactory
     * @param PaymentInfoDataInterfaceFactory $paymentInfoDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param SprintResponseRepositoryInterface $sprintResponseRepository
     * @param VoucherDetailDataFactory $voucherDetailDataFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param Payment $paymentHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ParentOrderDataInterfaceFactory $parentOrderDataFactory,
        PaymentInfoDataInterfaceFactory $paymentInfoDataFactory,
        DataObjectHelper $dataObjectHelper,
        SprintResponseRepositoryInterface $sprintResponseRepository,
        VoucherDetailDataFactory $voucherDetailDataFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\UrlInterface $urlInterface,
        Payment $paymentHelper,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->paymentHelper = $paymentHelper;
        $this->voucherDetailDataFactory = $voucherDetailDataFactory;
        $this->sprintResponseRepository = $sprintResponseRepository;
        $this->parentOrderDataFactory = $parentOrderDataFactory;
        $this->paymentInfoDataFactory = $paymentInfoDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->timezone = $timezone;
        $this->urlInterface = $urlInterface;
    }

    /**
     * @param Order $parentOrder
     * @param SubOrderDataInterface[] $subOrders
     * @param bool $hasInvoice
     * @return ParentOrderDataInterface
     */
    public function parentOrderProcess($parentOrder, $subOrders)
    {
        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $parentOrder->getPayment();

        foreach ($subOrders as $key => $subOrder) {
            if ($subOrder->getId() == $parentOrder->getEntityId() && count($subOrders) >= 2) {
                unset($subOrders[$key]);
            }
        }

        /** @var ParentOrderDataInterface $parentOrderData */
        $parentOrderData = $this->parentOrderDataFactory->create();
        $parentOrderData
            ->setIsDigital($parentOrder->getIsVirtual())
            ->setParentOrderId($parentOrder->getEntityId())
            ->setReferenceNumber($parentOrder->getReferenceNumber())
            ->setOrderDate($parentOrder->getCreatedAt())
            ->setStatus($parentOrder->getStatus())
            ->setTotalShippingAmount($parentOrder->getShippingAmount())
            ->setPaymentMethod($payment != null ? $payment->getMethodInstance()->getTitle() : null)
            ->setTotalPayment($parentOrder->getGrandTotal())
            ->setSubTotal($parentOrder->getSubtotal())
            ->setInvoiceNumber($parentOrder->getReferenceInvoiceNumber())
            ->setSubOrders($subOrders)
            ->setConvertDate($this->convertDate($parentOrder->getCreatedAt()));

        if ($parentOrder->getReferenceInvoiceNumber()) {
            $invoiceLink = $this->getUrl('sales/invoice/view', ['id' => $parentOrder->getId()]);
            $parentOrderData->setInvoiceLink($invoiceLink);
        }

        $this->voucherDetailProcess($parentOrderData, $parentOrder);
        $parentOrderData->setPaymentInfo(is_null($payment) ?
            $payment : $this->paymentInfoProcess($payment, $parentOrder->getReferenceNumber()));
        return $parentOrderData;
    }

    public function convertDate($date)
    {
        return $this->timezone->date($date)->format("d M Y | h:i A");
    }
    /**
     * @param OrderPaymentInterface $payment
     * @param string|null $orderIncrementId
     * @return PaymentInfoDataInterface
     */
    public function paymentInfoProcess($payment, $orderIncrementId = null)
    {
        /** @var PaymentInfoDataInterface $paymentInfoData */
        $paymentInfoData = $this->paymentInfoDataFactory->create();
        $method = $payment->getMethod();
        $data[PaymentInfoDataInterface::METHOD] = $method;

        $paymentMethodSplit = explode("_", $method);
        if ($orderIncrementId != null) {
            /** @var SprintResponse $sprintOrder */
            $sprintOrder = $this->getSprintOrder($orderIncrementId);
            $transactionId = $this->getTransactionId($sprintOrder);
            $data[PaymentInfoDataInterface::TRANSACTION_ID] = $transactionId;

            if (is_array($paymentMethodSplit)) {
                $methodShort = $paymentMethodSplit[count($paymentMethodSplit) - 1];
                if ($methodShort == "va") {
                    $expireDate = $this->getExpireTimeByOrder($sprintOrder);
                    $virtualAccount = $this->getVirtualAccount($sprintOrder);
                    $data[PaymentInfoDataInterface::EXPIRE_DATE] = $expireDate;
                    $data[PaymentInfoDataInterface::VIRTUAL_ACCOUNT] = $virtualAccount;
                }

                if ($methodShort == "cc") {
                    $redirectUrl = $this->getRedirectUrl($sprintOrder);
                    $data[PaymentInfoDataInterface::REDIRECT_URL] = $redirectUrl;
                }
            }
        }
        $additionalDataArray = $payment->getAdditionalInformation();

        if (isset($additionalDataArray["raw_details_info"])) {
            $detailsInfo = $additionalDataArray["raw_details_info"];
            if (isset($detailsInfo["transactionFeature"])) {
                $transactionFeatureJson = $detailsInfo["transactionFeature"];
                $transactionFeatureArray = json_decode($transactionFeatureJson, true);
                $data[PaymentInfoDataInterface::BANK_ISSUER] = $transactionFeatureArray["bank_issuer"];
                $data[PaymentInfoDataInterface::CARD_NO] = $additionalDataArray["raw_details_info"]["cardNo"];
                $data[PaymentInfoDataInterface::CARD_TYPE] = $transactionFeatureArray["card_type"];
                $data[PaymentInfoDataInterface::CARD_BRAND] = $transactionFeatureArray["card_brand"];
            }
        }

        $data[PaymentInfoDataInterface::LOGO] = $this->getLogo($method);

        $this->dataObjectHelper->populateWithArray(
            $paymentInfoData,
            $data,
            PaymentInfoDataInterface::class
        );

        $paymentInfoData->setHowToPayObjects($this->getHowToPay($method));

        return $paymentInfoData;
    }

    /**
     * @param SprintResponse $sprintOrder
     * @return string
     */
    private function getTransactionId($sprintOrder)
    {
        if ($sprintOrder->getId()) {
            return $sprintOrder->getTransactionNo();
        }
        return '';
    }

    /**
     * @param SprintResponse $sprintOrder
     * @return string
     */
    private function getExpireTimeByOrder($sprintOrder)
    {
        if ($sprintOrder->getId()) {
            return $sprintOrder->getExpireDate();
        }
        return '';
    }
    /**
     * @param SprintResponse $sprintOrder
     * @return string
     */
    private function getVirtualAccount($sprintOrder)
    {
        if ($sprintOrder->getId()) {
            return $sprintOrder->getData(SprintResponseInterface::CUSTOMER_ACCOUNT);
        }
        return '';
    }

    /**
     * Get redirect URL
     *
     * @param SprintResponse $sprintOrder
     * @return string
     */
    private function getRedirectUrl($sprintOrder)
    {
        if ($sprintOrder->getId()) {
            return $sprintOrder->getData(SprintResponseInterface::REDIRECT_URL);
        }
        return '';
    }

    /**
     * @param string $orderIncrementId
     * @return SprintResponseInterface
     */
    private function getSprintOrder($orderIncrementId)
    {
        return $this->sprintResponseRepository->getByTransactionNo($orderIncrementId);
    }

    /**
     * @param ParentOrderDataInterface|DigitalOrderDataInterface $parentOrderData
     * @param Order $parentOrderModel
     */
    public function voucherDetailProcess($parentOrderData, $parentOrderModel)
    {
        $data = $parentOrderModel->getData("voucher_detail");
        $details = json_decode($data, true);
        $result = [];
        if (is_array($details)) {
            foreach ($details as $detail) {
                /** @var VoucherDetailData $voucherDetailData */
                $voucherDetailData = $this->voucherDetailDataFactory
                    ->create()
                    ->setRuleAmount($detail[VoucherDetailData::RULE_AMOUNT]??"")
                    ->setRuleName($detail[VoucherDetailData::RULE_NAME]??"");
                $result[] = $voucherDetailData;
            }
        }
        $result[] = $this->voucherDetailDataFactory
            ->create()
            ->setRuleName(__("Voucher/Promo/Discount"))
            ->setRuleAmount($parentOrderModel->getDiscountAmount());
        $parentOrderData->setVoucherDetail($result);
    }

    /**
     * @param string $method
     * @return mixed|string|null
     */
    private function getLogo($method)
    {
        if (!isset($this->logo[$method])) {
            $this->logo[$method] = $this->paymentHelper->getLogoPayment($method, true);
        }
        return $this->logo[$method];
    }

    /**
     * @param $method
     * @return mixed|string|null
     */
    private function getHowToPay($method)
    {
        if (!isset($this->howToPay[$method])) {
            try {
                $howToPay = $this->paymentHelper->getBlockHowToPay($method, true);
            } catch (LocalizedException $e) {
                $this->logger->critical($e->getMessage());
                $howToPay = null;
            }
            $this->howToPay[$method] = $howToPay;
        }
        return $this->howToPay[$method];
    }

    /**
     * @param $path
     * @param $params
     * @return string
     */
    public function getUrl($path, $params = null)
    {
        return  $this->urlInterface->getUrl($path, $params);
    }
}
