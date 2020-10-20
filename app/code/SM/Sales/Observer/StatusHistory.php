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
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status\HistoryFactory;

/**
 * Class StatusHistory
 * @package SM\Sales\Observer
 */
class StatusHistory implements ObserverInterface
{
    /**
     * @var OrderStatusHistoryRepositoryInterface
     */
    protected $statusHistoryRepository;
    /**
     * @var HistoryFactory
     */
    protected $statusHistoryFactory;

    /**
     * StatusHistory constructor.
     * @param HistoryFactory $statusHistoryFactory
     * @param OrderStatusHistoryRepositoryInterface $statusHistoryRepository
     */
    public function __construct(
        HistoryFactory $statusHistoryFactory,
        OrderStatusHistoryRepositoryInterface $statusHistoryRepository
    ) {
        $this->statusHistoryFactory = $statusHistoryFactory;
        $this->statusHistoryRepository = $statusHistoryRepository;
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

        $statusHistory = $this->statusHistoryFactory->create();
        $statusHistory->setStatus($order->getStatus());
        $statusHistory->setParentId($order->getId());
        $statusHistory->setEntityName($order->getEntityType());
        $this->statusHistoryRepository->save($statusHistory);

        return $this;
    }
}
