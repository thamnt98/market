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
namespace Trans\Mepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Trans\Mepay\Model\Config\Provider\Cc;
use Trans\Mepay\Model\Config\Provider\Qris;
use Trans\Mepay\Model\Config\Provider\Va;
use Trans\Mepay\Model\Config\Provider\Debit;

class Data extends AbstractHelper
{
  /**
   * @var array
   */
  const BANK_MEGA_PAYMENT_METHOD = [ Cc::CODE_CC, Qris::CODE_QRIS, Va::CODE_VA, Debit::CODE ];

  /**
   * Constructor
   * @param Context $context
   */
  public function __construct(Context $context) {parent::__construct($context);}

  /**
   * Get class instance
   * @param  string $strClass
   * @return Class
   */
  public static function getClassInstance($strClass)
  {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    return $objectManager->create($strClass);
  }

  /**
   * Get connection class
   * @return \Magento\Framework\App\ResourceConnection
   */
  public static function getConnectionClass()
  {
    return self::getClassInstance('Magento\Framework\App\ResourceConnection');
  }

  /**
   * Get connection
   * @return connection
   */
  public static function getConnection()
  {
    return self::getConnectionClass()->getConnection();
  }

  /**
   * Is payment method is bank mega
   * @param  string  $method
   * @return boolean
   */
  public static function isMegaMethod($method)
  {
    return in_array($method, self::BANK_MEGA_PAYMENT_METHOD);
  }

  /**
   * Get order repo
   * @return \Magento\Sales\Api\OrderRepositoryInterface
   */
  public static function getOrderRepo()
  {
    return self::getClassInstance('Magento\Sales\Api\OrderRepositoryInterface');
  }

  /**
   * Get order object
   * @return \Magento\Sales\Api\Data\OrderInterface
   */
  public static function getOrderInterface()
  {
    return self::getClassInstance('Magento\Sales\Api\Data\OrderInterface');
  }

  /**
   * Get order collection object
   * @return \Magento\Sales\Model\ResourceModel\Order\Collection
   */
  public static function getOrderCollection()
  {
    return self::getOrderInterface()->getCollection();
  }

  /**
   * Get order by id
   * @param  int $orderId
   * @return \Magento\Sales\Api\Data\OrderInterface
   */
  public static function getOrderById($orderId)
  {
    $repo = self::getOrderRepo();
    return $repo->get($orderId);
  }

  /**
   * Get payment method by order id
   * @param  int $orderId
   * @return string
   */
  public static function getPaymentMethodByOrderId($orderId)
  {
    $order = self::getOrderById($orderId);
    return $order->getPayment()->getMethod();
  }

  /**
   * Get customer session
   * @return \Magento\Customer\Model\Session
   */
  public static function getCustomerSession()
  {
    return self::getClassInstance('Magento\Customer\Model\Session');
  }

  /**
   * Get customer repo
   * @return \Magento\Customer\Api\CustomerRepositoryInterface
   */
  public static function getCustomerRepo()
  {
    return self::getClassInstance('Magento\Customer\Api\CustomerRepositoryInterface');
  }

  /**
   * Get Customer
   * @return \Magento\Customer\Api\Data\CustomerInterface
   */
  public static function getCustomer()
  {
    return self::getCustomerSession()->getCustomer();
  }

  /**
   * Get Customer id
   * @return int
   */
  public static function getCustomerId()
  {
    return self::getCustomer()->getId();
  }

  /**
   * Get quote repo
   * @return \Magento\Quote\Api\CartRepositoryInterface
   */
  public static function getQuoteRepo()
  {
    return self::getClassInstance('Magento\Quote\Api\CartRepositoryInterface');
  }

}