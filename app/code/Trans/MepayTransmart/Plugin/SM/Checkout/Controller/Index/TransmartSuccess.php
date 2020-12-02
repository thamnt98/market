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
namespace Trans\MepayTransmart\Plugin\SM\Checkout\Controller\Index;

use Trans\Mepay\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;

class TransmartSuccess 
{
  /**
   * @var OrderRepositoryInterface
   */
  protected $orderRepo;

  /**
   * Constructor
   * @param OrderRepositoryInterface $orderRepo
   */
  public function __construct(OrderRepositoryInterface $orderRepo)
  {
    $this->orderRepo = $orderRepo;
  }

  /**
   * After 
   * @param  \SM\Checkout\Controller\Index\Success $subject
   * @param  string $result
   * @param  int $orderId
   * @return string
   */
  public function afterGetApiResponse(\SM\Checkout\Controller\Index\Success $subject, $result, $orderId)
  {
    $order = $this->orderRepo->get($orderId);
    $method = $order->getPayment()->getMethod();
    if(Data::isMegaMethod($method))
      return $this->getApiResponseMega($order);
    return $result;
  }

  /**
   * Get api response for mega
   * @param  \Magento\Sales\Api\Data\OrderInterface $order
   * @return string
   */
  public function getApiResponseMega($order)
  {
    if (empty($order->getId())) {
            return 'order not found';
    }

     $res = [
        'status'       => true,
        'message'      => 'payment succeed',
        'order_status' => $order->getStatus()
     ];

      if ($order && $order->getStatus() == 'canceled') {
          $res['status'] = false;
          $res['message'] = 'payment failed';
          $res['order_status'] = $order->getStatus();
          $res["error_message"] = __("Sorry, we couldn't process your payment. Please select another payment method.");
      }

      if ($order && $order->getStatus() == 'pending_payment') {
          $res['status'] = true;
          $res['message'] = 'Payment Pending';
          $res['order_status'] = $order->getStatus();
      }

      return json_encode($res);
  }
}