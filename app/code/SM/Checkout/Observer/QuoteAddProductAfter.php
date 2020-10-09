<?php

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteAddProductAfter implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $items = [];
        $parentItem = null;
        $listItem = $observer->getData('items');
        foreach ($listItem as $item) {
            if (!$parentItem ||
                ($parentItem->getProduct()->getTypeId() != 'bundle' && $item->getProduct()->getTypeId() != 'simple')
            ) {
                $parentItem = $item;
            }

            if ($parentItem && $item->getProduct()->getParentProductId()) {
                $item->setParentItem($parentItem);
            }

            $items[] = $item;
        }
        $observer->setData('items', $items);
    }
}
