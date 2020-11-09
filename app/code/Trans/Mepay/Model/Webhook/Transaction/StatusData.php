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
namespace Trans\Mepay\Model\Webhook\Transaction;

use Magento\Framework\DataObject;
use Trans\Mepay\Api\Data\TransactionStatusDataInterface;

class StatusData extends DataObject implements TransactionStatusDataInterface
{
  /**
   * @inheritdoc
   */
  public function getMessage()
  {
    return $this->_getData(TransactionStatusDataInterface::MESSAGE);
  }

  /**
   * @inheritdoc
   */
  public function setMessage($data)
  {
    $this->setData(TransactionStatusDataInterface::MESSAGE, $data);
  }

  /**
   * @inheritdoc
   */
  public function getAuthenticationModule()
  {
    return $this->_getData(TransactionStatusDataInterface::AUTHENTICATION_MODULE);
  }

  /**
   * @inheritdoc
   */
  public function setAuthenticationModule($data)
  {
    $this->setData(TransactionStatusDataInterface::AUTHENTICATION_MODULE, $data);
  }

  /**
   * @inheritdoc
   */
  public function getChallengeAuthenticationCode()
  {
    return $this->_getData(TransactionStatusDataInterface::CHALLENGE_AUTHENTICATION_CODE);
  }

  /**
   * @inheritdoc
   */
  public function setChallengeAuthenticationCode($data)
  {
    $this->setData(TransactionStatusDataInterface::CHALLENGE_AUTHENTICATION_CODE, $data);
  }

  /**
   * @inheritdoc
   */
  public function getAuthenticationCode()
  {
    return $this->_getData(TransactionStatusDataInterface::AUTHENTICATION_CODE);
  }

  /**
   * @inheritdoc
   */
  public function setAuthenticationCode($data)
  {
    $this->setData(TransactionStatusDataInterface::AUTHENTICATION_CODE, $data);
  }

  /**
   * @inheritdoc
   */
  public function getCardType()
  {
    return $this->_getData(TransactionStatusDataInterface::CARD_TYPE);
  }

  /**
   * Set card type
   * @param string $data
   * @return  void
   */
  public function setCardType($data)
  {
    $this->setData(TransactionStatusDataInterface::CARD_TYPE, $data);
  }

/**
   * Get card network
   * @return string
   */
  public function getCardNetwork()
  {
    return $this->_getData(TransactionStatusDataInterface::CARD_NETWORK);
  }

  /**
   * Set card network
   * @param string $data
   * @return  void
   */
  public function setCardNetwork($data)
  {
    $this->setData(TransactionStatusDataInterface::CARD_NETWORK, $data);
  }

  /**
   * @inheritdoc
   */
  public function getQrCode()
  {
    return $this->_getData(TransactionStatusDataInterface::QR_CODE);
  }

  /**
   * @inheritdoc
   */
  public function setQrCode($data)
  {
    $this->setData(TransactionStatusDataInterface::QR_CODE, $data);
  }

  /**
   * @inheritdoc
   */
  public function getExpireTime()
  {
    return $this->_getData(TransactionStatusDataInterface::EXPIRE_TIME); 
  }

  /**
   * @inheritdoc
   */
  public function setExpireTime($data)
  {
    $this->setData(TransactionStatusDataInterface::EXPIRE_TIME, $data);
  }

  /**
   * @inheritdoc
   */
  public function getVaNumber()
  {
    return $this->_getData(TransactionStatusDataInterface::VA_NUMBER); 
  }

  /**
   * @inheritdoc
   */
  public function setVaNumber($data)
  {
    $this->setData(TransactionStatusDataInterface::VA_NUMBER, $data);
  }

  /**
   * @inheritdoc
   */
  public function getProcessingCode()
  {
    return $this->_getData(TransactionStatusDataInterface::PROCESSING_CODE); 
  }

  /**
   * @inheritdoc
   */
  public function setProcessingCode($data) {
    $this->setData(TransactionStatusDataInterface::PROCESSING_CODE, $data);
  }


}