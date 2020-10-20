<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 6/9/20
 * Time: 11:08 AM
 */

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetDataToInvoice
 * @package SM\Checkout\Observer
 */
class SetDataToInvoice implements ObserverInterface
{
    /**
     * @var \SM\Checkout\Helper\OrderReferenceNumber
     */
    protected $referenceNumber;

    /**
     * SetDataToMainOrder constructor.
     * @param \SM\Checkout\Helper\OrderReferenceNumber $referenceNumber
     */
    public function __construct(
        \SM\Checkout\Helper\OrderReferenceNumber $referenceNumber
    ) {
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * set invoice reference number
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getInvoice()->getOrder();
        $referenceNumber = $this->referenceNumber->generateInvoiceReferenceNumber($order);
        $order->setReferenceInvoiceNumber($referenceNumber);
    }
}
