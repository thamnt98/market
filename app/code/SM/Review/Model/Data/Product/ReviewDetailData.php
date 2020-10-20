<?php

namespace SM\Review\Model\Data\Product;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\UrlInterface;
use SM\Review\Api\Data\Product\ReviewDetailInterface;

/**
 * Class ReviewDetailData
 * @package SM\Review\Model\Data\Product
 */
class ReviewDetailData extends AbstractExtensibleObject implements ReviewDetailInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * ReviewDetailData constructor.
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param UrlInterface $url
     * @param array $data
     */
    public function __construct(
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $attributeValueFactory,
        UrlInterface $url,
        array $data = []
    ) {
        $this->url = $url;
        parent::__construct($extensionFactory, $attributeValueFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductImage($image)
    {
        return $this->setData('product_image', $image);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductImage()
    {
        return $this->_get('product_image');
    }

    /**
     * {@inheritdoc}
     */
    public function setProductUrl($url)
    {
        return $this->setData('product_url', $url);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductUrl()
    {
        return $this->_get('product_url');
    }

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
    public function setVoteTitle($title)
    {
        return $this->setData('vote_title', $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getVoteTitle()
    {
        return $this->_get('vote_title');
    }

    /**
     * {@inheritdoc}
     */
    public function setVotePercent($percent)
    {
        return $this->setData('vote_percent', $percent);
    }

    /**
     * {@inheritdoc}
     */
    public function getVotePercent()
    {
        return $this->_get('vote_percent');
    }

    /**
     * {@inheritdoc}
     */
    public function setVoteComment($comment)
    {
        return $this->setData('vote_comment', $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getVoteComment()
    {
        return $this->_get('vote_comment');
    }

    /**
     * {@inheritdoc}
     */
    public function setReviewImage($arrImage)
    {
        return $this->setData('review_image', $arrImage);
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewImage()
    {
        return $this->_get('review_image');
    }

    /**
     * {@inheritdoc}
     */
    public function getPostReview()
    {
        $this->setData('post_review', '#');
        return $this->_get('post_review');
    }

    /**
     * @inheritDoc
     */
    public function getReviewId()
    {
        return $this->_get("review_id");
    }

    /**
     * @inheritDoc
     */
    public function setReviewId($value)
    {
        return $this->setData("review_id", $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImages()
    {
        return $this->_get(self::IMAGES);
    }

    /**
     * @inheritDoc
     */
    public function setImages($value)
    {
        return $this->setData(self::IMAGES, $value);
    }
}
