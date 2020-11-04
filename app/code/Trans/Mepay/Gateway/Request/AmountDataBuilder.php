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
use Trans\Mepay\Logger\Logger;

class AmountDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const AMOUNT = 'amount';

  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * Constructor
   * @param SubjectReader $subjectReader
   * @param Logger        $logger
   */
  public function __construct(
    SubjectReader $subjectReader,
    Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    $order = $paymentDO->getOrder();
    $data = [self::AMOUNT => (int) $order->getGrandTotalAmount()];
    return $data;
  }

  /**
   * Get amount
   * @param int $amount
   * @return  array
   */
  public function getAmount($amount)
  {
    return [self::AMOUNT => (int) $amount];
  }
}
