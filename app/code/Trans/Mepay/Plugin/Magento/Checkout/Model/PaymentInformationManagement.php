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
namespace Trans\Mepay\Plugin\Magento\Checkout\Model;

use Trans\Mepay\Helper\Data;

class PaymentInformationManagement
{
  public function afterSavePaymentInformationAndPlaceOrder(
    \Magento\Checkout\Model\PaymentInformationManagement $subject,
    $result
  ){
    $orderId = $result;

    $orderRepo = Data::getClassInstance('Magento\Sales\Api\OrderRepositoryInterface');
    $order = $orderRepo->get($orderId);
    $payment = $order->getPayment();
    $method = $payment->getMethod();

    if (Data::isMegaMethod($method)) {
        $connection = Data::getConnection();
        $table = $connection->getTableName('sales_order_status_history');
        $config = Data::getClassInstance('Trans\Mepay\Model\Config\Config');
        $query = "UPDATE `" . $table . "` SET `status`= '".$config->getOrderStatus($method)."' WHERE parent_id = '".$orderId."' ";
        $connection->query($query);
    }

    return $result;
  }
}