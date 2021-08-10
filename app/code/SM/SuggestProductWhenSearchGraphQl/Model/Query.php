<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\SuggestProductWhenSearchGraphQl\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Search\Model\ResourceModel\Query\Collection as QueryCollection;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;
use Magento\Search\Model\SearchCollectionFactory as CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb as DbCollection;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Query extends AbstractModel
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Search collection factory
     *
     * @var CollectionFactory
     */
    protected $_searchCollectionFactory;

    /**
     * Query collection factory
     *
     * @var QueryCollectionFactory
     */
    protected $_queryCollectionFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param Registry $registry
     * @param QueryCollectionFactory $queryCollectionFactory
     * @param CollectionFactory $searchCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param DbCollection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        Registry $registry,
        QueryCollectionFactory $queryCollectionFactory,
        CollectionFactory $searchCollectionFactory,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        DbCollection $resourceCollection = null,
        array $data = []
    )
    {
        $this->_queryCollectionFactory = $queryCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_searchCollectionFactory = $searchCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Search\Model\ResourceModel\Query::class);
    }

    /**
     * Retrieve collection of suggest queries
     *
     * @return QueryCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSuggestCollection($keyword = null)
    {
        $collection = $this->getData('suggest_collection');
        if ($collection === null) {
            $collection = $this->_queryCollectionFactory->create()->setStoreId(
                $this->getStoreId()
            )->setQueryFilter(
                is_null($keyword) ? $this->getQueryText() : $keyword
            );
            $this->setData('suggest_collection', $collection);
        }
        return $collection;
    }

    /**
     * Load Query object only by query text
     *
     * @param string $text
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByQueryText($text)
    {
        $this->_getResource()->loadByQueryText($this, $text);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return void
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve store Id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        if (!($storeId = $this->getData('store_id'))) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        return $storeId;
    }
}
