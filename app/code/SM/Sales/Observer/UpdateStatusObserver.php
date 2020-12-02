<?php
/**
 * @category Magento
 * @package SM\Sales\Observer
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use SM\Sales\Model\Order\Updater as OrderUpdater;

/**
 * Class UpdateStatus
 * @package SM\Sales\Observer
 */
class UpdateStatusObserver implements ObserverInterface
{
    protected $orderUpdater;

    /**
     * UpdateStatusObserver constructor.
     * @param OrderUpdater $orderUpdater
     */
    public function __construct(
        OrderUpdater $orderUpdater
    ) {
        $this->orderUpdater = $orderUpdater;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if (!$order->getId()) {
            return $this;
        }

        $this->orderUpdater->updateParentOrderStatus($order);
        return $this;
    }
}
