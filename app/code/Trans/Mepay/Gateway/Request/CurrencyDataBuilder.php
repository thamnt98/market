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
use Magento\Store\Model\StoreManagerInterface;
use Trans\Mepay\Logger\Logger;

class CurrencyDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const CURRENCY = 'currency';

  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var \Magento\Store\Model\StoreManagerInterface
   */
  private $storeManager;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * Constructor
   * @param SubjectReader         $subjectReader
   * @param StoreManagerInterface $storeManager
   * @param Logger                $logger
   */
  public function __construct(
    SubjectReader $subjectReader,
    StoreManagerInterface $storeManager,
    Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->storeManager = $storeManager;
      $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
    return [self::CURRENCY => $this->storeManager->getStore()->getCurrentCurrency()->getCode()];
  }

  /**
   * Get currency
   * @return array
   */
  public function getCurrency()
  {
    return [self::CURRENCY => $this->storeManager->getStore()->getCurrentCurrency()->getCode()]; 
  }
}
