<?php

namespace SM\FlashSale\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Quantity extends AbstractHelper
{
    /**
     * @var \Magento\CatalogEvent\Model\Category\EventList
     */
    private $categoryEventList;
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function __construct(
        Context $context,
        \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->categoryEventList = $categoryEventList;
        $this->categoryRepository = $categoryRepository;
        $this->url = $url;
        $this->request = $request;
    }

    public function getEvent()
    {
        $event = $this->categoryEventList->getEventCollection()
            ->addFieldToFilter('status', \Magento\CatalogEvent\Model\Event::STATUS_OPEN)->addVisibilityFilter()->getFirstItem();
        return $event;
    }

    public function getEventCategory()
    {
        $event = $this->getEvent();
        $eventCategoryId = $event->getData("category_id");
        if ($eventCategoryId == null) {
            return null;
        }

        $timeStart = (new \DateTime($event->getDateStart()))->getTimestamp();
        // Date already in gmt, no conversion
        $timeEnd = (new \DateTime($event->getDateEnd()))->getTimestamp();
        // Date already in gmt, no conversion
        $timeNow = gmdate('U');
        if($timeStart <= $timeNow && $timeEnd >= $timeNow) {
            $category = $this->categoryRepository->get($eventCategoryId);
            return $category;
        }
        else{
            return null;
        }
    }

    public function getFlashsaleUrl()
    {
        return $this->url->getUrl('flashsalehistory/index/index');
    }

    public function isCartPage()
    {
        $currentUrl = $this->request->getFullActionName();
        $isCartPage = false;
        if ($currentUrl == 'checkout_cart_index') {
            $isCartPage = true;
        }
        return $isCartPage;
    }
}
