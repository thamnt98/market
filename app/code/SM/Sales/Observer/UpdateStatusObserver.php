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
use SM\Sales\Api\ParentOrderRepositoryInterface;
use SM\Sales\Model\Order\Updater as OrderUpdater;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class UpdateStatus
 * @package SM\Sales\Observer
 */
class UpdateStatusObserver implements ObserverInterface
{
    /**
     * @var OrderUpdater
     */
    protected $orderUpdater;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * UpdateStatusObserver constructor.
     * @param OrderUpdater $orderUpdater
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        OrderUpdater $orderUpdater,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->orderUpdater = $orderUpdater;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if ($this->scopeConfig->isSetFlag('sm_sale/general/update_main_status_by_event')) {
            /** @var Order $order */
            $order = $observer->getEvent()->getOrder();

            if ($order->getId()
                && $order->getData("is_parent")
                && $order->getStatus() != ParentOrderRepositoryInterface::STATUS_PENDING_PAYMENT) {
                $this->orderUpdater->updateParentOrderStatus($order);
            }
        }
    }
}
