<?php


namespace SM\MobileApi\Model\Data\Order;


use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Order\ListOrdersInterface;

class ListOrders extends AbstractExtensibleModel implements ListOrdersInterface
{

    public function getOrders()
    {
        return $this->getData('orders');
    }

    public function setOrders($data)
    {
        return $this->setData('orders', $data);
    }

    public function getPageSize()
    {
        return $this->getData('page_size');
    }

    public function setPageSize($data)
    {
        return $this->setData('page_size', $data);
    }

    public function getTotal()
    {
        return $this->getData('total');
    }

    public function setTotal($data)
    {
        return $this->setData('total', $data);
    }
}
