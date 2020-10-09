<?php

namespace SM\DigitalProduct\Model;

use Magento\Framework\Api\DataObjectHelper;
use SM\DigitalProduct\Api\Data\Transaction\MobileProductIdDataInterface;
use SM\DigitalProduct\Api\Data\Transaction\MobileProductIdDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Transaction\ProductIdDataInterface;
use SM\DigitalProduct\Api\Data\Transaction\ProductIdDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Transaction\TransactionBpjsDataInterface;
use SM\DigitalProduct\Api\Data\Transaction\TransactionBpjsDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Transaction\TransactionDataInterface;
use SM\DigitalProduct\Api\Data\Transaction\TransactionDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Transaction\TransactionElectricityPrePaidDataInterface;
use SM\DigitalProduct\Api\Data\Transaction\TransactionElectricityPrePaidDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Transaction\TransactionMobileDataInterface;
use SM\DigitalProduct\Api\Data\Transaction\TransactionMobileDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Transaction\TransactionPdamDataInterface;
use SM\DigitalProduct\Api\Data\Transaction\TransactionPdamDataInterfaceFactory;
use SM\DigitalProduct\Api\ReorderRepositoryInterface;
use SM\DigitalProduct\Api\TransactionRepositoryInterface;
use Trans\DigitalProduct\Model\DigitalProductTransaction;

