<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface as StatusResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterfaceFactory as StatusResponseInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductStatusInterface;
use Trans\DigitalProduct\Api\DigitalProductStatusResponseRepositoryInterface as StatusResponseRepository;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface as TransactionResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterfaceFactory as TransactionResponseInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductTransactionInterface;
use Trans\DigitalProduct\Api\DigitalProductTransactionResponseRepositoryInterface as TransactionResponseRepository;
use Trans\DigitalProduct\Helper\Config;

/**
 * DigitalProductStatus
 */
class DigitalProductStatus implements DigitalProductStatusInterface
{

    /**
     * @var \Trans\DigitalProduct\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var statusResponseRepository
     */
    protected $statusResponseRepository;

    /**
     * @var statusResponseInterfaceFactory
     */
    protected $statusResponseInterfaceFactory;

    /**
     * @var TransactionResponseRepository
     */
    protected $transactionResponseRepository;

    /**
     * @var [type]
     */
    protected $transactionResponseInterfaceFactory;

    /**
     * @param \Trans\DigitalProduct\Helper\Data $dataHelper
     * @param statusResponseRepository         $statusResponseRepository
     * @param statusResponseInterfaceFactory   $statusResponseInterfaceFactory
     * @param TransactionResponseRepository       $transactionResponseRepository
     * @param TransactionResponseInterfaceFactory $transactionResponseInterfaceFactory
     */
    public function __construct(
        \Trans\DigitalProduct\Helper\Data $dataHelper,
        StatusResponseRepository $statusResponseRepository,
        StatusResponseInterfaceFactory $statusResponseInterfaceFactory,
        TransactionResponseRepository $transactionResponseRepository,
        TransactionResponseInterfaceFactory $transactionResponseInterfaceFactory
    ) {
        $this->dataHelper                      = $dataHelper;
        $this->statusResponseRepository       = $statusResponseRepository;
        $this->statusResponseInterfaceFactory = $statusResponseInterfaceFactory;
        $this->transactionResponseRepository       = $transactionResponseRepository;
        $this->transactionResponseInterfaceFactory = $transactionResponseInterfaceFactory;

        $this->config = $this->dataHelper->getConfigHelper();
        $this->logger = $this->dataHelper->getLogger();
    }

    /**
     * Status or callback from altera
     *
     * @param  string $dataReq
     * @return mixed
     */
    public function getCallbackAltera($dataReq)
    {
        $response = $dataReq;

        // get order id from response altera
        $jsonResponse = json_decode($response, true);
        $orderId = $jsonResponse['order_id'];
        // get status from response altera
        $status = $jsonResponse['status'];

        $data = $this->saveData($orderId, $dataReq, $status);

        if ($data) {
            $dataStatus = $this->saveDataStatus($orderId, $dataReq, $status);
        }

        return $response;
    }

    /**
     * Save Data to Table transaction status
     *
     * @param  int $customerId
     * @param  string $dataReq
     * @return mixed
     */
    public function saveData($orderId, $dataReq, $status)
    {
        try {
            $data = [
                StatusResponseInterface::ORDER_ID    => $orderId,
                StatusResponseInterface::RESPONSE    => $dataReq,
                StatusResponseInterface::STATUS      => $status,
            ];

            $statusResponse = $this->statusResponseInterfaceFactory->create();
            $statusResponse->addData($data);

            $response = $this->statusResponseRepository->save($statusResponse);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return $response;
    }

    /**
     * Save Data to Table transaction response for add status response
     *
     * @param  int $customerId
     * @param  string $productData
     * @param  string $response
     * @param  string $orderId
     * @return mixed
     */
    public function saveDataStatus($orderId, $dataReq, $status)
    {
        try {
            $data = [
                TransactionResponseInterface::ORDER_ID    => $orderId,
                TransactionResponseInterface::RESPONSE    => $dataReq,
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
