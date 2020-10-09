<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use SM\Help\Api\Data\QuestionInterface;

/**
 *
 * @api
 * @method Question setStoreId(int $storeId)
 * @method int getStoreId()
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @since 100.0.2
 */
class Question extends AbstractModel implements QuestionInterface, IdentityInterface, UrlInterface
{
    const URL_PATH = 'help';

    /**
     * Help question cache tag
     */
    const CACHE_TAG = 'help_question';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param Url $url
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \SM\Help\Model\Url $url,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->url = $url;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SM\Help\Model\ResourceModel\Question::class);
    }

    /**
     * Return the desired URL of a post
     * @return string
     */
    public function getPostUrl()
    {
        return $this->_urlBuilder->getUrl(self::URL_PATH . '/' . $this->getUrlKey());
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
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
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \SM\Help\Api\Data\QuestionInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get Status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param int|bool $status
     * @return \SM\Help\Api\Data\QuestionInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get url key
     *
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * Set url key
     *
     * @param string $urlKey
     * @return \SM\Help\Api\Data\QuestionInterface
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * Get topic ids
     *
     * @return string
     */
    public function getTopicId()
    {
        return $this->getData(self::TOPIC_IDS);
    }

    /**
     * Set topic ids
     *
     * @param string $value
     * @return \SM\Help\Api\Data\QuestionInterface
     */
    public function setTopicId($value)
    {
        return $this->setData(self::TOPIC_IDS, $value);
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
     * Set content
     *
     * @param string $value
     * @return \SM\Help\Api\Data\QuestionInterface
     */
    public function setContent($value)
    {
        return $this->setData(self::CONTENT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $data
     * @return $this
     */

    public function setCreatedAt($data){
        return $this->setData(self::CREATED_AT,$data);
    }

    /**
     * Get store ids
     *
     * @return string
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * Set store ids
     *
     * @param string $value
     * @return \SM\Help\Api\Data\QuestionInterface
     */
    public function setStoreIds($value)
    {
        return $this->setData(self::STORE_IDS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUrl($urlParams = [])
    {
        return $this->url->getQuestionUrl($this, $urlParams);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritDoc
     */
    public function getContentUrl()
    {
        return $this->getData(self::CONTENT_URL);
    }

    /**
     * @inheritDoc
     */
    public function setContentUrl($data)
    {
        return $this->setData(self::CONTENT_URL, $data);
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
}
