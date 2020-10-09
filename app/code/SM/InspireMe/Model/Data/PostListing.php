<?php

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\InspireMe\Api\Data\PostListingInterface;

/**
 * Class PostListing
 * @package SM\InspireMe\Model\Data
 */
class PostListing extends AbstractExtensibleModel implements PostListingInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Mirasvit\Blog\Model\Config
     */
    protected $config;

    /**
     * PostListing constructor.
     * @param \Mirasvit\Blog\Model\Config $config
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Mirasvit\Blog\Model\Config $config,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->timezone = $timezone;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getShortContent()
    {
        return $this->getData(self::SHORT_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setShortContent($shortContent)
    {
        return $this->setData(self::SHORT_CONTENT, $shortContent);
    }

    /**
     * @inheritDoc
     */
    public function getPublishedDate()
    {
        return $this->getData(self::PUBLISHED_DATE);
    }

    /**
     * @param $date
     *
     * @return PostListing
     * @throws \Exception
     */
    public function setPublishedDate($date)
    {
        $date = $this->timezone->date(new \DateTime($date))->format('d F Y');

        return $this->setData(self::PUBLISHED_DATE, $date);
    }

    /**
     * @inheritDoc
     */
    public function getHomeImage()
    {
        return $this->getData(self::HOME_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setHomeImage($url)
    {
        return $this->setData(self::HOME_IMAGE, $url);
    }

    /**
     * @inheritDoc
     */
    public function getTagName()
    {
        return $this->getData(self::TAG_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setTagName($tagName)
    {
        return $this->setData(self::TAG_NAME, $tagName);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getFeaturedImageUrl()
    {
        return $this->config->getMediaUrl($this->getData(\Mirasvit\Blog\Api\Data\PostInterface::FEATURED_IMAGE));
    }

    /**
     * @inheritDoc
     */
    public function getHomeImageUrl()
    {
        return $this->config->getMediaUrl($this->getHomeImage());
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->getData('position');
    }

    /**
     * @inheritDoc
     */
    public function getTopicName()
    {
        return $this->getData(self::TOPIC_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setTopicName($value)
    {
        return $this->setData(self::TOPIC_NAME, $value);
    }

    /**
     * @param $date
     * @return string
     */
    protected function getFormatDate($date)
    {
        return $this->timezone->formatDateTime(
            $date,
            null,
            null,
            null,
            $this->timezone->getConfigTimezone(),
            'd LLL YYYY'
        );
    }

    /**
     * @inheritDoc
     */
    public function getFormatCreatedAt()
    {
        return $this->getData(self::FORMAT_CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getGtmCreatedAt()
    {
        return $this->getData(self::GTM_CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getSource()
    {
        return $this->getData(self::SOURCE);
    }

    /**
     * @inheritDoc
     */
    public function setSource($value)
    {
        return $this->setData(self::SOURCE, $value);
    }

    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    public function setFeaturedImageUrl($value)
    {
        return $this->setData(self::FEATURE_IMAGE_URL, $value);
    }

    public function setPosition($value)
    {
        return $this->setData(self::POSITION, $value);
    }

    public function setFormatCreatedAt($value)
    {
        return $this->setData(self::FORMAT_CREATED_AT, $value);
    }

    public function setGtmCreatedAt($value)
    {
        $value = $this->timezone->date(new \DateTime($value))->format('d-M-Y');

        return $this->setData(self::GTM_CREATED_AT, $value);
    }

    public function getArticleList()
    {
        return $this->getData(self::ARTICLE_LIST);
    }

    public function setArticleList($value)
    {
        return $this->setData(self::ARTICLE_LIST, $value);
    }
}

