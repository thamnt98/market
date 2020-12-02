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
namespace Trans\MepayTransmart\Model\Order;

use Amasty\Rules\Model\DiscountBreakdownLine as VoucherDetailData;
use Amasty\Rules\Model\DiscountBreakdownLineFactory as VoucherDetailDataFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
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
use SM\Sales\Model\Order\ParentOrder;
use Trans\Mepay\Helper\Data;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Magento\Framework\Serialize\Serializer\Json;

class TransmartParentOrder extends ParentOrder
{
  protected $transactionHelper;
  protected $json;
  /** 
   * @inheritdoc
   */
  public function __construct(
    ParentOrderDataInterfaceFactory $parentOrderDataFactory,
    PaymentInfoDataInterfaceFactory $paymentInfoDataFactory,
    DataObjectHelper $dataObjectHelper,
    SprintResponseRepositoryInterface $sprintResponseRepository,
    VoucherDetailDataFactory $voucherDetailDataFactory,
    TimezoneInterface $timezone,
    UrlInterface $urlInterface,
    Payment $paymentHelper,
    LoggerInterface $logger,
    TransactionHelper $transactionHelper,
    Json $json
  ) {
    $this->transactionHelper = $transactionHelper;
    $this->json = $json;
    parent::__construct(
      $parentOrderDataFactory,
      $paymentInfoDataFactory,
      $dataObjectHelper,
      $sprintResponseRepository,
      $voucherDetailDataFactory,
      $timezone,
      $urlInterface,
      $paymentHelper,
      $logger
    );
  }

      /**
     * @param OrderPaymentInterface $payment
     * @param string|null $orderIncrementId
     * @return PaymentInfoDataInterface
     */
    public function paymentInfoProcess($payment, $orderIncrementId = null)
    {
        /** @var PaymentInfoDataInterface $paymentInfoData */
        $method = $payment->getMethod();
        if (Data::isMegaMethod($method)) {
          return $this->paymentInfoProcessMega($payment, $orderIncrementId);
        }
        $paymentInfoData = $this->paymentInfoDataFactory->create();
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
     * Payment info process mega
     * @param $payment
     * @param $orderIncrementId
     * @return $paymentInfodata
     */
    public function paymentInfoProcessMega($payment, $orderIncrementId = null)
    {
        $paymentInfoData = $this->paymentInfoDataFactory->create();
        $method = $payment->getMethod();
        $data[PaymentInfoDataInterface::METHOD] = $method;

        $paymentMethodSplit = explode("_", $method);
        $additionalDataArray = $payment->getAdditionalInformation();

        if ($orderIncrementId != null) {
            $data[PaymentInfoDataInterface::TRANSACTION_ID] = $payment->getLastTransId();
            if (is_array($paymentMethodSplit)) {
                $methodShort = $paymentMethodSplit[count($paymentMethodSplit) - 1];
                if ($methodShort == "va") {
                  $expireDate = '';
                  $virtualAccount = '';
                  try {
                    $paymentData = $this->transactionHelper->getLastOrderTransaction($payment->getParentId());
                    if ($mepayTxnData = $paymentData->getTransMepayTransaction()) {

                      $this->logger->critical($mepayTxnData);
                      $txnData = $this->json->unserialize($mepayTxnData);
                      if (isset($txnData['statusData']['vaNumber']) && $txnData['statusData']['vaNumber']) {
                        $virtualAccount = isset($txnData['statusData']['vaNumber'])? $txnData['statusData']['vaNumber'] : $virtualAccount;
                        $this->logger->critical($virtualAccount);
                        $txExpireDate = str_replace('T',' ', $txnData['statusData']['expireTime']);
                        $txExpireDate = substr($txExpireDate, 0, strpos($txExpireDate, "."));
                        $this->logger->critical($txExpireDate);
                        $expireDate = $this->timezone->date(date_create($txExpireDate))->format('M j, Y g:i:s A');
                        $this->logger->critical($expireDate);
                      }
                    }

                  } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                  }
                    $this->logger->critical($virtualAccount);
                    $data[PaymentInfoDataInterface::EXPIRE_DATE] = $expireDate;
                    $data[PaymentInfoDataInterface::VIRTUAL_ACCOUNT] = $virtualAccount;

                }

                if ($methodShort == "cc") {

                    $redirectUrl = (isset($additionalDataArray['urls']))? $additionalDataArray['urls']['checkout'] : '';
                    $data[PaymentInfoDataInterface::REDIRECT_URL] = $redirectUrl;

                }
            }
        }


        $this->dataObjectHelper->populateWithArray(
            $paymentInfoData,
            $data,
            PaymentInfoDataInterface::class
        );

        $paymentInfoData->setHowToPayObjects($this->getHowToPay($method));

        return $paymentInfoData;
    }

    /**
     * @param $method
     * @return mixed|string|null
     */
    private function getHowToPay($method)
    {
        if (!isset($this->howToPay[$method])) {
            try {
                $howToPay = (Data::isMegaMethod($method))? $this->paymentHelper->getBlockHowToPay('', true) : $this->paymentHelper->getBlockHowToPay($method, true);
            } catch (LocalizedException $e) {
                $this->logger->critical($e->getMessage());
                $howToPay = null;
            }
            $this->howToPay[$method] = $howToPay;
        }
        return $this->howToPay[$method];
    }
}