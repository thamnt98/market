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
namespace Trans\Mepay\Observer\Magento\Checkout\Model;

use Magento\Framework\Event\ObserverInterface;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;

class Session implements ObserverInterface
{
  /**
   * @var TransactionHelper
   */
  protected $transactionHelper;

  /**
   * Constructor method
   * @param TransactionHelper $transactionHelper
   */
  public function __construct(TransactionHelper $transactionHelper)
  {
    $this->transactionHelper = $transactionHelper;
  }

  /**
   * Execute
   * @param  \Magento\Framework\Event\Observer $observer
   * @return void
   */
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    if ($this->isNeedRestore($session->getLastRealOrderId())) {
      $session = $observer->getData('checkout_session');
      $session->restoreQuote();
      $this->closeVoidTransaction($session->getLastRealOrderId());
    }
    
  }

  /**
   * Is need restore quote
   * @param int $orderId
   * @return boolean
   */
  public function isNeedRestore($orderId)
  {
    $collection = $this->transactionHelper->getVoidTransaction($orderId);
    if ($collection->getSize())
      return true;
    return false;
  }

  /**
   * Close void transaction
   * @param int $orderId
   * @return void
   */
  public function closeVoidTransaction($orderId)
  {
    $collection = $this->transactionHelper->getVoidTransaction($orderId);
    foreach ($collection as $key => $value) {
      $id = $value->getId();
      $txn = $this->transactionHelper->getTransaction($id);
      $txn->close();
    }
  }
}