<?php

namespace SM\FlashSale\Cron;

use \Magento\CatalogEvent\Model\Event;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class StatusChecker extends \Magento\CatalogEvent\Cron\StatusChecker{

    protected $cacheTypeList;
    protected $cacheFrontendPool;

    public function __construct(\Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
                                TypeListInterface $cacheTypeList,
                                Pool $cacheFrontendPool)
    {
        parent::__construct($categoryEventList);
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute()
    {
        $eventCollection = $this->categoryEventList->getEventCollection()->addVisibilityFilter();
        $countChange = 0;
        /** @var \Magento\CatalogEvent\Model\Event $event */
        foreach ($eventCollection as $event) {
            if ($event->getDateStart() && $event->getDateEnd()) {
                $timeStart = (new \DateTime($event->getDateStart()))->getTimestamp();
                // Date already in gmt, no conversion
                $timeEnd = (new \DateTime($event->getDateEnd()))->getTimestamp();
                // Date already in gmt, no conversion
                $timeNow = gmdate('U');
                if ($timeStart <= $timeNow && $timeEnd >= $timeNow && $event->getStatus() == Event::STATUS_UPCOMING) {
                    $event->setStatus(Event::STATUS_OPEN);
                    $event->save();
                    $countChange++;
                } elseif ($timeNow > $timeEnd && $event->getStatus() == Event::STATUS_OPEN) {
                    $event->setStatus(Event::STATUS_CLOSED);
                    $event->save();
                    $countChange++;
                }
            }
        }

        if($countChange > 0) {
            $_types = [
                'block_html',
                'full_page'
            ];

            foreach ($_types as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            foreach ($this->cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
        }
    }
}