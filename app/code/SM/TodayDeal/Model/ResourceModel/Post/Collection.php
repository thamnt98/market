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

namespace SM\TodayDeal\Model\ResourceModel\Post;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use SM\TodayDeal\Api\Data\PostInterface;
use SM\TodayDeal\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'post_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_today_deals_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'trans_today_deals_collection';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Helper
     */
    protected $_resourceHelper;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Eav\Model\ResourceModel\Helper $_resourceHelper
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param MetadataPool $metadataPool
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Eav\Model\ResourceModel\Helper $_resourceHelper,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null)
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $metadataPool,
            $connection,
            $resource
        );
        $this->timezone = $timezone;
        $this->_resourceHelper = $_resourceHelper;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\TodayDeal\Model\Post', 'SM\TodayDeal\Model\ResourceModel\Post');
        $this->_map['fields']['post_id'] = 'main_table.post_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function addPeriodDateFilter()
    {
        $currentDate = $this->timezone->date()->format('Y-m-d H:i:s');
        return $this->addFieldToFilter('publish_to', ['gteq' => $currentDate]);
    }

    /**
     * @return Collection
     */
    public function addStartDateFilter(){
        $currentDate = $this->timezone->date()->format('Y-m-d H:i:s');
        return $this->addFieldToFilter('publish_from', ['lteq' => $currentDate]);
    }
    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);
        $this->performAfterLoad('trans_today_deals_store', $entityMetadata->getLinkField());
        $this->_previewFlag = false;
        foreach ($this->_items as $item) {
            try {
                $item->load($item->getId());
            } catch (\Exception $e) {
                continue;
            }
        }

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     * @throws \Exception
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);
        $this->joinStoreRelationTable('trans_today_deals_store', $entityMetadata->getLinkField());
    }

    /**
     * @param string $q
     *
     * @return $this
     */
    public function addSearchFilter($q)
    {
        $likeExpression = $this->_resourceHelper->addLikeEscape($q, ['position' => 'any']);

        $this->addFieldToFilter(
            ['title', 'mb_short_title', 'mb_short_description', 'mb_signature_description'],
            [
                ['like' => $likeExpression],
                ['like' => $likeExpression],
                ['like' => $likeExpression],
                ['like' => $likeExpression]
            ]
        );

        return $this;
    }
}
