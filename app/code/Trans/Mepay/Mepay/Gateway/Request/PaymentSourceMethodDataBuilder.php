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
namespace Trans\Mepay\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Trans\Mepay\Model\Config\Source\Provider;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Logger\Logger;
use Trans\Mepay\Model\Config\Provider\CcDebit;

class PaymentSourceMethodDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const PAYMENT_SOURCE_METHOD = 'paymentSourceMethod';

  const AUTH_CAPTURE = 'authcapture';

  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var Provider
   */
  private $provider;

  /**
   * @var string
   */
  private $code;

  /**
   * @var Config
   */
  private $config;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * Constructor
   * @param SubjectReader $subjectReader
   * @param Provider      $provider
   * @param Logger        $logger
   * @param string        $code
   */
  public function __construct(
    SubjectReader $subjectReader,
    Provider $provider,
    Config $config,
    Logger $logger,
    string $code = ''
  ) {
      $this->subjectReader = $subjectReader;
      $this->provider = $provider;
      $this->config = $config;
      $this->code = $code;
      $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    
    $payment = $paymentDO->getPayment();
    $method = $payment->getMethodInstance();
    $code = $method->getCode();

    $this->code = $code;
    
    $source = $this->provider->getPaymentSource($this->code);
    $method = $this->getPaymentSourceMethod($source);
    return [self::PAYMENT_SOURCE_METHOD => $method];
  }

  /**
   * Get Payment Source Method
   * @param  string $source
   * @return string
   */
  public function getPaymentSourceMethod($source)
  {
    $method = '';
    if ((int) $this->config->getIsAuthCapture()) {
      switch($source) {
        case $this->provider::MEGA_CC :
            $method = self::AUTH_CAPTURE;
        break;
      }
    }
    return $method;
  }
}
