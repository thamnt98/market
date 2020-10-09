<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\TodayDeal\Model;

use Magento\Cms\Model\Page\CustomLayout\CustomLayoutRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use SM\TodayDeal\Api\Data\PostInterface;

/**
 * Today Deals Post Model
 *
 * @api
 * @method Post setStoreId(int $storeId)
 * @method int getStoreId()
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @since 100.0.2
 */
class Post extends AbstractModel implements PostInterface, IdentityInterface
{
    const URL_PATH = 'todaydeal';
    const CAMPAIGN_TYPE = 2;

    /**
     * No route post id
     */
    const NOROUTE_PAGE_ID = 'no-route';

    /**#@+
     * Post's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * Today Deals post cache tag
     */
    const CACHE_TAG = 'today_deal';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var CustomLayoutRepository
     */
    private $customLayoutRepository;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param CustomLayoutRepository|null $customLayoutRepository
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ?CustomLayoutRepository $customLayoutRepository = null
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->customLayoutRepository = $customLayoutRepository
            ?? ObjectManager::getInstance()->get(CustomLayoutRepository::class);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SM\TodayDeal\Model\ResourceModel\Post::class);
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Page
     *
     * @return \SM\TodayDeal\Model\Post
     */
    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName());
    }

    /**
     * Receive post store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /**
     * Check if post identifier exist for specific store return post id if post exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->loadByIdentifier($identifier, $storeId);
    }

    /**
     * Return the desired URL of a post
     * @return string
     */
    public function getPostUrl()
    {
        return $this->_urlBuilder->getUrl(self::URL_PATH . '/' . $this->getIdentifier());
    }

    /**
     * Prepare page's statuses, available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::POST_ID);
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get page layout
     *
     * @return string
     */
    public function getTodayDealLayout()
    {
        return $this->getData(self::TODAY_DEAL_LAYOUT);
    }

    /**
     * Get meta title
     *
     * @return string|null
     * @since 101.0.0
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * Get content heading
     *
     * @return string
     */
    public function getContentHeading()
    {
        return $this->getData(self::CONTENT_HEADING);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Get layout update xml
     *
     * @return string
     */
    public function getLayoutUpdateXml()
    {
        return $this->getData(self::LAYOUT_UPDATE_XML);
    }

    /**
     * Get custom theme
     *
     * @return string
     */
    public function getCustomTheme()
    {
        return $this->getData(self::CUSTOM_THEME);
    }

    /**
     * Get custom root template
     *
     * @return string
     */
    public function getCustomRootTemplate()
    {
        return $this->getData(self::CUSTOM_ROOT_TEMPLATE);
    }

    /**
     * Get custom layout update xml
     *
     * @return string
     */
    public function getCustomLayoutUpdateXml()
    {
        return $this->getData(self::CUSTOM_LAYOUT_UPDATE_XML);
    }

    /**
     * Get custom theme from
     *
     * @return string
     */
    public function getCustomThemeFrom()
    {
        return $this->getData(self::CUSTOM_THEME_FROM);
    }

    /**
     * Get custom theme to
     *
     * @return string
     */
    public function getCustomThemeTo()
    {
        return $this->getData(self::CUSTOM_THEME_TO);
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setId($id)
    {
        return $this->setData(self::POST_ID, $id);
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set page layout
     *
     * @param string $pageLayout
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setPageLayout($postLayout)
    {
        return $this->setData(self::TODAY_DEAL_LAYOUT, $postLayout);
    }

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return \SM\TodayDeal\Api\Data\PostInterface
     * @since 101.0.0
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set content heading
     *
     * @param string $contentHeading
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setContentHeading($contentHeading)
    {
        return $this->setData(self::CONTENT_HEADING, $contentHeading);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set layout update xml
     *
     * @param string $layoutUpdateXml
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        return $this->setData(self::LAYOUT_UPDATE_XML, $layoutUpdateXml);
    }

    /**
     * Set custom theme
     *
     * @param string $customTheme
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomTheme($customTheme)
    {
        return $this->setData(self::CUSTOM_THEME, $customTheme);
    }

    /**
     * Set custom root template
     *
     * @param string $customRootTemplate
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomRootTemplate($customRootTemplate)
    {
        return $this->setData(self::CUSTOM_ROOT_TEMPLATE, $customRootTemplate);
    }

    /**
     * Set custom layout update xml
     *
     * @param string $customLayoutUpdateXml
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomLayoutUpdateXml($customLayoutUpdateXml)
    {
        return $this->setData(self::CUSTOM_LAYOUT_UPDATE_XML, $customLayoutUpdateXml);
    }

    /**
     * Set custom theme from
     *
     * @param string $customThemeFrom
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomThemeFrom($customThemeFrom)
    {
        return $this->setData(self::CUSTOM_THEME_FROM, $customThemeFrom);
    }

    /**
     * Set custom theme to
     *
     * @param string $customThemeTo
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomThemeTo($customThemeTo)
    {
        return $this->setData(self::CUSTOM_THEME_TO, $customThemeTo);
    }

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get file_name
     * @return string|null
     */
    public function getThumbnailName()
    {
        return $this->getData(self::THUMBNAIL_NAME);
    }

    /**
     * Set file_name
     * @param string $thumbnailName
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setThumbnailName($thumbnailName)
    {
        return $this->setData(self::THUMBNAIL_NAME, $thumbnailName);
    }

    /**
     * Get file_path
     * @return string|null
     */
    public function getThumbnailPath()
    {
        return $this->getData(self::THUMBNAIL_PATH);
    }

    /**
     * Set file_path
     * @param string $thumbnailPath
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setThumbnailPath($thumbnailPath)
    {
        return $this->setData(self::THUMBNAIL_PATH, $thumbnailPath);
    }

    /**
     * Get file_size
     * @return string|null
     */
    public function getThumbnailSize()
    {
        return $this->getData(self::THUMBNAIL_SIZE);
    }

    /**
     * Set file_size
     * @param string $thumbnailSize
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setThumbnailSize($thumbnailSize)
    {
        return $this->setData(self::THUMBNAIL_SIZE, $thumbnailSize);
    }

    /**
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getRelatedProducts()
    {
        $ids = $this->getData('product_ids');
        if (!$ids) {
            return [];
        }

        $order = $this->getData('product_positions');

        uksort($ids, function ($key1, $key2) use ($order) {
            return (($order[$key1] > $order[$key2]) ? 1 : -1);
        });

        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', $ids)
            ->getItems();

        return array_replace(array_flip($ids), $products);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getThumbnailUrl()
    {
        return $this->getThumbnailPath() ?
            $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . $this->getThumbnailPath()
            : null;
    }

    /**
     * @return string
     */
    public function getPeriodDate()
    {
        return $this->convertDate($this->getData('publish_from'), $this->getData('publish_to'));
    }

    /**
     * @param string $from
     * @param string $to
     * @return string
     */
    protected function convertDate($from, $to)
    {
        if (!$from || !$to) {
            return null;
        }

        $from = $this->formatDate($from);
        $to = $this->formatDate($to);

        if ($from[2] === $to[2]) {
            if ($from[1] === $to[1]) {
                return $from[0] . ' - ' . implode(' ', $to);
            }
            return implode(' ', [$from[0], $from[1]]) . ' - ' . implode(' ', $to);
        }
        return implode(' ', $from) . ' - ' . implode(' ', $to);
    }

    /**
     * @param mixed $date
     * @return string[]
     */
    protected function formatDate($date)
    {
        $date = $this->timezone->formatDateTime(
            $date,
            null,
            null,
            null,
            $this->timezone->getConfigTimezone(),
            'd MMM YYYY'
        );
        return explode(' ', $date);
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->getSortOrder() + 1;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return self::CAMPAIGN_TYPE;
    }
}
