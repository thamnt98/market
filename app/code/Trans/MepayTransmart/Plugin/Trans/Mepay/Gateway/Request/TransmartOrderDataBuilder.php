<?php 
namespace Trans\MepayTransmart\Plugin\Trans\Mepay\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use SM\Checkout\Helper\OrderReferenceNumber;
use Trans\Mepay\Logger\LoggerWrite;

class TransmartOrderDataBuilder 
{
  protected $subjectReader;
  protected $referenceNumberHelper;
  protected $logger;

  public function __construct(
    SubjectReader $subjectReader,
    OrderReferenceNumber $referenceNumberHelper,
    LoggerWrite $logger
  ) {
    $this->subjectReader = $subjectReader;
    $this->referenceNumberHelper = $referenceNumberHelper;
    $this->logger = $logger;
  }

  public function afterBuild(\Trans\Mepay\Gateway\Request\OrderDataBuilder $subject, $result, $buildSubject)
  {
    $result[$subject::ORDER][$subject::ID] = $this->getReferenceNumber($buildSubject);
    return $result;
  }

  public function getReferenceNumber($buildSubject)
  {
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    $order = $paymentDO->getOrder();
    return $this->referenceNumberHelper->generateReferenceNumber($order, false);
  }
}