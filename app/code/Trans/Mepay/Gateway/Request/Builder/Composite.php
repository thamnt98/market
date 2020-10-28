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
namespace Trans\Mepay\Gateway\Request\Builder;

use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use Magento\Payment\Gateway\Request\BuilderComposite;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Trans\Mepay\Logger\Logger;

class Composite extends BuilderComposite
{
  /**
   * @var BuilderInterface[] | TMap
   */
  private $builders;

  /**
   * @var \Logger
   */
  protected $logger;

  /**
   * Constructor
   * @param TMapFactory $tmapFactory
   * @param Logger      $logger
   * @param array       $builders
   */
  public function __construct(
      TMapFactory $tmapFactory,
      Logger $logger,
      array $builders = []
  ) {
      $this->builders = $tmapFactory->create(
          [
              'array' => $builders,
              'type' => BuilderInterface::class
          ]
      );
      $this->logger = $logger;
  }

  /**
   * Builds ENV request
   *
   * @param array $buildSubject
   * @return array
   */
  public function build(array $buildSubject)
  {
      $result = [];
      foreach ($this->builders as $builder) {
          // @TODO implement exceptions catching
          $result = $this->merge($result, $builder->build($buildSubject));
      }
      return $result;
  }

  /**
   * Build capture request
   * @param   $amount
   * @param   $methodCode
   * @param   $order
   * @return array
   */
  public function buildCaptureRequest($amount, $methodCode, $order)
  {
    return array_merge(
      \Trans\Mepay\Gateway\Request\AmountDataBuilder::getAmount($amount),
      \Trans\Mepay\Gateway\Request\CurrencyDataBuilder::getCurrency(),
      \Trans\Mepay\Gateway\Request\ReferenceUrlDataBuilder::getReferenceUrl(),
      \Trans\Mepay\Gateway\Request\OrderDataBuilder::getOrder($order),
      \Trans\Mepay\Gateway\Request\CustomerDataBuilder::getCustomer($order),
      \Trans\Mepay\Gateway\Request\PaymentSourceDataBuilder::getPaymentSource($methodCode),
      \Trans\Mepay\Gateway\Request\PaymentSourceMethodDataBuilder::getPaymentSourceMethod($methodCode),
      \Trans\Mepay\Gateway\Request\TokenDataBuilder::getCustomerToken($order)
    );
  }

  /**
   * Merge function for builders
   *
   * @param array $result
   * @param array $builder
   * @return array
   */
  protected function merge(array $result, array $builder)
  {
      return array_replace_recursive($result, $builder);
  }
}
