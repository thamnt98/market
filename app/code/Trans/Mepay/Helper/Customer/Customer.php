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
namespace Trans\Mepay\Helper\Customer;

use Magento\CardinalCommerce\Model\Request\TokenBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Trans\Mepay\Api\Data\CardSavedTokenInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Trans\Mepay\Logger\LoggerWrite;

class Customer extends AbstractHelper 
{
  /**
   * @var CustomerRepositoryInterface
   */
  protected $customerRepo;

  protected $cartrepo;

  /**
   * @var CustomerInterfaceFactory
   */
  protected $customerFactory;

  /**
   * @var \Magento\Framework\Serialize\SerializerInterface
   */
  protected $serialize;

  /**
   * @var LoggerWrite
   */
  protected $logger;

  /**
   * Constructor
   * @param  Context                     $context
   * @param  CustomerRepositoryInterface $customerRepo
   * @param  CustomerInterfaceFactory    $customerFactory
   */
  public function __construct(
    Context $context,
    CustomerRepositoryInterface $customerRepo,
    CartRepositoryInterface $cartrepo,
    CustomerInterfaceFactory $customerFactory,
    SerializerInterface $serialize,
    LoggerWrite $logger
  ) {
    $this->customerRepo = $customerRepo;
    $this->cartrepo = $cartrepo;
    $this->customerFactory = $customerFactory;
    $this->serialize = $serialize;
    $this->logger = $logger;
    parent::__construct($context);
  }

  /**
   * Get customer
   * @param  int $id
   * @return CustomerInterface
   */
  public function getCustomer($id)
  {
    return $this->customerRepo->getById($id);
  }

  /**
   * Set customer token
   * @param int $id
   * @param string $token
   * @return CustomerInterface
   */
  public function setCustomerToken($id, $method, $token)
  {
    try {
      return $this->_setCustomerToken($id, $method, $token);
    } catch (\Exception $e) {
      $this->logger->log('[SavingCardTokenizationError]'.$e->getMessage());
    }
    return false;
  }

  /**
   * Get customer token
   * @param  int $id
   * @return string
   */
  public function getCustomerToken($id, $method)
  {
      $customer = $this->getCustomer($id);
      return $this->_getCustomerToken($customer, $method);
  }

  public function getCustomerActiveToken($id)
  {
    $quote = $this->cartrepo->getActiveForCustomer($id);
    $payment = $quote->getPayment();
    return ($payment->getCardToken())?? '';
  }

  /**
   * Set customer token
   * @param int $id
   * @param string $token
   * @return CustomerInterface
   */
  protected function _setCustomerToken($id, $method, $token)
  {
    $customer = $this->getCustomer($id);
    $oldToken = $this->_getCustomerToken($customer, $method);
    $newToken = $this->composeNewToken($method, $oldToken, $token);
    $customer->setCustomAttribute($method.'_'.CardSavedTokenInterface::CARDTOKEN, $newToken);
    return $this->customerRepo->save($customer);
  }

  /**
   * Get customer token
   *
   * @param \Magento\Customer\Api\Data\CustomerInterface $customer
   * @param string $method
   * @return string
   */
  protected function _getCustomerToken($customer, $method)
  {
      $token = $customer->getCustomAttribute($method.'_'.CardSavedTokenInterface::CARDTOKEN);
      return ($token)? $token->getValue() : '[]';
  }

  /**
   * Compose New Token
   *
   * @param string $method
   * @param string $oldToken
   * @param string $newToken
   * @return void
   */
  protected function composeNewToken($method, $oldToken, $newToken)
  {
    $token = $this->compose($method, $newToken);
    $oldToken = $this->serialize->unserialize($oldToken);
    $addFlag = true;
    foreach ($oldToken as $key => $value) {
      foreach ($token as $index => $data) {
        if($data['key'] == $value['key']){
          $oldToken[$key] = $data;
          $addFlag = false;
          break;
        }
      }
    }
    $result = ($addFlag)? array_merge($token, $oldToken) : $oldToken;
    return $this->serialize->serialize($result);
  }

  /**
   * Compose
   *
   * @param string $method
   * @param string $token
   * @return array
   */
  protected function compose($method, $token)
  {
    $result = [];
    $split = explode('|', $token);
    $result['method'] = $method;
    $result['key'] = $split[1];
    $result['token'] = $token;
    return [$result];
  }

}<?php
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
namespace Trans\Mepay\Helper\Customer;

