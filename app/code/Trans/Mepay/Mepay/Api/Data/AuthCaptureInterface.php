<?php

/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Api\Data;

/**
 * AuthCaptureInterface
 */
interface AuthCaptureInterface
{ 
  /**
   * Constants for keys of data array. Identical to the name of the getter in snake case
   */
  const TABLE_NAME = 'trans_mepay_auth_capture';
  const ID = 'id';
  const REFERENCE_NUMBER = 'reference_number';
  const REFERENCE_ORDER_ID = 'reference_order_id';
  const STATUS = 'status';
  const CREATED_AT = 'created_at';
  const UPDATED_AT = 'updated_at';

  /**
   * @return int
   */
  public function getId();

  /**
   * @param int $idData
   * @return void
   */
  public function setId($idData);

  /**
   * @return string
   */
  public function getReferenceNumber();

  /**
   * @param string $refNumber
   * @return void
   */
  public function setReferenceNumber($refNumber);

  /**
   * @return string
   */
  public function getReferenceOrderId();

  /**
   * @param string $refOrderId
   * @return void
   */
  public function setReferenceOrderId($refOrderId);

  /**
   * @return int
   */
  public function getStatus();

  /**
   * @param string $status
   * @return void
   */
  public function setStatus($status);

  /**
   * @return string
   */
  public function getCreatedAt();

  /**
   * @param string $createdAt
   * @return void
   */
  public function setCreatedAt($createdAt);

  /**
   * @return string
   */
  public function getUpdatedAt();

  /**
   * @param string $updatedAt
   * @return void
   */
  public function setUpdatedAt($updatedAt);

  /**
   * Send second capture request
   * @param  string $reffNumber
   * @param  int $amountAdjustment
   * @return \Trans\Mepay\Api\Data\ResponseInterface
   */
  public function send($reffNumber, $amountAdjustment);

}
