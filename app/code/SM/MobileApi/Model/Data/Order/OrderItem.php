<?php


namespace SM\MobileApi\Model\Data\Order;


use SM\MobileApi\Api\Data\Order\OrderItemInterface;

class OrderItem extends \Magento\Framework\Model\AbstractExtensibleModel implements OrderItemInterface
{

    public function getSku()
    {
        return $this->getData('sku');
    }

    public function setSku($data)
    {
        return $this->setData('sku', $data);
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function setName($data)
    {
        return $this->setData('name', $data);
    }

    public function getQty()
    {
        return $this->getData('qty');
    }

    public function setQty($data)
    {
        return $this->setData('qty', $data);
    }
}
