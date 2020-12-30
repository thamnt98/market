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
namespace Trans\MepayTransmart\Controller\Payment;

use Trans\Mepay\Controller\Payment\Callbacks;
use Magento\Framework\Controller\ResultFactory;
use Trans\Mepay\Helper\Data;

class TransmartCallbacks extends Callbacks
{
  /**
   * @var  string
   */
  const PAYMENT_FAILED_MESSAGES = 'Payment process is not success';

  /**
   * @inheritdoc
   */
  public function execute()
  {
    $incrementId = $this->getRequest()->getParam('increment_id');
    if (!empty($incrementId)) {
      return $this->calbacksMobile($incrementId);
    }

    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    if ($this->checkoutSession->getLastRealOrderId()) {
      $orderId = $this->getOrderId();
      $order = $this->transaction->getOrder($orderId);
      if ($this->checkOrderCreated($order)) {
        $txn = $this->transaction->getLastOrderTransaction($orderId);
        if(\is_object($txn)) {
          $txn = $txn->getData();
        }
        if ($txn['txn_type'] == 'authorization') {
          return $resultRedirect->setPath(''); 
        }

        $this->checkoutSession->setIsSucceed(1);
        $resultRedirect->setPath(
          'transcheckout/index/success'
        );

        return $resultRedirect;

      } else {

        $this->checkoutSession->restoreQuote();
        $this->messageManager->addError( __(self::PAYMENT_FAILED_MESSAGES));
        $resultRedirect->setPath('checkout/cart/index');

        return $resultRedirect;

      }
    }

    $resultRedirect->setPath('checkout/cart/index');

    return $resultRedirect;
  }

  /**
   * Callbacks from mobile
   */
  public function calbacksMobile($incrementId)
  {
    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    $order = $this->transaction->getOrderByIncrementId($incrementId);
    if ($orderId = $order->getId()) {

      if ($this->checkOrderCreated($order)) {

        $txn = $this->transaction->getLastOrderTransaction($orderId);
        if ($txn['txn_type'] == 'authorization') {
            return $resultRedirect->setPath('?fromtransmepay=1');
        }

      } else {

        $this->restoreQuoteMobile($order->getQuoteId());

      }

      $resultRedirect->setPath(
          'transcheckout/index/success?orderid='.$orderId
      );

      return $resultRedirect;

    }

    $resultRedirect->setPath('checkout/cart/index');

    return $resultRedirect;
  }

  public function restoreQuoteMobile($quoteId)
  {
    $quoteRepo = Data::getQuoteRepo();
    $quote = $quoteRepo->get($quoteId);
    $quote->setIsActive(1)->setReservedOrderId(null);
    $quoteRepo->save($quote);
  }

}