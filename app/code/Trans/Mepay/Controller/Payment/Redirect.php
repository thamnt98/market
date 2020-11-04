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
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\ProductFactory;
use Trans\Mepay\Helper\Payment\Transaction;
use Trans\Mepay\Helper\Response\Response;

class Redirect extends AbstractAction
{
  /**
   * @var ResultFactory
   */
  protected $resultFactory;

  /**
   * @var ResultFactory
   */
  protected $url;

  /**
   * @var ResultFactory
   */
  protected $quoteFactory;

  /**
   * @var ResultFactory
   */
  protected $productFactory;

  /**
   * @var ResultFactory
   */
  protected $cart;

  /**
   * Constructor
   * @param Context        $context
   * @param Session        $checkoutSession
   * @param Transaction    $transaction
   * @param Response       $response
   * @param ResultFactory  $resultFactory
   * @param UrlInterface   $url
   * @param QuoteFactory   $quoteFactory
   * @param ProductFactory $productFactory
   * @param Cart           $cart
   */
  public function __construct(
    Context $context,
    Session $checkoutSession,
    Transaction $transaction,
    Response $response,
    ResultFactory $resultFactory,
    UrlInterface $url,
    QuoteFactory $quoteFactory,
    ProductFactory $productFactory,
    Cart $cart
  ) {
    $this->resultFactory = $resultFactory;
    $this->url = $url;
    $this->quoteFactory = $quoteFactory;
    $this->productFactory = $productFactory;
    $this->cart = $cart;
    
    parent::__construct($context, $checkoutSession, $transaction, $response);
  }

  /**
   * Execute method
   * @return ResultInterface
   */
  public function execute()
  {
    $redirectUrl = $this->getRedirectUrl();
    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    $resultRedirect->setUrl($redirectUrl);
    return $resultRedirect;
  }

  /**
   * Get redirect url
   * @return string
   */
  protected function getRedirectUrl()
  {
    try {
      $orderId = $this->getOrderId();
      $order = $this->transaction->getOrder($orderId);
      $additionalInfo = $this->transaction->getOrder($orderId)->getPayment()->getAdditionalInformation();
      $info = $this->response->extract($additionalInfo);
      return $info[$this->transaction::CHECKOUT_URL];
    } catch (\Exception $e) {
      $this->cancelOrder($order);
      //$this->returnQuote($order->getQuoteId());
      $this->messageManager->addError( __($info['message']));
      return $this->url->getUrl('checkout/cart/index');
    }
  }

  /**
   * Cancel order
   * @param  Magento\Sales\Api\Data\OrderInterface $order
   * @return void
   */
  public function cancelOrder($order)
  {
    $order->setState(Order::STATE_CANCELED);
    $order->setStatus(Order::STATE_CANCELED);
    $order->cancel();
    $this->transaction->saveOrder($order);
    $this->checkoutSession->restoreQuote();
  }

  /**
   * Return quoute
   * @param  int $quoteId
   * @return void
   */
  public function returnQuote($quoteId)
  {
    if ($quoteId) {
        $quote = $this->quoteFactory->create()->load($quoteId);
        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
          $productId = $item->getProductId();
          $_product = $this->productFactory->create()->load($productId);
          $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
          $info = $options['info_buyRequest'];
          $request1 = new \Magento\Framework\DataObject();
          $request1->setData($info);
          $this->cart->addProduct($_product, $request1);
        }
        $this->cart->save();
    }
  }

  public function setErrorMessage($message)
  {
    $this->messageManager->addError( __($message));
  }
}
