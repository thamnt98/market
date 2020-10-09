<?php

namespace Mirasvit\Blog\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Image as MagentoImage;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Blog\Api\Data\CategoryInterface;
use Mirasvit\Blog\Api\Data\PostInterface as PostInterface;
use Mirasvit\Blog\Api\Data\TagInterface;
use Mirasvit\Blog\Api\Repository\AuthorRepositoryInterface;
use Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface;
use Mirasvit\Blog\Api\Repository\TagRepositoryInterface;

/**
 * @method string getFeaturedShowOnHome()
 * @method int getParentId()
 * @method $this setParentId($parent)
 * @method ResourceModel\Post getResource()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Post extends AbstractExtensibleModel implements IdentityInterface, PostInterface
{
    const ENTITY    = 'blog_post';
    const CACHE_TAG = 'blog_post';

    /**
     * @var MagentoImage
     */
    protected $_processor;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * Post constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param TagRepositoryInterface $tagRepository
     * @param AuthorRepositoryInterface $authorRepository
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Config $config
     * @param Url $url
     * @param StoreManagerInterface $storeManager
     * @param ImageFactory $imageFactory
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Mirasvit\Blog\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository,
        AuthorRepositoryInterface $authorRepository,
        ProductCollectionFactory $productCollectionFactory,
        Config $config,
        Url $url,
        StoreManagerInterface $storeManager,
        ImageFactory $imageFactory,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory
    ) {
        $this->categoryRepository       = $categoryRepository;
        $this->tagRepository            = $tagRepository;
        $this->authorRepository         = $authorRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config                   = $config;
        $this->url                      = $url;
        $this->storeManager             = $storeManager;
        $this->imageFactory             = $imageFactory;

        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory);
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [Category::CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
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
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorId($value)
    {
        return $this->setData(self::AUTHOR_ID, $value);
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
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortContent()
    {
        return $this->getData(self::SHORT_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setShortContent($value)
    {
        return $this->setData(self::SHORT_CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($value)
    {
        return $this->setData(self::CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($value)
    {
        return $this->setData(self::URL_KEY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($value)
    {
        return $this->setData(self::META_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($value)
    {
        return $this->setData(self::META_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($value)
    {
        return $this->setData(self::META_KEYWORDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedImage($value)
    {
        return $this->setData(self::FEATURED_IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedAlt()
    {
        return $this->getData(self::FEATURED_ALT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedAlt($value)
    {
        return $this->setData(self::FEATURED_ALT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isPinned()
    {
        return $this->getData(self::IS_PINNED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsPinned($value)
    {
        return $this->setData(self::IS_PINNED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryIds($value)
    {
        return $this->setData(self::CATEGORY_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($value)
    {
        return $this->setData(self::STORE_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setTagIds($value)
    {
        return $this->setData(self::TAG_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductIds($value)
    {
        return $this->setData(self::PRODUCT_IDS, $value);
    }

    /**
     * @return \Mirasvit\Blog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategories()
    {
        $ids   = $this->getCategoryIds();
        $ids[] = 0;

        $collection = $this->categoryRepository->getCollection()
            ->addAttributeToSelect(['*'])
            ->addFieldToFilter(CategoryInterface::ID, $ids);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds()
    {
        return $this->getData(self::CATEGORY_IDS);
    }

    /**
     * @return \Mirasvit\Blog\Api\Data\TagInterface[]
     */
    public function getTags()
    {
        $ids = $this->getTagIds();
        if (!$ids) {
            return [];
        }

        return $this->tagCollectionFactory->create()
            ->addFieldToFilter(TagInterface::ID, $ids)
            ->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getTagIds()
    {
        return $this->getData(self::TAG_IDS);
    }

    /**
     * @return \SM\InspireMe\Api\Data\RelatedProductsInterface[]
     */
    public function getRelatedProducts()
    {
        $ids = $this->getProductIds();
        if (!$ids) {
            return [];
        }

        $order = $this->getData(\SM\InspireMe\Helper\Data::RELATED_PRODUCT_POSITION);

        uksort($ids, function ($key1, $key2) use ($order) {
            return (($order[$key1] > $order[$key2]) ? 1 : -1);
        });

        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', $ids)
            ->getItems();

        $productValues = $this->getData(\SM\InspireMe\Helper\Data::RELATED_PRODUCT_VALUE);
        $cnt = 0;
        foreach ($products as $product) {
            $product->setData(\SM\InspireMe\Helper\Data::RELATED_PRODUCT_VALUE, $productValues[$cnt++]);
        }

        return array_replace(array_flip($ids), $products);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductIds()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    /**
     * @param bool $useSid
     *
     * @return string
     */
    public function getUrl($useSid = true)
    {
        return $this->url->getPostUrl($this, $useSid);
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        if (!$this->hasData('author')) {
            $this->setData('author', $this->authorRepository->get($this->getAuthorId()));
        }

        return $this->getData('author');
    }
    //
    //    /**
    //     * @return \Magento\Store\Model\Store|false
    //     */
    //    public function getStore()
    //    {
    //        $ids = $this->getStoreIds();
    //        if (count($ids) == 0) {
    //            return false;
    //        }
    //
    //        $storeId = reset($ids);
    //        $store = $this->storeManager->getStore($storeId);
    //
    //        return $store;
    //    }

    //    /**
    //     * @param int $storeId
    //     * @return bool
    //     */
    //    public function isStoreAllowed($storeId)
    //    {
    //        return in_array(0, $this->getStoreIds()) || in_array($storeId, $this->getStoreIds());
    //    }
    //

    /**
     * {@inheritdoc}
     */
    public function getAuthorId()
    {
        return $this->getData(self::AUTHOR_ID);
    }

    /**
     * @return string
     */
    public function getFeaturedImageUrl()
    {
        return $this->config->getMediaUrl($this->getFeaturedImage());
    }

    /**
     * @return string
     */
    public function getHomeImageUrl()
    {
        return $this->config->getMediaUrl($this->getData('home_image'));
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedImage()
    {
        return $this->getData(self::FEATURED_IMAGE);
    }

    /**
     * @return string
     */
    public function getFormatCreatedAt()
    {
        return $this->getFormatDate($this->getCreatedAt());
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTopicName()
    {
        $topics = $this->getCategories()->excludeRoot();
        if ($topics->getSize()) {
            return $topics->getFirstItem()->getName();
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Blog\Model\ResourceModel\Post');
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
     * @return string
     */
    public function getGtmCreatedAt()
    {
        return $this->timezone->formatDateTime(
            $this->getCreatedAt(),
            null,
            null,
            null,
            $this->timezone->getConfigTimezone(),
            'dd-MM-yyyy'
        );
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getAuthor()->getName();
    }
}
