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

class PaymentSourceDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const PAYMENT_SOURCE = 'paymentSource';

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
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    
    $payment = $paymentDO->getPayment();
    $method = $payment->getMethodInstance();
    $code = $method->getCode();

    $this->code = $code;
    
    $this->logger->info('$code ' . $code);
    return [self::PAYMENT_SOURCE => $this->provider->getPaymentSource($code)];
  }

  /**
   * Get payment source
   * @param  string $code
   * @return array
   */
  public function getPaymentSource($code)
  {
   return [self::PAYMENT_SOURCE => $this->provider->getPaymentSource($code)]; 
  }
}
