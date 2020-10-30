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
namespace Trans\MepayTransmart\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Trans\Mepay\Logger\Logger;
use Trans\Mepay\Gateway\Request\OrderDataBuilder;
use Trans\Sprint\Model\ResourceModel\SprintResponse;

class TransmartOrderDataBuilder extends OrderDataBuilder
{
    /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * @var SprintResponse
   */
  private $sprintResource;

  /**
   * Constructor
   * @param SubjectReader  $subjectReader
   * @param SprintResponse $sprintResource
   * @param Logger         $logger
   */
  public function __construct(
    SubjectReader $subjectReader,
    SprintResponse $sprintResource,
    Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->sprintResource = $sprintResource;
      $this->logger = $logger;
      parent::__construct($subjectReader, $logger);
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
    //required variables
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    $order = $paymentDO->getOrder();
    $items = $this->getOrderItems($order);

    //buid order id
    $orderIncrementId = $order->getOrderIncrementId();
    $refNumber = $this->sprintResource->getReferenceNumber($orderIncrementId);
    return [
      self::ORDER =>[
        self::ID => ($refNumber)? $refNumber : $orderIncrementId,
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