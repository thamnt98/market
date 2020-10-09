<?php

namespace SM\Review\Model\Data\Product;

/**
 * Class ProductReviewedData
 * @package SM\Review\Model\Data\Product
 */
class ProductReviewedData extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Review\Api\Data\Product\ProductReviewedInterface
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
    public function setPercentVote($percent)
    {
        return $this->setData('percent_vote', $percent);
    }
    /**
     * {@inheritdoc}
     */
    public function getPercentVote()
    {
        return $this->_get('percent_vote');
    }
    /**
     * {@inheritdoc}
     */
    public function setTitleVote($title)
    {
        return $this->setData('title_vote', $title);
    }
    /**
     * {@inheritdoc}
     */
    public function getTitleVote()
    {
        return $this->_get('title_vote');
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

    /**
     * @inheritDoc
     */
    public function setReviewId($value)
    {
        return $this->setData(self::REVIEW_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getReviewId()
    {
        return $this->_get(self::REVIEW_ID);
    }

    public function getCreatedAt()
    {
        return $this->_get("created_at");
    }


    public function setCreatedAt($value)
    {
        return $this->setData("created_at", $value);
    }
}
