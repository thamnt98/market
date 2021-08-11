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
use Trans\Mepay\Helper\Customer\Customer as CustomerHelper;
use Trans\Mepay\Logger\Logger;

class TokenDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const TOKEN = 'token';

  /**
   * @var string
   */
  const DELIMITER = '|';

  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var Customer
   */
  private $customerHelper;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * Constructor
   * @param SubjectReader  $subjectReader
   * @param CustomerHelper $customerHelper
   * @param Logger         $logger
   */
  public function __construct(
    SubjectReader $subjectReader,
    CustomerHelper $customerHelper,
    Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->customerHelper = $customerHelper;
      $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    $order = $paymentDO->getOrder();
    try {
      $token = $this->customerHelper->getCustomerActiveToken($order->getCustomerId());
      if ($token && $token != '') {
          $token = $this->extractToken($token);
      }
    } catch (\Exception $e) {
      $token = '';
    }
    return [self::TOKEN => $token];
  }

  /**
   * Get customer token
   * @param  \Magento\Sales\Api\Data\OrderInterface $order
   * @return array
   */
  public function getCustomerToken($order)
  {
    $payment = $order->getPayment();
    $token = $this->customerHelper->getCustomerToken($order->getCustomerId(), $payment->getMethodInstance());
    return [self::TOKEN => $token];
  }

  /**
   * Extract token
   *
   * @param string $token
   * @return string
   */
  protected function extractToken($token)
  {
    $result = explode(self::DELIMITER, $token);
    foreach ($result as $value) {
      return $value;
    }
  }
}