use Magento\CardinalCommerce\Model\Request\TokenBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Trans\Mepay\Api\Data\CardSavedTokenInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Trans\Mepay\Logger\LoggerWrite;

class Customer extends AbstractHelper 
{
  /**
   * @var CustomerRepositoryInterface
   */
  protected $customerRepo;

  protected $cartrepo;

  /**
   * @var CustomerInterfaceFactory
   */
  protected $customerFactory;

  /**
   * @var \Magento\Framework\Serialize\SerializerInterface
   */
  protected $serialize;

  /**
   * @var LoggerWrite
   */
  protected $logger;

  /**
   * Constructor
   * @param  Context                     $context
   * @param  CustomerRepositoryInterface $customerRepo
   * @param  CustomerInterfaceFactory    $customerFactory
   */
  public function __construct(
    Context $context,
    CustomerRepositoryInterface $customerRepo,
    CartRepositoryInterface $cartrepo,
    CustomerInterfaceFactory $customerFactory,
    SerializerInterface $serialize,
    LoggerWrite $logger
  ) {
    $this->customerRepo = $customerRepo;
    $this->cartrepo = $cartrepo;
    $this->customerFactory = $customerFactory;
    $this->serialize = $serialize;
    $this->logger = $logger;
    parent::__construct($context);
  }

  /**
   * Get customer
   * @param  int $id
   * @return CustomerInterface
   */
  public function getCustomer($id)
  {
    return $this->customerRepo->getById($id);
  }

  /**
   * Set customer token
   * @param int $id
   * @param string $token
   * @return CustomerInterface
   */
  public function setCustomerToken($id, $method, $token)
  {
    try {
      return $this->_setCustomerToken($id, $method, $token);
    } catch (\Exception $e) {
      $this->logger->log('[SavingCardTokenizationError]'.$e->getMessage());
    }
    return false;
  }

  /**
   * Get customer token
   * @param  int $id
   * @return string
   */
  public function getCustomerToken($id, $method)
  {
      $customer = $this->getCustomer($id);
      return $this->_getCustomerToken($customer, $method);
  }

  public function getCustomerActiveToken($id)
  {
    $quote = $this->cartrepo->getActiveForCustomer($id);
    $payment = $quote->getPayment();
    return ($payment->getCardToken())?? '';
  }

  /**
   * Set customer token
   * @param int $id
   * @param string $token
   * @return CustomerInterface
   */
  protected function _setCustomerToken($id, $method, $token)
  {
    $customer = $this->getCustomer($id);
    $oldToken = $this->_getCustomerToken($customer, $method);
    $newToken = $this->composeNewToken($method, $oldToken, $token);
    $customer->setCustomAttribute($method.'_'.CardSavedTokenInterface::CARDTOKEN, $newToken);
    return $this->customerRepo->save($customer);
  }

  /**
   * Get customer token
   *
   * @param \Magento\Customer\Api\Data\CustomerInterface $customer
   * @param string $method
   * @return string
   */
  protected function _getCustomerToken($customer, $method)
  {
      $token = $customer->getCustomAttribute($method.'_'.CardSavedTokenInterface::CARDTOKEN);
      return ($token)? $token->getValue() : '[]';
  }

  /**
   * Compose New Token
   *
   * @param string $method
   * @param string $oldToken
   * @param string $newToken
   * @return void
   */
  protected function composeNewToken($method, $oldToken, $newToken)
  {
    $token = $this->compose($method, $newToken);
    $oldToken = $this->serialize->unserialize($oldToken);
    $addFlag = true;
    foreach ($oldToken as $key => $value) {
      foreach ($token as $index => $data) {
        if($data['key'] == $value['key']){
          $oldToken[$key] = $data;
          $addFlag = false;
          break;
        }
      }
    }
    $result = ($addFlag)? array_merge($token, $oldToken) : $oldToken;
    return $this->serialize->serialize($result);
  }

  /**
   * Compose
   *
   * @param string $method
   * @param string $token
   * @return array
   */
  protected function compose($method, $token)
  {
    $result = [];
    $split = explode('|', $token);
    $result['method'] = $method;
    $result['key'] = $split[1];
    $result['token'] = $token;
    return [$result];
  }

}