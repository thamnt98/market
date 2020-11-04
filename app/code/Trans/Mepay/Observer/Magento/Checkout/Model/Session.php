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
  protected $transactionHelper;
  public function __construct(TransactionHelper $transactionHelper)
  {
    $this->transactionHelper = $transactionHelper;
  }
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    if ($this->isNeedRestore($session->getLastRealOrderId())) {
      $session = $observer->getData('checkout_session');
      $session->restoreQuote();
      $this->closeVoidTransaction($session->getLastRealOrderId());
    }
    
  }

  public function isNeedRestore($orderId)
  {
    $collection = $this->transactionHelper->getVoidTransaction($orderId);
    if ($collection->getSize())
      return true;
    return false;
  }

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