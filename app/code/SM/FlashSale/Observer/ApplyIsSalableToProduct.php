<?php

namespace SM\FlashSale\Observer;

class ApplyIsSalableToProduct extends \Magento\CatalogEvent\Observer\ApplyIsSalableToProduct{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent()->getProduct()->getEvent();
//        if ($event && in_array($event->getStatus(), [Event::STATUS_CLOSED, Event::STATUS_UPCOMING])) {
//            $observer->getEvent()->getSalable()->setIsSalable(false);
//        }
        return $this;
    }
}