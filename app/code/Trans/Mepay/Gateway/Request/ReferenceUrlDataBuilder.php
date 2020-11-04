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
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Logger\Logger;

class ReferenceUrlDataBuilder implements BuilderInterface
{

  const REFERENCE_URL = 'referenceUrl';

  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var \Trans\Mepay\Model\Config\Config
   */
  private $config;

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
    Config $config,
    Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->config = $config;
      $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
    return [self::REFERENCE_URL => $this->config->getMerchantReferenceUrl()];
  }

  /**
   * Get reference url
   * @return array
   */
  public function getReferenceUrl()
  {
   return [self::REFERENCE_URL => $this->config->getMerchantReferenceUrl()]; 
  }
}
