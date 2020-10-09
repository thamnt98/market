<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Model\UrlInterface;

/**
 * Class Topic
 * @package SM\Help\Model
 */
class Topic extends \Magento\Framework\Model\AbstractModel implements IdentityInterface, UrlInterface, TopicInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sm_help_topic';

    /**
     * Root tree topic ID
     */
    const TREE_ROOT_ID = 1;

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sm_help_topic';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sm_help_topic';

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Topic constructor.
     * @param Config $config
     * @param Url $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \SM\Help\Model\Config $config,
        \SM\Help\Model\Url $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Init resource model topic
     */
    protected function _construct()
    {
        $this->_init('SM\Help\Model\ResourceModel\Topic');
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
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
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setUrlKey($value)
    {
        return $this->setData(self::URL_KEY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * @inheritDoc
     */
    public function setPath($value)
    {
        return $this->setData(self::PATH, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLevel()
    {
        return $this->getData(self::LEVEL);
    }

    /**
     * @inheritDoc
     */
    public function setLevel($value)
    {
        return $this->setData(self::LEVEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($value)
    {
        return $this->setData(self::POSITION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setParentId($value)
    {
        return $this->setData(self::PARENT_ID, $value);
    }

    /**
     * Get all parent topics ids
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    /**
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if ($ids === null) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }

        return $ids;
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
    public function getUrl($urlParams = [])
    {
        return $this->url->getTopicUrl($this, $urlParams);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * Get Store Id
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * @param int $value
     * @return Topic
     */
    public function setStoreId($value)
    {
        return $this->setData('store_id', $value);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        try {
            return $this->config->getMediaUrl($this->getImage());
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * @param $urlKey
     * @return \Magento\Framework\DataObject
     */
    public function loadByUrlKey($urlKey)
    {
        $this->setData($this->getCollection()->addFieldToFilter("url_key", $urlKey)->getFirstItem()->getData());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }
}
