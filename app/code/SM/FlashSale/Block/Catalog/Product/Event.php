<?php

namespace SM\FlashSale\Block\Catalog\Product;

use Magento\Framework\View\Element\Template;
use \Magento\CatalogEvent\Model\Event as SaleEvent;


class Event extends \Magento\Framework\View\Element\Template{

    /**
     * @var \Magento\CatalogEvent\Model\Category\EventList
     */
    protected $categoryEventList;

    /**
     * @var
     */
    protected $_statuses;

    /**
     * @var \Magento\CatalogEvent\Model\DateResolver
     */
    protected $dateResolver;

    /**
     * Event constructor.
     * @param Template\Context $context
     * @param \Magento\CatalogEvent\Model\Category\EventList $categoryEventList
     * @param \Magento\CatalogEvent\Model\DateResolver $dateResolver
     * @param array $data
     */

    protected $_registry;

    public function __construct(Template\Context $context,
                                \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
                                \Magento\CatalogEvent\Model\DateResolver $dateResolver,
                                \Magento\Framework\Registry $registry,
                                array $data = [])
    {
        $this->categoryEventList = $categoryEventList;
        parent::__construct($context, $data);
        $this->dateResolver = $dateResolver;
        $this->categoryEventList = $categoryEventList;
        $this->_registry = $registry;
    }

    /**
     * Pseudo-constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_statuses = [
            \Magento\CatalogEvent\Model\Event::STATUS_UPCOMING => __('Coming Soon'),
            \Magento\CatalogEvent\Model\Event::STATUS_OPEN => __('Sale Ends In'),
            \Magento\CatalogEvent\Model\Event::STATUS_CLOSED => __('Closed'),
        ];
    }

    public function getEvent(){
        $event = $this->categoryEventList->getEventCollection()
            ->addFieldToFilter('status',SaleEvent::STATUS_OPEN)->addVisibilityFilter()->getFirstItem();
        return $event;
    }

    public function getCurrentProduct(){
        return $this->_registry->registry('current_product');
    }

    public function checkProductFlashSale(){
        $product = $this->getCurrentProduct();
        if($product){
            $event = $this->getEvent();
            if($event){
                $categoryId = $event->getData("category_id");
                if($categoryId) {
                    $productCat = $product->getCategoryIds();
                    if (in_array($categoryId, $productCat)) {
                        return true;
                    } else {
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Return catalog event status text
     *
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return string
     */
    public function getStatusText($event)
    {
        if (isset($this->_statuses[$event->getStatus()])) {
            return $this->_statuses[$event->getStatus()];
        }

        return '';
    }

    /**
     * Return event formatted time
     *
     * @param string $type (start, end)
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return string
     */
    public function getEventTime($type, $event)
    {
        return $this->_getEventDate($type, $event, \IntlDateFormatter::NONE, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Return event formatted date
     *
     * @param string $type (start, end)
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return string
     */
    public function getEventDate($type, $event)
    {
        return $this->_getEventDate($type, $event, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
    }

    /**
     * Return event formatted datetime
     *
     * @param string $type (start, end)
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return string
     */
    public function getEventDateTime($type, $event)
    {
        return $this->_getEventDate($type, $event);
    }

    /**
     * Return event date by in store timezone, with specified format
     *
     * @param string $type (start, end)
     * @param \Magento\CatalogEvent\Model\Event $event
     * @param int $dateType
     * @param int $timeType
     * @return string
     */
    protected function _getEventDate(
        $type,
        $event,
        $dateType = \IntlDateFormatter::MEDIUM,
        $timeType = \IntlDateFormatter::MEDIUM
    ) {
        $dateString = $event->getData('date_' . $type);
        $date = new \DateTime($dateString, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone($this->dateResolver->getConfigStoreTimezone()));

        return $this->_localeDate->formatDateTime($date, $dateType, $timeType);
    }

    /**
     * Return event time to close in seconds
     *
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return int
     */
    public function getSecondsToClose($event)
    {
        $endTime = strtotime($event->getDateEnd());
        $currentTime = $this->_localeDate->scopeTimeStamp();

        return $endTime - $currentTime;
    }

    /**
     * Return event finish UTC timestamp
     *
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return int
     */
    public function getEndTimeUTC($event)
    {
        return strtotime($event->getData('date_end'));
    }

}