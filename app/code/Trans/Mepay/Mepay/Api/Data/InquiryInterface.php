<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Api\Data;

/**
 * @api
 */
interface InquiryInterface
{
  /**
   * @var string
   */
  const ID = 'id';

  /**
   * @var string
   */
  const CREATED_TIME = 'createdTime';

  /**
   * @var string
   */
  const UPDATED_TIME = 'updatedTime';

  /**
   * @var string
   */
  const CURRENCY = 'currency';

  /**
   * @var string
   */
  const AMOUNT = 'amount';

  /**
   * @var string
   */
  const LOCKED_AMOUNT = 'lockedAmount';

  /**
   * @var string
   */
  const STATUS = 'status';

  /**
   * @var string
   */
  const MERCHANT_ID = 'merchantId';

  /**
   * @var string
   */
  const ORDER = 'order';

  /**
   * @var string
   */
  const CUSTOMER = 'customer';

  /**
   * @var string
   */
  const MERCHANT = 'merchant';

  /**
   * getid
   * @return string
   */
  public function getId();


  /**
   * Set id
   * @param string $data
   * @return void
   */
  public function setId($data);

  /**
   * Get created time
   * @return string
   */
  public function getCreatedTime();

  /**
   * Set created time
   * @param string $data
   * @return void
   */
  public function setCreatedTime($data);

  /**
   * Get updated time
   * @return string
   */
  public function getUpdatedTime();

  /**
   * Set updated time
   * @param string $data
   * @return void
   */
  public function setUpdatedTime($data);

  /**
   * Get currency
   * @return string
   */
  public function getCurrency();

  /**
   * Set currency
   * @param string $data
   * @return void
   */
  public function setCurrency($data);

  /**
   * Get amount
   * @return string
   */
  public function getAmount();

  /**
   * Set amount
   * @param string $data
   * @return void
   */
  public function setAmount($data);

  /**
   * Get locked amount
   * @return string
   */
  public function getLockedAmount();

  /**
   * Set locked amount
   * @param string $data
   * @return void
   */
  public function setLockedAmount($data);

  /**
   * Get status
   * @return string
   */
  public function getStatus();

  /**
   * Set status
   * @param string $data
   * @return void
   */
  public function setStatus($data);

  /**
   * Get merchant id
   * @return string
   */
  public function getMerchantId();

  /**
   * Set merchant id
   * @param string $data
   * @return void
   */
  public function setMerchantId($data);

  /**
   * Get order
   * @return \Trans\Mepay\Api\Data\InquiryOrderInterface
   */
  public function getOrder();

  /**
   * Set order
   * @param \Trans\Mepay\Api\Data\InquiryOrderInterface $data
   * @return void
   */
  public function setOrder($data);

  /**
   * Get customer
   * @return \Trans\Mepay\Api\Data\InquiryCustomerInterface
   */
  public function getCustomer();

  /**
   * Set customer
   * @param \Trans\Mepay\Api\Data\InquiryCustomerInterface $data
   * @return void
   */
  public function setCustomer($data);

  /**
   * Get merchant
   * @return \Trans\Mepay\Api\Data\InquiryMerchantInterface
   */
  public function getMerchant();

  /**
   * Set merchant
   * @param \Trans\Mepay\Api\Data\InquiryMerchantInterface $data
   * @return void
   */
  public function setMerchant($data);
}
