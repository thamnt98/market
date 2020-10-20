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
use Magento\Quote\Model\Quote;

/**
 * Class SetDataToMainOrder
 * @package SM\Checkout\Observer
 */
class SetDataToMainOrder implements ObserverInterface
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
     * set order reference number
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        //@todo: check multiple order & remove -01 suffix if needed
        $referenceNumber = $this->referenceNumber->generateReferenceNumber($order);
        $order->setReferenceNumber($referenceNumber);

        if (!$quote->isVirtual()) {
            $order->setData(
                \SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD,
                $quote->getData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD)
            );
        }

        $paymentReferenceNumber = $this->referenceNumber->generatePaymentReferenceNumber($order);
        $order->setReferencePaymentNumber($paymentReferenceNumber);
    }
}
