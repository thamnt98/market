<?php

namespace SM\Review\Model\Data\Product;

/**
 * Class ProductToBeReviewedData
 * @package SM\Review\Model\Data\Product
 */
class ProductToBeReviewedData extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Review\Api\Data\Product\ProductToBeReviewedInterface
{
    /**
     * {@inheritdoc}
     */
    public function setProductName($name)
    {
        return $this->setData('product_name', $name);
    }
    /**
     * {@inheritdoc}
     */
    public function getProductName()
    {
        return $this->_get('product_name');
    }
    /**
     * {@inheritdoc}
     */
    public function setProductUrl($url)
    {
        return $this->setData('url', $url);
    }
    /**
     * {@inheritdoc}
     */
    public function getProductUrl()
    {
        return $this->_get('url');
    }
    /**
     * {@inheritdoc}
     */
    public function setProductImage($url)
    {
        return $this->setData('image', $url);
    }
    /**
     * {@inheritdoc}
     */
    public function getProductImage()
    {
        return $this->_get('image');
    }
    /**
     * {@inheritdoc}
     */
    public function setProductId($id)
    {
        return $this->setData('product_id', $id);
    }
    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->_get('product_id');
    }
}
