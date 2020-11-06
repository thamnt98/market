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
namespace Trans\Mepay\Observer\Magento\Sales\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;

class Order implements ObserverInterface
{
  protected $transactionHelper;
  protected $orderRepo;
  public function __construct(
    TransactionHelper $transactionHelper,
    OrderRepositoryInterface $orderRepo
  ) {
    $this->transactionHelper = $transactionHelper;
    $this->orderRepo = $orderRepo;
  }

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    $order= $observer->getData('order');
    $order->setState('new');
    $order->setStatus('pending_payment');
    $this->orderRepo->save($order);
  }
}