<?php

namespace SM\FlashSale\Observer;

class ProductEventApplier extends \Magento\CatalogEvent\Observer\ProductEventApplier{

    /**
     * Applies event to product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function applyEventToProduct($product)
    {
        if ($product && !$product->hasEvent()) {
            // use event category or product
            if ($product->hasData('cat_index_position') && $product->getCategory()) {
                $catalogEvent = $product->getCategory()->getEvent();
            } else {
                $catalogEvent = $this->getProductEvent($product);
            }
            $product->setEvent($catalogEvent);
        }
        return $this;
    }

    /**
     * Get event for product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogEvent\Model\Event|false
     */
    protected function getProductEvent($product)
    {
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            return false;
        }

        $categoryIds = $product->getCategoryIds();

        $event = false;
        $noOpenEvent = false;
        $eventCount = 0;
        foreach ($categoryIds as $categoryId) {
            $categoryEvent = $this->categoryEventList->getEventInStore($categoryId);
            if ($categoryEvent === false || $categoryEvent === null) {
                $noOpenEvent = $categoryEvent;
            } elseif ($categoryEvent->getStatus() == \Magento\CatalogEvent\Model\Event::STATUS_OPEN) {
                $event = $categoryEvent;
            } else {
                $noOpenEvent = $categoryEvent;
            }
            $eventCount++;
        }

        if ($eventCount > 1) {
            $product->setEventNoTicker(true);
        }

        return $event ? $event : $noOpenEvent;
    }
}