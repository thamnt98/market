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

namespace SM\TodayDeal\Model\ResourceModel;

use Magento\Framework\Filter\FilterManager;
use SM\TodayDeal\Model\Post as TDPost;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use SM\TodayDeal\Api\Data\PostInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Post extends AbstractDb
{

    /**
     * Store model
     *
     * @var null|Store
     */
    protected $_store = null;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param FilterManager $filter
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        FilterManager $filter,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->filter = $filter;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('trans_today_deals', 'post_id');
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(PostInterface::class)->getEntityConnection();
    }

    /**
     *  Check whether post identifier is numeric
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isNumericPostIdentifier(AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether post identifier is valid
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isValidPostIdentifier(AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getPostId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);

        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $postId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $postId = count($result) ? $result[0] : false;
        }
        return $postId;
    }

    /**
     * Load an object
     *
     * @param TDPost|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        parent::load($object, $value, $field);
        $pageId = $this->getPostId($object, $value, $field);
        if ($pageId) {
            $this->entityManager->load($object, $pageId);
        }
        return $this;
    }

    /**
     * @param PostInterface $model
     * @return $this
     * @throws \Exception
     */
    protected function saveProductIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('trans_today_deals_post_product');

        $productIds    = $model->getProductIds();
        $oldProductIds = $this->getProductIds($model);

        $insert = array_diff($productIds, $oldProductIds);
        $delete = array_diff($oldProductIds, $productIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId) {
                if (empty($productId)) {
                    continue;
                }
                $data[] = [
                    'product_id' => (int)$productId,
                    'post_id'    => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $productId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'product_id = ?' => (int)$productId];
                $connection->delete($table, $where);
            }
        }

        $productPositions = $model->getData('product_position');

        for ($cnt = 0; $cnt < count($productIds); $cnt++) {
            $where = [
                'post_id = ?' => (int)$model->getId(),
                'product_id = ?' => (int)$productIds[$cnt]
            ];
            $connection->update(
                $table,
                ['position' => $productPositions[$cnt]],
                $where
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $post)
    {
        $post->setData('product_ids', $this->getProductIds($post));
        $post->setData('product_positions', $this->getProductPositions($post));
        $post->setData('related_ids', $this->getRelatedIds($post));

        return parent::_afterLoad($post);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return int[]|null
     */
    protected function getProductPositions(\Magento\Framework\Model\AbstractModel $model)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('trans_today_deals_post_product'),
            'position'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return int[]|null
     */
    protected function getProductIds(\Magento\Framework\Model\AbstractModel $model)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('trans_today_deals_post_product'),
            'product_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param TDPost|AbstractModel $object
     * @return Select
     * @throws \Exception
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [
                Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId(),
            ];
            $select->join(
                ['trans_today_deals_store' => $this->getTable('trans_today_deals_store')],
                $this->getMainTable() . '.' . $linkField . ' = trans_today_deals_store.' . $linkField,
                []
            )
                ->where('is_active = ?', 1)
                ->where('trans_today_deals_store.store_id IN (?)', $storeIds)
                ->order('trans_today_deals_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Check if post identifier exist for specific store
     * return post id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     * @throws LocalizedException
     * @throws \Exception
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);

        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Select::COLUMNS)
            ->columns('td.' . $entityMetadata->getIdentifierField())
            ->order('tds.store_id DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Select
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $this->getConnection()->select()
            ->from(['td' => $this->getMainTable()])
            ->join(
                ['tds' => $this->getTable('trans_today_deals_store')],
                'td.' . $linkField . ' = tds.' . $linkField,
                []
            )
            ->where('td.identifier = ?', $identifier)
            ->where('tds.store_id IN (?)', $store);

        if ($isActive !== null) {
            $select->where('td.is_active = ?', $isActive);
        }
        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $pageId
     * @return array
     * @throws \Exception
     */
    public function lookupStoreIds($pageId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['tds' => $this->getTable('trans_today_deals_store')], 'store_id')
            ->join(
                ['td' => $this->getMainTable()],
                'tds.' . $linkField . ' = td.' . $linkField,
                []
            )
            ->where('td.' . $entityMetadata->getIdentifierField() . ' = :post_id');

        return $connection->fetchCol($select, ['post_id' => (int)$pageId]);
    }

    /**
     * Set store model
     *
     * @param Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        if (!$object->getData('identifier')) {
            $object->setData('identifier', $this->filter->translitUrl($object->getTitle()));
        }
        if (!$this->isValidPostIdentifier($object)) {
            throw new LocalizedException(
                __(
                    "The post URL key can't use capital letters or disallowed symbols. "
                    . "Remove the letters and symbols and try again."
                )
            );
        }

        if ($this->isNumericPostIdentifier($object)) {
            throw new LocalizedException(
                __("The post URL key can't use only numbers. Add letters or words and try again.")
            );
        }
        $this->entityManager->save($object);
        $this->saveProductIds($object);
        $this->saveRelatedPosts($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return int[]|null
     * @throws \Exception
     */
    protected function getRelatedIds(\Magento\Framework\Model\AbstractModel $model)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('trans_today_deals_related_post'),
            'related_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param TDPost $model
     * @return $this
     * @throws \Exception
     */
    protected function saveRelatedPosts(\SM\TodayDeal\Model\Post $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('trans_today_deals_related_post');

        $relatedIds    = $model->getData('mb_related_campaigns');
        $oldRelatedIds = $this->getRelatedIds($model);

        if (!empty($relatedIds)) {
            $insert = array_diff($relatedIds, $oldRelatedIds);
            $delete = array_diff($oldRelatedIds, $relatedIds);
        } else {
            $delete = $oldRelatedIds;
        }

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $relatedId) {
                if (empty($relatedId)) {
                    continue;
                }
                $data[] = [
                    'related_id' => (int)$relatedId,
                    'post_id'    => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $relatedId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'related_id = ?' => (int)$relatedId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param $identifier
     * @param $store
     * @param null $isActive
     * @return array
     * @throws LocalizedException
     */
    public function loadByIdentifier($identifier, $store, $isActive = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PostInterface::class);

        $stores = [Store::DEFAULT_STORE_ID, $store];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Select::COLUMNS)
            ->columns('td.' . $entityMetadata->getIdentifierField())
            ->order('tds.store_id DESC')
            ->limit(1);

        $select->columns([
            "post_id",
            "is_redirect_to_plp",
            "category_to_redirect"
        ]);
        return $this->getConnection()->fetchRow($select);
    }
}
