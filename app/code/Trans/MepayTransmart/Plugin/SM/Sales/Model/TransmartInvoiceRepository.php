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
namespace Trans\MepayTransmart\Plugin\SM\Sales\Model;

use Trans\Mepay\Helper\Data;
use SM\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class TransmartInvoiceRepository 
{
  /**
   * @var OrderCollectionFactory
   */
  protected $orderCollectionFactory;

  /**
   * Constructor
   * @param OrderCollectionFactory $orderCollectionFactory
   */
  public function __construct(OrderCollectionFactory $orderCollectionFactory)
  {
    $this->orderCollectionFactory = $orderCollectionFactory;
  }

  /**
   * Before Get Data
   * @param  \SM\Sales\Model\InvoiceRepository $subject
   * @param  int  $customerId
   * @param  int $orderId 
   * @return array
   */
  public function beforeGetDataInvoice(\SM\Sales\Model\InvoiceRepository $subject, $customerId, $orderId)
  {
        if(Data::isMegaMethod(Data::getPaymentMethodByOrderId($orderId)))
            $orderId = $this->getMainOrderIdMepay($orderId);

        return[$customerId, $orderId];
  }

  /**
   * Get Main Order Id Mepay
   * @param  int $mainOrderId
   * @return int
   */
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