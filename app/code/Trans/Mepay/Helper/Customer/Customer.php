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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Customer extends AbstractHelper 
{
  /**
   * @var CustomerRepositoryInterface
   */
  protected $customerRepo;

  /**
   * @var CustomerInterfaceFactory
   */
  protected $customerFactory;

  /**
   * Constructor
   * @param  Context                     $context
   * @param  CustomerRepositoryInterface $customerRepo
   * @param  CustomerInterfaceFactory    $customerFactory
   */
  public function __construct(
    Context $context,
    CustomerRepositoryInterface $customerRepo,
    CustomerInterfaceFactory $customerFactory
  ) {
    $this->customerRepo = $customerRepo;
    $this->customerFactory = $customerFactory;
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
  public function setCustomerToken($id, $token)
  {
    $customer = $this->getCustomer($id);
    try {
      $customer->setCustomAttribute('trans_mepay_cc_token', $token);
      //$customer->setData('trans_mepay_cc_token', $token);
      return $this->customerRepo->save($customer);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Get customer token
   * @param  int $id
   * @return string
   */
  public function getCustomerToken($id)
  {
    try {
      $customer = $this->getCustomer($id);
      $token = $customer->getCustomAttribute('trans_mepay_cc_token');
      return ($token)? $token->getValue() : '';
    } catch (\Exception $e) {
      throw $e;
    }
  }
}