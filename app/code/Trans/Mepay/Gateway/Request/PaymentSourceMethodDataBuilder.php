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
use Trans\Mepay\Logger\Logger;

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
    Logger $logger,
    string $code = ''
  ) {
      $this->subjectReader = $subjectReader;
      $this->provider = $provider;
      $this->code = $code;
      $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
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
    // switch($source) {
    //   case $this->provider::MEGA_CC : $method = self::AUTH_CAPTURE;
    //     break;
    // }
    return $method;
  }
}
