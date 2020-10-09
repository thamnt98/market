<?php
namespace SM\FlashSale\Model;

use \Magento\CatalogEvent\Model\Event as SaleEvent;
use Magento\Checkout\Exception;
use Magento\Framework\Exception\StateException;

class FlashSaleEvent implements \SM\FlashSale\Api\FlashSaleEventInterface
{

    /**
     * @var \Magento\CatalogEvent\Model\Category\EventList
     */
    protected $categoryEventList;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \SM\MobileApi\Model\Data\Product\LiistFactory
     */
    protected $productListFactory;

    /**
     * @var \SM\MobileApi\Model\Data\Product\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \SM\Category\Model\Catalog\Category
     */
    protected $catalogCategory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $product;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var FlashSaleDateFactory
     */
    protected $flashSaleDate;
    /**
     * FlashSaleEvent constructor.
     * @param \Magento\CatalogEvent\Model\Category\EventList $categoryEventList
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \SM\MobileApi\Model\Data\Product\LiistFactory $listFactory
     * @param HistoryFactory $historyFactory
     * @param \SM\MobileApi\Model\Data\Product\ProductFactory $productFactory
     * @param \SM\Category\Model\Catalog\Category $catalogCategory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \SM\MobileApi\Model\Data\Product\LiistFactory $listFactory,
        \SM\FlashSale\Model\HistoryFactory $historyFactory,
        \SM\MobileApi\Model\Data\Product\ProductFactory $productFactory,
        \SM\Category\Model\Catalog\Category $catalogCategory,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \SM\FlashSale\Model\FlashSaleDateFactory $flashSaleDate
    ) {
        $this->categoryEventList = $categoryEventList;
        $this->jsonHelper = $jsonHelper;
        $this->productCollectionFactory = $productCollectionFactory ;
        $this->historyFactory = $historyFactory;
        $this->productFactory = $productFactory;
        $this->catalogCategory = $catalogCategory;
        $this->productListFactory = $listFactory;
        $this->product = $product;
        $this->timezone = $timezone;
        $this->jsonHelper = $jsonHelper;
        $this->flashSaleDate = $flashSaleDate;
    }

    /**
     * Get open flash sale event
     */
    public function getEvent()
    {
        $event = $this->getOpenEvent();

        if ($event->getData() && $event->getData("category_id")) {
            $data = [
                "event_id" => $event->getData("event_id"),
                "category_id" => $event->getData("category_id"),
                "date_start" => $event->getData("date_start"),
                "date_end" => $event->getData("date_end"),
                "status" => $event->getData("status")
            ];
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Get all product, category, end date in flash sale event
     * @param int $limit
     * @param int $p
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getEventProduct($limit = 12, $p = 1)
    {
        $event = $this->getOpenEvent();
        $category_id = $event->getData("category_id");
        if($category_id && $category_id != "") {
            $dateEndConverted = $this->timezone->date(new \DateTime($event->getData("date_end")))->format('Y-m-d H:i:s');
            $this->catalogCategory->init($category_id);
            /* @var $result \SM\MobileApi\Api\Data\Product\ListInterface */
            $result = $this->productListFactory->create();
            $result->setCategoryId($category_id);
            $result->setFilters($this->catalogCategory->getFilters());
            $result->setToolbarInfo($this->catalogCategory->getToolbarInfo());
            $result->setProducts($this->catalogCategory->getProductsV2());
            $result->setTotalProduct($this->catalogCategory->getTotalProductByCategoryId($category_id));
            $result->setEndTime($event->getData("date_end"));
            $result->setFlashImage($event->getData("flash_sale_image"));
            $result->setEndTimeConverted($dateEndConverted);
            return $result;
        }else{
            $message = __("There is no open event");
            throw new \Magento\Framework\Webapi\Exception($message,99);
        }
    }

    /**
     * Get all product in flash sale event
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getEventProductOnly()
    {
        $event = $this->getOpenEvent();
        $category_id = $event->getData("category_id");
        if($category_id && $category_id != "") {
            $this->catalogCategory->init($category_id);
            /* @var $result \SM\MobileApi\Api\Data\Product\ListInterface */
            $result = $this->productListFactory->create();
            $result->setProducts($this->catalogCategory->getProductsV2());
            $result->setTotalProduct($this->catalogCategory->getTotalProductByCategoryId($category_id));
            return $result;
        }else{
            $message = __("There is no open event");
            throw new \Magento\Framework\Webapi\Exception($message,99);
        }
    }

    /**
     * @return \SM\FlashSale\Api\Data\FlashSaleDateInterface
     */
    public function getEventEndTime()
    {
        $event = $this->getOpenEvent();
        if ($event->getData() && $event->getData("category_id")) {
            $dateStartConverted = $this->timezone->date(new \DateTime($event->getData("date_start")))->format('Y-m-d H:i:s');
            $dateEndConverted = $this->timezone->date(new \DateTime($event->getData("date_end")))->format('Y-m-d H:i:s');
            /* @var $result \SM\FlashSale\Api\Data\FlashSaleDateInterface */
            $result = $this->flashSaleDate->create();
            $result->setDateStart($event->getData("date_start"));
            $result->setDateEnd($event->getData("date_end"));
            $result->setDateStartConverted($dateStartConverted);
            $result->setDateEndConverted($dateEndConverted);
            return $result;
        }
    }

    /**
     * @param int $productId
     * @return \SM\FlashSale\Api\Data\FlashSaleDateInterface
     */
    public function getEventEndTimeByProduct($productId)
    {
        $event = $this->getOpenEvent();
        $product = $this->product->create()->load($productId);
        if ($event->getData() && $event->getData("category_id") && $product->getId()) {
            $categoryId = $event->getData("category_id");
            if ($categoryId) {
                $productCat = $product->getCategoryIds();
                if (in_array($categoryId, $productCat)) {
                    $dateStartConverted = $this->timezone->date(new \DateTime($event->getData("date_start")))->format('Y-m-d H:i:s');
                    $dateEndConverted = $this->timezone->date(new \DateTime($event->getData("date_end")))->format('Y-m-d H:i:s');
                    /* @var $result \SM\FlashSale\Api\Data\FlashSaleDateInterface */
                    $result = $this->flashSaleDate->create();
                    $result->setDateStart($event->getData("date_start"));
                    $result->setDateEnd($event->getData("date_end"));
                    $result->setDateStartConverted($dateStartConverted);
                    $result->setDateEndConverted($dateEndConverted);
                    return $result;
                }
            }
        }
    }

    /**
     * Get current open event
     * @return \Magento\Framework\DataObject
     */
    public function getOpenEvent()
    {
        return $this->categoryEventList->getEventCollection()
            ->addFieldToFilter('status', SaleEvent::STATUS_OPEN)
            ->addVisibilityFilter()
            ->getFirstItem();
    }
}
