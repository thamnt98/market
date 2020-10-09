<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model;

use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface as TransactionResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterfaceFactory as TransactionResponseInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductTransactionInterface;
use Trans\DigitalProduct\Api\DigitalProductTransactionResponseRepositoryInterface as TransactionResponseRepository;
use Trans\DigitalProduct\Helper\Config;

/**
 * DigitalProductTransaction
 */
class DigitalProductTransaction implements DigitalProductTransactionInterface
{

    /**
     * @var \Trans\DigitalProduct\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var TransactionResponseRepository
     */
    protected $transactionResponseRepository;

    /**
     * @var [type]
     */
    protected $transactionResponseInterfaceFactory;

    /**
     * @param \Trans\DigitalProduct\Helper\Data   $dataHelper
     * @param TransactionResponseRepository       $transactionResponseRepository
     * @param TransactionResponseInterfaceFactory $transactionResponseInterfaceFactory
     */
    public function __construct(
        \Trans\DigitalProduct\Helper\Data $dataHelper,
        TransactionResponseRepository $transactionResponseRepository,
        TransactionResponseInterfaceFactory $transactionResponseInterfaceFactory
    ) {
        $this->dataHelper                          = $dataHelper;
        $this->transactionResponseRepository       = $transactionResponseRepository;
        $this->transactionResponseInterfaceFactory = $transactionResponseInterfaceFactory;

        $this->config = $this->dataHelper->getConfigHelper();
        $this->logger = $this->dataHelper->getLogger();
    }

    /**
     * Transaction Mobile ( Pulsa & Data )
     *
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function mobile($customerId, $customerNumber, $productId, $orderId)
    {
        $action      = Config::ACTION_TRANSACTION;
        $path        = Config::URL_PATH_MOBILE;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
            "order_id"        => $orderId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['status'])) {
            return;
        }
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response, $orderId, $status);

        return $response;
    }

    /**
     * Transaction BPJS Kesehatan
     *
     * @param  string $customerNumber
     * @param string $paymentPeriod
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function bpjsKesehatan($customerId, $customerNumber, $paymentPeriod, $productId, $orderId)
    {
        $action      = Config::ACTION_TRANSACTION;
        $path        = Config::URL_PATH_BPJS_KESEHATAN;
        $productData = [
            "customer_number" => $customerNumber,
            "payment_period"  => $paymentPeriod,
            "product_id"      => $productId,
            "order_id"        => $orderId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['status'])) {
            return;
        }
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response, $orderId, $status);

        return $response;
    }

    /**
     * Transaction Fpdamity ( PLN Reguler )
     *
     * @param  string $customerNumber
     * @param string $meterNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function electricity($customerId, $customerNumber, $meterNumber, $productId, $orderId)
    {
        $action      = Config::ACTION_TRANSACTION;
        $path        = Config::URL_PATH_ELECTRICITY;
        $productData = [
            "customer_number" => $customerNumber,
            "meter_number"    => $meterNumber,
            "product_id"      => $productId,
            "order_id"        => $orderId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['status'])) {
            return;
        }
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response, $orderId, $status);

        return $response;
    }

    /**
     * Transaction Electricity Postpaid ( PLN Reguler )
     *
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function electricityPostpaid($customerId, $customerNumber, $productId, $orderId)
    {
        $action      = Config::ACTION_TRANSACTION;
        $path        = Config::URL_PATH_ELECTRICITY_POSTPAID;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
            "order_id"        => $orderId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['status'])) {
            return;
        }
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response, $orderId, $status);

        return $response;
    }

    /**
     * Inquire Telkom Postpaid
     *
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function telkomPostpaid($customerId, $customerNumber, $productId, $orderId)
    {
        $action      = Config::ACTION_TRANSACTION;
        $path        = Config::URL_PATH_TELKOM_POSTPAID;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
            "order_id"        => $orderId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['status'])) {
            return;
        }
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response, $orderId, $status);

        return $response;
    }

    /**
     * Inquire PDAM
     *
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $operatorCode
     * @param  string $orderId
     * @return mixed
     */
    public function pdam($customerId, $customerNumber, $productId, $operatorCode, $orderId)
    {
        $action      = Config::ACTION_TRANSACTION;
        $path        = Config::URL_PATH_PDAM;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
            "operator_code"   => $operatorCode,
            "order_id"        => $orderId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['status'])) {
            return;
        }
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response, $orderId, $status);

        return $response;
    }

    /**
     * Inquire Mobile Postpaid ( Pascabayar )
     *
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $orderId
     * @return mixed
     */
    public function mobilePostpaid($customerId, $customerNumber, $productId, $orderId)
    {
        $action      = Config::ACTION_TRANSACTION;
        $path        = Config::URL_PATH_MOBILE_POSTPAID;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
            "order_id"        => $orderId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['status'])) {
            return;
        }
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response, $orderId, $status);

        return $response;
    }

    /**
     * Save Data to Table
     *
     * @param  int $customerId
     * @param  string $productData
     * @param  string $response
     * @param  string $orderId
     * @return mixed
     */
    public function saveData($customerId, $productData, $response, $orderId, $status)
    {
        try {
            $data = [
                TransactionResponseInterface::CUSTOMER_ID => $customerId,
                TransactionResponseInterface::ORDER_ID    => $orderId,
                TransactionResponseInterface::REQUEST     => $this->dataHelper->serializeJson($productData),
                TransactionResponseInterface::RESPONSE    => $response,
                TransactionResponseInterface::STATUS      => $status,
            ];

            $transactionResponse = $this->transactionResponseInterfaceFactory->create();
            $transactionResponse->addData($data);

            $response = $this->transactionResponseRepository->save($transactionResponse);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return $response;
    }
}
