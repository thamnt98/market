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
interface ResponseInterface
{
  /**
   * @var string
   */
  const STATUS = 'status';

  /**
   * @var string
   */
  const STATUS_OK = 'ok';

  /**
   * @var string
   */
  const STATUS_ACK = 'ack';

  /**
   * @var string
   */
  const STATUS_NACK = 'nack';

  /**
   * @var string
   */
  const STATUS_FAILED = 'nok';

  /**
   * @var string
   */
  const INQUIRY = 'inquiry';

  /**
   * @var string
   */
  const VALIDATE_SIGNATURE = 'validateSignature';


  /**
   * @var string
   */
  const PAYMENT_VALIDATE_TYPE = 'payment.validate';

  /**
   * @var string
   */
  const PAYMENT_RECEIVED_TYPE = 'payment.received';

  /**
   * @var string
   */
  const LIST_DATA = 'list';

    /**
   * @var string
   */
  const TOKEN = 'token';

  /**
   * Set status
   * @param string $status
   * @return void
   */
  public function setStatus($data);

  /**
   * Get status
   * @return void
   */
  public function getStatus();

  /**
   * Set validate signature
   * @param string $data
   * @return void
   */
  public function setValidateSignature($data);

  /**
   * Get validate signature
   * @return string
   */
  public function getValidateSignature();

  /**
   * Set Inquiry
   * @param [] $data
   * @return void
   */
  public function setInquiry($data);

  /**
   * Get Inquiry
   * @return Trans\Mepay\Api\Data\InquiryInterface
   */
  public function getInquiry();

  /**
   * get list
   * @return \Trans\Mepay\Model\CardSavedToken[]
   */
  public function getList();

  /**
   * set list
   * @param array $data
   * @return $this
   */
  public function setList($data);

  /**
   * Set token
   * @param string $token
   * @return void
   */
  public function setToken($token);

  /**
   * Get token
   * @return string
   */
  public function getToken();
}