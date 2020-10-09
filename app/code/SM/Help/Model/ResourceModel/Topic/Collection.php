<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model\ResourceModel\Topic;

use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Helper\Topic;

/**
 * Class Collection
 * @package SM\Help\Model\ResourceModel\Topic
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'main_table.topic_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sm_help_topic_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'topic_collection';

    /**
     * @var bool
     */
    protected $fromRoot = true;

    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var Topic
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('SM\Help\Model\Topic', 'SM\Help\Model\ResourceModel\Topic');
    }

    /**
     * Collection constructor.
     * @param Topic $helper
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \SM\Help\Helper\Topic $helper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->helper = $helper;
    }

    /**
     * @return $this|Collection|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->join(
                ['topic_store' => 'sm_help_topic_store'],
                'main_table.topic_id = topic_store.topic_id',
                ['store_id', 'name', 'status', 'description']
            );

        return $this;
    }

    /**
     * @return $this
     */
    public function addRootFilter()
    {
        $this->addFieldToFilter('parent_id', 0);

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function excludeRoot()
    {
        $this->fromRoot = false;

        return $this->addFieldToFilter('main_table.topic_id', ['neq' => \SM\Help\Model\Topic::TREE_ROOT_ID]);
    }

    /**
     * Add store availability filter.
     *
     * @param null|string|bool|int|Store $store
     * @return Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addStoreFilter()
    {
        $this->addFieldToFilter('store_id', ['eq' => $this->getStoreId()]);

        return $this;
    }

    /**
     * @return Collection
     */
    public function addVisibilityFilter()
    {
        return $this->addFieldToFilter(TopicInterface::STATUS, ['eq' => TopicInterface::STATUS_ENABLED]);
    }

    /**
     * Return current store id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoreId()
    {
        $this->setStoreId($this->helper->getCurrentStoreId());

        return $this->_storeId;
    }

    /**
     * Set store scope
     *
     * @param int|string|\Magento\Store\Api\Data\StoreInterface $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof \Magento\Store\Api\Data\StoreInterface) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }
}
