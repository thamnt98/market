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
namespace Trans\Mepay\Cron\Transaction;

use Trans\Mepay\Gateway\Http\GetTransactionStatus;
use Trans\Mepay\Gateway\Http\Client\Connect;
use Magento\Framework\Serialize\Serializer\Json;
use Trans\Mepay\Setup\Patch\Data\ConfigNamePullStatusTransaction as CronStatus;
use Trans\Mepay\Api\ConfigPullStatusRepositoryInterface as CronRepo;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Mepay\Helper\Response\Payment\Inquiry as InquiryResponse;
use Trans\Mepay\Helper\Response\Payment\Transaction as TransactionResponse;
use Trans\Mepay\Model\Payment\Status as StatusModel;

class Status 
{
  /**
   * @var GetTransactionStatus
   */
  protected $transfer;

  /**
   * @var Connect
   */
  protected $connector;

  /**
   * @var Json
   */
  protected $json;

  /**
   * @var ConfigPullStatusRepositoryInterface
   */
  protected $cronRepo;

  /**
   * @var TransactionHelper
   */
  protected $txnHelper;

  /**
   * @var TransactionHelper
   */
  protected $inquiryResponse;

  /**
   * @var TransactionResponse
   */
  protected $transactionResponse;

  /**
   * @var StatusModel
   */
  protected $statusModel;

  /**
   * Constructor
   * @param GetTransactionStatus $transfer
   * @param Connect              $connecto
   * @param Json                 $json
   * @param CronRepo             $cronRepo
   * @param TransactionHelper    $txnHelpe
   * @param InquiryResponse      $inquiryResponse
   * @param TransactionResponse  $transactionResponse
   * @param StatusModel          $statusModel
   */
  public function __construct(
    GetTransactionStatus $transfer,
    Connect $connector,
    Json $json,
    CronRepo $cronRepo,
    TransactionHelper $txnHelper,
    InquiryResponse $inquiryResponse,
    TransactionResponse $transactionResponse,
    StatusModel $statusModel
  ) {
    $this->transfer = $transfer;
    $this->connector = $connector;
    $this->json = $json;
    $this->cronRepo = $cronRepo;
    $this->txnHelper = $txnHelper;
    $this->inquiryResponse = $inquiryResponse;
    $this->transactionResponse = $transactionResponse;
    $this->statusModel = $statusModel;
  }

  /**
   * Execute
   * @return ConfigNamePullStatusTransactionInterface
   */
  public function execute()
  {
    $cron = $this->cronRepo->get(CronStatus::CONFIG_PULL_STATUS);
    $collection = $this->getCollectionData($cron);
    if ($collection->getSize()) {
      $coll = $collection->getFirstItem();
      if ($inquiry = $coll->getTransMepayInquiry()) {
        $inquiry = $this->json->unserialize($inquiry);
        $response = $this->sendData($inquiry);
        $this->update($inquiry, $response);
        return $this->cronRepo->save($cron->setConfigOffset($offset + 1));
      }
    }
     return $this->cronRepo->save($cron->setConfigOffset(0));
  }

  /**
   * Update
   * @param  array $inquiry 
   * @param  array $transaction
   * @return void
   */
  public function update($inquiry, $transaction)
  {
    $inquiry = $this->inquiryResponse->convertToObject($inquiry);
    $transaction = $this->transactionResponse->convertToObject($transaction);
    $this->statusModel->update($inquiry, $transaction);
  }

  /**
   * Get collection
   * @param  $cron 
   * @return Collection
   */
  public function getCollectionData($cron)
  {
    $collection = $this->txnHelper->getAuthorizedTxn();
    $collection->getSelect()->limit((int)$cron->getConfigLimit(), (int)$cron->getConfigOffset());
    return $collection;
  }

  /**
   * Send data
   * @param  array $inquiry
   * @return array
   */
  public function sendData($inquiry)
  {
     if (isset($inquiry['id']) && $inquiry['id']) {
       $inquiryId = $inquiry['id'];
       $transfer = $this->transfer->create($inquiryId);
       return $this->getResult($this->connector->placeRequest($transfer));
      }
      throw new \Exception(__('Inqury data doesn\'t found'));
  }

  public function getResult($response)
  {
    foreach ($response as $key => $value) {
      return $value;
    }
  }
}