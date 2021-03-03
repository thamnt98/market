<?php

namespace SM\Checkout\Observer\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use SM\Checkout\Model\ResourceModel\ConnectionDB;

/**
 * Class SaveAfter
 * @package SM\Checkout\Observer
 */
class SaveAfter implements ObserverInterface
{

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var ConnectionDB
     */
    protected $connectionDB;

    /**
     * SaveAfter constructor.
     * @param OrderSender $orderSender
     * @param ConnectionDB $connectionDB
     */
    public function __construct(
        OrderSender $orderSender,
        ConnectionDB $connectionDB
    ) {
        $this->orderSender = $orderSender;
        $this->connectionDB = $connectionDB;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        $status = $order->getStatus();
        if ($order->getData('is_parent') &&
            $status == 'in_process' &&
            $status !== $order->getOrigData(\Magento\Sales\Model\Order::STATUS) &&
            $order->getData('send_order_confirmation') != 1
        ) {
            $this->orderSender->send($order);
            $this->connectionDB->updateSendOrderConfirmation($order->getId());
        }
    }
}
