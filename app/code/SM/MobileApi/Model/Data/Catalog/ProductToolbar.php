<?php

namespace SM\MobileApi\Model\Data\Catalog;

class ProductToolbar extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\ProductToolbarInterface
{
    public function getCurrentPageNum()
    {
        return $this->getData(self::CURRENT_PAGE_NUM);
    }

    public function setCurrentPageNum($data)
    {
        return $this->setData(self::CURRENT_PAGE_NUM, $data);
    }

    public function getLastPageNum()
    {
        return $this->getData(self::LAST_PAGE_NUM);
    }

    public function setLastPageNum($data)
    {
        return $this->setData(self::LAST_PAGE_NUM, $data);
    }

    public function getCurrentLimit()
    {
        return $this->getData(self::CURRENT_LIMIT);
    }

    public function setCurrentLimit($data)
    {
        return $this->setData(self::CURRENT_LIMIT, $data);
    }

    public function getCurrentOrder()
    {
        return $this->getData(self::CURRENT_ORDER);
    }

    public function setCurrentOrder($data)
    {
        return $this->setData(self::CURRENT_ORDER, $data);
    }

    public function getCurrentDirection()
    {
        return $this->getData(self::CURRENT_DIRECTION);
    }

    public function setCurrentDirection($data)
    {
        return $this->setData(self::CURRENT_DIRECTION, $data);
    }

    public function getProductTotal()
    {
        return $this->getData(self::PRODUCT_TOTAL);
    }

    public function setProductTotal($total)
    {
        return $this->setData(self::PRODUCT_TOTAL, $total);
    }

    public function getAvailableOrders()
    {
        return $this->getData(self::AVAILABLE_ORDERS);
    }

    public function setAvailableOrders($data)
    {
        return $this->setData(self::AVAILABLE_ORDERS, $data);
    }
}