/**
 * Class TransactionRepository
 * @package SM\DigitalProduct\Model
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var DigitalProductTransaction
     */
    protected $digitalProductTransaction;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var TransactionMobileDataInterfaceFactory
     */
    protected $transactionMobileDataFactory;

    /**
     * @var TransactionElectricityPrePaidDataInterfaceFactory
     */
    protected $transactionElectricityPrepaidDataFactory;

    /**
     * @var ProductIdDataInterfaceFactory
     */
    protected $productIdDataFactory;

    /**
     * @var MobileProductIdDataInterfaceFactory
     */
    protected $mobileProductIdDataFactory;

    /**
     * @var TransactionPdamDataInterfaceFactory
     */
    protected $transactionPdamDataFactory;

    /**
     * @var TransactionBpjsDataInterfaceFactory
     */
    protected $transactionBpjsDataFactory;

    /**
     * @var TransactionDataInterfaceFactory
     */
    protected $transactionDataFactory;

    /**
     * TransactionRepository constructor.
     * @param DigitalProductTransaction $digitalProductTransaction
     * @param DataObjectHelper $dataObjectHelper
     * @param TransactionMobileDataInterfaceFactory $transactionMobileDataFactory
     * @param TransactionElectricityPrePaidDataInterfaceFactory $transactionElectricityPrepaidDataFactory
     * @param MobileProductIdDataInterfaceFactory $mobileProductIdDataFactory
     * @param ProductIdDataInterfaceFactory $productIdDataFactory
     * @param TransactionPdamDataInterfaceFactory $transactionPdamDataFactory
     * @param TransactionBpjsDataInterfaceFactory $transactionBpjsDataFactory
     * @param TransactionDataInterfaceFactory $transactionDataFactory
     */
    public function __construct(
        DigitalProductTransaction $digitalProductTransaction,
        DataObjectHelper $dataObjectHelper,
        TransactionMobileDataInterfaceFactory $transactionMobileDataFactory,
        TransactionElectricityPrePaidDataInterfaceFactory $transactionElectricityPrepaidDataFactory,
        MobileProductIdDataInterfaceFactory $mobileProductIdDataFactory,
        ProductIdDataInterfaceFactory $productIdDataFactory,
        TransactionPdamDataInterfaceFactory $transactionPdamDataFactory,
        TransactionBpjsDataInterfaceFactory $transactionBpjsDataFactory,
        TransactionDataInterfaceFactory $transactionDataFactory
    ) {
        $this->transactionDataFactory = $transactionDataFactory;
        $this->transactionPdamDataFactory = $transactionPdamDataFactory;
        $this->transactionBpjsDataFactory = $transactionBpjsDataFactory;
        $this->mobileProductIdDataFactory = $mobileProductIdDataFactory;
        $this->productIdDataFactory = $productIdDataFactory;
        $this->digitalProductTransaction = $digitalProductTransaction;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->transactionMobileDataFactory = $transactionMobileDataFactory;
        $this->transactionElectricityPrepaidDataFactory = $transactionElectricityPrepaidDataFactory;
    }

    /**
     * @param $response
     * @param $transactionData
     * @param $interface
     * @param $productIdInterface
     * @return mixed
     */
    private function responseProcess($response, $transactionData, $interface, $productIdInterface)
    {
        if ($response != false) {
            $this->dataObjectHelper->populateWithArray(
                $transactionData,
                $response,
                $interface
            );
            if (isset($response["product_id"])) {
                if ($productIdInterface == MobileProductIdDataInterface::class) {
                    $productIdData = $this->mobileProductIdDataFactory->create();
                } else {
                    $productIdData = $this->productIdDataFactory->create();
                }
                $this->dataObjectHelper->populateWithArray(
                    $productIdData,
                    $response["product_id"],
                    $productIdInterface
                );
                $transactionData->setProductId($productIdData);
            }

            if (isset($responseArray["data"])) {
                $transactionData->setTransactionData($responseArray["data"]);
            }
        } else {
            $transactionData
                ->setResponseCode(ReorderRepositoryInterface::TIMEOUT_RESPONSE_CODE);
        }
        return $transactionData;
    }

    /**
     * @inheritDoc
     */
    public function transactionMobile($productId, $customerId, $customerNumber, $orderId)
    {
        $response = $this->digitalProductTransaction->mobile(
            $customerId,
            $customerNumber,
            $productId,
            $orderId
        );

        return $this->responseProcess(
            ($response == false) ? $response : json_decode($response, true),
            $this->transactionMobileDataFactory->create(),
            TransactionMobileDataInterface::class,
            MobileProductIdDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function transactionElectricityPrePaid($productId, $meterNumber, $customerId, $customerNumber, $orderId)
    {
        $response = $this->digitalProductTransaction->electricity(
            $customerId,
            $customerNumber,
            $meterNumber,
            $productId,
            $orderId
        );

        return $this->responseProcess(
            ($response == false) ? $response : json_decode($response, true),
            $this->transactionElectricityPrepaidDataFactory->create(),
            TransactionElectricityPrePaidDataInterface::class,
            ProductIdDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function transactionElectricityPostPaid($productId, $customerId, $customerNumber, $orderId)
    {
        $response = $this->digitalProductTransaction->electricityPostpaid(
            $customerId,
            $customerNumber,
            $productId,
            $orderId
        );

        return $this->responseProcess(
            ($response == false) ? $response : json_decode($response, true),
            $this->transactionDataFactory->create(),
            TransactionDataInterface::class,
            ProductIdDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function transactionPdam($productId, $customerId, $customerNumber, $orderId, $operatorCode)
    {
        $response = $this->digitalProductTransaction->pdam(
            $customerId,
            $customerNumber,
            $productId,
            $operatorCode,
            $orderId
        );

        return $this->responseProcess(
            ($response == false) ? $response : json_decode($response, true),
            $this->transactionPdamDataFactory->create(),
            TransactionPdamDataInterface::class,
            ProductIdDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function transactionBpjs($productId, $customerId, $customerNumber, $orderId, $paymentPeriod)
    {
        $response = $this->digitalProductTransaction->bpjsKesehatan(
            $customerId,
            $customerNumber,
            $paymentPeriod,
            $productId,
            $orderId
        );

        return $this->responseProcess(
            ($response == false) ? $response : json_decode($response, true),
            $this->transactionBpjsDataFactory->create(),
            TransactionBpjsDataInterface::class,
            ProductIdDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function transactionMobilePostpaid($productId, $customerId, $customerNumber, $orderId)
    {
        $response = $this->digitalProductTransaction->mobilePostpaid(
            $customerId,
            $customerNumber,
            $productId,
            $orderId
        );

        return $this->responseProcess(
            ($response == false) ? $response : json_decode($response, true),
            $this->transactionDataFactory->create(),
            TransactionDataInterface::class,
            ProductIdDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function transactionTelkom($productId, $customerId, $customerNumber, $orderId)
    {
        $response = $this->digitalProductTransaction->telkomPostpaid(
            $customerId,
            $customerNumber,
            $productId,
            $orderId
        );

        return $this->responseProcess(
            ($response == false) ? $response : json_decode($response, true),
            $this->transactionDataFactory->create(),
            TransactionDataInterface::class,
            ProductIdDataInterface::class
        );
    }
}
