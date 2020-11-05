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

use Magento\Framework\App\ObjectManager;
use Trans\Mepay\Api\Data\InquiryInterface;
use Trans\Mepay\Api\Data\InquiryCustomerInterface;
use Trans\Mepay\Api\Data\InquiryMerchantInterface;
use Trans\Mepay\Api\Data\InquiryOrderInterface;
use Trans\Mepay\Api\Data\InquiryOrderItemsInterface;

class Inquiry
{
  /**
   * @var InquiryInterface
   */
  protected $inquiry;


  /**
   * @var InquiryCustomerInterface
   */
  protected $customer;

  /**
   * @var InquiryMerchantInterface
   */
  protected $merchant;


  /**
   * @var InquiryOrderInterface
   */
  protected $order;

  /**
   * @var ObjectManager
   */
  protected $objectManager;

  /**
   * Constructor
   * @param  ObjectManager $objectManager
   */
  public function __constructor(
    ObjectManager $objectManager
  ){ 
    $this->objectManager = $objectManager;
   }

  /**
   * Convert to Array
   * @param  InquiryInterface $inquiry
   * @return array
   */
  public function convertToArray($inquiry) {
    $arrayInquiry = $this->convertToArrayInquiry($inquiry);
    return $arrayInquiry;
  }

  /**
   * Convert to Array Inquiry
   * @param  InquiryInterface $inquiry
   * @return array
   */
  public function convertToArrayInquiry($inquiry)
  {
    $result = [];
    foreach ($inquiry->getData() as $key => $value) {
      if($key == InquiryInterface::MERCHANT) {
        $result[$key] = $this->convertToArrayMerchant($value);
      } elseif ($key == InquiryInterface::CUSTOMER) {
        $result[$key] = $this->convertToArrayCustomer($value);
      } elseif ($key == InquiryInterface::ORDER) {
        $result[$key] = $this->convertToArrayOrder($value);
      } else {
        $result[$key] = $value;
      }
    }
    return $result;
  }

  /**
   * Convert to array merchant
   * @param  InquiryMerchantInterface $merchant
   * @return array
   */
  public function convertToArrayMerchant($merchant)
  {
    $result = [];
    if ($merchant) {
      foreach ($merchant->getData() as $key => $value) {
        $result[$key] = $value;
      }
    }
    return $result;
  }

  /**
   * Convert to array customer
   * @param  InquiryCustomerInterface $customer
   * @return array
   */
  public function convertToArrayCustomer($customer)
  {
    $result = [];
    if ($customer) {
      foreach ($customer->getData() as $key => $value) {
        $result[$key] = $value;
      }
    }
    return $result;
  }

  /**
   * Convert to array order
   * @param  array $order
   * @return array
   */
  public function convertToArrayOrder($order)
  {
    $result = [];
    if ($order) {
      foreach ($order->getData() as $key => $value) {
        if (in_array($key,[InquiryOrderInterface::ITEMS]) && $value)
        {
           $result[$key] = $this->convertToArrayOrderItems($value);
        } else {
         $result[$key] = $value; 
        }
      }
    }
    return $result;
  }

  /**
   * Convert to array order items
   * @param  array $items
   * @return array
   */
  public function convertToArrayOrderItems($items)
  {
    $result = [];$isList=false;
    foreach ($items as $key => $value) {
      try {
       $sub = [];
       foreach ($value as $index => $item) {
         $sub[$index] = $item;
       }
       $result[] = $sub;
      } catch (\Exception $e) {
        $result[$key] = $value;
        $isList=true;
      }
    }
    return ($isList)? [$result] : $result;
  }

  /**
   * Convert array to object
   * @param  array $inputInquiry
   * @return InquiryInterface
   */
  public function convertToObject($inputInquiry)
  {
    if ($this->objectManager) {
        $inquiry = $this->objectManager->create('Trans\Mepay\Api\Data\InquiryInterface');
        foreach ($inputInquiry as $key => $value) {
          if($key == InquiryInterface::MERCHANT) {
            $value = $this->convertToObjectMerchant($value);
          } elseif ($key == InquiryInterface::CUSTOMER) {
            $value = $this->convertToObjectCustomer($value);
          } elseif ($key == InquiryInterface::ORDER) {
            $value = $this->convertToObjectOrder($value);
          }
          $inquiry->setData($key, $value);
        }
        return $inquiry;
    }
  }

  /**
   * Convert to object merchant
   * @param  array $inputMerchant
   * @return InquiryMerchantInterface
   */
  public function convertToObjectMerchant($inputMerchant)
  {
    $merchant = $this->objectManager->create('Trans\Mepay\Api\Data\InquiryMerchantInterface');
    foreach ($inputMerchant as $key => $value) {
      $merchant->setData($key, $value);
    }
    return $merchant;
  }

  /**
   * Convert to object customer
   * @param  array $inputCustomer
   * @return InquiryCustomerInterface
   */
  public function convertToObjectCustomer($inputCustomer)
  {
    $customer = $this->objectManager->create('Trans\Mepay\Api\Data\InquiryCustomerInterface');
    foreach ($inputCustomer as $key => $value) {
      $customer->setData($key, $value);
    }
    return $customer;
  }

  /**
   * Convert to object
   * @param  array $inputOrder
   * @return InquiryOrderInterface
   */
  public function convertToObjectOrder($inputOrder)
  {
      $order = $this->objectManager->create('Trans\Mepay\Api\Data\InquiryOrderInterface');
    foreach ($inputOrder as $key => $value) {
      if ($key == InquiryOrderInterface::ITEMS) {
        $value = $this->convertToObjectOrderItems($value);
      }
      $order->setData($key, $value);
    }
  }

  /**
   * Convert to object
   * @param  array $inputItems
   * @return InquiryOrderItemsInterface
   */
  public function convertToObjectOrderItems($inputItems)
  {
    $result = [];
    foreach ($inputItems as $key => $value) {
      $item = $this->objectManager->create('Trans\Mepay\Api\Data\InquiryOrderItemsInterface');
      foreach ($value as $index => $data) {
        $item->setData($key, $value);
      }
      $result = $item;
    }
    return $result;
  }
}