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
namespace Trans\Mepay\Helper\Response\Payment;

use Trans\Mepay\Api\Data\TransactionInterface;
use Trans\Mepay\Api\Data\TransactionInterfaceFactory;
use Trans\Mepay\Api\Data\TransactionStatusDataInterface;
use Trans\Mepay\Api\Data\TransactionStatusDataInterfaceFactory;

class Transaction 
{
  /**
   * @var TransactionInterfaceFactory
   */
  protected $transactionFactory;

  /**
   * @var TransactionStatusDataInterfaceFactory
   */
  protected $statusDataFactory;

  /**
   * Constructor
   * @param TransactionInterfaceFactory           $transactionFactory
   * @param TransactionStatusDataInterfaceFactory $statusDataFactory
   */
  public function __construct(
    TransactionInterfaceFactory $transactionFactory,
    TransactionStatusDataInterfaceFactory $statusDataFactory
  ) {
    $this->transactionFactory = $transactionFactory;
    $this->statusDataFactory = $statusDataFactory;
  }

  /**
   * Convert to object
   * @param   $transaction
   * @return  TransactionInterface
   */
  public function convertToObject($transaction)
  {
    $transaction = $this->transactionFactory->create();
    foreach ($transaction as $key => $value) {
      if ($key == TransactionInterface::STATUS_DATA) {
        $value = $this->convertToObjectStatusData($value);
      }
      $transaction->setData($key, $value);
    }
    return $transaction;
  }

  /**
   * Convert status data
   * @param  array $statusData
   * @return TransactionStatusDataInterface
   */
  public function convertToObjectStatusData($statusData)
  {
    $status = $this->statusDataFactory->create();
    foreach ($statusData as $key => $value) {
      $status->setData($key, $value);
    }
    return $status;
  }

  public function convertToArray($transaction) {
    $arrayInquiry = $this->convertToArrayTransaction($transaction);
    return $arrayInquiry;
  }

  public function convertToArrayTransaction($transaction)
  {
    $result = [];
    foreach ($transaction->getData() as $key => $value) {
      if ($key == TransactionInterface::STATUS_DATA) {
        $value = $this->convertToArrayStatusData($value);
      }
      $result[$key] = $value;
    }
    return $result;
  }

  public function convertToArrayStatusData($statusData)
  {
    $result = [];
    foreach ($statusData as $key => $value) {
      $result[$key] = $value;
    }
    return $result;
  }
}