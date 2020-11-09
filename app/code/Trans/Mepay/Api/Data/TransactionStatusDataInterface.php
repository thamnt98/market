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
interface TransactionStatusDataInterface
{
  /**
   * @var  string
   */
  const MESSAGE = 'message';

  /**
   * @var  string
   */
  const AUTHENTICATION_MODULE = 'authenticationModule';

  /**
   * @var  string
   */
  const CHALLENGE_AUTHENTICATION_CODE = 'challengeAuthenticationCode';

  /**
   * @var  string
   */
  const PROCESSING_CODE = 'processingCode';

  /**
   * @var  string
   */
  const AUTHENTICATION_CODE = 'authenticationCode';

  /**
   * @var  string
   */
  const CARD_TYPE = 'cardType';

  /**
   * @var  string
   */
  const CARD_NETWROK = 'cardNetwork';

  /**
   * @var  string
   */
  const QR_CODE = 'qrCode';

  /**
   * @var  string
   */
  const EXPIRE_TIME = 'expireTime';

  /**
   * @var  string
   */
  const VA_NUMBER = 'vaNumber';


/**
   * Get message
   * @return string
   */
  public function getMessage();

  /**
   * Set message
   * @param string $data
   * @return  void
   */
  public function setMessage($data);

/**
   * Get authenctication module
   * @return string
   */
  public function getAuthenticationModule();

  /**
   * Set authentication module
   * @param string $data
   * @return  void
   */
  public function setAuthenticationModule($data);

/**
   * Get challenge authentication code
   * @return string
   */
  public function getChallengeAuthenticationCode();

  /**
   * Set challenge authentication code
   * @param string $data
   * @return  void
   */
  public function setChallengeAuthenticationCode($data);

/**
   * Get authentication code
   * @return string
   */
  public function getAuthenticationCode();

  /**
   * Set authentication code
   * @param string $data
   * @return  void
   */
  public function setAuthenticationCode($data);

/**
   * Get card type
   * @return string
   */
  public function getCardType();

  /**
   * Set card type
   * @param string $data
   * @return  void
   */
  public function setCardType($data);

/**
   * Get card network
   * @return string
   */
  public function getCardNetwrork();

  /**
   * Set card network
   * @param string $data
   * @return  void
   */
  public function setCardNetwork($data);

  /**
   * Get Qr Code
   * @return string
   */
  public function getQrCode();

  /**
   * Set Qr Code
   * @param  string $data
   * @return  void
   */
  public function setQrCode($data);

  /**
   * Get expire time
   * @return string
   */
  public function getExpireTime();

  /**
   * Set expire time
   * @param string $data
   */
  public function setExpireTime($data);

  /**
   * Get expire time
   * @return string
   */
  public function getVaNumber();

  /**
   * Set expire time
   * @param string $data
   */
  public function setVaNumber($data);

  /**
   * Get processing code
   * @return string
   */
  public function getProcessingCode();

  /**
   * Set processing code
   * @param string $data
   */
  public function setProcessingCode($data);
}