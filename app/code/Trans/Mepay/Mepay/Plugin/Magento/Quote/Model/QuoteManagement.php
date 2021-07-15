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
namespace Trans\Mepay\Plugin\Magento\Quote\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Helper\Search as SearchHelper;
use Trans\Mepay\Helper\Data;

class QuoteManagement 
{
  /**
   * @var \OrderRepositoryInterface
   */
  protected $orderRepo;

  /**
   * @var \OrderStatusHistoryRepositoryInterface
   */
  protected $orderStatusRepo;

  /**
   * @var \Config
   */
  protected $config;

  /**
   * @var SearchHelper
   */
  protected $searchHelper;

  /**
   * Constructor
   * @param OrderRepositoryInterface $orderRepo
   * @param OrderStatusHistoryRepositoryInterface $orderStatusRepo
   * @param Config $config
   * @param SearchHelper $searchHelper
   */
  public function __construct(
    OrderRepositoryInterface $orderRepo,
    OrderStatusHistoryRepositoryInterface $orderStatusRepo,
    Config $config,
    SearchHelper $searchHelper
  ) {
    $this->orderRepo = $orderRepo;
    $this->orderStatusRepo = $orderStatusRepo;
    $this->config = $config;
    $this->searchHelper = $searchHelper;
  }

  /**
   * After place order method
   * @param  \Magento\Quote\Model\QuoteManagement $subject
   * @param  int $result
   * @param  int $cartId
   * @param  PaymentInterface|null $paymentMethod
   * @return int
   */
  public function afterPlaceOrder(
    \Magento\Quote\Model\QuoteManagement $subject,
    $result,
    $cartId,
    PaymentInterface $paymentMethod = null
  ) {
    $order = $this->orderRepo->get($result);
    $payment = $order->getPayment();
    $method = $payment->getMethod();
    if (Data::isMegaMethod($method)) {
      $this->updateOrderHistoryStatus($order->getId(), $method);
    }

    return $result;
  }

  /**
   * Update order history status
   * @param  int $orderId
   * @param  string $paymentMethodName
   * @return void
   */
  public function updateOrderHistoryStatus($orderId, $paymentMethodName)
  {
    $searchCriteria = $this->searchHelper->getSearchCriteriaSortedBy(
      [OrderStatusHistoryInterface::PARENT_ID => $orderId],
      OrderStatusHistoryInterface::ENTITY_ID,
      'DESC'
    );
    $orderHistories = $this->orderStatusRepo->getList($searchCriteria);
    foreach ($orderHistories as $key => $value) {
      $value->setStatus($this->config->getOrderStatus($paymentMethodName));
      $this->orderStatusRepo->save($value);
    }
  }

}