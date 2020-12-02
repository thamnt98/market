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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Checkout\Model\Session;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Logger\Logger;


class TransmartReferenceUrlDataBuilder 
{
  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var \Trans\Mepay\Model\Config\Config
   */
  private $config;

  /**
   * @var Session
   */
  private $session;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * Constructor
   * @param SubjectReader $subjectReader
   * @param Config        $config
   * @param Logger        $logger
   */
  public function __construct(
    SubjectReader $subjectReader,
    Session $session,
    Config $config,
    Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->session = $session;
      $this->config = $config;
      $this->logger = $logger;
  }

  /**
   * After build plugin
   * @param  \Trans\Mepay\Gateway\Request\ReferenceUrlDataBuilder $subject
   * @param  array $result
   * @param  array $buildSubject
   * @return array
   */
  public function afterBuild(\Trans\Mepay\Gateway\Request\ReferenceUrlDataBuilder $subject, $result, $buildSubject)
  {
    if ($this->session->getArea() == \SM\Checkout\Helper\OrderReferenceNumber::AREA_APP)
    {
      $paymentDO = $this->subjectReader->readPayment($buildSubject);
      $order = $paymentDO->getOrder();
      $result[$subject::REFERENCE_URL] = $result[$subject::REFERENCE_URL].'?increment_id='.$order->getOrderIncrementId();
    }
    return $result;
  }
}