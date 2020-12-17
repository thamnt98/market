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
    $email = $data['customer']['email'];
    $referenceNumber = $data['order']['id'];

    //init magento object
    $customerRepo = Data::getCustomerRepo();
    $collection = Data::getOrderCollection();

    //check if same reference number order already sending
    $customer = $customerRepo->get($email);
    $collection->addFieldToFilter('customer_id',['eq'=>$customer->getId()]);
    $collection->getSelect()->order('entity_id desc')->limit(1);
    $exist = $collection->getFirstItem()->getData();
    $referenceNumberExist = (isset($exist['reference_number']))? $exist['reference_number'] : null;
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