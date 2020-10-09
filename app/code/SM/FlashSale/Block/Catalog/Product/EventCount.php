<?php

declare(strict_types=1);

namespace SM\FlashSale\Block\Catalog\Product;

/**
 * Class Iteminfo
 * @package SM\Catalog\Block\Product\ProductList\Item\AddTo
 */
class EventCount extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    const PRODUCT_CONFIGURABLE = 'configurable';
    const PRODUCT_BUNDLE = 'bundle';
    const PRODUCT_GROUPED = 'grouped';
    const PRODUCT_SIMPLE = 'simple';

    protected $categoryEventList;
    protected $historyFactory;
    protected $_registry;

    /**
     * EventCount constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\CatalogEvent\Model\Category\EventList $categoryEventList
     * @param \SM\FlashSale\Model\HistoryFactory $historyFactory
     * @param array $data
     */
    public function __construct(\Magento\Catalog\Block\Product\Context $context,
                                \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
                                \SM\FlashSale\Model\HistoryFactory $historyFactory,
                                \Magento\Framework\Registry $registry,
                                array $data = [])
    {
        parent::__construct($context, $data);
        $this->historyFactory = $historyFactory;
        $this->categoryEventList = $categoryEventList;
        $this->_registry = $registry;
    }

    /**
     * @return bool|mixed
     */
    public function getProductEventId(){
        $product = $this->getProduct();
        // use event category or product
        if ($product->hasData('cat_index_position') && $product->getCategory()) {
            $catalogEvent = $product->getCategory()->getEvent();
        } else {
            $catalogEvent = $this->getProductEvent($product);
        }

        if(isset($catalogEvent) && $catalogEvent->getStatus() == $catalogEvent::STATUS_OPEN)
        return $catalogEvent->getId();
        else return false;
    }

    /**
     * @return bool|mixed
     */
    public function getProductLimit(){
        $product = $this->getProduct();
        $flashSaleLimit = false;
        if($product->getData('is_flashsale')) {
            if ($product->getTypeId() != self::PRODUCT_CONFIGURABLE) {
                $flashSaleLimit = $product->getData('flashsale_qty');
            }
        }
        return $flashSaleLimit;
    }

    /**
     * @return bool|int|null
     */
    public function getProductId(){
        $product = $this->getProduct();
        if ($product->getTypeId() != self::PRODUCT_CONFIGURABLE) {
            return $product->getId();
        }else{
            return false;
        }
    }

    /**
     * @param $product
     * @return null
     */
    public function getFirstItemOfConfigProduct($product)
    {
        $_firstSimple = $product->getTypeInstance();
        $usedProduct = $_firstSimple->getUsedProducts($product);
        $firstChild = null;
        foreach ($usedProduct as $child) {
            if ($firstChild == null) {
                $firstChild = $child;
            }
        }

        return $firstChild;
    }

    public function getCurrentCategory(){
        $catId = $this->_registry->registry('current_category')->getId();
        return $catId;
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
