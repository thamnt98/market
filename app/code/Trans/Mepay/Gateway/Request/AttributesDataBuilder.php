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

class AttributesDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const ATTRIBUTES = 'attributes';

  /**
   * @var string
   */
  const DISABLE_PROMO = 'disablePromo';

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
    // return [self::ATTRIBUTES => [self::DISABLE_PROMO => $this->isPromoDisabled()]];
    return [];
  }

  /**
   * Is Promo Disabled
   * @return string
   */
  public function isPromoDisabled()
  {
    return 'true';
  }
}
