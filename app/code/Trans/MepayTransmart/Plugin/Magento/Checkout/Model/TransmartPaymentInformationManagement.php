<?php 
namespace Trans\MepayTransmart\Plugin\Magento\Checkout\Model;

use Trans\Mepay\Plugin\Magento\Checkout\Model\PaymentInformationManagement;

class TransmartPaymentInformationManagement extends PaymentInformationManagement
{
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

    if (in_array($method, ['trans_mepay_cc', 'trans_mepay_va', 'trans_mepay_qris'])) {
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