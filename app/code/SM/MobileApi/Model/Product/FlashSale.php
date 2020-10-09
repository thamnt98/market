<?php

namespace SM\MobileApi\Model\Product;

use Magento\CatalogEvent\Model\Category\EventList;
use Magento\CatalogEvent\Model\Event as SaleEvent;
use SM\FlashSale\Model\HistoryFactory;

/**
 * Class FlashSale
 * @package SM\MobileApi\Model\Product
 */
class FlashSale
{
    /**
     * @var EventList
     */
    protected $categoryEventList;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * FlashSale constructor.
     * @param EventList $categoryEventList
     * @param HistoryFactory $historyFactory
     */
    public function __construct(EventList $categoryEventList, HistoryFactory $historyFactory)
    {
        $this->categoryEventList = $categoryEventList;
        $this->historyFactory = $historyFactory;
    }

    public function getFlashSaleAvailableQty($product)
    {
        $event       = $this->getEvent();
        $categoryIds = $product->getCategoryIds();

        if (in_array($event->getData("category_id"), $categoryIds)
            && $product->getTypeId() == "simple"
            && $product->getData("is_flashsale") != 0) {
            $history = $this->historyFactory->create();

            $collection = $history->getCollection()
                ->addFieldToFilter('event_id', $event->getData("event_id"))
                ->addFieldToFilter('item_id', $product->getId());

            $itemTotalBuy = 0;
            foreach ($collection as $historyItem) {
                $itemTotalBuy += $historyItem->getData('item_qty_purchase');
            }
            $availableQty = $product->getData("flashsale_qty") - $itemTotalBuy;
            if ($availableQty <= 0) {
                $availableQty = 0;
            }

            return $availableQty;
        } else {
            return 0;
        }
    }

    protected function getEvent()
    {
        return $this->categoryEventList->getEventCollection()
            ->addFieldToFilter('status', SaleEvent::STATUS_OPEN)
            ->addVisibilityFilter()
            ->getFirstItem();
    }
}
