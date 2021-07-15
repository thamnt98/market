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
namespace Trans\MepayTransmart\Plugin\Magento\Quote\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Helper\Search as SearchHelper;
use Trans\Mepay\Helper\Data;
use Trans\Mepay\Plugin\Magento\Quote\Model\QuoteManagement;

class TransmartQuoteManagement extends QuoteManagement 
{
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
    parent::__construct(
      $orderRepo,
      $orderStatusRepo,
      $config,
      $searchHelper
    );
  }

    /**
   * Update order history status
   * @param  int $orderId
   * @param  string $paymentMethodName
   * @return void
   */
  public function updateOrderHistoryStatus($orderId, $paymentMethodName)
  {
    $order = $this->orderRepo->get($orderId);
    $collection = $order->getCollection()->addFieldToFilter('reference_number', $order->getReferenceNumber());
    foreach ($collection as $key => $value) {
      $searchCriteria = $this->searchHelper->getSearchCriteriaSortedBy(
        [OrderStatusHistoryInterface::PARENT_ID => $value->getEntityId()],
        OrderStatusHistoryInterface::ENTITY_ID,
        'DESC'
      );
      $orderHistories = $this->orderStatusRepo->getList($searchCriteria);
      foreach ($orderHistories as $index => $data) {
        $data->setStatus($this->config->getOrderStatus($paymentMethodName));
        $this->orderStatusRepo->save($data);
      }
    }
  }
}