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
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Mepay\Model\Config\Config;

class Order implements ObserverInterface
{
  /**
   * @var 
   */
  const ORDER = 'order';

  /**
   * @var 
   */
  protected $transactionHelper;

  /**
   * @var 
   */
  protected $orderRepo;

  /**
   * @var 
   */
  protected $config;

  protected $statusHistory;

  protected $statusHistoryRepo;

  /**
   * Constructor
   * @param TransactionHelper        $transactionHelper 
   * @param OrderRepositoryInterface $orderRepo         
   * @param Config                   $config            
   */
  public function __construct(
    TransactionHelper $transactionHelper,
    OrderRepositoryInterface $orderRepo,
    OrderStatusHistoryInterfaceFactory $statusHistory,
    OrderStatusHistoryRepositoryInterface $statusHistoryRepo,
    Config $config
  ) {
    $this->transactionHelper = $transactionHelper;
    $this->statusHistory = $statusHistory;
    $this->statusHistoryRepo = $statusHistoryRepo;
    $this->orderRepo = $orderRepo;
    $this->config = $config;
  }

  /**
   * Execute
   * @param  \Magento\Framework\Event\Observer $observer 
   * @return void
   */
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    //set order state & status
    $order= $observer->getData(self::ORDER);
    $payment = $order->getPayment();
    $order->setState($this->config->getOrderState($payment->getMethod()));
    $order->setStatus($this->config->getOrderStatus($payment->getMethod()));
    $this->orderRepo->save($order);
    //set order history status
    $statusHistory = $this->statusHistory->create();
    $statusHistory->setParentId($order->getId());
    $statusHistory->setStatus($this->config->getOrderStatus($payment->getMethod()));
    $this->statusHistoryRepo->save($statusHistory);
  }
}