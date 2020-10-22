<?php
/**
 * Class PaymentSuccessUpdateAfter
 * @package SM\FlashSale\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\FlashSale\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\FlashSale\Model\HistoryFactory;
use SM\FlashSale\Model\ResourceModel\History;
use SM\FlashSale\Model\ResourceModel\History\CollectionFactory;

class PaymentSuccessUpdateAfter implements ObserverInterface
{

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionHistoryFactory;

    /**
     * @var History
     */
    private $resourceHistory;

    /**
     * PaymentSuccessUpdateAfter constructor.
     * @param HistoryFactory $historyFactory
     * @param History $resourceHistory
     * @param CollectionFactory $collectionHistoryFactory
     */
    public function __construct(
        HistoryFactory $historyFactory,
        History $resourceHistory,
        CollectionFactory $collectionHistoryFactory
    ) {
        $this->historyFactory = $historyFactory;
        $this->resourceHistory = $resourceHistory;
        $this->collectionHistoryFactory = $collectionHistoryFactory;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order $order
         * @var \Magento\Sales\Model\Order\Item $item
         */
        $order = $observer->getEvent()->getOrder();

        foreach ($order->getAllVisibleItems() as $item) {
            $historyExist =  $this->collectionHistoryFactory->create()
                ->addFieldToFilter('event_id', $item->getEventId())
                ->addFieldToFilter('item_id', $item->getProductId())
                ->addFieldToFilter('customer_id', $order->getCustomerId())
                ->getFirstItem();

            if ($historyExist->getId()) {
                $historyExist->setData(
                    'item_qty_purchase',
                    $historyExist->getData('item_qty_purchase') + $item->getQtyOrdered()
                );
                $this->resourceHistory->save($historyExist);
            } elseif($item->getEventId()) {
                $history = $this->historyFactory->create();
                $history->setData('event_id', $item->getEventId());
                $history->setData('item_id', $item->getProductId());
                $history->setData('customer_id', $order->getCustomerId());
                $history->setData('item_qty_purchase', $item->getQtyOrdered());
                $this->resourceHistory->save($history);
            }
        }
    }
}
