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

use Magento\Framework\Exception\CouldNotSaveException;
use Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface as InquireResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterfaceFactory as InquireResponseInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductInquireInterface;
use Trans\DigitalProduct\Api\DigitalProductInquireResponseRepositoryInterface as InquireResponseRepository;
use Trans\DigitalProduct\Helper\Config;

/**
 * DigitalProductInquire
 */
class DigitalProductInquire implements DigitalProductInquireInterface
{

    /**
     * @var \Trans\DigitalProduct\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var InquireResponseRepository
     */
    protected $inquireResponseRepository;

    /**
     * @var InquireResponseInterfaceFactory
     */
    protected $inquireResponseInterfaceFactory;

    /**
     * @param \Trans\DigitalProduct\Helper\Data $dataHelper
     * @param InquireResponseRepository         $inquireResponseRepository
     * @param InquireResponseInterfaceFactory   $inquireResponseInterfaceFactory
     */
    public function __construct(
        \Trans\DigitalProduct\Helper\Data $dataHelper,
        InquireResponseRepository $inquireResponseRepository,
        InquireResponseInterfaceFactory $inquireResponseInterfaceFactory
    ) {
        $this->dataHelper                      = $dataHelper;
        $this->inquireResponseRepository       = $inquireResponseRepository;
        $this->inquireResponseInterfaceFactory = $inquireResponseInterfaceFactory;

        $this->config = $this->dataHelper->getConfigHelper();
        $this->logger = $this->dataHelper->getLogger();
    }

    /**
     * Inquire BPJS Kesehatan
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  string $paymentPeriod
     * @param  int $productId
     * @return mixed
     */
    public function bpjsKesehatan($customerId, $customerNumber, $paymentPeriod, $productId)
    {
        $action      = Config::ACTION_INQUIRE;
        $path        = Config::URL_PATH_BPJS_KESEHATAN;
        $productData = [
            "customer_number" => $customerNumber,
            "payment_period"  => $paymentPeriod,
            "product_id"      => $productId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $data = $this->saveData($customerId, $productData, $response);

        return $response;
    }

    /**
     * Inquire Electricity ( PLN Reguler )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function electricity($customerId, $customerNumber, $productId)
    {
        $action      = Config::ACTION_INQUIRE;
        $path        = Config::URL_PATH_ELECTRICITY;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $jsonResponse = json_decode($response, true);
        $status = $jsonResponse['status'];

        $data = $this->saveData($customerId, $productData, $response);

        return $response;
    }

    /**
     * Inquire Electricity Postpaid ( PLN Token )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function electricityPostpaid($customerId, $customerNumber, $productId)
    {
        $action      = Config::ACTION_INQUIRE;
        $path        = Config::URL_PATH_ELECTRICITY_POSTPAID;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $data = $this->saveData($customerId, $productData, $response);

        return $response;
    }

    /**
     * Inquire Telkom Postpaid
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function telkomPostpaid($customerId, $customerNumber, $productId)
    {
        $action      = Config::ACTION_INQUIRE;
        $path        = Config::URL_PATH_TELKOM_POSTPAID;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $data = $this->saveData($customerId, $productData, $response);

        return $response;
    }

    /**
     * Inquire PDAM
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @param  string $operatorCode
     * @return mixed
     */
    public function pdam($customerId, $customerNumber, $productId, $operatorCode)
    {
        $action      = Config::ACTION_INQUIRE;
        $path        = Config::URL_PATH_PDAM;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
            "operator_code"   => $operatorCode,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $data = $this->saveData($customerId, $productData, $response);

        return $response;
    }

    /**
     * Inquire Mobile Postpaid ( Pascabayar )
     *
     * @param  int $customerId
     * @param  string $customerNumber
     * @param  int $productId
     * @return mixed
     */
    public function mobilePostpaid($customerId, $customerNumber, $productId)
    {
        $action      = Config::ACTION_INQUIRE;
        $path        = Config::URL_PATH_MOBILE_POSTPAID;
        $productData = [
            "customer_number" => $customerNumber,
            "product_id"      => $productId,
        ];

        $response = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

        $data = $this->saveData($customerId, $productData, $response);

        return $response;
    }

    /**
     * Save Data to Table
     *
     * @param  int $customerId
     * @param  string $productData
     * @param  string $response
     * @return mixed
     */
    public function saveData($customerId, $productData, $response)
    {
        try {
            $data = [
                InquireResponseInterface::CUSTOMER_ID => $customerId,
                InquireResponseInterface::REQUEST     => $this->dataHelper->serializeJson($productData),
                InquireResponseInterface::RESPONSE    => $response,
            ];

            $inquireResponse = $this->inquireResponseInterfaceFactory->create();
            $inquireResponse->addData($data);

            $response = $this->inquireResponseRepository->save($inquireResponse);
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
