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
namespace Trans\Mepay\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Trans\Mepay\Helper\Payment\Transaction;
use Trans\Mepay\Helper\Response\Response;

class Callbacks extends AbstractAction 
{
  /**
   * @var ResultFactory
   */
  protected $resultFactory;

  /**
   * Constructor
   * @param Context       $context
   * @param Session       $checkoutSession
   * @param ResultFactory $resultFactory
   * @param Transaction   $transaction
   * @param Response      $response
   */
  public function __construct(
    Context $context,
    Session $checkoutSession,
    ResultFactory $resultFactory,
    Transaction $transaction,
    Response $response
  ) {
    $this->resultFactory = $resultFactory;
    parent::__construct($context, $checkoutSession, $transaction, $response);
  }

  /**
   * Execute
   * @return ResultInterface
   */
  public function execute()
  {
    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    if ($this->checkoutSession->getLastRealOrderId()) {
      $orderId = $this->getOrderId();
      $order = $this->transaction->getOrder($orderId);
      if ($this->checkOrderCreated($order)) {
        $resultRedirect->setPath('checkout/onepage/success');
        return $resultRedirect;
      } else {
        $resultRedirect->setPath('checkout/onepage/failure');
        return $resultRedirect;
      }
    }
    $resultRedirect->setPath('checkout/cart/index');
    return $resultRedirect;
  }

  /**
   * Check created order
   * @param  $order
   * @return boolean
   */
  public function checkOrderCreated($order)
  {
    $txnColl = $this->transaction->getLastOrderTransaction($order->getId());
    if ($txnColl->getTxnType() == \Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID)
      return false;
    if ($order->getStatus() == \Magento\Sales\Model\Order::STATE_CANCELED)
      return false;
    return true;
  }
}