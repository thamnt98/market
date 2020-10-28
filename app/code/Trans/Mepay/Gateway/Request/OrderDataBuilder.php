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

class OrderDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const ORDER = 'order';

  /**
   * @var string
   */
  const ID = 'id';

  /**
   * @var string
   */
  const ITEMS = 'items';

  /**
   * @var string
   */
  const NAME = 'name';

  /**
   * @var string
   */
  const QUANTITY = 'quantity';

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
    $items = $this->getOrderItems($order);
    return [
      self::ORDER =>[
        self::ID => $order->getOrderIncrementId(),
        self::ITEMS => $items
      ]
    ];
  }

  /**
   * Get order items
   *
   * @param  \Magento\Payment\Gateway\Data\OrderAdapterInterface $order
   * @return Array
   */
  public function getOrderItems($order)
  {
    $items = [];
    foreach ($order->getItems() as $key => $value) {
      $items[] = [
        self::NAME => $value->getName(),
        self::QUANTITY => $value->getQtyOrdered(),
        self::AMOUNT => (int) $value->getPrice()
      ];
    }
    return $items;
  }

  /**
   * Get order
   * @param  \Magento\Sales\Api\Data\OrderInterface $order
   * @return array
   */
  public function getOrder($order)
  {
    $items = $this->getOrderItems($order);
    return [
      self::ORDER =>[
        self::ID => $order->getOrderIncrementId(),
        self::ITEMS => $items
      ]
    ];
  }
}
