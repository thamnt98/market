<?php

namespace SM\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;

/**
 * Class SaveVoucherDetail
 * @package SM\Sales\Plugin\Order
 */
class SaveVoucherDetail implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if (!$order->getId()) {
            return $this;
        }

        $detail = $quote->getData("voucher_detail");
        if (!is_null($detail)) {
            $order->setData("voucher_detail", $detail);
        }
        return $this;
    }
}
