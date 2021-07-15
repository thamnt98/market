<?php 
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Plugin\Trans\Mepay\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use SM\Checkout\Helper\OrderReferenceNumber;
use Trans\Mepay\Logger\LoggerWrite;

class TransmartOrderDataBuilder 
{
  /**
   * @var SubjectReader
   */
  protected $subjectReader;

  /**
   * @var OrderReferenceNumber
   */
  protected $referenceNumberHelper;

  /**
   * @var LoggerWrite
   */
  protected $logger;

  /**
   * Constructor method
   * @param SubjectReader $subjectReader
   * @param OrderReferenceNumber $referenceNumberHelper
   * @param LoggerWrite $logger
   */
  public function __construct(
    SubjectReader $subjectReader,
    OrderReferenceNumber $referenceNumberHelper,
    LoggerWrite $logger
  ) {
    $this->subjectReader = $subjectReader;
    $this->referenceNumberHelper = $referenceNumberHelper;
    $this->logger = $logger;
  }

  /**
   * After build
   * @param  \Trans\Mepay\Gateway\Request\OrderDataBuilder $subject
   * @param  array $result
   * @param  array $buildSubject
   * @return array
   */
  public function afterBuild(\Trans\Mepay\Gateway\Request\OrderDataBuilder $subject, $result, $buildSubject)
  {
    $result[$subject::ORDER][$subject::ID] = $this->getReferenceNumber($buildSubject);
    return $result;
  }

  /**
   * Get reference number
   * @param  array $buildSubject
   * @return string
   */
  public function getReferenceNumber($buildSubject)
  {
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    $order = $paymentDO->getOrder();
    return $this->referenceNumberHelper->generateReferenceNumber($order, false);
  }
}