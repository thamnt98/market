<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use SM\InspireMe\Api\Data\PostDetailInterface;

/**
 * Class PostDetail
 * @package SM\InspireMe\Model\Data
 */
class PostDetail extends AbstractExtensibleModel implements PostDetailInterface
{
    /**
     * @var \Mirasvit\Blog\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * PostDetail constructor.
     * @param \Mirasvit\Blog\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param \Mirasvit\Blog\Model\Config $config
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Mirasvit\Blog\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Mirasvit\Blog\Model\Config $config,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
        $this->config = $config;
        $this->tagCollectionFactory = $tagCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortContent()
    {
        return $this->getData(self::SHORT_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatCreatedAt()
    {
        return $this->getData(self::FORMAT_CREATED_AT);
    }

    /**
     * @return int
     */
    public function getIsShopIngredient()
    {
        return $this->getData('is_shop_ingredient');
    }

    /**
     * @inheritDoc
     */
    public function getShowHotSpot()
    {
        return $this->getData('show_hot_spot');
    }

    /**
     * @inheritDoc
     */
    public function getHotSpot()
    {
        return $this->getData('hot_spot');
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
    public function getTags()
    {
        $ids = $this->getData(\Mirasvit\Blog\Api\Data\PostInterface::TAG_IDS);
        if (!$ids) {
            return [];
        }

        return $this->tagCollectionFactory->create()
            ->addFieldToFilter(\Mirasvit\Blog\Api\Data\TagInterface::ID, $ids)
            ->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getMobileMainContent()
    {
        return $this->getData(\SM\InspireMe\Helper\Data::MOBILE_MAIN_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getMobileSubContent()
    {
        return $this->getData(\SM\InspireMe\Helper\Data::MOBILE_SUB_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getTopicName(){
        return $this->getData(self::TOPIC_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setTopicName($data){
        return $this->setData(self::TOPIC_NAME,$data);
    }

    /**
     * @inheritDoc
     */
    public function getArticleAuthor()
    {
        return $this->getData(self::ARTICLE_AUTHOR);
    }

    /**
     * @inheritDoc
     */
    public function setArticleAuthor($data)
    {
        return $this->setData(self::ARTICLE_AUTHOR,$data);
    }
}
