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
namespace Trans\MepayTransmart\Plugin\Magento\Checkout\Model;

use Trans\Mepay\Plugin\Magento\Checkout\Model\PaymentInformationManagement;

class TransmartPaymentInformationManagement extends PaymentInformationManagement
{
  /**
   * After Save Payment Information And Place Order
   * @param  \Magento\Checkout\Model\PaymentInformationManagement $subject
   * @param  $result
   * @return int
   */
  public function afterSavePaymentInformationAndPlaceOrder(
    \Magento\Checkout\Model\PaymentInformationManagement $subject,
    $result
  ){
    $orderId = $result;

    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $orderRepo = $objectManager->create('Magento\Sales\Api\OrderRepositoryInterface');
    $order = $orderRepo->get($orderId);
    $payment = $order->getPayment();

    $method = $payment->getMethod();

    if (in_array($method, ['trans_mepay_allbankccdebit', 'trans_mepay_cc', 'trans_mepay_va', 'trans_mepay_qris', 'trans_mepay_allbank_cc', 'trans_mepay_allbank_debit'])) {
        $resourceCon = $objectManager->create('Magento\Framework\App\ResourceConnection');
        $connection = $resourceCon->getConnection();
        $table = $connection->getTableName('sales_order_status_history');
        $config = $objectManager->create('Trans\Mepay\Model\Config\Config');

        $referenceNumber = $order->getReferenceNumber();
        $collection = $order->getCollection()->addFieldToFilter('reference_number',['eq'=>$referenceNumber]);
        foreach ($collection as $key => $value) {
          $query = "UPDATE `" . $table . "` SET `status`= '".$config->getOrderStatus($method)."' WHERE parent_id = '".$value->getEntityId()."' ";
          //$query = "UPDATE `" . $table . "` SET `status`= 'in_process' ";
          $connection->query($query); 
        }
    }

    return $result;
  }
}
