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
use Trans\Mepay\Api\Data\ResponseInterface;
use Trans\Mepay\Api\Data\InquiryInterfaceFactory;
use Trans\Mepay\Api\Data\InquiryInterface;
use Trans\Mepay\Api\Data\TransactionInterfaceFactory;
use Trans\Mepay\Api\Data\TransactionInterface;

class Response extends DataObject implements ResponseInterface
{
  /**
   * @var InquiryInterface
   */
  protected $inquiry;

  /**
   * @var TransactionInterface
   */
  protected $transaction;

  /**
   * Constructor
   * @param InquiryInterfaceFactory     $inquiryFactory
   * @param TransactionInterfaceFactory $transactionFactory
   */
  public function __construct(
    InquiryInterfaceFactory $inquiryFactory,
    TransactionInterfaceFactory $transactionFactory
  ) {
    $this->inquiry = $inquiryFactory->create();
    $this->transaction = $transactionFactory->create();
  }

  /**
   * @inheritdoc
   */
  public function setStatus($data)
  {
    $this->setData(ResponseInterface::STATUS, $data);
  }

  /**
   * @inheritdoc
   */
  public function getStatus()
  {
    return $this->_getData(ResponseInterface::STATUS);
  }

  /**
   * @inheritdoc
   */
  public function setValidateSignature($data)
  {
   $this->setData(ResponseInterface::VALIDATE_SIGNATURE, $data);
  }

  /**
   * @inheritdoc
   */
  public function getValidateSignature()
  {
    return $this->_getData(ResponseInterface::VALIDATE_SIGNATURE);
  }

  /**
   * @inheritdoc
   */
  public function setInquiry($data)
  {
    $this->setData(ResponseInterface::INQUIRY, $data);
  }
  /**
   * @inheritdoc
   */
  public function getInquiry()
  {
    return $this->_getData(ResponseInterface::INQUIRY);
  }

  /**
   * @inheritdoc
   */
  public function getList()
  {
    return $this->_getData(ResponseInterface::LIST_DATA);
  }

  /**
   * @inheritdoc
   */
  public function setList($data)
  {
    $this->setData(ResponseInterface::LIST_DATA, $data);
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function setToken($token)
  {
    $this->setData(ResponseInterface::TOKEN, $token);
  }

  /**
   * @inheritdoc
   */
  public function getToken()
  {
    return $this->_getData(ResponseInterface::TOKEN);
  }

  /**
   * Build
   * @param  string $type
   * @param  string $status
   * @param  string $signature
   * @param  InquiryInterface $inquiry
   * @return ResponseInterface
   */
  public function build($type, $status, $signature, $inquiry = null, $transaction = null)
  {
    $this->setStatus($status);
    $this->setValidateSignature($signature);
    //if ($type == ResponseInterface::PAYMENT_VALIDATE_TYPE) {
      $this->setInquiry($inquiry);
   // }
    return $this;
  }
}
