<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 6/9/20
 * Time: 10:07 AM
 */

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetDataToMainOrder
 * @package SM\Checkout\Observer
 */
class QuoteAddressCollect implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * ShippingAssignment constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * set order reference number
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->checkoutSession->getMainOrder() && $this->checkoutSession->getIsMultipleShippingAddresses()) {
            $ShippingAssignment = $observer->getEvent()->getShippingAssignment();
            $ShippingAssignment->setItems($observer->getEvent()->getQuote()->getAllItems());
        }
    }
}
