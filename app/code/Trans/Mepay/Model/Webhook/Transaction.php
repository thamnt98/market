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
namespace Trans\Mepay\Model\Webhook;

use Magento\Framework\DataObject;
use Trans\Mepay\Api\Data\TransactionInterface;
use Trans\Mepay\Api\Data\TransactionStatusDataInterface;
use Trans\Mepay\Api\Data\TransactionStatusDataInterfaceFactory;

class Transaction extends DataObject implements TransactionInterface
{
  /**
   * @var \TransactionStatusDataInterface
   */
  protected $statusData;

  /**
   * Constructor
   * @param TransactionStatusDataInterfaceFactory $statusDataFactory
   */
  public function __construct(TransactionStatusDataInterfaceFactory $statusDataFactory)
  {
    $this->statusData = $statusDataFactory->create();
  }
  /**
   * @inheritdoc
   */
  public function getId()
  {
    return $this->_getData(TransactionInterface::ID);
  }

  /**
   * @inheritdoc
   */
  public function setId($data)
  {
    $this->setData(TransactionInterface::ID, $data);
  }

  /**
   * @inheritdoc
   */
  public function getCreatedTime()
  {
    return $this->_getData(TransactionInterface::CREATED_TIME);
  }

  /**
   * @inheritdoc
   */
  public function setCreatedTime($data)
  {
    $this->setData(TransactionInterface::CREATED_TIME, $data);
  }

  /**
   * @inheritdoc
   */
  public function getUpdatedTime()
  {
    return $this->_getData(TransactionInterface::UPDATED_TIME);
  }

  /**
   * @inheritdoc
   */
  public function setUpdatedTime($data)
  {
    $this->setData(TransactionInterface::UPDATED_TIME, $data);
  }

  /**
   * @inheritdoc
   */
  public function getCurrency()
  {
    return $this->_getData(TransactionInterface::CURRENCY);
  }

  /**
   * @inheritdoc
   */
  public function setCurrency($data)
  {
    $this->setData(TransactionInterface::CURRENCY, $data);
  }

  /**
   * @inheritdoc
   */
  public function getAmount()
  {
    return $this->_getData(TransactionInterface::AMOUNT);
  }

  /**
   * @inheritdoc
   */
  public function setAmount($data)
  {
    $this->setData(TransactionInterface::AMOUNT, $data);
  }

  /**
   * @inheritdoc
   */
  public function getInquiryId()
  {
    return $this->_getData(TransactionInterface::INQUIRY_ID);
  }

  /**
   * @inheritdoc
   */
  public function setInquiryId($data)
  {
    $this->setData(TransactionInterface::INQUIRY_ID, $data);
  }

  /**
   * @inheritdoc
   */
  public function getMerchantId()
  {
    return $this->_getData(TransactionInterface::MERCHANT_ID);
  }

  /**
   * @inheritdoc
   */
  public function setMerchantId($data)
  {
    $this->setData(TransactionInterface::MERCHANT_ID, $data);
  }

  /**
   * @inheritdoc
   */
  public function getType()
  {
    return $this->_getData(TransactionInterface::TYPE);
  }

  /**
   * @inheritdoc
   */
  public function setType($data)
  {
    $this->setData(TransactionInterface::TYPE, $data);
  }

  /**
   * Get status
   * @return string
   */
  public function getStatus()
  {
    return $this->_getData(TransactionInterface::STATUS);
  }

  /**
   * @inheritdoc
   */
  public function setStatus($data)
  {
    $this->setData(TransactionInterface::STATUS, $data);
  }

  /**
   * @inheritdoc
   */
  public function getStatusCode()
  {
    return $this->_getData(TransactionInterface::STATUS_CODE);
  }

  /**
   * @inheritdoc
   */
  public function setStatusCode($data)
  {
    $this->setData(TransactionInterface::STATUS_CODE, $data);
  }

  /**
   * @inheritdoc
   */
  public function getStatusData()
  {
    return $this->_getData(TransactionInterface::STATUS_DATA);
  }

  /**
   * @inheritdoc
   */
  public function setStatusData($data)
  {
    $this->setData(TransactionInterface::STATUS_DATA, $data);
  }

  /**
   * @inheritdoc
   */
  public function getNetworkReferenceId()
  {
    return $this->_getData(TransactionInterface::NETWORK_REFERENCE_ID);
  }

  /**
   * @inheritdoc
   */
  public function setNetworkReferenceId($data)
  {
    $this->setData(TransactionInterface::NETWORK_REFERENCE_ID, $data);
  }

  /**
   * @inheritdoc
   */
  public function getAuthorizationCode()
  {
    return $this->_getData(TransactionInterface::AUTHORIZATION_CODE);
  }

  /**
   * @inheritdoc
   */
  public function setAuthorizationCode($data)
  {
    $this->setData(TransactionInterface::AUTHORIZATION_CODE, $data);
  }

  /**
   * Extract status data
   * @param  array $input
   * @return TransactionStatusDataInterface
   */
  public function extractStatusData($input)
  {
    foreach ($input as $key => $value) {
      foreach ($value as $index => $data) {
        $this->statusData->setdata($index, $data);
      }
    }
    return $this->statusData;
  }

}