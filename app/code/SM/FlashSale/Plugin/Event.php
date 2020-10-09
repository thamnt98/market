<?php

namespace SM\FlashSale\Plugin;
/**
 * Class Event
 * @package SM\FlashSale\Plugin
 */
class Event
{
    /**
     * @var \Magento\CatalogEvent\Model\Category\EventList
     */
    protected $categoryEventList;
    protected $date;

    /**
     * Event constructor.
     * @param \Magento\CatalogEvent\Model\Category\EventList $categoryEventList
     */
    public function __construct(\Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
                                \Magento\Framework\Stdlib\DateTime\DateTime $date)
    {
        $this->date = $date;
        $this->categoryEventList = $categoryEventList;
    }

    /**
     * @param \Magento\CatalogEvent\Model\Event $subject
     * @param \Closure $proceed
     * @return array|mixed
     */
    public function aroundValidate(
        \Magento\CatalogEvent\Model\Event $subject,
        \Closure $proceed
    ){
        $dateStartUnixTime = strtotime($subject->getData('date_start'));
        $dateEndUnixTime = strtotime($subject->getData('date_end'));
        if($dateEndUnixTime < $dateStartUnixTime){
            return [__('Please make sure the end date follows the start date.')];
        }

        if($subject->getData('event_id')){
            $eventCollection = $this->categoryEventList->getEventCollection()
                ->addVisibilityFilter()->addFieldToFilter('event_id',['neq' => $subject->getData('event_id')]);
        }else{
            $eventCollection = $this->categoryEventList->getEventCollection()
                ->addVisibilityFilter();
        }

        foreach ($eventCollection as $event){
            if($event->getData('status') != $subject::STATUS_CLOSED) {
                if ($dateStartUnixTime >= strtotime($event->getData('date_start')) && $dateStartUnixTime <= strtotime($event->getData('date_end'))) {
                    return [__('Same time range with this event. ID: %1', $event->getData('event_id'))];

                } else if ($dateStartUnixTime < strtotime($event->getData('date_start'))) {
                    if ($dateEndUnixTime >= strtotime($event->getData('date_start'))) {
                        return [__('Same time range with this event. ID: %1', $event->getData('event_id'))];
                    }
                }
            }

        }
        return $proceed();
    }
}