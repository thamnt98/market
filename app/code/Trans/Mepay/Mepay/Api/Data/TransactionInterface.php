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
interface TransactionInterface
{
  /**
   * @var  string
   */
  const ID = 'id';

  /**
   * @var  string
   */
  const CREATED_TIME = 'createdTime';

  /**
   * @var  string
   */
  const UPDATED_TIME = 'updatedTime';

  /**
   * @var  string
   */
  const CURRENCY = 'currency';

  /**
   * @var  string
   */
  const AMOUNT = 'amount';

  /**
   * @var  string
   */
  const INQUIRY_ID = 'inquiryId';

  /**
   * @var  string
   */
  const MERCHANT_ID = 'merchantId';

  /**
   * @var  string
   */
  const TYPE = 'type';

  /**
   * @var  string
   */
  const STATUS = 'status';

  /**
   * @var  string
   */
  const STATUS_CODE = 'statusCode';

  /**
   * @var  string
   */
  const STATUS_DATA = 'statusData';

  /**
   * @var  string
   */
  const NETWORK_REFERENCE_ID = 'networkReferenceId';

  /**
   * @var  string
   */
  const AUTHORIZATION_CODE = 'authorizationCode';

  /**
   * Get id
   * @return string
   */
  public function getId();

  /**
   * Set Id
   * @param string $data
   * @return  void
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
   * @return  void
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
   * @return  void
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
   * @return  void
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
   * @return  void
   */
  public function setAmount($data);

  /**
   * Get inquiry id
   * @return string
   */
  public function getInquiryId();

  /**
   * Set inquiry id
   * @param string $data
   * @return  void
   */
  public function setInquiryId($data);

  /**
   * Get merchant id
   * @return string
   */
  public function getMerchantId();

  /**
   * Set merchant id
   * @param string $data
   * @return  void
   */
  public function setMerchantId($data);

  /**
   * Get type
   * @return string
   */
  public function getType();

  /**
   * Set type
   * @param string $data
   * @return  void
   */
  public function setType($data);

  /**
   * Get status
   * @return string
   */
  public function getStatus();

  /**
   * Set status
   * @param string $data
   * @return  void
   */
  public function setStatus($data);

  /**
   * Get status code
   * @return string
   */
  public function getStatusCode();

  /**
   * Set status code
   * @param string $data
   * @return  void
   */
  public function setStatusCode($data);

  /**
   * Get status data
   * @return \Trans\Mepay\Api\Data\TransactionStatusDataInterface
   */
  public function getStatusData();

  /**
   * Set status data
   * @param \Trans\Mepay\Api\Data\TransactionStatusDataInterface $data
   * @return  void
   */
  public function setStatusData($data);

  /**
   * Get network reference id
   * @return string
   */
  public function getNetworkReferenceId();

  /**
   * Set nework reference id
   * @param string $data
   * @return  void
   */
  public function setNetworkReferenceId($data);

  /**
   * Get authorization code
   * @return string
   */
  public function getAuthorizationCode();

  /**
   * Set authorization code
   * @param string $data
   * @return  void
   */
  public function setAuthorizationCode($data);
}