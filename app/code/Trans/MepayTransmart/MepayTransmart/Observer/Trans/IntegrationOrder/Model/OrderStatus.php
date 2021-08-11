<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Observer\Trans\IntegrationOrder\Model;

use Trans\Mepay\Api\Data\AuthCaptureInterface;
use Trans\Mepay\Helper\Gateway\Http\Client\ConnectAuthCapture;
use Trans\Mepay\Model\Config\Config;
use Magento\Framework\Event\ObserverInterface;
use Trans\Mepay\Model\Cron\Transaction\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Mepay\Helper\Data;

class OrderStatus implements ObserverInterface
{
  /**
   * @var ConnectAuthCapture
   */
  protected $clientHelper;

  /**
   * @var Config
   */
  protected $config;

  /**
   * @var \Magento\Framework\App\ResourceConnection
   */
  protected $resource;

  /**
   * @var \Trans\Mepay\Api\Data\AuthCaptureInterfaceFactory
   */
  protected $authCaptureFactory;

  /**
   * @var \Trans\Mepay\Api\AuthCaptureRepositoryInterface
   */
  protected $authCaptureRepository;

  /**
   * @var \Trans\Mepay\Model\Cron\Transaction\Status
   */
  protected $status;

  /**
   * @var \Trans\Mepay\Logger\LoggerWrite
   */
  protected $logger;

  /**
   * Constructor
   * @param ConnectAuthCapture $clientHelper
   * @param Config $config
   * @param \Magento\Framework\App\ResourceConnection $resource
   * @param \Trans\Mepay\Api\Data\AuthCaptureInterfaceFactory $authCaptureFactory
   * @param \Trans\Mepay\Api\AuthCaptureRepositoryInterface $authCaptureRepository
   * @param \Trans\Mepay\Logger\LoggerWrite $loggetWrite
   */
  public function __construct(
    ConnectAuthCapture $clientHelper,
    Config $config,
    Status $status,
    \Magento\Framework\App\ResourceConnection $resource,
    \Trans\Mepay\Api\Data\AuthCaptureInterfaceFactory $authCaptureFactory,
    \Trans\Mepay\Api\AuthCaptureRepositoryInterface $authCaptureRepository,
    \Trans\Mepay\Logger\Logger $logger
  ) {
    $this->authCaptureFactory = $authCaptureFactory;
    $this->authCaptureRepository = $authCaptureRepository;
    $this->status = $status;
    $this->resource = $resource;
    $this->clientHelper = $clientHelper;
    $this->config = $config;
    $this->logger = $logger;
  }

  /**
   * Execute
   * @param  \Magento\Framework\Event\Observer $observer
   * @return void
   */
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    if ((int) $this->config->getIsAuthCapture()) {
      $orderId = $observer->getData('order_id');
      $refNumber = $observer->getData('reference_number');
      $newAmount = $observer->getData('new_amount');
      $adjAmount = $observer->getData('adjustment_amount');
      $amount = $observer->getData('amount');

      $check = $this->checkOrderCapture($refNumber);
      
      if($check && $this->isPgCaptured($orderId, $observer) == false) {
        try {
          $this->logger->info('== {{Auth Capture Start}} ==');

          $this->logger->info('Data order_id = ' . $orderId);
          $this->logger->info('Data reference_number = ' . $refNumber);
          $this->logger->info('Data new_amount = ' . $newAmount);
          $this->logger->info('Data adjustment_amount = ' . $adjAmount);
          $this->logger->info('Data amount = ' . $amount);

          $this->clientHelper->setAmount($amount);
          $this->clientHelper->setReferenceNumber($refNumber);
          $this->clientHelper->setAdjustmentAmount($adjAmount);
          $this->clientHelper->setNewAmount($newAmount);
          $this->clientHelper->setTxnByOrderId($refNumber);
          $send = $this->clientHelper->send();

          $this->logger->info('$send ' . print_r($send, true));
          
          $this->saveCaptureTrack($send, $observer);
        } catch (\Exception $e) {
          $this->logger->info('== {{Auth Capture Error}} == ' . $e->getMessage());
        }
      } else {
        $this->logger->info('== {{Order with reference number ' . $refNumber . ' Captured already}} ==');
      }

      $this->logger->info('== {{Auth Capture End}} ==');
    }
  }

  /**
   * check order capture track
   *
   * @param string $refNumber
   * @return bool
   */
  protected function checkOrderCapture($refNumber)
  {
    try {
      $data = $this->authCaptureRepository->getByReferenceNumber($refNumber);
      if($data->getStatus()) {
          return false;
      }
    } catch (NoSuchEntityException $e) {
        return true;
    }
    
    return true;
  }

  /**
   * save capture result
   * 
   * @param array $apiResult
   * @param \Magento\Framework\Event\Observer $observer
   */
  protected function saveCaptureTrack($apiResults = [], \Magento\Framework\Event\Observer $observer)
  {
    $orderId = $observer->getData('order_id');
    $refNumber = $observer->getData('reference_number');
    $result = 1;
    if(is_array($apiResults)) {
      if(isset($apiResults[0])) {
          $apiResult = json_decode($apiResults[0], true);
          if(isset($apiResult['error'])) {
              $result = 0;
          }
      }
    }

    try {
      $data = $this->authCaptureRepository->getByReferenceNumber($refNumber);
    } catch (NoSuchEntityException $e) {
      $data = $this->authCaptureFactory->create();
    }

    $data->setReferenceNumber($refNumber);
    $data->setReferenceOrderId($orderId);
    $data->setStatus($result);
    $data->setPgResponse($apiResults[0]);

    $this->authCaptureRepository->save($data);
  }

  /**
   * Is payment has captured on pg
   * @param  int $orderId
   * @return boolean
   */
  public function isPgCaptured($orderId, $observer)
  {
    $orderCollection = Data::getClassInstance('\Magento\Sales\Model\ResourceModel\Order\Collection');
    $orderCollection->addFieldToFilter('reference_order_id', $orderId);
    $collection = $orderCollection->getFirstItem();
    try {
      if(empty($data = $this->status->checkOrderTransactionStatusById($collection->getId())) == false) {
        $this->saveCaptureTrack($data, $observer);
        foreach ($data as $key => $value) {
          $result = json_decode($value, true);
          if ($result[0]['status'] == 'captured')
            return true;
        }
      }
    } catch (\Exception $e) {
      //
    }
    return false;
  }
}