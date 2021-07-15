<?php 
namespace Trans\MepayTransmart\Plugin\Trans\Mepay\Gateway\Http\Client;

use Trans\Mepay\Helper\Data;
use Magento\Payment\Gateway\Http\TransferInterface;

class TransmartConnect
{
  /**
   * Around place order
   * @param  \Trans\Mepay\Gateway\Http\Client\Connect $subject
   * @param  callable  $proceed
   * @param  TransferInterface  $transferObject
   * @return array
   */
  public function aroundPlaceRequest(
    \Trans\Mepay\Gateway\Http\Client\Connect $subject,
    callable $proceed,
    TransferInterface $transferObject
  ){
    //init transfer data
    $data = $transferObject->getBody();
    $exist = [];
    $referenceNumber = 0;
    
    if(isset($data['order'])) {
      $referenceNumber = $data['order']['id'];
    }

    if(isset($data['customer'])) {
      $email = $data['customer']['email'];

      //init magento object
      $customerRepo = Data::getCustomerRepo();
      $collection = Data::getOrderCollection();

      //check if same reference number order already sending
      $customer = $customerRepo->get($email);
      $collection->addFieldToFilter('customer_id',['eq'=>$customer->getId()]);
      $collection->getSelect()->order('entity_id desc')->limit(1);
      $exist = $collection->getFirstItem()->getData();
    }

    $referenceNumberExist = ($exist && isset($exist['reference_number']))? $exist['reference_number'] : null;
    if ($referenceNumber !== $referenceNumberExist) {
      return $proceed($transferObject);
    }

    //get latest order payment response information
    return $this->getPreviousPaymentResponse($exist['entity_id']);
   }

   /**
    * Get previous payment response
    * @param int $orderId
    * @return array
    */
   protected function getPreviousPaymentResponse($orderId)
   {
     $order = Data::getOrderById($orderId);
     $additionalInformation = $order->getPayment()->getAdditionalInformation();
     return [json_encode($additionalInformation['raw_details_info'])];
   }


}