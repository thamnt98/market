<?php 
namespace Trans\MepayTransmart\Plugin\SM\Sales\Model;

use Trans\Mepay\Helper\Data;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class TransmartInvoiceRepository 
{
  protected $orderCollectionFactory;

  public function __construct(OrderCollectionFactory $orderCollectionFactory)
  {
    $this->orderCollectionFactory = $orderCollectionFactory;
  }

  public function beforeGetDataInvoice(\SM\Sales\Model\InvoiceRepository $subject, $customerId, $orderId)
  {
        if(Data::isMegaMethod(Data::getPaymentMethodByOrderId($orderId)))
            $orderId = $this->getMainOrderIdMepay($orderId);

        return[$customerId, $orderId];
  }

  protected function getMainOrderIdMepay($mainOrderId)
    {

      $order = Data::getOrderById($mainOrderId);
      $referenceNumber = $order->getReferenceNumber();
      $collection = $this->orderCollectionFactory->create()
        ->addFieldToFilter('reference_number',['eq'=>$referenceNumber])
        ->addFieldToFilter('parent_order', ['null'=>true])
        ->getFirstItem();
      return $collection->getEntityId();
    }
}