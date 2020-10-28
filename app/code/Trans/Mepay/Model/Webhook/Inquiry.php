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
use Trans\Mepay\Api\Data\InquiryInterface;
use Trans\Mepay\Api\Data\InquiryCustomerInterface;
use Trans\Mepay\Api\Data\InquiryCustomerInterfaceFactory;
use Trans\Mepay\Api\Data\InquiryOrderInterface;
use Trans\Mepay\Api\Data\InquiryOrderInterfaceFactory;
use Trans\Mepay\Api\Data\InquiryMerchantInterface;
use Trans\Mepay\Api\Data\InquiryMerchantInterfaceFactory;

class Inquiry extends DataObject implements InquiryInterface
{
  /**
   * @var \InquiryCustomerInterface
   */
  protected $inquiryCustomer;

  /**
   * @var \InquiryOrderInterface
   */
  protected $inquiryOrder;

  /**
   * @var \InquiryMerchantInterface
   */
  protected $inquiryMerchant;

  public function __construct(
    InquiryCustomerInterfaceFactory $inquiryCustomerFactory,
    InquiryOrderInterfaceFactory $inquiryOrderFactory,
    InquiryMerchantInterfaceFactory $inquiryMerchantFactory
  ) {
    $this->inquiryCustomer = $inquiryCustomerFactory->create();
    $this->inquiryOrder = $inquiryOrderFactory->create();
    $this->inquiryMerchant = $inquiryMerchantFactory->create();
  }

  /**
   * @inheritdoc
   */
  public function getId()
  {
    return $this->_getData(InquiryInterface::ID);
  }

  /**
   * @inheritdoc
   */
  public function setId($data)
  {
    $this->setData(InquiryInterface::ID, $data);
  }

  /**
   * @inheritdoc
   */
  public function getCreatedTime()
  {
    return $this->_getData(InquiryInterface::CREATED_TIME);
  }

  /**
   * @inheritdoc
   */
  public function setCreatedTime($data)
  {
    $this->setData(InquiryInterface::CREATED_TIME, $data);
  }

  /**
   * @inheritdoc
   */
  public function getUpdatedTime()
  {
    return $this->_getData(InquiryInterface::UPDATED_TIME);
  }

  /**
   * @inheritdoc
   */
  public function setUpdatedTime($data)
  {
    $this->setData(InquiryInterface::UPDATED_TIME, $data);
  }

  /**
   * @inheritdoc
   */
  public function setCurrency($data)
  {
    $this->setData(InquiryInterface::CURRENCY, $data);
  }

  /**
   * @inheritdoc
   */
  public function getCurrency()
  {
    return $this->_getData(InquiryInterface::CURRENCY);
  }

  /**
   * @inheritdoc
   */
  public function getAmount()
  {
    return $this->_getData(InquiryInterface::AMOUNT);
  }

  /**
   * @inheritdoc
   */
  public function setAmount($data)
  {
    $this->setData(InquiryInterface::AMOUNT, $data);
  }

  /**
   * @inheritdoc
   */
  public function getLockedAmount()
  {
    return $this->_getData(InquiryInterface::LOCKED_AMOUNT);
  }

  /**
   * @inheritdoc
   */
  public function setLockedAmount($data)
  {
    $this->setData(InquiryInterface::LOCKED_AMOUNT, $data);
  }

  /**
   * @inheritdoc
   */
  public function getStatus()
  {
    return $this->_getData(InquiryInterface::STATUS);
  }

  /**
   * @inheritdoc
   */
  public function setStatus($data)
  {
    $this->setData(InquiryInterface::STATUS, $data);
  }

  /**
   * @inheritdoc
   */
  public function getMerchantId()
  {
    return $this->_getData(InquiryInterface::MERCHANT_ID);
  }

  /**
   * @inheritdoc
   */
  public function setMerchantId($data)
  {
    $this->setData(InquiryInterface::MERCHANT_ID, $data);
  }

  /**
   * @inheritdoc
   */
  public function getOrder()
  {
    return $this->_getData(InquiryInterface::ORDER);
  }

  /**
   * @inheritdoc
   */
  public function setOrder($data)
  {
    $this->setData(InquiryInterface::ORDER, $data);
  }

  /**
   * @inheritdoc
   */
  public function getCustomer()
  {
    return $this->_getData(InquiryInterface::CUSTOMER);
  }

  /**
   * @inheritdoc
   */
  public function setCustomer($data)
  {
    $this->setData(InquiryInterface::CUSTOMER, $data);
  }

  /**
   * @inheritdoc
   */
  public function getMerchant()
  {
    return $this->_getData(InquiryInterface::MERCHANT);
  }

  /**
   * @inheritdoc
   */
  public function setMerchant($data)
  {
    $this->setData(InquiryInterface::MERCHANT, $data);
  }

  /**
   * Validate
   * @return boolean
   */
  public function validate()
  {
    if ($this->inquiryCustomer->validate() && $this->inquiryOrder->validate() && $this->inquiryMerchant->validate())
      return true;
    return false;
  }

  /**
   * Extract customer
   * @param  array $input
   * @return InquiryCustomerInterface
   */
  public function extractCustomer($input)
  {
    foreach ($input as $key => $value) {
      foreach ($value as $index => $data) {
        $this->inquiryCustomer->setData($index, $data);
      }
    }
    return $this->inquiryCustomer;
  }

  /**
   * Extract merchant
   * @param  array $input
   * @return InquiryMerchantInterface
   */
  public function extractMerchant($input)
  {
    foreach ($input as $key => $value) {
      foreach ($value as $index => $data) {
        $this->inquiryMerchant->setData($index, $data);
      }
    }
    return $this->inquiryMerchant;
  }

  /**
   * Extract order
   * @param  array $input
   * @return InquiryOrderInterface
   */
  public function extractOrder($input)
  {
    foreach ($input as $key => $value) {
      foreach ($value as $index => $data) {
        switch ($index) {
          case InquiryOrderInterface::ITEMS : 
          $this->inquiryOrder->setData($index, $this->inquiryOrder->extractItems($data));
            break;
          
          default:$this->inquiryOrder->setData($index, $data);
            break;
        }
      }
    }
    return $this->inquiryOrder;
  }
}
