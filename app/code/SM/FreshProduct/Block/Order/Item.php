<?php

namespace SM\FreshProduct\Block\Order;

class Item extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Catalog\Model\Product|\SM\FreshProductApi\Api\Data\FreshProductInterface
     */
    protected $product = null;

    /**
     * @var int
     */
    protected $qty = 1;

    /**
     * @return \Magento\Catalog\Model\Product|\SM\FreshProductApi\Api\Data\FreshProductInterface|null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\SM\FreshProductApi\Api\Data\FreshProductInterface $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    public function getTotalWeight($qty, $weight)
    {
        return $qty * (float)$weight;
    }
}
