<?php


namespace SM\MobileApi\Model\Data\Order;


use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use SM\MobileApi\Api\Data\Order\OrderInterface;

class Order extends AbstractExtensibleModel implements OrderInterface
{

    public function getIncrementId()
    {
        return $this->getData('increment_id');
    }

    public function setIncrementId($data)
    {
        return $this->setData('increment_id', $data);
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function setCreatedAt($data)
    {
        return $this->setData('created_at', $data);
    }

    public function getBaseGrandTotal()
    {
        return $this->getData('base_grand_total');
    }

    public function setBaseGrandTotal($data)
    {
        return $this->setData('base_grand_total', $data);
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function setStatus($data)
    {
        return $this->setData('status', $data);
    }

    public function getOrderItems()
    {
        return $this->getData('order_items');
    }

    public function setOrderItems($data)
    {
        return $this->setData('order_items', $data);
    }

}
