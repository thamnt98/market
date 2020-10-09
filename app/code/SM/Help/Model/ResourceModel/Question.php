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

namespace SM\Help\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use SM\Help\Api\Data\QuestionInterface;
use SM\Help\Model\Question as QuestionModel;
use Magento\Store\Model\Store;

class Question extends AbstractDb
{
    /**
     * Store model
     *
     * @var null|Store
     */
    protected $_store = null;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Filter\FilterManager $filter,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->filter = $filter;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sm_help_question', 'question_id');
    }

    public function getConnection()
    {
        return $this->metadataPool->getMetadata(QuestionInterface::class)->getEntityConnection();
    }

    /**
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getQuestionId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(QuestionInterface::class);

        if (!is_numeric($value) && $field === null) {
            $field = 'url_key';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $questionId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $questionId = count($result) ? $result[0] : false;
        }
        return $questionId;
    }

    /**
     * Load an object
     *
     * @param QuestionModel|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     * @throws LocalizedException
     * @throws \Exception
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $questionId = $this->getQuestionId($object, $value, $field);
        if ($questionId) {
            $this->entityManager->load($object, $questionId);
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param QuestionModel|AbstractModel $object
     * @return Select
     * @throws \Exception
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(QuestionInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [
                Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId(),
            ];
            $select->join(
                ['question_store' => $this->getTable('sm_help_question_store')],
                $this->getMainTable() . '.' . $linkField . ' = question_store.' . $linkField,
                []
            )
                ->where('status = ?', 1)
                ->where('question_store.store_id IN (?)', $storeIds)
                ->order('question_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     * @throws LocalizedException
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(QuestionInterface::class);

        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Select::COLUMNS)
            ->columns('sq.' . $entityMetadata->getIdentifierField())
            ->order('sqs.store_id DESC')
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
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(QuestionInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $this->getConnection()->select()
            ->from(['sq' => $this->getMainTable()])
            ->join(
                ['sqs' => $this->getTable('sm_help_question_store')],
                'sq.' . $linkField . ' = sqs.' . $linkField,
                []
            )
            ->where('sq.url_key = ?', $identifier)
            ->where('sqs.store_id IN (?)', $store);

        if ($isActive !== null) {
            $select->where('sq.status = ?', $isActive);
        }
        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param $questionId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function lookupStoreIds($questionId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(QuestionInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['sqs' => $this->getTable('sm_help_question_store')], 'store_id')
            ->join(
                ['sq' => $this->getMainTable()],
                'sqs.' . $linkField . ' = sq.' . $linkField,
                []
            )
            ->where('sq.' . $entityMetadata->getIdentifierField() . ' = :question_id');

        return $connection->fetchCol($select, ['question_id' => (int)$questionId]);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        if (!$object->getData('url_key')) {
            $object->setData('url_key', $this->filter->translitUrl($object->getTitle()));
        }
        $this->entityManager->save($object);
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
}
